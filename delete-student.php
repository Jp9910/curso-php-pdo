<?php

use Alura\Pdo\Domain\Model\Student;

require_once 'vendor/autoload.php';

$caminho = __DIR__ . '/banco.sqlite';
$pdo = new PDO("sqlite:$caminho");

$preparedStatement = $pdo->prepare('DELETE FROM students WHERE id = ?;');
$preparedStatement->bindValue(/*parameter:*/ 1, /*value:*/ 2, /*data_type:*/ PDO::PARAM_INT);

var_dump($preparedStatement->execute());

$preparedStatement->bindValue(1, 3, PDO::PARAM_INT); //PDO::PARAM_STR
var_dump($preparedStatement->execute());