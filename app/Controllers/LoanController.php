<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Services\LoanService;
use Throwable;

final class LoanController extends BaseController
{
    public function checkout(): void
    {
        Auth::requirePermission('loans.manage');

        try {
            $bookId = (int) ($_POST['book_id'] ?? 0);
            $loanId = (new LoanService($this->database))->checkout(
                $bookId,
                (string) ($_POST['borrower_name'] ?? ''),
                (string) ($_POST['due_at'] ?? ''),
                (int) Auth::id()
            );
            $this->audit('checkout', 'loan', $loanId, ['book_id' => $bookId]);
            $this->success('Das Buch wurde ausgeliehen.', 'books');
        } catch (Throwable $exception) {
            $this->failure($exception, 'books');
        }
    }

    public function returnBook(): void
    {
        Auth::requirePermission('loans.manage');

        try {
            $loanId = (int) ($_POST['loan_id'] ?? 0);

            if ($loanId < 1) {
                throw new \DomainException('Ungültige Ausleih-ID.');
            }

            $bookId = (new LoanService($this->database))->returnBook($loanId);
            $this->audit('return', 'loan', $loanId, ['book_id' => $bookId]);
            $this->success('Die Rückgabe wurde gespeichert.', 'books');
        } catch (Throwable $exception) {
            $this->failure($exception, 'books');
        }
    }
}

