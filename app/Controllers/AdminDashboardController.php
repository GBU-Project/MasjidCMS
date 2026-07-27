<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AdminDashboardController extends BaseController
{
    public function index(): string
    {
        return view('admin/dashboard/index');
    }
}
