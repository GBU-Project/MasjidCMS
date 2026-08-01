<?php

namespace App\Services\Prayer;

/**
 * Class PrayerTimeCalculator
 *
 * TASK-022 finding G: MasjidCMS's frontend prayer widget was fully
 * hardcoded ("04:38", "12:05", ...) with no Master Data configuration and
 * no local calculation at all. This service computes the five daily prayer
 * times (Fajr, Dhuhr, Asr, Maghrib, Isha) plus Sunrise/Imsak entirely
 * locally from latitude/longitude/timezone/date, using the standard
 * astronomical method shared by essentially every prayer-time
 * implementation worldwide: solar declination + the equation of time give
 * solar noon (Dhuhr); each other prayer is solar noon offset by the hour
 * angle at which the sun reaches a configured altitude below the horizon
 * (a twilight angle for Fajr/Isha, ~0.833° for Sunrise/Maghrib, and a
 * shadow-length-based altitude for Asr). No external API is used or
 * required; everything here is self-contained trigonometry + a small table
 * of per-method twilight angles.
 *
 * This class deliberately has zero framework/DB dependencies (pure value
 * in, value out) so it can be unit tested and reused anywhere (public
 * homepage widget, an eventual admin preview, a future REST/JSON prayer
 * time endpoint, a scheduled Jumu'ah/notification job, etc.) --
 * "future-ready architecture" per the RC0 requirement.
 */
class PrayerTimeCalculator
{
    /**
     * Fajr/Isha twilight angles (degrees below horizon) per calculation
     * method. Where a method historically defines Isha as a fixed number of
     * minutes after Maghrib instead of an angle (e.g. Umm al-Qura), that is
     * expressed via 'isha_minutes' instead of 'isha_angle'.
     */
    private const METHODS = [
        'KEMENAG' => ['label' => 'Kemenag RI (Indonesia)', 'fajr_angle' => 20.0, 'isha_angle' => 18.0],
        'MWL'     => ['label' => 'Muslim World League', 'fajr_angle' => 18.0, 'isha_angle' => 17.0],
        'ISNA'    => ['label' => 'Islamic Society of North America', 'fajr_angle' => 15.0, 'isha_angle' => 15.0],
        'EGYPT'   => ['label' => 'Egyptian General Authority of Survey', 'fajr_angle' => 19.5, 'isha_angle' => 17.5],
        'MAKKAH'  => ['label' => 'Umm al-Qura, Makkah', 'fajr_angle' => 18.5, 'isha_minutes' => 90],
        'KARACHI' => ['label' => 'University of Islamic Sciences, Karachi', 'fajr_angle' => 18.0, 'isha_angle' => 18.0],
    ];

    private const ASR_METHODS = [
        'STANDARD' => ['label' => "Standard (Syafi'i/Maliki/Hanbali)", 'shadow_factor' => 1.0],
        'HANAFI'   => ['label' => 'Hanafi', 'shadow_factor' => 2.0],
    ];

    public const HIGH_LAT_RULES = [
        'NONE'        => 'Tidak Ada Penyesuaian',
        'ANGLE_BASED' => 'Angle-Based (1/60th of the night per degree)',
        'ONE_SEVENTH' => 'One-Seventh of the Night',
        'MIDNIGHT'    => 'Middle of the Night',
    ];

    public static function availableMethods(): array
    {
        return self::METHODS;
    }

    public static function availableAsrMethods(): array
    {
        return self::ASR_METHODS;
    }

