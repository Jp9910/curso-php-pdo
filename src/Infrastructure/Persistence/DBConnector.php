<?php

namespace Alura\Pdo\Infrastructure\Persistence;

use PDO;

class DBConnector
{
    public static function createConnection(): PDO
    {
        // $databasePath = __DIR__ . '/../../../banco.sqlite';
        // $con = new PDO('sqlite:' . $databasePath);

        $con = new PDO('mysql:host=localhost:8080;dbname=alura','root','password');
        
        //lançar exceção quando houver erro no sql
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //modo padrão do fetch all
        $con->setAttribute(/*attribute:*/PDO::ATTR_DEFAULT_FETCH_MODE, /*value:*/PDO::FETCH_ASSOC);
        return $con;
    }
}