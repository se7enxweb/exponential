---
title: Zeichensätze und Sortierfolgen
audience: dba
category: database-administration
order: 23
summary: Wie Exponential Text auf jeder der sechs Engines als UTF-8 speichert, welchen Zeichensatz und welche Sortierfolge Sie anlegen, wie sich die Verbindung von MySQL und MariaDB von den Tabellen unterscheidet, wie sich Vergleiche mit und ohne Beachtung der Groß-/Kleinschreibung zwischen Engines unterscheiden und wie Sie eine laufende Website prüfen.
keywords: Zeichensatz, Charset, Sortierfolge, Kollation, UTF-8, UTF8, utf8mb4, utf8mb3, AL32UTF8, BSON, template0, Groß-/Kleinschreibung, LIKE, lower, NLS_COMP, OracleCaseInsensitive, latin1, Systeminformationen, character set, collation, case sensitive, case insensitive, System information
type: help
---
# Zeichensätze und Sortierfolgen

Diese Seite richtet sich an Datenbankadministratoren, die die Datenbank einer Exponential-Website anlegen oder eine
bestehende auf Kodierungsprobleme prüfen. Exponential speichert und liefert Text als UTF-8; jede Engine benennt das anders
und vergleicht Zeichenketten anders. Diese Seite beantwortet: was Sie auf jeder Engine anlegen, wo sich die Verbindung von
den Tabellen unterscheiden kann und wie sich die Schreibweise auf Suchen auswirkt. Quelle:
[Datenbankunterstützung, Zeichensätze und Sortierfolgen](../datenbankunterstuetzung.md#zeichensätze-und-kollationen).

## Inhalt

1. [Was Sie anlegen, je Engine](#was-sie-anlegen-je-engine)
2. [Die Verbindung von MySQL und MariaDB](#die-verbindung-von-mysql-und-mariadb)
3. [Groß-/Kleinschreibung und Vergleiche](#groß-kleinschreibung-und-vergleiche)
4. [Eine laufende Website prüfen](#eine-laufende-website-prüfen)
5. [Eine alte Datenbank umwandeln](#eine-alte-datenbank-umwandeln)
6. [Checkliste](#checkliste)

## Was Sie anlegen, je Engine

| Engine | Legen Sie die Datenbank an mit | Hinweise |
|---|---|---|
| SQLite 3 | nichts zu wählen | SQLite speichert Text als UTF-8 |
| PostgreSQL | `--encoding=UTF8 --template=template0` | `template0` lässt Sie die Kodierung unabhängig vom Standard des Servers wählen |
| MongoDB | nichts zu wählen | BSON-Zeichenketten sind UTF-8 |
| MariaDB | `CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci` | Der Assistent lehnt eine Datenbank ab, deren Standardzeichensatz nicht UTF-8 ist |
| Oracle | Datenbankzeichensatz `AL32UTF8` | Der Treiber verbindet sich in AL32UTF8; `NLS_LANG` wird nicht gebraucht |
| MySQL | `CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci` | Wie MariaDB |

Der Oracle-Zeichensatz wird beim Anlegen der Datenbank gewählt; er ist eine Eigenschaft der ganzen Datenbank, ein Schema für
Exponential gehört also in eine `AL32UTF8`-Datenbank.

## Die Verbindung von MySQL und MariaDB

`site.ini [DatabaseSettings] Charset=utf-8` (der Standard) wird als `utf8` an den Server gesendet, was beide Server als das
Drei-Byte-`utf8mb3` behandeln. Zeichen außerhalb der Basic Multilingual Plane, etwa Emoji, können die Verbindung nicht
passieren, selbst wenn die Tabellen `utf8mb4` sind. Halten Sie die Datenbank trotzdem in `utf8mb4`.

Lässt sich der Zeichensatz der Verbindung nicht setzen, läuft der Treiber weiter und protokolliert "could not set the
connection charset"; Text kommt dann mit `?` anstelle der fehlenden Zeichen zurück. Die Warnung wird nur protokolliert, wenn
Warnungen protokolliert werden (`site.ini [DebugSettings] AlwaysLog[]=warning`). Details:
[MySQL und MariaDB: utf8mb4, der Verbindungszeichensatz und InnoDB](mysql-und-mariadb-zeichensatz-und-innodb.md).

## Groß-/Kleinschreibung und Vergleiche

| Engine | `=` | `LIKE` | Wie Sie es ändern |
|---|---|---|---|
| SQLite 3 | mit Beachtung | ignoriert sie nur für ASCII-Buchstaben | |
| PostgreSQL | mit Beachtung | mit Beachtung (`ILIKE` ignoriert sie) | `lower(...)` vergleichen |
| MongoDB | mit Beachtung | | eine Collation auf der Abfrage oder dem Index |
| MariaDB, MySQL | Standard-Sortierfolgen ignorieren sie | ignoriert sie | eine binäre oder `_cs`-Sortierfolge |
| Oracle | mit Beachtung (binär) | mit Beachtung | `OracleCaseInsensitive=enabled` (`NLS_COMP=LINGUISTIC`, `NLS_SORT`), dazu linguistische Indizes aus `ext:ezoracle:ci-indexes --create` |

Die Folge: Eine Suche nach Login oder Bezeichner, die gegen die Sortierfolgen von MySQL ohne Beachtung der
Groß-/Kleinschreibung geschrieben wurde, findet auf PostgreSQL, Oracle oder MongoDB womöglich nichts. Der eigene Code von
Exponential berücksichtigt das; Erweiterungen und Ihre eigenen Berichtsabfragen müssen `lower(...)` auf beiden Seiten
vergleichen, wo die Schreibweise keine Rolle spielen darf
([SQL von Erweiterungen für jede Engine prüfen](portables-sql-fuer-erweiterungen.md)).

Benutzer-Logins sind ein Sonderfall: `exp:userlogincheck` listet Logins, die mehr als ein Benutzerkonto hat (zum Beispiel
derselbe Login in zwei Schreibweisen), und `--fix-index` fügt den eindeutigen Index auf `ezuser.login` hinzu, sobald es keine
mehr gibt (`php bin/php/console list` beschreibt es).

## Eine laufende Website prüfen

- **Setup > Systeminformationen**, Karte Datenbank: der Zeichensatz der Verbindung; die Seite warnt, wenn er nicht UTF-8 ist
  ([Systeminformationen](../../anleitungen/systeminformationen.md)).
- MySQL und MariaDB:

  ```sql
  SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME
    FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = 'exponential';
  SELECT TABLE_NAME, TABLE_COLLATION FROM information_schema.TABLES
   WHERE TABLE_SCHEMA = 'exponential' AND TABLE_COLLATION NOT LIKE 'utf8%';
  ```

- PostgreSQL: `psql -l` zeigt die Kodierung jeder Datenbank.
- Oracle: `SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET';`

## Eine alte Datenbank umwandeln

Eine Datenbank aus einem alten Release in einem Einzelbyte-Zeichensatz wird mit `bin/php/ezconvertdbcharset.php` umgewandelt
(nur MySQL, PostgreSQL und Oracle). Geben Sie die Sortierfolge ausdrücklich an; ihr Standard ist `utf8_general_ci`. Führen Sie
es nach einer Sicherung auf einer Kopie aus
([MySQL und MariaDB: utf8mb4](mysql-und-mariadb-zeichensatz-und-innodb.md#eine-alte-datenbank-nach-utf-8-umwandeln)).

## Checkliste

- [ ] Die Datenbank wurde im UTF-8-Zeichensatz der Engine angelegt.
- [ ] MySQL und MariaDB: Jede Tabelle ist `utf8mb4`; keine Zeichensatzwarnung in `var/log/warning.log`.
- [ ] Oracle: `NLS_CHARACTERSET` ist `AL32UTF8`.
- [ ] Erweiterungen und Berichte vergleichen ohne Beachtung der Groß-/Kleinschreibung, wo sie müssen.

## Verwandte Seiten

- [MySQL und MariaDB einrichten](mysql-und-mariadb-einrichten.md), [PostgreSQL einrichten](postgresql-einrichten.md), [Oracle einrichten](oracle-einrichten.md)
- [9.8 Hinweise über Engines hinweg](../../installation/09-datenbanken.md#98-hinweise-über-engines-hinweg)
- [Sprachen](../sprachen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
