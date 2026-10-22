---
title: Portables SQL schreiben
audience: software-engineer, developer
category: software-engineering
order: 220
summary: Wie Sie Abfragen schreiben, die auf jeder von Exponential unterstützten Datenbank laufen - Maskieren und Casten, Limits als Parameter, die Dialektunterschiede jedes Treibers (Zeilenlimits, IDs, Verkettung, MD5, bitweise Operatoren, Updates mit Join, leere Zeichenketten, Groß- und Kleinschreibung) und wie Sie eine Abfrage auf mehr als einer Engine testen.
keywords: SQL, portables SQL, Dialekt, escapeString, Cast, LIMIT, OFFSET, Sequenzen, Verkettung, MD5, bitweise, UPDATE JOIN, leere Zeichenkette, Oracle, SQLite, PostgreSQL, MySQL, MongoDB, Softwareingenieur, portable SQL, dialect, cast, sequences, concatenation, bitwise, empty string
type: help
---
# Portables SQL schreiben

Diese Seite richtet sich an Ingenieure, die SQL in Kernel- oder Erweiterungscode schreiben. Exponential läuft auf sechs
Datenbanken; eine Abfrage, die auf Ihrer Entwicklungsdatenbank funktioniert, ist also nicht fertig, bis sie auf den
anderen funktioniert. Die Regeln und die Dialekttabelle stammen aus
[Standards für Datenbanken und SQL](../../standards/datenbanken-und-sql.md).

## Inhalt

1. [Die zwei verpflichtenden Regeln](#die-zwei-verpflichtenden-regeln)
2. [Die API dem SQL vorziehen](#die-api-dem-sql-vorziehen)
3. [Dialektunterschiede](#dialektunterschiede)
4. [Muster, die portabel bleiben](#muster-die-portabel-bleiben)
5. [Auf mehr als einer Engine testen](#auf-mehr-als-einer-engine-testen)

## Die zwei verpflichtenden Regeln

1. **Maskieren Sie jede Zeichenkette** mit `$db->escapeString( $value )`, innerhalb einfacher Anführungszeichen. Jeder
   Treiber verwendet die eigene Funktion der Engine; der Oracle-Treiber verdoppelt einfache Anführungszeichen.
2. **Casten Sie jede Zahl** mit `(int)`, bevor sie ins SQL geht. Um einen Cast lässt sich nicht herummaskieren.

Diese Regeln hat die Sicherheitskorrektur von 6.0.13 im Kernel wiederhergestellt (Ganzzahl-Casts, `escapeString()`, eine
Freigabeliste für Spalten in `ORDER BY`); dieselben Muster in Erweiterungscode werden durch eine Kernel-Aktualisierung
nicht behoben ([Sicherheitshärtung von 6.0.13](../../bc/6.0/haertung.md)).

## Die API dem SQL vorziehen

- `eZPersistentObject::fetchObjectList()` und die anderen Methoden der Persistenzobjekte maskieren für Sie.
- Inhaltsabfragen laufen über Fetch-Funktionen oder die Inhaltsklassen; sie wenden Berechtigungen an und funktionieren
  auf jeder Engine, MongoDB eingeschlossen.
- Übergeben Sie Offset und Limit als Parameter von `arrayQuery()`, damit jeder Treiber seine eigene Syntax schreibt.

## Dialektunterschiede

| Thema | SQLite 3 | PostgreSQL | MySQL, MariaDB | Oracle | MongoDB-Adapter |
|---|---|---|---|---|---|
| Zeilenlimit | `LIMIT offset, count` | `LIMIT count OFFSET offset` | `LIMIT offset, count` | `OFFSET n ROWS FETCH NEXT m ROWS ONLY` | `limit` und `skip` |
| Neue IDs | `lastInsertRowID()` | Sequenz `<table>_<column>_seq` | `AUTO_INCREMENT` | Sequenz `s_<table>` | ein Zählerdokument je Sequenz |
| Verkettung von Zeichenketten | `\|\|` | `\|\|` | `concat( ... )` | `\|\|` | `concat( ... )`, vom Adapter gelesen |
| MD5 | Benutzerfunktion `md5()` | braucht `pgcrypto` | `MD5()` | `STANDARD_HASH( s, 'MD5' )` | in PHP gehasht |
| `UPDATE ... JOIN` von MySQL | umgeschrieben (braucht SQLite 3.33) | nicht umgeschrieben | nativ | nicht umgeschrieben | nicht zutreffend |
| Leere Zeichenkette | `''` | `''` | `''` | als NULL gespeichert | nicht geprüft |
| Vergleich von Zeichenketten | `=` mit Groß- und Kleinschreibung | mit Groß- und Kleinschreibung | ohne Groß- und Kleinschreibung (Standard-Kollationen) | mit Groß- und Kleinschreibung | mit Groß- und Kleinschreibung, sofern keine Kollation gesetzt ist |
| Beginn einer Transaktion | `BEGIN IMMEDIATE` nach einer Schreibsperre | `BEGIN WORK` | `BEGIN WORK` | kein Auto-Commit | keiner |

Die vollständige Tabelle mit Quellen steht unter
[Dialektunterschiede, Treiber für Treiber](../../standards/datenbanken-und-sql.md#unterschiede-der-dialekte-treiber-für-treiber).

## Muster, die portabel bleiben

| Statt | Schreiben Sie |
|---|---|
| `LIMIT 10` in der SQL-Zeichenkette | `array( 'offset' => 0, 'limit' => 10 )` als Parameter |
| `UPDATE a JOIN b ...` | `UPDATE a SET ... WHERE id IN ( SELECT ... )` oder zwei Anweisungen in einer Transaktion |
| Vergleich mit `''` | behandeln Sie `''` und `NULL` gleich, weil Oracle die leere Zeichenkette als NULL speichert |
| Sich auf ein `=` ohne Groß- und Kleinschreibung verlassen | normalisieren Sie die Schreibung in PHP oder auf beiden Seiten |
| `SET @var := ...` | lesen Sie den Wert in PHP und übergeben Sie ihn an die nächste Anweisung |
| Eine Engine-Funktion (`GROUP_CONCAT`, `ILIKE`) | erledigen Sie die Arbeit in PHP oder verzweigen Sie nach Treiber, mit einem Rückfall für jede Engine |
| `ORDER BY` mit einem Spaltennamen aus der Anfrage | eine Freigabeliste erlaubter Spalten |

## Auf mehr als einer Engine testen

- SQLite braucht keinen Server: Führen Sie Ihren Code als zweite Engine gegen eine SQLite-Installation aus.
- Die CI führt die Datenbanktests auf MySQL 8.0 und PostgreSQL 15 (PHP 8.3) aus; reproduzieren Sie sie mit
  `tests/runtests.php --dsn ... --db-per-test` gegen eine Wegwerf-Datenbank ([CI-Gates](ci-gates.md)).
- Tests, die eine laufende Datenbank brauchen, lesen ihren Ort aus Umgebungsvariablen und werden übersprungen, wenn er
  nicht gesetzt ist ([Tests ohne Datenbank und Testsuiten](tests-ohne-datenbank-und-testsuiten.md)).

## Verwandte Seiten

- [Persistenz und die Datenbankschicht](persistenz-und-die-datenbankschicht.md)
- [Datenbankcode für PHP 8](../erweiterungsentwicklung/teil-4-aktualisieren/datenbankcode-fuer-php-8.md)
- [Kapitel 9, Hinweise über Engines hinweg](../../installation/09-datenbanken.md#98-hinweise-über-engines-hinweg)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
