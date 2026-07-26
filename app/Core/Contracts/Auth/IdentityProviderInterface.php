<?php

namespace App\Core\Contracts\Auth;

use App\Domains\System\Entities\AuthenticatedUser;

/**
 * Interface IdentityProviderInterface
 *
 * Kontrak standar untuk seluruh penyedia identitas (Identity Provider) di MasjidCMS.
 * Mengisolasi Authentication Engine dari detail sumber data (DB, LDAP, OAuth, SAML, dll).
 */
interface IdentityProviderInterface
{
    /**
     * Mencari pengguna berdasarkan identitas pengenal (username/email/ID unik).
     *
     * @param string $identifier
     * @return AuthenticatedUser|null
     */
    public function findByIdentifier(string $identifier): ?AuthenticatedUser;

    /**
     * Mencari pengguna berdasarkan ID unik sistem.
     *
     * @param int|string $id
     * @return AuthenticatedUser|null
     */
    public function findById(int|string $id): ?AuthenticatedUser;

    /**
     * Memverifikasi keabsahan kata sandi / kredensial pengguna.
     *
     * @param AuthenticatedUser $user
     * @param string $password
     * @return bool
     */
    public function validateCredential(AuthenticatedUser $user, string $password): bool;

    /**
     * Memeriksa apakah provider mendukung fitur Remember Me.
     *
     * @return bool
     */
    public function supportsRememberMe(): bool;

    /**
     * Memeriksa apakah provider mendukung perubahan password secara internal.
     *
     * @return bool
     */
    public function supportsPasswordChange(): bool;
}
