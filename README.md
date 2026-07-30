# Bibliothksystem

`Bibliothksystem` হলো একটি ছোট **PHP 8 + MySQL 8 + Apache + Docker** learning
project। এটি Staats- und Universitätsbibliothek Hamburg-এর
System- und Anwendungsadministration ধরনের কাজ মাথায় রেখে বানানো।

## Project-এ কী কী আছে

- Login, logout এবং secure session
- তিনটি role: `admin`, `librarian`, `support`
- Role-Based Access Control (RBAC) ও Least Privilege
- বই তৈরি, খোঁজা ও delete করা
- বই loan/return এবং database transaction
- JavaScript `fetch()` দিয়ে Ajax search
- XML import ও export
- MySQL database, indexes, foreign keys ও prepared statements
- Telephone number/extension backend
- Internal ও external-provider support ticket
- Application health, file log ও database audit log
- CSRF, XSS ও SQL Injection-এর basic protection
- Linux/Docker health check, backup, restore ও update scripts
- Bangla architecture guide এবং German B1 interview answers

## সবচেয়ে সহজভাবে চালু করা

### আগে যা লাগবে

Windows-এ [Docker Desktop](https://www.docker.com/products/docker-desktop/)
install এবং চালু থাকতে হবে।

### PowerShell commands

```powershell
cd "C:\path\to\Bibliothksystem"
Copy-Item .env.example .env
docker compose up -d --build
docker compose ps
```

Browser-এ খুলুন:

- Application: `http://localhost:8080`
- Adminer database UI: `http://localhost:8081`

Adminer login:

- System: `MySQL`
- Server: `db`
- Username: `.env`-এর `DB_USERNAME`
- Password: `.env`-এর `DB_PASSWORD`
- Database: `bibliothksystem`

## Demo login

| Role | E-mail | Password | কী করতে পারবে |
|---|---|---|---|
| Admin | `admin@bibliothek.local` | `Admin123!` | সব module |
| Librarian | `staff@bibliothek.local` | `Staff123!` | বই, loan, ticket |
| Support | `support@bibliothek.local` | `Support123!` | phone, ticket, system/log |

এগুলো শুধু local demo password। Production-এ অবশ্যই বদলাতে হবে।

## Application বন্ধ/চালু

```powershell
docker compose stop
docker compose start
```

Logs:

```powershell
docker compose logs --tail=100 app db
docker compose logs -f app
```

Database connection পরীক্ষা:

```powershell
docker compose exec db mysqladmin ping -h localhost -ubibliothek -p
```

## Request কোন file হয়ে যায়

```text
Browser
  → public/index.php
  → Router
  → Controller
  → Service
  → Repository
  → MySQL
  → View অথবা JSON
  → Browser
```

উদাহরণ: একটি বই loan করলে

```text
views/books.php
  → POST route loans/checkout
  → LoanController.php
  → LoanService.php
  → LoanRepository.php + BookRepository.php
  → loans এবং books table
```

## গুরুত্বপূর্ণ folder

| Folder/File | কাজ |
|---|---|
| `public/` | Browser-এর জন্য একমাত্র public document root |
| `public/index.php` | সব route-এর entry point |
| `app/Controllers/` | Request গ্রহণ, permission check, response |
| `app/Services/` | Business rules ও transaction |
| `app/Repositories/` | Prepared SQL ও MySQL access |
| `views/` | HTML pages |
| `database/init/` | Tables, indexes ও demo data |
| `storage/logs/` | Application log |
| `ops/` | Backup, restore, update ও health scripts |
| `docs/` | শেখা ও interview documentation |

## XML পরীক্ষা

`database/books-example.xml` file-টি **Bücher & Ausleihe → XML importieren**
থেকে upload করুন। Export button দিয়ে current book list XML হিসেবে download
করা যায়।

## Database একদম নতুন করে তৈরি

নিচের command database volume এবং সব local database data delete করে। কেবল
demo data reset করতে চাইলে ব্যবহার করবেন:

```powershell
docker compose down -v
docker compose up -d --build
```

## পড়ার সঠিক order

1. `docs/JOB_SKILL_MAPPING.md`
2. `docs/ARCHITECTURE.md`
3. `public/index.php`
4. একটি Controller, যেমন `BookController.php`
5. `BookService.php`
6. `BookRepository.php`
7. `database/init/001_schema.sql`
8. `docs/TROUBLESHOOTING.md`
9. `docs/INTERVIEW_GUIDE_DE_B1.md`

## Production-এর আগে যা আরও লাগবে

এটি একটি learning project। Real production-এর জন্য secret manager,
HTTPS reverse proxy, central identity provider/SSO, automated migrations,
central monitoring, rate limiting, tested restore procedure, off-site backup
ও independent security review যোগ করতে হবে।

