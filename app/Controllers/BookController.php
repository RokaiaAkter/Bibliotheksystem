<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Repositories\BookRepository;
use App\Repositories\LoanRepository;
use App\Services\BookService;
use SimpleXMLElement;
use Throwable;

final class BookController extends BaseController
{
    public function index(): void
    {
        Auth::requirePermission('books.view');
        $query = substr(trim((string) ($_GET['q'] ?? '')), 0, 100);
        $books = new BookRepository($this->database);
        $loans = new LoanRepository($this->database);

        $this->render('books', [
            'title' => 'Bücher und Ausleihen',
            'books' => $books->search($query),
            'availableBooks' => $books->available(),
            'activeLoans' => $loans->active(),
            'query' => $query,
        ]);
    }

    public function create(): void
    {
        Auth::requirePermission('books.manage');

        try {
            $bookId = (new BookService($this->database))->create($_POST);
            $this->audit('create', 'book', $bookId, ['isbn' => (string) ($_POST['isbn'] ?? '')]);
            $this->success('Das Buch wurde angelegt.', 'books');
        } catch (Throwable $exception) {
            $this->failure($exception, 'books');
        }
    }

    public function delete(): void
    {
        Auth::requirePermission('books.manage');
        $bookId = filter_var($_POST['book_id'] ?? null, FILTER_VALIDATE_INT);

        try {
            if (!$bookId) {
                throw new \DomainException('Ungültige Buch-ID.');
            }

            (new BookService($this->database))->delete((int) $bookId);
            $this->audit('delete', 'book', (int) $bookId);
            $this->success('Das Buch wurde gelöscht.', 'books');
        } catch (Throwable $exception) {
            $this->failure($exception, 'books');
        }
    }

    public function exportXml(): void
    {
        Auth::requirePermission('books.view');
        $repository = new BookRepository($this->database);
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><books/>');

        foreach ($repository->allForExport() as $book) {
            $bookNode = $xml->addChild('book');

            foreach (['isbn', 'title', 'author', 'status', 'created_at', 'updated_at'] as $field) {
                $bookNode->addChild(
                    $field,
                    htmlspecialchars((string) $book[$field], ENT_XML1 | ENT_COMPAT, 'UTF-8')
                );
            }
        }

        $this->audit('export', 'book', null, ['format' => 'xml']);
        header('Content-Type: application/xml; charset=UTF-8');
        header('Content-Disposition: attachment; filename="bibliothksystem-books.xml"');
        header('X-Content-Type-Options: nosniff');
        echo $xml->asXML();
    }

    public function importXml(): void
    {
        Auth::requirePermission('books.manage');

        try {
            $file = $_FILES['xml_file'] ?? null;

            if (
                !is_array($file)
                || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
                || (int) ($file['size'] ?? 0) > 1_000_000
            ) {
                throw new \DomainException('Bitte wählen Sie eine XML-Datei bis maximal 1 MB.');
            }

            $originalName = (string) ($file['name'] ?? '');

            if (strtolower(pathinfo($originalName, PATHINFO_EXTENSION)) !== 'xml') {
                throw new \DomainException('Nur Dateien mit der Endung .xml sind erlaubt.');
            }

            $contents = file_get_contents((string) $file['tmp_name']);

            if ($contents === false || stripos($contents, '<!DOCTYPE') !== false) {
                throw new \DomainException('Die XML-Datei ist ungültig oder enthält eine nicht erlaubte DTD.');
            }

            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($contents, SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOCDATA);

            if (!$xml instanceof SimpleXMLElement) {
                throw new \DomainException('Die XML-Datei konnte nicht gelesen werden.');
            }

            $count = (new BookService($this->database))->importXml($xml);
            $this->audit('import', 'book', null, ['format' => 'xml', 'count' => $count]);
            $this->success("{$count} Bücher wurden importiert oder aktualisiert.", 'books');
        } catch (Throwable $exception) {
            $this->failure($exception, 'books');
        }
    }
}

