<?php

declare (strict_types = 1);

namespace App\Controllers;

use App\Services\{ValidatorService, UserService};
use Framework\TemplateEngine;

class AuthController
{
    public function __construct(
        private TemplateEngine $view,
        private ValidatorService $validator_service,
        private UserService $user_service
        )
    {
    }

    public function registerView()
    {
        echo $this->view->render("register.php");
    }

    public function loginView()
    {
        echo $this->view->render("login.php");
    }

    public function login()
    {
        $this->validator_service->validateLogin($_POST);
        
        $this->user_service->login($_POST);

        redirectTo("/");
    }

    public function register()
    {
        $this->validator_service->validateRegister($_POST);

        $this->user_service->isEmailTaken($_POST["email"]);

        $this->user_service->create($_POST);

        redirectTo("/");
    }

    public function logout()
    {
        $this->user_service->logout();

        redirectTo('/login');
    }
}