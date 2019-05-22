<?php
namespace Utensils\Test\TestCase\Http\Middleware;

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Utensils\Http\Middleware\SecurityMiddleware;

class SecurityMiddlewareTest extends TestCase
{
    public function testInvokeAddsHeaders()
    {
        $middleware = new SecurityMiddleware();

        $request = $this->getMockBuilder(ServerRequest::class)->getMock();
        $response = new Response();
        $next = function ($request, $response) {
            return $response;
        };

        $result = $middleware->__invoke($request, $response, $next);
        $headers = $result->getHeaders();

        $this->assertArrayNotHasKey('X-Frame-Options', $headers);
        $this->assertArrayHasKey('X-Content-Type-Options', $headers);
        $this->assertArrayHasKey('X-XSS-Protection', $headers);
    }

    public function testInvokeAddsHeadersNotDebug()
    {
        \Cake\Core\Configure::write('debug', false);

        $middleware = new SecurityMiddleware();

        $request = $this->getMockBuilder(ServerRequest::class)->getMock();
        $response = new Response();
        $next = function ($request, $response) {
            return $response;
        };

        $result = $middleware->__invoke($request, $response, $next);
        $headers = $result->getHeaders();

        $this->assertArrayHasKey('X-Frame-Options', $headers);
        $this->assertArrayHasKey('X-Content-Type-Options', $headers);
        $this->assertArrayHasKey('X-XSS-Protection', $headers);
    }
}
