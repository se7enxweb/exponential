---
title: Datenbankcode für PHP 8 und jede Engine
audience: developer
category: extension-development
order: 100
summary: Bringen Sie den Datenbankcode einer alten Erweiterung auf den Stand von Exponential 6 - von mysql_*-Aufrufen und reinem MySQL-SQL zu eZDB, eZPersistentObject und .dba-Schemadateien, die auf SQLite, PostgreSQL, MariaDB, MySQL, Oracle und MongoDB funktionieren.
keywords: Datenbank, Transaktionen, Schema, portables SQL, Upgrade, database, eZDB, mysql_query, eZPersistentObject, SQLite, PostgreSQL, MongoDB, Oracle, MariaDB, escapeString, transactions, dba, schema, upgrade
type: help
---
# Datenbankcode für PHP 8 und jede Engine

Teil 4, Kapitel 10. Alte Erweiterungen wurden für eine einzige Datenbank geschrieben: MySQL. Exponential 6 unterstützt
mehrere, und der Standard für eine neue Installation ist SQLite 3. Dieses Kapitel zeigt, was Sie am Datenbankcode
einer alten Erweiterung ändern müssen, damit er unter PHP 8 und auf jeder Engine funktioniert, auf der die Website
laufen kann.

## Die Engines, auf die eine Erweiterung treffen kann

[Datenbankunterstützung](../../datenbankunterstuetzung.md) listet die unterstützten Datenbanken in der Reihenfolge
auf, die das Projekt bevorzugt: SQLite 3 (der Standard), PostgreSQL, MongoDB, MariaDB, Oracle und MySQL. Ihre
Erweiterung wählt nicht; die Website wählt. Code, der nur auf MySQL funktioniert, funktioniert auf einer von sechs.

| Engine | Kernel-Klasse | Was sich für reines SQL ändert |
|---|---|---|
| MySQL, MariaDB | `eZMySQLiDB` | nichts, dafür wurde alter Code geschrieben |
| PostgreSQL | `eZPostgreSQLDB` | keine Backticks, kein `LIMIT a,b`, strengere Typen, `||` zum Verketten |
| SQLite 3 | `eZSQLite3DB` | wenige Spaltentypen, eingeschränktes `ALTER TABLE` |
| Oracle | bereitgestellt von der Erweiterung ezoracle | Namen mit 30 Zeichen in älteren Versionen, kein `LIMIT` |
| MongoDB | `expMongoDB` | überhaupt kein SQL: `arrayQuery()` liefert keine Zeilen und schreibt eine Warnung |

Die Zeile zu MongoDB ist das stärkste Argument für die Regel dieses Kapitels: **Verwenden Sie `eZPersistentObject`
für Ihre eigenen Tabellen.** Der MongoDB-Adapter übersetzt die Aufrufe von `eZPersistentObject`
(`fetchObjectList()`, `storeObject()`, `removeObject()`) in MongoDB-Abfragen; Ihr von Hand geschriebenes SQL kann er
in nichts übersetzen ([MongoDB-Unterstützung im Kernel](../../../bc/6.0/mongodb-kernel-unterstuetzung.md)).

## Schritt 1: kein mysql_* und kein eigenes mysqli_*

Die `mysql_*()`-Funktionen wurden in PHP 7 entfernt. Sie durch `mysqli_*()` und eine neue Verbindung zu ersetzen, ist
die falsche Lösung: Damit öffnen Sie eine zweite Verbindung, außerhalb der Transaktionen des Kernels, zu einer
Datenbank, die vielleicht gar nicht MySQL ist. Verwenden Sie die Verbindung des Kernels:

```php
<?php
$db = eZDB::instance();
$rows = $db->arrayQuery( 'SELECT id, name FROM myoldext_item ORDER BY name' );
foreach ( $rows as $row )
{
    echo $row['id'], ' ', $row['name'], "\n";
}
```

