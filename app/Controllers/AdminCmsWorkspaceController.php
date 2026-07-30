<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class AdminCmsWorkspaceController extends BaseController
{
    public function index(): string
    {
        $db = Database::connect();
        $path = $this->request->getUri()->getPath();
        $defaultTab = 'posts';
        if (str_contains($path, 'program')) {
            $defaultTab = 'program';
        } elseif (str_contains($path, 'layanan')) {
            $defaultTab = 'layanan';
        } elseif (str_contains($path, 'kajian')) {
            $defaultTab = 'kajian';
        } elseif (str_contains($path, 'agenda')) {
            $defaultTab = 'agenda';
        } elseif (str_contains($path, 'pages')) {
            $defaultTab = 'pages';
        } elseif (str_contains($path, 'gallery')) {
            $defaultTab = 'gallery';
        }

        $tab = (string) ($this->request->getGet('tab') ?? $defaultTab);

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
            } elseif ($tab === 'agenda' && $db->tableExists('agenda')) {
                $headers = ['Judul Agenda', 'Tanggal & Jam', 'Lokasi', 'Status', 'Aksi'];
                $data = $db->table('agenda')->orderBy('event_date', 'ASC')->get()->getResultArray();
                foreach ($data as $a) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($a['title']) . '</strong>',
                            '<span class="stat-mono">' . esc($a['event_date']) . ' (' . esc(substr($a['event_time'] ?? '', 0, 5)) . ' WIB)</span>',
                            esc($a['location'] ?? '-'),
                            '<span class="badge badge-green">' . esc($a['status']) . '</span>',
                            '<div style="display:flex; gap:4px;">' .
                            '<a href="/admin/cms/edit/agenda/' . $a['id'] . '" class="btn btn-secondary" style="padding: 2px 8px; font-size: 12px;">Edit</a>' .
                            '<a href="/admin/cms/delete/agenda/' . $a['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus agenda ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>' .
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
            } elseif ($tab === 'program' && $db->tableExists('program_kegiatan')) {
                $headers = ['Nama Program / Kegiatan', 'Bidang', 'Penanggung Jawab', 'Lokasi', 'Status', 'Aksi'];
                $builder = $db->table('program_kegiatan');
                if ($db->tableExists('bidang')) {
                    $builder->select('program_kegiatan.*, bidang.name as bidang_name')
                            ->join('bidang', 'bidang.id = program_kegiatan.bidang_id', 'left');
                }
                $data = $builder->orderBy('program_kegiatan.created_at', 'DESC')->get()->getResultArray();
                foreach ($data as $pr) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($pr['nama']) . '</strong>',
                            '<span class="badge badge-blue">' . esc($pr['bidang_name'] ?? 'Umum') . '</span>',
                            esc($pr['penanggung_jawab'] ?? '-'),
                            esc($pr['lokasi'] ?? '-'),
                            '<span class="badge badge-green">' . esc($pr['status'] ?? 'ACTIVE') . '</span>',
                            '<div style="display:flex; gap:4px;">' .
                            '<a href="' . site_url('admin/cms/edit/program/' . $pr['id']) . '" class="btn btn-secondary" style="padding: 2px 8px; font-size: 12px;">Edit</a>' .
                            '<a href="' . site_url('admin/cms/delete/program/' . $pr['id']) . '" class="btn btn-secondary" onclick="return confirm(\'Hapus program ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>' .
                            '</div>',
                        ]
                    ];
                }
            } elseif ($tab === 'layanan' && $db->tableExists('layanan_masjid')) {
                $headers = ['Nama Layanan', 'Icon', 'Jam Layanan', 'Kontak / Wa', 'Status', 'Aksi'];
                $data = $db->table('layanan_masjid')->orderBy('urutan', 'ASC')->get()->getResultArray();
                foreach ($data as $l) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($l['nama']) . '</strong>',
                            '<span style="font-size: 18px;">' . esc($l['icon'] ?? '🤝') . '</span>',
                            '<span class="stat-mono">' . esc($l['jam_layanan'] ?? '-') . '</span>',
                            esc($l['kontak'] ?? '-'),
                            '<span class="badge badge-green">' . esc($l['status'] ?? 'ACTIVE') . '</span>',
                            '<div style="display:flex; gap:4px;">' .
                            '<a href="' . site_url('admin/cms/edit/layanan/' . $l['id']) . '" class="btn btn-secondary" style="padding: 2px 8px; font-size: 12px;">Edit</a>' .
                            '<a href="' . site_url('admin/cms/delete/layanan/' . $l['id']) . '" class="btn btn-secondary" onclick="return confirm(\'Hapus layanan ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>' .
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
                            '<a href="' . site_url('admin/cms/edit/gallery/' . $g['id']) . '" class="btn btn-secondary" style="padding: 2px 8px; font-size: 12px;">Edit</a>' .
                            '<a href="' . site_url('admin/cms/delete/gallery/' . $g['id']) . '" class="btn btn-secondary" onclick="return confirm(\'Hapus foto ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>' .
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
            'agenda'  => 'Agenda & Jadwal Kegiatan Masjid',
            'program' => 'Program & Kegiatan Masjid',
            'layanan' => 'Katalog Layanan Masjid',
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

            } elseif ($tab === 'agenda') {
                $rules = [
                    'title'      => 'required',
                    'event_date' => 'required',
                ];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Agenda: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $title = (string) $this->request->getPost('title');
                $description = (string) $this->request->getPost('description');
                $eventDate = (string) $this->request->getPost('event_date');
                $eventTime = (string) ($this->request->getPost('event_time') ?: null);
                $location = (string) $this->request->getPost('location');

                $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));

                $db->table('agenda')->insert([
                    'uuid'        => $uuid,
                    'masjid_id'   => '1',
                    'title'       => $title,
                    'description' => $description,
                    'event_date'  => $eventDate,
                    'event_time'  => $eventTime,
                    'location'    => $location,
                    'status'      => 'UPCOMING',
                    'created_at'  => date('Y-m-d H:i:s'),
                ]);

                session()->setFlashdata('success', 'Agenda "' . esc($title) . '" berhasil ditambahkan.');

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

            } elseif ($tab === 'program') {
                $rules = ['nama' => 'required'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Program: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $nama = (string) $this->request->getPost('nama');
                $db->table('program_kegiatan')->insert([
                    'bidang_id'        => (int) ($this->request->getPost('bidang_id') ?: 1),
                    'nama'             => $nama,
                    'slug'             => url_title($nama, '-', true),
                    'ringkasan'        => (string) $this->request->getPost('ringkasan'),
                    'deskripsi'        => (string) $this->request->getPost('deskripsi'),
                    'penanggung_jawab' => (string) $this->request->getPost('penanggung_jawab'),
                    'lokasi'           => (string) $this->request->getPost('lokasi'),
                    'status'           => 'ACTIVE',
                    'featured'         => (int) ($this->request->getPost('featured') ?: 0),
                    'created_at'       => date('Y-m-d H:i:s'),
                ]);
                session()->setFlashdata('success', 'Program "' . esc($nama) . '" berhasil ditambahkan.');

            } elseif ($tab === 'layanan') {
                $rules = ['nama' => 'required'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Layanan: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $nama = (string) $this->request->getPost('nama');
                $db->table('layanan_masjid')->insert([
                    'nama'        => $nama,
                    'slug'        => url_title($nama, '-', true),
                    'icon'        => (string) ($this->request->getPost('icon') ?: '🤝'),
                    'deskripsi'   => (string) $this->request->getPost('deskripsi'),
                    'persyaratan' => (string) $this->request->getPost('persyaratan'),
                    'jam_layanan' => (string) $this->request->getPost('jam_layanan'),
                    'kontak'      => (string) $this->request->getPost('kontak'),
                    'lokasi'      => (string) $this->request->getPost('lokasi'),
                    'status'      => 'ACTIVE',
                    'urutan'      => (int) ($this->request->getPost('urutan') ?: 1),
                    'created_at'  => date('Y-m-d H:i:s'),
                ]);
                session()->setFlashdata('success', 'Layanan "' . esc($nama) . '" berhasil ditambahkan.');

            } elseif ($tab === 'gallery') {
                $rules = [
                    'caption'  => 'required',
                    'media_id' => 'required|numeric',
                ];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Galeri Foto: pilih gambar dari Media Library terlebih dahulu.');
                    return redirect()->back()->withInput();
                }

                $caption = (string) $this->request->getPost('caption');
                $mediaId = (int) $this->request->getPost('media_id');

                $mediaExists = $db->table('media')->where('id', $mediaId)->countAllResults() > 0;
                if (!$mediaExists) {
                    session()->setFlashdata('error', 'Media yang dipilih tidak ditemukan di Media Library.');
                    return redirect()->back()->withInput();
                }

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

    public function edit(string $type, string $id)
    {
        $db = Database::connect();
        $allowedTables = ['posts' => 'posts', 'kajian' => 'kajian', 'agenda' => 'agenda', 'pages' => 'pages', 'gallery' => 'gallery', 'program' => 'program_kegiatan', 'layanan' => 'layanan_masjid'];
        $tableName = $allowedTables[$type] ?? $type;
        $item = null;

        if ($db->tableExists($tableName)) {
            if ($type === 'gallery') {
                $builder = $db->table('gallery');
                if ($db->tableExists('media')) {
                    $builder->select('gallery.*, media.filepath')
                            ->join('media', 'media.id = gallery.media_id', 'left');
                }
                $item = $builder->where('gallery.id', $id)->get()->getRowArray();
            } else {
                $item = $db->table($tableName)->where('id', $id)->get()->getRowArray();
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

            } elseif ($tab === 'agenda') {
                $rules = ['title' => 'required', 'event_date' => 'required'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal memperbarui Agenda: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $db->table('agenda')->where('id', $id)->update([
                    'title'       => (string) $this->request->getPost('title'),
                    'description' => (string) $this->request->getPost('description'),
                    'event_date'  => (string) $this->request->getPost('event_date'),
                    'event_time'  => (string) ($this->request->getPost('event_time') ?: null),
                    'location'    => (string) $this->request->getPost('location'),
                ]);
                session()->setFlashdata('success', 'Agenda berhasil diperbarui.');

            } elseif ($tab === 'program') {
                $db->table('program_kegiatan')->where('id', $id)->update([
                    'nama'             => (string) $this->request->getPost('nama'),
                    'ringkasan'        => (string) $this->request->getPost('ringkasan'),
                    'deskripsi'        => (string) $this->request->getPost('deskripsi'),
                    'penanggung_jawab' => (string) $this->request->getPost('penanggung_jawab'),
                    'lokasi'           => (string) $this->request->getPost('lokasi'),
                ]);
                session()->setFlashdata('success', 'Program Kegiatan berhasil diperbarui.');

            } elseif ($tab === 'layanan') {
                $db->table('layanan_masjid')->where('id', $id)->update([
                    'nama'        => (string) $this->request->getPost('nama'),
                    'icon'        => (string) $this->request->getPost('icon'),
                    'deskripsi'   => (string) $this->request->getPost('deskripsi'),
                    'persyaratan' => (string) $this->request->getPost('persyaratan'),
                    'jam_layanan' => (string) $this->request->getPost('jam_layanan'),
                    'kontak'      => (string) $this->request->getPost('kontak'),
                ]);
                session()->setFlashdata('success', 'Layanan Masjid berhasil diperbarui.');

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
                $newMediaId = (int) $this->request->getPost('media_id');

                $gRow = $db->table('gallery')->where('id', $id)->get()->getRowArray();
                if ($gRow) {
                    $updateData = ['caption' => $caption];
                    // Only swap the underlying media if the user picked a
                    // different item from the library; never edit the media
                    // row's filepath directly (that row may be reused by
                    // Logo/Favicon/other galleries too).
                    if ($newMediaId > 0 && $db->table('media')->where('id', $newMediaId)->countAllResults() > 0) {
                        $updateData['media_id'] = $newMediaId;
                    }
                    $db->table('gallery')->where('id', $id)->update($updateData);
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
            $tableMap = ['posts' => 'posts', 'kajian' => 'kajian', 'agenda' => 'agenda', 'pages' => 'pages', 'gallery' => 'gallery', 'program' => 'program_kegiatan', 'layanan' => 'layanan_masjid'];
            $tableName = $tableMap[$type] ?? $type;
            if ($db->tableExists($tableName)) {
                $db->table($tableName)->where('id', $id)->delete();
                session()->setFlashdata('success', 'Data ' . esc($type) . ' berhasil dihapus.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminCmsWorkspaceController Delete Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menghapus data: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/cms?tab=' . $type));
    }
}
