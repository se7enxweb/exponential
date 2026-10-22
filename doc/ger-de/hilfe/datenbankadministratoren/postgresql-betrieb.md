---
title: "PostgreSQL betreiben: Sequenzen, Groß-/Kleinschreibung und Stolperfallen"
audience: dba
category: database-administration
order: 13
summary: Alltagswissen für den Betrieb von Exponential auf PostgreSQL - wie Auto-Increment-Spalten auf Sequenzen mit dem Namen table_column_seq abgebildet werden, wie Sie sie nach dem Laden von Daten korrigieren, die Umbenennung der Sequenzen, die Websites aus 5.x brauchen, Vergleiche mit Beachtung der Groß-/Kleinschreibung, persistente Verbindungen, Sicherungen und die Fehler, denen Sie am wahrscheinlichsten begegnen.
keywords: PostgreSQL, Sequenzen, setval, nextval, currval, _seq, Umbenennung von Sequenzen, duplicate key, Groß-/Kleinschreibung, lower, pg_pconnect, persistente Verbindung, pg_dump, pg_restore, Backticks, Stolperfallen, sequences, sequence rename, case sensitive, persistent connection, gotchas
type: help
---
# PostgreSQL betreiben: Sequenzen, Groß-/Kleinschreibung und Stolperfallen

