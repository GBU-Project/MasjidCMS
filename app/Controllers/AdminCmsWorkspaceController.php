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
                            esc(substr($p['created_at'] ?? date('Y-m-d'), 0, 10)),
                            '<div style="display:flex; gap:4px;">' .
                            '<a href="/admin/cms/edit/posts/' . $p['id'] . '" class="btn btn-secondary" style="padding: 2px 8px; font-size: 12px;">Edit</a>' .
                            '<a href="/admin/cms/delete/posts/' . $p['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus berita ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>' .
                            '</div>',
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
                            '<span class="stat-mono">' . esc($k['schedule_date']) . ' (' . esc(substr($k['schedule_time'] ?? '', 0, 5)) . ' WIB)</span>',
                            esc($k['location']),
                            '<span class="badge badge-green">' . esc($k['status']) . '</span>',
                            '<div style="display:flex; gap:4px;">' .
                            '<a href="/admin/cms/edit/kajian/' . $k['id'] . '" class="btn btn-secondary" style="padding: 2px 8px; font-size: 12px;">Edit</a>' .
                            '<a href="/admin/cms/delete/kajian/' . $k['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus kajian ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>' .
                            '</div>',
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
                            esc(substr($pg['created_at'] ?? date('Y-m-d'), 0, 10)),
                            '<div style="display:flex; gap:4px;">' .
                            '<a href="/admin/cms/edit/pages/' . $pg['id'] . '" class="btn btn-secondary" style="padding: 2px 8px; font-size: 12px;">Edit</a>' .
                            '<a href="/admin/cms/delete/pages/' . $pg['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus halaman ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>' .
                            '</div>',
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
                            '<div style="display:flex; gap:4px;">' .
                            '<a href="/admin/cms/edit/gallery/' . $g['id'] . '" class="btn btn-secondary" style="padding: 2px 8px; font-size: 12px;">Edit</a>' .
                            '<a href="/admin/cms/delete/gallery/' . $g['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus foto ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>' .
                            '</div>',
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
                $rules = [
                    'title'   => 'required|min_length[3]',
                    'content' => 'required',
                ];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Berita: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $title = (string) $this->request->getPost('title');
                $inputSlug = trim((string) $this->request->getPost('slug'));
                $slug = !empty($inputSlug) ? url_title($inputSlug, '-', true) : url_title($title, '-', true);
                $content = (string) $this->request->getPost('content');
                $isPublished = (int) $this->request->getPost('is_published');

                // Get valid user ID UUID if exists
                $authorId = null;
                if ($db->tableExists('users')) {
                    $userRow = $db->table('users')->get()->getRowArray();
                    if ($userRow) {
                        $authorId = $userRow['id'] ?? $userRow['uuid'] ?? null;
                    }
                }

                $db->table('posts')->insert([
                    'author_id'    => $authorId,
                    'title'        => $title,
                    'slug'         => $slug,
                    'content'      => $content,
                    'is_published' => $isPublished,
                    'published_at' => $isPublished ? date('Y-m-d H:i:s') : null,
                    'created_at'   => date('Y-m-d H:i:s'),
                ]);

                session()->setFlashdata('success', 'Berita "' . esc($title) . '" berhasil ditambahkan.');

            } elseif ($tab === 'kajian') {
                $rules = [
                    'speaker_name'  => 'required',
                    'topic'         => 'required',
                    'schedule_date' => 'required',
                    'schedule_time' => 'required',
                    'location'      => 'required',
                ];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Kajian: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $speakerName = (string) $this->request->getPost('speaker_name');
                $topic = (string) $this->request->getPost('topic');
                $date = (string) $this->request->getPost('schedule_date');
                $time = (string) $this->request->getPost('schedule_time');
                $location = (string) $this->request->getPost('location');

                $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));

                $db->table('kajian')->insert([
                    'uuid'          => $uuid,
                    'masjid_id'     => '1',
                    'speaker_name'  => $speakerName,
                    'topic'         => $topic,
                    'schedule_date' => $date,
                    'schedule_time' => $time,
                    'location'      => $location,
                    'status'        => 'UPCOMING',
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);

                session()->setFlashdata('success', 'Jadwal Kajian "' . esc($topic) . '" berhasil ditambahkan.');

            } elseif ($tab === 'pages') {
                $rules = [
                    'title'   => 'required|min_length[3]',
                    'content' => 'required',
                ];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Halaman Statis: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $title = (string) $this->request->getPost('title');
                $inputSlug = trim((string) $this->request->getPost('slug'));
                $slug = !empty($inputSlug) ? url_title($inputSlug, '-', true) : url_title($title, '-', true);
                $content = (string) $this->request->getPost('content');
                $isPublished = (int) $this->request->getPost('is_published');

                $db->table('pages')->insert([
                    'title'        => $title,
                    'slug'         => $slug,
                    'content'      => $content,
                    'is_published' => $isPublished,
                    'created_at'   => date('Y-m-d H:i:s'),
                ]);

                session()->setFlashdata('success', 'Halaman Statis "' . esc($title) . '" berhasil ditambahkan.');

            } elseif ($tab === 'gallery') {
                $rules = [
                    'caption' => 'required',
                ];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Galeri Foto: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

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

                session()->setFlashdata('success', 'Foto Galeri "' . esc($caption) . '" berhasil ditambahkan.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminCmsWorkspaceController Store Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }

        return redirect()->to(site_url('admin/cms?tab=' . $tab));
    }

    public function edit(string $type, string $id): string
    {
        $db = Database::connect();
        $allowedTables = ['posts', 'kajian', 'pages', 'gallery'];
        $item = null;

        if (in_array($type, $allowedTables, true) && $db->tableExists($type)) {
            if ($type === 'gallery') {
                $builder = $db->table('gallery');
                if ($db->tableExists('media')) {
                    $builder->select('gallery.*, media.filepath')
                            ->join('media', 'media.id = gallery.media_id', 'left');
                }
                $item = $builder->where('gallery.id', $id)->get()->getRowArray();
            } else {
                $item = $db->table($type)->where('id', $id)->get()->getRowArray();
            }
        }

        if (!$item) {
            session()->setFlashdata('error', 'Data tidak ditemukan di database.');
            return redirect()->to(site_url('admin/cms?tab=' . $type));
        }

        return view('admin/cms/edit', [
            'tab'  => $type,
            'id'   => $id,
            'item' => $item,
        ]);
    }

    public function update()
    {
        $db = Database::connect();
        $tab = (string) ($this->request->getPost('tab') ?? 'posts');
        $id = (string) $this->request->getPost('id');

        try {
            if ($tab === 'posts') {
                $rules = ['title' => 'required|min_length[3]', 'content' => 'required'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal memperbarui Berita: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $title = (string) $this->request->getPost('title');
                $inputSlug = trim((string) $this->request->getPost('slug'));
                $slug = !empty($inputSlug) ? url_title($inputSlug, '-', true) : url_title($title, '-', true);

                $db->table('posts')->where('id', $id)->update([
                    'title'        => $title,
                    'slug'         => $slug,
                    'content'      => (string) $this->request->getPost('content'),
                    'is_published' => (int) $this->request->getPost('is_published'),
                ]);
                session()->setFlashdata('success', 'Berita berhasil diperbarui.');

            } elseif ($tab === 'kajian') {
                $rules = ['speaker_name' => 'required', 'topic' => 'required'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal memperbarui Kajian: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $db->table('kajian')->where('id', $id)->update([
                    'speaker_name'  => (string) $this->request->getPost('speaker_name'),
                    'topic'         => (string) $this->request->getPost('topic'),
                    'schedule_date' => (string) $this->request->getPost('schedule_date'),
                    'schedule_time' => (string) $this->request->getPost('schedule_time'),
                    'location'      => (string) $this->request->getPost('location'),
                ]);
                session()->setFlashdata('success', 'Jadwal Kajian berhasil diperbarui.');

            } elseif ($tab === 'pages') {
                $rules = ['title' => 'required|min_length[3]', 'content' => 'required'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal memperbarui Halaman Statis: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $title = (string) $this->request->getPost('title');
                $inputSlug = trim((string) $this->request->getPost('slug'));
                $slug = !empty($inputSlug) ? url_title($inputSlug, '-', true) : url_title($title, '-', true);

                $db->table('pages')->where('id', $id)->update([
                    'title'        => $title,
                    'slug'         => $slug,
                    'content'      => (string) $this->request->getPost('content'),
                    'is_published' => (int) $this->request->getPost('is_published'),
                ]);
                session()->setFlashdata('success', 'Halaman Statis berhasil diperbarui.');

            } elseif ($tab === 'gallery') {
                $rules = ['caption' => 'required'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal memperbarui Galeri: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $caption = (string) $this->request->getPost('caption');
                $filepath = (string) ($this->request->getPost('filepath') ?: '/assets/img/gallery-placeholder.jpg');

                $gRow = $db->table('gallery')->where('id', $id)->get()->getRowArray();
                if ($gRow) {
                    $db->table('gallery')->where('id', $id)->update(['caption' => $caption]);
                    if (!empty($gRow['media_id'])) {
                        $db->table('media')->where('id', $gRow['media_id'])->update([
                            'filename' => basename($filepath),
                            'filepath' => $filepath,
                        ]);
                    }
                }
                session()->setFlashdata('success', 'Foto Galeri berhasil diperbarui.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminCmsWorkspaceController Update Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan sistem saat memperbarui data: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }

        return redirect()->to(site_url('admin/cms?tab=' . $tab));
    }

    public function delete(string $type, string $id)
    {
        $db = Database::connect();
        try {
            $allowedTables = ['posts', 'kajian', 'pages', 'gallery'];
            if (in_array($type, $allowedTables, true) && $db->tableExists($type)) {
                $db->table($type)->where('id', $id)->delete();
                session()->setFlashdata('success', 'Data ' . esc($type) . ' berhasil dihapus.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminCmsWorkspaceController Delete Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menghapus data: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/cms?tab=' . $type));
    }
}
