<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use App\Models\HeroSlideModel;
use Config\Database;

class AdminHeroSlideController extends BaseController
{
    protected HeroSlideModel $heroSlideModel;

    public function __construct()
    {
        $this->heroSlideModel = new HeroSlideModel();
    }

    public function index(): string
    {
        $db = Database::connect();
        $slides = [];

        if ($db->tableExists('hero_slides')) {
            $builder = $db->table('hero_slides')
                ->where('deleted_at', null)
                ->orderBy('sort_order', 'ASC')
                ->orderBy('id', 'ASC');

            $slides = $builder->get()->getResultArray();

            if (!empty($slides) && $db->tableExists('media')) {
                $mediaIds = array_filter(array_column($slides, 'bg_image_media_id'));
                if (!empty($mediaIds)) {
                    $mediaRows = $db->table('media')->whereIn('id', $mediaIds)->get()->getResultArray();
                    $mediaMap = [];
                    foreach ($mediaRows as $m) {
                        $mediaMap[$m['id']] = $m['filepath'];
                    }
                    foreach ($slides as &$s) {
                        if (!empty($s['bg_image_media_id']) && isset($mediaMap[$s['bg_image_media_id']])) {
                            $s['bg_image_path'] = $mediaMap[$s['bg_image_media_id']];
                        }
                    }
                }
            }
        }

        return view('admin/hero/index', [
            'activePage' => 'homepage-manager',
            'slides'     => $slides,
        ]);
    }

    public function create(): string
    {
        return view('admin/hero/create', [
            'activePage' => 'homepage-manager',
        ]);
    }

    public function store()
    {
        $db = Database::connect();
        if (!$db->tableExists('hero_slides')) {
            return redirect()->back()->with('error', 'Tabel hero_slides belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $rules = [
            'title' => 'permit_empty|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul slide, jika diisi, minimal 3 karakter (maks. 255).');
        }

        $maxOrder = $db->table('hero_slides')->selectMax('sort_order', 'max_order')->get()->getRowArray();
        $nextOrder = ((int) ($maxOrder['max_order'] ?? 0)) + 1;

        $bgMediaId = (int) $this->request->getPost('bg_image_media_id');

        $publishAt = $this->request->getPost('publish_at');
        $expireAt  = $this->request->getPost('expire_at');

        $slideData = [
            'uuid'                => HeroSlideModel::generateUuid(),
            'title'               => (string) $this->request->getPost('title'),
            'subtitle'            => (string) $this->request->getPost('subtitle'),
            'bg_image_media_id'   => $bgMediaId > 0 ? $bgMediaId : null,
            'primary_btn_text'    => (string) $this->request->getPost('primary_btn_text'),
            'primary_btn_url'     => (string) $this->request->getPost('primary_btn_url'),
            'primary_btn_new_tab' => $this->request->getPost('primary_btn_new_tab') ? 1 : 0,
            'secondary_btn_text'  => (string) $this->request->getPost('secondary_btn_text'),
            'secondary_btn_url'   => (string) $this->request->getPost('secondary_btn_url'),
            'secondary_btn_new_tab' => $this->request->getPost('secondary_btn_new_tab') ? 1 : 0,
            'overlay_opacity'     => max(0, min(100, (int) $this->request->getPost('overlay_opacity'))),
            'text_alignment'      => in_array($this->request->getPost('text_alignment'), ['left', 'center', 'right'], true) ? $this->request->getPost('text_alignment') : 'left',
            'status'              => in_array($this->request->getPost('status'), ['ACTIVE', 'DRAFT', 'INACTIVE'], true) ? $this->request->getPost('status') : 'ACTIVE',
            'sort_order'          => $nextOrder,
            'publish_at'          => !empty($publishAt) ? date('Y-m-d H:i:s', strtotime($publishAt)) : null,
            'expire_at'           => !empty($expireAt) ? date('Y-m-d H:i:s', strtotime($expireAt)) : null,
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ];

        $this->heroSlideModel->insert($slideData);

        return redirect()->to('admin/hero-slides')->with('success', 'Hero Slide berhasil ditambahkan.');
    }

