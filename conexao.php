<?php
//'driver:informacoes_especificas_do_driver'
//'mysql:host=endereco_do_servidor;dbname=nome_do_banco'
/**
 * $connection = new PDO(
 *           dsn:'mysql:host=172.17.0.2;dbname=banco',
 *           username: 'root',
 *           password: 'senhalura'
 *       );
 */

//$caminho = __DIR__ . '/banco.sqlite';
//$pdo = new PDO("sqlite:$caminho");

$connection = new PDO('mysql:host=localhost:8080;dbname=alura','root','password');
echo 'Conectado' . PHP_EOL;

//$pdo->exec("INSERT INTO phones (area_code, number, student_id) VALUES ('24', '999999999', 4),('21', '222222222', 4);");
exit();

$createTableSql = '
    CREATE TABLE IF NOT EXISTS students (
        id INTEGER PRIMARY KEY,
        name TEXT,
        birth_date TEXT
    );

    CREATE TABLE IF NOT EXISTS phones (
        id INTEGER PRIMARY KEY,
        area_code TEXT,
        number TEXT,
        student_id INTEGER,
        FOREIGN KEY(student_id) REFERENCES students(id)
    );
';

$pdo->exec($createTableSql);