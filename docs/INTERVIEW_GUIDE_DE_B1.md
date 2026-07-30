# Interview-Erklärung auf Deutsch — B1

## 1. Projekt kurz vorstellen

> Mein Projekt heißt Bibliothksystem. Es ist eine kleine Webanwendung für eine
> Bibliothek. Ich habe PHP, MySQL, HTML, CSS, JavaScript, Ajax und XML benutzt.
> Die Anwendung läuft mit Apache und Docker. Es gibt Bücher, Ausleihen,
> Benutzerrechte, Telefon-Durchwahlen, Support-Tickets und System-Logs.

## 2. Architektur erklären

> Eine Anfrage kommt zuerst im Front Controller `public/index.php` an. Der
> Router wählt den passenden Controller. Der Controller prüft die Eingaben und
> die Berechtigung. Der Service enthält die Geschäftslogik. Das Repository
> arbeitet mit der MySQL-Datenbank. Am Ende bekommt der Browser HTML oder JSON.

## 3. Benutzer und Rechte

> Es gibt drei Rollen: Admin, Bibliothek und Support. Ein Admin darf Benutzer
> verwalten. Die Bibliotheksrolle verwaltet Bücher und Ausleihen. Der Support
> bearbeitet Tickets, Telefon-Durchwahlen und Logs. Ich benutze das
> Least-Privilege-Prinzip. Jede Person bekommt nur die notwendigen Rechte.

## 4. SQL Injection verhindern

> Ich benutze PDO und Prepared Statements. Benutzerwerte werden nicht direkt
> in den SQL-Text geschrieben. Zusätzlich validiere ich alle Eingaben.

## 5. XSS und CSRF

> Bei der Ausgabe maskiere ich Daten mit `htmlspecialchars`. Dadurch kann
> fremder HTML- oder JavaScript-Code nicht einfach ausgeführt werden. Bei
> POST-Formularen benutze ich außerdem ein CSRF-Token.

## 6. Ajax erklären

> Bei der Buchsuche sendet JavaScript mit `fetch` eine Anfrage an eine kleine
> API. Die API liest die Daten aus MySQL und antwortet mit JSON. JavaScript
> aktualisiert die Tabelle ohne einen kompletten Seiten-Reload.

## 7. Transaction erklären

> Bei einer Ausleihe werden zwei Dinge geändert: Ein Eintrag kommt in die
> Tabelle `loans`, und der Status des Buches wird `loaned`. Beide Änderungen
> laufen in einer Datenbank-Transaktion. Wenn ein Schritt fehlschlägt, wird
> alles zurückgerollt.

## 8. Application Update

> Vor einem Update prüfe ich die Dokumentation und erstelle ein Backup. Danach
> teste ich das Update in einer Testumgebung. Ich prüfe die Datenbank, die
> wichtigsten Funktionen und die Logs. Wenn es ein Problem gibt, nutze ich den
> Rollback-Plan. Danach dokumentiere ich das Ergebnis.

## 9. Troubleshooting

> Zuerst bestätige ich das Problem und sammle Informationen: Zeitpunkt,
> Fehlermeldung und betroffene Benutzer. Danach prüfe ich den Service, die
> Logs, Speicher, Festplatte und Datenbankverbindung. Ich teste eine sichere
> Lösung. Zum Schluss informiere ich die Benutzer und dokumentiere Ursache und
> Lösung.

## 10. Externer Dienstleister

> Wenn ich einen externen Dienstleister brauche, erstelle ich ein genaues
> Ticket. Ich schreibe die Uhrzeit, die Fehlermeldung, betroffene Systeme,
> Priorität und meine bisherigen Tests. Nach der Lösung teste ich die Funktion
> selbst und dokumentiere das Ergebnis.

## 11. Verbindung zur eigenen Erfahrung

> Bei Acuity Applied habe ich Webanwendungen entwickelt, Unit-Tests gemacht und
> technische Dokumentation geschrieben. Bei LESER habe ich mit TOPdesk,
> Baramundi, Rollouts und Benutzersupport gearbeitet. Dieses Projekt verbindet
> diese Erfahrung mit PHP, MySQL und Linux.

## 12. Was ist noch nicht Production-ready?

> Das Projekt ist bewusst klein und für das Lernen gebaut. Für Produktion
> würde ich HTTPS, Single Sign-on, zentrale Logs, Monitoring, Rate Limiting,
> sichere Secret-Verwaltung, automatisierte Tests und externe Backups
> ergänzen.

