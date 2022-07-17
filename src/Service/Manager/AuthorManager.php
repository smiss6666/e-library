<?php
declare(strict_types=1);

namespace App\Service\Manager;

class AuthorManager extends AbstractManager
{

    public function query(array $filter = []): array
    {
        $sql    = "SELECT authors.id AS _id, authors.* FROM `authors` WHERE 1 ";
        $params = [];

        if (!empty($filter['q'])) {
            $sql             .= " AND (
                authors.first_name    LIKE :query OR
                authors.last_name     LIKE :query
            )";
            $params['query'] = '%' . $filter['q'] . '%';
        }

        return [$sql, $params];
    }

    public function create(array $data): int
    {
        $sql = "
        INSERT INTO `authors` (
            `first_name`, `last_name`
        )
        VALUES  (
            :first_name, :last_name
        )";

        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery([
            'first_name' => $data['first_name'] ?? null,
            'last_name'  => $data['last_name'] ?? null,
        ]);

        $id = (int)$conn->lastInsertId();

        return $id;
    }

    public function update(int $id, array $data): int
    {
        $sql  = "UPDATE `authors` SET
                `first_name` = :first_name,
                `last_name` = :last_name
                WHERE id = :id";
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery([
            'id'         => $id,
            'first_name' => $data['first_name'] ?? null,
            'last_name'  => $data['last_name'] ?? null,
        ]);

        return $id;
    }

    public function get(int $id): ?array
    {
        $sql  = "SELECT * FROM `authors` WHERE id = :id";
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);

        $result = $stmt->executeQuery(['id' => $id])->fetchAssociative();

        return $result ?: null;
    }

    public function delete(int $id): void
    {
        $sql  = "DELETE FROM `authors` WHERE id = :id";
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery(['id' => $id]);
    }

}
