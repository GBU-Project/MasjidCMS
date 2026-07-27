<?php

namespace Tests\Unit;

use App\Domains\System\Controllers\AuthenticationController;
use App\Domains\System\Services\AuthenticationService;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;
use PHPUnit\Framework\TestCase;

class AuthenticationThrottlingTest extends TestCase
{
    public function testLoginThrottlingAfterMaxAttempts(): void
    {
        $authService = $this->createMock(AuthenticationService::class);
        $authService->method('login')->willThrowException(new \App\Core\Exceptions\AuthorizationException('Invalid credentials', [], 401));

        $controller = new AuthenticationController($authService);

        $request = $this->createMock(IncomingRequest::class);
        $request->method('getJSON')->willReturn(['username' => 'baduser', 'password' => 'wrongpass']);
        $request->method('getIPAddress')->willReturn('192.168.1.100');

        $response = new Response(new \Config\App());
        $controller->initController($request, $response, new \Psr\Log\NullLogger());

        // Perform 5 attempts (allowed capacity)
        for ($i = 0; $i < 5; $i++) {
            $controller->login();
        }

        // 6th attempt should be throttled (429 Too Many Requests)
        $res = $controller->login();
        $this->assertSame(429, $res->getStatusCode());

        $body = json_decode($res->getBody(), true);
        $this->assertSame('error', $body['status']);
        $this->assertStringContainsString('Terlalu banyak percobaan', $body['message']);
    }
}
