<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class AdminMediaController extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function index(): string
    {
        $db = Database::connect();
        $search = (string) ($this->request->getGet('q') ?? '');
        $filterType = (string) ($this->request->getGet('type') ?? 'all');

        $mediaList = [];
        if ($db->tableExists('media')) {
            $builder = $db->table('media');

            if (!empty($search)) {
                $builder->groupStart()
                    ->like('filename', $search)
                    ->orLike('filepath', $search)
                    ->groupEnd();
            }

            if ($filterType === 'image') {
                $builder->like('mime_type', 'image/');
            } elseif ($filterType === 'document') {
                $builder->notLike('mime_type', 'image/');
            }

            $mediaList = $builder->orderBy('id', 'DESC')->get()->getResultArray();
        }

        return view('admin/media/index', [
            'activePage' => 'media',
            'mediaList'  => $mediaList,
            'search'     => $search,
            'filterType' => $filterType,
        ]);
    }

    public function apiList()
    {
        $db = Database::connect();
        $search = (string) ($this->request->getGet('q') ?? '');
        $type = (string) ($this->request->getGet('type') ?? 'image');

        $items = [];
        if ($db->tableExists('media')) {
            $builder = $db->table('media');

            if (!empty($search)) {
                $builder->like('filename', $search);
            }

            if ($type === 'image') {
                $builder->like('mime_type', 'image/');
            }

            $rows = $builder->orderBy('id', 'DESC')->limit(100)->get()->getResultArray();
            foreach ($rows as $r) {
                $url = str_starts_with($r['filepath'], 'http') ? $r['filepath'] : base_url($r['filepath']);
                $items[] = [
                    'id'        => (int) $r['id'],
                    'filename'  => $r['filename'],
                    'filepath'  => $r['filepath'],
                    'url'       => $url,
                    'mime_type' => $r['mime_type'],
                    'filesize'  => (int) $r['filesize'],
                    'formatted_size' => $this->formatBytes((int) $r['filesize']),
                    'created_at' => $r['created_at'] ?? date('Y-m-d H:i:s'),
                ];
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $items,
        ]);
    }

    public function upload()
    {
        $db = Database::connect();
        $uploadDir = FCPATH . 'uploads/media/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $files = $this->request->getFiles();
        $uploadedMedia = [];
        $errors = [];

        $fileList = [];
        if (isset($files['files'])) {
            $fileList = is_array($files['files']) ? $files['files'] : [$files['files']];
        } elseif (isset($files['file'])) {
            $fileList = [$files['file']];
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'application/pdf'];
        $maxSize = 10 * 1024 * 1024; // 10MB

        foreach ($fileList as $file) {
            if (!$file->isValid() || $file->hasMoved()) {
                $errors[] = $file->getErrorString();
                continue;
            }

            if ($file->getSize() > $maxSize) {
                $errors[] = 'File ' . $file->getClientName() . ' melebihi batas 10MB.';
                continue;
            }

            $mime = $file->getMimeType();
            if (!in_array($mime, $allowedMimes, true)) {
                $errors[] = 'Tipe file ' . $file->getClientName() . ' (' . $mime . ') tidak diizinkan.';
                continue;
            }

            $originalName = $file->getClientName();
            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);

            $relativePath = 'uploads/media/' . $newName;

            $mediaData = [
                'filename'  => $originalName,
                'filepath'  => $relativePath,
                'mime_type' => $mime,
                'filesize'  => $file->getSize(),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if ($db->tableExists('media')) {
                $db->table('media')->insert($mediaData);
                $mediaId = $db->insertID();
                $mediaData['id'] = $mediaId;
                $mediaData['url'] = base_url($relativePath);
                $uploadedMedia[] = $mediaData;
            }
        }

        if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
            return $this->response->setJSON([
                'status'   => count($errors) > 0 && count($uploadedMedia) === 0 ? 'error' : 'success',
                'media'    => $uploadedMedia,
                'errors'   => $errors,
                'message'  => count($uploadedMedia) . ' file berhasil diunggah.',
                'csrf_token_value' => csrf_hash(),
            ]);
        }

        if (count($errors) > 0) {
            session()->setFlashdata('error', implode('<br>', $errors));
        } else {
            session()->setFlashdata('success', count($uploadedMedia) . ' file berhasil diunggah ke Media Library.');
        }

        return redirect()->to(site_url('admin/media'));
    }

    public function delete(string $id)
    {
        $db = Database::connect();
        try {
            if ($db->tableExists('media')) {
                $row = $db->table('media')->where('id', $id)->get()->getRowArray();
                if ($row) {
                    $fullPath = FCPATH . $row['filepath'];
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                    $db->table('media')->where('id', $id)->delete();
                    session()->setFlashdata('success', 'Media berhasil dihapus.');
                }
            }
        } catch (\Throwable $e) {
            session()->setFlashdata('error', 'Gagal menghapus media: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/media'));
    }

    public function bulkDelete()
    {
        $db = Database::connect();
        $ids = (array) $this->request->getPost('ids');

        if (count($ids) > 0 && $db->tableExists('media')) {
            $rows = $db->table('media')->whereIn('id', $ids)->get()->getResultArray();
            foreach ($rows as $row) {
                $fullPath = FCPATH . $row['filepath'];
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
            $db->table('media')->whereIn('id', $ids)->delete();
            session()->setFlashdata('success', count($rows) . ' item media berhasil dihapus secara massal.');
        }

        return redirect()->to(site_url('admin/media'));
    }

    public function rename()
    {
        $db = Database::connect();
        $id = (int) $this->request->getPost('id');
        $newFilename = trim((string) $this->request->getPost('filename'));

        if ($id <= 0 || $newFilename === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID atau nama file baru tidak valid.']);
        }

        if (!$db->tableExists('media')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tabel media tidak tersedia.']);
        }

        $row = $db->table('media')->where('id', $id)->get()->getRowArray();
        if (!$row) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Media tidak ditemukan.']);
        }

        // Only the display filename changes; the on-disk path/filepath stays
        // the same so nothing that already references filepath (Website
        // Identity, Gallery, homepage assets, etc.) breaks.
        $db->table('media')->where('id', $id)->update(['filename' => $newFilename]);

        if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Nama media berhasil diperbarui.']);
        }

        session()->setFlashdata('success', 'Nama media berhasil diperbarui.');
        return redirect()->to(site_url('admin/media'));
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
