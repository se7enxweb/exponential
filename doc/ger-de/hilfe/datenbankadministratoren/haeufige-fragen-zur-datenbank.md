---
title: Häufige Fragen zur Datenbank
audience: dba
category: database-administration
order: 46
summary: Kurze Antworten auf die Fragen, die Datenbankadministratoren am häufigsten zu Exponential stellen - welche Engine, reicht SQLite wirklich, welche Rechte, wo das Passwort liegt, wie Sie sichern und aktualisieren, Replikate, Clustering, SQL Server, Grenzen von MongoDB, Schreiben in die Datenbank von Hand und mehr - jeweils mit einem Link auf die vollständige Seite.
keywords: FAQ, häufige Fragen, Fragen, Datenbank, SQLite in der Produktion, welche Datenbank, Rechte, root, Passwort, Sicherung, Upgrade, Replikate, Cluster, SQL Server, MongoDB, Oracle, utf8mb4, Emoji, Abfrage-Cache, Handbearbeitung, Version, questions, database, SQLite production, which database, privileges, password, backup, upgrade, replicas, query cache, hand edits
type: help
---
# Häufige Fragen zur Datenbank

Diese Seite richtet sich an Datenbankadministratoren, die eine schnelle Antwort möchten, bevor sie eine vollständige Seite lesen.
Jede Antwort verlinkt auf die Seite mit den Details.

## Inhalt

