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

        try {
            if ($tab === 'posts' && $db->tableExists('posts')) {
                $headers = ['Judul Berita / Artikel', 'Slug', 'Status', 'Tanggal Publish', 'Aksi'];
                $data = $db->table('posts')->orderBy('created_at', 'DESC')->get()->getResultArray();
                foreach ($data as $p) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($p['title']) . '</strong>',
                            '<span class="stat-mono">' . esc($p['slug']) . '</span>',
                            '<span class="badge badge-green">' . ($p['is_published'] ? 'PUBLISHED' : 'DRAFT') . '</span>',
                            esc(substr($p['created_at'], 0, 10)),
                            '<a href="/admin/cms/delete/posts/' . $p['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus berita ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>',
                        ]
                    ];
                }
            } elseif ($tab === 'kajian' && $db->tableExists('kajian')) {
                $headers = ['Penceramah / Ustadz', 'Tema Kajian', 'Jadwal & Jam', 'Lokasi Ruang', 'Status', 'Aksi'];
                $data = $db->table('kajian')->orderBy('schedule_date', 'ASC')->get()->getResultArray();
                foreach ($data as $k) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($k['speaker_name']) . '</strong>',
                            esc($k['topic']),
                            '<span class="stat-mono">' . esc($k['schedule_date']) . ' (' . esc(substr($k['schedule_time'], 0, 5)) . ' WIB)</span>',
                            esc($k['location']),
                            '<span class="badge badge-green">' . esc($k['status']) . '</span>',
                            '<a href="/admin/cms/delete/kajian/' . $k['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus kajian ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>',
                        ]
                    ];
                }
            } elseif ($tab === 'pages' && $db->tableExists('pages')) {
                $headers = ['Judul Halaman Statis', 'Slug', 'Status', 'Tanggal Dibuat', 'Aksi'];
                $data = $db->table('pages')->get()->getResultArray();
                foreach ($data as $pg) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($pg['title']) . '</strong>',
                            '<span class="stat-mono">' . esc($pg['slug']) . '</span>',
                            '<span class="badge badge-green">' . ($pg['is_published'] ? 'PUBLISHED' : 'DRAFT') . '</span>',
                            esc(substr($pg['created_at'], 0, 10)),
                            '<a href="/admin/cms/delete/pages/' . $pg['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus halaman ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>',
                        ]
                    ];
                }
            } elseif ($tab === 'gallery' && $db->tableExists('gallery')) {
                $headers = ['ID Media', 'Caption Foto', 'Media Object', 'Aksi'];
                $data = $db->table('gallery')->get()->getResultArray();
                foreach ($data as $g) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">MEDIA-' . esc($g['media_id']) . '</span>',
                            '<strong>' . esc($g['caption'] ?? 'Foto Kegiatan Masjid') . '</strong>',
                            '<span class="badge badge-green">GALLERY ASSET</span>',
                            '<a href="/admin/cms/delete/gallery/' . $g['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus foto ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>',
                        ]
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminCmsWorkspaceController Exception: ' . $e->getMessage());
        }

        $moduleLabels = [
            'posts'   => 'Berita & Artikel Warta Masjid',
            'kajian'  => 'Jadwal Kajian Rutin & Tematik',
            'pages'   => 'Halaman Statis CMS Portal',
            'gallery' => 'Galeri Foto & Media Kegiatan',
        ];

        return view('admin/cms/index', [
            'activeTab'         => $tab,
            'activeModuleLabel' => $moduleLabels[$tab] ?? 'Berita & Artikel Warta Masjid',
            'headers'           => $headers,
            'rows'              => $rows,
        ]);
    }

    public function create(): string
    {
        $tab = (string) ($this->request->getGet('tab') ?? 'posts');
        return view('admin/cms/create', [
            'tab' => $tab,
        ]);
    }

    public function store()
    {
        $db = Database::connect();
        $tab = (string) ($this->request->getPost('tab') ?? 'posts');

        try {
            if ($tab === 'posts') {
                $title = (string) $this->request->getPost('title');
                $slug = (string) ($this->request->getPost('slug') ?: url_title($title, '-', true));
                $content = (string) $this->request->getPost('content');
                $isPublished = (int) $this->request->getPost('is_published');

                $db->table('posts')->insert([
                    'author_id'    => 1,
                    'title'        => $title,
                    'slug'         => $slug,
                    'content'      => $content,
                    'is_published' => $isPublished,
                    'published_at' => $isPublished ? date('Y-m-d H:i:s') : null,
                    'created_at'   => date('Y-m-d H:i:s'),
                ]);
            } elseif ($tab === 'kajian') {
                $speakerName = (string) $this->request->getPost('speaker_name');
                $topic = (string) $this->request->getPost('topic');
                $date = (string) $this->request->getPost('schedule_date');
                $time = (string) $this->request->getPost('schedule_time');
                $location = (string) $this->request->getPost('location');

                $db->table('kajian')->insert([
                    'uuid'          => sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)),
                    'masjid_id'     => '1',
                    'speaker_name'  => $speakerName,
                    'topic'         => $topic,
                    'schedule_date' => $date,
                    'schedule_time' => $time,
                    'location'      => $location,
                    'status'        => 'UPCOMING',
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
            } elseif ($tab === 'pages') {
                $title = (string) $this->request->getPost('title');
                $slug = (string) ($this->request->getPost('slug') ?: url_title($title, '-', true));
                $content = (string) $this->request->getPost('content');
                $isPublished = (int) $this->request->getPost('is_published');

                $db->table('pages')->insert([
                    'title'        => $title,
                    'slug'         => $slug,
                    'content'      => $content,
                    'is_published' => $isPublished,
                    'created_at'   => date('Y-m-d H:i:s'),
                ]);
            } elseif ($tab === 'gallery') {
                $caption = (string) $this->request->getPost('caption');
                $filepath = (string) ($this->request->getPost('filepath') ?: '/assets/img/gallery-placeholder.jpg');

                $db->table('media')->insert([
                    'filename'   => basename($filepath),
                    'filepath'   => $filepath,
                    'mime_type'  => 'image/jpeg',
                    'filesize'   => 102400,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $mediaId = $db->insertID();

                $db->table('gallery')->insert([
                    'media_id' => $mediaId,
                    'caption'  => $caption,
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminCmsWorkspaceController Store Exception: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/cms?tab=' . $tab));
    }

    public function delete(string $type, int $id)
    {
        $db = Database::connect();
        try {
            $allowedTables = ['posts', 'kajian', 'pages', 'gallery'];
            if (in_array($type, $allowedTables, true) && $db->tableExists($type)) {
                $db->table($type)->where('id', $id)->delete();
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminCmsWorkspaceController Delete Exception: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/cms?tab=' . $type));
    }
}