Diese Seite richtet sich an Datenbankadministratoren, die eine Exponential-Website auf PostgreSQL betreiben. Sie
beantwortet: wie das Schema Auto-Increment-IDs behandelt, was nach einem Datenimport oder einer teilweisen Wiederherstellung
zu tun ist, was sich an den Namen der Sequenzen zwischen 5.x und 6.0 geändert hat, wie sich Vergleiche von MySQL
unterscheiden und welche Fehler häufig sind. Quellen: [9.4 PostgreSQL](../../installation/09-datenbanken.md#94-postgresql) und
[11.3 Die Verzeichnisse 6.12, 7.2 und 7.3](../../installation/11-aktualisieren.md#die-verzeichnisse-612-72-und-73).

## Inhalt

1. [Sequenzen](#sequenzen)
2. [Sequenzen korrigieren](#sequenzen-korrigieren)
3. [Namen der Sequenzen auf Websites aus 5.x](#namen-der-sequenzen-auf-websites-aus-5x)
4. [Groß-/Kleinschreibung und Vergleiche](#groß-kleinschreibung-und-vergleiche)
5. [Persistente Verbindungen](#persistente-verbindungen)
6. [Sicherungen](#sicherungen)
7. [Stolperfallen](#stolperfallen)

## Sequenzen

Auto-Increment-Spalten sind Ganzzahlen mit dem Standardwert `nextval('<table>_<column>_seq')`. Der Schema-Handler
(`lib/ezdbschema/classes/ezpgsqlschema.php`) legt jede Sequenz vor ihrer Tabelle an (`CREATE SEQUENCE IF NOT EXISTS`), und
`lastSerialID()` des Treibers liest nach einem Insert `currval('<table>_<column>_seq')`. Die IDs von `ezcontentobject`
kommen zum Beispiel aus `ezcontentobject_id_seq`.

## Sequenzen korrigieren

Werden Daten mit expliziten IDs geladen (ein Import, eine Wiederherstellung ausgewählter Tabellen, ein Umzug von einer
anderen Engine), bleiben die Sequenzen zurück, und der nächste Insert scheitert mit einem doppelten Schlüssel. Der
Schema-Installer korrigiert sie nach dem Laden von Daten selbst (`correctSequenceValues()`); für eine Tabelle von Hand:

```sql
SELECT setval('ezcontentobject_id_seq', (SELECT max(id) FROM ezcontentobject));
```

Der Kernel liefert `kernel/sql/postgresql/setval.sql` mit solchen Anweisungen für die Tabellen des Kernels. Lesen Sie die
Datei, bevor Sie sie ausführen, und führen Sie sie mit dem Konto der Website aus.

## Namen der Sequenzen auf Websites aus 5.x

Bis 5.4 hießen die Sequenzen `<table>_s`. Der Kernel 6.0 liest `<table>_<column>_seq`, auf einer Datenbank, die noch die
alten Namen hat, scheitert also jeder Insert, der seine neue ID braucht. Die Update-Datei von 5.4 auf 6.0.0
(`update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql`) benennt sie um, jede nur, wenn der alte Name existiert und der
neue frei ist, und richtet jeden Spaltenstandard auf den neuen Namen aus; die Datei von 6.0.0 auf 6.0.15 tut dasselbe
erneut für eine Website, die 6.0 mit einer älteren Kopie erreicht hat. Ihre abgesicherten Schritte sind `DO`-Blöcke.

Wenden Sie `update/database/postgresql/7.2/dbupdate-6.13.0-to-7.2.0.sql` nicht als Datei an: Sie stammt aus dem
Upstream-Kernel von 2018, beginnt bei einer Version, die keine Exponential-Datenbank hat, und ihre Änderung steckt bereits
in den 6.0-Dateien.

Prüfen Sie mit `\ds` in `psql`: Die Liste zeigt nur Namen der Form `<table>_<column>_seq`.

## Groß-/Kleinschreibung und Vergleiche

PostgreSQL vergleicht Zeichenketten mit Beachtung der Groß-/Kleinschreibung. Die Standard-Sortierfolgen von MySQL tun das
nicht. Eine für MySQL geschriebene Suche nach Login oder Bezeichner (in einer Erweiterung oder in Ihrer eigenen
Berichtsabfrage) findet auf PostgreSQL daher womöglich nichts. Vergleichen Sie `lower(...)` auf beiden Seiten, wo die
Schreibweise keine Rolle spielen darf ([Zeichensätze und Sortierfolgen](zeichensaetze-und-sortierfolgen.md)).

## Persistente Verbindungen

`UsePersistentConnection=enabled` lässt den Treiber `pg_pconnect` statt `pg_connect` verwenden und hält eine Verbindung je
PHP-Prozess. Unter den persistenten Workern von Velocity behält jeder Worker seine Verbindung ohnehin für seine ganze
Lebensdauer. Zählen Sie die Verbindungen, die Sie brauchen: PHP-FPM-Worker plus Velocity-Worker plus Cron, gegen
`max_connections` auf dem Server. Ein Connection-Pooler vor PostgreSQL ist eine serverseitige Entscheidung; Exponential hat
dafür keine eigene Einstellung, testen Sie ihn also vor der Produktion auf Staging.

## Sicherungen

```bash
pg_dump -U exponential -Fc exponential > exponential.dump
pg_restore -U exponential -d exponential --clean exponential.dump
```

Das Custom-Format (`-Fc`) ist komprimiert und lässt sich selektiv wiederherstellen. Wiederherstellung zu einem Zeitpunkt
und Streaming-Replikation sind eigene Funktionen von PostgreSQL und funktionieren mit Exponential wie mit jedem Client;
konfigurieren Sie sie auf dem Server ([Sicherungen je Engine](sicherungen-je-engine.md)).

## Stolperfallen

| Symptom | Ursache |
|---|---|
| "The 'digest' function is not available in your database ..." | `pgcrypto` fehlt; legen Sie es an ([PostgreSQL einrichten](postgresql-einrichten.md#pgcrypto)) |
| Connection refused | `listen_addresses` oder `pg_hba.conf` |
| Doppelter Schlüssel beim Insert nach einem Import | Sequenzen hinter ihren Tabellen; korrigieren Sie sie wie oben |
| Jeder Insert scheitert nach einem Upgrade von 5.x | Alte Sequenznamen `<table>_s`; wenden Sie die aktuelle Update-Datei 6.0 an |
| Das SQL einer Erweiterung scheitert nur auf PostgreSQL | MySQL-Syntax wie Quoting mit Backticks oder reine MySQL-`SET`-Anweisungen ([SQL von Erweiterungen prüfen](portables-sql-fuer-erweiterungen.md)) |
| Ein Login funktioniert nur in einer Schreibweise | Vergleich mit Beachtung der Groß-/Kleinschreibung |

`errorNumber()` von PostgreSQL ist in Exponential der SQLSTATE (eine Zeichenkette wie `'23505'` für einen doppelten
Schlüssel); vor 6.0.15 war er für jeden Fehler `1` ([Fehlercodes und Meldungen der Datenbank](fehlercodes-und-meldungen.md)).

## Verwandte Seiten

- [PostgreSQL einrichten](postgresql-einrichten.md)
- [Upgrades und die SQL-Update-Dateien](upgrades-und-sql-update-dateien.md)
- [PostgreSQL für Hoster](../hosting/grundlagen/postgresql-fuer-hoster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