    /**
     * Compute all prayer times for a single day.
     *
     * @param float  $latitude    Decimal degrees, positive = North.
     * @param float  $longitude   Decimal degrees, positive = East.
     * @param string $timezone    IANA timezone name, e.g. 'Asia/Jakarta'.
     * @param string $date        'Y-m-d'.
     * @param string $calcMethod  One of self::METHODS keys.
     * @param string $asrMethod   One of self::ASR_METHODS keys.
     * @param string $highLatRule One of self::HIGH_LAT_RULES keys.
     * @return array<string,string> e.g. ['imsak' => '04:28', 'fajr' => '04:38', ...]
     */
    public function calculate(
        float $latitude,
        float $longitude,
        string $timezone,
        string $date,
        string $calcMethod = 'KEMENAG',
        string $asrMethod = 'STANDARD',
        string $highLatRule = 'NONE'
    ): array {
        $method = self::METHODS[$calcMethod] ?? self::METHODS['KEMENAG'];
        $asr = self::ASR_METHODS[$asrMethod] ?? self::ASR_METHODS['STANDARD'];

        // Bug found in UAT: an invalid IANA timezone string here (e.g. the
        // reported 'Asia/Bogor' or 'Bogor' -- Bogor shares Jakarta's WIB
        // zone and has no IANA identifier of its own) throws an uncaught
        // \Exception from DateTimeZone's constructor. Since this runs on
        // every public page via resolveMasjidProfile(), that crashed the
        // entire site with a 500, not just the prayer widget. Validate
        // against the real IANA identifier list and fall back safely
        // instead of ever letting a bad config value take down the site.
        $timezone = in_array($timezone, \DateTimeZone::listIdentifiers(), true) ? $timezone : 'Asia/Jakarta';
        $tz = new \DateTimeZone($timezone);
        $day = new \DateTime($date . ' 12:00:00', $tz);
        $utcOffsetHours = $tz->getOffset($day) / 3600;

        $julianDay = self::julianDay((int) $day->format('Y'), (int) $day->format('n'), (int) $day->format('j'));

        [$declination, $equationOfTime] = self::sunPosition($julianDay);

        // Solar noon (Dhuhr), in local clock hours.
        $dhuhrHours = 12 + $utcOffsetHours - ($longitude / 15) - ($equationOfTime / 60);

        $fajrAngle = $method['fajr_angle'];
        $hFajr = self::hourAngle($latitude, $declination, -$fajrAngle);
        $hSun = self::hourAngle($latitude, $declination, -0.833); // Sunrise/Maghrib
        $hAsr = self::hourAngle($latitude, $declination, self::asrAltitude($latitude, $declination, $asr['shadow_factor']));

        $nightHours = ($hSun !== null) ? (2 * $hSun) : 12.0; // fallback night length estimate

        $fajrHours = self::resolveHours($dhuhrHours, $hFajr, $nightHours, $fajrAngle, $highLatRule, true);
        $sunriseHours = ($hSun !== null) ? ($dhuhrHours - $hSun) : ($dhuhrHours - 6);
        $maghribHours = ($hSun !== null) ? ($dhuhrHours + $hSun) : ($dhuhrHours + 6);
        $asrHours = ($hAsr !== null) ? ($dhuhrHours + $hAsr) : ($dhuhrHours + 3);

        if (isset($method['isha_minutes'])) {
            $ishaHours = $maghribHours + ($method['isha_minutes'] / 60);
        } else {
            $ishaHours = self::resolveHours($dhuhrHours, self::hourAngle($latitude, $declination, -$method['isha_angle']), $nightHours, $method['isha_angle'], $highLatRule, false, $maghribHours);
        }

        // Imsak: conventional 10-minute safety margin before Fajr.
        $imsakHours = $fajrHours - (10 / 60);

        return [
            'imsak'   => self::formatHours($imsakHours),
            'fajr'    => self::formatHours($fajrHours),
            'sunrise' => self::formatHours($sunriseHours),
            'dhuhr'   => self::formatHours($dhuhrHours),
            'asr'     => self::formatHours($asrHours),
            'maghrib' => self::formatHours($maghribHours),
            'isha'    => self::formatHours($ishaHours),
        ];
    }

    /**
     * Hour angle (in hours) between solar noon and the moment the sun
     * reaches the given altitude (degrees; negative = below horizon).
     * Returns null when the sun never reaches that altitude on this day at
     * this latitude (polar day/night), which callers use to trigger the
     * configured high-latitude fallback rule.
     */
    private static function hourAngle(float $latitude, float $declination, float $altitude): ?float
    {
        $latRad = deg2rad($latitude);
        $decRad = deg2rad($declination);
        $altRad = deg2rad($altitude);

        $cosH = (sin($altRad) - sin($latRad) * sin($decRad)) / (cos($latRad) * cos($decRad));

        if ($cosH < -1 || $cosH > 1) {
            return null; // Sun never reaches this altitude today at this latitude.
        }

        return rad2deg(acos($cosH)) / 15; // degrees -> hours
    }

