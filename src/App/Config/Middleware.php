<?php

declare(strict_types=1);

namespace App\Config;

use Framework\App;
use App\Middleware\{
    FlashMiddleware,
    TemplateDataMiddleware,
    ValidationExceptionMiddleware,
    SessionMiddleware,
    CsrfTokenMiddleware,
    CsrfGuardMiddleware
};

function registerMiddleware(App $app)
{
    $app->addMiddleware(CsrfGuardMiddleware::class);
    /* ANTES DE GENERARSE LA SESION SE GENERA EL TOKEN CSRF PARA EVITAR QUE SE GENERA POR FUERA DE LA APP */
    $app->addMiddleware(CsrfTokenMiddleware::class);
    $app->addMiddleware(TemplateDataMiddleware::class);
    $app->addMiddleware(ValidationExceptionMiddleware::class);
    /* ES IMPORTANTE QUE FLASHMIDDLEWARE VAYA ANTES DE SESSIONMIDDLEWARE PORQUE PRIMEROS VALIDAMOS 
    LOS MENSAJES Y LUEGO HABILITAMOS LA SESION CORRESPONDIENTE  */
    $app->addMiddleware(FlashMiddleware::class);
    $app->addMiddleware(SessionMiddleware::class);
}