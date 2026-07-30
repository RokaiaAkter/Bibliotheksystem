<div class="two-column">
    <section class="panel">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">RBAC · Least Privilege</p>
                <h2>Benutzerkonten</h2>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Name</th><th>E-Mail</th><th>Rolle</th><th>Status</th><th>Aktion</th></tr></thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= e($user['name']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><span class="role-badge"><?= e($user['role']) ?></span></td>
                        <td>
                            <span class="status <?= $user['active'] ? 'status-available' : 'status-resolved' ?>">
                                <?= $user['active'] ? 'aktiv' : 'inaktiv' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ((int) $user['id'] !== \App\Core\Auth::id()): ?>
                                <form method="post" action="<?= e(url('users/toggle')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="user_id" value="<?= e($user['id']) ?>">
                                    <button class="button button-secondary button-small" type="submit">
                                        <?= $user['active'] ? 'Deaktivieren' : 'Aktivieren' ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <small>Eigenes Konto</small>
                            <?php endif; ?>
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
                <p class="eyebrow">Onboarding</p>
                <h2>Neuen Benutzer anlegen</h2>
            </div>
        </div>
        <form method="post" action="<?= e(url('users/create')) ?>" class="stack-form">
            <?= csrf_field() ?>
            <label>Name<input name="name" maxlength="120" required></label>
            <label>E-Mail<input type="email" name="email" maxlength="190" required></label>
            <label>Startpasswort<input type="password" name="password" minlength="10" required></label>
            <label>
                Rolle
                <select name="role" required>
                    <option value="librarian">Bibliothek</option>
                    <option value="support">IT-Support</option>
                    <option value="admin">Administration</option>
                </select>
            </label>
            <button class="button button-primary" type="submit">Konto anlegen</button>
        </form>
        <div class="note">
            <strong>Rechteprinzip:</strong>
            Eine Person bekommt nur die Rechte, die sie für ihre Arbeit wirklich braucht.
        </div>
    </section>
</div>

