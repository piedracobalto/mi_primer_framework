<?php 

declare (strict_types = 1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;
use InvalidArgumentException;

class NumericRule implements RuleInterface
{
    public function validate(array $data, string $field, array $params): bool
    {

        return is_numeric($data[$field]);
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return "Only numbers allowed.";
    }
}