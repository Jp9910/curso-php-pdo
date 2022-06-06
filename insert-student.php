<?php

use Alura\Pdo\Domain\Model\Student;

require_once 'vendor/autoload.php';

$pdo = \Alura\Pdo\Infrastructure\Persistence\DBConnector::createConnection();

// $student = new Student(null, 'Vinicius Dias', new \DateTimeImmutable('1997-10-15'));
// $sql = ("INSERT INTO students (name, birth_date) VALUES ('{$student->name()}', '{$student->birthDate()->format('Y-m-d')}')");
// echo $sql . PHP_EOL;
// $resultado = $pdo->exec($sql);
// var_dump($resultado);


//prepared statement:
$student = new Student(
    null,
    "Vinicius', ''); DROP TABLE students; -- Dias",
    new \DateTimeImmutable('1997-10-15')
);

// $sql = "INSERT INTO students (name, birth_date) VALUES (?, ?);";
// $statement = $pdo->prepare($sql);
// $statement->bindValue(1, $student->name()); //bindValue passa por valor. bindParam passa por referência
// $statement->bindValue(2, $student->birthDate()->format('Y-m-d'));

$student = new Student(
    null,
    "Patricia",
    new \DateTimeImmutable('2000-10-15')
);

$sql = "INSERT INTO students (name, birth_date) VALUES (:name , :birth_date);";
$statement = $pdo->prepare($sql);
$statement->bindParam(':name', $student->name()); //bindValue passa por valor. bindParam passa por referência
$statement->bindValue(':birth_date', $student->birthDate()->format('Y-m-d'));

if ($statement->execute())
    echo "Aluno Inserido";