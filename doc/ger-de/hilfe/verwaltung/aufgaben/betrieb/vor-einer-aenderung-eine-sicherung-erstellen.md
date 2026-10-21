---
title: Vor einer Änderung eine Sicherung erstellen
audience: government, admin, hosting
category: public-sector
order: 40
summary: Schritt für Schritt - direkt vor einer Änderung, innerhalb des Wartungsfensters, eine konsistente Sicherung von Datenbank und Dateien erstellen, mit dem richtigen Werkzeug für Ihre Datenbank-Engine, und prüfen, ob die Dateien vollständig sind.
keywords: Sicherung, Backup, vor einer Änderung, Datenbank-Dump, mysqldump, pg_dump, SQLite-Sicherung, var/storage, settings, Wartungsmodus, konsistente Sicherung, git_manager, backup, before a change, database dump, sqlite backup, maintenance mode, consistent backup
type: help
---
# Vor einer Änderung eine Sicherung erstellen

Diese Aufgabe ist für Administratoren vor einem Upgrade, einer Migration oder jeder Änderung, die die Datenbank
berührt. Am Ende haben Sie einen Datenbank-Dump und ein Dateiarchiv vom selben Zeitpunkt, und das ist Ihr Weg zurück.
Hintergrund: [Was zu sichern ist](../../../../../eng-us/help/government/operations/backups-and-recovery/what-to-back-up.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Fallstricke](#fallstricke)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang, der Datenbankbenutzer und genug Speicherplatz auf einem anderen Datenträger oder System.
- Kennen Sie Ihre Engine:
  `grep -rn "DatabaseImplementation" settings/override settings/siteaccess/*/site.ini.append.php settings/site.ini`.

## Schritte

1. Schalten Sie die Wartung ein, damit Datenbank und Dateien übereinstimmen
   ([Die Site für ein Upgrade in den Wartungsmodus versetzen](die-site-fuer-ein-upgrade-in-den-wartungsmodus-versetzen.md)).
2. Erstellen Sie den Dump der Datenbank (der Client fragt nach dem Passwort; tippen Sie es niemals auf der
   Kommandozeile ein):

   | Engine | Befehl |
   |---|---|
   | MySQL, MariaDB | `mysqldump --single-transaction -u USER -p DATABASE > backup-before-change.sql` |
   | PostgreSQL | `pg_dump -U USER DATABASE > backup-before-change.sql` |
   | SQLite | `sqlite3 var/storage/sqlite3/exponential.db ".backup 'backup-before-change.db'"` (Ihr Dateiname) |

   Andere Engines: [Sicherungen je Datenbank-Engine](../../../../../eng-us/help/government/operations/backups-and-recovery/backups-per-database-engine.md).
3. Archivieren Sie die Dateien:

   ```bash
   tar czf backup-before-change-files.tgz var/storage settings/override settings/siteaccess extension design
   ```

4. Kopieren Sie beides an den Sicherungsort, weg von diesem Server.

Auf MySQL- oder MariaDB-Sites mit der Erweiterung git_manager ist **Setup > Backup**, **Create full site backup** eine
Alternative aus dem Browser; ihre Archive bleiben auf demselben Server, bis Sie sie verschieben
([Sicherungen aus der Administration mit git_manager](../../../../../eng-us/help/government/operations/backups-and-recovery/backups-from-the-admin-with-git-manager.md)).

## Prüfen, ob es funktioniert hat

- Der Dump endet normal (bei MySQL ist die letzte Zeile ein Kommentar `-- Dump completed`) und hat eine plausible
  Größe.
- `tar tzf backup-before-change-files.tgz | head` listet die Dateien.
- Notieren Sie Dateinamen und Größen im Änderungsnachweis.

## Fallstricke

- Die Audit-Schlüssel stehen in `settings/override/audit.ini.append.php`: Das Archiv oben enthält sie, also schützen Sie
  es wie ein Geheimnis ([Schlüssel und Geheimnisse in Sicherungen](../../../../../eng-us/help/government/operations/backups-and-recovery/keys-and-secrets-in-backups.md)).
- Kopieren Sie niemals eine SQLite-Datei, während die Site läuft; verwenden Sie `.backup`.
- Eine Sicherung, die Sie nie wiederhergestellt haben, ist eine Hoffnung:
  [Die Sicherung der letzten Nacht auf einem Testserver wiederherstellen](die-sicherung-der-letzten-nacht-auf-einem-testserver-wiederherstellen.md).

## Verwandte Seiten
- [Eine SQLite-Site sicher sichern](eine-sqlite-site-sicher-sichern.md)
- [Planung des Zurückrollens](../../../../../eng-us/help/government/operations/upgrades-and-change/rollback-planning.md)
- [Aktualisieren, Schritt 1](../../../../anleitungen/aktualisieren.md#schritt-1-immer-eine-datensicherung)
- [Sicherungen: was zu sichern ist (Hosting)](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
