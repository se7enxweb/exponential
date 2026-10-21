---
title: Die Datenbank-Engine einer Kunden-Website wechseln
audience: hosting, admin
category: hosting
order: 60
summary: Wie ein Hoster eine Kunden-Website von einer der sechs unterstützten Datenbanken auf eine andere umzieht (zum Beispiel von SQLite 3 auf PostgreSQL, wenn eine Website einer Maschine entwächst) - wann es sich lohnt, die von der Engine unabhängige Übertragung über .dba, Besonderheiten von Oracle und MongoDB, die zu ändernden Einstellungen und die Prüfungen vor dem Wiederöffnen.
keywords: Datenbank-Engine, Datenbank wechseln, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, ezsqldumpschema, ezsqlinsertschema, dba, DatabaseImplementation, Wartungsfenster, Zeilenzahlen, Sequenzen, ezoracle, mysql2oracle, database engine, switch database, maintenance window, row counts, sequences
type: help
---
# Die Datenbank-Engine einer Kunden-Website wechseln

Diese Seite ist für Systemadministratoren bei Hosting-Anbietern, die eine Kunden-Website auf eine andere Datenbank-Engine
umziehen. Sie beantwortet: wann sich ein Wechsel lohnt, wie die Daten übertragen werden, was sich in den Einstellungen
ändert und wie Sie das Ergebnis prüfen.

Die unterstützten Datenbanken in der offiziellen Reihenfolge: 1. SQLite 3 (Standard) · 2. PostgreSQL · 3. MongoDB ·
4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich, ohne
Treiber in 6.0.x ([Datenbankunterstützung](../../datenbankunterstuetzung.md)).

## Inhalt

