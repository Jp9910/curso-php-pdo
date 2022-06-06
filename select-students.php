<?php

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Infrastructure\Persistence\DBConnector;
use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

require_once 'vendor/autoload.php';

$caminho = __DIR__ . '/banco.sqlite';
$pdo = new PDO("sqlite:$caminho");

$pdo = DBConnector::createConnection();
$repository = new PdoStudentRepository($pdo);
$studentList = $repository->allStudents();

var_dump($studentList);


exit();

/** formato padrão do retorno do fetchAll()
[
    [
        {nomeDaColuna1} => {valorDaColuna1},
        0 => {valorDaColuna1},
        {nomeDaColuna2} => {valorDaColuna2},
        1 => {valorDaColuna2},
    ]
]
 */

$sql = ("SELECT * FROM students");
$statement = $pdo->query($sql);

//pegar todos e transformar num array ASSOCiativo
$studentDataList = $statement->fetchAll(PDO::FETCH_ASSOC);
$studentList = [];
foreach ($studentDataList as $studentData) {
    $studentList[] = new Student(
        $studentData['id'],
        $studentData['name'],
        new \DateTimeImmutable($studentData['birth_date'])
    );
}
var_dump($studentList);


//pegar um por um e transformar num array ASSOCiativo
// while ($studentData = $statement->fetch(PDO::FETCH_ASSOC)) { //o fetch parte de onde o anterior parou. então se tiver um fetchAll() antes, não entrará no loop
//     $student = new Student(
//         $studentData['id'],
//         $studentData['name'],
//         new \DateTimeImmutable($studentData['birth_date'])
//     );

//     echo $student->age() . PHP_EOL;
// }
