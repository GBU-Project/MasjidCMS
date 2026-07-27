<?php

namespace App\Domains\Family\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ValidationException;
use App\Domains\Family\DTO\CreateFamilyDTO;
use App\Domains\Family\DTO\UpdateFamilyDTO;
use App\Domains\Family\Services\FamilyService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class FamilyController
 *
 * REST Controller penangan endpoint Domain Family (Keluarga).
 */
class FamilyController extends BaseController
{
    protected FamilyService $service;

    public function __construct(?FamilyService $service = null)
    {
        $this->service = $service ?? new FamilyService();
    }

    /**
     * GET /family
     */
    public function index(): ResponseInterface
    {
        $search  = (string) ($this->request->getGet('search') ?? '');
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 15);

        $filters = [
            'family_status' => $this->request->getGet('status') ?? $this->request->getGet('family_status'),
            'city'          => $this->request->getGet('city'),
            'district'      => $this->request->getGet('district'),
        ];

        $sort = [
            'by'    => $this->request->getGet('sort_by') ?? 'created_at',
            'order' => $this->request->getGet('sort_order') ?? 'DESC',
        ];

        $result = $this->service->searchAndPaginate($search, $filters, $sort, $page, $perPage);
        return $this->respondSuccess($result, 'List of Families retrieved successfully');
    }

    /**
     * GET /family/{id}
     */
    public function show(int|string|null $id = null): ResponseInterface
    {
        if (empty($id)) {
            return $this->respondError('Missing Family ID', null, 400);
        }

        $record = $this->service->find($id);
        if (!$record) {
            return $this->respondError(sprintf('Family with ID [%s] not found.', (string) $id), null, 404);
        }

        return $this->respondSuccess($record, 'Family detail retrieved successfully');
    }

    /**
     * POST /family
     */
    public function create(): ResponseInterface
    {
        $payload = $this->request->getJSON(true) ?? $this->request->getPost();

        try {
            $dto = CreateFamilyDTO::fromArray($payload ?? []);
            $result = $this->service->create($dto->toArray());

            return $this->respondSuccess($result, 'Family created successfully', 201);
        } catch (ValidationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), 422);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }

    /**
     * PUT /family/{id}
     */
    public function update(int|string|null $id = null): ResponseInterface
    {
        if (empty($id)) {
            return $this->respondError('Missing Family ID', null, 400);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getRawInput();

        try {
            $dto = UpdateFamilyDTO::fromArray($payload ?? []);
            $result = $this->service->update($id, $dto->toArray());

            return $this->respondSuccess($result, 'Family updated successfully');
        } catch (NotFoundException $e) {
            return $this->respondError($e->getMessage(), null, 404);
        } catch (ValidationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), 422);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }

    /**
     * DELETE /family/{id}
     */
    public function delete(int|string|null $id = null): ResponseInterface
    {
        if (empty($id)) {
            return $this->respondError('Missing Family ID', null, 400);
        }

        try {
            $this->service->delete($id);
            return $this->respondSuccess(null, 'Family deleted successfully');
        } catch (NotFoundException $e) {
            return $this->respondError($e->getMessage(), null, 404);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }

    /**
     * GET /family/{id}/members
     */
    public function members(int|string|null $id = null): ResponseInterface
    {
        if (empty($id)) {
            return $this->respondError('Missing Family ID', null, 400);
        }

        try {
            $members = $this->service->getFamilyMembers((string) $id);
            return $this->respondSuccess($members, 'Family members retrieved successfully');
        } catch (NotFoundException $e) {
            return $this->respondError($e->getMessage(), null, 404);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }

    /**
     * POST /family/{id}/transfer-head
     */
    public function transferHead(int|string|null $id = null): ResponseInterface
    {
        if (empty($id)) {
            return $this->respondError('Missing Family ID', null, 400);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $newHeadJamaahId = $payload['new_head_jamaah_id'] ?? $payload['head_jamaah_id'] ?? null;

        if (empty($newHeadJamaahId)) {
            return $this->respondError('Missing new_head_jamaah_id parameter', null, 400);
        }

        try {
            $result = $this->service->transferHead((string) $id, (string) $newHeadJamaahId);
            return $this->respondSuccess($result, 'Head of family transferred successfully');
        } catch (NotFoundException $e) {
            return $this->respondError($e->getMessage(), null, 404);
        } catch (ValidationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), 422);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }

    /**
     * POST /family/{id}/members
     */
    public function addMember(int|string|null $id = null): ResponseInterface
    {
        if (empty($id)) {
            return $this->respondError('Missing Family ID', null, 400);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $jamaahId = $payload['jamaah_id'] ?? null;
        $relationType = $payload['family_relation_type'] ?? $payload['relation_type'] ?? 'OTHER';

        if (empty($jamaahId)) {
            return $this->respondError('Missing jamaah_id parameter', null, 400);
        }

        try {
            $this->service->addMember((string) $id, (string) $jamaahId, (string) $relationType);
            return $this->respondSuccess(null, 'Member added to family successfully', 201);
        } catch (NotFoundException $e) {
            return $this->respondError($e->getMessage(), null, 404);
        } catch (ValidationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), 422);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }

    /**
     * DELETE /family/{id}/members/{jamaahId}
     */
    public function removeMember(int|string|null $id = null, int|string|null $jamaahId = null): ResponseInterface
    {
        if (empty($id) || empty($jamaahId)) {
            return $this->respondError('Missing Family ID or Jamaah ID', null, 400);
        }

        try {
            $this->service->removeMember((string) $id, (string) $jamaahId);
            return $this->respondSuccess(null, 'Member removed from family successfully');
        } catch (NotFoundException $e) {
            return $this->respondError($e->getMessage(), null, 404);
        } catch (ValidationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), 422);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }

    /**
     * PUT /family/{id}/members/{jamaahId}/relation
     */
    public function changeRelation(int|string|null $id = null, int|string|null $jamaahId = null): ResponseInterface
    {
        if (empty($id) || empty($jamaahId)) {
            return $this->respondError('Missing Family ID or Jamaah ID', null, 400);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $relationType = $payload['family_relation_type'] ?? $payload['relation_type'] ?? null;

        if (empty($relationType)) {
            return $this->respondError('Missing relation_type parameter', null, 400);
        }

        try {
            $this->service->changeRelation((string) $id, (string) $jamaahId, (string) $relationType);
            return $this->respondSuccess(null, 'Family relation updated successfully');
        } catch (NotFoundException $e) {
            return $this->respondError($e->getMessage(), null, 404);
        } catch (ValidationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), 422);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }

    /**
     * POST /family/{id}/move-member
     */
    public function moveMember(int|string|null $id = null): ResponseInterface
    {
        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $jamaahId = $payload['jamaah_id'] ?? null;
        $targetFamilyId = $payload['target_family_id'] ?? $id;
        $relationType = $payload['family_relation_type'] ?? $payload['relation_type'] ?? 'OTHER';

        if (empty($jamaahId) || empty($targetFamilyId)) {
            return $this->respondError('Missing jamaah_id or target_family_id', null, 400);
        }

        try {
            $this->service->moveMember((string) $jamaahId, (string) $targetFamilyId, (string) $relationType);
            return $this->respondSuccess(null, 'Member moved to target family successfully');
        } catch (NotFoundException $e) {
            return $this->respondError($e->getMessage(), null, 404);
        } catch (ValidationException $e) {
            return $this->respondError($e->getMessage(), $e->getErrors(), 422);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage(), null, 500);
        }
    }
}
