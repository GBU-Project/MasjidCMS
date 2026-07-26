<?php

namespace App\Domains\Masjid\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Exceptions\DomainException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ValidationException;
use App\Domains\Masjid\DTO\CreateMasjidRequest;
use App\Domains\Masjid\DTO\UpdateMasjidRequest;
use App\Domains\Masjid\Services\MasjidService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class MasjidController
 *
 * Orchestration HTTP Controller untuk Domain Masjid.
 * DILARANG memanggil Repository atau mengeksekusi logika bisnis secara langsung.
 */
class MasjidController extends BaseController
{
    protected MasjidService $masjidService;

    public function __construct(?MasjidService $masjidService = null)
    {
        $this->masjidService = $masjidService ?? new MasjidService();
    }

    /**
     * Endpoint membaca daftar profil masjid (Paginated).
     */
    public function index(): ResponseInterface
    {
        try {
            $page = (int) ($this->request->getGet('page') ?? 1);
            $perPage = (int) ($this->request->getGet('per_page') ?? 15);

            $result = $this->masjidService->paginate($page, $perPage);
            return $this->respondSuccess($result, 'Masjid list fetched successfully');
        } catch (\Throwable $e) {
            return $this->respondError('Failed to fetch masjid list.', null, 500);
        }
    }

    /**
     * Endpoint detail profil masjid.
     */
    public function show(int|string $id): ResponseInterface
    {
        try {
            $masjid = $this->masjidService->find($id);

            if (!$masjid) {
                return $this->respondError(sprintf('Masjid with ID [%s] not found.', (string) $id), null, 404);
            }

            return $this->respondSuccess($masjid, 'Masjid detail fetched successfully');
        } catch (\Throwable $e) {
            return $this->respondError('Failed to fetch masjid detail.', null, 500);
        }
    }

    /**
     * Endpoint pembuatan profil masjid baru.
     */
    public function create(): ResponseInterface
    {
        try {
            $data = $this->request->getJSON(true) ?? $this->request->getPost();
            $dto = CreateMasjidRequest::fromArray($data);

            $created = $this->masjidService->create($dto);
            return $this->respondSuccess($created, 'Masjid profile created successfully', 201);
        } catch (ValidationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), $e->getCode());
        } catch (DomainException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), $e->getCode());
        } catch (\Throwable $e) {
            return $this->respondError('Failed to create masjid profile.', null, 500);
        }
    }

    /**
     * Endpoint pembaruan profil masjid.
     */
    public function update(int|string $id): ResponseInterface
    {
        try {
            $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
            $dto = UpdateMasjidRequest::fromArray($data);

            $updated = $this->masjidService->update($id, $dto);
            return $this->respondSuccess($updated, 'Masjid profile updated successfully');
        } catch (NotFoundException $e) {
            return $this->respondError($e->getMessage(), null, 404);
        } catch (ValidationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), $e->getCode());
        } catch (\Throwable $e) {
            return $this->respondError('Failed to update masjid profile.', null, 500);
        }
    }

    /**
     * Endpoint penghapusan profil masjid.
     */
    public function delete(int|string $id): ResponseInterface
    {
        try {
            $this->masjidService->delete($id);
            return $this->respondSuccess(null, 'Masjid profile deleted successfully');
        } catch (NotFoundException $e) {
            return $this->respondError($e->getMessage(), null, 404);
        } catch (\Throwable $e) {
            return $this->respondError('Failed to delete masjid profile.', null, 500);
        }
    }
}
