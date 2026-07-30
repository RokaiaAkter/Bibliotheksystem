# Job Skill → Project Feature Mapping

## কোন বিষয় জানলে কোন job duty করা যায়

| Technical skill | Project-এ কোথায় | Job-এ কোন কাজ |
|---|---|---|
| Linux Server | Docker, Apache config, `ops/*.sh` | Application চালু রাখা, service/log/disk check |
| Web Application Administration | health page, backup/update runbook | Configure, update, test, rollback |
| HTML + CSS | `views/`, `public/assets/css` | Accessible ও responsive frontend |
| PHP | Controller, Service, Repository | Backend logic ও maintenance |
| JavaScript + Ajax | `public/assets/js/app.js` | Page reload ছাড়া search |
| XML | book import/export | Structured data exchange/configuration |
| MySQL | schema, indexes, prepared query | Library data, user, loan, ticket manage |
| Open Source | PHP, Apache, MySQL, Docker, MIT license | Documentation/community-based software |
| User/Role/Permission | `Auth.php`, Users page | Account, role, access, offboarding |
| Telephone Backend | Phones page | User, extension, activate/deactivate |
| External Provider | Support ticket checkbox/status | Clear ticket ও provider coordination |
| Troubleshooting | System page, log, health endpoint | Cause খোঁজা, test, rollback |
| Documentation | `README.md`, `docs/` | Runbook ও colleague instruction |

## পাঁচটি মূল দায়িত্বের সঙ্গে সম্পর্ক

### 1. Library application চালু ও নিরাপদ রাখা

Project practice:

- `docker compose ps`
- `docker compose logs`
- health endpoint
- application এবং database পৃথক service
- Apache security headers
- backup এবং update script

### 2. User, Role ও Permission

Project practice:

- admin নতুন user তৈরি করে;
- librarian ও support-এর permission আলাদা;
- নিজের admin account ভুল করে deactivate করা যায় না;
- inactive user login করতে পারে না;
- change audit log-এ থাকে।

### 3. Telephone Backend

Project practice:

- employee, department ও extension তৈরি;
- duplicate extension reject;
- extension activate/deactivate;
- কে change করেছে audit log-এ থাকে।

### 4. External IT Service Provider

Project practice:

- ticket title, detailed description ও priority;
- `external_provider` select করলে status `waiting_provider`;
- support/admin status update করে;
- time ও responsible user সংরক্ষণ হয়।

### 5. Documentation

Project practice:

- installation guide;
- architecture/file map;
- troubleshooting runbook;
- backup/restore steps;
- test checklist;
- B1 German interview explanation।

## Interview-তে নিজের experience-এর সঙ্গে বলবেন

> Bei Acuity Applied habe ich Webanwendungen weiterentwickelt, getestet und
> dokumentiert. In diesem Lernprojekt habe ich diese Erfahrung mit PHP, MySQL
> und Linux verbunden. Aus meiner Arbeit bei LESER kenne ich außerdem
> Benutzer-Support, TOPdesk, Software-Rollouts und strukturierte
> Dokumentation.

