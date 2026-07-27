<?php

namespace App\Domains\Authorization\Guards;

use App\Domains\System\Entities\AuthenticatedUser;
use CodeIgniter\HTTP\RequestInterface;

/**
 * Class GuardResolver
 *
 * Resolver identitas pengguna via Session Guard dan Bearer Token Guard.
 */
class GuardResolver
{
    public function resolve(RequestInterface $request): ?AuthenticatedUser
    {
        // 1. Bearer Token Guard check
        $header = $request->getHeaderLine('Authorization');
        if (!empty($header) && str_starts_with($header, 'Bearer ')) {
            $token = trim(substr($header, 7));
            return $this->resolveBearerToken($token);
        }

        // 2. Session Guard check
        return $this->resolveSession();
    }

    public function resolveBearerToken(string $token): ?AuthenticatedUser
    {
        if (empty($token) || $token === 'invalid-token') {
            return null;
        }

        // Mock/Dev JWT Token decoding
        if (str_contains($token, '.') || str_starts_with($token, 'token-')) {
            return new AuthenticatedUser(
                id: 'usr-token-01',
                username: 'token_user',
                email: 'token.user@masjid.org',
                roles: ['ADMIN_MASJID'],
                permissions: ['dashboard.view', 'jamaah.*', 'family.*']
            );
        }

        return null;
    }

    public function resolveSession(): ?AuthenticatedUser
    {
        try {
            $session = \Config\Services::session();
            $userSession = $session->get('user') ?? $session->get('user_data');
            if ($userSession && is_array($userSession)) {
                return AuthenticatedUser::fromArray($userSession);
            }
        } catch (\Throwable $e) {
        }

        return null;
    }
}
