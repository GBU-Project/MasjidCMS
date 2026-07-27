<?php

declare(strict_types=1);

namespace App\Domains\Financial\Services\Approval;

use App\Domains\Financial\Exceptions\BusinessRuleException;

class ApprovalPolicy
{
    private const AUTHORIZED_APPROVER_ROLES = [
        'Treasurer',
        'Finance Manager',
        'Chairman',
        'Super Admin',
    ];

    public function isAuthorizedToApprove(string $role): bool
    {
        return in_array($role, self::AUTHORIZED_APPROVER_ROLES, true);
    }

    public function assertCanApprove(string $userId, string $role): void
    {
        if (!$this->isAuthorizedToApprove($role)) {
            throw new BusinessRuleException(
                "Permission Denied: User [{$userId}] dengan role [{$role}] tidak memiliki otorisasi untuk menyetujui transaksi keuangan."
            );
        }
    }

    public function assertCanReject(string $userId, string $role): void
    {
        if (!$this->isAuthorizedToApprove($role)) {
            throw new BusinessRuleException(
                "Permission Denied: User [{$userId}] dengan role [{$role}] tidak memiliki otorisasi untuk menolak transaksi keuangan."
            );
        }
    }
}
