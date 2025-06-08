<?php

declare (strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\Exceptions\ValidationException;

class ValidationExceptionMiddleware implements MiddlewareInterface
{

    public function process (callable $next)
    {

        try 
        {
            $next();
        } 
        catch (ValidationException $e) 
        {
            $old_form_data = $_POST;

            $excluded_fields = ["password","confirm_password"];

            /* SOLO DEVUELVE LOS CAMPOS QUE NO ESTAN EN EL VECTOR DE excluded_fields */
            $formatted_form_data = array_diff_key(
                $old_form_data,
                /* el array_flip invierte los key con los values siendo  
                "password" => 0,  
                "confirm_password" => 1 */
                array_flip($excluded_fields) 
            ); 

            $_SESSION["errors"] = $e->errors;
            $_SESSION["old_form_data"] = $formatted_form_data;
            // redirige a la URL de la página web desde la cual se originó una solicitud
            $referer = $_SERVER["HTTP_REFERER"];
            redirectTo($referer);
        }
    }
}