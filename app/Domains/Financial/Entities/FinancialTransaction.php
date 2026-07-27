<?php

declare(strict_types=1);

namespace App\Domains\Financial\Entities;

use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Events\ApprovalGrantedEvent;
use App\Domains\Financial\Events\ApprovalRejectedEvent;
use App\Domains\Financial\Events\FinancialTransactionApprovedEvent;
use App\Domains\Financial\Events\FinancialTransactionPostedEvent;
use App\Domains\Financial\Events\FinancialTransactionRejectedEvent;
use App\Domains\Financial\Events\FinancialTransactionSubmittedEvent;
use App\Domains\Financial\Events\FinancialTransactionVoidedEvent;
use App\Domains\Financial\Events\HasDomainEventsTrait;
use App\Domains\Financial\Exceptions\BusinessRuleException;

class FinancialTransaction
{
    use HasDomainEventsTrait;

    private ?int $id;
    private string $uuid;
    private string $masjidId;
    private int $fundId;
    private int $accountId;
    private int $financialAccountId;
    private ?int $programId;
    private ?string $jamaahId;
    private ?string $familyId;
    private ?string $vendorId;
    private ?string $assetId;
    private TransactionNumber $transactionNo;
    private string $transactionType; // 'INCOME', 'EXPENSE', 'TRANSFER', 'ADJUSTMENT'
    private Money $amount;
    private string $paymentMethod;   // 'CASH', 'BANK_TRANSFER', 'QRIS'
    private string $status;          // 'DRAFT', 'PENDING_APPROVAL', 'APPROVED', 'POSTED', 'REJECTED', 'CANCELLED', 'VOID'
    private string $transactionDate;
    private ?string $description;
    private ?string $createdBy;
    private ?string $updatedBy;
    private ?string $approvedBy;
    private ?string $postedAt;

