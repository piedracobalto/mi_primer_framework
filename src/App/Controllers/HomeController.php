<?php

declare (strict_types = 1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Config\Paths;
use App\Services\TransactionService;

class HomeController
{

    public function __construct(private TemplateEngine $view,
    private TransactionService $transaction_service
    )
    {
    }

    public function home()
    {
        $search_term = $_GET["s"] ?? NULL;

        /* "p" es una convencion para referirse a page para las paginaciones */
        $page = $_GET["p"] ?? 1;
        $page = (int) $page;
        /* $length es la cantidad de elementos que se muestran en cada pagina */
        $length = 2;
        /* $offset o desplazamiento indica cuántos registros se deben saltar antes de 
        comenzar a devolver los resultados */
        $offset = ($page -1) * $length;

        /* [$transactions,$count] SIGNIFICA QUE EL METODO DEVUELVE UN VECTOR CON DOS ELEMENTOS
        ENTONCES SE CREAN EN OTRO VECTOR DOS VARIABLES Y SE LE ASIGNA UN VALOR SEGUN LA POSICION
        EN LA QUE ESTEN */
        [$transactions,$count] = $this->transaction_service->getUserTransactions($length,$offset);

        $last_page = ceil($count / $length);

        /* genera un vector de numeros ordenados si existe la variable $last_page  */
        $pages = $last_page ? range(1,$last_page) : [];

        $page_links = array_map(
            fn($page_num) => http_build_query([
                "p"         => $page_num,
                "s"         => $search_term,
            ]),
            $pages
        );

        echo $this->view->render("/index.php", [
            "current_page" => $page,
            "title"        => "home page",
            "transactions" => $transactions,
            "previous_page_query" => http_build_query([
                "p"         => $page - 1,
                "s"         => $search_term,
            ]),
            "last_page" => $last_page,
            "next_page_query" => http_build_query([
                "p"         => $page + 1,
                "s"         => $search_term,
            ]),
            "page_links" => $page_links,
            "search_term" => $search_term
        ]);
    }
}