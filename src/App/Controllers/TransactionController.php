<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\{ValidatorService,TransactionService};

class TransactionController
{
    public function __construct(private TemplateEngine $view,
    private ValidatorService $validator_service,
    private TransactionService $transaction_service
    )
    {
        
    }

    public function createView()
    {
        echo $this->view->render("transactions/create.php");
    }
    
    public function create()
    {
        $this->validator_service->validateTransaction($_POST);
        
        $this->transaction_service->create($_POST);
        
        redirectTo('/');
    }

    public function editView(array $params)
    {
        $transaction = $this->transaction_service->getUserTransaction($params["transaction"]);

        if (!$transaction) {
            redirectTo('/');
        }

        echo $this->view->render('transactions/edit.php',[
            "transaction" => $transaction
        ]);
    }
    
    public function edit(array $params)
    {
        $transaction = $this->transaction_service->getUserTransaction($params["transaction"]);

        if (!$transaction) {
            redirectTo('/');
        }

        $this->validator_service->validateTransaction($_POST);

        $this->transaction_service->update($_POST,$transaction["id"]);

        /* redirige al usuario de nuevo a la página desde la que vino */
        redirectTo($_SERVER["HTTP_REFERER"]);
    }

    public function delete(array $params)
    {
        $this->transaction_service->delete((int) $params["transaction"]);

        redirectTo('/');
    }
}