| Alt | Exponential 6 |
|---|---|
| `mysql_connect()`, `mysql_select_db()` | nichts: `eZDB::instance()` ist bereits verbunden |
| `mysql_query( "SELECT ..." )` mit einer Fetch-Schleife | `$db->arrayQuery( "SELECT ..." )` liefert alle Zeilen als Arrays |
| `mysql_query( "UPDATE ..." )` | `$db->query( "UPDATE ..." )` |
| `mysql_real_escape_string( $s )` | `$db->escapeString( $s )` |
| `mysql_insert_id()` | `$db->lastSerialID( 'myoldext_item', 'id' )` |
| `mysql_error()` | `$db->errorMessage()` |
| `LIMIT 10, 20` im SQL-String | `$db->arrayQuery( $sql, array( 'offset' => 10, 'limit' => 20 ) )` |

Die letzte Zeile ist wichtig: Das zweite Argument von `arrayQuery()` nimmt `offset` und `limit`, und jeder Treiber
schreibt das richtige SQL für seine Engine.

## Schritt 2: Werte in SQL

Jeder Wert, den Sie in SQL einsetzen, muss je nach Typ abgesichert werden:

```php
<?php
$db = eZDB::instance();
$http = eZHTTPTool::instance();

$id = (int)$http->postVariable( 'ItemID' );
$name = $db->escapeString( (string)$http->postVariable( 'Name' ) );
$ids = array_map( 'intval', (array)$http->postVariable( 'SelectedIDs', array() ) );

$db->query( "UPDATE myoldext_item SET name = '$name' WHERE id = $id" );
if ( $ids )
{
    $rows = $db->arrayQuery( 'SELECT id, name FROM myoldext_item WHERE ' . $db->generateSQLINStatement( $ids, 'id', false, true, 'int' ) );
}
```

- Ganzzahlen: `(int)`.
- Strings: `escapeString()`, innerhalb einfacher Anführungszeichen.
- Listen: `generateSQLINStatement()`, das sehr lange Listen außerdem so aufteilt, wie die Engine es braucht.
- Niemals ein Wert aus der Anfrage ohne eines davon.

Beachten Sie das `(string)` vor `escapeString()`: `postVariable()` liefert `null` für eine fehlende Variable, und
`null` an String-Funktionen zu übergeben, ist seit PHP 8.1 veraltet
([Veraltetes in PHP 8.1 bis 8.5](php-8-1-bis-8-5-veraltete-funktionen.md)).

## Schritt 3: portables SQL

Wenn Sie reines SQL behalten, beschränken Sie es auf das, was jede SQL-Engine versteht:

| Vermeiden | Verwenden |
|---|---|
| Backticks um Namen | einfache kleingeschriebene Namen ohne Anführungszeichen |
| `LIMIT a, b` | die Parameter `offset` und `limit` von `arrayQuery()` |
| `CONCAT( a, b )` | `$db->concatString( array( 'a', 'b' ) )` |
| Varianten von `SUBSTRING()` | `$db->subString( $string, $from, $len )` |
| von Hand geschriebenes `a & b` | `$db->bitAnd( 'a', 'b' )` |
| `INSERT ... ON DUPLICATE KEY UPDATE`, `REPLACE INTO` | holen, dann einfügen oder aktualisieren, innerhalb einer Transaktion |
| `NOW()`, `UNIX_TIMESTAMP()` | ein PHP-`time()`-Wert als Ganzzahl |
| `AUTO_INCREMENT` in Ihrem eigenen `CREATE TABLE` | eine `.dba`-Schemadatei (Schritt 5) |

## Schritt 4: Transaktionen

Fassen Sie Schreibvorgänge, die zusammengehören, zusammen, damit ein Fehler nichts halb erledigt zurücklässt:

```php
<?php
$db = eZDB::instance();
$db->begin();
$db->query( "DELETE FROM myoldext_item_tag WHERE item_id = $id" );
foreach ( $tagIDs as $tagID )
{
    $db->query( 'INSERT INTO myoldext_item_tag ( item_id, tag_id ) VALUES ( ' . (int)$id . ', ' . (int)$tagID . ' )' );
}
$db->commit();
```

Der Kernel zählt verschachtelte `begin()`-Aufrufe, sodass Ihre Transaktion innerhalb einer laufen kann, die der Kernel
geöffnet hat (zum Beispiel beim Veröffentlichen), und erst auf der äußersten Ebene committet wird.
se7enxweb/expchangeclass hat beim Fork von eZChangeclass ein Rollback per Transaktion hinzugefügt, „damit eine
fehlgeschlagene Umwandlung den Inhalt unberührt lässt“ (sein erster Import, Commit 7c3f164).

