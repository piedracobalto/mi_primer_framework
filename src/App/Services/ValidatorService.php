<?php

declare (strict_types = 1);

namespace App\Services;

use Framework\Validator;
use Framework\Rules\{RequiredRule,
    EmailRule,
    InRule,
    MatchRule,
    MinRule,
    UrlRule,
    LengthMaxRule,
    NumericRule,
    DateFormatRule
};

class ValidatorService
{
    private Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator();

        $this->validator->add("required",new RequiredRule());
        $this->validator->add("email",new EmailRule());
        $this->validator->add("min",new MinRule());
        $this->validator->add("in",new InRule());
        $this->validator->add("url",new UrlRule());
        $this->validator->add("match",new MatchRule());
        $this->validator->add("length_max",new LengthMaxRule());
        $this->validator->add("numeric",new NumericRule());
        $this->validator->add("date_format",new DateFormatRule());
    }

    public function validateLogin(array $form_data)
    {
        $this->validator->validate($form_data, [
            "email" => ["required","email"],
            "password" => ["required"],
        ]);
    }

    public function validateRegister(array $form_data)
    {
        $this->validator->validate($form_data, [
            "email" => ["required","email"],
            "age" => ["required", "min:18"],
            "country" => ["required","in:USA,Canada,Mexico"],
            "social_media_url" => ["required","url"],
            "password" => ["required"],
            "confirm_password" => ["required","match:password"],
            "tos" => ["required"],
        ]);
    }

    public function validateTransaction(array $form_data)
    {
        $this->validator->validate($form_data, [
            "description" => ["required", "length_max:255"],
            "amount" => ["required","numeric"],
            "date" => ["required","date_format:Y-m-d"],
        ]);
    }
}