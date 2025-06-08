<?php 

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\TemplateEngine;

class CsrfTokenMiddleware implements MiddlewareInterface
{
    public function __construct(private TemplateEngine $view)
    {
        
    }

    public function process(callable $next)
    {
        /*  1. random_bytes(32) Genera 32 bytes de datos aleatorios criptográficamente seguros.
            Esto es útil para generar valores difíciles de predecir, como tokens de seguridad.
            2. bin2hex(random_bytes(32)) Convierte esos 32 bytes binarios en una cadena hexadecimal.
            Cada byte se representa con 2 caracteres hexadecimales, 
            así que el resultado tendrá 64 caracteres.*/

        /* SI NO ESTA DEFINIDO O TIENE VALOR NULL $_SESSION["token"] SE LE ASIGNA EL CODIGO ENCRIPTADO */
        $_SESSION["token"] =  $_SESSION["token"] ?? bin2hex(random_bytes(32));

        $this->view->addGlobal('csrf_token',$_SESSION["token"]);
        $next();
    }
}