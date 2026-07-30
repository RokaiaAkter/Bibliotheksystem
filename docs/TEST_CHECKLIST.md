# Manual Test Checklist

## Authentication

- [ ] ভুল password reject হয়
- [ ] correct demo login কাজ করে
- [ ] inactive user login করতে পারে না
- [ ] logout-এর পরে protected page login-এ পাঠায়

## Role/permission

- [ ] admin সব menu দেখে
- [ ] librarian Users, Phone, System menu দেখে না
- [ ] support বই দেখতে পারে, কিন্তু create/delete করতে পারে না
- [ ] direct URL দিয়েও forbidden action 403 দেয়

## Books ও loans

- [ ] book create হয়
- [ ] duplicate ISBN reject হয়
- [ ] normal search কাজ করে
- [ ] Ajax search page reload ছাড়া result দেয়
- [ ] available book loan করা যায়
- [ ] same book দ্বিতীয়বার loan করা যায় না
- [ ] return করলে book আবার available হয়
- [ ] loaned book delete করা যায় না

## XML

- [ ] example XML import হয়
- [ ] invalid root reject হয়
- [ ] DTD-containing XML reject হয়
- [ ] export valid XML download করে

## Users ও phones

- [ ] unique e-mail check হয়
- [ ] password minimum length check হয়
- [ ] admin নিজের account deactivate করতে পারে না
- [ ] duplicate telephone extension reject হয়

## Tickets ও logs

- [ ] external-provider ticket `waiting_provider` হয়
- [ ] support/admin status update করতে পারে
- [ ] action audit table-এ আসে
- [ ] error app log-এ আসে
- [ ] health endpoint HTTP 200 দেয়

## Security

- [ ] POST form CSRF token ছাড়া reject হয়
- [ ] HTML input text হিসেবে display হয়
- [ ] raw SQL concatenation নেই
- [ ] `storage`, `.env`, database files public URL দিয়ে খোলে না

