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

    public function gallery(): string
    {
        $db = Database::connect();
        $gallery = [];
        try {
            if ($db->tableExists('gallery')) {
                $builder = $db->table('gallery');
                if ($db->tableExists('media')) {
                    $builder->select('gallery.*, media.filepath')
                            ->join('media', 'media.id = gallery.media_id', 'left');
                }
                $gallery = $builder->get()->getResultArray();
            }
        } catch (\Throwable $e) {
            log_message('error', 'PublicPortalController Gallery Exception: ' . $e->getMessage());
        }

        return view('public/gallery', [
            'activePage' => 'gallery',
            'gallery'    => $gallery,
        ]);
    }

    public function transparency(): string
    {
        $db = Database::connect();
        $transactions = [];
        $totalIncome = 0;
        $totalExpense = 0;
        try {
            if ($db->tableExists('financial_transactions')) {
                $transactions = $db->table('financial_transactions')
                    ->select('transaction_no, transaction_type, amount, transaction_date, description')
                    ->orderBy('transaction_date', 'DESC')
                    ->limit(20)
                    ->get()
                    ->getResultArray();

                $incQuery = $db->table('financial_transactions')->selectSum('amount')->where('transaction_type', 'INCOME')->get()->getRow();
                $totalIncome = (float) ($incQuery->amount ?? 0);

                $expQuery = $db->table('financial_transactions')->selectSum('amount')->where('transaction_type', 'EXPENSE')->get()->getRow();
                $totalExpense = (float) ($expQuery->amount ?? 0);
            }
        } catch (\Throwable $e) {
            log_message('error', 'PublicPortalController Transparency Exception: ' . $e->getMessage());
        }

        return view('public/transparency', [
            'activePage'   => 'transparency',
            'transactions' => $transactions,
            'totalIncome'  => $totalIncome,
            'totalExpense' => $totalExpense,
        ]);
    }
}
