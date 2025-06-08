<?php
declare(strict_types = 1);

namespace Framework;

class TemplateEngine 
{
    private array $global_template_data = [];

    public function __construct(private string $base_path)
    {
        
    }

    public function render(string $template, array $data = [])
    {
        extract($data,EXTR_SKIP);
        extract($this->global_template_data,EXTR_SKIP);

        /* Inicia el búfer de memoria y todo lo posterior hasta ob_end_clean se guarda */
        ob_start();

        include $this->resolve($template);
        
        /* se guarda el recurso que es el archivo junto con la data traida si es que tiene  */
        $output = ob_get_contents();

        /* limpia el bufer de memoria */
        ob_end_clean();

        /* devuelve todo el recurso */
        return $output;
    }

    public function resolve(string $path)
    {
        return "{$this->base_path}/{$path}";
    }

    public function addGlobal(string $key, mixed $value)
    {
        $this->global_template_data[$key] = $value;
    }
}