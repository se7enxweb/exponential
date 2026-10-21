---
title: Datenbank nicht erreichbar
audience: government, admin, hosting
category: public-sector
order: 40
summary: Runbook für „We'll be right back“-Seiten, weil Exponential seine Datenbank nicht erreicht - Server, Zugangsdaten, Verbindungen und Festplatte je Engine prüfen, auf einen Standby-Server umschalten, falls Sie einen betreiben, und mit geleerten Caches wiederherstellen.
keywords: Datenbank ausgefallen, Datenbank nicht erreichbar, 503, MySQL, MariaDB, PostgreSQL, SQLite, Datenbank belegt, Verbindung abgelehnt, Zugangsdaten, Standby, Failover, Runbook, database down, database unreachable, We'll be right back, database is busy, connection refused, credentials, standby, failover
type: help
---
# Datenbank nicht erreichbar

Dieses Runbook richtet sich an die Administration, wenn Besucher „We'll be right back“ (503) sehen, weil Exponential
seine Datenbank nicht erreicht. Exponential hat kein automatisches Datenbank-Failover; der Datenbankserver und seine
Replikation werden mit den Werkzeugen des Herstellers betrieben
([Hochverfügbarkeit der Datenbank](../verfuegbarkeit/hochverfuegbarkeit-der-datenbank.md)).

## Inhalt
1. [Bestätigen](#bestätigen)
2. [Welche Datenbank?](#welche-datenbank)
3. [Server-Datenbanken](#server-datenbanken)
4. [SQLite](#sqlite)
5. [Wiederherstellen](#wiederherstellen)
6. [Verwandte Seiten](#verwandte-seiten)

## Bestätigen

```bash
./console exp:logs --tail=var/log/error.log --lines=30
./console exp:logs --search='database' --scope=kernel --level=error
```

Die Logzeile enthält die Meldung des Treibers (connection refused, access denied, too many connections, database is
busy). Die Startseite wird vielleicht noch aus einem Cache ausgeliefert, während die Administration scheitert; prüfen
Sie beides.

## Welche Datenbank?

```bash
grep -rn "DatabaseImplementation" settings/override settings/siteaccess/*/site.ini.append.php settings/site.ini
```

Werte: `ezmysqli` (MySQL, MariaDB), `ezpostgresql` oder `pgsql` (PostgreSQL), `sqlite3`, `mongodb`; Oracle kommt mit
seiner Erweiterung ([Betrieb einer Website, Abschnitt 7](../../../../anleitungen/betrieb-einer-website.md#7-den-eigenen-datenbanktreiber-kennen)).

## Server-Datenbanken

1. Läuft der Server, und ist er vom Webserver aus erreichbar? Verwenden Sie den Client des Herstellers mit Host, Port
   und Benutzer aus `[DatabaseSettings]` in `settings/override/site.ini.append.php` (der Client fragt nach dem Passwort).
2. **Access denied**: Das Passwort wurde geändert oder der Benutzer entfernt; korrigieren Sie es in der Datenbank oder
   in den Einstellungen, leeren Sie dann den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`)
   und laden Sie PHP neu.
3. **Too many connections**: Prüfen Sie das Verbindungslimit des Servers und die persistenten Verbindungen
   (`UsePersistentConnection`); unter Velocity hält jeder Worker seine Verbindung.
4. **Festplatte auf dem Datenbankserver voll**: Schaffen Sie gemeinsam mit dem Datenbankteam Platz.
5. **Es gibt einen Standby-Server**: Wenn der Primärserver nicht bald zurückkommt, stufen Sie den Standby-Server mit den
   Werkzeugen des Herstellers hoch und richten Sie Exponential darauf aus
   ([Auf eine Standby-Datenbank umschalten](../../../../../eng-us/help/government/tasks/operations/fail-over-to-a-standby-database.md)).

`ConnectRetries` (Standard `0`) lässt den Treiber nach einer gescheiterten ersten Verbindung erneut versuchen; einen
Standby-Server ersetzt das nicht.

## SQLite

- „Database is busy“: Schreibende Prozesse warten in einer Schlange auf die einzige Schreibsperre; die Wartezeit ist
  `SQLiteTransactionWait` (25 Sekunden). Viele gleichzeitig Veröffentlichende sind ein Grund, auf eine
  Server-Datenbank umzusteigen.
- Jeder Prozess (PHP des Webservers, Velocitys Worker, Cron, Konsole) muss in das Datenbankverzeichnis und seine
  Dateien `-wal`, `-shm` und `.writer-lock` schreiben können; gemischte Dateieigentümer nach einem als root
  ausgeführten Befehl sind eine häufige Ursache.
- SQLite kann nicht über ein Netzwerkdateisystem geteilt werden ([Datenbankunterstützung](../../../datenbankunterstuetzung.md)).

## Wiederherstellen

1. Bestätigen Sie, dass die Administration lädt und eine Seite aus der Datenbank (nicht nur aus dem Cache) gerendert
   wird.
2. Leeren Sie die Caches, die Fehlerzustände enthalten könnten, sobald PHP und Velocity wieder normal laufen:
   `php bin/php/ezcache.php --clear-tag=content --allow-root-user`; unter Velocity
   `./console exp:velocity cache clear --allow-root-user`.
3. Prüfen Sie, dass die Cronjobs wieder laufen (sie sind während des Ausfalls gescheitert), und führen Sie den häufigen
   Teil einmal von Hand aus.
4. Dokumentieren Sie die Ursache; wenn Daten verloren gegangen sein könnten (ein Failover auf einen nachhinkenden
   Standby-Server), sagen Sie den Inhaltsverantwortlichen, was sie prüfen sollen.

## Verwandte Seiten
- [Website nicht erreichbar](website-nicht-erreichbar.md)
- [Lese-Replikate](../verfuegbarkeit/lese-replikate.md)
- [Datensicherungen je Datenbank-Engine](../../../../../eng-us/help/government/operations/backups-and-recovery/backups-per-database-engine.md)
- [Kapitel Datenbanken](../../../../installation/09-datenbanken.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
