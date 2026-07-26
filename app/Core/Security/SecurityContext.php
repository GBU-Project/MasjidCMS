<?php

namespace App\Core\Security;

use App\Domains\System\Entities\AuthenticatedUser;

/**
 * Class SecurityContext
 *
 * Singleton / Request-Scoped Context yang menyimpan entitas pengguna terotentikasi
 * selama siklus hidup satu HTTP Request.
 */
class SecurityContext
{
    /**
     * Instansi user yang sedang terotentikasi untuk request ini.
     */
    protected static ?AuthenticatedUser $currentUser = null;

    /**
     * Menyimpan entitas user ke dalam konteks keamanan request saat ini.
     *
     * @param AuthenticatedUser $user
     * @return void
     */
    public static function setUser(AuthenticatedUser $user): void
    {
        self::$currentUser = $user;
    }

    /**
     * Mendapatkan entitas user terotentikasi saat ini.
     *
     * @return AuthenticatedUser|null
     */
    public static function user(): ?AuthenticatedUser
    {
        return self::$currentUser;
    }

    /**
     * Mendapatkan ID user terotentikasi saat ini.
     *
     * @return int|string|null
     */
    public static function id(): int|string|null
    {
        return self::$currentUser?->id;
    }

    /**
     * Memeriksa apakah request berasal dari guest (belum login).
     *
     * @return bool
     */
    public static function guest(): bool
    {
        return self::$currentUser === null;
    }

    /**
     * Memeriksa apakah request berasal dari user yang sudah terotentikasi.
     *
     * @return bool
     */
    public static function authenticated(): bool
    {
        return self::$currentUser !== null;
    }

    /**
     * Membersihkan konteks keamanan (diakhir request atau saat logout).
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$currentUser = null;
    }
}
