<div class="two-column">
    <section class="panel">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">2nd / 3rd Level Support</p>
                <h2>Tickets</h2>
            </div>
        </div>

        <div class="ticket-list">
            <?php foreach ($tickets as $ticket): ?>
                <article class="ticket">
                    <div class="ticket-topline">
                        <span class="priority priority-<?= e($ticket['priority']) ?>">
                            <?= e($ticket['priority']) ?>
                        </span>
                        <span class="status status-<?= e($ticket['status']) ?>">
                            <?= e($ticket['status']) ?>
                        </span>
                        <?php if ($ticket['external_provider']): ?>
                            <span class="provider-badge">Externer Dienstleister</span>
                        <?php endif; ?>
                    </div>
                    <h3>#<?= e($ticket['id']) ?> · <?= e($ticket['title']) ?></h3>
                    <p><?= nl2br(e($ticket['description'])) ?></p>
                    <small>
                        <?= e($ticket['created_by_name'] ?? 'System') ?> ·
                        <?= e($ticket['created_at']) ?>
                    </small>

                    <?php if (can('tickets.manage')): ?>
                        <form method="post" action="<?= e(url('tickets/status')) ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="ticket_id" value="<?= e($ticket['id']) ?>">
                            <label>
                                <span class="sr-only">Status</span>
                                <select name="status">
                                    <?php foreach (['open', 'in_progress', 'waiting_provider', 'resolved'] as $status): ?>
                                        <option value="<?= e($status) ?>" <?= $ticket['status'] === $status ? 'selected' : '' ?>>
                                            <?= e($status) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <button class="button button-secondary button-small" type="submit">Aktualisieren</button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">Problem → Evidenz → Lösung</p>
                <h2>Neues Ticket</h2>
            </div>
        </div>
        <form method="post" action="<?= e(url('tickets/create')) ?>" class="stack-form">
            <?= csrf_field() ?>
            <label>Titel<input name="title" maxlength="180" required></label>
            <label>
                Beschreibung
                <textarea
                    name="description"
                    rows="7"
                    maxlength="4000"
                    required
                    placeholder="Zeit, Fehlermeldung, betroffene Nutzer, bereits geprüfte Schritte ..."
                ></textarea>
            </label>
            <label>
                Priorität
                <select name="priority">
                    <option value="low">low</option>
                    <option value="medium" selected>medium</option>
                    <option value="high">high</option>
                    <option value="critical">critical</option>
                </select>
            </label>
            <label class="checkbox">
                <input type="checkbox" name="external_provider" value="1">
                Externer Dienstleister ist erforderlich
            </label>
            <button class="button button-primary" type="submit">Ticket anlegen</button>
        </form>
    </section>
</div>

