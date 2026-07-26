<?php

namespace App\Domains\System\Controllers;

use App\Core\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class AuthenticationController
 *
 * Controller skeleton untuk penanganan request otentikasi.
 */
class AuthenticationController extends BaseController
{
    /**
     * Endpoint login (Placeholder).
     *
     * @return ResponseInterface
     */
    public function login(): ResponseInterface
    {
        return $this->respondError('Not Implemented', null, 501);
    }

    /**
     * Endpoint logout (Placeholder).
     *
     * @return ResponseInterface
     */
    public function logout(): ResponseInterface
    {
        return $this->respondError('Not Implemented', null, 501);
    }

    /**
     * Endpoint refresh token/session (Placeholder).
     *
     * @return ResponseInterface
     */
    public function refresh(): ResponseInterface
    {
        return $this->respondError('Not Implemented', null, 501);
    }
}
