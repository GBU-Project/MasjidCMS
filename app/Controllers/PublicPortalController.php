<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class PublicPortalController extends BaseController
{
    public function index(): string
    {
        $db = Database::connect();

        $masjidName = 'Masjid Agung Darussalam';
        $activePrograms = [];
        $latestPosts = [];

        if ($db->tableExists('masjids')) {
            $m = $db->table('masjids')->get()->getRowArray();
            if ($m) {
                $masjidName = $m['name'];
            }
        }
        if ($db->tableExists('programs')) {
            $activePrograms = $db->table('programs')->where('is_active', 1)->get()->getResultArray();
        }
        if ($db->tableExists('posts')) {
            $latestPosts = $db->table('posts')->where('is_published', 1)->orderBy('created_at', 'DESC')->limit(3)->get()->getResultArray();
        }

        return view('public/index', [
            'activePage'     => 'home',
            'masjidName'     => $masjidName,
            'activePrograms' => $activePrograms,
            'latestPosts'    => $latestPosts,
        ]);
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
