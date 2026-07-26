<?php

namespace App\Domains\System\Events;

use App\Domains\System\Entities\AuthenticatedUser;
use Config\Services;
use Psr\Log\LoggerInterface;

/**
 * Class AuthenticationEvent
 *
 * Event hook placeholder untuk mencatat aktivitas otentikasi (Audit Trail / Activity Log).
 */
class AuthenticationEvent
{
    protected LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? Services::logger();
    }

    /**
     * Handler saat login berhasil.
     */
    public function onLogin(AuthenticatedUser $user): void
    {
        $this->logger->info(sprintf('User logged in successfully: %s (ID: %s)', $user->username, (string) $user->id));
    }

    /**
     * Handler saat logout berhasil.
     */
    public function onLogout(?AuthenticatedUser $user): void
    {
        $username = $user ? $user->username : 'Unknown/Guest';
        $this->logger->info(sprintf('User logged out: %s', $username));
    }
}
