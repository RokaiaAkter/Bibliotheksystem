<div class="two-column two-column-wide">
    <section class="panel">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">Ajax + MySQL</p>
                <h2>Buchbestand</h2>
            </div>
            <a class="button button-secondary button-small" href="<?= e(url('books/export')) ?>">
                XML exportieren
            </a>
        </div>

        <form method="get" action="/index.php" class="search-form" role="search">
            <input type="hidden" name="route" value="books">
            <label class="sr-only" for="book-search">Bücher suchen</label>
            <input
                id="book-search"
                name="q"
                type="search"
                value="<?= e($query) ?>"
                placeholder="Titel, Autor oder ISBN"
                autocomplete="off"
                data-ajax-book-search
                data-endpoint="<?= e(url('api/books')) ?>"
            >
            <button class="button button-secondary" type="submit">Suchen</button>
        </form>
        <p class="muted search-status" data-search-status aria-live="polite">
            <?= count($books) ?> Ergebnis(se)
        </p>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Titel</th>
                    <th>Autor</th>
                    <th>ISBN</th>
                    <th>Status</th>
                    <?php if (can('books.manage')): ?><th>Aktion</th><?php endif; ?>
                </tr>
                </thead>
                <tbody
                    data-book-results
                    data-can-manage="<?= can('books.manage') ? '1' : '0' ?>"
                    data-delete-url="<?= e(url('books/delete')) ?>"
                >
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= e($book['title']) ?></td>
                        <td><?= e($book['author']) ?></td>
                        <td><code><?= e($book['isbn']) ?></code></td>
                        <td>
                            <span class="status status-<?= e($book['status']) ?>">
                                <?= $book['status'] === 'available' ? 'verfügbar' : 'ausgeliehen' ?>
                            </span>
                        </td>
                        <?php if (can('books.manage')): ?>
                            <td>
                                <form method="post" action="<?= e(url('books/delete')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="book_id" value="<?= e($book['id']) ?>">
                                    <button
                                        class="button button-danger button-small"
                                        type="submit"
                                        <?= $book['status'] !== 'available' ? 'disabled' : '' ?>
                                        data-confirm="Dieses verfügbare Buch wirklich löschen?"
                                    >
                                        Löschen
                                    </button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php if (can('books.manage')): ?>
        <aside class="stack">
            <section class="panel">
                <div class="panel-heading">
                    <div>
                        <p class="eyebrow">CRUD</p>
                        <h2>Neues Buch</h2>
                    </div>
                </div>
                <form method="post" action="<?= e(url('books/create')) ?>" class="stack-form">
                    <?= csrf_field() ?>
                    <label>ISBN<input name="isbn" required maxlength="20" placeholder="978..."></label>
                    <label>Titel<input name="title" required maxlength="180"></label>
                    <label>Autor<input name="author" required maxlength="140"></label>
                    <button class="button button-primary" type="submit">Buch speichern</button>
                </form>
            </section>

            <section class="panel">
                <div class="panel-heading">
                    <div>
                        <p class="eyebrow">XML</p>
                        <h2>Bestand importieren</h2>
                    </div>
                </div>
                <form
                    method="post"
                    action="<?= e(url('books/import')) ?>"
                    enctype="multipart/form-data"
                    class="stack-form"
                >
                    <?= csrf_field() ?>
                    <label>XML-Datei<input type="file" name="xml_file" accept=".xml,application/xml" required></label>
                    <small>Maximal 1 MB und 200 Bücher.</small>
                    <button class="button button-secondary" type="submit">XML importieren</button>
                </form>
            </section>
        </aside>
    <?php endif; ?>
</div>

<?php if (can('loans.manage')): ?>
    <div class="two-column">
        <section class="panel">
            <div class="panel-heading">
                <div>
                    <p class="eyebrow">Transaction</p>
                    <h2>Buch ausleihen</h2>
                </div>
            </div>
            <form method="post" action="<?= e(url('loans/checkout')) ?>" class="stack-form">
                <?= csrf_field() ?>
                <label>
                    Verfügbares Buch
                    <select name="book_id" required>
                        <option value="">Bitte wählen</option>
                        <?php foreach ($availableBooks as $book): ?>
                            <option value="<?= e($book['id']) ?>">
                                <?= e($book['title']) ?> · <?= e($book['isbn']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Ausgeliehen an<input name="borrower_name" required maxlength="140"></label>
                <label>Rückgabe bis<input type="date" name="due_at" min="<?= e(date('Y-m-d', strtotime('+1 day'))) ?>" required></label>
                <button class="button button-primary" type="submit">Ausleihe speichern</button>
            </form>
        </section>

        <section class="panel">
            <div class="panel-heading">
                <div>
                    <p class="eyebrow">Aktiv</p>
                    <h2>Offene Ausleihen</h2>
                </div>
            </div>
            <?php if (!$activeLoans): ?>
                <p class="muted">Keine offenen Ausleihen.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Buch</th><th>Person</th><th>Fällig</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($activeLoans as $loan): ?>
                            <tr>
                                <td><?= e($loan['title']) ?></td>
                                <td><?= e($loan['borrower_name']) ?></td>
                                <td><?= e($loan['due_at']) ?></td>
                                <td>
                                    <form method="post" action="<?= e(url('loans/return')) ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="loan_id" value="<?= e($loan['id']) ?>">
                                        <button class="button button-secondary button-small" type="submit">
                                            Rückgabe
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>
<?php endif; ?>

