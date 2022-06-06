<?php

use Alura\Pdo\Infrastructure\Persistence\DBConnector;
use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

require_once 'vendor/autoload.php';

$con = DBConnector::createConnection();
$repo = new PdoStudentRepository($con);

$studentList = $repo->studentsWithPhones();

var_dump($studentList);