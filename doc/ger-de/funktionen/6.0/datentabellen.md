# Datentabellen: die Tabellen der Module als Teil des Systems (6.0.15)

Lesen Sie diese Seite, wenn Sie eine Extension mit eigenen Tabellen schreiben oder pflegen (eine persistente Klasse,
ein Modul darüber), wenn Sie eine solche Tabelle in einer anderen Datenbank als der der Installation führen wollen,
oder wenn Sie die Datenbanken verwalten, mit denen eine Installation spricht. Die Passwörter dieser Datenbanken liegen
im [Geheimnisspeicher](geheimnisspeicher.md).

## Kurz gesagt

| | |
|---|---|
| Was eine Datentabelle ist | Die Tabelle einer persistenten Klasse: eine PHP-Klasse, die `eZPersistentObject` erweitert und ihre Tabelle in `definition()` beschreibt (Felder, Schlüssel, Tabellenname). Der Kernel hat Hunderte davon, die meisten Extensions bringen eigene mit. |
| Was 6.0.15 hinzufügt | Einen Katalog aller dieser Klassen mit Tabelle, Feldern, Schlüsseln und Beziehungen zu Inhalten; eine Beschreibung jeder Tabelle, wie ihre Datenbank sie führt, und wo sie von der Definition abweicht; benannte Datenbankverbindungen (eine SQLite-Datei, MySQL/MariaDB, PostgreSQL, Oracle, SQL Server, MongoDB, lokal oder entfernt); und persistente Klassen, die auf einer solchen Verbindung lesen und schreiben statt in der Datenbank der Installation. |
| Wo | **Setup > Datentabellen** (Modul `datatable`): Datentabellen, Verbindungen. **Setup > Geheimnisse** (Modul `secrets`) für ihre Passwörter. |
| Kein Befehl nötig | Die Kernel-Tabellen werden auf der Seite Datentabellen angelegt, der Katalog entsteht beim ersten Aufruf der Seite, Verbindungen werden im Browser angelegt und getestet. |
| Klassen | `expDataTableCatalogue`, `expDataTableDescriber`, `expDataConnections`, `expDataConnection`, `expKernelTableSchema`, `expPDODB`; `eZPersistentObject::dbFor()` |
| Einstellungen | `datatable.ini` (`[DataTableSettings]`, `[ConnectionSettings]`, `[Connection_<id>]`, `[ClassSettings]`, `[Relation_<klasse>_<spalte>]`) |

## Der Katalog

`expDataTableCatalogue` führt jede persistente Klasse der Installation:

1. Er liest die Klassenlisten des Autoloaders, die des Kernels (`autoload/ezp_kernel.php`) und die der Extensions
   (`var/autoload/ezp_extension.php`). Testklassen und Klassen mit Namensraum bleiben außen vor.
2. Er liest jede Datei als Text und behält nur die Dateien, die eine Methode `definition()` deklarieren und eine
   Klasse, die eine andere erweitert (`extends`). Dafür wird keine Datei geladen.
3. Er lädt die übrigen Klassen und behält die, die von `eZPersistentObject` erben, nicht abstrakt sind, `definition()`
   selbst deklarieren (eine Klasse, die die Definition ihrer Elternklasse erbt, ist deren Tabelle und wird einmal
   geführt) und deren Definition eine Tabelle und Felder nennt.
4. Eine Klasse, deren `definition()` scheitert, erscheint mit der Fehlermeldung, statt die Seite abzubrechen.

Jeder Eintrag enthält die Klasse, die Tabelle, die Datei, die Herkunft (`kernel` oder den Namen der Extension), die
Felder (Typ, Vorgabewert, Pflicht, Kurzname, fremde Klasse und fremdes Attribut), die Schlüssel, den hochgezählten
Schlüssel, die Standardsortierung, die Funktionsattribute, die Verbindung (leer für die Datenbank der Installation) und
die Beziehungen.

