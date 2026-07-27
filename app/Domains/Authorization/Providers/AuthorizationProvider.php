<?php

namespace App\Domains\Authorization\Providers;

use App\Domains\Authorization\Services\AuthorizationService;
use App\Domains\Authorization\Services\PolicyResolver;

/**
 * Class AuthorizationProvider
 */
class AuthorizationProvider
{
    protected ?AuthorizationService $authService = null;
    protected ?PolicyResolver $policyResolver = null;

    public function register(): void
    {
        if ($this->authService === null) {
            $this->authService = new AuthorizationService();
            $this->policyResolver = new PolicyResolver($this->authService);
        }
    }

    public function getAuthService(): AuthorizationService
    {
        if ($this->authService === null) {
            $this->register();
        }
        return $this->authService;
    }

    public function getPolicyResolver(): PolicyResolver
    {
        if ($this->policyResolver === null) {
            $this->register();
        }
        return $this->policyResolver;
    }
}
