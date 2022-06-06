<?php

namespace Alura\Pdo\Infrastructure\Repository;

use PDO;
use RuntimeException;
use Alura\Pdo\Infrastructure\Persistence\DBConnector;
use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Domain\Model\Phone;
use Alura\Pdo\Domain\Repository\StudentRepository;

// Repósitory é um padrão de projeto que extrai toda a lógica de acesso aos 
// dados do domínio para uma camada de infraestrutura. Um repository é uma classe
// que faz o acesso aos dados, mas esses dados serão acessados como uma collection

// A classe de repositório tem uma única responsabilidade: gerenciar a persistência 
// dos alunos. Com isso, em um sistema grande, fica muito mais fácil dar manutenção
// quando temos classes coesas como essa.

// The significant difference is that Repositories represent collections, while DAOs 
//   are closer to the database and are often far more table-centric.
// Basicamente, ambos servem o mesmo propósito, mas a diferença é na interface deles, 
//   ou seja, em termos práticos, nos nomes dos métodos.
class PdoStudentRepository implements StudentRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        //injeção de dependência
        $this->connection = $connection;
    }

    public function allStudents(): array
    {
        $sqlQuery = 'SELECT * FROM students;';
        $stmt = $this->connection->query($sqlQuery);

        return $this->hydrateStudentList($stmt);
    }

    public function studentsBirthAt(\DateTimeInterface $birthDate): array
    {
        $sqlQuery = 'SELECT * FROM students WHERE birth_date = ?;';
        $stmt = $this->connection->prepare($sqlQuery);
        $stmt->bindValue(1, $birthDate->format('Y-m-d'));
        $stmt->execute();

        return $this->hydrateStudentList($stmt);
    }

    // "Este é o conceito de "hidratar", sendo um padrão que simplesmente transfere 
    // dados de uma camada para outra; ou seja, estamos trazendo da camada do banco 
    // de dados para a de nossa classe, devolvendo esta lista de alunos."
    private function hydrateStudentList(\PDOStatement $stmt): array
    {
        $studentDataList = $stmt->fetchAll();
        $studentList = [];

        foreach ($studentDataList as $studentData) {
                $student = new Student(
                        $studentData['id'],
                        $studentData['name'],
                        new \DateTimeImmutable($studentData['birth_date'])
                );

                $this->fillPhonesOf($student);
                $studentList[] = $student;
        }

        return $studentList;
    }

    //Preenchendo os telefones por esse método tem-se o problema do N+1
    private function fillPhonesOf(Student $student): void
    {
        $sqlQuery = 'SELECT id, area_code, number FROM phones WHERE student_id = ?';
        $stmt = $this->connection->prepare($sqlQuery);
        $stmt->bindValue(1, $student->id(), PDO::PARAM_INT);
        $stmt->execute();

        $phoneDataList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($phoneDataList);
        foreach ($phoneDataList as $phoneData) {
            $phone = new Phone(
                $phoneData['id'],
                $phoneData['area_code'],
                $phoneData['number']
            );

            $student->addPhone($phone);
        }
    }

    public function save(Student $student): bool
    {
        if ($student->id() === null) {
            return $this->insert($student);
        }

        return $this->update($student);
    }

    public function insert(Student $student): bool
    {
        $insertQuery = 'INSERT INTO studentss (name, birth_date) VALUES (:name, :birth_date);';
        $stmt = $this->connection->prepare($insertQuery);

        $success = $stmt->execute([
            ':name' => $student->name(),
            ':birth_date' => $student->birthDate()->format('Y-m-d'),
        ]);

        $student->defineId($this->connection->lastInsertId());

        return $success;
    }

    public function update(Student $student): bool
    {
        $updateQuery = 'UPDATE students SET name = :name, birth_date = :birth_date WHERE id = :id;';
        $stmt = $this->connection->prepare($updateQuery);
        $stmt->bindValue(':name', $student->name());
        $stmt->bindValue(':birth_date', $student->birthDate()->format('Y-m-d'));
        $stmt->bindValue(':id', $student->id(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function remove(Student $student): bool
    {
        $stmt = $this->connection->prepare('DELETE FROM students WHERE id = ?;');
        $stmt->bindValue(1, $student->id(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function studentsWithPhones(): array
    {
        $sqlQuery = 'SELECT DISTINCT students.id,
                            students.name,
                            students.birth_date,
                            phones.id AS phone_id,
                            phones.area_code,
                            phones.number
                     FROM students
                     JOIN phones ON students.id = phones.student_id;';
        $stmt = $this->connection->query($sqlQuery);
        $result = $stmt->fetchAll();
        //var_dump($result);
        $studentList = [];

        foreach ($result as $row) {
            if (!array_key_exists($row['id'], $studentList)) {
                $studentList[$row['id']] = new Student(
                    $row['id'],
                    $row['name'],
                    new \DateTimeImmutable($row['birth_date'])
                );
            }
            $phone = new Phone($row['phone_id'], $row['area_code'], $row['number']);
            $studentList[$row['id']]->addPhone($phone);
        }

        return $studentList;
    }
}