<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;

class TransactionService
{
    public function __construct(private Database $db)
    {
        
    }

    public function create(array $form_data)
    {
        $formatted_date = "{$form_data['date']} 00:00:00";

        $this->db->query(
            "INSERT INTO transactions(user_id,description,amount,date)
            VALUES(:user_id,:description,:amount,:date)",
            [
                "date"        => $formatted_date,
                "user_id"     => $_SESSION["user"],
                "amount"      => $form_data["amount"],
                "description" => $form_data["description"],
            ]
        );
    }

    public function getUserTransactions(int $length, int $offset)
    {
        /* La función addcslashes escapa los caracteres especiales que aparecen 
        en la segunda cadena ('%_') dentro de la cadena de entrada.
        En este caso:

        % y _ son caracteres comodín en SQL LIKE:
        % = cualquier secuencia de caracteres.
        _ = cualquier carácter individual.
        
        Entonces, addcslashes antepondrá una barra invertida \ a esos caracteres 
        para que se traten como literales en una búsqueda con LIKE. */

        /* "s" es una convencion de la variable search para buscar cosas */
        $search_term = addcslashes($_GET["s"] ?? '','%_');

        $params = [
                "description" => "%{$search_term}%",
                "user_id"     => $_SESSION["user"],
        ];

        $transactions = $this->db->query(
            "SELECT *,DATE_FORMAT(date, '%Y-%m-%d') as formatted_date 
            FROM transactions WHERE user_id = :user_id
            AND description LIKE :description
            LIMIT {$length} OFFSET {$offset}",
            $params
        )->findAll();

        $transactions = array_map(function(array $transaction){

            $transaction["receipts"] = $this->db->query(
                "SELECT * FROM receipts where transaction_id = :transaction_id",
                [
                    "transaction_id" => $transaction["id"]
                ]
            )->findAll();

            return $transaction;
        },$transactions);

        $transactions_count = $this->db->query(
            "SELECT COUNT(*)
            FROM transactions WHERE user_id = :user_id
            AND description LIKE :description",
            $params
        )->count();

        return [$transactions,$transactions_count];
    }

    public function getUserTransaction(string $id)
    {
        return $this->db->query(
            "SELECT *, DATE_FORMAT(date,'%Y-%m-%d') AS formatted_date 
            FROM transactions 
            WHERE id = :id AND user_id = :user_id",
            [
                "id"      => $id,
                "user_id" => $_SESSION["user"]
            ]
        )->find();
    }

    public function update(array $form_data, int $id)
    {
        $formatted_date = "{$form_data['date']} 00:00:00";

        $this->db->query(
            "UPDATE transactions
            SET description = :description,
            amount = :amount,
            date = :date
            WHERE id = :id AND user_id = :user_id",
            [
                "id"          => $id,
                "user_id"     => $_SESSION["user"],
                "date"        => $formatted_date,
                "amount"      => $form_data["amount"],
                "description" => $form_data["description"],
            ]
        );
    }

    public function delete(int $id)
    {
        $this->db->query(
            "DELETE FROM transactions
            WHERE id = :id AND user_id = :user_id",
            [
                "id"          => $id,
                "user_id"     => $_SESSION["user"],
            ]
        );
    }
}