<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class AdminCmsWorkspaceController extends BaseController
{
    public function index(): string
    {
        $db = Database::connect();
        $tab = (string) ($this->request->getGet('tab') ?? 'posts');

        $headers = [];
        $rows = [];

        if ($tab === 'posts' && $db->tableExists('posts')) {
            $headers = ['Judul Berita / Artikel', 'Slug', 'Status', 'Tanggal Publish'];
            $data = $db->table('posts')->orderBy('created_at', 'DESC')->get()->getResultArray();
            foreach ($data as $p) {
                $rows[] = [
                    'columns' => [
                        '<strong>' . esc($p['title']) . '</strong>',
                        '<span class="stat-mono">' . esc($p['slug']) . '</span>',
                        '<span class="badge badge-green">' . ($p['is_published'] ? 'PUBLISHED' : 'DRAFT') . '</span>',
                        esc(substr($p['created_at'], 0, 10)),
                    ]
                ];
            }
        } elseif ($tab === 'pages' && $db->tableExists('pages')) {
            $headers = ['Judul Halaman Statis', 'Slug', 'Status', 'Tanggal Dibuat'];
            $data = $db->table('pages')->get()->getResultArray();
            foreach ($data as $pg) {
                $rows[] = [
                    'columns' => [
                        '<strong>' . esc($pg['title']) . '</strong>',
                        '<span class="stat-mono">' . esc($pg['slug']) . '</span>',
                        '<span class="badge badge-green">' . ($pg['is_published'] ? 'PUBLISHED' : 'DRAFT') . '</span>',
                        esc(substr($pg['created_at'], 0, 10)),
                    ]
                ];
            }
        } elseif ($tab === 'gallery' && $db->tableExists('gallery')) {
            $headers = ['ID Media', 'Caption Foto', 'Media Object'];
            $data = $db->table('gallery')->get()->getResultArray();
            foreach ($data as $g) {
                $rows[] = [
                    'columns' => [
                        '<span class="stat-mono">MEDIA-' . esc($g['media_id']) . '</span>',
                        '<strong>' . esc($g['caption'] ?? 'Foto Kegiatan Masjid') . '</strong>',
                        '<span class="badge badge-green">GALLERY ASSET</span>',
                    ]
                ];
            }
        }

        $moduleLabels = [
            'posts'   => 'Berita & Artikel Kajian',
            'pages'   => 'Halaman Statis CMS',
            'gallery' => 'Galeri Foto & Media',
        ];

        return view('admin/cms/index', [
            'activeTab'         => $tab,
            'activeModuleLabel' => $moduleLabels[$tab] ?? 'Berita & Artikel Kajian',
            'headers'           => $headers,
            'rows'              => $rows,
        ]);
    }
}
