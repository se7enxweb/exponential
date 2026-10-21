---
title: "Lektion 9.3: Persistente Objekte"
audience: trainer, developer
category: training
order: 93
summary: Die Teilnehmenden legen eine Tabelle für ihre Erweiterung an, beschreiben sie mit einer Definition für eZPersistentObject und speichern, holen, zählen, ändern und entfernen Zeilen, ohne für jede Operation SQL zu schreiben. 60 Minuten.
keywords: entwickler, eZPersistentObject, persistente objekte, definition, fields, keys, increment_key, class_name, fetchObject, fetchObjectList, count, store, remove, setAttribute, attribute, eigene tabelle, share/db_schema.dba, sql, developers course, persistent objects, custom table
type: help
---
# Lektion 9.3: Persistente Objekte

Erweiterungen, die eigene Daten speichern, etwa ein Protokoll von Begrüßungen, Abonnements oder Einstellungen pro
Benutzer, brauchen eine Tabelle. `eZPersistentObject` ordnet eine Tabelle anhand einer einzigen Beschreibung, der
Definition, einer PHP-Klasse zu und übernimmt dann das Speichern, Holen, Zählen und Entfernen von Zeilen; dabei wird
jeder Wert escapt, und es funktioniert auf jeder Datenbank, die Exponential unterstützt. In dieser Lektion legen die
Teilnehmenden eine Tabelle und eine Klasse dafür an.

