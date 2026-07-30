<section class="stat-grid" aria-label="Systemübersicht">
    <article class="stat-card">
        <span>Bücher gesamt</span>
        <strong><?= e($bookCounts['total']) ?></strong>
        <small><?= e($bookCounts['available']) ?> verfügbar</small>
    </article>
    <article class="stat-card">
        <span>Aktive Ausleihen</span>
        <strong><?= e($activeLoans) ?></strong>
        <small><?= e($bookCounts['loaned']) ?> Bücher ausgeliehen</small>
    </article>
    <article class="stat-card">
        <span>Offene Tickets</span>
        <strong><?= e($ticketCounts['active']) ?></strong>
        <small><?= e($ticketCounts['waiting_provider']) ?> beim Dienstleister</small>
    </article>
    <article class="stat-card">
        <span>Aktive Konten</span>
        <strong><?= e($activeUsers) ?></strong>
        <small><?= e($activePhones) ?> aktive Durchwahlen</small>
    </article>
</section>

<section class="panel">
    <div class="panel-heading">
        <div>
            <p class="eyebrow">Request Flow</p>
            <h2>So arbeitet die Anwendung</h2>
        </div>
    </div>
    <ol class="flow-list">
        <li><strong>Browser</strong><span>sendet HTTP Request</span></li>
        <li><strong>Apache</strong><span>leitet an PHP weiter</span></li>
        <li><strong>Controller</strong><span>prüft Request &amp; Rechte</span></li>
        <li><strong>Service</strong><span>führt Business-Logik aus</span></li>
        <li><strong>Repository</strong><span>spricht mit MySQL</span></li>
        <li><strong>View / JSON</strong><span>sendet die Response</span></li>
    </ol>
</section>

<div class="two-column">
    <section class="panel">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">Job-Aufgaben</p>
                <h2>Was hier geübt wird</h2>
            </div>
        </div>
        <ul class="check-list">
            <li>Linux- und Web-Anwendungsbetrieb</li>
            <li>Benutzer, Rollen und Least Privilege</li>
            <li>MySQL, Backup und Datenpflege</li>
            <li>Telefon-Backend und Durchwahlen</li>
            <li>Support-Tickets mit externem Dienstleister</li>
            <li>Monitoring, Logs und Dokumentation</li>
        </ul>
    </section>

    <section class="panel">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">Audit Trail</p>
                <h2>Letzte Aktivitäten</h2>
            </div>
        </div>
        <?php if (!$recentAudits): ?>
            <p class="muted">Noch keine Aktivität vorhanden.</p>
        <?php else: ?>
            <ul class="activity-list">
                <?php foreach ($recentAudits as $audit): ?>
                    <li>
                        <strong><?= e($audit['action']) ?> · <?= e($audit['entity_type']) ?></strong>
                        <span><?= e($audit['user_name'] ?? 'System') ?> · <?= e($audit['created_at']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>

