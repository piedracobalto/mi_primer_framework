<?php 

declare (strict_types = 1);

namespace Framework;

use PDO,PDOException, PDOStatement;

class Database
{
    public PDO $connection;
    public PDOStatement $stmt;

    public function __construct(
        string $driver, 
        array $config, 
        string $username, 
        string $password)
    {
        //lo convierte en este formato de string
        //host=localhost;port:3306;dbname=mi_primer_db
        $config = http_build_query(data:$config ,arg_separator: ';');


        //string en formato dsn
        // mysql:host=localhost;port:3306;dbname=mi_primer_db
        $dsn = "{$driver}:{$config}";

        //CREAR CONEXION A LA BASE DE DATOS
        try 
        {
            //el ultimo parametro le dice a PDO que por defecto, al hacer fetch() devuelva los resultados como un array asociativo en lugar de un array mixto (asociativo + numérico)
            $this->connection = new PDO($dsn,$username,$password, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } 
        catch (PDOException $e) 
        {
            die("Unable to connect to database");
        }
    }

    public function query(string $query, array $params = []) : Database
    {
        $this->stmt = $this->connection->prepare($query);

        $this->stmt->execute($params);

        //Finalmente, retorna $this para permitir encadenamiento de métodos y hacer una query builder usando el patron de diseño Builder
        return $this;
    }

    public function count()
    {
        // fetchColumn() Devuelve la primera columna del primer resultado
        return $this->stmt->fetchColumn();
    }

    public function find()
    {
        // fetch() Devuelve en un vector de clave valor de la consulta sql
        return $this->stmt->fetch();
    }

    public function id()
    {
        return $this->connection->lastInsertId();
    }

    public function findAll()
    {
        return $this->stmt->fetchAll();
    }
}