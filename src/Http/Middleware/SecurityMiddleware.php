<?php
namespace Utensils\Http\Middleware;

use Cake\Core\Configure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SecurityMiddleware
{
    /**
     * Very basic security middleware for adding a few extra headers
     *
     * @param ServerRequestInterface $request Instance of the incoming request
     * @param ResponseInterface $response Instance of the outgoing response
     * @param callable $next The next middleware
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $next($request, $response);

        if (Configure::read('debug') === false) {
            $response = $response->withAddedHeader('X-Frame-Options', 'deny');
        }
        $response = $response->withAddedHeader('X-Content-Type-Options', 'nosniff');
        $response = $response->withAddedHeader('X-XSS-Protection', '1; mode=block');

        return $response;
    }
}