1. [Wann wechseln](#wann-wechseln)
2. [Planen](#planen)
3. [Zwischen den SQL-Engines](#zwischen-den-sql-engines)
4. [Nach Oracle oder MongoDB](#nach-oracle-oder-mongodb)
5. [Zu ändernde Einstellungen](#zu-ändernde-einstellungen)
6. [Prüfungen vor dem Wiederöffnen](#prüfungen-vor-dem-wiederöffnen)
7. [Spätere Upgrades](#spätere-upgrades)

## Wann wechseln

Typische Gründe, die ein Hoster sieht, aus [Datenbankunterstützung](../../datenbankunterstuetzung.md#1-sqlite-3-der-standard):
Wählen Sie statt SQLite 3 einen Server, wenn mehr als eine Maschine in die Datenbank schreiben muss, wenn viele
Redakteure den ganzen Tag gleichzeitig große Änderungen veröffentlichen oder wenn Sie Replikation oder Benutzer und Rechte
auf Ebene der Datenbank brauchen. Der empfohlene Server ist PostgreSQL. Andere Gründe: Die Organisation des Kunden
standardisiert auf eine Engine, oder Ihre Plattform bietet verwaltete Backups nur für eine Engine an.

> **Hinweis**
> Ein Wechsel ist für sich genommen keine Leistungskorrektur. Prüfen Sie zuerst die Cache-Schichten
> ([Cache-Schichten](../leistung/cache-schichten.md)); der größte Teil der Leselast erreicht die Datenbank nie.

## Planen

- Ein Wartungsfenster: Zwischen dem Dump und dem Moment, in dem die Website auf der neuen Datenbank läuft, darf kein
  Redakteur veröffentlichen, sonst geht diese Arbeit verloren ([9.9](../../../installation/09-datenbanken.md#99-eine-bestehende-site-auf-eine-andere-engine-umstellen)).
- Ein vollständiges Backup der aktuellen Datenbank und der Dateien, bevor Sie beginnen.
- Der neue Datenbankserver, eine leere Datenbank und ein Benutzer mit dem Recht, Tabellen anzulegen.
- Eine Generalprobe auf einer Kopie, mit Zeitmessung.

## Zwischen den SQL-Engines

Die Schema-Werkzeuge ziehen eine Datenbank zwischen SQLite 3, PostgreSQL, MariaDB, MySQL und (mit `ezoracle`) Oracle über
das von der Engine unabhängige Format `.dba` von Exponential um. `--type` nimmt einen Schema-Handler aus
`settings/dbschema.ini`: `mysql`, `postgresql`, `sqlite3`, `oracle`.

```bash
# 1. maintenance on
php bin/php/maintenance.php on --message="Database maintenance" --until=1h --allow-root-user

# 2. dump schema and data of the current database (MySQL in this example)
php bin/php/ezsqldumpschema.php --type=mysql --host=localhost --user=exponential --password='<password>' \
    --format=generic --output-array --output-types=all exponential var/backup/site.dba --allow-root-user

# 3. load them into the new one (PostgreSQL in this example)
php bin/php/ezsqlinsertschema.php --type=postgresql --host=localhost --user=exponential --password='<password>' \
    --insert-types=all --schema-file=var/backup/site.dba var/backup/site.dba exponential --allow-root-user
```

Für SQLite 3 ist das Datenbankargument der Dateiname, wie in der Einstellung `Database`. Führen Sie beide Skripte zuerst mit
`--help` aus; dort stehen die Optionen je Engine (Host, Port, Socket, Tabellentyp, Zeichensatz).

> **Warnung**
> `--password=` setzt das Passwort auf die Kommandozeile, wo andere Benutzer der Maschine es in der Prozessliste sehen
> können. Führen Sie die Übertragung auf einer Maschine aus, die Sie kontrollieren, oder mit einem temporären Passwort, das
> Sie danach ändern ([9.9](../../../installation/09-datenbanken.md#99-eine-bestehende-site-auf-eine-andere-engine-umstellen)).

## Nach Oracle oder MongoDB

- **Oracle**: Die Erweiterung `ezoracle` hat eigene Übertragungsbefehle, `ext:ezoracle:mysql2oracle-schema` und
  `ext:ezoracle:mysql2oracle-data` ([Oracle für Hoster](../grundlagen/oracle-fuer-hoster.md)).
- **MongoDB**: die Skripte in `bin/mongodb/` (`export_mysql.sh`, `mysql2ndjson.py`, `import_all.sh`,
  `validate_ndjson.sh`). Prüfen Sie die [Grenzen von MongoDB](../../datenbankunterstuetzung.md#grenzen-von-mongodb) gegen die
  Erweiterungen des Kunden, bevor Sie den Wechsel zusagen ([MongoDB für Hoster](../grundlagen/mongodb-fuer-hoster.md)).

## Zu ändernde Einstellungen

In `settings/override/site.ini.append.php`, Block `[DatabaseSettings]`: `DatabaseImplementation` und die Schlüssel der
Verbindung (Server, Port, Benutzer, Passwort, Datenbank). Ein vergessenes `DatabaseImplementation` ist der übliche Grund,
warum nach einem Wechsel keine Seite lädt (9.9). Die Schlüssel je Engine stehen auf
[Die Einstellungen: DatabaseSettings in site.ini](../../datenbankunterstuetzung.md#die-einstellungen-databasesettings-in-siteini).

Leeren Sie dann jeden Cache und starten Sie die Worker neu:

```bash
php bin/php/ezcache.php --clear-all --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user    # when Velocity serves the site
```

Unter PHP-FPM laden Sie stattdessen den PHP-FPM-Dienst der Website neu.

## Prüfungen vor dem Wiederöffnen

1. Zählen Sie die Zeilen von `ezcontentobject`, `ezcontentobject_tree` und `ezcontentobject_attribute` auf beiden Seiten;
   die Zahlen müssen übereinstimmen.
2. Korrigieren Sie auf PostgreSQL die Sequenzen, wenn `ezsqlinsertschema.php` ein Problem damit gemeldet hat
   ([9.4](../../../installation/09-datenbanken.md#94-postgresql)).
3. Melden Sie sich in der Administration an, öffnen Sie einige Seiten und veröffentlichen Sie ein Testobjekt: Eine
   Veröffentlichung prüft die Schreibvorgänge, die ein Seitenaufruf nicht prüft.
4. Schalten Sie den Wartungsmodus aus: `php bin/php/maintenance.php off --allow-root-user`.
5. Stellen Sie den Backup-Job auf das Werkzeug der neuen Engine um ([Datenbank-Backups je Engine](../betrieb/datenbank-backups-je-engine.md)).

## Spätere Upgrades

Schema-Updates gelten je Engine: Update-Dateien werden nach der Engine gewählt, auf der die Website jetzt läuft, nicht nach
der, auf der sie installiert wurde (`update/database/mysql/`, `update/database/postgresql/`, `update/database/sqlite/` und
für Oracle die Dateien in `ezoracle`; MongoDB hat keine SQL-Update-Dateien). Halten Sie die neue Engine im Datensatz des
Kunden fest.

## Verwandte Seiten

- [Datenbanken in der offiziellen Reihenfolge](../grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)
- [Eine Website zwischen Hostern umziehen](eine-website-zwischen-hostern-umziehen.md)
- [Kapitel 9: Datenbanken](../../../installation/09-datenbanken.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
