---
title: "Lektion 9.4: Sicheres SQL mit eZDB"
audience: trainer, developer
category: training
order: 94
summary: Die Teilnehmenden schreiben mit eZDB die Abfragen, die sich mit persistenten Objekten nicht ausdrücken lassen, escapen jeden String und wandeln jede Zahl um, teilen Ergebnisse mit arrayQuery in Seiten auf, fassen Schreibvorgänge in Transaktionen zusammen und erkennen die Unterschiede zwischen Dialekten und Datenbanken, die rohes SQL weniger portabel machen. 45 Minuten.
keywords: entwickler, eZDB, arrayQuery, query, escapeString, int-umwandlung, SQL-injection, begin, commit, rollback, transaktion, offset, limit, dialekte, SQLite, MySQL, PostgreSQL, MongoDB, datenbankstandard, sicheres sql, developers course, transaction, dialects, databases standard, SQL safely
type: help
---
# Lektion 9.4: Sicheres SQL mit eZDB

Manchmal braucht ein Bericht oder eine Aufräumarbeit eine Abfrage, die sich mit persistenten Objekten nicht ausdrücken
lässt: einen Join, ein Group by, ein Update vieler Zeilen. `eZDB` führt solches SQL auf der konfigurierten Datenbank
aus. Hier lagen auch die meisten Sicherheitslücken alter Erweiterungen: ein Wert aus einer Anfrage, der ohne Escaping
in SQL eingefügt wurde. In dieser Lektion befolgen die Teilnehmenden die zwei Regeln, die das verhindern, und lernen,
was sich zwischen Datenbanken ändert.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 9: Die Konsole und persistente Objekte](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Experten |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 9.3](persistente-objekte.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Leseabfrage mit `$db->arrayQuery()` ausführen und mit `offset` und `limit` in Seiten aufteilen;
- jeden String mit `$db->escapeString()` innerhalb einfacher Anführungszeichen escapen und jede Zahl mit `(int)`
  umwandeln;
- Schreibvorgänge mit `$db->begin()`, `$db->commit()` und `$db->rollback()` zusammenfassen;
- Unterschiede der Dialekte zwischen SQLite, MySQL und PostgreSQL sowie die Grenzen unter MongoDB nennen;
- zwischen einem persistenten Objekt und rohem SQL wählen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Standards für Datenbanken und SQL](../../../../standards/datenbanken-und-sql.md), die Abschnitte über die
eZDB-Regeln und die Unterschiede der Dialekte.

1. **Die zwei Regeln** (5 Minuten). Jeder String aus einer Anfrage, einer Einstellung oder der Datenbank wird mit
   `$db->escapeString( $value )` innerhalb einfacher Anführungszeichen escapt; jede Zahl wird mit `(int)`
   umgewandelt, bevor sie ins SQL gelangt. Zeigen Sie das Beispiel:

   ```php
   $db = eZDB::instance();
   $rows = $db->arrayQuery(
       "SELECT id, name FROM ezsection WHERE identifier = '" . $db->escapeString( $identifier ) . "'"
       . " AND id <> " . (int)$excludeId,
       array( 'offset' => 0, 'limit' => 10 )
   );
   ```

2. **Ergebnisse** (3 Minuten). `arrayQuery()` gibt ein Array von Zeilen zurück (Spalte zu Wert); sein zweites
   Argument teilt das Ergebnis in der eigenen Syntax der Datenbank in Seiten auf. `query()` führt eine Anweisung aus
   und gibt `true` oder `false` zurück.
3. **Transaktionen** (4 Minuten). `begin()` und `commit()` lassen sich verschachteln und werden gezählt; nur das
   äußerste Paar sendet `BEGIN` und `COMMIT`. `rollback()` macht die ganze Transaktion rückgängig. Unter SQLite sorgt
   eine Schreibsperre dafür, dass Schreibvorgänge nacheinander laufen.
4. **Dialekte** (5 Minuten). Zeilenbegrenzungen, neue IDs, das Verketten von Strings, Teilstrings und
   `UPDATE ... JOIN` unterscheiden sich je nach Datenbank; überlassen Sie `offset` und `limit` von `arrayQuery()` und
   den persistenten Objekten, was sie übernehmen können. Unter MongoDB gibt SQL, das der Adapter nicht übersetzen kann,
   ein leeres Ergebnis mit einer Warnung zurück, und Transaktionen bewirken nichts.
5. **Die Wahl** (3 Minuten). Zuerst persistente Objekte; rohes SQL für Berichte und Massenänderungen, immer mit den
   zwei Regeln.

### Häufige Fragen

**Reicht `escapeString()` für eine Zahl?** Nein: Eine Zahl außerhalb von Anführungszeichen lässt sich nicht durch
Escaping absichern; wandeln Sie sie um.

**Kann ich Prepared Statements verwenden?** Die Schnittstelle des Kernels baut SQL als Strings; die zwei Regeln sind
ihr Schutz. Befolgen Sie sie ausnahmslos.

