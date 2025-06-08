<?php

declare (strict_types = 1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;

class MatchRule implements RuleInterface
{
    public function validate(array $data, string $field, array $params) : bool
    {
        $field_one = $data[$field];
        $field_two = $data[$params[0]];

        return $field_one === $field_two;

    }

    public function getMessage(array $data, string $field, array $params) : string
    {
        return "Does not match {$params[0]} field";
    }
}