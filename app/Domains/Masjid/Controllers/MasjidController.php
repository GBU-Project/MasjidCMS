<?php

namespace App\Domains\Masjid\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ValidationException;
use App\Domains\Masjid\DTO\CreateMasjidDTO;
use App\Domains\Masjid\DTO\UpdateMasjidDTO;
use App\Domains\Masjid\Services\MasjidService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class MasjidController
 *
 * REST Controller penangan endpoint Domain Masjid.
 */
class MasjidController extends BaseController
{
    protected MasjidService $service;

    public function __construct(?MasjidService $service = null)
    {
        $this->service = $service ?? new MasjidService();
    }

    /**
     * GET /masjid
     */
    public function index(): ResponseInterface
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 15);

        $result = $this->service->paginate($page, $perPage);
        return $this->respondSuccess($result, 'List of Masjids retrieved successfully');
    }

    /**
     * GET /masjid/{id}
     */
    public function show(int|string|null $id = null): ResponseInterface
    {
        if (empty($id)) {
            return $this->respondError('Missing Masjid ID', null, 400);
        }

        $masjid = $this->service->find($id);
        if (!$masjid) {
            return $this->respondError(sprintf('Masjid with ID [%s] not found.', (string) $id), null, 404);
        }

        return $this->respondSuccess($masjid, 'Masjid detail retrieved successfully');
    }

    /**
     * POST /masjid
     */
    public function create(): ResponseInterface
    {
        $payload = $this->request->getJSON(true) ?? $this->request->getPost();

        try {
            $dto = CreateMasjidDTO::fromArray($payload ?? []);
            $result = $this->service->create($dto->toArray());

            return $this->respondSuccess($result, 'Masjid created successfully', 201);
        } catch (ValidationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), 422);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }

    /**
     * PUT /masjid/{id}
     */
    public function update(int|string|null $id = null): ResponseInterface
    {
        if (empty($id)) {
            return $this->respondError('Missing Masjid ID', null, 400);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getRawInput();

        try {
            $dto = UpdateMasjidDTO::fromArray($payload ?? []);
            $result = $this->service->update($id, $dto->toArray());

            return $this->respondSuccess($result, 'Masjid updated successfully');
        } catch (NotFoundException $e) {
            return $this->respondError($e->getMessage(), null, 404);
        } catch (ValidationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), 422);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }

    /**
     * DELETE /masjid/{id}
     */
    public function delete(int|string|null $id = null): ResponseInterface
    {
        if (empty($id)) {
            return $this->respondError('Missing Masjid ID', null, 400);
        }

        try {
            $this->service->delete($id);
            return $this->respondSuccess(null, 'Masjid deleted successfully');
        } catch (NotFoundException $e) {
            return $this->respondError($e->getMessage(), null, 404);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }
}
