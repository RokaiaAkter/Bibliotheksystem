# Architecture — সহজ বাংলা ব্যাখ্যা

## 1. পুরো request flow

```text
Browser
   |
   v
Apache Web Server
   |
   v
public/index.php  ← Front Controller
   |
   v
Router            ← URL দেখে Controller নির্বাচন
   |
   v
Controller        ← input, login, role/permission check
   |
   v
Service           ← business rule এবং transaction
   |
   v
Repository        ← prepared SQL
   |
   v
MySQL Database
   |
   v
View (HTML) অথবা API (JSON)
```

## 2. কেন layer আলাদা

### Controller

Controller-এর কাজ HTTP request handle করা। যেমন `BookController` form থেকে
ISBN, title ও author নেয়। Controller নিজে বড় business logic বা raw SQL রাখে
না।

### Service

Service check করে কাজটি নিয়ম অনুযায়ী করা যাবে কি না। যেমন:

- loan-এর due date future হতে হবে;
- বই available হতে হবে;
- book ও loan update একই transaction-এ হবে;
- XML import-এ সর্বোচ্চ 200 book থাকবে।

### Repository

Repository database query রাখে। সব user input prepared statement দিয়ে
database-এ পাঠানো হয়েছে। এতে SQL Injection risk কমে।

### View

View HTML তৈরি করে। Database থেকে আসা text `e()` helper দিয়ে escape করা হয়।
এতে basic XSS protection পাওয়া যায়।

## 3. Dependency Injection-এর example

`LoanService`-এর constructor-এ PDO database dependency দেওয়া হয়:

```php
$service = new LoanService($database);
```

Service নিজে নতুন database connection তৈরি করে না। একই connection
`BookRepository` এবং `LoanRepository` ব্যবহার করে। তাই একটি transaction-এর
ভিতরে দুই table নিরাপদভাবে update করা যায়।

সহজ German:

> Der LoanService bekommt die Datenbankverbindung von außen. Dadurch kann ich
> dieselbe Verbindung in mehreren Repositories nutzen und die Klassen besser
> testen.

## 4. Role ও Permission

| Role | Permission |
|---|---|
| `admin` | সব page, user, phone, ticket, system |
| `librarian` | book, loan, ticket create |
| `support` | book view, phone, ticket, health/log |

এখানে **Least Privilege** ব্যবহার হয়েছে: কাজের জন্য যতটুকু access দরকার,
শুধু ততটুকু দেওয়া হয়।

## 5. Database relation

```text
users 1 ─── many loans
books 1 ─── many loans
users 1 ─── many support_tickets
users 1 ─── many audit_logs
```

`telephone_extensions` আলাদা configuration table।

## 6. Ajax

`public/assets/js/app.js` search input-এর value নিয়ে:

```text
GET index.php?route=api/books&q=Kafka
```

request পাঠায়। `ApiController` JSON response দেয়। Page reload ছাড়া result
table update হয়।

## 7. XML

- Export: MySQL → Repository → Controller → XML file
- Import: XML file → validation → Service → Repository → MySQL

Security-এর জন্য maximum file size, `.xml` extension, DTD block এবং
`LIBXML_NONET` ব্যবহার করা হয়েছে।

## 8. Logging ও Monitoring

দুই ধরনের log:

1. `storage/logs/app.log` — error, login, system event
2. `audit_logs` table — কে কোন data change করেছে

`System & Logs` page-এ database, PHP version, disk space এবং recent logs দেখা
যায়। Public health endpoint:

```text
http://localhost:8080/index.php?route=health
```

