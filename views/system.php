<section class="stat-grid">
    <?php foreach ($health as $label => $value): ?>
        <article class="stat-card stat-card-compact">
            <span><?= e(str_replace('_', ' ', $label)) ?></span>
            <strong><?= e($value) ?></strong>
        </article>
    <?php endforeach; ?>
</section>

<div class="two-column">
    <section class="panel">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">Application Monitoring</p>
                <h2>Letzte Log-Einträge</h2>
            </div>
        </div>
        <?php if (!$logLines): ?>
            <p class="muted">Noch keine Log-Einträge vorhanden.</p>
        <?php else: ?>
            <pre class="log-viewer"><?php foreach ($logLines as $line): ?><?= e($line) . PHP_EOL ?><?php endforeach; ?></pre>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">Database Audit</p>
                <h2>Nachvollziehbare Änderungen</h2>
            </div>
        </div>
        <?php if (!$recentAudits): ?>
            <p class="muted">Noch keine Audit-Einträge vorhanden.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Zeit</th><th>Benutzer</th><th>Aktion</th><th>Objekt</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentAudits as $audit): ?>
                        <tr>
                            <td><?= e($audit['created_at']) ?></td>
                            <td><?= e($audit['user_name'] ?? 'System') ?></td>
                            <td><?= e($audit['action']) ?></td>
                            <td><?= e($audit['entity_type']) ?> #<?= e($audit['entity_id'] ?? '–') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>

<section class="panel">
    <div class="panel-heading">
        <div>
            <p class="eyebrow">Runbook</p>
            <h2>Wenn die Anwendung nicht erreichbar ist</h2>
        </div>
    </div>
    <ol class="runbook">
        <li><strong>Problem bestätigen:</strong> URL, Uhrzeit, Nutzerkreis und Fehlermeldung erfassen.</li>
        <li><strong>Container prüfen:</strong> <code>docker compose ps</code></li>
        <li><strong>Logs prüfen:</strong> <code>docker compose logs --tail=100 app db</code></li>
        <li><strong>Ressourcen prüfen:</strong> Disk, Memory, DB-Verbindung und Health Endpoint.</li>
        <li><strong>Sichere Lösung testen:</strong> Erst Backup, dann Änderung; Rollback vorbereiten.</li>
        <li><strong>Dokumentieren:</strong> Ursache, Lösung, Test und Kommunikation festhalten.</li>
    </ol>
</section>

