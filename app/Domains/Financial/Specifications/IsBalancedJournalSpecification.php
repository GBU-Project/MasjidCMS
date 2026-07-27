<?php

declare(strict_types=1);

namespace App\Domains\Financial\Specifications;

use App\Domains\Financial\Entities\JournalEntry;

class IsBalancedJournalSpecification
{
    public function isSatisfiedBy(JournalEntry $journal): bool
    {
        return $journal->isBalanced();
    }
}