Der Katalog liegt im Cache-Verzeichnis (`<cache>/datatable/catalogue.json`, der Verzeichnisname ist
`[DataTableSettings] CatalogueCacheDir`) und wird neu erstellt, wenn sich eines der Autoload-Arrays oder
`settings/datatable.ini` ändert. **Katalog neu erstellen** auf der Seite Datentabellen (Richtlinie `datatable/manage`)
tut es sofort. `[DataTableSettings] IgnoreClasses[]` lässt Klassen weg, etwa eine, deren `definition()` einen Request
braucht.

```php
$catalogue = expDataTableCatalogue::instance();
$catalogue->classes();                    // Klasse => Eintrag
$catalogue->entry( 'eZViewCounter' );     // ein Eintrag oder null
$catalogue->byTable( 'ezview_counter' );  // die Klassen einer Tabelle
$catalogue->byOrigin();                   // zuerst der Kernel, dann jede Extension
$catalogue->pointingAt( 'node' );         // Klassen mit einer Spalte, die eine Knoten-ID enthält
```

### Beziehungen

Eine Beziehung sagt, dass eine Spalte die ID von etwas anderem enthält: eines Inhaltsobjekts, eines Knotens, einer
Inhaltsklasse, eines Benutzers, einer Sektion oder einer Zeile einer anderen Tabelle. Sie kommen aus diesen Quellen, in
dieser Reihenfolge (die erste für eine Spalte gilt):

| Quelle | Wie sie geschrieben wird | Angezeigt als |
|---|---|---|
| Der Schlüssel `relations` der Definition | `'relations' => array( 'node_id' => array( 'class' => 'eZContentObjectTreeNode', 'field' => 'node_id' ) )`; optional `'kind'` (`object`, `node`, `class`, `user`, `section`, `table`) | die Definition |
| Der Fremdschlüssel eines Felds | `'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id'` im Feld | ein Fremdschlüssel eines Felds |
| `datatable.ini` | ein Block `[Relation_<klasse>_<spalte>]` mit `Kind=`, `Class=` und `Field=` | datatable.ini |
| Der Name der Spalte | `contentobject_id`, `object_id`, `*_object_id` (Objekt); `node_id`, `main_node_id`, `parent_node_id`, `*_node_id` (Knoten); `contentclass_id`, `class_id` (Klasse); `user_id`, `creator_id`, `owner_id`, `modifier_id` (Benutzer); `section_id` (Sektion) | aus dem Namen erschlossen |

Die Art ergibt sich aus der Zielklasse: `eZContentObject` ist ein Objekt, `eZContentObjectTreeNode` ein Knoten,
`eZContentClass` eine Klasse, `eZUser` ein Benutzer, `eZSection` eine Sektion, jede andere Klasse eine Tabelle. Der
eigene hochgezählte Schlüssel einer Tabelle gilt nie als Beziehung. `[DataTableSettings] InferRelations=disabled`
schaltet das Erschließen aus dem Namen ab.

```ini
[Relation_myArchiveRow_contentobject_id]
Kind=object
Class=eZContentObject
Field=id
```

Der Schlüssel `relations` gehörte schon vorher zum Format der Definition (`eZViewCounter` und `eZTipafriendCounter`
nutzen ihn), und `eZPersistentObject::storeObject()` liest ihn wie bisher.

## Beschreiben: die Tabelle, wie ihre Datenbank sie führt

Die Seite einer Klasse (**Datentabellen**, dann die Klasse) zeigt die Definition neben der Tabelle:
`expDataTableDescriber::describe( $entry )` fragt die eigene Verbindung der Tabelle

- ob die Tabelle existiert, aus dem Katalog der Datenbank (`information_schema` bei MySQL, PostgreSQL und SQL Server,
  `sqlite_master` und `PRAGMA table_info` bei SQLite, `user_tables` und `user_tab_columns` bei Oracle,
  `listCollectionNames()` bei MongoDB). Das Vorhandensein wird nie aus einem Abfrageergebnis geschlossen: MongoDB
  antwortet für eine fehlende Collection mit einer leeren Liste, MySQL wirft einen Fehler;
