<section class="error-card">
    <span class="error-code"><?= e($code ?? 500) ?></span>
    <p class="eyebrow">Bibliothksystem</p>
    <h1><?= e($title ?? 'Fehler') ?></h1>
    <p><?= e($message ?? 'Ein Fehler ist aufgetreten.') ?></p>
    <a class="button button-primary" href="<?= e(\App\Core\Auth::check() ? url('dashboard') : url('login')) ?>">
        Zurück
    </a>
</section>
