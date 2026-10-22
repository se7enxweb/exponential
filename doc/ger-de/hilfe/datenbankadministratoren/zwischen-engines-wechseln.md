---
title: Eine Website auf eine andere Engine umziehen
audience: dba
category: database-administration
order: 33
summary: Wie Sie eine bestehende Exponential-Website von einer Datenbank-Engine auf eine andere umziehen - das Wartungsfenster planen, Dump und Insert im Format .dba zwischen SQL-Engines, die Übertragungsbefehle von ezoracle nach Oracle, die Skripte in bin/mongodb nach MongoDB, die zu ändernden Einstellungen, die Prüfungen vor dem Wiederöffnen und was sich für spätere Upgrades ändert.
keywords: Datenbank migrieren, Engine wechseln, Datenbank ändern, MySQL nach PostgreSQL, MySQL nach SQLite, SQLite nach PostgreSQL, nach Oracle, mysql2oracle, nach MongoDB, ezsqldumpschema, ezsqlinsertschema, dba, Wartungsfenster, Zeilenzahlen, Sequenzen, DatabaseImplementation, migrate database, switch engine, change database, maintenance window, row counts, sequences
type: help
---
# Eine Website auf eine andere Engine umziehen

Diese Seite richtet sich an Datenbankadministratoren, die eine bestehende Exponential-Website auf eine andere
Datenbank-Engine umziehen, zum Beispiel von SQLite auf PostgreSQL, wenn die Website auf mehrere Server wächst, oder von MySQL
auf PostgreSQL wegen einer Richtlinie der Organisation. Die Inhalte bleiben dieselben; nur der Ort ihrer Speicherung ändert
sich. Quellen:
[9.9 Eine bestehende Website auf eine andere Engine umstellen](../../installation/09-datenbanken.md#99-eine-bestehende-site-auf-eine-andere-engine-umstellen)
und [Datenbankunterstützung](../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen).

## Inhalt

1. [Planen](#planen)
2. [Zwischen SQL-Engines](#zwischen-sql-engines)
3. [Nach Oracle](#nach-oracle)
4. [Nach MongoDB](#nach-mongodb)
5. [Die Einstellungen umstellen](#die-einstellungen-umstellen)
6. [Prüfen, bevor Sie wieder öffnen](#prüfen-bevor-sie-wieder-öffnen)
7. [Nach dem Umzug](#nach-dem-umzug)
8. [Checkliste](#checkliste)

## Planen

- **Ein Wartungsfenster**: Kein Redakteur darf zwischen dem Dump und dem Moment, in dem die Website auf der neuen Datenbank
  läuft, veröffentlichen, sonst geht diese Arbeit verloren.
- **Eine Probe**: Führen Sie den ganzen Umzug zuerst auf einer Kopie aus und messen Sie die Zeit.
- **Das Ziel vorbereitet**: Datenbank, Konto und Zeichensatz wie auf der Einrichtungsseite der Engine
  ([PostgreSQL](postgresql-einrichten.md), [MySQL und MariaDB](mysql-und-mariadb-einrichten.md), [MongoDB](mongodb-einrichten.md),
  [Oracle](oracle-einrichten.md)).
- **Eine Sicherung** der Quelle in ihrem nativen Format.
- **Erweiterungen geprüft** für die Ziel-Engine ([SQL von Erweiterungen prüfen](portables-sql-fuer-erweiterungen.md)).

## Zwischen SQL-Engines

SQLite, PostgreSQL, MariaDB, MySQL und Oracle mit `ezoracle`, über das engine-neutrale Format `.dba`. `--type` nimmt einen
Schema-Handler aus `settings/dbschema.ini`: `mysql`, `postgresql`, `sqlite3` und mit `ezoracle` `oracle`. Für SQLite ist das
Datenbankargument der Dateiname, wie in `Database`.

```bash
php bin/php/maintenance.php on --message="Moving the database" --allow-root-user
# dump schema and data of the current database (MySQL in this example)
php bin/php/ezsqldumpschema.php --type=mysql --host=localhost --user=exponential --password='<password>' \
    --format=generic --output-array --output-types=all exponential var/backup/site.dba --allow-root-user
# load them into the new one
php bin/php/ezsqlinsertschema.php --type=postgresql --host=localhost --user=exponential --password='<password>' \
    --insert-types=all --schema-file=var/backup/site.dba var/backup/site.dba exponential --allow-root-user
```

`--password=` ist in der Prozessliste sichtbar: Führen Sie den Umzug auf einer Maschine aus, die Sie kontrollieren, oder
verwenden Sie ein vorübergehendes Passwort und ändern Sie es danach. Führen Sie beide Skripte zuerst mit `--help` aus
([Schemawerkzeuge](schemawerkzeuge-dump-insert-diff.md)).

## Nach Oracle

Die Erweiterung hat eigene Übertragungsbefehle, `ext:ezoracle:mysql2oracle-schema` und `ext:ezoracle:mysql2oracle-data`
(Skripte in `extension/ezoracle/bin/php/`). Lesen Sie ihr `--help` und führen Sie danach `ext:ezoracle:sequence-sync` und
`ext:ezoracle:gather-stats` aus ([Wartungsbefehle und Cronjobs für Oracle](oracle-wartungsbefehle.md)).

## Nach MongoDB

Die Skripte in `bin/mongodb/` (`export_mysql.sh`, `mysql2ndjson.py`, `validate_ndjson.sh`, `fixup_ndjson.py`,
`import_all.sh`) und Abschnitt 18 der [Portierungsreferenz](../../bc/6.0/mongodb-kernel-unterstuetzung.md). Danach
`create_indexes.js` und ein Lauf der Cronjobs
([MongoDB: Grenzen und Betrieb](mongodb-grenzen-und-betrieb.md#eine-mysql-website-nach-mongodb-umziehen)).

## Die Einstellungen umstellen

Setzen Sie in `settings/override/site.ini.append.php` `DatabaseImplementation` und die Verbindungsschlüssel auf die neue
Engine (und `ActiveExtensions[]=ezoracle` für Oracle). Dann:

```bash
php bin/php/ezcache.php --clear-all --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user     # when Velocity serves the site
```

und laden Sie PHP-FPM neu. Ein vergessenes `DatabaseImplementation` ist der übliche Grund, warum nach einem Wechsel keine Seite
lädt.

## Prüfen, bevor Sie wieder öffnen

1. Zählen Sie die Zeilen großer Tabellen auf beiden Seiten; die Zahlen müssen übereinstimmen:

   ```sql
   SELECT COUNT(*) FROM ezcontentobject;
   SELECT COUNT(*) FROM ezcontentobject_tree;
   SELECT COUNT(*) FROM ezcontentobject_attribute;
   ```

2. Korrigieren Sie auf PostgreSQL die Sequenzen, wenn `ezsqlinsertschema.php` ein Problem mit ihnen gemeldet hat
   ([PostgreSQL betreiben](postgresql-betrieb.md#sequenzen-korrigieren)).
3. Führen Sie die Prüfung der Datenbankkonsistenz aus ([Das Schema prüfen](das-schema-nach-einem-upgrade-pruefen.md)).
4. Melden Sie sich in der Administration an, öffnen Sie einige Seiten und veröffentlichen Sie ein Testobjekt.
5. Bauen Sie den Suchindex neu auf, wenn Ergebnisse falsch aussehen: `php bin/php/updatesearchindex.php --clean --allow-root-user`.
6. Wartungsmodus aus.

## Nach dem Umzug

- **Spätere Upgrades** verwenden die Update-Dateien der Engine, auf der die Website **jetzt** läuft, nicht die, auf der sie
  installiert wurde.
- **Sicherungen** wechseln das Werkzeug: Passen Sie die Sicherungsjobs an und testen Sie eine Wiederherstellung auf der neuen
  Engine.
- **Überwachung** ändert sich: Richten Sie Ihre Prüfungen auf den neuen Server aus ([Die Datenbank überwachen](die-datenbank-ueberwachen.md)).
- Halten Sie die alte Datenbank eine Weile nur lesbar und entfernen Sie sie dann mitsamt ihren Zugangsdaten.

## Checkliste

- [ ] Wartungsfenster vereinbart; Probe durchgeführt.
- [ ] Zieldatenbank, Konto und Zeichensatz vorbereitet.
- [ ] Dump und Insert ohne Fehler abgeschlossen.
- [ ] Einstellungen umgestellt; Caches geleert; Worker neu gestartet.
- [ ] Zeilenzahlen stimmen überein; Konsistenzprüfung sauber; Testveröffentlichung erledigt.
- [ ] Sicherungen, Überwachung und Upgrade-Hinweise für die neue Engine angepasst.

## Verwandte Seiten

- [Die Datenbank-Engine wechseln](../hosting/migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md) (für Hosting-Unternehmen)
- [Eine Engine wählen](eine-engine-waehlen.md)
- [Eine Website zwischen Hosts umziehen](../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
