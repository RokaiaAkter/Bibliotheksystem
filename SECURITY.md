# Security Notes

This repository is a local learning application, not a production-certified
library platform.

Implemented basics:

- PDO prepared statements
- output escaping
- CSRF tokens on state-changing forms
- password hashing with PHP `password_hash`
- strict, HTTP-only, SameSite session cookies
- role-based access checks on controllers
- audit logging
- XML size limit, DTD rejection and network-disabled parsing
- non-public application, database and storage directories
- basic Apache security headers

Before production:

- replace demo credentials and rotate all database secrets;
- use HTTPS and secure cookies behind a hardened reverse proxy;
- integrate SSO/MFA and a real identity lifecycle;
- implement rate limiting and login lockout;
- use central secret management, logs, metrics and alerting;
- run dependency, SAST, DAST and penetration tests;
- automate database migrations and backup/restore tests;
- apply retention and privacy rules for logs and personal data;
- separate development, test and production environments.

Please do not commit `.env`, database dumps or real personal data.