**Wo sind die Sicherheitskorrekturen älterer Releases beschrieben?** In den Hinweisen zur Härtung in 6.0.13, die vom
Datenbankstandard aus verlinkt sind.

### Stolperfallen

- **`LIMIT` von Hand geschrieben.** Es unterscheidet sich je nach Datenbank; verwenden Sie die Parameter `offset` und
  `limit`.
- **Eine offen gelassene Transaktion.** Ein `begin()` ohne `commit()` oder `rollback()` auf einem Fehlerpfad hält
  Sperren; schreiben Sie das Rollback in den Fehlerzweig.
- **Nur auf SQLite testen.** Eine Abfrage, die unter SQLite funktioniert, kann unter MySQL oder PostgreSQL
  fehlschlagen; testen Sie auf der Datenbank der Live-Website.

## Praktische Übung

Auf der Entwicklungsinstallation, mit der Tabelle `mysite_greeting` aus Lektion 9.3.

1. Legen Sie `scripts/report.php` an:

   ```php
   <?php
   // Greetings per name: php bin/php/ezexec.php extension/mysite_hello/scripts/report.php
   $cli = eZCLI::instance();
   $db = eZDB::instance();
   $since = time() - 86400;
   $rows = $db->arrayQuery( "SELECT name, COUNT(*) AS total FROM mysite_greeting"
                            . " WHERE created > " . (int)$since
                            . " GROUP BY name ORDER BY total DESC",
                            array( 'offset' => 0, 'limit' => 10 ) );
   foreach ( $rows as $row )
   {
       $cli->output( $row['name'] . ': ' . $row['total'] );
   }
   ```

2. Führen Sie es mit `ezexec.php` aus.
3. Fügen Sie eine Suche nach einem Namen aus einer Umgebungsvariablen hinzu, escapt:

   ```php
   $name = (string)getenv( 'GREET_NAME' );
   $count = $db->arrayQuery( "SELECT COUNT(*) AS total FROM mysite_greeting WHERE name = '"
                             . $db->escapeString( $name ) . "'" );
   $cli->output( 'Greetings for ' . $name . ': ' . $count[0]['total'] );
   ```

4. Führen Sie es mit `GREET_NAME="Ada" php bin/php/ezexec.php extension/mysite_hello/scripts/report.php` aus, dann
   mit `GREET_NAME="x' OR '1'='1"`. Vergleichen Sie: Der zweite Lauf findet keine Zeilen, weil das
   Anführungszeichen escapt wird.
5. Fügen Sie eine Transaktion hinzu, die alle Zeilen `Ada` umbenennt und ein Rollback ausführt:

   ```php
   $db->begin();
   $db->query( "UPDATE mysite_greeting SET name = 'Ada L.' WHERE name = 'Ada'" );
   $db->rollback();
   ```

   Führen Sie den Bericht erneut aus: Nichts hat sich geändert.
6. Lesen Sie die Dialekttabelle im Datenbankstandard und finden Sie heraus, wie unter PostgreSQL eine
   Zeilenbegrenzung geschrieben wird.
7. Committen Sie das Skript.

## Erwartetes Ergebnis

- Schritt 2 gibt pro Name eine Zeile mit seiner Anzahl aus.
- Schritt 4: eine Anzahl für `Ada` und `0` für den Injection-Versuch.
- Schritt 5: Die Namen sind nach dem Rollback unverändert.
- Schritt 6: `LIMIT count OFFSET offset`.
- Der Trainer lässt alle Teilnehmenden die zwei Regeln nennen.

## Quiz

1. Wie lauten die zwei Regeln für Werte in SQL?
2. Welche Methode gibt die Zeilen eines `SELECT` zurück?
   A. `query()` B. `arrayQuery()` C. `begin()` D. `escapeString()`
3. Wie werden `offset` und `limit` angegeben, damit sie auf jeder Datenbank funktionieren?
4. Was passiert bei verschachtelten Aufrufen von `begin()` und `commit()`?
5. Warum ist ein persistentes Objekt vorzuziehen, wo eines passt?

<details>
<summary>Antworten</summary>

1. Jeden String mit `$db->escapeString()` innerhalb einfacher Anführungszeichen escapen; jede Zahl mit `(int)`
   umwandeln.
2. **B.** `arrayQuery()`.
3. Als zweites Argument von `arrayQuery()`: `array( 'offset' => ..., 'limit' => ... )`.
4. Sie werden gezählt; nur das äußerste Paar sendet `BEGIN` und `COMMIT`.
5. Es escapt für Sie und läuft auf jeder unterstützten Datenbank, MongoDB eingeschlossen.

</details>

## Weiterführende Literatur

- [Standards für Datenbanken und SQL](../../../../standards/datenbanken-und-sql.md).
- [Sicherheitsstandard](../../../../standards/sicherheit.md).
- [MongoDB als Datenbank](../../../../funktionen/6.0/mongodb-datenbankunterstuetzung.md): was rohes SQL dort nicht kann.

## Verwandte Seiten

- [Modul 9: Die Konsole und persistente Objekte](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
