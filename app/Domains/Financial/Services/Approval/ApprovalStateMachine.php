<?php

declare(strict_types=1);

namespace App\Domains\Financial\Services\Approval;

use App\Domains\Financial\Exceptions\BusinessRuleException;

class ApprovalStateMachine
{
    private const ALLOWED_TRANSITIONS = [
        'DRAFT' => ['PENDING_APPROVAL', 'CANCELLED', 'POSTED'],
        'PENDING_APPROVAL' => ['APPROVED', 'REJECTED', 'CANCELLED'],
        'REJECTED' => ['PENDING_APPROVAL', 'CANCELLED'],
        'APPROVED' => ['POSTED', 'CANCELLED'],
        'POSTED' => ['VOID'],
        'CANCELLED' => [],
        'VOID' => [],
    ];

    public function canTransition(string $currentStatus, string $targetStatus): bool
    {
        $current = strtoupper($currentStatus);
        $target = strtoupper($targetStatus);

        if (!isset(self::ALLOWED_TRANSITIONS[$current])) {
            return false;
        }

        return in_array($target, self::ALLOWED_TRANSITIONS[$current], true);
    }

    public function assertTransitionAllowed(string $currentStatus, string $targetStatus): void
    {
        if (!$this->canTransition($currentStatus, $targetStatus)) {
            throw new BusinessRuleException(
                "Invalid State Transition: Transaksi tidak dapat berpindah status dari [{$currentStatus}] ke [{$targetStatus}]."
            );
        }
    }
}
