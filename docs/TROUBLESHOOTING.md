# Troubleshooting Runbook

## Standard method

```text
Problem verstehen
→ Auswirkung und Priorität bestimmen
→ Evidenz sammeln
→ Hypothese bilden
→ sichere Änderung testen
→ Ergebnis prüfen
→ Benutzer informieren
→ dokumentieren
```

## 1. Application খোলে না

```bash
docker compose ps
docker compose logs --tail=100 app
curl -i http://localhost:8080/index.php?route=health
```

Check:

- app container running?
- port 8080 already used?
- Apache/PHP fatal error?
- `.env` আছে?

## 2. Database connection error

```bash
docker compose ps db
docker compose logs --tail=100 db
docker compose exec db mysqladmin ping -h localhost -u"${DB_USERNAME}" -p
```

তারপর `.env`-এর DB username, password এবং database name check করুন।

## 3. Login হচ্ছে না

1. correct demo e-mail ব্যবহার হয়েছে?
2. Caps Lock off?
3. account `active = 1`?
4. `users` table আছে?
5. app log-এ failed login আছে?

```bash
docker compose logs --tail=100 app
```

## 4. Ajax search কাজ করে না

- Browser DevTools → Network → `api/books`
- HTTP 401 হলে login session নেই
- HTTP 403 হলে role permission নেই
- HTTP 500 হলে app log দেখুন
- JavaScript বন্ধ থাকলেও normal search button কাজ করবে

## 5. XML import fail

- extension `.xml`?
- file 1 MB-এর কম?
- root element `<books>`?
- প্রতিটি book-এ `isbn`, `title`, `author`?
- DTD আছে? Security কারণে DTD allowed নয়।

## 6. Disk full

```bash
df -h
du -sh storage/logs storage/backups
docker system df
```

Log ও backup retention policy অনুযায়ী পুরোনো file archive/delete করুন। কোনো
destructive command চালানোর আগে exact target যাচাই করুন।

## 7. Safe restart

```bash
docker compose restart app
curl --fail http://localhost:8080/index.php?route=health
```

Database restart শুধু প্রয়োজন হলে এবং impact বুঝে করবেন।

## 8. Ticket-এ কী লিখবেন

- প্রথম সমস্যা দেখা দেওয়ার সময়
- exact error message
- কতজন user affected
- system এবং version
- reproduction steps
- relevant log excerpt
- ইতিমধ্যে কী check করেছেন
- priority ও business impact
- expected next action

## 9. Backup ও restore test

Backup:

```bash
./ops/backup.sh
```

Restore rehearsal production database-এ করবেন না। আলাদা test environment-এ:

```bash
CONFIRM_RESTORE=YES ./ops/restore.sh storage/backups/FILE.sql
```

Restore-এর পরে login, book search, loan transaction, ticket এবং row counts
test করুন।