    /**
     * Sun altitude (degrees above horizon, expressed as a positive number
     * even though Asr's altitude is below Dhuhr's) at which shadow length
     * equals shadowFactor + tan(|latitude - declination|), per the
     * standard Asr definition.
     */
    private static function asrAltitude(float $latitude, float $declination, float $shadowFactor): float
    {
        $latRad = deg2rad($latitude);
        $decRad = deg2rad($declination);
        $shadowRatio = $shadowFactor + abs(tan($latRad - $decRad));

        return rad2deg(atan(1 / $shadowRatio));
    }

    /**
     * Apply the configured high-latitude rule when the twilight angle
     * hour-angle could not be computed directly (polar summer/winter),
     * otherwise return the direct angle-based time.
     */
    private static function resolveHours(
        float $dhuhrHours,
        ?float $hourAngle,
        float $nightHours,
        float $twilightAngle,
        string $rule,
        bool $isBeforeNoon,
        ?float $maghribHours = null
    ): float {
        if ($hourAngle !== null) {
            return $isBeforeNoon ? ($dhuhrHours - $hourAngle) : ($dhuhrHours + $hourAngle);
        }

        // Fallback per configured High Latitude Rule.
        $portion = match ($rule) {
            'ONE_SEVENTH' => $nightHours / 7,
            'ANGLE_BASED' => $nightHours * ($twilightAngle / 60),
            'MIDNIGHT'    => $nightHours / 2,
            default        => $nightHours / 2, // 'NONE': still avoid NAN, fall back to midpoint
        };

        return $isBeforeNoon ? ($dhuhrHours - $portion) : (($maghribHours ?? $dhuhrHours) + $portion);
    }

    /**
     * Solar declination and the equation of time (minutes) for a given
     * Julian Day, via the standard low-precision solar coordinates
     * approximation (accurate to well within a minute for civil prayer
     * time purposes).
     *
     * @return array{0: float, 1: float} [declinationDegrees, equationOfTimeMinutes]
     */
    private static function sunPosition(float $julianDay): array
    {
        $d = $julianDay - 2451545.0; // days since J2000.0
        $g = fmod(357.529 + 0.98560028 * $d, 360); // mean anomaly
        $q = fmod(280.459 + 0.98564736 * $d, 360); // mean longitude
        $l = $q + 1.915 * sin(deg2rad($g)) + 0.020 * sin(deg2rad(2 * $g)); // ecliptic longitude
        $l = fmod($l, 360);

        $e = 23.439 - 0.00000036 * $d; // obliquity of the ecliptic

        $declination = rad2deg(asin(sin(deg2rad($e)) * sin(deg2rad($l))));

        $ra = rad2deg(atan2(cos(deg2rad($e)) * sin(deg2rad($l)), cos(deg2rad($l)))) / 15; // hours
        $ra = fmod($ra + 24, 24);

        $lQuotient = $q / 15;
        $eqTimeHours = $lQuotient - $ra;
        // Normalize into [-12, 12] hours before converting to minutes.
        if ($eqTimeHours > 12) {
            $eqTimeHours -= 24;
        } elseif ($eqTimeHours < -12) {
            $eqTimeHours += 24;
        }
        $equationOfTime = $eqTimeHours * 60;

        return [$declination, $equationOfTime];
    }

    private static function julianDay(int $year, int $month, int $day): float
    {
        if ($month <= 2) {
            $year -= 1;
            $month += 12;
        }
        $a = intdiv($year, 100);
        $b = 2 - $a + intdiv($a, 4);

        return floor(365.25 * ($year + 4716)) + floor(30.6001 * ($month + 1)) + $day + $b - 1524.5;
    }

    private static function formatHours(float $hours): string
    {
        $hours = fmod($hours + 24, 24);
        $h = (int) floor($hours);
        $m = (int) round(($hours - $h) * 60);
        if ($m === 60) {
            $m = 0;
            $h = ($h + 1) % 24;
        }

        return sprintf('%02d:%02d', $h, $m);
    }
}
