<?php

declare(strict_types = 1);

namespace Framework;

class Router 
{
    private array $routes = [];
    private array $middlewares = [];
    private array $error_handler = [];

    public function add(string $method,string $path, array $controller)
    {
        $path = $this->normalizePath($path);

        /* devuelve todos los parametros de ruta que esten entre llaves: {user}/etc */
        $regex_path = preg_replace('#{[^/]+}#','([^/]+)',$path);

        $this->routes [] = [
            "path" => $path,
            "method" => strtoupper($method), 
            "controller" => $controller,
            "middleware" => [],
            "regex_path" => $regex_path
        ];
    }

    private function normalizePath(string $path) : string
    {
        $path = trim($path,'/');
        $path = "/{$path}/";
        // reemplazar dos o más barras consecutivas ([/]{2,}) por una sola /
        $path = preg_replace("#[/]{2,}#","/",$path);

        // devolcerias un formato de esta manera: /home/user/documents/
        return $path;
    }

    public function dispatch(string $path, string $method, Container $container = null)
    {
        $path = $this->normalizePath($path);
        /* $_POST["_METHOD"] esta pensado para un verbo DELETE o PATCH */
        $method = strtoupper($_POST["_METHOD"] ?? $method);

        foreach ($this->routes as $route) 
        {
            if (
                /* $param_values no se inicializa antes porque es una variable por referencia y se inicializa
                al momento de nombrarla como parametro */
                !preg_match("#^{$route['regex_path']}$#", $path,$param_values) ||
                $route["method"] !== $method)
            {
                /* Si no coincide la ruta con las que estan registradas o el metodo es distinto al de la ruta
                se lo ignora y pasa a recorrer otra ruta*/
                continue;
            }

            /* Elimina el primer elemento del vector que es el path dejando solamente los parametros */
            array_shift($param_values);

            /* trae todos los nombres de los parametros de una ruta dinamica
            y param_keys pasa lo mismo como param_values*/
            preg_match_all('#{([^/]+)}#',$route['path'],$param_keys);

            /* Solo usamos el segundo elemento que es el vector con los strings de los parametros */
            $param_keys = $param_keys[1];

            $params = array_combine($param_keys,$param_values);

            [$class, $function] = $route["controller"];

            $controller_instance = $container ?
            $container->resolve($class) :
            new $class;

            /* la variable ejecuta la funcion con los parametros dados del controllador  */
            $action = fn() => $controller_instance->{$function}($params);

            /* se unen los middlewares globales con los middlewares agregado a esa ruta */
            $all_middlewares = [...$route["middleware"], ...$this->middlewares];

            /* Se aplica el patron de diseño chain of responsability cuando se recorren
            los middlewares */
            foreach ($all_middlewares as $middleware)
            {
                $middleware_instance = $container ? 
                    $container->resolve($middleware) :
                    new $middleware;
                $action = fn() => $middleware_instance->process($action);
            }

            /* Si pasa correctamente los middlewares se ejecuta 
            la funcion con los parametros dados del controllador que es usa antes de 
            llamar los middlewares
            */

            /* EJEMPLO:
            $controller = fn() => "Respuesta final";

            $action = fn() => $middleware1->process(
                fn() => $middleware2->process($controller)
            );

            echo $action(); // Imprime "Respuesta final"
            */
            $action();

            return;
        }

        $this->dispatchNotFound($container);
    }

    public function addMiddleware(string $middleware)
    {
        $this->middlewares [] = $middleware;
    }

    public function addRouteMiddleware(string $middleware)
    {
        $last_route_key = array_key_last($this->routes);
        $this->routes[$last_route_key]["middleware"][] = $middleware;
    }

    public function setErrorHandler(array $controller)
    {
        $this->error_handler = $controller;

    }

    public function dispatchNotFound(?Container $container)
    {
        [$class, $function] = $this->error_handler;

        $controller_instance = $container ? $container->resolve($class) : new $class; 

        $action = fn() => $controller_instance->$function();

        foreach($this->middlewares as $middleware)
        {
            $middleware_instance = $container ? $container->resolve($middleware) : new $middleware; 
            $action = fn() => $middleware_instance->process($action);
        }

        /* Si pasa correctamente los middlewares se ejecuta 
        la funcion con los parametros dados del controllador que es usa antes de 
        llamar los middlewares
        */

        /* EJEMPLO:
        $controller = fn() => "Respuesta final";

        $action = fn() => $middleware1->process(
            fn() => $middleware2->process($controller)
        );

        echo $action(); // Imprime "Respuesta final"
        */

        $action();
    }
}

