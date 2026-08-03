<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class PublicPortalController extends BaseController
{
    protected $helpers = ['form', 'url'];

    /**
     * UAT TASK-022 finding #1: admin correctly saved logo_media_id /
     * favicon_media_id (Profil Masjid -> Unified Media Picker), but no
     * public-facing view ever joined those IDs to the media table or
     * rendered the resulting URL anywhere -- header.php used a hardcoded
     * emoji icon, and layouts/public.php had no <link rel="icon"> at all.
     * This is the single place that resolves both into ready-to-use URLs
     * so every controller action can pass a consistent $masjid array.
     */
    private function resolveMasjidProfile(): ?array
    {
        $db = Database::connect();
        if (!$db->tableExists('masjids')) {
            return null;
        }

        $masjid = $db->table('masjids')->get()->getRowArray();
        if (!$masjid) {
            return null;
        }

        if ($db->tableExists('media')) {
            foreach (['logo_media_id' => 'logo_url', 'favicon_media_id' => 'favicon_url'] as $idField => $urlField) {
                if (!empty($masjid[$idField])) {
                    $media = $db->table('media')->where('id', $masjid[$idField])->get()->getRowArray();
                    if ($media && !empty($media['filepath'])) {
                        $masjid[$urlField] = str_starts_with($media['filepath'], 'http')
                            ? $media['filepath']
                            : base_url($media['filepath']);
                    }
                }
            }
        }

        return $masjid;
    }

    public function index(): string
    {
        helper(['form', 'url']);
        $db = Database::connect();

        $masjidName = 'Masjid Agung Darussalam';
        $activePrograms = [];
        $activeServices = [];
        $bidangList = [];
        $pengurusList = [];
        $latestPosts = [];
        $settings = [];

        if ($db->tableExists('settings')) {
            $rawSettings = $db->table('settings')->get()->getResultArray();
            foreach ($rawSettings as $s) {
                $settings[$s['setting_key']] = $s['setting_value'];
            }
        }

        $defaultOrder = ['hero', 'prayer', 'profile', 'program', 'layanan', 'pengurus', 'bidang', 'kajian', 'berita', 'gallery', 'financial', 'donation'];
        $sectionOrder = $defaultOrder;
        if (!empty($settings['homepage_section_order'])) {
            $decoded = json_decode($settings['homepage_section_order'], true);
            if (is_array($decoded) && count($decoded) > 0) {
                $sectionOrder = $decoded;
            }
        }
        // Bug fix: on a site that already saved homepage_section_order
        // before 'berita'/'financial' (or any future section) existed in
        // \$defaultOrder, that persisted list simply doesn't contain those
        // keys -- so they were skipped entirely by the render loop below,
        // no matter what their show_*_section toggle said. Append any
        // known section that's missing from the saved order so newly
        // introduced sections always render (at the end) until an admin
        // explicitly reorders them.
        $sectionOrder = array_values(array_unique(array_merge($sectionOrder, $defaultOrder)));

        // Homepage Manager is the single source of truth for section visibility.
        $sectionVisibilityKeys = [
            'profile'   => 'show_profile_section',
            'program'   => 'show_program_section',
            'layanan'   => 'show_layanan_section',
            'pengurus'  => 'show_pengurus_section',
            'bidang'    => 'show_bidang_section',
            'kajian'    => 'show_kajian_section',
            'agenda'    => 'show_agenda_section',
            'berita'    => 'show_berita_section',
            'gallery'   => 'show_gallery_section',
            'financial' => 'show_financial_section',
            'donation'  => 'show_donation_section',
        ];
        $sectionVisibility = [];
        foreach ($sectionVisibilityKeys as $sectionKey => $settingKey) {
            $sectionVisibility[$sectionKey] = ($settings[$settingKey] ?? '1') === '1';
        }

        $limitProgram = (int) ($settings['limit_program'] ?? 6);
        $limitLayanan = (int) ($settings['limit_layanan'] ?? 4);
        $limitBidang = (int) ($settings['limit_bidang'] ?? 6);
        $limitPengurus = (int) ($settings['limit_pengurus'] ?? 3);
        $limitKajian = (int) ($settings['limit_kajian'] ?? 6);

        $masjid = $this->resolveMasjidProfile();
        if ($masjid) {
            $masjidName = $masjid['name'];
        }

        if ($db->tableExists('bidang')) {
            $builder = $db->table('bidang')->where('deleted_at', null);
            if ($db->fieldExists('homepage_visible', 'bidang')) {
                $builder->where('homepage_visible', 1);
            }
            $bidangList = $builder->orderBy('sort_order', 'ASC')->limit($limitBidang)->get()->getResultArray();
        }

        if ($db->tableExists('program_kegiatan')) {
            $builder = $db->table('program_kegiatan');
            if ($db->tableExists('bidang')) {
                $builder->select('program_kegiatan.*, bidang.name as bidang_name')
                        ->join('bidang', 'bidang.id = program_kegiatan.bidang_id', 'left');
            }
            if ($db->fieldExists('homepage_visible', 'program_kegiatan')) {
                $builder->where('program_kegiatan.homepage_visible', 1);
            }
            $activePrograms = $builder->where('program_kegiatan.status', 'ACTIVE')
                                     ->orderBy('program_kegiatan.created_at', 'DESC')
                                     ->limit($limitProgram)
                                     ->get()
                                     ->getResultArray();
        }

        if ($db->tableExists('layanan_masjid')) {
            $builder = $db->table('layanan_masjid')->where('status', 'ACTIVE');
            if ($db->fieldExists('homepage_visible', 'layanan_masjid')) {
                $builder->where('homepage_visible', 1);
            }
            $activeServices = $builder->orderBy('urutan', 'ASC')->limit($limitLayanan)->get()->getResultArray();
        }

        if ($db->tableExists('pengurus')) {
            $builder = $db->table('pengurus')->where('pengurus.status', 'ACTIVE');
            if ($db->tableExists('bidang')) {
                $builder->select('pengurus.*, bidang.name as bidang_name')
                        ->join('bidang', 'bidang.id = pengurus.bidang_id', 'left');
            }
            if ($db->fieldExists('homepage_visible', 'pengurus')) {
                $builder->where('pengurus.homepage_visible', 1);
            }
            $pengurusList = $builder->orderBy('pengurus.urutan', 'ASC')->limit($limitPengurus)->get()->getResultArray();
        }

        $limitBidang = (int) ($settings['limit_bidang'] ?? 6);
        $bidangList = [];
        if ($db->tableExists('bidang')) {
            $bidangList = $db->table('bidang')
                ->where('status', 'ACTIVE')
                ->where('deleted_at', null)
                ->orderBy('sort_order', 'ASC')
                ->limit($limitBidang)
                ->get()->getResultArray();
        }

        if ($db->tableExists('posts')) {
            $latestPosts = $db->table('posts')->where('is_published', 1)->orderBy('created_at', 'DESC')->limit($limitKajian)->get()->getResultArray();
        }

        $kajianList = [];
        if ($db->tableExists('kajian')) {
            $kajianList = $db->table('kajian')->orderBy('schedule_date', 'DESC')->limit(6)->get()->getResultArray();
        }

        $limitAgenda = (int) ($settings['limit_agenda'] ?? 5);
        $agendaList = [];
        if ($db->tableExists('agenda')) {
            $agendaList = $db->table('agenda')->where('status', 'UPCOMING')->orderBy('event_date', 'ASC')->limit($limitAgenda)->get()->getResultArray();
        }

        // Prayer Times
        $prayerTimes = [];
        $prayerCity = 'Kota Masjid';
        if ($db->tableExists('prayer_times')) {
            $prayerTimes = $db->table('prayer_times')
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->get()
                ->getResultArray();
        }
        $configuredPrayerCity = (string) ($settings['prayer_city'] ?? '');
        if (!empty($configuredPrayerCity) && $configuredPrayerCity !== 'Kota Masjid') {
            $prayerCity = $configuredPrayerCity;
        } elseif (!empty($masjid['city'])) {
            $prayerCity = $masjid['city'];
        } else {
            $prayerCity = 'Kota Masjid';
        }

        $financialSummary = [
            'total_balance' => 0,
            'total_income'  => 0,
            'total_expense' => 0,
        ];
        if ($db->tableExists('financial_accounts')) {
            $sumObj = $db->table('financial_accounts')->selectSum('balance', 'tot')->get()->getRowArray();
            $financialSummary['total_balance'] = (float) ($sumObj['tot'] ?? 0);
        }
        if ($db->tableExists('financial_transactions')) {
            $typeCol = $db->fieldExists('transaction_type', 'financial_transactions') ? 'transaction_type' : ($db->fieldExists('type', 'financial_transactions') ? 'type' : null);
            if ($typeCol) {
                $incObj = $db->table('financial_transactions')->selectSum('amount', 'tot')->where($typeCol, 'INCOME')->get()->getRowArray();
                $expObj = $db->table('financial_transactions')->selectSum('amount', 'tot')->where($typeCol, 'EXPENSE')->get()->getRowArray();
                $financialSummary['total_income']  = (float) ($incObj['tot'] ?? 0);
                $financialSummary['total_expense'] = (float) ($expObj['tot'] ?? 0);
            }
        }

        $heroSlides = [];
        if ($db->tableExists('hero_slides')) {
            $heroSlideModel = new \App\Models\HeroSlideModel();
            $heroSlides = $heroSlideModel->getActiveSlides();
        }

        return view('public/index', [
            'activePage'       => 'home',
            'masjidName'       => $masjidName,
            'masjid'           => $masjid,
            'heroSlides'       => $heroSlides,
            'prayerTimes'      => $prayerTimes,
            'sectionOrder'     => $sectionOrder,
            'sectionVisibility' => $sectionVisibility,
            'activePrograms'   => $activePrograms,
            'activeServices'   => $activeServices,
            'bidangList'       => $bidangList,
            'pengurusList'     => $pengurusList,
            'latestPosts'      => $latestPosts,
            'kajianList'       => $kajianList,
            'agendaList'       => $agendaList,
            'financialSummary' => $financialSummary,
            'settings'         => $settings,
            'donationSettings' => $settings,
            'prayerTimes'      => $prayerTimes,
            'prayerCity'       => $prayerCity,
        ]);
    }

    /**
     * TASK-022 finding G follow-up: the hero prayer widget had nowhere to
     * link to -- there was no dedicated "Jadwal Sholat" page at all, so it
     * couldn't be made clickable. This adds that page: today's times plus
     * the full month, computed the same way as the homepage widget
     * (PrayerTimeCalculator, using Master Data -> Profil Masjid -> Prayer
     * Time configuration).
     */
    public function prayerSchedule(): string
    {
        $db = Database::connect();
        $masjid = $this->resolveMasjidProfile();

        $month = (int) (($this->request->getGet('month')) ?: date('n'));
        $year = (int) (($this->request->getGet('year')) ?: date('Y'));
        $month = max(1, min(12, $month));

        $daysInMonth = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
        $monthlySchedule = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $monthlySchedule[] = ['date' => $date] + $this->computePrayerTimesForMasjid($masjid, $date);
        }

        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }
        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        return view('public/prayer_schedule', [
            'activePage'      => 'prayer-schedule',
            'masjid'          => $masjid,
            'today'           => $this->computePrayerTimesForMasjid($masjid, date('Y-m-d')),
            'monthlySchedule' => $monthlySchedule,
            'month'           => $month,
            'year'            => $year,
            'prevMonth'       => $prevMonth,
            'prevYear'        => $prevYear,
            'nextMonth'       => $nextMonth,
            'nextYear'        => $nextYear,
        ]);
    }

    /**
     * Shared prayer-time computation for a masjid record, reading its
     * Prayer Time configuration (Master Data -> Profil Masjid -> Prayer
     * Time). No hardcoded schedule, no external API -- purely local
     * calculation via PrayerTimeCalculator.
     */
    private function computePrayerTimesForMasjid(?array $masjid, string $date): array
    {
        $calculator = new \App\Services\Prayer\PrayerTimeCalculator();

        $latitude = (float) ($masjid['latitude'] ?? -6.200000);
        $longitude = (float) ($masjid['longitude'] ?? 106.816666);
        $timezone = $masjid['timezone'] ?? 'Asia/Jakarta';
        $calcMethod = $masjid['prayer_calc_method'] ?? 'KEMENAG';
        $asrMethod = $masjid['prayer_asr_method'] ?? 'STANDARD';
        $highLatRule = $masjid['prayer_high_lat_rule'] ?? 'NONE';

        return $calculator->calculate($latitude, $longitude, $timezone, $date, $calcMethod, $asrMethod, $highLatRule);
    }

    public function profile(): string
    {
        $db = Database::connect();
        $masjid = $this->resolveMasjidProfile();
        return view('public/profile', [
            'activePage' => 'profile',
            'masjid'     => $masjid,
        ]);
    }

    public function orgStructure(): string
    {
        $db = Database::connect();
        $pengurusList = [];
        $bidangList = [];

        if ($db->tableExists('bidang')) {
            $bidangList = $db->table('bidang')->where('deleted_at', null)->orderBy('sort_order', 'ASC')->get()->getResultArray();
        }

        if ($db->tableExists('pengurus')) {
            $builder = $db->table('pengurus');
            if ($db->tableExists('bidang')) {
                $builder->select('pengurus.*, bidang.name as bidang_name')
                        ->join('bidang', 'bidang.id = pengurus.bidang_id', 'left');
            }
            $pengurusList = $builder->orderBy('pengurus.urutan', 'ASC')->get()->getResultArray();
        }

        return view('public/org_structure', [
            'activePage'   => 'org_structure',
            'masjid'       => $this->resolveMasjidProfile(),
            'pengurusList' => $pengurusList,
            'bidangList'   => $bidangList,
        ]);
    }

    public function news(): string
    {
        $db = Database::connect();
        $posts = [];
        $kajianList = [];

        if ($db->tableExists('posts')) {
            $posts = $db->table('posts')->where('is_published', 1)->orderBy('created_at', 'DESC')->get()->getResultArray();
        }
        if ($db->tableExists('kajian')) {
            $kajianList = $db->table('kajian')->where('status', 'UPCOMING')->orderBy('schedule_date', 'ASC')->get()->getResultArray();
        }

        return view('public/news', [
            'activePage' => 'news',
            'masjid'     => $this->resolveMasjidProfile(),
            'posts'      => $posts,
            'kajianList' => $kajianList,
        ]);
    }

    /**
     * Bug fix: berita cards on the homepage and news listing page were not
     * actually clickable to a full article -- there was no detail page at
     * all. Every card either wasn't a link (plain <div> on the listing
     * page) or linked back to the generic listing (site_url('berita') on
     * every homepage card, regardless of which post). This adds the real
     * single-article view.
     */
    public function newsDetail(string $slug): string
    {
        $db = Database::connect();
        $post = null;
        if ($db->tableExists('posts')) {
            $post = $db->table('posts')->where('slug', $slug)->where('is_published', 1)->get()->getRowArray();
        }

        if (!$post) {
            return $this->response->setStatusCode(404)->setBody(view('errors/html/error_404'));
        }

        $relatedPosts = [];
        if ($db->tableExists('posts')) {
            $relatedPosts = $db->table('posts')
                ->where('is_published', 1)
                ->where('id !=', $post['id'])
                ->orderBy('created_at', 'DESC')
                ->limit(3)
                ->get()->getResultArray();
        }

        return view('public/news_detail', [
            'activePage'   => 'news',
            'masjid'       => $this->resolveMasjidProfile(),
            'post'         => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    public function programs(): string
    {
        $db = Database::connect();
        $programs = [];

        if ($db->tableExists('program_kegiatan')) {
            $builder = $db->table('program_kegiatan');
            if ($db->tableExists('bidang')) {
                $builder->select('program_kegiatan.*, bidang.name as bidang_name')
                        ->join('bidang', 'bidang.id = program_kegiatan.bidang_id', 'left');
            }
            $programs = $builder->where('program_kegiatan.status', 'ACTIVE')->orderBy('program_kegiatan.created_at', 'DESC')->get()->getResultArray();
        }

        return view('public/programs', [
            'activePage' => 'programs',
            'masjid'     => $this->resolveMasjidProfile(),
            'programs'   => $programs,
        ]);
    }

    public function services(): string
    {
        $db = Database::connect();
        $services = [];

        if ($db->tableExists('layanan_masjid')) {
            $services = $db->table('layanan_masjid')->where('status', 'ACTIVE')->orderBy('urutan', 'ASC')->get()->getResultArray();
        }

        return view('public/services', [
            'activePage' => 'services',
            'masjid'     => $this->resolveMasjidProfile(),
            'services'   => $services,
        ]);
    }

    public function donation(): string
    {
        $db = Database::connect();
        $accounts = [];

        if ($db->tableExists('financial_accounts')) {
            $accounts = $db->table('financial_accounts')->get()->getResultArray();
        }

        return view('public/donation', [
            'activePage' => 'donation',
            'masjid'     => $this->resolveMasjidProfile(),
            'accounts'   => $accounts,
        ]);
    }

    public function contact(): string
    {
        return view('public/contact', ['activePage' => 'contact', 'masjid' => $this->resolveMasjidProfile()]);
    }

    /**
     * TASK-022A: 'Agenda' is a Direktori submenu item in the unified nav,
     * but only ever existed embedded in the homepage section -- no
     * standalone listing page existed to link to.
     */
    public function agenda(): string
    {
        $db = Database::connect();
        $agendaList = [];
        if ($db->tableExists('agenda')) {
            $agendaList = $db->table('agenda')->orderBy('event_date', 'ASC')->get()->getResultArray();
        }

        return view('public/agenda', [
            'activePage' => 'agenda',
            'masjid'     => $this->resolveMasjidProfile(),
            'agendaList' => $agendaList,
        ]);
    }

    /**
     * TASK-022A: lightweight JSON endpoint powering the Prayer Time header
     * utility on every public page (fetched client-side so every page gets
     * it "for free" via the shared layout, without every controller action
     * needing to compute and pass prayer times individually).
     */
    public function prayerTimesJson()
    {
        $masjid = $this->resolveMasjidProfile();
        $times = $this->computePrayerTimesForMasjid($masjid, date('Y-m-d'));

        return $this->response->setJSON([
            'times'    => $times,
            'masjid'   => $masjid['name'] ?? null,
            'date'     => date('Y-m-d'),
            'schedule_url' => site_url('jadwal-shalat'),
        ]);
    }

    /**
     * TASK-022A: Search utility in the header. Simple cross-content search
     * across Berita, Program, and Layanan -- kept intentionally lightweight
     * (LIKE query, no external search engine dependency).
     */
    public function search(): string
    {
        $db = Database::connect();
        $q = trim((string) ($this->request->getGet('q') ?? ''));
        $results = ['posts' => [], 'programs' => [], 'services' => []];

        if ($q !== '') {
            if ($db->tableExists('posts')) {
                $results['posts'] = $db->table('posts')->where('is_published', 1)->like('title', $q)->orderBy('created_at', 'DESC')->limit(10)->get()->getResultArray();
            }
            if ($db->tableExists('program_kegiatan')) {
                $results['programs'] = $db->table('program_kegiatan')->where('status', 'ACTIVE')->like('nama', $q)->limit(10)->get()->getResultArray();
            }
            if ($db->tableExists('layanan_masjid')) {
                $results['services'] = $db->table('layanan_masjid')->where('status', 'ACTIVE')->like('nama', $q)->limit(10)->get()->getResultArray();
            }
        }

        return view('public/search', [
            'activePage' => 'search',
            'masjid'     => $this->resolveMasjidProfile(),
            'query'      => $q,
            'results'    => $results,
        ]);
    }

    public function gallery(): string
    {
        $db = Database::connect();
        $gallery = [];
        $type = (string) ($this->request->getGet('type') ?? '');
        try {
            if ($db->tableExists('gallery')) {
                $builder = $db->table('gallery');
                if ($db->tableExists('media')) {
                    $builder->select('gallery.*, media.filepath, media.mime_type')
                            ->join('media', 'media.id = gallery.media_id', 'left');
                }
                if ($type === 'photo') {
                    $builder->like('media.mime_type', 'image/', 'after');
                } elseif ($type === 'video') {
                    $builder->like('media.mime_type', 'video/', 'after');
                } elseif ($type === 'document') {
                    $builder->like('media.mime_type', 'application/', 'after');
                }
                $gallery = $builder->get()->getResultArray();
            }
        } catch (\Throwable $e) {
            log_message('error', 'PublicPortalController Gallery Exception: ' . $e->getMessage());
        }

        return view('public/gallery', [
            'activePage'  => 'gallery',
            'masjid'      => $this->resolveMasjidProfile(),
            'gallery'     => $gallery,
            'currentType' => $type,
        ]);
    }

    public function transparency(): string
    {
        $db = Database::connect();
        $transactions = [];
        $totalIncome = 0;
        $totalExpense = 0;
        try {
            if ($db->tableExists('financial_transactions')) {
                $transactions = $db->table('financial_transactions')
                    ->select('transaction_no, transaction_type, amount, transaction_date, description')
                    ->orderBy('transaction_date', 'DESC')
                    ->limit(20)
                    ->get()
                    ->getResultArray();

                $incQuery = $db->table('financial_transactions')->selectSum('amount')->where('transaction_type', 'INCOME')->get()->getRow();
                $totalIncome = (float) ($incQuery->amount ?? 0);

                $expQuery = $db->table('financial_transactions')->selectSum('amount')->where('transaction_type', 'EXPENSE')->get()->getRow();
                $totalExpense = (float) ($expQuery->amount ?? 0);
            }
        } catch (\Throwable $e) {
            log_message('error', 'PublicPortalController Transparency Exception: ' . $e->getMessage());
        }

        return view('public/transparency', [
            'activePage'   => 'transparency',
            'masjid'       => $this->resolveMasjidProfile(),
            'transactions' => $transactions,
            'totalIncome'  => $totalIncome,
            'totalExpense' => $totalExpense,
        ]);
    }

    public function prayerTimes(): string
    {
        $db = Database::connect();
        $prayerTimes = [];
        $prayerCity = 'Kota Masjid';
        $settings = [];

        if ($db->tableExists('settings')) {
            $rawSettings = $db->table('settings')->get()->getResultArray();
            foreach ($rawSettings as $s) {
                $settings[$s['setting_key']] = $s['setting_value'];
            }
        }

        $masjid = $this->getMasjidProfile();
        $configuredPrayerCity = (string) ($settings['prayer_city'] ?? '');
        if (!empty($configuredPrayerCity) && $configuredPrayerCity !== 'Kota Masjid') {
            $prayerCity = $configuredPrayerCity;
        } elseif (!empty($masjid['city'])) {
            $prayerCity = $masjid['city'];
        } else {
            $prayerCity = 'Kota Masjid';
        }

        if ($db->tableExists('prayer_times')) {
            $prayerTimes = $db->table('prayer_times')
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->get()
                ->getResultArray();
        }

        return view('public/prayer_times', [
            'activePage'   => 'prayer-times',
            'prayerTimes'  => $prayerTimes,
            'prayerCity'   => $prayerCity,
            'settings'     => $settings,
        ]);
    }
}
