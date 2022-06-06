<?php

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Infrastructure\Persistence\DBConnector;
use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

require_once 'vendor/autoload.php';

$connection = DBConnector::createConnection();
$studentRepository = new PdoStudentRepository($connection);

$connection->beginTransaction();
try {
    $aStudent = new Student(
        null,
        'Nico Steppat',
        new DateTimeimmutable('1985-05-01')
    );

    $studentRepository->save($aStudent);

    $anotherStudent = new Student(
        null,
        'Sergio Lopes',
        new DateTimeimmutable('1985-05-01')
    );

    $studentRepository->save($anotherStudent);
    $connection->commit();
} catch (PDOException $e) {
    $connection->rollBack();
    echo $e->getMessage();
}