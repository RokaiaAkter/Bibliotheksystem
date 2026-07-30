<?php

declare(strict_types=1);

namespace App\Repositories;

final class LoanRepository extends BaseRepository
{
    public function active(): array
    {
        return $this->database
            ->query(
                'SELECT
                    loans.id,
                    loans.borrower_name,
                    loans.borrowed_at,
                    loans.due_at,
                    books.title,
                    books.isbn,
                    users.name AS created_by_name
                 FROM loans
                 INNER JOIN books ON books.id = loans.book_id
                 INNER JOIN users ON users.id = loans.created_by
                 WHERE loans.returned_at IS NULL
                 ORDER BY loans.due_at'
            )
            ->fetchAll();
    }

    public function create(int $bookId, string $borrowerName, string $dueAt, int $createdBy): int
    {
        $statement = $this->database->prepare(
            'INSERT INTO loans (book_id, borrower_name, due_at, created_by)
             VALUES (:book_id, :borrower_name, :due_at, :created_by)'
        );
        $statement->execute([
            'book_id' => $bookId,
            'borrower_name' => $borrowerName,
            'due_at' => $dueAt,
            'created_by' => $createdBy,
        ]);

        return (int) $this->database->lastInsertId();
    }

    public function findActiveForUpdate(int $loanId): ?array
    {
        $statement = $this->database->prepare(
            'SELECT id, book_id
             FROM loans
             WHERE id = :id AND returned_at IS NULL
             FOR UPDATE'
        );
        $statement->execute(['id' => $loanId]);
        $loan = $statement->fetch();

        return $loan ?: null;
    }

    public function markReturned(int $loanId): void
    {
        $statement = $this->database->prepare(
            'UPDATE loans SET returned_at = CURRENT_TIMESTAMP WHERE id = :id'
        );
        $statement->execute(['id' => $loanId]);
    }

    public function countActive(): int
    {
        return (int) $this->database
            ->query('SELECT COUNT(*) FROM loans WHERE returned_at IS NULL')
            ->fetchColumn();
    }
}

