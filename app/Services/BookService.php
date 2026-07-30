<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\BookRepository;
use DomainException;
use PDO;
use SimpleXMLElement;
use Throwable;

final class BookService
{
    private BookRepository $books;

    public function __construct(private readonly PDO $database)
    {
        $this->books = new BookRepository($database);
    }

    public function create(array $input): int
    {
        $book = $this->validate($input);

        return $this->books->create($book);
    }

    public function delete(int $bookId): void
    {
        if (!$this->books->delete($bookId)) {
            throw new DomainException('Das Buch existiert nicht oder ist noch ausgeliehen.');
        }
    }

    public function importXml(SimpleXMLElement $xml): int
    {
        if ($xml->getName() !== 'books') {
            throw new DomainException('Das XML-Wurzelelement muss <books> heißen.');
        }

        if (count($xml->book) > 200) {
            throw new DomainException('Pro Import sind maximal 200 Bücher erlaubt.');
        }

        $imported = 0;
        $this->database->beginTransaction();

        try {
            foreach ($xml->book as $bookNode) {
                $book = $this->validate([
                    'isbn' => (string) $bookNode->isbn,
                    'title' => (string) $bookNode->title,
                    'author' => (string) $bookNode->author,
                ]);
                $this->books->upsert($book);
                $imported++;
            }

            $this->database->commit();
        } catch (Throwable $exception) {
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }

            throw $exception;
        }

        return $imported;
    }

    private function validate(array $input): array
    {
        $isbn = trim((string) ($input['isbn'] ?? ''));
        $title = trim((string) ($input['title'] ?? ''));
        $author = trim((string) ($input['author'] ?? ''));

        if ($isbn === '' || $title === '' || $author === '') {
            throw new DomainException('ISBN, Titel und Autor sind Pflichtfelder.');
        }

        if (strlen($isbn) > 20 || strlen($title) > 180 || strlen($author) > 140) {
            throw new DomainException('Mindestens ein Feld ist länger als erlaubt.');
        }

        if (!preg_match('/^[0-9Xx-]{10,20}$/', $isbn)) {
            throw new DomainException('Bitte geben Sie eine gültige ISBN ein.');
        }

        return [
            'isbn' => strtoupper($isbn),
            'title' => $title,
            'author' => $author,
        ];
    }
}

