<?php

namespace Tests\Unit;

use App\Controllers\AdminFinancialWorkspaceController;
use PHPUnit\Framework\TestCase;

class FinancialWorkspaceUiTest extends TestCase
{
    public function testAdminFinancialWorkspaceControllerInstance(): void
    {
        $controller = new AdminFinancialWorkspaceController();
        $this->assertInstanceOf(AdminFinancialWorkspaceController::class, $controller);
    }

    public function testFinancialWorkspaceViewFilesExist(): void
    {
        $this->assertFileExists(APPPATH . 'Views/admin/financial/index.php');
        $this->assertFileExists(APPPATH . 'Views/admin/financial/create.php');
        $this->assertFileExists(APPPATH . 'Views/admin/financial/detail.php');
    }

    public function testFinancialWorkspaceUsesReusableComponents(): void
    {
        $viewContent = file_get_contents(APPPATH . 'Views/admin/financial/index.php');
        $this->assertStringContainsString("view('components/toolbar'", $viewContent);
        $this->assertStringContainsString("view('components/search_filter'", $viewContent);
        $this->assertStringContainsString("view('components/table'", $viewContent);
        $this->assertStringContainsString("view('components/pagination'", $viewContent);
    }

    public function testFinancialJournalEntriesBalanceAndUseDifferentAccountsByMapping(): void
    {
        $controller = new AdminFinancialWorkspaceController();
        $reflection = new \ReflectionClass($controller);

        $resolveMethod = $reflection->getMethod('resolveDoubleEntryAccounts');
        $resolveMethod->setAccessible(true);
        $buildMethod = $reflection->getMethod('buildJournalDetailRows');
        $buildMethod->setAccessible(true);

        $incomeAccounts = $resolveMethod->invoke($controller, 'INCOME', 101, 201);
        $this->assertSame([101, 201], $incomeAccounts);

        $incomeRows = $buildMethod->invoke($controller, 'INCOME', 150000.0, 101, 201);
        $this->assertCount(2, $incomeRows);
        $this->assertSame(150000.0, array_sum(array_column($incomeRows, 'debit_amount')));
        $this->assertSame(150000.0, array_sum(array_column($incomeRows, 'credit_amount')));
        $this->assertNotSame($incomeRows[0]['account_id'], $incomeRows[1]['account_id']);

        $expenseAccounts = $resolveMethod->invoke($controller, 'EXPENSE', 101, 201);
        $this->assertSame([201, 101], $expenseAccounts);

        $expenseRows = $buildMethod->invoke($controller, 'EXPENSE', 75000.0, 101, 201);
        $this->assertCount(2, $expenseRows);
        $this->assertSame(75000.0, array_sum(array_column($expenseRows, 'debit_amount')));
        $this->assertSame(75000.0, array_sum(array_column($expenseRows, 'credit_amount')));
        $this->assertNotSame($expenseRows[0]['account_id'], $expenseRows[1]['account_id']);
    }
}
