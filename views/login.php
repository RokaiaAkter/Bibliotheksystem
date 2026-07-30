<section class="login-card">
    <div class="login-intro">
        <span class="brand-mark brand-mark-large" aria-hidden="true">B</span>
        <p class="eyebrow">PHP · MySQL · Linux</p>
        <h1>Bibliothksystem</h1>
        <p>
            Ein kleines Lernprojekt für die Administration von
            Bibliotheksanwendungen.
        </p>
    </div>

    <form method="post" action="<?= e(url('login')) ?>" class="stack-form">
        <?= csrf_field() ?>
        <label>
            E-Mail-Adresse
            <input type="email" name="email" autocomplete="username" required>
        </label>
        <label>
            Passwort
            <input type="password" name="password" autocomplete="current-password" required>
        </label>
        <button class="button button-primary" type="submit">Sicher anmelden</button>
    </form>

    <details class="demo-accounts">
        <summary>Demo-Zugänge anzeigen</summary>
        <dl>
            <div><dt>Admin</dt><dd>admin@bibliothek.local / Admin123!</dd></div>
            <div><dt>Bibliothek</dt><dd>staff@bibliothek.local / Staff123!</dd></div>
            <div><dt>Support</dt><dd>support@bibliothek.local / Support123!</dd></div>
        </dl>
        <p><strong>Nur für die Demo:</strong> In Produktion müssen alle Passwörter geändert werden.</p>
    </details>
</section>

