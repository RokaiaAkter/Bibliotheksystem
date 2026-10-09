# Bibliothksystem – Library Management System

A web-based **Library Management and Administration System** developed using **PHP 8, MySQL 8, Apache, and Docker**.

This project was created as a practical learning project to strengthen skills in **software development, database management, system administration, application security, and IT operations**.

It was inspired by typical System and Application Administration responsibilities in university libraries, particularly the Staats- und Universitätsbibliothek Hamburg.

## 1. Project Overview

Bibliothksystem provides essential library management functionality, including book management, borrowing and returning books, user authentication, role-based permissions, and administrative support tools.

The project follows a layered architecture to separate request handling, business logic, and database operations.

**Technology Stack**

- **Backend:** PHP 8
- **Database:** MySQL 8
- **Web Server:** Apache
- **Frontend:** HTML, CSS, JavaScript, AJAX
- **Infrastructure:** Docker, Docker Compose
- **Data Exchange:** XML
- **Database Administration:** Adminer
- **Architecture:** Controller–Service–Repository Pattern

## 2. Key Features

### Authentication and Authorization
- Secure login and logout functionality
- Session management
- Three user roles: `admin`, `librarian`, and `support`
- Role-Based Access Control (RBAC)
- Least Privilege access principles

### Library Management
- Add, search, and delete books
- Manage book loans and returns
- Database transactions for borrowing operations
- Dynamic book search using JavaScript `fetch()` and AJAX
- Import and export book data using XML

### Database Management
- MySQL database integration
- Relational database design
- Primary and foreign keys
- Database indexes
- Prepared SQL statements
- Transaction-based database operations

### IT Support and Administration
- Telephone number and extension management
- Internal IT support tickets
- External-provider support tickets
- Application health monitoring
- File-based application logging
- Database audit logging

### Security
- Role-based permission checks
- Basic Cross-Site Request Forgery (CSRF) protection
- Cross-Site Scripting (XSS) protection
- SQL injection prevention using prepared statements
- Secure session handling

### DevOps and System Operations
- Docker-based development environment
- Linux and Docker health-check scripts
- Database backup and restore scripts
- Application update scripts
- Troubleshooting documentation

## 3. Getting Started

### Prerequisites

Before running this project, make sure the following software is installed:

- Docker Desktop
- Docker Compose (included with Docker Desktop)
- PowerShell (Windows)
- A modern web browser

Download Docker Desktop:

https://www.docker.com/products/docker-desktop/

### Installation

**Step 1: Navigate to the project directory**

```powershell
cd "C:\path\to\Bibliothksystem"
```

**Step 2: Create the environment configuration file**

```powershell
Copy-Item .env.example .env
```

Configure the database credentials in the `.env` file as required.

**Step 3: Build and start the Docker containers**

```powershell
docker compose up -d --build
```

**Step 4: Verify the running containers**

```powershell
docker compose ps
```

### Access the Application

After successful installation, open the following URLs:

| Service | URL |
|---|---|
| Library Management Application | http://localhost:8080 |
| Adminer Database Interface | http://localhost:8081 |

### Adminer Database Login

Use the following connection settings:

| Setting | Value |
|---|---|
| System | MySQL |
| Server | `db` |
| Username | Value of `DB_USERNAME` in `.env` |
| Password | Value of `DB_PASSWORD` in `.env` |
| Database | `bibliothksystem` |

## 4. Demo Authentication

The application supports three user roles:

- **Admin:** Administrative access based on assigned permissions
- **Librarian:** Library management operations based on assigned permissions
- **Support:** IT support operations based on assigned permissions

Demo account credentials are intended for local testing only.

**Security Note:** All demo passwords and default credentials must be replaced before any production deployment.

## 5. Docker Management

**Stop the application**

```powershell
docker compose stop
```

**Restart the application**

```powershell
docker compose start
```

**View application and database logs**

```powershell
docker compose logs --tail=100 app db
```

**Monitor application logs in real time**

```powershell
docker compose logs -f app
```

**Check MySQL database connectivity**

