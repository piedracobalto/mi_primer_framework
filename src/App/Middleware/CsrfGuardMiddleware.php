<?php 

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\TemplateEngine;

class CsrfGuardMiddleware implements MiddlewareInterface
{
    public function __construct(private TemplateEngine $view)
    {
        
    }

    public function process(callable $next)
    {
        $request_method = strtoupper($_SERVER["REQUEST_METHOD"]);
        $valid_methods = ["POST","PATCH","DELETE"];

        if (!in_array($request_method,$valid_methods)) 
        {
            $next();
            return;
        }

        if ($_SESSION["token"] !== $_POST["token"]) 
        {
            redirectTo('/');
        }

        // se borra el token de la session para generar una nueva mas adelante
        unset($_SESSION["token"]);

        $next();
    }
}