    public function edit($id): string
    {
        $slide = $this->heroSlideModel->find($id);
        if (!$slide) {
            return redirect()->to('admin/hero-slides')->with('error', 'Hero Slide tidak ditemukan.');
        }

        $db = Database::connect();
        if (!empty($slide['bg_image_media_id']) && $db->tableExists('media')) {
            $media = $db->table('media')->where('id', $slide['bg_image_media_id'])->get()->getRowArray();
            if ($media) {
                $slide['bg_image_path'] = $media['filepath'];
            }
        }

        return view('admin/hero/edit', [
            'activePage' => 'homepage-manager',
            'slide'      => $slide,
        ]);
    }

    public function update($id)
    {
        $slide = $this->heroSlideModel->find($id);
        if (!$slide) {
            return redirect()->to('admin/hero-slides')->with('error', 'Hero Slide tidak ditemukan.');
        }

        $rules = [
            'title' => 'permit_empty|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul slide, jika diisi, minimal 3 karakter (maks. 255).');
        }

        $bgMediaId = (int) $this->request->getPost('bg_image_media_id');
        $publishAt = $this->request->getPost('publish_at');
        $expireAt  = $this->request->getPost('expire_at');

        $updateData = [
            'title'               => (string) $this->request->getPost('title'),
            'subtitle'            => (string) $this->request->getPost('subtitle'),
            'bg_image_media_id'   => $bgMediaId > 0 ? $bgMediaId : null,
            'primary_btn_text'    => (string) $this->request->getPost('primary_btn_text'),
            'primary_btn_url'     => (string) $this->request->getPost('primary_btn_url'),
            'primary_btn_new_tab' => $this->request->getPost('primary_btn_new_tab') ? 1 : 0,
            'secondary_btn_text'  => (string) $this->request->getPost('secondary_btn_text'),
            'secondary_btn_url'   => (string) $this->request->getPost('secondary_btn_url'),
            'secondary_btn_new_tab' => $this->request->getPost('secondary_btn_new_tab') ? 1 : 0,
            'overlay_opacity'     => max(0, min(100, (int) $this->request->getPost('overlay_opacity'))),
            'text_alignment'      => in_array($this->request->getPost('text_alignment'), ['left', 'center', 'right'], true) ? $this->request->getPost('text_alignment') : 'left',
            'status'              => in_array($this->request->getPost('status'), ['ACTIVE', 'DRAFT', 'INACTIVE'], true) ? $this->request->getPost('status') : 'ACTIVE',
            'publish_at'          => !empty($publishAt) ? date('Y-m-d H:i:s', strtotime($publishAt)) : null,
            'expire_at'           => !empty($expireAt) ? date('Y-m-d H:i:s', strtotime($expireAt)) : null,
            'updated_at'          => date('Y-m-d H:i:s'),
        ];

        $this->heroSlideModel->update($id, $updateData);

        return redirect()->to('admin/hero-slides')->with('success', 'Hero Slide berhasil diperbarui.');
    }

    public function delete($id)
    {
        $slide = $this->heroSlideModel->find($id);
        if (!$slide) {
            return redirect()->to('admin/hero-slides')->with('error', 'Hero Slide tidak ditemukan.');
        }

        // Soft delete slide
        $this->heroSlideModel->delete($id);

        return redirect()->to('admin/hero-slides')->with('success', 'Hero Slide berhasil dihapus.');
    }

    public function toggle($id)
    {
        $slide = $this->heroSlideModel->find($id);
        if (!$slide) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Slide tidak ditemukan']);
        }

        $newStatus = ($slide['status'] === 'ACTIVE') ? 'INACTIVE' : 'ACTIVE';
        $this->heroSlideModel->update($id, ['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON([
            'status'     => 'success',
            'new_status' => $newStatus,
            'message'    => 'Status slide berhasil diubah menjadi ' . $newStatus,
        ]);
    }

    public function saveOrder()
    {
        $orders = $this->request->getPost('order');
        if (empty($orders) || !is_array($orders)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data urutan tidak valid']);
        }

        foreach ($orders as $position => $slideId) {
            $this->heroSlideModel->update((int) $slideId, [
                'sort_order' => $position + 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Urutan slide berhasil disimpan']);
    }
}
