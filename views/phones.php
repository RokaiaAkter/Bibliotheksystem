<div class="two-column">
    <section class="panel">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">PBX / VoIP Simulation</p>
                <h2>Durchwahlen verwalten</h2>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Mitarbeiter:in</th><th>Abteilung</th><th>Durchwahl</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($phones as $phone): ?>
                    <tr>
                        <td><?= e($phone['employee_name']) ?></td>
                        <td><?= e($phone['department']) ?></td>
                        <td><code><?= e($phone['extension']) ?></code></td>
                        <td>
                            <span class="status <?= $phone['active'] ? 'status-available' : 'status-resolved' ?>">
                                <?= $phone['active'] ? 'aktiv' : 'inaktiv' ?>
                            </span>
                        </td>
                        <td>
                            <form method="post" action="<?= e(url('phones/toggle')) ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="phone_id" value="<?= e($phone['id']) ?>">
                                <button class="button button-secondary button-small" type="submit">
                                    <?= $phone['active'] ? 'Deaktivieren' : 'Aktivieren' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">Backend-Konfiguration</p>
                <h2>Neue Durchwahl</h2>
            </div>
        </div>
        <form method="post" action="<?= e(url('phones/create')) ?>" class="stack-form">
            <?= csrf_field() ?>
            <label>Mitarbeiter:in<input name="employee_name" maxlength="120" required></label>
            <label>Abteilung<input name="department" maxlength="120" required></label>
            <label>Durchwahl<input name="extension" pattern="[0-9]{3,8}" maxlength="8" required></label>
            <button class="button button-primary" type="submit">Durchwahl speichern</button>
        </form>
        <div class="note">
            In einem echten System würde diese Änderung zusätzlich über eine
            abgesicherte API an die Telefonanlage übertragen.
        </div>
    </section>
</div>

