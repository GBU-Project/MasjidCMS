<?php

namespace App\Domains\Jamaah\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ValidationException;
use App\Domains\Jamaah\DTO\CreateJamaahDTO;
use App\Domains\Jamaah\DTO\UpdateJamaahDTO;
use App\Domains\Jamaah\Services\JamaahService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class JamaahController
 *
 * REST Controller penangan endpoint Domain Jamaah.
 */
class JamaahController extends BaseController
{
    protected JamaahService $service;

    public function __construct(?JamaahService $service = null)
    {
        $this->service = $service ?? new JamaahService();
    }

    /**
     * GET /jamaah
     * Supports search, filters (status, gender, city, district), sorting, and pagination.
     */
    public function index(): ResponseInterface
    {
        $search  = (string) ($this->request->getGet('search') ?? '');
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 15);

        $filters = [
            'status'   => $this->request->getGet('status'),
            'gender'   => $this->request->getGet('gender'),
            'city'     => $this->request->getGet('city'),
            'district' => $this->request->getGet('district'),
        ];

        $sort = [
            'by'    => $this->request->getGet('sort_by') ?? 'created_at',
            'order' => $this->request->getGet('sort_order') ?? 'DESC',
        ];

        $result = $this->service->searchAndPaginate($search, $filters, $sort, $page, $perPage);
        return $this->respondSuccess($result, 'List of Jamaah retrieved successfully');
    }

    /**
     * GET /jamaah/{id}
     */
    public function show(int|string|null $id = null): ResponseInterface
    {
        if (empty($id)) {
            return $this->respondError('Missing Jamaah ID', null, 400);
        }

        $record = $this->service->find($id);
        if (!$record) {
            return $this->respondError(sprintf('Jamaah with ID [%s] not found.', (string) $id), null, 404);
        }

        return $this->respondSuccess($record, 'Jamaah detail retrieved successfully');
    }

    /**
     * POST /jamaah
     */
    public function create(): ResponseInterface
    {
        $payload = $this->request->getJSON(true) ?? $this->request->getPost();

        try {
            $dto = CreateJamaahDTO::fromArray($payload ?? []);
            $result = $this->service->create($dto->toArray());

            return $this->respondSuccess($result, 'Jamaah created successfully', 201);
        } catch (ValidationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), 422);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }

    /**
     * PUT /jamaah/{id}
     */
    public function update(int|string|null $id = null): ResponseInterface
    {
        if (empty($id)) {
            return $this->respondError('Missing Jamaah ID', null, 400);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getRawInput();

        try {
            $dto = UpdateJamaahDTO::fromArray($payload ?? []);
            $result = $this->service->update($id, $dto->toArray());

            return $this->respondSuccess($result, 'Jamaah updated successfully');
        } catch (NotFoundException $e) {
            return $this->respondError($e->getMessage(), null, 404);
        } catch (ValidationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), 422);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }

    /**
     * DELETE /jamaah/{id}
     */
    public function delete(int|string|null $id = null): ResponseInterface
    {
        if (empty($id)) {
            return $this->respondError('Missing Jamaah ID', null, 400);
        }

        try {
            $this->service->delete($id);
            return $this->respondSuccess(null, 'Jamaah deleted successfully');
        } catch (NotFoundException $e) {
            return $this->respondError($e->getMessage(), null, 404);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }
}
