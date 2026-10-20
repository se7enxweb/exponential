---
title: Wiederherstellung und Wiederherstellungstests
audience: hosting, admin
category: hosting
order: 80
summary: Das Verfahren zur Wiederherstellung einer Exponential-Website Schritt für Schritt, wie Sie einen Wiederherstellungstest auf einer anderen Maschine ausführen, ohne Mail zu versenden oder Jobs laufen zu lassen, und die Prüfungen, die beweisen, dass die wiederhergestellte Website vollständig ist.
keywords: Wiederherstellung, Wiederherstellungstest, Notfallwiederherstellung, Prüfung von Backups, Wartungsmodus, Caches, Autoloads, Velocity, Datei-Transport, integrity_check, audit verify, Staging-Kopie, restore, restore test, disaster recovery, backup verification, maintenance mode, file transport, staging copy
type: help
---
# Wiederherstellung und Wiederherstellungstests

Diese Seite ist für die Systemadministratoren und Support-Mitarbeitenden, die die Website eines Kunden aus einem Backup
zurückholen, und für die, die beweisen, bevor etwas schiefgeht, dass sich die Backups wiederherstellen lassen. Sie
beantwortet: in welcher Reihenfolge Sie wiederherstellen, was Sie danach leeren und neu starten, wie Sie einen
Wiederherstellungstest sicher auf einer anderen Maschine ausführen und wie Sie prüfen, dass das Ergebnis vollständig ist.

