<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class PublicPortalController extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function index(): string
    {
        helper(['form', 'url']);
        $db = Database::connect();

        $masjidName = 'Masjid Agung Darussalam';
        $activePrograms = [];
        $activeServices = [];
        $pengurusList = [];
        $latestPosts = [];
        $settings = [];

        if ($db->tableExists('settings')) {
            $rawSettings = $db->table('settings')->get()->getResultArray();
            foreach ($rawSettings as $s) {
                $settings[$s['setting_key']] = $s['setting_value'];
            }
        }

        $defaultOrder = ['hero', 'prayer', 'profile', 'program', 'layanan', 'pengurus', 'kajian', 'gallery', 'donation'];
        $sectionOrder = $defaultOrder;
        if (!empty($settings['homepage_section_order'])) {
            $decoded = json_decode($settings['homepage_section_order'], true);
            if (is_array($decoded) && count($decoded) > 0) {
                $sectionOrder = $decoded;
            }
        }

        $limitProgram = (int) ($settings['limit_program'] ?? 6);
        $limitLayanan = (int) ($settings['limit_layanan'] ?? 4);
        $limitPengurus = (int) ($settings['limit_pengurus'] ?? 3);
        $limitKajian = (int) ($settings['limit_kajian'] ?? 6);

        $masjid = null;
        if ($db->tableExists('masjids')) {
            $masjid = $db->table('masjids')->get()->getRowArray();
            if ($masjid) {
                $masjidName = $masjid['name'];
            }
        }

        if ($db->tableExists('program_kegiatan')) {
            $builder = $db->table('program_kegiatan');
            if ($db->tableExists('bidang')) {
                $builder->select('program_kegiatan.*, bidang.name as bidang_name')
                        ->join('bidang', 'bidang.id = program_kegiatan.bidang_id', 'left');
            }
            if ($db->fieldExists('homepage_visible', 'program_kegiatan')) {
                $builder->where('program_kegiatan.homepage_visible', 1);
            }
            $activePrograms = $builder->where('program_kegiatan.status', 'ACTIVE')
                                     ->orderBy('program_kegiatan.created_at', 'DESC')
                                     ->limit($limitProgram)
                                     ->get()
                                     ->getResultArray();
        }

        if ($db->tableExists('layanan_masjid')) {
            $builder = $db->table('layanan_masjid')->where('status', 'ACTIVE');
            if ($db->fieldExists('homepage_visible', 'layanan_masjid')) {
                $builder->where('homepage_visible', 1);
            }
            $activeServices = $builder->orderBy('urutan', 'ASC')->limit($limitLayanan)->get()->getResultArray();
        }

        if ($db->tableExists('pengurus')) {
            $builder = $db->table('pengurus')->where('pengurus.status', 'ACTIVE');
            if ($db->tableExists('bidang')) {
                $builder->select('pengurus.*, bidang.name as bidang_name')
                        ->join('bidang', 'bidang.id = pengurus.bidang_id', 'left');
            }
            if ($db->fieldExists('homepage_visible', 'pengurus')) {
                $builder->where('pengurus.homepage_visible', 1);
            }
            $pengurusList = $builder->orderBy('pengurus.urutan', 'ASC')->limit($limitPengurus)->get()->getResultArray();
        }

        if ($db->tableExists('posts')) {
            $latestPosts = $db->table('posts')->where('is_published', 1)->orderBy('created_at', 'DESC')->limit($limitKajian)->get()->getResultArray();
        }

        $kajianList = [];
        if ($db->tableExists('kajian')) {
            $kajianList = $db->table('kajian')->orderBy('schedule_date', 'DESC')->limit(6)->get()->getResultArray();
        }

        $financialSummary = [
            'total_balance' => 0,
            'total_income'  => 0,
            'total_expense' => 0,
        ];
        if ($db->tableExists('financial_accounts')) {
            $sumObj = $db->table('financial_accounts')->selectSum('balance', 'tot')->get()->getRowArray();
            $financialSummary['total_balance'] = (float) ($sumObj['tot'] ?? 0);
        }
        if ($db->tableExists('financial_transactions')) {
            $typeCol = $db->fieldExists('transaction_type', 'financial_transactions') ? 'transaction_type' : ($db->fieldExists('type', 'financial_transactions') ? 'type' : null);
            if ($typeCol) {
                $incObj = $db->table('financial_transactions')->selectSum('amount', 'tot')->where($typeCol, 'INCOME')->get()->getRowArray();
                $expObj = $db->table('financial_transactions')->selectSum('amount', 'tot')->where($typeCol, 'EXPENSE')->get()->getRowArray();
                $financialSummary['total_income']  = (float) ($incObj['tot'] ?? 0);
                $financialSummary['total_expense'] = (float) ($expObj['tot'] ?? 0);
            }
        }

        return view('public/index', [
            'activePage'       => 'home',
            'masjidName'       => $masjidName,
            'masjid'           => $masjid,
            'sectionOrder'     => $sectionOrder,
            'activePrograms'   => $activePrograms,
            'activeServices'   => $activeServices,
            'pengurusList'     => $pengurusList,
            'latestPosts'      => $latestPosts,
            'kajianList'       => $kajianList,
            'financialSummary' => $financialSummary,
            'settings'         => $settings,
            'donationSettings' => $settings,
        ]);
    }

    public function profile(): string
    {
        $db = Database::connect();
        $masjid = null;
        if ($db->tableExists('masjids')) {
            $masjid = $db->table('masjids')->get()->getRowArray();
        }
        return view('public/profile', [
            'activePage' => 'profile',
            'masjid'     => $masjid,
        ]);
    }

    public function orgStructure(): string
    {
        $db = Database::connect();
        $pengurusList = [];
        $bidangList = [];

        if ($db->tableExists('bidang')) {
            $bidangList = $db->table('bidang')->where('deleted_at', null)->orderBy('sort_order', 'ASC')->get()->getResultArray();
        }

        if ($db->tableExists('pengurus')) {
            $builder = $db->table('pengurus');
            if ($db->tableExists('bidang')) {
                $builder->select('pengurus.*, bidang.name as bidang_name')
                        ->join('bidang', 'bidang.id = pengurus.bidang_id', 'left');
            }
            $pengurusList = $builder->orderBy('pengurus.urutan', 'ASC')->get()->getResultArray();
        }

        return view('public/org_structure', [
            'activePage'   => 'org_structure',
            'pengurusList' => $pengurusList,
            'bidangList'   => $bidangList,
        ]);
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

        if ($db->tableExists('program_kegiatan')) {
            $builder = $db->table('program_kegiatan');
            if ($db->tableExists('bidang')) {
                $builder->select('program_kegiatan.*, bidang.name as bidang_name')
                        ->join('bidang', 'bidang.id = program_kegiatan.bidang_id', 'left');
            }
            $programs = $builder->where('program_kegiatan.status', 'ACTIVE')->orderBy('program_kegiatan.created_at', 'DESC')->get()->getResultArray();
        }

        return view('public/programs', [
            'activePage' => 'programs',
            'programs'   => $programs,
        ]);
    }

    public function services(): string
    {
        $db = Database::connect();
        $services = [];

        if ($db->tableExists('layanan_masjid')) {
            $services = $db->table('layanan_masjid')->where('status', 'ACTIVE')->orderBy('urutan', 'ASC')->get()->getResultArray();
        }

        return view('public/services', [
            'activePage' => 'services',
            'services'   => $services,
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
