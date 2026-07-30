# Linux Administration Practice

## Project status

```bash
docker compose ps
docker compose top
docker compose stats
```

## Service control

```bash
docker compose up -d
docker compose stop
docker compose start
docker compose restart app
```

## Logs

```bash
docker compose logs --tail=100 app
docker compose logs --tail=100 db
docker compose logs -f app
tail -f storage/logs/app.log
```

## CPU, RAM, disk, port

```bash
top
free -h
df -h
du -sh storage/*
ss -lntp
```

## File permissions

```bash
ls -la storage
namei -l storage/logs/app.log
```

নীতি:

- source code সাধারণত read-only;
- log/backup directory শুধু application user লিখতে পারবে;
- `.env` file public হবে না;
- database backup `chmod 600` হবে;
- application কখনও root user হিসেবে চালানো উচিত নয়।

## Apache

```bash
docker compose exec app apache2ctl -t
docker compose exec app apache2ctl -S
docker compose exec app tail -n 100 /var/log/apache2/bibliothksystem-error.log
```

## PHP

```bash
docker compose exec app php -v
docker compose exec app php -m
docker compose exec app php -l app/Controllers/BookController.php
```

## MySQL

```bash
docker compose exec db mysqladmin status -u"${DB_USERNAME}" -p
docker compose exec db mysql -u"${DB_USERNAME}" -p "${DB_DATABASE}"
```

MySQL shell-এর ভিতরে:

```sql
SHOW TABLES;
SELECT status, COUNT(*) FROM books GROUP BY status;
SELECT status, COUNT(*) FROM support_tickets GROUP BY status;
SHOW PROCESSLIST;
```

## Monitoring logic

Health timer example `ops/systemd/` folder-এ আছে। Production-এ এর সঙ্গে
Prometheus/Grafana অথবা central monitoring/alerting যোগ করা উচিত।

