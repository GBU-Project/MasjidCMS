<?php

declare(strict_types=1);

namespace App\Domains\Financial\Specifications;

use App\Domains\Financial\Entities\Fund;

class IsRestrictedFundSpecification
{
    public function isSatisfiedBy(Fund $fund): bool
    {
        return $fund->isRestricted();
    }
}
