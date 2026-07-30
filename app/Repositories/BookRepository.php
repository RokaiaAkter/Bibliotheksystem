<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class BookRepository extends BaseRepository
{
    public function search(string $query = '', int $limit = 100): array
    {
        $query = trim($query);
        $sql = 'SELECT id, isbn, title, author, status, created_at, updated_at
                FROM books';
        $parameters = [];

        if ($query !== '') {
            $sql .= ' WHERE title LIKE :query OR author LIKE :query OR isbn LIKE :query';
            $parameters['query'] = '%' . $query . '%';
        }

        $sql .= ' ORDER BY title LIMIT :limit';
        $statement = $this->database->prepare($sql);

        foreach ($parameters as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function available(): array
    {
        return $this->database
            ->query(
                "SELECT id, isbn, title, author
                 FROM books
                 WHERE status = 'available'
                 ORDER BY title"
            )
            ->fetchAll();
    }

    public function allForExport(): array
    {
        return $this->database
            ->query(
                'SELECT isbn, title, author, status, created_at, updated_at
                 FROM books
                 ORDER BY title'
            )
            ->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->database->prepare(
            'SELECT id, isbn, title, author, status FROM books WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $book = $statement->fetch();

        return $book ?: null;
    }

    public function findForUpdate(int $id): ?array
    {
        $statement = $this->database->prepare(
            'SELECT id, isbn, title, author, status
             FROM books
             WHERE id = :id
             FOR UPDATE'
        );
        $statement->execute(['id' => $id]);
        $book = $statement->fetch();

        return $book ?: null;
    }

    public function create(array $data): int
    {
        $statement = $this->database->prepare(
            "INSERT INTO books (isbn, title, author, status)
             VALUES (:isbn, :title, :author, 'available')"
        );
        $statement->execute([
            'isbn' => $data['isbn'],
            'title' => $data['title'],
            'author' => $data['author'],
        ]);

        return (int) $this->database->lastInsertId();
    }

    public function upsert(array $data): void
    {
        $statement = $this->database->prepare(
            "INSERT INTO books (isbn, title, author, status)
             VALUES (:isbn, :title, :author, 'available')
             ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                author = VALUES(author),
                updated_at = CURRENT_TIMESTAMP"
        );
        $statement->execute([
            'isbn' => $data['isbn'],
            'title' => $data['title'],
            'author' => $data['author'],
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->database->prepare(
            "DELETE FROM books WHERE id = :id AND status = 'available'"
        );
        $statement->execute(['id' => $id]);

        return $statement->rowCount() === 1;
    }

    public function updateStatus(int $id, string $status): void
    {
        $statement = $this->database->prepare(
            'UPDATE books SET status = :status WHERE id = :id'
        );
        $statement->execute(['status' => $status, 'id' => $id]);
    }

    public function counts(): array
    {
        $rows = $this->database
            ->query('SELECT status, COUNT(*) AS total FROM books GROUP BY status')
            ->fetchAll();
        $counts = ['available' => 0, 'loaned' => 0];

        foreach ($rows as $row) {
            $counts[(string) $row['status']] = (int) $row['total'];
        }

        $counts['total'] = $counts['available'] + $counts['loaned'];

        return $counts;
    }
}

