<?php

declare(strict_types=1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;

class DateFormatRule implements RuleInterface
{
    public function validate(array $data, string $field, array $params): bool
    {
        /* La función date_parse_from_format de PHP analiza una cadena de fecha/hora según 
        un formato específico y devuelve un array detallado con los componentes de fecha y hora 
        encontrados, junto con información sobre errores o advertencias de análisis. */
        $parsed_date = date_parse_from_format($params[0],$data[$field]);

        return $parsed_date["error_count"] === 0 && $parsed_date["warning_count"] === 0;
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return "Invalid date";
    }
}