- ihre Spalten mit Typ, ob sie leer sein dürfen, und ihrem Vorgabewert; bei MongoDB die Felder einer Stichprobe von
  Dokumenten (`[DataTableSettings] MongoSampleSize`, 50);
- die Zahl der Zeilen;
- wo Tabelle und Definition voneinander abweichen:

| Abweichung | Bedeutung |
|---|---|
| `missing_table` | Die Tabelle der Definition fehlt in der Datenbank |
| `missing_column` | Ein Feld der Definition hat keine Spalte (bei MongoDB nicht gemeldet, wo eine Stichprobe nur die Felder ihrer Dokumente kennt) |
| `extra_column` | Eine Spalte, die die Definition nicht kennt: harmlos, aufgeführt, damit man sie sieht |
| `type_mismatch` | Ein Ganzzahlfeld über einer Textspalte oder ein Textfeld über einer Zahlenspalte (bei SQLite nicht gemeldet, das jeden Typ in jeder Spalte speichert) |

Ein Feld mit Kurznamen wird über diesen zugeordnet, wenn der lange Name keine Spalte hat. Die Seite zeigt außerdem die
Beziehungen und die ersten 20 Zeilen; Spalten, deren Name auf ein Geheimnis deutet (`password`, `hash`, `token`,
`secret`, `salt`, `ciphertext`, `private_key`, `api_key`, `key`), werden nicht gezeigt.

## Verbindungen

Eine Verbindung ist eine Datenbank, die die Installation neben ihrer eigenen benutzen kann. Es gibt drei Arten, die
**Setup > Datentabellen > Verbindungen** gemeinsam auflistet:

| Art | Woher sie kommt | In der Administration |
|---|---|---|
| `site` | Die eigene Datenbank der Installation (`site.ini [DatabaseSettings]`) | immer vorhanden, testbar |
| Angemeldet | Ein Block `[Connection_<bezeichner>]` in `datatable.ini`, in den Einstellungen oder einer Extension | nur lesbar, testbar |
| In der Administration angelegt | Eine Zeile der Tabelle `exp_data_connection` | dort angelegt, geändert, getestet und entfernt (Richtlinie `datatable/connections`) |

Eine angemeldete Verbindung lässt sich nicht durch eine Zeile mit demselben Bezeichner ersetzen. Ein Bezeichner besteht
aus Kleinbuchstaben, Ziffern und `_`, beginnt mit einem Buchstaben und hat höchstens 64 Zeichen; `site` ist vergeben.

```ini
[Connection_archive]
Name=Archive database
Engine=mysql
Server=db2.example.com
Port=3306
Database=archive
User=archive
Secret=env:ARCHIVE_DB_PASSWORD
Charset=utf8mb4
ReadOnly=false
```

Weitere Schlüssel einer angemeldeten Verbindung: `Socket`, `DSN` (ein PDO-Data-Source-Name), `Dialect`, `Driver`,
`Description`.

### Datenbanksysteme

| System | Über | Hinweise |
|---|---|---|
| `sqlite` | den SQLite-Treiber des Kernels (`sqlite3`) | `Database` ist die Datei, relativ zur Installation; sie endet auf `.db`, `.sqlite` oder `.sqlite3` und liegt in einem der Verzeichnisse von `[ConnectionSettings] SQLiteDirectories[]` (Vorgabe `var/datatable`, wird bei Bedarf angelegt). Ein Pfad, der sie verlässt (`..`, ein Link nach außen), wird abgelehnt. Die Datei entsteht bei der ersten Benutzung. |
| `mysql` | den MySQL/MariaDB-Treiber des Kernels | `mariadb` gilt als dasselbe System |
| `postgresql` | den PostgreSQL-Treiber des Kernels | |
| `oracle` | den Treiber von ezoracle, wenn die Extension aktiv ist und PHP oci8 hat, sonst PDO `oci` | `Database` ist der Service-Name |
| `sqlserver` | PDO `sqlsrv`, `dblib` oder `odbc` (der erste, den PHP hat, oder der als Treiber genannte) | hier nicht getestet: der Server, auf dem dies entstand, hat keinen SQL-Server-Treiber |
| `mongodb` | den MongoDB-Treiber des Kernels (`expMongoDB`) | |

