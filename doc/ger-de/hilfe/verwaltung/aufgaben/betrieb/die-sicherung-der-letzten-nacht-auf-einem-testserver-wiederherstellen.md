---
title: Die Sicherung der letzten Nacht auf einem Testserver wiederherstellen
audience: government, admin, hosting
category: public-sector
order: 60
summary: Schritt für Schritt - beweisen, dass Ihre Sicherung funktioniert, indem Sie Datenbank und Dateien der letzten Nacht auf einem Testserver wiederherstellen, mit auf den Datei-Transport umgestellten E-Mails, damit kein Bürger angeschrieben wird, dann die Site prüfen und die benötigte Zeit festhalten.
keywords: Test der Wiederherstellung, Prüfung der Sicherung, Testserver, Datenbank wiederherstellen, mysql restore, psql, sqlite, Datei-Transport, Wiederherstellungszeit, Nachweis, Notfallwiederherstellung, restore test, backup verification, test server, restore database, file transport, recovery time, evidence, disaster recovery
type: help
---
# Die Sicherung der letzten Nacht auf einem Testserver wiederherstellen

Diese Aufgabe ist für Administratoren, die zeigen müssen, dass sich Sicherungen wiederherstellen lassen, wie es die
meisten Pläne zur Betriebskontinuität verlangen. Am Ende läuft auf einem Testserver die Site aus der Sicherung der
letzten Nacht, und Sie wissen, wie lange eine Wiederherstellung dauert. Hintergrund:
[Tests der Wiederherstellung](../../../../../eng-us/help/government/operations/backups-and-recovery/restore-testing.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Das Ergebnis festhalten](#das-ergebnis-festhalten)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Testserver mit derselben Version von Exponential, PHP und Datenbank-Engine und einer leeren Datenbank.
- Die Sicherungsdateien: Datenbank-Dump und Dateiarchiv derselben Nacht.
- Eine Vereinbarung mit Ihrem Datenschutzbeauftragten über das Wiederherstellen von Produktionsdaten auf diesem Server
  und darüber, wer darauf zugreifen darf.

## Schritte

1. Notieren Sie die Startzeit.
2. Legen Sie den Code derselben Version auf den Testserver (Checkout des Tags, `composer install`).
3. Entpacken Sie die Dateien: `tar xzf files-<date>.tgz` im Installationsverzeichnis.
4. Setzen Sie für diese Kopie den Datei-Transport, **bevor** irgendetwas E-Mails sendet:

   ```ini
   # settings/override/site.ini.append.php on the test server
   [MailSettings]
   Transport=file
   ```

   Verweisen Sie außerdem `[DatabaseSettings]` auf die Testdatenbank.
5. Stellen Sie die Datenbank wieder her:

   | Engine | Befehl |
   |---|---|
   | MySQL, MariaDB | `mysql -u USER -p DATABASE < backup.sql` |
   | PostgreSQL | `psql -U USER DATABASE < backup.sql` |
   | SQLite | kopieren Sie `backup.db` nach `var/storage/sqlite3/<file>`, während die Site im Wartungsmodus ist |

6. Neu erzeugen und leeren:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

7. Notieren Sie die Endzeit.

## Prüfen, ob es funktioniert hat

- Die Startseite und einige tiefe Seiten werden dargestellt; Bilder laden (aus `var/storage`).
- Sie können sich mit einem Testkonto in der Administration anmelden.
- **Setup > Systeminformationen**: Zustandsprüfungen; E-Mail zeigt `Transport=file`.
- `./console exp:audit status --allow-root-user` liest die wiederhergestellte Konfiguration des Audits (die Schlüssel
  kamen mit den Einstellungen).

## Das Ergebnis festhalten

Datum, verwendete Sicherung, benötigte Zeit, gefundene Probleme, wer geprüft hat. Die benötigte Zeit ist eine Tatsache
für Ihre Planung der Wiederherstellung
([RPO und RTO als Entscheidungen](../../../../../eng-us/help/government/operations/backups-and-recovery/rpo-and-rto-as-decisions.md)). Entfernen oder
sperren Sie die Testkopie, wenn Sie fertig sind, denn sie enthält Produktionsdaten.

## Verwandte Seiten
- [Auf einem Testserver wiederherstellen](../../../../../eng-us/help/government/operations/backups-and-recovery/restoring-to-a-test-server.md)
- [Wiederherstellung und Tests der Wiederherstellung (Hosting)](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)
- [Aus der Sicherung wiederherstellen (Runbook für Hoster)](../../../hosting/runbooks/aus-einem-backup-wiederherstellen.md)
- [Plan für die Notfallwiederherstellung](../../../../../eng-us/help/government/operations/backups-and-recovery/disaster-recovery-plan.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
