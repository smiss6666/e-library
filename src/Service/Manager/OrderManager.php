<?php
declare(strict_types=1);

namespace App\Service\Manager;

use App\Entity\Order;
use DateTime;
use DateTimeInterface;

class OrderManager extends AbstractManager
{


    protected ReadingManager $readingManager;

    public function __construct(
        ReadingManager $readingManager
    ) {
        $this->readingManager = $readingManager;
    }

    public function query(array $filter = []): array
    {
        $sql    = "
        SELECT orders.id AS _id, orders.*
        FROM `orders`
        LEFT JOIN `books` AS book ON book.id = orders.book_id
        LEFT JOIN `authors_books` AS ab ON ab.book_id = book.id
        LEFT JOIN `authors` AS author ON author.id = ab.author_id
        WHERE 1 ";
        $params = [];

        if (!empty($filter['q'])) {
            $sql             .= " AND (
                books.title         LIKE :query OR
                books.description   LIKE :query OR
                author.first_name   LIKE :query OR
                author.last_name    LIKE :query
            )";
            $params['query'] = '%' . $filter['q'] . '%';
        }

        if (!empty($filter['status'])) {
            $sql              .= " AND (orders.status = :status) ";
            $params['status'] = $filter['status'];
        }


        $sql .= " GROUP BY orders.id";
        $sql .= " ORDER BY orders.id ASC , orders.created_at ASC ";

        return [$sql, $params];
    }

    public function create(array $data): int
    {
        /** @var DateTimeInterface $startAt */
        $startAt = $data['start_at'] ?? null;
        /** @var DateTimeInterface $endAt */
        $endAt = $data['end_at'] ?? null;

        $sql = "
        INSERT INTO `orders` (
            `book_id`, `user_id`, `quantity`, `reading_type`, `start_at`, `end_at`, `status`
        )
        VALUES  (
            :book_id, :user_id, :quantity, :reading_type, :start_at, :end_at, :status
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
            'status'       => Order::STATUS_OPEN,

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

        $sql  = "UPDATE `orders` SET
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
            'book_id'      => $data['book_id'],
            'user_id'      => $data['user_id'],
            'quantity'     => $data['quantity'],
            'reading_type' => $data['reading_type'],
            'start_at'     => $startAt ? $startAt->format('Y-m-d') : null,
            'end_at'       => $endAt ? $endAt->format('Y-m-d') : null
        ]);

        return $id;
    }

    public function get(int $id): ?array
    {
        $sql  = "SELECT * FROM `orders` WHERE id = :id";
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['id' => $id])->fetchAssociative();
    }

    public function delete(int $id): void
    {
        $sql  = "DELETE FROM `orders` WHERE id = :id";
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery(['id' => $id]);
    }

    public function status($id, array $data): int
    {
        $sql  = "UPDATE `orders` SET
                `status` = :status
                WHERE id = :id";
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery([
            'id'     => $id,
            'status' => $data['status'] ?? null,
        ]);

        return $id;
    }

    public function cancel(int $id): int
    {
        $this->status($id, ['status' => Order::STATUS_CANCELED]);
        return $id;
    }

    public function done(int $id): int
    {
        $this->status($id, ['status' => Order::STATUS_DONE]);
        $order   = $this->get($id);
        $startAt = $order['start_at'] ? DateTime::createFromFormat('Y-m-d', $order['start_at']) : null;
        $endAt   = $order['end_at'] ? DateTime::createFromFormat('Y-m-d', $order['end_at']) : null;
        $data    = [
            'book_id'      => $order['book_id'],
            'quantity'     => $order['quantity'],
            'user_id'      => $order['user_id'],
            'reading_type' => $order['reading_type'],
            'start_at'     => $startAt,
            'end_at'       => $endAt,
        ];
        $this->readingManager->create($data);

        return $id;
    }

    public function open(int $id): int
    {
        $this->status($id, ['status' => Order::STATUS_OPEN]);

        return $id;
    }

}
