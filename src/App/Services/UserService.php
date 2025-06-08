<?php

declare (strict_types = 1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class UserService
{
    public function __construct(private Database $db)
    {
        
    }

    public function isEmailTaken(string $email)
    {
        $email_count = $this->db->query(
            "SELECT count(1) from users where email = :email",
            [
                "email" => $email
            ]
        )->count();

        if ($email_count > 0) 
        {
            throw new ValidationException(["email" => "Email is Taken"]);
        }
    }

    public function create(array $form_data)
    {
        $password = password_hash($form_data["password"],PASSWORD_BCRYPT,["cost" => 12]);

        $this->db->query(
            "INSERT INTO users(email,password,age,country,social_media_url)
            VALUES(:email, :password, :age, :country, :url)",
            [
                "age"       => $form_data["age"],
                "url"       => $form_data["social_media_url"],
                "email"     => $form_data["email"],
                "country"   => $form_data["country"],
                "password"  => $password,
            ]
        );

        session_regenerate_id();

        $_SESSION["user"] = $this->db->id();
    }

    public function login(array $form_data)
    {
        $user = $this->db->query("SELECT * FROM users WHERE email = :email",[
            "email" => $form_data["email"]
        ])->find();

        $passwords_match = password_verify($form_data["password"], $user["password"] ?? '');

        if (!$user || !$passwords_match) 
        {
            throw new ValidationException(["password" => ["Invalid credentials"]]);
        }

        session_regenerate_id();

        /* Al loguearse correctamente se crea una variable de sesion para saber que sea ese usuario quien esta usando esa sesion */
        $_SESSION["user"] = $user["id"];
    }

    public function logout()
    {
        /* la funcion unset() solo borra la variable de sesion que le solicitamos
        mientras que session_destroy destruye todas las variables de sesion
        */
        //unset($_SESSION["user"]);
        session_destroy();

        /* la funcion session_regenerate_id() genera una nueva id de sesion
        mientras que setcookie() crea una variable de sesion personalizado
        utilizando parte de las variable de sesion y datos asignados por el desarrollador
        */
        //session_regenerate_id();

        $params = session_get_cookie_params();

        setcookie(
            name:"PHPSESSID",
            //borra el contenido de la variable de de sesion (en este caso PHPSESSID)
            value:'',
            //La cookie dura 1 hora (3600 segundos) antes de que la cookie expire
            expires_or_options:time() - 3600,
            path: $params["path"],
            domain: $params["domain"],
            secure: $params["secure"],
            httponly: $params["httponly"]
        );
    }
}