<?php

declare(strict_types=1);

namespace App\Domains\Financial\Entities;

class ApprovalLog
{
    private ?int $id;
    private int $transactionId;
    private string $approverUserId;
    private string $action; // 'APPROVED', 'REJECTED'
    private ?string $notes;
    private string $createdAt;

    public function __construct(
        ?int $id,
        int $transactionId,
        string $approverUserId,
        string $action,
        ?string $notes = null,
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->transactionId = $transactionId;
        $this->approverUserId = $approverUserId;
        $this->action = strtoupper($action);
        $this->notes = $notes;
        $this->createdAt = $createdAt ?? date('Y-m-d H:i:s');
    }

    public function getId(): ?int { return $this->id; }
    public function getTransactionId(): int { return $this->transactionId; }
    public function getApproverUserId(): string { return $this->approverUserId; }
    public function getAction(): string { return $this->action; }
    public function getNotes(): ?string { return $this->notes; }
    public function getCreatedAt(): string { return $this->createdAt; }
}
