<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\BookRepository;
use App\Repositories\LoanRepository;
use DateTimeImmutable;
use DomainException;
use PDO;
use Throwable;

final class LoanService
{
    private BookRepository $books;
    private LoanRepository $loans;

    public function __construct(private readonly PDO $database)
    {
        $this->books = new BookRepository($database);
        $this->loans = new LoanRepository($database);
    }

    public function checkout(int $bookId, string $borrowerName, string $dueAt, int $createdBy): int
    {
        $borrowerName = trim($borrowerName);

        if ($bookId < 1 || $borrowerName === '' || strlen($borrowerName) > 140) {
            throw new DomainException('Bitte wählen Sie ein Buch und geben Sie einen Namen ein.');
        }

        $dueDate = DateTimeImmutable::createFromFormat('!Y-m-d', $dueAt);
        $today = new DateTimeImmutable('today');

        if (!$dueDate || $dueDate <= $today) {
            throw new DomainException('Das Rückgabedatum muss in der Zukunft liegen.');
        }

        $this->database->beginTransaction();

        try {
            $book = $this->books->findForUpdate($bookId);

            if (!$book) {
                throw new DomainException('Das Buch wurde nicht gefunden.');
            }

            if ($book['status'] !== 'available') {
                throw new DomainException('Das Buch ist bereits ausgeliehen.');
            }

            $loanId = $this->loans->create($bookId, $borrowerName, $dueAt, $createdBy);
            $this->books->updateStatus($bookId, 'loaned');
            $this->database->commit();

            return $loanId;
        } catch (Throwable $exception) {
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }

            throw $exception;
        }
    }

    public function returnBook(int $loanId): int
    {
        $this->database->beginTransaction();

        try {
            $loan = $this->loans->findActiveForUpdate($loanId);

            if (!$loan) {
                throw new DomainException('Die aktive Ausleihe wurde nicht gefunden.');
            }

            $this->loans->markReturned($loanId);
            $this->books->updateStatus((int) $loan['book_id'], 'available');
            $this->database->commit();

            return (int) $loan['book_id'];
        } catch (Throwable $exception) {
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }

            throw $exception;
        }
    }
}

