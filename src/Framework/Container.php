<?php

declare(strict_types=1);

namespace Framework;

use ReflectionClass, ReflectionNamedType;
use Framework\Exceptions\ContainerException;

class Container
{
    private array $definitions = [];
    private array $resolved = [];

    public function addDefinitions(array $new_definitions)
    {
        // es lo mismo que hacer array_merge($this->definitions, $new_definitions)
        // agrega todas las definiciones
        $this->definitions = [...$this->definitions, ...$new_definitions];
    }

    public function resolve(string $class_name)
    {
        $reflection_class = new ReflectionClass($class_name);

        if (!$reflection_class->isInstantiable()) 
        {
            throw new ContainerException("Class {$class_name} is not instantiable");
        }

        $constructor = $reflection_class->getConstructor();

        if (!$constructor)
        {
            return new $class_name;
        }

        $params = $constructor->getParameters();

        if (count($params) === 0)
        {
            return new $class_name;
        }

        $dependencies = [];

        foreach ($params as $param) 
        {
            $name = $param->getName();
            $type = $param->getType();

            if (!$type)
            {
                throw new ContainerException("Failed to resolved class {$class_name} because param {$name} is missing a type hint");
            }

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin())
            {
                throw new ContainerException("Failed to resolved class {$class_name} because invalid param name");
            }

            $dependencies[] = $this->get($type->getName());
        }

        return $reflection_class->newInstanceArgs($dependencies);
    }

    /* funcion de obtener la dependencia */
    public function get(string $id)
    {
        if (!array_key_exists($id,$this->definitions)) 
        {
            throw new ContainerException("Class {$id} does not exist in container");
        }

        /* APLICACION DE SINGLETON */
        if (array_key_exists($id,$this->resolved)) 
        {
            return $this->resolved[$id];
        }

        // ESTO ES UN PATRON DE DISEÑO FACTORY
        $factory = $this->definitions[$id];
        // se agrega el parametro $this para poder instanciar Clases (en este caso se puede instanciar Services y base de datos)
        $dependency = $factory($this);

        $this->resolved[$id] = $dependency;

        return $dependency;
    }
}