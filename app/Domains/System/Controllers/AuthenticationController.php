<?php

namespace App\Domains\System\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Exceptions\AuthorizationException;
use App\Core\Exceptions\DomainException;
use App\Core\Exceptions\ValidationException;
use App\Domains\System\DTO\LoginRequest;
use App\Domains\System\Services\AuthenticationService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class AuthenticationController
 *
 * Orchestration controller untuk menangani HTTP Request/Response proses otentikasi.
 * DILARANG mengandung logika bisnis atau manipulasi Session secara langsung.
 */
class AuthenticationController extends BaseController
{
    protected AuthenticationService $authService;

    public function __construct(?AuthenticationService $authService = null)
    {
        $this->authService = $authService ?? new AuthenticationService();
    }

    /**
     * Endpoint untuk memproses Login HTTP Request.
     *
     * @return ResponseInterface
     */
    public function login(): ResponseInterface
    {
        try {
            $rawInput = $this->request->getJSON(true) ?? $this->request->getPost();

            $loginDto = new LoginRequest(
                username: (string) ($rawInput['username'] ?? ''),
                password: (string) ($rawInput['password'] ?? ''),
                remember: (bool) ($rawInput['remember'] ?? false)
            );

            $user = $this->authService->login($loginDto);

            return $this->respondSuccess([
                'user' => [
                    'id'          => $user->id,
                    'username'    => $user->username,
                    'displayName' => $user->displayName(),
                    'email'       => $user->email,
                ],
            ], 'Login successful');
        } catch (ValidationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), $e->getCode());
        } catch (AuthorizationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), $e->getCode());
        } catch (DomainException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), $e->getCode());
        } catch (\Throwable $e) {
            return $this->respondError('An unexpected error occurred during login.', null, 500);
        }
    }

    /**
     * Endpoint untuk memproses Logout.
     *
     * @return ResponseInterface
     */
    public function logout(): ResponseInterface
    {
        try {
            $this->authService->logout();
            return $this->respondSuccess(null, 'Logout successful');
        } catch (\Throwable $e) {
            return $this->respondError('Failed to logout.', null, 500);
        }
    }

    /**
     * Endpoint untuk memperbarui Session ID (Refresh).
     *
     * @return ResponseInterface
     */
    public function refresh(): ResponseInterface
    {
        try {
            $this->authService->refresh();
            return $this->respondSuccess(null, 'Session refreshed successfully');
        } catch (\Throwable $e) {
            return $this->respondError('Failed to refresh session.', null, 500);
        }
    }
}
