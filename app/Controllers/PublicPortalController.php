<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;

class PublicPortalController extends BaseController
{
    public function index(): string
    {
        return view('public/index', ['activePage' => 'home']);
    }

    public function profile(): string
    {
        return view('public/profile', ['activePage' => 'profile']);
    }

    public function news(): string
    {
        return view('public/news', ['activePage' => 'news']);
    }

    public function programs(): string
    {
        return view('public/programs', ['activePage' => 'programs']);
    }

    public function donation(): string
    {
        return view('public/donation', ['activePage' => 'donation']);
    }

    public function contact(): string
    {
        return view('public/contact', ['activePage' => 'contact']);
    }
}
