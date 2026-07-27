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
        $db = Database::connect();
        $posts = [];
        $kajianList = [];

        if ($db->tableExists('posts')) {
            $posts = $db->table('posts')->where('is_published', 1)->orderBy('created_at', 'DESC')->get()->getResultArray();
        }
        if ($db->tableExists('kajian')) {
            $kajianList = $db->table('kajian')->where('status', 'UPCOMING')->orderBy('schedule_date', 'ASC')->get()->getResultArray();
        }

        return view('public/news', [
            'activePage' => 'news',
            'posts'      => $posts,
            'kajianList' => $kajianList,
        ]);
    }

    public function programs(): string
    {
        $db = Database::connect();
        $programs = [];

        if ($db->tableExists('programs')) {
            $programs = $db->table('programs')->where('is_active', 1)->get()->getResultArray();
        }

        return view('public/programs', [
            'activePage' => 'programs',
            'programs'   => $programs,
        ]);
    }

    public function donation(): string
    {
        $db = Database::connect();
        $accounts = [];

        if ($db->tableExists('financial_accounts')) {
            $accounts = $db->table('financial_accounts')->get()->getResultArray();
        }

        return view('public/donation', [
            'activePage' => 'donation',
            'accounts'   => $accounts,
        ]);
    }

    public function contact(): string
    {
        return view('public/contact', ['activePage' => 'contact']);
    }
}
