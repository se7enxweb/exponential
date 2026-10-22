---
title: Sicherungen je Engine
audience: dba
category: database-administration
order: 28
summary: Wie Sie die Datenbank einer Exponential-Website auf jeder Engine sichern (sqlite3 .backup oder VACUUM INTO, pg_dump, mongodump, mariadb-dump und mysqldump mit --single-transaction, Data Pump für Oracle), was sonst zu einer vollständigen Sicherung gehört, wann der Wartungsmodus nötig ist und wie Sie eine Sicherung prüfen.
keywords: Sicherung, Backup, Datenbanksicherung, sqlite3 .backup, VACUUM INTO, integrity_check, pg_dump, mongodump, mysqldump, mariadb-dump, --single-transaction, expdp, datapump, ext:ezoracle:datapump, Wartungsmodus, var/storage, settings/override, Aufbewahrung, portabler Dump, dba, backup, database backup, maintenance mode, retention, portable dump
type: help
---
# Sicherungen je Engine

Diese Seite richtet sich an Datenbankadministratoren, die die Sicherungen einer Exponential-Website einrichten oder prüfen.
Sie beantwortet: welcher Befehl jede Engine konsistent sichert, was mit der Datenbank gesichert werden muss, wann der
Wartungsmodus nötig ist und wie Sie eine Sicherung prüfen. Quellen:
[Datenbankunterstützung, Sicherung und Wiederherstellung](../datenbankunterstuetzung.md#backup-und-wiederherstellung) und
[10.9 Sicherungen und Wiederherstellung](../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung).

## Inhalt

1. [Eine vollständige Sicherung](#eine-vollständige-sicherung)
2. [Die Datenbank, je Engine](#die-datenbank-je-engine)
3. [Konsistenz und Wartungsmodus](#konsistenz-und-wartungsmodus)
4. [Ein portables zweites Format](#ein-portables-zweites-format)
5. [Eine Sicherung prüfen](#eine-sicherung-prüfen)
6. [Zeitpläne und Aufbewahrung](#zeitpläne-und-aufbewahrung)
7. [Checkliste](#checkliste)

## Eine vollständige Sicherung

Eine vollständige Sicherung ist die Datenbank **und** die Dateien, zum selben Zeitpunkt aufgenommen:

| Pfad | Warum |
|---|---|
| die Datenbank | Inhalte, Benutzer, in Tabellen gespeicherte Einstellungen |
| `var/storage` und `var/<site>/storage` | hochgeladene Bilder und Dateien |
| `settings/override`, `settings/siteaccess` | die Einstellungen der Installation, mit dem Datenbankpasswort |
| Ihre eigenen Erweiterungen und Designs | wenn sie nicht in einem Repository liegen |
| `var/storage/sqlite3/` | die Datenbank, bei SQLite (mit den Werkzeugen von SQLite aufgenommen, siehe unten) |

Caches (`var/cache`, `var/<site>/cache`) brauchen keine Sicherung; sie werden neu erzeugt. Eine Datenbanksicherung allein
stellt keine Website wieder her: Inhalte verweisen auf Dateien in `var/storage`.

## Die Datenbank, je Engine

| Engine | Sicherung | Hinweis |
|---|---|---|
| SQLite 3 | `sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/exponential.db'"` | online und konsistent, während die Website läuft; oder `"VACUUM INTO '/backup/exponential.db'"` (SQLite 3.27+) für eine verdichtete Kopie. **Kopieren Sie nie eine Datenbankdatei, die in Gebrauch ist** |
| PostgreSQL | `pg_dump -U exponential -Fc exponential > exponential.dump` | das Custom-Format lässt sich selektiv wiederherstellen |
| MongoDB | `mongodump --uri="mongodb://exponential@localhost:27017/exponential" --out=/backup/exponential` | das Werkzeug fragt nach dem Passwort |
| MariaDB | `mariadb-dump --single-transaction -u exponential -p exponential > exponential.sql` | `mysqldump` ist der ältere Name |
| Oracle | `php bin/php/console ext:ezoracle:datapump --export=db.dmp` | oder `expdp` von Oracle |
| MySQL | `mysqldump --single-transaction -u exponential -p exponential > exponential.sql` | |

`--single-transaction` liefert einen konsistenten Dump von InnoDB-Tabellen, ohne sie zu sperren. `-p` ohne Wert lässt den
Client nach dem Passwort fragen; schreiben Sie es nie auf die Kommandozeile, verwenden Sie lieber die Abfragen oder
Optionsdateien der Werkzeuge. Die Wiederherstellung zu einem Zeitpunkt (WAL-Archivierung bei PostgreSQL, Binärlogs bei MySQL,
Oplog bei MongoDB, RMAN bei Oracle) ist eine eigene Funktion der Engine und funktioniert mit Exponential wie mit jeder
Anwendung; konfigurieren Sie sie auf dem Server.

## Konsistenz und Wartungsmodus

Auf SQLite und auf MySQL oder MariaDB mit InnoDB ist die Datenbanksicherung ohne Wartungsmodus konsistent. Der Wartungsmodus
sorgt dafür, dass Datenbank und Dateien übereinstimmen, weil zwischen beiden nichts hochgeladen wird:

```bash
php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user
# database backup, then:
tar czf /backup/files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess
php bin/php/maintenance.php off --allow-root-user
```

`--allow-admin` hält die Administration erreichbar, `--allow-ip` lässt angegebene Adressen durch
([Wartungsmodus](../../anleitungen/wartungsmodus.md)). Für eine nächtliche Sicherung einer viel genutzten Website nehmen viele
Betreiber stattdessen einige Minuten Unterschied zwischen Dateien und Datenbank in Kauf; sichern Sie dann zuerst die
Datenbank und gleich danach die Dateien.

## Ein portables zweites Format

Neben dem nativen Dump der Engine ist ein `.dba`-Dump von der Engine unabhängig und lässt sich in eine andere SQL-Engine
laden:

```bash
php bin/php/ezsqldumpschema.php --type=mysql --user=exponential --password='<password>' \
    --format=generic --output-array --output-types=all exponential var/backup/site.dba --allow-root-user
```

Er ist langsamer und größer als ein nativer Dump und nimmt das Passwort auf der Kommandozeile
([Schemawerkzeuge](schemawerkzeuge-dump-insert-diff.md)). Für MongoDB ist er nicht verfügbar.

## Eine Sicherung prüfen

| Engine | Prüfung |
|---|---|
| SQLite | `sqlite3 /backup/exponential.db "PRAGMA integrity_check"` gibt `ok` aus |
| PostgreSQL | `pg_restore --list exponential.dump` listet die Objekte ohne Fehler |
| MySQL, MariaDB | der Dump endet mit `-- Dump completed`; die Datei ist nicht leer |
| MongoDB | `mongorestore --dryRun` auf dem Verzeichnis |
| Oracle | das Data-Pump-Log meldet Erfolg |

Die eigentliche Prüfung ist eine Wiederherstellung auf einer anderen Maschine ([Eine Datenbank wiederherstellen](eine-datenbank-wiederherstellen.md)).
Eine Sicherung, die nie wiederhergestellt wurde, ist eine Hoffnung, keine Sicherung.

## Zeitpläne und Aufbewahrung

Bewahren Sie Sicherungen getrennt vom Server auf und behalten Sie mehrere Generationen. Sicherungen enthalten E-Mail-Adressen
und Passwort-Hashes: Verschlüsseln Sie sie und löschen Sie alte Generationen nach Plan. Zeitpläne für Hosting-Unternehmen
stehen unter [Sicherungszeitpläne und Aufbewahrung](../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md).

## Checkliste

- [ ] Der Datenbankbefehl Ihrer Engine läuft nächtlich und ist konsistent.
- [ ] `var/storage`, `settings/override` und `settings/siteaccess` werden mit gesichert.
- [ ] SQLite wird mit `.backup` oder `VACUUM INTO` gesichert, nie mit `cp`.
- [ ] Kein Passwort auf einer Kommandozeile oder in einer Crontab.
- [ ] Sicherungen sind geprüft, verschlüsselt und getrennt vom Server aufbewahrt.
- [ ] In diesem Quartal wurde eine Wiederherstellung getestet.

## Verwandte Seiten

- [Eine Datenbank wiederherstellen](eine-datenbank-wiederherstellen.md)
- [Datenbanksicherungen je Engine](../hosting/betrieb/datenbank-backups-je-engine.md) (für Hosting-Unternehmen)
- [Eine Website betreiben, sichern und wiederherstellen](../../anleitungen/betrieb-einer-website.md#6-sichern-und-wiederherstellen)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
