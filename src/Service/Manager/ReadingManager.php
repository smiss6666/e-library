<?php
declare(strict_types=1);

namespace App\Service\Manager;

use DateTime;
use DateTimeInterface;

class ReadingManager extends AbstractManager
{

    public function query(array $filter = []): array
    {
        $sql    = "
        SELECT reading.id AS _id, reading.*, IF(reading.end_at < NOW(), 1, 0) AS is_expire
        FROM `reading`
        LEFT JOIN `users` ON users.id = reading.user_id
        LEFT JOIN `authors_books` AS ab ON ab.book_id = reading.book_id
        LEFT JOIN `authors` AS author ON author.id = ab.author_id
        WHERE 1 ";
        $params = [];

        if (!empty($filter['q'])) {
            $sql             .= " AND (
                users.username      LIKE :query OR
                users.email         LIKE :query OR
                users.first_name    LIKE :query OR
                users.last_name     LIKE :query OR
                author.first_name   LIKE :query OR
                author.last_name    LIKE :query
            ) ";
            $params['query'] = '%' . $filter['q'] . '%';
        }

        if (!empty($filter['reading_type'])) {
            $sql                    .= " AND (reading.reading_type = :reading_type) ";
            $params['reading_type'] = $filter['reading_type'];
        }

        if (!empty($filter['user_id'])) {
            $sql               .= " AND (reading.user_id = :user_id) ";
            $params['user_id'] = $filter['user_id'];
        }

        if (!empty($filter['isProlong'])) {
            $sql .= " AND (reading.prolong_at IS NOT NULL) ";
        }

        $sql .= " GROUP BY reading.id";

        return [$sql, $params];
    }

    public function create(array $data): int
    {
        /** @var DateTimeInterface $startAt */
        $startAt = $data['start_at'] ?? null;

        /** @var DateTimeInterface $endAt */
        $endAt = $data['end_at'] ?? null;

        $sql = "
        INSERT INTO `reading` (
            `book_id`, `user_id`, `quantity`, `reading_type`, `start_at`, `end_at`
        )
        VALUES  (
            :book_id, :user_id, :quantity, :reading_type, :start_at, :end_at
        )";

        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery([
            'book_id'      => $data['book_id'] ?? null,
            'user_id'      => $data['user_id'] ?? null,
            'quantity'     => $data['quantity'] ?? null,
            'reading_type' => $data['reading_type'] ?? null,
            'start_at'     => $startAt ? $startAt->format('Y-m-d') : null,
            'end_at'       => $endAt ? $endAt->format('Y-m-d') : null,
        ]);

        $id = (int)$conn->lastInsertId();

        return $id;
    }

    public function update(int $id, array $data): int
    {
        /** @var DateTimeInterface $startAt */
        $startAt = $data['start_at'] ?? null;

        /** @var DateTimeInterface $endAt */
        $endAt = $data['end_at'] ?? null;

        $sql  = "UPDATE `reading` SET
                `book_id` = :book_id,
                `user_id` = :user_id,
                `quantity` = :quantity,
                `reading_type` = :reading_type,
                `start_at` = :start_at,
                `end_at` = :end_at
                WHERE id = :id";
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery([
            'id'           => $id,
            'book_id'      => $data['book_id'] ?? null,
            'user_id'      => $data['user_id'] ?? null,
            'quantity'     => $data['quantity'] ?? null,
            'reading_type' => $data['reading_type'] ?? null,
            'start_at'     => $startAt ? $startAt->format('Y-m-d') : null,
            'end_at'       => $endAt ? $endAt->format('Y-m-d') : null,
        ]);

        return $id;
    }

    public function get(int $id): ?array
    {
        $sql  = "SELECT * FROM `reading` WHERE id = :id";
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['id' => $id])->fetchAssociative();
    }

    public function delete(int $id): void
    {
        $sql  = "DELETE FROM `reading` WHERE id = :id";
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery(['id' => $id]);
    }

    public function countBooks($bookId): int
    {
        $sql  = "SELECT COUNT(id) FROM `reading` WHERE book_id = :book_id";
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);

        return (int)$stmt->executeQuery(['book_id' => $bookId])->fetchOne();
    }

    public function prolong(int $id, array $data): int
    {
        /** @var DateTimeInterface $prolongAt */
        $prolongAt = $data['prolong_at'] ?? null;

        $sql  = "UPDATE `reading` SET
                `prolong_at` = :prolong_at
                WHERE id = :id";
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery([
            'id'         => $id,
            'prolong_at' => $prolongAt ? $prolongAt->format('Y-m-d') : null,
        ]);

        return $id;
    }

    public function prolongCancel(int $id): int
    {
        $this->prolong($id, ['prolong_at' => null]);

        return $id;
    }

    public function prolongAccept(int $id): int
    {
        $data = $this->get($id);

        /** @var DateTimeInterface $prolongAt */
        $prolongAt  = $data['prolong_at'] ? DateTime::createFromFormat('Y-m-d', $data['prolong_at']) : null;

        $sql  = "UPDATE `reading` SET
                `prolong_at` = null,
                `end_at` = :prolong_at
                WHERE id = :id";
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery([
            'id'         => $id,
            'prolong_at' => $prolongAt ? $prolongAt->format('Y-m-d') : null,
        ]);

        return $id;
    }
}
