<?php

namespace App\Middleware;

use \Slim\Csrf\Guard;

class CsrfResponseHeader
{

    private $csrf;

    public function __construct(Guard $csrf)
    {
        $this->csrf = $csrf;
    }

    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {        

        $response = $next($request, $response);
        $nameKey = $this->csrf->getTokenNameKey();
        $valueKey = $this->csrf->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);
        $tokenArray = [
            $nameKey => $name,
            $valueKey => $value
        ];
        $response = $response->withAddedHeader('X-CSRF-Token', json_encode($tokenArray));
        return $response;
    }
}