Ein Backup, das nie wiederhergestellt wurde, ist eine Hoffnung, kein Backup
([10.9 Backups und Wiederherstellung](../../../../en/us/install/10-after-installing.md#109-backups-and-restore)). Die Notfallfassung
dieser Seite, für einen Vorfall, ist das Runbook [Aus einem Backup wiederherstellen](../runbooks/aus-einem-backup-wiederherstellen.md).

## Inhalt

1. [Bevor Sie wiederherstellen](#bevor-sie-wiederherstellen)
2. [Die Wiederherstellung, Schritt für Schritt](#die-wiederherstellung-schritt-für-schritt)
3. [Prüfungen nach einer Wiederherstellung](#prüfungen-nach-einer-wiederherstellung)
4. [Ein Wiederherstellungstest auf einer anderen Maschine](#ein-wiederherstellungstest-auf-einer-anderen-maschine)
5. [Was schiefgeht](#was-schiefgeht)
6. [Checkliste](#checkliste)

## Bevor Sie wiederherstellen

- **Vereinbaren Sie, was wiederhergestellt wird.** Eine Wiederherstellung bringt die ganze Website auf den Moment des Backups
  zurück: Seitdem veröffentlichte Inhalte gehen verloren. Bei einer einzelnen gelöschten Seite sehen Sie zuerst in den
  Papierkorb und die älteren Versionen in Exponential.
- **Sichern Sie den aktuellen Zustand**, bevor Sie ihn überschreiben, damit eine falsche Wiederherstellung rückgängig gemacht
  werden kann.
- **Prüfen Sie das Release.** Stellen Sie auf dasselbe Exponential-Release (und dieselbe `composer.lock`) wieder her, mit dem
  das Backup erstellt wurde. Eine Datenbank aus einem älteren Release braucht danach die Upgrade-Schritte
  ([Kunden-Websites aktualisieren](kunden-websites-aktualisieren.md)).
- **Kennen Sie den Befehl der Engine zur Wiederherstellung**: [Datenbank-Backups je Engine](datenbank-backups-je-engine.md).

## Die Wiederherstellung, Schritt für Schritt

Aus [10.9, Wiederherstellung](../../../../en/us/install/10-after-installing.md#restore):

1. **Wartung an.** Besucher erhalten die Wartungsseite 503, während sich die Daten ändern.

   ```bash
   php bin/php/maintenance.php on --message="Restoring" --allow-root-user
   ```

   Unter Velocity halten Sie auch den Server an, damit kein Worker die alte Datenbank hält:

   ```bash
   php bin/php/console exp:velocity stop --allow-root-user
   ```

2. **Stellen Sie die Datenbank** mit dem Werkzeug ihrer Engine **wieder her**, und **entpacken Sie die Dateien** über die
   Installation (`var/storage`, den Speicher pro Website, `settings/override`, `settings/siteaccess`, Ihre Erweiterungen).
3. **Korrigieren Sie Eigentümer und Modus** dessen, was Sie entpackt haben: Die Dateien müssen dem Benutzer der Website oder
   der gemeinsamen Gruppe gehören ([8.7 Dateiberechtigungen und Eigentum](../../../../en/us/install/08-serving-the-site.md#87-file-permissions-and-ownership)).
4. **Leeren Sie jeden Cache und erzeugen Sie die Autoloads neu**:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   ```

5. **Starten Sie Velocity, leeren Sie seinen Antwort-Cache, schalten Sie die Wartung aus**, und öffnen Sie die Startseite und
   die Administration:

   ```bash
   php bin/php/console exp:velocity start --allow-root-user
   php bin/php/console exp:velocity cache clear --allow-root-user
   php bin/php/maintenance.php off --allow-root-user
   ```

Unter Apache oder nginx mit PHP-FPM gibt es keinen Schritt für Velocity; laden Sie das PHP-FPM neu, das die Website ausliefert,
wenn sich Code oder Einstellungen geändert haben ([Änderungen ausrollen](aenderungen-ausrollen.md)).

> **Warnung**
> Führen Sie die Befehle als Benutzer der Website aus (oder als root mit `--allow-root-user` und korrigieren Sie dann das
> Eigentum). Dateien, die root in `var/` anlegt, kann der Webserver danach nicht ersetzen, und die Website versagt an seltsamen
> Stellen ([12.3 Berechtigungen und Eigentum](../../../../en/us/install/12-troubleshooting.md#123-permissions-and-ownership)).

> **Hinweis**
> Der Wartungszustand ist die Datei `var/maintenance.json`. Wenn ein Backup des ganzen Verzeichnisses `var/` erstellt wurde,
> während die Wartung an war, kommt die wiederhergestellte Website im Wartungsmodus zurück; `php bin/php/maintenance.php off`
> beendet ihn.

## Prüfungen nach einer Wiederherstellung

| Prüfung | Befehl oder Handlung | Gutes Ergebnis |
|---|---|---|
| Die Website antwortet | `curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/` | `200` |
| Die Administration antwortet | `/admin/user/login` öffnen, anmelden | das Dashboard |
| Version | **Setup > Systeminformationen** oder `./console exp:systeminfo` | das erwartete Release; keine gescheiterte Prüfung |
| Caches | `php bin/php/cache.php status --allow-root-user` | endet mit `PASS` |
| Prüfprotokoll | `./console exp:audit verify --allow-root-user` | Exit-Status 0 |
| Inhalte | aktuelle Seiten und ein Bild öffnen; ein Testobjekt veröffentlichen | Bilder laden, Veröffentlichen funktioniert |
| Nur SQLite | `sqlite3 <db file> "PRAGMA integrity_check"` | `ok` |
| Neue Fehler | `tail -n 50 var/log/error.log` | nichts, was Sie nicht erklären können |

Quellen: [10.11 Überwachung und Zustandsprüfungen](../../../../en/us/install/10-after-installing.md#1011-monitoring-and-health-checks),
[Datenbankunterstützung](../../../../en/us/help/database-support.md#backup-and-restore). Wenn Inhalte zwischen Maschinen oder Engines umgezogen
sind, vergleichen Sie außerdem die Zeilenzahlen von `ezcontentobject`, `ezcontentobject_tree` und `ezcontentobject_attribute`
mit der Quelle ([Eine Website auf eine andere Datenbank umziehen](../../../../en/us/help/database-support.md#moving-a-site-to-another-database)).

## Ein Wiederherstellungstest auf einer anderen Maschine

Eine Testwiederherstellung beweist das Backup, das Verfahren und die Dauer. Führen Sie sie mindestens einmal und nach jedem
größeren Upgrade auf einer anderen Maschine aus ([10.9](../../../../en/us/install/10-after-installing.md#restore)), und nach einem
Zeitplan, den Sie für Ihren Dienst festlegen ([Backup-Zeitpläne und Aufbewahrung](backup-zeitplaene-und-aufbewahrung.md)).

Eine wiederhergestellte Kopie ist eine vollständige Website mit echten Benutzerkonten und echten E-Mail-Adressen. Halten Sie
sie davon ab, sich wie die Live-Website zu verhalten:

1. **Mail stoppen.** Setzen Sie auf der Testmaschine den Datei-Transport, sodass jede Mail nach `var/log/mail/` geschrieben statt
   versandt wird ([Mailzustellung](mailzustellung.md)):

   ```ini
   # settings/override/site.ini.append.php on the test machine
   [MailSettings]
   Transport=file
   ```

   Oder behalten Sie den Transport und setzen `DebugSending=enabled` mit `DebugReceiverEmail=<your test address>`, was jede Mail
   an eine Adresse schickt. Eine Variante pro Maschine (`settings/override/site.ini.test.append.php` mit `EXP_ENV=test`) hält
   das aus den Live-Einstellungen heraus ([Einstellungen je Umgebung](../../../../en/us/features/6.0/environment-settings.md)).
2. **Richten Sie die Crontab** auf der Testmaschine **nicht ein**, sonst laufen Benachrichtigungen, Newsletter und Inhaltsjobs
   gegen echte Adressen. Führen Sie einen Teil nur von Hand aus, wenn Sie ihn testen.
3. **Beschränken Sie den Zugang.** Stellen Sie die Kopie nicht ins öffentliche Internet; sie enthält personenbezogene Daten.
4. **Wiederherstellen, prüfen, messen.** Folgen Sie den Schritten und Prüfungen oben, und schreiben Sie auf, wie lange jeder
   gedauert hat.
5. **Entfernen Sie die Kopie**, wenn der Test erledigt ist, einschließlich ihrer Datenbank und Dateien.

> **Tipp**
> Halten Sie jeden Test in einer Zeile fest: Datum, Website, genutztes Backup, Dauer der Wiederherstellung, Ergebnis, wer ihn
> ausgeführt hat. Diese Zeile beantwortet die Frage des Kunden (und des Prüfers), ob Ihre Backups funktionieren, mit einem Beleg.

## Was schiefgeht

| Symptom nach einer Wiederherstellung | Ursache und Abhilfe |
|---|---|
| Seiten zeigen Inhalte von vor der Wiederherstellung | Caches halten sie noch: alle Caches und den Antwort-Cache von Velocity leeren, nach einem eventuellen Neuladen von FPM |
| `Class ... not found` | Autoloads oder `vendor/` passen nicht: `php bin/php/ezpgenerateautoloads.php -e`, und `vendor/` muss zu `composer.json` passen |
| Bilder fehlen, Seiten sind in Ordnung | die Hälfte mit den Dateien wurde nicht wiederhergestellt, oder sie stammt aus einem anderen Moment als die Datenbank |
| Fehler zu fehlenden Tabellen oder Spalten | das Backup stammt aus einem anderen Release: die Upgrade-Schritte anwenden |
| Der Webserver kann `var/` nicht schreiben | Eigentümer oder Modus nach dem Entpacken falsch: korrigieren |
| `.restore` von SQLite scheitert | ein Schreiber hielt die Sperre, oder die Seitengrößen passen nicht; nutzen Sie das Ersetzen der Datei wie auf [Datenbank-Backups je Engine](datenbank-backups-je-engine.md#1-sqlite-3) beschrieben |

## Checkliste

- [ ] Der aktuelle Zustand wurde vor der Wiederherstellung gesichert.
- [ ] Die Wartung war an und Velocity angehalten, während sich die Daten änderten.
- [ ] Datenbank und Dateien stammen aus demselben Moment des Backups.
- [ ] Eigentümer und Modus stimmen; Caches und Autoloads wurden neu aufgebaut.
- [ ] Die Prüfungen oben bestehen alle.
- [ ] Testwiederherstellungen laufen auf einer anderen Maschine ohne Mail und ohne Crontab und werden festgehalten.

## Verwandte Seiten

- [Aus einem Backup wiederherstellen](../runbooks/aus-einem-backup-wiederherstellen.md) (Runbook)
- [Was gesichert werden muss](backups-was-gesichert-werden-muss.md)
- [Datenbank-Backups je Engine](datenbank-backups-je-engine.md)
- [Wartungsmodus](wartungsmodus.md)
- [Eine Website zwischen Hostern umziehen](../migrationen/eine-website-zwischen-hostern-umziehen.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
