<?php

namespace App\Controllers\Admin;

use App\Core\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class FinancialController extends BaseController
{
    public function index(): ResponseInterface
    {
        $data = [
            'title' => 'Manajemen Keuangan & Kas Masjid',
        ];

        return $this->response->setBody(json_encode([
            'status'  => 'success',
            'message' => 'Admin Financial Dashboard Overview',
            'data'    => $data,
        ]));
    }
}