```powershell
docker compose exec db mysqladmin ping -h localhost -ubibliothek -p
```

The MySQL command prompts for the configured database password.

## 6. Application Architecture

The application follows a layered architecture based on the Controller–Service–Repository pattern.

### Request Processing Flow

```text
Browser
   |
   v
public/index.php
   |
   v
Router
   |
   v
Controller
   |
   v
Service
   |
   v
Repository
   |
   v
MySQL Database
   |
   v
HTML View / JSON Response
   |
   v
Browser
```

### Example: Book Checkout Process

When a librarian checks out a book, the request follows this workflow:

```text
views/books.php
   |
   v
POST /loans/checkout
   |
   v
LoanController.php
   |
   v
LoanService.php
   |
   v
LoanRepository.php
   +
BookRepository.php
   |
   v
MySQL Database
   |
   +-- loans table
   |
   +-- books table
```

The service layer coordinates the borrowing process and database transaction to maintain data consistency.

## 7. Project Structure

| Directory / File | Description |
|---|---|
| `public/` | Public web document root |
| `public/index.php` | Main application entry point and route handling |
| `app/Controllers/` | HTTP request processing, authorization, and responses |
| `app/Services/` | Business logic and transaction management |
| `app/Repositories/` | Database access and prepared SQL queries |
| `views/` | HTML templates and user interface |
| `database/init/` | Database schema, indexes, and demonstration data |
| `storage/logs/` | Application log files |
| `ops/` | Backup, restore, update, and health-check scripts |
| `docs/` | Architecture, troubleshooting, learning, and interview documentation |

## 8. XML Import and Export

The application supports XML-based book data exchange.

### Import Book Data

1. Open the application.
2. Navigate to **Bücher & Ausleihe**.
3. Select **XML importieren**.
4. Upload the sample file:

   `database/books-example.xml`

### Export Book Data

Use the XML export functionality to download the current book catalog as an XML file.

## 9. Database Reset

To completely recreate the local database, execute:

```powershell
docker compose down -v
docker compose up -d --build
```

**Warning:** The `docker compose down -v` command removes Docker Compose-managed volumes, including the local database volume. All data stored in those volumes will be permanently deleted.

Use this procedure only when resetting the demonstration environment.

## 10. Recommended Learning Path

For developers who want to understand the project architecture and implementation, the following reading order is recommended:

1. `docs/JOB_SKILL_MAPPING.md`
2. `docs/ARCHITECTURE.md`
3. `public/index.php`
4. `app/Controllers/BookController.php`
5. `app/Services/BookService.php`
6. `app/Repositories/BookRepository.php`
7. `database/init/001_schema.sql`
8. `docs/TROUBLESHOOTING.md`
9. `docs/INTERVIEW_GUIDE_DE_B1.md`

The documentation includes architecture explanations, IT administration concepts, troubleshooting guidance, and German-language interview preparation materials.

## 11. Production Considerations

**This project is designed for learning and demonstration purposes and is not production-ready.**

A production deployment would require additional security, operational, and infrastructure improvements, including:

- HTTPS configuration and a secure reverse proxy
- Centralized identity management and Single Sign-On (SSO)
- Secure secrets management
- Automated database migrations
- Centralized monitoring and alerting
- Rate limiting and additional security controls
- Automated and off-site database backups
- Tested backup restoration procedures
- Independent security review

## 12. Project Objectives

The main objectives of this project are to:

- Develop practical backend development skills using PHP and MySQL.
- Understand relational database design and transaction management.
- Implement authentication and role-based authorization.
- Gain experience with Docker-based application deployment.
- Practice application monitoring, logging, backup, and recovery.
- Understand the separation of concerns in layered software architecture.
- Strengthen technical knowledge relevant to software development and system/application administration roles.

## 13. Project Status

**Learning and demonstration project**

Developed to demonstrate practical knowledge of backend engineering, database administration, application security, and IT operations.

---

**Tech Stack:** PHP 8 | MySQL 8 | Apache | JavaScript | AJAX | XML | Docker | RBAC | REST-style routing

**Purpose:** Software Development · Library Management · System Administration · Application Administration
