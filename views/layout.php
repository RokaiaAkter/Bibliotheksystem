<?php

use App\Core\Auth;
use App\Core\Config;
use App\Core\Csrf;

$currentUser = Auth::user();
$successMessage = flash('success');
$errorMessage = flash('error');
$pageTitle = isset($title) ? (string) $title : 'Bibliothksystem';
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
    <title><?= e($pageTitle) ?> · <?= e(Config::get('APP_NAME', 'Bibliothksystem')) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="/assets/js/app.js" defer></script>
</head>
<body class="<?= $currentUser ? 'app-shell' : 'guest-shell' ?>">
<?php if ($currentUser): ?>
    <aside class="sidebar" aria-label="Hauptnavigation">
        <a class="brand" href="<?= e(url('dashboard')) ?>">
            <span class="brand-mark" aria-hidden="true">B</span>
            <span>
                <strong>Bibliothksystem</strong>
                <small>SUB Lernprojekt</small>
            </span>
        </a>

        <nav>
            <a href="<?= e(url('dashboard')) ?>">Dashboard</a>
            <?php if (can('books.view')): ?>
                <a href="<?= e(url('books')) ?>">Bücher &amp; Ausleihe</a>
            <?php endif; ?>
            <?php if (can('users.manage')): ?>
                <a href="<?= e(url('users')) ?>">Benutzer &amp; Rollen</a>
            <?php endif; ?>
            <?php if (can('phones.manage')): ?>
                <a href="<?= e(url('phones')) ?>">Telefon-Backend</a>
            <?php endif; ?>
            <?php if (can('tickets.view')): ?>
                <a href="<?= e(url('tickets')) ?>">Support-Tickets</a>
            <?php endif; ?>
            <?php if (can('system.view')): ?>
                <a href="<?= e(url('system')) ?>">System &amp; Logs</a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-user">
            <strong><?= e($currentUser['name']) ?></strong>
            <small><?= e($currentUser['role']) ?> · <?= e($currentUser['email']) ?></small>
            <form method="post" action="<?= e(url('logout')) ?>">
                <?= csrf_field() ?>
                <button class="button button-ghost button-small" type="submit">Abmelden</button>
            </form>
        </div>
    </aside>
<?php endif; ?>

<main class="<?= $currentUser ? 'main-content' : 'guest-content' ?>">
    <?php if ($currentUser): ?>
        <header class="page-header">
            <div>
                <p class="eyebrow">Bibliotheksadministration</p>
                <h1><?= e($pageTitle) ?></h1>
            </div>
            <span class="environment-badge"><?= e(Config::get('APP_ENV', 'development')) ?></span>
        </header>
    <?php endif; ?>

    <?php if ($successMessage): ?>
        <div class="alert alert-success" role="status"><?= e($successMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="alert alert-error" role="alert"><?= e($errorMessage) ?></div>
    <?php endif; ?>

    <?= $content ?>
</main>
</body>
</html>