> **Hinweis**
> Die Übung legt eine Tabelle an. Führen Sie sie nur auf Ihrer Entwicklungsinstallation aus.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 9: Die Konsole und persistente Objekte](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Experten |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 9.2](ein-eigener-befehl.md); eine Entwicklungsinstallation auf SQLite (der Standard von `exp:install`) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine `definition()` mit `fields`, `keys`, `increment_key`, `class_name`, `sort` und `name` schreiben;
- neue Zeilen mit `'id' => null` und `store()` anlegen;
- eine Zeile mit `fetchObject()` und viele mit `fetchObjectList()` holen und mit `count()` zählen;
- eine Zeile mit `setAttribute()` und `store()` ändern und mit `remove()` löschen;
- erklären, warum persistente Objekte gegenüber von Hand geschriebenem SQL vorzuziehen sind.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: `kernel/classes/ezpersistentobject.php` (die Definition ist oberhalb von `definition()` dokumentiert),
[Standards für Datenbanken und SQL](../../../../standards/datenbanken-und-sql.md#die-ezdb-regeln-für-sicheres-sql) und
Kernel-Klassen, die sie verwenden, etwa `kernel/classes/ezrole.php`.

1. **Die Definition** (6 Minuten). Gehen Sie `mysiteGreeting::definition()` durch: Jedes Feld hat einen `name` (den
   Namen des PHP-Attributs), einen `datatype` (`integer`, `string`, `float`), einen `default` und `required`; `keys`
   ist der Primärschlüssel; `increment_key` wird beim Einfügen von der Datenbank gefüllt; `class_name` ist die Klasse,
   zu der die Zeilen werden; `name` ist die Tabelle.
2. **Neue Zeilen** (3 Minuten). Legen Sie sie mit `'id' => null` an, damit die Datenbank die nächste ID vergibt; nach
   `store()` enthält `attribute( 'id' )` diese ID. Kernel-Klassen wie `eZRole` machen es genauso.
3. **Holen** (4 Minuten). `fetchObject( $def, null, $conditions )` gibt ein Objekt oder `null` zurück;
   `fetchObjectList( $def, null, $conditions, $sorts, $limit )` gibt ein Array zurück; dazu `count( $def,
   $conditions )`. Bedingungen sind ein Hash aus Feld und Wert; die Werte werden escapt.
4. **Warum** (4 Minuten). Jeder Wert wird für Sie escapt und umgewandelt; derselbe Code läuft auf MySQL,
   PostgreSQL, SQLite und MongoDB, was bei von Hand geschriebenem SQL nicht der Fall wäre.
5. **Die Tabelle mitliefern** (3 Minuten). Eine echte Erweiterung liefert ihr Schema in `share/db_schema.dba` (das
   Format des Kernels, geschrieben mit `ezsqldumpschema.php` und geladen mit `ezsqlinsertschema.php`) oder als
   SQL-Dateien pro Datenbank in `sql/<engine>/`. Im Kurs legt ein kleines Skript die Tabelle auf SQLite an.

### Häufige Fragen

**Kann sich ein Feldname vom Spaltennamen unterscheiden?** Die Schlüssel von `fields` sind die Spaltennamen; `name`
ist der Attributname, der mit `attribute()` verwendet wird. Die meisten Klassen halten beide gleich.

**Wie füge ich Methoden wie `fetchByName` hinzu?** Als statische Methoden der Klasse, die `fetchObjectList()` mit
Bedingungen aufrufen, wie in der Übung.

**Transaktionen?** Fassen Sie mehrere Schreibvorgänge in `$db->begin()` und `$db->commit()` ein (Lektion 9.4).

### Stolperfallen

- **`'id' => 0` für eine neue Zeile.** Eine Null ist ein Wert; geben Sie `null` an, damit die Datenbank die ID
  vergibt.
- **Ein Tabellenname ohne Präfix.** Verwenden Sie das Präfix Ihrer Erweiterung (`mysite_`), damit Tabellen nie mit
  den `ez*`-Tabellen des Kernels kollidieren.
- **Das SQL der Tabelle.** Das `CREATE TABLE` der Übung ist SQLite-Syntax; MySQL schreibt `AUTO_INCREMENT`,
  PostgreSQL verwendet `SERIAL`. Ein `.dba`-Schema erspart es, es für jede Datenbank zu schreiben.

## Praktische Übung

Auf der Entwicklungsinstallation auf SQLite, in `extension/mysite_hello`.

1. Legen Sie `scripts/create_table.php` an, das die Tabelle anlegt, falls es sie noch nicht gibt:

   ```php
   <?php
   // Run once: php bin/php/ezexec.php extension/mysite_hello/scripts/create_table.php (SQLite syntax)
   $db = eZDB::instance();
   $db->query( "CREATE TABLE IF NOT EXISTS mysite_greeting (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name VARCHAR(100) NOT NULL DEFAULT '',
                    created INTEGER NOT NULL DEFAULT 0 )" );
   eZCLI::instance()->output( 'Table mysite_greeting is ready' );
   ```

2. Führen Sie es aus: `php bin/php/ezexec.php extension/mysite_hello/scripts/create_table.php`.
3. Legen Sie `classes/mysitegreeting.php` an:

   ```php
   <?php
   /**
    * One stored greeting: a row of the table mysite_greeting.
    */
   class mysiteGreeting extends eZPersistentObject
   {
       public static function definition()
       {
           return array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                           'datatype' => 'integer',
                                                           'default' => 0,
                                                           'required' => true ),
                                            'name' => array( 'name' => 'Name',
                                                             'datatype' => 'string',
                                                             'default' => '',
                                                             'required' => true ),
                                            'created' => array( 'name' => 'Created',
                                                                'datatype' => 'integer',
                                                                'default' => 0,
                                                                'required' => true ) ),
                         'keys' => array( 'id' ),
                         'increment_key' => 'id',
                         'class_name' => 'mysiteGreeting',
                         'sort' => array( 'created' => 'desc' ),
                         'name' => 'mysite_greeting' );
       }

       public static function create( $name )
       {
           return new self( array( 'id' => null, 'name' => $name, 'created' => time() ) );
       }

       public static function fetch( $id )
       {
           return eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)$id ) );
       }

       public static function fetchByName( $name, $limit = 10 )
       {
           return eZPersistentObject::fetchObjectList( self::definition(), null, array( 'name' => $name ),
                                                       null, array( 'offset' => 0, 'length' => (int)$limit ) );
       }

       public static function countAll()
       {
           return eZPersistentObject::count( self::definition() );
       }
   }
   ```

4. Erzeugen Sie die Autoloads neu: `php bin/php/ezpgenerateautoloads.php -e`.
5. Legen Sie `scripts/greetings.php` an und führen Sie es mit `ezexec.php` aus:

   ```php
   <?php
   $cli = eZCLI::instance();
   $ada = mysiteGreeting::create( 'Ada' );
   $ada->store();
   $grace = mysiteGreeting::create( 'Grace' );
   $grace->store();
   $cli->output( 'Stored IDs ' . $ada->attribute( 'id' ) . ' and ' . $grace->attribute( 'id' ) );
   $cli->output( 'Count: ' . mysiteGreeting::countAll() );
   $cli->output( 'Named Ada: ' . count( mysiteGreeting::fetchByName( 'Ada' ) ) );
   $row = mysiteGreeting::fetch( $grace->attribute( 'id' ) );
   $row->setAttribute( 'name', 'Grace H.' );
   $row->store();
   $cli->output( 'Renamed: ' . mysiteGreeting::fetch( $row->attribute( 'id' ) )->attribute( 'name' ) );
   $row->remove();
   $cli->output( 'Count after remove: ' . mysiteGreeting::countAll() );
   ```

6. Führen Sie das Skript ein zweites Mal aus und vergleichen Sie die IDs.
7. Ändern Sie `create()` auf `'id' => 0`, führen Sie es einmal aus, lesen Sie das Ergebnis und ändern Sie es wieder
   zurück.
8. Committen Sie die Klasse und die Skripte.

## Erwartetes Ergebnis

- Schritt 2 gibt "Table mysite_greeting is ready" aus.
- Schritt 5 gibt zwei neue IDs, die Anzahl, `Named Ada: 1`, `Renamed: Grace H.` und die Anzahl nach dem Entfernen
  aus.
- Schritt 6: neue, höhere IDs; die Anzahl wächst pro Lauf um eins (Ada bleibt, Grace wird entfernt).
- Schritt 7: Die neuen Zeilen erhalten die ID 0 statt neuer IDs ("Stored IDs 0 and 0"); unter SQLite ersetzt die
  zweite die erste. Die Datenbank vergibt IDs nur, wenn die ID `null` ist.
- Der Trainer lässt alle Teilnehmenden `increment_key` erklären.

## Quiz

1. Welcher Schlüssel der Definition nennt die Tabelle?
2. Welchen Wert muss die ID einer neuen Zeile haben, damit die Datenbank eine vergibt?
   A. `0` B. `null` C. `-1` D. `''`
3. Welche Methode gibt viele Zeilen zurück, und welche eine Zeile?
4. Warum sind persistente Objekte sicherer als SQL-Strings?
5. Wie liefert eine Erweiterung das Schema ihrer Tabellen mit?

<details>
<summary>Antworten</summary>

1. `name`.
2. **B.** `null`, wie es Kernel-Klassen wie `eZRole` tun.
3. `fetchObjectList()` für viele, `fetchObject()` für eine.
4. Werte werden für Sie escapt und umgewandelt, und derselbe Code läuft auf jeder unterstützten Datenbank.
5. In `share/db_schema.dba` (erzeugt mit `ezsqldumpschema.php`, geladen mit `ezsqlinsertschema.php`) oder als
   SQL-Dateien pro Datenbank in `sql/<engine>/`.

</details>

## Weiterführende Literatur

- [Standards für Datenbanken und SQL](../../../../standards/datenbanken-und-sql.md): sicheres SQL, Dialekte, Schemas.
- [Datenbankunterstützung](../../../datenbankunterstuetzung.md): auf welchen Datenbanken Exponential läuft.
- [SQLite als Datenbank](../../../../funktionen/6.0/sqlite-datenbank.md).

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
