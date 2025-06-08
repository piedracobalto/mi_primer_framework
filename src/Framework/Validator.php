<?php 

declare (strict_types = 1);

namespace Framework;

use Framework\Contracts\RuleInterface;
use Framework\Exceptions\ValidationException;

class Validator
{
    private array $rules = [];

    public function add (string $alias, RuleInterface $rule)
    {
        $this->rules[$alias] = $rule;
    }

    public function validate (array $form_data, array $fields)
    {

        $errors = [];

        foreach ($fields as $field_name => $rules )
        {
            foreach ($rules as $rule) 
            {
                $rule_params = [];

                if (str_contains($rule, ":"))
                {
                    //EJEMPLO SI TENGO UNA REGLA DE in:3,10,12
                    // $rule = "in"
                    // $rule_params[] = "3,10,12"
                    [$rule, $rule_params] = explode(":", $rule);
                    
                    //$rule_params[0] = "3"
                    //$rule_params[1] = "10"
                    //$rule_params[2] = "12"
                    $rule_params = explode(",",$rule_params);
                }


                $rule_validator = $this->rules[$rule];

                if ($rule_validator->validate($form_data,$field_name,$rule_params)) 
                {
                    continue;
                }

                $errors[$field_name][] = $rule_validator->getMessage(
                    $form_data,
                    $field_name,
                    $rule_params
                );
            }    
        }

        if (count($errors)) 
        {
            throw new ValidationException($errors);
        }
    }
}