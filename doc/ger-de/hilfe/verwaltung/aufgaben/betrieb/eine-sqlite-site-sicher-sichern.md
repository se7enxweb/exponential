---
title: Eine SQLite-Site sicher sichern
audience: government, admin
category: public-sector
order: 50
summary: Schritt für Schritt - eine Exponential-Site, die auf SQLite 3 läuft, ohne Anhalten sichern, mit dem Befehl .backup von sqlite3 statt einer Dateikopie, und es planen.
keywords: SQLite, Sicherung, Backup, sqlite3, .backup, WAL, Datenbankdatei, var/storage/sqlite3, Online-Sicherung, Cron, kleine Sites, backup, database file, online backup, small sites
type: help
---
# Eine SQLite-Site sicher sichern

Diese Aufgabe ist für Administratoren von Sites auf SQLite 3, der Standarddatenbank für eine Site, die von einer
einzigen Maschine ausgeliefert wird. Am Ende haben Sie eine konsistente Kopie der Datenbank. Eine einfache Dateikopie
einer Datenbank in Benutzung kann inkonsistent sein; der Befehl `.backup` des Werkzeugs `sqlite3` ist sicher
([Eine Site betreiben, Abschnitt 6](../../../../anleitungen/betrieb-einer-website.md#6-sichern-und-wiederherstellen)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Es planen](#es-planen)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Das Kommandozeilenwerkzeug `sqlite3` auf dem Server.
- Der Dateiname der Datenbank: `ls var/storage/sqlite3/` (`exponential.db` ist der Name, den `exp:install`
  standardmäßig verwendet).
- Ein Zielverzeichnis außerhalb der Installation, idealerweise auf einem anderen Datenträger.

## Schritte

```bash
sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/example/site-$(date +%F).db'"
tar czf /backup/example/files-$(date +%F).tgz var/storage settings/override settings/siteaccess
```

Die Datenbank besteht aus mehreren Dateien (`.db`, `-wal`, `-shm`, `.writer-lock`); `.backup` liest sie über SQLite
und schreibt eine einzige konsistente Datei, sodass Sie die anderen nicht kopieren.

## Es planen

Eine nächtliche Zeile in der Crontab als Benutzer der Site (passen Sie die Pfade an):

```
30 2 * * * cd /path/to/installation && sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/example/site-nightly.db'"
```

Rotieren Sie die Kopien nach Ihrer Aufbewahrungsregel
([Sicherungspläne und Aufbewahrung](../../../../../eng-us/help/government/operations/backups-and-recovery/backup-schedules-and-retention.md)).

## Prüfen, ob es funktioniert hat

```bash
sqlite3 /backup/example/site-nightly.db "PRAGMA integrity_check;"     # ok
```

Stellen Sie sie dann ab und zu auf einem Testserver wieder her
([Die Sicherung der letzten Nacht auf einem Testserver wiederherstellen](die-sicherung-der-letzten-nacht-auf-einem-testserver-wiederherstellen.md)).

## Verwandte Seiten
- [Vor einer Änderung eine Sicherung erstellen](vor-einer-aenderung-eine-sicherung-erstellen.md)
- [SQLite für Hoster](../../../hosting/grundlagen/sqlite-fuer-hoster.md)
- [Unterstützung für die SQLite-Datenbank](../../../../../eng-us/features/6.0/sqlite-database.md)
- [Sicherungen je Datenbank-Engine](../../../../../eng-us/help/government/operations/backups-and-recovery/backups-per-database-engine.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
