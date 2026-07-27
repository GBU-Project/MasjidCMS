<?php

declare(strict_types=1);

namespace App\Application\Financial\Reporting\DTO;

class IncomeExpenseResponse
{
    public string $masjidId;
    public string $period;
    public float $totalIncome;
    public float $totalExpense;
    public float $netSurplusDeficit;
    public array $incomeBreakdown = [];
    public array $expenseBreakdown = [];

    public function __construct(
        string $masjidId,
        string $period,
        float $totalIncome,
        float $totalExpense,
        array $incomeBreakdown = [],
        array $expenseBreakdown = []
    ) {
        $this->masjidId = $masjidId;
        $this->period = $period;
        $this->totalIncome = round($totalIncome, 2);
        $this->totalExpense = round($totalExpense, 2);
        $this->netSurplusDeficit = round($totalIncome - $totalExpense, 2);
        $this->incomeBreakdown = $incomeBreakdown;
        $this->expenseBreakdown = $expenseBreakdown;
    }
}
