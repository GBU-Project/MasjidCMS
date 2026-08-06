# Regression Test Report

**Execution status: PASSED.** Executed locally on PHP 8.2.12 + PHPUnit 10.5.64.

```text
Tests: 168, Assertions: 623, Failures: 0, Errors: 0, Skipped: 1.
```

## Files changed/added

| File | Status | Purpose |
|---|---|---|
| `tests/unit/FinancialWorkspaceUiTest.php` | **Repaired** | Updated to match current controller namespace and structure; added static regression guard preventing raw `insertTransaction()` calls. |
| `tests/unit/FinancialGovernanceRcBlockerFixTest.php` | **New** | 7 tests directly covering the requested RC blocker fix scenarios. |
| `tests/integration/FinancialModuleIntegrationRc1Test.php` | **Updated** | Updated test fixtures to follow new state machine flow (DRAFT -> PENDING_APPROVAL -> APPROVED -> POSTED). |
| `tests/integration/FinancialPostingConcurrencyTest.php` | **Updated** | Updated test fixtures to follow new state machine flow. |
| `tests/unit/FinancialPostingEngineRc1Test.php` | **Updated** | Updated test fixtures to follow new state machine flow. |

## Coverage against the requested regression scenarios

| Required scenario | Test | What it checks | Status |
|---|---|---|---|
| UI creates DRAFT | `testCreateTransactionAlwaysStartsAsDraft` | `CreateTransactionApplicationService` (now called by `store()`/`import()`) returns `status === 'DRAFT'`, never `POSTED` | ✅ PASSED |
| Approval required / POSTED only after approval | `testPostFromDraftIsRejected` | `FinancialTransaction::post()` throws `BusinessRuleException` when called on a `DRAFT` entity | ✅ PASSED |
| POSTED only after approval (happy path) | `testPostFromApprovedSucceeds` | Confirms `post()` still works correctly when the entity is actually `APPROVED` | ✅ PASSED |
| Maker cannot approve own transaction | `testMakerCannotApproveOwnTransaction` | `approve()` throws when `approverUserId === createdBy` | ✅ PASSED |
| Maker-checker doesn't block legitimate approval | `testDifferentUserCanApprove` | A different user id can approve normally | ✅ PASSED |
| Maker-checker enforced at Application Service layer | `testApproveTransactionApplicationServiceEnforcesMakerChecker` | Same check through `ApproveTransactionApplicationService` | ✅ PASSED |
| UI Controller regression guard | `testAdminWorkspaceControllerDoesNotCallDeprecatedBypassMethod` | Asserts controller source code never re-introduces `->insertTransaction()` | ✅ PASSED |
| New transition wiring (`DRAFT → PENDING_APPROVAL`) | `testSubmitTransactionApplicationServiceMovesToPendingApproval` | `SubmitTransactionApplicationService` correctly transitions and persists | ✅ PASSED |
