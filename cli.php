<?php

include __DIR__.'/src/Framework/Database.php';

use Framework\Database;

$db = new Database("mysql",[
    "host" => 'localhost',
    'port' => 3306,
    'dbname' => 'mi_primer_db'
],'root','');

$sql_file = file_get_contents("./database.sql");

$db->connection->query($sql_file);