1. [Wählen und installieren](#wählen-und-installieren)
2. [Konten und Sicherheit](#konten-und-sicherheit)
3. [Die Datenbank betreiben](#die-datenbank-betreiben)
4. [Upgrades und Umzüge](#upgrades-und-umzüge)
5. [Skalierung](#skalierung)
6. [Fragen zu einzelnen Engines](#fragen-zu-einzelnen-engines)

## Wählen und installieren

**Welche Datenbanken werden unterstützt?** Sechs, in dieser Reihenfolge der Präferenz: SQLite 3 (der Standard), PostgreSQL,
MongoDB, MariaDB, Oracle, MySQL. Microsoft SQL Server ist bekanntermaßen mit zusätzlicher Arbeit möglich
([Unterstützte Engines und Versionen](unterstuetzte-engines-und-versionen.md)).

**Taugt SQLite wirklich für die Produktion?** Für eine Website, die von einer Maschine ausgeliefert wird, ja: Der Treiber fügt
WAL, Verbindungseinstellungen für einen Webserver und eine Schreibwarteschlange hinzu, und Caches beantworten die meisten
Seitenaufrufe. Wählen Sie eine Server-Engine für mehrere Webserver, starkes gleichzeitiges Veröffentlichen, Replikation oder
Datenbankrechte ([Eine Engine wählen](eine-engine-waehlen.md)).

**Welche Mindestversionen?** SQLite-Bibliothek 3.33, PostgreSQL 13, MongoDB 6.0 mit PHP 8.2, Oracle 19c; MySQL und MariaDB ein
Release, das Ihr Hersteller unterstützt, mit InnoDB und `utf8mb4` ([Versionen](unterstuetzte-engines-und-versionen.md#die-versionstabelle)).

**Legt der Installer die Datenbank und den Benutzer an?** Nein. Legen Sie sie zuerst an; die Installer legen die Tabellen an
([PostgreSQL einrichten](postgresql-einrichten.md), [MySQL und MariaDB](mysql-und-mariadb-einrichten.md)).

## Konten und Sicherheit

**Welche Rechte braucht die Website?** Volle Rechte auf ihre eigene Datenbank und sonst nichts; auf MySQL die Liste unter
[Datenbankkonten und Rechte](datenbankkonten-und-rechte.md), auf PostgreSQL die Eigentümerschaft der Datenbank.

**Kann die Website ohne DDL-Rechte laufen?** Das Projekt dokumentiert und testet diese Trennung nicht; dasselbe Konto wendet die
Update-Dateien an. Testen Sie sie auf Staging, wenn Ihre Richtlinie sie verlangt.

**Wo liegt das Datenbankpasswort?** In `settings/override/site.ini.append.php`. Halten Sie es aus der Versionsverwaltung heraus,
Modus 640 ([Die Datenbank absichern](die-datenbank-absichern.md)).

**Zeigt die Administration das Passwort?** Nein. Geheimnisse werden überall maskiert, und der Datenbankbenutzer ist im Bericht
der Systeminformationen immer verborgen.

## Die Datenbank betreiben

**Wie sichere ich sie?** Mit dem Werkzeug der Engine: `.backup` für SQLite, `pg_dump`, `mysqldump --single-transaction`,
`mongodump`, Data Pump. Dazu `var/storage` und `settings/override` ([Sicherungen je Engine](sicherungen-je-engine.md)).

**Kann ich die SQLite-Datei kopieren?** Nicht, solange sie in Gebrauch ist. Verwenden Sie `.backup` oder `VACUUM INTO`.

**Darf ich Daten von Hand mit SQL ändern?** Vermeiden Sie es; verwenden Sie die Administration oder die Skripte. Wenn es sein
muss, leeren Sie danach die Caches, den SQL-Abfrage-Cache eingeschlossen
([Der SQL-Abfrage-Cache](sql-abfrage-cache.md#schreibzugriffe-außerhalb-von-exponential)).

**Welche Cronjobs berühren die Datenbank?** Der Standardteil, `frequent` und `infrequent`; `session_gc.php` steht in keinem
mitgelieferten Teil ([Aufräumen](aufraeumen-cronjobs-und-warteschlangen.md)).

**Liegen Sessions in der Datenbank?** Nur mit `[Session] Handler=ezpSessionHandlerDB`
([Sessions in der Datenbank](sessions-in-der-datenbank.md)).

## Upgrades und Umzüge

**Auf welcher Version steht meine Datenbank?** `SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';`
([Upgrades](upgrades-und-sql-update-dateien.md#auf-welcher-version-steht-die-datenbank)).

**Welche Update-Dateien wende ich an?** Die Ihrer Engine und Ihres Weges, der Reihe nach, jede einmal; nie `6.12/`, `7.2/`,
`7.3/` oder `unstable/` ([Upgrades und die SQL-Update-Dateien](upgrades-und-sql-update-dateien.md)).

**Wie prüfe ich danach das Schema?** **Setup > Upgrade-Prüfung**, "Überprüfe Datenbankkonsistenz", oder `ezsqldiff.php`
([Das Schema prüfen](das-schema-nach-einem-upgrade-pruefen.md)).

**Kann ich von MySQL auf PostgreSQL umziehen?** Ja, über einen `.dba`-Dump und Insert in einem Wartungsfenster
([Eine Website auf eine andere Engine umziehen](zwischen-engines-wechseln.md)).

## Skalierung

**Unterstützt Exponential Lesereplikate?** Auf MySQL und MariaDB mit `UseSlaveServer`; auf den anderen Engines ist Replikation
Sache des Servers ([Lesereplikate](mysql-lesereplikate.md)).

**Können mehrere Webserver eine Datenbank teilen?** Ja, auf jeder Engine außer SQLite; Dateien werden dann mit dem DFS-Cluster
geteilt ([Clustering](clustering-dfs-und-die-datenbank.md)).

**Wie finde ich langsame Abfragen?** `SQLOutput` und `SlowQueriesOutput` in der Debug-Ausgabe für Ihre Adresse, der
SQL-Profiler und das Log langsamer Abfragen Ihrer Engine ([Langsame Abfragen finden](langsame-abfragen-finden.md)).

**Sollte ich den Abfrage-Cache einschalten?** Versuchen Sie `request`, dann `shared`, und messen Sie; denken Sie daran, ihn nach
Schreibzugriffen außerhalb von Exponential zu leeren ([Der SQL-Abfrage-Cache](sql-abfrage-cache.md)).

## Fragen zu einzelnen Engines

**Warum gehen Emoji auf MySQL verloren?** Die Verbindung verwendet `utf8`, das Drei-Byte-`utf8mb3` ist
([utf8mb4](mysql-und-mariadb-zeichensatz-und-innodb.md#der-verbindungszeichensatz)).

**Warum braucht PostgreSQL pgcrypto?** Der Treiber berechnet MD5 mit `digest()` ([PostgreSQL einrichten](postgresql-einrichten.md#pgcrypto)).

**Was unterstützt MongoDB nicht?** Einige Funktionen, aufgeführt unter [MongoDB: Grenzen und Betrieb](mongodb-grenzen-und-betrieb.md);
unübersetztes SQL liefert leere Ergebnisse und protokolliert `MONGO TODO`.

**Wird Oracle von Haus aus unterstützt?** Über die Erweiterung `ezoracle`, in jeder Installation vorhanden und für
Oracle-Websites aktiviert, ab 6.0.15 ([Oracle einrichten](oracle-einrichten.md)).

**Kann ich Microsoft SQL Server verwenden?** Nicht in 6.0.x ([Microsoft SQL Server: Stand](stand-von-microsoft-sql-server.md)).

## Verwandte Seiten

- [Datenbankadministratoren: hier beginnen](datenbankadministratoren-hier-beginnen.md)
- [Glossar für Datenbankadministratoren](glossar-fuer-datenbankadministratoren.md)
- [Datenbankfehler beheben](datenbankfehler-beheben.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
