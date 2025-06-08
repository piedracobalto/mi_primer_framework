<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\{TransactionService, ReceiptService};

class ReceiptController
{
  public function __construct(
    private TemplateEngine $view,
    private TransactionService $transaction_service,
    private ReceiptService $receipt_service,
  ) {
  }

  public function uploadView(array $params)
  {
    $transaction = $this->transaction_service->getUserTransaction($params['transaction']);

    if (!$transaction) {
      redirectTo("/");
    }

    echo $this->view->render("receipts/create.php");
  }

  public function upload(array $params)
  {
    $transaction = $this->transaction_service->getUserTransaction($params['transaction']);

    if (!$transaction) {
      redirectTo("/");
    }

    $receipt_file = $_FILES["receipt"] ?? null;

    $this->receipt_service->validateFile($receipt_file);

    $this->receipt_service->upload($receipt_file,$transaction["id"]);

    redirectTo("/");
    }

    public function download(array $params)
    {
        $transaction = $this->transaction_service->getUserTransaction($params['transaction']);

        if (empty($transaction)) 
        {
            redirectTo("/");
        }

        $receipt = $this->receipt_service->getReceipt($params['receipt']);

        if (empty($receipt)) 
        {
            redirectTo("/");
        }

        if ($receipt["transaction_id"] !== $transaction["id"]) 
        {
            redirectTo("/");
        }

        $this->receipt_service->read($receipt);
    }

    public function delete(array $params)
    {
        $transaction = $this->transaction_service->getUserTransaction($params['transaction']);

        if (empty($transaction)) 
        {
            redirectTo("/");
        }

        $receipt = $this->receipt_service->getReceipt($params['receipt']);

        if (empty($receipt)) 
        {
            redirectTo("/");
        }

        if ($receipt["transaction_id"] !== $transaction["id"]) 
        {
            redirectTo("/");
        }

        $this->receipt_service->delete($receipt);

        redirectTo("/");
    }
}