    public function __construct(
        ?int $id,
        string $uuid,
        string $masjidId,
        int $fundId,
        int $accountId,
        int $financialAccountId,
        TransactionNumber $transactionNo,
        string $transactionType,
        Money $amount,
        string $transactionDate,
        ?int $programId = null,
        ?string $jamaahId = null,
        ?string $familyId = null,
        ?string $vendorId = null,
        ?string $assetId = null,
        string $paymentMethod = 'CASH',
        string $status = 'DRAFT',
        ?string $description = null,
        ?string $createdBy = null,
        ?string $updatedBy = null,
        ?string $approvedBy = null,
        ?string $postedAt = null
    ) {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->masjidId = $masjidId;
        $this->fundId = $fundId;
        $this->accountId = $accountId;
        $this->financialAccountId = $financialAccountId;
        $this->transactionNo = $transactionNo;
        $this->transactionType = strtoupper($transactionType);
        $this->amount = $amount;
        $this->transactionDate = $transactionDate;
        $this->programId = $programId;
        $this->jamaahId = $jamaahId;
        $this->familyId = $familyId;
        $this->vendorId = $vendorId;
        $this->assetId = $assetId;
        $this->paymentMethod = strtoupper($paymentMethod);
        $this->status = strtoupper($status);
        $this->description = $description;
        $this->createdBy = $createdBy;
        $this->updatedBy = $updatedBy;
        $this->approvedBy = $approvedBy;
        $this->postedAt = $postedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getUuid(): string { return $this->uuid; }
    public function getMasjidId(): string { return $this->masjidId; }
    public function getFundId(): int { return $this->fundId; }
    public function getAccountId(): int { return $this->accountId; }
    public function getFinancialAccountId(): int { return $this->financialAccountId; }
    public function getProgramId(): ?int { return $this->programId; }
    public function getJamaahId(): ?string { return $this->jamaahId; }
    public function getFamilyId(): ?string { return $this->familyId; }
    public function getVendorId(): ?string { return $this->vendorId; }
    public function getAssetId(): ?string { return $this->assetId; }
    public function getTransactionNo(): TransactionNumber { return $this->transactionNo; }
    public function getTransactionType(): string { return $this->transactionType; }
    public function getAmount(): Money { return $this->amount; }
    public function getPaymentMethod(): string { return $this->paymentMethod; }
    public function getStatus(): string { return $this->status; }
    public function getTransactionDate(): string { return $this->transactionDate; }
    public function getDescription(): ?string { return $this->description; }
    public function getCreatedBy(): ?string { return $this->createdBy; }
    public function getApprovedBy(): ?string { return $this->approvedBy; }
    public function getPostedAt(): ?string { return $this->postedAt; }

    public function isImmutable(): bool
    {
        return in_array($this->status, ['POSTED', 'VOID', 'CANCELLED'], true);
    }

    public function submitForApproval(): void
    {
        $this->assertMutable();
        $this->status = 'PENDING_APPROVAL';
        $this->recordEvent(new FinancialTransactionSubmittedEvent('evt-' . bin2hex(random_bytes(4)), $this->uuid, [
            'transaction_no' => $this->transactionNo->getValue(),
            'amount'         => $this->amount->getAmount(),
        ]));
    }

    public function approve(string $approverUserId): void
    {
        if ($this->status !== 'PENDING_APPROVAL') {
            throw new BusinessRuleException("Hanya transaksi PENDING_APPROVAL yang dapat disetujui.");
        }
        $this->status = 'APPROVED';
        $this->approvedBy = $approverUserId;

        $evtId = 'evt-' . bin2hex(random_bytes(4));
        $this->recordEvent(new FinancialTransactionApprovedEvent($evtId, $this->uuid, ['approver_user_id' => $approverUserId]));
        $this->recordEvent(new ApprovalGrantedEvent($evtId, $this->uuid, ['approver_user_id' => $approverUserId]));
    }

    public function reject(string $approverUserId, ?string $notes = null): void
    {
        if ($this->status !== 'PENDING_APPROVAL') {
            throw new BusinessRuleException("Hanya transaksi PENDING_APPROVAL yang dapat ditolak.");
        }
        $this->status = 'REJECTED';
        $this->approvedBy = $approverUserId;

        $evtId = 'evt-' . bin2hex(random_bytes(4));
        $this->recordEvent(new FinancialTransactionRejectedEvent($evtId, $this->uuid, ['approver_user_id' => $approverUserId, 'notes' => $notes]));
        $this->recordEvent(new ApprovalRejectedEvent($evtId, $this->uuid, ['approver_user_id' => $approverUserId, 'notes' => $notes]));
    }

    public function post(string $postedAtTimestamp): void
    {
        if (!in_array($this->status, ['DRAFT', 'APPROVED'], true)) {
            throw new BusinessRuleException("Transaksi berstatus [{$this->status}] tidak dapat di-post.");
        }
        $this->status = 'POSTED';
        $this->postedAt = $postedAtTimestamp;

        $this->recordEvent(new FinancialTransactionPostedEvent('evt-' . bin2hex(random_bytes(4)), $this->uuid, [
            'posted_at' => $postedAtTimestamp,
            'amount'    => $this->amount->getAmount(),
        ]));
    }

    public function cancel(): void
    {
        $this->assertMutable();
        $this->status = 'CANCELLED';
    }

    public function voidTransaction(): void
    {
        if ($this->status !== 'POSTED') {
            throw new BusinessRuleException("Hanya transaksi POSTED yang dapat di-VOID via reversal.");
        }
        $this->status = 'VOID';

        $this->recordEvent(new FinancialTransactionVoidedEvent('evt-' . bin2hex(random_bytes(4)), $this->uuid, [
            'transaction_no' => $this->transactionNo->getValue(),
        ]));
    }

    private function assertMutable(): void
    {
        if ($this->isImmutable()) {
            throw new BusinessRuleException("Transaksi berstatus [{$this->status}] bersifat HAKIKI IMMUTABLE dan dilarang diubah.");
        }
    }

    public function equals(FinancialTransaction $other): bool
    {
        return $this->uuid === $other->getUuid();
    }
}
