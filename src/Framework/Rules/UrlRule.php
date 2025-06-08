<?php

declare (strict_types = 1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;

class UrlRule implements RuleInterface
{
    public function validate(array $data, string $field, array $params) : bool
    {
        /* SE CASTEA EN BOOL PORQUE FILTER_VAR DEVUELVE UN STRING CUANDO ES VALIDO EL MAIL 
        Y DEVUELVE FALSE CUANDO NO LO ES Y ESTE METODO REQUIERE QUE DEVUELVA UN BOOLEANO */
        return (bool) filter_var($data[$field],FILTER_VALIDATE_URL);
    }

    public function getMessage(array $data, string $field, array $params) : string
    {
        return "Invalid URL.";
    }
}