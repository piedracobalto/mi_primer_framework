<?php

declare(strict_types=1);

namespace Framework;

class App {

    private Container $container;
    private Router $router;
    
    public function __construct(string $container_definitions_path = null) 
    {
        $this->router = new Router();
        $this->container = new Container();

        if ($container_definitions_path)
        {
            $container_definitions = include $container_definitions_path;
            $this->container->addDefinitions($container_definitions);
        }
    }

    public function run () {
        $path = parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH);
        $method = $_SERVER["REQUEST_METHOD"];

        $this->router->dispatch($path, $method, $this->container);
    }

    public function get(string $path, array $controller) : App
    {
        $this->router->add("GET", $path, $controller);

        //Finalmente, retorna $this para permitir encadenamiento de métodos usando el patron de diseño Builder
        return $this;
    }

    public function post(string $path, array $controller) : App
    {
        $this->router->add("POST", $path, $controller);

        //Finalmente, retorna $this para permitir encadenamiento de métodos usando el patron de diseño Builder
        return $this;
    }

    public function delete(string $path, array $controller) : App
    {
        $this->router->add("DELETE", $path, $controller);

        //Finalmente, retorna $this para permitir encadenamiento de métodos usando el patron de diseño Builder
        return $this;
    }

    public function addMiddleware (string $middleware)
    {
        $this->router->addMiddleware($middleware);
    }

    public function add(string $middleware)
    {
        $this->router->addRouteMiddleware($middleware);
    }

    public function setErrorHandler(array $controller)
    {
        $this->router->setErrorHandler($controller);
    }

}