## Schritt 5: Tabellen, Schemas und .dba-Dateien

Alte Erweiterungen liefern `sql/mysql/schema.sql` mit und erwarten, dass der Administrator es ausführt.
Erweiterungen für Exponential 6 beschreiben ihre Tabellen einmal, in `share/db_schema.dba`, und die Schemawerkzeuge
des Kernels schreiben das SQL für jede Engine. Das ganze Verfahren steht in
[Datenbankschema und .dba-Dateien](../teil-3-erweiterungsentwickler/datenbankschema-und-dba-dateien.md).

Wenn Sie zusätzlich Dateien in `sql/<engine>/` behalten, geben Sie jeder Engine ihre eigenen: `sql/mysql/`,
`sql/postgresql/`, `sql/sqlite/`.

## Schritt 6: eZPersistentObject

Für die eigenen Tabellen einer Erweiterung ersetzt eine Klasse, die `eZPersistentObject` erweitert, das meiste reine
SQL und funktioniert auch auf MongoDB:

```php
<?php
class myOldExtItem extends eZPersistentObject
{
    public static function definition()
    {
        return array(
            'fields' => array(
                'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'name' => array( 'name' => 'Name', 'datatype' => 'string', 'default' => '', 'required' => true ),
            ),
            'keys' => array( 'id' ),
            'increment_key' => 'id',
            'class_name' => 'myOldExtItem',
            'sort' => array( 'name' => 'asc' ),
            'name' => 'myoldext_item',
        );
    }

    public static function fetchList( $offset = 0, $limit = 50 )
    {
        return eZPersistentObject::fetchObjectList( self::definition(), null, null, null,
                                                    array( 'offset' => $offset, 'limit' => $limit ) );
    }
}
```

`store()`, `remove()` und `fetchObject()` kommen aus der Basisklasse. Das vollständige Kapitel ist
[Persistente Objekte und Ihre eigenen Tabellen](../teil-3-erweiterungsentwickler/persistente-objekte-und-tabellen.md).

## PHP-8-Details im Datenbankcode

| Symptom | Ursache | Lösung |
|---|---|---|
| `count(): Argument #1 must be of type Countable` nach einer Abfrage | `arrayQuery()` hat bei einem Fehler `false` geliefert | `$rows = $db->arrayQuery( ... ); if ( !is_array( $rows ) ) ...` |
| Deprecated: `null` an `strlen()` / `trim()` übergeben | ein `NULL`-Wert in einer Spalte | `(string)$row['name']` oder `?? ''` |
| `Unsupported operand types: string + int` | Arithmetik mit einer Spalte, die als String `''` zurückkam | zuerst umwandeln: `(int)$row['count'] + 1` |
| Eine Abfrage, die auf MySQL funktionierte, scheitert auf PostgreSQL | MySQL akzeptierte `'5'` für eine Ganzzahlspalte oder eine fehlende Spalte in `GROUP BY` | schreiben Sie das SQL streng; testen Sie auf SQLite und PostgreSQL |

## Auf mehr als einer Engine testen

Eine Entwicklungsinstallation auf SQLite ist in Minuten eingerichtet und der Standard, testen Sie also zuerst dort:
Was auf SQLite und MySQL funktioniert, funktioniert meist überall sonst, außer auf MongoDB. Wenn die Benutzer Ihrer
Erweiterung MongoDB einsetzen könnten, testen Sie auch dort, oder schreiben Sie in Ihrer README deutlich, dass die
Erweiterung eine SQL-Datenbank braucht.

## Verwandte Seiten

- Zurück: [Dynamische Eigenschaften](dynamische-eigenschaften.md)
- Weiter: [ezinfo.php und extension.xml modernisieren](ezinfo-und-extension-xml-modernisieren.md)
- [Datenbankunterstützung](../../datenbankunterstuetzung.md)
- [Persistente Objekte und Ihre eigenen Tabellen](../teil-3-erweiterungsentwickler/persistente-objekte-und-tabellen.md)
- [Datenbankschema und .dba-Dateien](../teil-3-erweiterungsentwickler/datenbankschema-und-dba-dateien.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
