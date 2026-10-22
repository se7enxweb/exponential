---
title: "Schemawerkzeuge: Dump, Insert und Diff"
audience: dba
category: database-administration
order: 27
summary: Die drei Kommandozeilen-Schemawerkzeuge von Exponential - ezsqldumpschema.php, um Schema und Daten als .dba oder SQL zu dumpen, ezsqlinsertschema.php, um sie in jede SQL-Engine zu laden, und ezsqldiff.php, um zwei Schemas mit Exit-Code zu vergleichen - mit ihren Optionen, wie ihre Hilfe sie ausgibt, sicherer Verwendung und Beispielen.
keywords: ezsqldumpschema, ezsqlinsertschema, ezsqldiff, exp:ezsqldumpschema, exp:ezsqlinsertschema, exp:ezsqldiff, dba, Schema dumpen, Schema einfügen, Schemavergleich, --output-types, --insert-types, --clean-existing, --check-only, --lint-check, --reverse, Exit-Code, dump schema, insert schema, schema diff, exit code
type: help
---
# Schemawerkzeuge: Dump, Insert und Diff

Diese Seite richtet sich an Datenbankadministratoren, die Exponential-Schemas dumpen, laden oder vergleichen. Drei Skripte in
`bin/php/` erledigen das, jedes auch über die Konsole verfügbar (`exp:ezsqldumpschema`, `exp:ezsqlinsertschema`,
`exp:ezsqldiff`). Die Optionen unten sind aus ihrer Ausgabe von `--help` in 6.0.15 übernommen; führen Sie `--help` auf Ihrem
Release aus, bevor Sie sich auf eine verlassen.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [ezsqldumpschema.php](#ezsqldumpschemaphp)
3. [ezsqlinsertschema.php](#ezsqlinsertschemaphp)
4. [ezsqldiff.php](#ezsqldiffphp)
5. [Beispiele](#beispiele)
6. [Checkliste](#checkliste)

## Bevor Sie beginnen

- Die Option `--password=` setzt das Passwort auf die Kommandozeile, sichtbar in der Prozessliste. Führen Sie die Werkzeuge
  auf einer Maschine aus, die Sie kontrollieren, oder mit einem vorübergehenden Passwort.
- `--type` nimmt einen Typ von Schema-Handler aus `settings/dbschema.ini`; für SQLite ist das Datenbankargument der Dateiname,
  wie in `Database`. Die eigene Hilfe der Skripte listet nur einige Typen; `sqlite3` und, mit `ezoracle`, `oracle`
  funktionieren ebenfalls ([9.9](../../installation/09-datenbanken.md#99-eine-bestehende-site-auf-eine-andere-engine-umstellen)).
- Fügen Sie `--allow-root-user` hinzu, wenn Sie sie als root ausführen.

## ezsqldumpschema.php

`ezsqldumpschema.php [OPTION]... [DATABASE] [FILENAME]` dumpt das Schema, die Daten oder beides in eine Datei oder auf die
Standardausgabe.

| Option | Bedeutung |
|---|---|
| `--type`, `--host`, `--port`, `--socket`, `--user`, `--password` | die Quelldatenbank (`--socket` nur MySQL) |
| `--output-array` | eine Datei mit Array-Strukturen (für Menschen lesbar): das Format `.dba` |
| `--output-serialized` | serialisierte Daten (spart Platz) |
| `--output-sql` | SQL für die Quell-Engine |
| `--format=generic` oder `local` | `generic` passt zu allen Datenbanken (Standard); `local` nur zu der, aus der gedumpt wurde |
| `--output-types=schema`, `data`, `all` | was gedumpt wird (Standard: nur Schema) |
| `--diff-friendly` | SQL-Ausgabe, die `diff` besser verträgt |
| `--meta-data` | zusätzliche engine-spezifische Informationen |
| `--table-type`, `--table-charset` | für SQL-Ausgabe (MySQL-Tabellentypen: bdb, innodb, myisam) |
| `--compatible-sql`, `--no-sort`, `--allow-multi-insert` | Details der Ausgabe |
| `--schema-file` | nur beim Dumpen aus Dateien |

## ezsqlinsertschema.php

`ezsqlinsertschema.php [OPTION]... [FILENAME] [DATABASE]` lädt ein `.dba`-Schema und Daten in eine Datenbank.

| Option | Bedeutung |
|---|---|
| `--type`, `--host`, `--port`, `--socket`, `--user`, `--password` | die Zieldatenbank |
| `--insert-types=schema`, `data`, `all`, `none` | was geladen wird (Standard: nur Schema); `none` mit `--clean-existing` räumt nur auf |
| `--schema-file` | die Schemadatei, erforderlich beim Import eines Schemas |
| `--table-type`, `--table-charset` | Tabellenoptionen für das Ziel |
| `--allow-multi-insert` | mehrzeilige Inserts, wo die Engine sie unterstützt |
| `--clean-existing` | **entfernt alle Datenbankobjekte** vor dem Laden |

`--clean-existing` leert die Zieldatenbank. Prüfen Sie das Argument `DATABASE` zweimal und richten Sie es nie versehentlich auf
eine Produktionsdatenbank. Auf PostgreSQL korrigiert der Installer nach dem Laden von Daten die Sequenzen.

## ezsqldiff.php

`ezsqldiff.php [OPTION]... [SOURCE] [MATCH]` zeigt die Unterschiede zwischen zwei Schemas und setzt den Exit-Code: 0, wenn sie
übereinstimmen, 1, wenn sie sich unterscheiden.

| Option | Bedeutung |
|---|---|
| `--type`, `--host`, `--user`, `--password`, `--socket` | eine Verbindung für beide Seiten |
| `--source-*`, `--match-*` | getrennte Verbindungen je Seite |
| `--check-only` | kein SQL, nur der Exit-Code |
| `--reverse` | die Unterschiede umkehren |
| `--lint-check` | das Quellschema gegen die Namensregeln prüfen, statt zu vergleichen ([Das .dba-Schemaformat](das-dba-schemaformat.md#der-lint-checker)) |

Typen: `mysql` und `postgresql`. Eine Seite kann eine `.dba`-Datei sein. Um Ihre Datenbank mit dem Release zu vergleichen,
geben Sie das Referenzschema **zuerst** an; die Ausgabe ist das SQL, das Ihre Datenbank in die Referenz verwandeln würde. Lesen
Sie es, führen Sie es nicht aus: Eine Zeile `DROP TABLE` nennt oft eine Tabelle Ihrer eigenen Erweiterung
([11.7](../../installation/11-aktualisieren.md#117-das-upgrade-überprüfen)).

## Beispiele

Schema und Daten einer MySQL-Website in eine portable Datei dumpen:

```bash
php bin/php/ezsqldumpschema.php --type=mysql --host=localhost --user=exponential --password='<password>' \
    --format=generic --output-array --output-types=all exponential var/backup/site.dba --allow-root-user
```

In PostgreSQL laden:

```bash
php bin/php/ezsqlinsertschema.php --type=postgresql --host=localhost --user=exponential --password='<password>' \
    --insert-types=all --schema-file=var/backup/site.dba var/backup/site.dba exponential --allow-root-user
```

Eine MySQL-Datenbank mit dem Schema des Releases vergleichen:

```bash
php bin/php/ezsqldiff.php --type=mysql --user=exponential --password='<password>' share/db_schema.dba exponential --allow-root-user
echo $?        # 0: the same, 1: differences
```

Halten Sie Dumps aus dem Web-Root heraus: `var/backup/` liegt unter `var/`, das die mitgelieferten Rewrite-Regeln nicht
ausliefern, aber prüfen Sie Ihren Server wie bei der SQLite-Datei ([Die Datenbank absichern](die-datenbank-absichern.md)).

## Checkliste

- [ ] `--help` auf Ihrem Release gelesen.
- [ ] Passwörter auf der Kommandozeile nur auf einer Maschine, die Sie kontrollieren.
- [ ] `--clean-existing` nur gegen ein Ziel verwendet, das Sie leeren wollten.
- [ ] Ausgabe des Diffs gelesen und geprüft, nie direkt in die Datenbank geleitet.

## Verwandte Seiten

- [Eine Website auf eine andere Engine umziehen](zwischen-engines-wechseln.md)
- [Das Schema nach einem Upgrade prüfen](das-schema-nach-einem-upgrade-pruefen.md)
- [Ein portabler Dump zwischen SQL-Engines](../hosting/betrieb/datenbank-backups-je-engine.md#ein-portabler-dump-zwischen-sql-engines)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