Ein System, für das der Kernel keinen Treiber hat, läuft über `expPDODB`, einen allgemeinen Datenbanktreiber über PDO:
Abfragen, Limit und Offset im Dialekt des Systems, Transaktionen, die zuletzt vergebene ID, Maskierung, die
Tabellenliste. So ist jede Verbindung ein Datenbankobjekt derselben Art wie das der Installation, welches System auch
dahintersteht. `[ConnectionSettings] Engines[]` begrenzt die wählbaren Systeme; die Seite Verbindungen nennt jedes
System mit der Angabe, ob dieser Server es benutzen kann, und wenn nicht, warum (die fehlende PHP-Erweiterung oder der
fehlende Treiber).

`[ConnectionSettings] AllowedHosts[]` begrenzt die Server, die eine Verbindung nennen darf (leer oder `*`: jeder).
`[ConnectionSettings] ConnectTimeout` (5 Sekunden, höchstens 60) ist die Zeit, die eine Verbindung auf ihren Server
wartet.

### Das Passwort ist der Name eines Geheimnisses

Eine Verbindung enthält nie ein Passwort. Ihr `Secret` (das Feld „Passwort: der Name eines Geheimnisses") nennt ein
Geheimnis des [Geheimnisspeichers](geheimnisspeicher.md): ein dort verschlüsselt gespeichertes Geheimnis oder
`env:VARIABLE` für eine Umgebungsvariable des PHP-Prozesses. Auf der Seite einer Verbindung lässt sich ein Passwort nur
für einen Test eintippen; mit **Beim Speichern im Geheimnisspeicher ablegen** (zusätzlich Richtlinie `secrets/manage`)
wird es verschlüsselt unter dem angegebenen Namen gespeichert, ohne Namen unter `datatable.<bezeichner>`, und die
Verbindung behält diesen Namen. Ein DSN, der ein Passwort enthält, wird abgelehnt.

### Verbindung testen

**Testen** in der Liste und **Verbindung testen** auf der Seite einer Verbindung öffnen die Verbindung, fragen die
Version des Servers und seine Tabellen ab und schließen sie wieder. Das Ergebnis nennt den Treiber (`ez` für den des
Kernels, `pdo`), die Zeit in Millisekunden, die Version und die Zahl der Tabellen, oder den Fehler. Ein Server, der auf
seinem Port nicht innerhalb der Wartezeit antwortet, wird als solcher gemeldet, bevor der Treiber gefragt wird (ein
Treiber kann sonst eine Minute warten). Jede Meldung ist maskiert: der Wert des Geheimnisses der Verbindung und eines
für den Test eingetippten Passworts erscheint nie.

```php
$result = expDataConnections::test( 'archive' );
// ok, engine, via, version, tables, ms, error
```

### Eine Verbindung im Code benutzen

```php
$db = expDataConnections::db( 'archive' );     // die Datenbank der Verbindung, ein eZDBInterface
$rows = $db->arrayQuery( 'SELECT id, title FROM archive_item', array( 'limit' => 10 ) );
```

`db()` öffnet jede Verbindung einmal je Request und behält sie für dessen Rest; `db( 'site' )` und `db( '' )` liefern
die Datenbank der Installation. Eine Verbindung, die es nicht gibt oder die sich nicht öffnen lässt, wirft eine
`RuntimeException`, deren Meldung die Verbindung nennt und nie den Wert eines Geheimnisses enthält.

Unter Exponential Velocity bedient ein Worker einen Request nach dem anderen: Die Verbindungen eines Requests werden
geschlossen, wenn der nächste beginnt. Ein Worker behält also nie einen Socket von einem Request zum nächsten, und auch
keinen, den sein Elternprozess vor dem Fork geöffnet hat.

## Persistente Klassen auf einer Verbindung

Eine persistente Klasse nennt ihre Verbindung in ihrer Definition:

```php
public static function definition()
{
    return array(
        'fields' => array( ... ),
        'keys' => array( 'id' ),
        'increment_key' => 'id',
        'class_name' => 'myArchiveItem',
        'name' => 'archive_item',
        'connection' => 'archive',
    );
}
```

oder die Einstellungen nennen sie für eine Klasse, die es nicht tut (die Klasse einer Extension, ohne sie zu ändern):

```ini
[ClassSettings]
Connection[myArchiveItem]=archive
```

`eZPersistentObject` liest und schreibt die Zeilen der Klasse dann dort: `fetchObject()`, `fetchObjectList()`,
`count()`, `store()`, `remove()`, `removeObject()`, `updateObjectList()`, `newObjectOrder()` und `reorderObject()`
fragen alle `eZPersistentObject::dbFor( $definition )`. Alles, was der Aufruf zusammensetzt, wird für diese Verbindung
maskiert, nicht für die Datenbank der Installation (MySQL und SQLite maskieren ein Hochkomma verschieden): die
Bedingungen, die Wertelisten, das Umsortieren und die Kurznamen der Felder. Eine Klasse der Datenbank der Installation,
die mitten in einem solchen Aufruf gelesen wird (ein Funktionsattribut, eine Beziehung), geht für dieses Lesen zurück
in die Datenbank der Installation.

Eine Klasse ohne Verbindung kostet zwei `isset()`-Prüfungen und ein Nachschlagen mehr als vorher; sonst ändert sich für
sie nichts.

Die Einstellungen in `[ClassSettings]` werden einmal je Prozess gelesen. Unter Velocity erreicht eine Änderung die
Worker mit dem nächsten Neustart.

## Die Administration

| Seite | Adresse | Richtlinie |
|---|---|---|
| Datentabellen: der Katalog, nach Kernel und Extension, gefiltert nach Wörtern und Herkunft | `datatable/dashboard` | `datatable/read` |
| Eine Klasse: Definition, Verbindung, Tabelle, Abweichungen, Beziehungen, erste Zeilen | `datatable/table/<klasse>` | `datatable/read` |
| Verbindungen und die Datenbanksysteme dieses Servers | `datatable/connections` | `datatable/connections` |
| Eine Verbindung: neu, ändern, testen, entfernen | `datatable/connection/<bezeichner>` (`new` für eine neue) | `datatable/connections` |
| Geheimnisse | `secrets/list` | `secrets/manage` |

| Richtlinie | Erlaubt |
|---|---|
| `datatable/read` | den Katalog und die Seite jeder Klasse |
| `datatable/manage` | die Kernel-Tabellen anlegen, den Katalog neu erstellen |
| `datatable/connections` | Verbindungen anlegen, ändern, testen und entfernen |
| `datatable/sync` | die Synchronisation zwischen Tabellen und Inhalten (siehe „Als Nächstes") |

In einer neuen Installation hat nur die Rolle Administrator sie. Das Setup-Menü hat eine Gruppe **Datentabellen** mit
**Datentabellen**, **Verbindungen** und **Geheimnisse**.

## Die Kernel-Tabellen

| Tabelle | Enthält |
|---|---|
| `exp_data_connection` | die in der Administration angelegten Verbindungen: Bezeichner (eindeutig), Name, System, Server, Port, Datenbank, Benutzer, den Namen des Geheimnisses, Optionen (JSON: Zeichensatz, Socket, Treiber, DSN), Beschreibung, nur lesbar, wer sie wann angelegt und geändert hat |
| `exp_secret` | den [Geheimnisspeicher](geheimnisspeicher.md) |

Eine neue Installation hat beide aus dem Kernel-Schema. Eine Installation von vor 6.0.15 bekommt sie aus den
Update-Dateien für MySQL, PostgreSQL und SQLite (`update/database/<system>/6.0/dbupdate-6.0.0-6.0.15.sql`, die sie nur
anlegen, wenn sie fehlen), oder auf der Seite Datentabellen: Solange eine von ihnen fehlt, sagt die Seite das, und
**Tabellen anlegen** (Richtlinie `datatable/manage`) legt sie in der Datenbank der Installation über den
Schema-Handler des Systems an, auf jedem System, das der Kernel unterstützt, MongoDB eingeschlossen.
`expKernelTableSchema` tut das für jede Tabelle eines `.dba`-Schemas:

```php
expKernelTableSchema::exists( 'exp_secret' );          // true, false oder null, wenn es sich nicht sagen lässt
expKernelTableSchema::install( array( 'exp_data_connection', 'exp_secret' ), null, null, $messages );
expKernelTableSchema::tables( $db );                   // alle Tabellen einer Verbindung, aus ihrem Katalog
```

## Beispiel: eine Klasse in ihrer eigenen SQLite-Datei

1. **Setup > Datentabellen**: Wenn die Seite meldet, dass die Tabellen fehlen, **Tabellen anlegen**.
2. **Verbindungen > Neue Verbindung**: Bezeichner `archive`, System SQLite, Datenbank `var/datatable/archive.sqlite`.
   **Verbindung testen** meldet die SQLite-Version und 0 Tabellen; **Speichern**.
3. Die Klasse der Extension nennt die Verbindung in ihrer Definition (`'connection' => 'archive'`), oder die
   Einstellungen tun es (`[ClassSettings] Connection[myArchiveItem]=archive`).
4. Die Tabelle wird auf dieser Verbindung angelegt, etwa aus der `share/db_schema.dba` der Extension:

   ```php
   $db = expDataConnections::db( 'archive' );
   expKernelTableSchema::install( array( 'archive_item' ), $db, 'extension/myarchive/share/db_schema.dba' );
   ```

5. Von da an arbeitet die Klasse wie jede andere:

   ```php
   $item = new myArchiveItem( array( 'title' => "O'Briens Notizen", 'node_id' => 42 ) );
   $item->store();
   $items = myArchiveItem::fetchObjectList( myArchiveItem::definition(), null, array( 'node_id' => 42 ) );
   ```

6. **Datentabellen** führt `myArchiveItem` mit der Verbindung `archive`; ihre Seite zeigt die Tabelle in der
   SQLite-Datei, ihre Zeilen und `node_id` als Beziehung zu einem Knoten.

## Als Nächstes

Die nächsten Schritte der Datentabellen bauen auf dieser Schicht auf:

- das Schema einer Tabelle aus einer `.dba`-Datei auf jeder Verbindung aus der Administration anlegen und
  aktualisieren, mit dem SQL je System vorab angezeigt;
- die Beziehungen in der Inhaltsansicht: die Zeilen jeder Datentabelle, die auf das gezeigte Objekt verweisen;
- ein RAD-Assistent, der eine neue Modultabelle erstellt (die persistente Klasse, ihr Schema je System, die Seiten der
  Administration mit ihren Richtlinien, Fetch-Funktionen und einen JSON-Endpunkt), aus einer Feldliste, einer
  Inhaltsklasse oder den Feldern einer Importquelle;
- die Synchronisation zwischen Tabellen und Inhalten: eine einmalige Kopie in beide Richtungen, eine geplante
  Synchronisation in eine Richtung über eindeutige Schlüssel, ein Live-Spiegel beim Veröffentlichen, Verschieben und
  Entfernen, und eine Synchronisation in beide Richtungen mit Konfliktregeln und einer Warteschlange von Konflikten, die
  in der Administration aufgelöst werden.

## Tests

In `tests/tests/kernel/classes/datatable/` (Testsuite kernel-classes, ohne Installation): der Katalog, die Beschreibung
und ihre Abweichungen, die Verbindungen (Werte, Prüfungen, Pläne, Data-Source-Names, der Test auf SQLite), persistente
Klassen auf einer SQLite-Verbindung (speichern, lesen, ändern, entfernen, Maskierung), und die beiden Kernel-Tabellen,
die in `share/db_schema.dba`, den persistenten Klassen, den drei Kernel-Schemas und den drei Update-Dateien gleich sein
müssen. `tests/tests/lib/ezdb/expPDODBTest.php` prüft den PDO-Treiber auf pdo_sqlite und das SQL der anderen Dialekte.
