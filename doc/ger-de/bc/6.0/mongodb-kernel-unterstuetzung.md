# MongoDB-Unterstützung im Kernel

Lesen Sie diese Seite, wenn Sie Exponential mit MongoDB statt mit einer SQL-Datenbank betreiben möchten oder Code
pflegen, der mit beiden funktionieren muss. Sie ist die vollständige Referenz der Portierung: Architektur,
Collections, jede geänderte Datei, der Status jedes Kernel-Moduls und jeder Klasse, Testpläne, der Leitfaden zur
Umstellung von SQL auf MongoDB und eine Anleitung für den Einstieg. Wenn Sie nur eine Website auf MongoDB installieren
möchten, gehen Sie direkt zu
[Abschnitt 24, Die wichtigsten ersten Schritte](#24-die-wichtigsten-ersten-schritte-mit-exponential-6014-und-mongodb),
und lesen Sie danach [Abschnitt 26, Bekannte Einschränkungen](#26-bekannte-einschränkungen).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Der MongoDB-Adapter `expMongoDB` und die Erweiterung `sevenx_mongodb` ersetzen jede SQL-Abfrage des Kernels durch MongoDB-Aufrufe (`aggregate()`, `insert()`, `upsert()`, `deleteWhere()`). Der Adapter führt nie SQL aus: `arrayQuery()` gibt `[]` zurück und protokolliert eine Warnung `MONGO TODO`; daran erkennen Sie eine fehlende Portierung. |
| Wer betroffen ist | Nur Installationen, die sich für MongoDB entscheiden. SQL-Installationen bleiben unverändert. Erweiterungen, die ihr eigenes SQL über `arrayQuery()` ausführen, liefern unter MongoDB keine Zeilen, bis sie portiert sind. |
| Wie Sie es prüfen | Suchen Sie in der Debug-Ausgabe oder im Fehlerprotokoll einer Seite nach `MONGO TODO`; eine vollständig portierte Seite hat keinen solchen Eintrag. |
| Wie Sie es beheben | Portieren Sie die Abfrage nach [Muster und Konventionen](#7-muster-und-konventionen); sehen Sie sich die Liste zur [Kompatibilität der Datentypen](#25-kompatibilität-der-datentypen--kern-und-community) an, bevor Sie Community-Erweiterungen installieren. |

### Die Referenzumgebung

Die Portierung wurde auf der folgenden Umgebung entwickelt und getestet. Hostnamen und Pfade sind Platzhalter;
verwenden Sie Ihre eigenen.

| | |
|---|---|
| Website | Exponential CMS mit MongoDB statt MySQL |
| PHP-Laufzeit | `/opt/plesk/php/8.5/bin/php`, FPM-Pool `plesk-php85-fpm` |
| Datenbank | MongoDB `exp` unter `localhost:27017` |
| MongoDB-PHP-Treiber | `MongoDB\Client` über Composer (`vendor/autoload.php`) |
| Administration | `https://admin.example.com/`, ein eigenes Document Root unter `/path/to/admin-docroot/` |
| Website (Frontend) | `https://www.example.com/`, Document Root (ein Symlink) unter `/path/to/exponential/` |
| Anmeldung | das Administratorkonto der Installation |
| Fehlerprotokoll | `/path/to/logs/admin/error_log` |

## Inhaltsverzeichnis

1. [Hintergrund und Zweck](#1-hintergrund-und-zweck)
2. [Architektur](#2-architektur)
3. [Aufbau mit zwei Docroots](#3-aufbau-mit-zwei-docroots)
4. [Schema der MongoDB-Collections](#4-schema-der-mongodb-collections)
5. [Alle geänderten Dateien (kumulativ)](#5-alle-geänderten-dateien-kumulativ)
6. [Kernel-Module — Status und bekannte Probleme](#6-kernel-module--status-und-bekannte-probleme)
7. [Muster und Konventionen](#7-muster-und-konventionen)
8. [Betriebshinweise](#8-betriebshinweise)
9. [Testplan — Administration](#9-testplan--administration)
10. [Testplan — Website](#10-testplan--website)
11. [Protokoll gelöster Probleme](#11-protokoll-gelöster-probleme)
12. [Testplan — Cronjobs / Skripte](#12-testplan--cronjobs--skripte)
13. [Referenzen](#13-referenzen)
14. [PHPUnit-Tests — Theorie und Plan für die nächste Phase](#14-phpunit-tests--theorie-und-plan-für-die-nächste-phase)
15. [Kernel-PHP-Klassen — Status und bekannte Probleme](#15-kernel-php-klassen--status-und-bekannte-probleme)
16. [Design des MongoDB-Treibers und technischer Fertigstellungsgrad im Vergleich zu ezmysqli](#16-design-des-mongodb-treibers-und-technischer-fertigstellungsgrad-im-vergleich-zu-ezmysqli)
17. [Weitere Korrekturen — System-Upgrade, RAD-Codegeneratoren, BC-CIE-Export, Sprach-Bitmaske](#17-weitere-korrekturen--system-upgrade-rad-codegeneratoren-bc-cie-export-sprach-bitmaske)
18. [Leitfaden zur Umstellung von SQL-Datenbanken — ein beliebiges RDBMS nach JSON exportieren und in MongoDB importieren](#18-leitfaden-zur-umstellung-von-sql-datenbanken--ein-beliebiges-rdbms-nach-json-exportieren-und-in-mongodb-importieren)
19. [Projekt abgeschlossen — Liste aller gepatchten oder geänderten Dateien](#19-projekt-abgeschlossen--liste-aller-gepatchten-oder-geänderten-dateien)
20. [Schritte zur vollständigen Neuimplementierung im Kernel (KEINE Kernel-Override-Erweiterung mehr)](#20-schritte-zur-vollständigen-neuimplementierung-im-kernel-keine-kernel-override-erweiterung-mehr)
21. [PHPUnit-Testsuite — umgesetzt (Mai 2026)](#21-phpunit-testsuite--umgesetzt-mai-2026)
22. [Leistungsoptimierungen des Setup-Assistenten (Juni 2026)](#22-leistungsoptimierungen-des-setup-assistenten-juni-2026)
23. [Installationspakete für Websites — Verteilung und Paketserver](#23-installationspakete-für-websites--verteilung-und-paketserver)
24. [Die wichtigsten ersten Schritte mit Exponential 6.0.14 und MongoDB](#24-die-wichtigsten-ersten-schritte-mit-exponential-6014-und-mongodb)
25. [Kompatibilität der Datentypen — Kern und Community](#25-kompatibilität-der-datentypen--kern-und-community)
26. [Bekannte Einschränkungen](#26-bekannte-einschränkungen)


## 1. Hintergrund und Zweck

Exponential ist ein klassisches, SQL-basiertes CMS. Dieses Projekt ersetzt MySQL vollständig durch MongoDB.
Die Erweiterung `sevenx_mongodb` stellt den MongoDB-Datenbankadapter (`expMongoDB`) bereit und
überschreibt praktisch jede Kernel-Klasse, die SQL-Abfragen absetzt. Das Override-System ordnet die
Klassennamen über `var/autoload/ezp_override.php` zu.

**Ziel:** jeden Aufruf `MONGO TODO arrayQuery()` aus der Debug-Leiste zu entfernen, sodass jede Seite
mit vollständigem, korrektem Inhalt dargestellt wird und kein SQL-Ausweichpfad mehr ausgeführt wird.

Alle Korrekturen sind auf PHP 8.5 vorwärtsportiert. SQL-Anweisungen DELETE, INSERT und SELECT werden
durch MongoDB-Aufrufe `aggregate()`, `insert()`, `upsert()` und `deleteWhere()` ersetzt. Der Adapter
führt nie echtes SQL aus; `arrayQuery()` gibt immer `[]` zurück und protokolliert eine Warnung `MONGO TODO`.


## 2. Architektur

### Das Klassen-Override-System

`var/autoload/ezp_override.php` ordnet PHP-Klassennamen Dateien in
`extension/sevenx_mongodb/classes/kernel/` zu. Wenn eine Kernel-Klasse instanziiert wird, lädt PHP die
Override-Datei statt der ursprünglichen Datei unter `kernel/`. Die Override-Dateien definieren **denselben
Klassennamen** (keine Unterklasse), daher verwenden alle bestehenden Aufrufe `new eZContentObject(...)` und
`eZContentObject::fetch(...)` automatisch die MongoDB-Implementierung.

Die Override-Dateien sind in beiden Docroots identisch — das Verzeichnis der Erweiterung `sevenx_mongodb`
ist vom Edit-Docroot in das Haupt-Docroot **verlinkt** (Symlink). Jede Änderung an einer Datei der Erweiterung
wirkt sich sofort auf beide Websites aus.

### Der MongoDB-Adapter: `expMongoDB`

**Datei:** `extension/sevenx_mongodb/classes/expMongoDB.php`

Erweitert `eZDBInterface`. Die wichtigsten Methoden:

| Methode | Zweck |
|---|---|
| `databaseName()` | Gibt `'mongo'` zurück — wird überall als `if ($db->databaseName() === 'mongo')` verwendet |
| `aggregate($table, $pipeline)` | Führt eine MongoDB-Aggregationspipeline aus; **für komplexe Abfragen immer richtig** |
| `insert($table, $doc)` | Fügt ein einzelnes Dokument ein |
| `upsert($table, $filter, $doc)` | Ersetzt oder fügt ein, abhängig vom Filter |
| `deleteWhere($table, $filter)` | Löscht die Dokumente, auf die der Filter passt (verwendet `deleteMany`) |
| `nextSeqID($table, $column)` | Gibt `MAX(column)+1` über `$group/$max` zurück — dient als Autoincrement |
| `arrayQuery($sql, ...)` | **Stub — gibt immer `[]` zurück** — protokolliert MONGO TODO in error_log |
| `translateConditions($conds)` | Wandelt einfache skalare Bedingungen um; **unterstützt `$or`, `$in`, `$expr` NICHT** |
| `eZTableList()` | Gibt `[]` zurück — überschreibt die Rückgabe null der Basisklasse, die `array_keys()` abstürzen ließ |
| `begin()` / `commit()` / `rollback()` | Ohne Wirkung — MongoDB-Operationen wirken pro Dokument |

### Wichtige Regel: Für Filter mit MongoDB-Operatoren immer `aggregate()` verwenden

`translateConditions()` verarbeitet nur skalare Werte und `like`. Wenn Sie `$or`, `$in`, `$expr` oder
ein beliebiges verschachteltes Array über `find()` übergeben, wird der Filter verfälscht. Verwenden Sie `aggregate()`
mit einer ausdrücklichen `$match`-Stufe, sobald die Bedingung nicht trivial ist.


## 3. Aufbau mit zwei Docroots

```
/path/to/exponential/   ← front site (symlink)
  extension/sevenx_mongodb/                               ← SYMLINKED to edit docroot
  var/autoload/ezp_override.php                           ← same as edit docroot (identical)
  kernel/class/*.php                                      ← ORIGINAL (not symlinked)
  kernel/content/*.php                                    ← ORIGINAL (not symlinked)

/path/to/admin-docroot/  ← edit/admin site
  extension/sevenx_mongodb/                               ← SYMLINK TARGET
  var/autoload/ezp_override.php                           ← identical content to front site
  kernel/class/*.php                                      ← SEPARATE COPY — edit separately
  kernel/content/*.php                                    ← SEPARATE COPY — edit separately
```

**Wichtig:** `kernel/class/edit.php`, `kernel/class/copy.php`, `kernel/content/edit.php` und
`kernel/content/attribute_edit.php` sind im Edit-Docroot **eigene physische Dateien**.
Änderungen an diesen Dateien wirken sich NICHT auf die Website aus und umgekehrt.

Die Dateien der Erweiterung `sevenx_mongodb` wirken sich **sehr wohl** gleichzeitig auf beide Websites aus.

Nach dem Bearbeiten einer PHP-Datei: `touch <file>`, um den OPcache zu umgehen (revalidate_freq=2s).


## 4. Schema der MongoDB-Collections

### Zentrale Inhaltstabellen

| Collection | Wichtige Felder |
|---|---|
| `ezcontentobject_tree` | `node_id`, `parent_node_id`, `contentobject_id`, `path_string` (z. B. `/1/2/67/`), `depth`, `is_hidden`, `is_invisible`, `main_node_id`, `contentobject_version`, `priority`, `sort_field`, `sort_order` |
| `ezcontentobject` | `id`, `contentclass_id`, `current_version`, `name`, `owner_id`, `published`, `modified`, `section_id`, `status`, `language_mask`, `initial_language_id`, `remote_id` |
| `ezcontentobject_version` | `id`, `contentobject_id`, `version`, `status` (0=Entwurf, 1=veröffentlicht, 3=archiviert), `created`, `modified`, `creator_id`, `language_mask`, `initial_language_id` |
| `ezcontentobject_attribute` | `id`, `contentobject_id`, `version`, `language_code`, `contentclassattribute_id`, `data_text`, `data_int`, `data_float`, `data_type_string` |
| `ezcontentobject_name` | `contentobject_id`, `content_version`, `real_translation`, `content_translation`, `language_id`, `name` |

### Klassentabellen

| Collection | Wichtige Felder |
|---|---|
| `ezcontentclass` | `id`, `version` (0=veröffentlicht, 1=temporär/in Bearbeitung, 2=geändert/gesperrt), `identifier`, `serialized_name_list`, `is_container`, `modifier_id`, `modified` |
| `ezcontentclass_attribute` | `id`, `contentclass_id`, `version`, `identifier`, `data_type_string`, `placement`, `is_required`, `is_searchable`, `serialized_name_list` |
| `ezcontentclassgroup` | `id`, `name` |
| `ezcontentclass_classgroup` | `contentclass_id`, `contentclass_version`, `group_id`, `group_name` |
| `ezcontentclass_name` | `contentclass_id`, `language_id`, `name` |

### URLs und Navigation

| Collection | Wichtige Felder |
|---|---|
| `ezurlalias_ml` | `id`, `parent`, `link`, `text`, `text_md5`, `action`, `is_alias`, `is_original`, `alias_redirects`, `lang_mask` |
| `ezurlwildcard` | `id`, `source_url`, `destination_url`, `type` |
| `eznode_assignment` | `id`, `contentobject_id`, `contentobject_version`, `parent_node`, `is_main`, `op_code`, `sort_field`, `sort_order` |

### Status, Rolle, Benutzer

| Collection | Wichtige Felder |
|---|---|
| `ezcobj_state` | `id`, `group_id`, `identifier`, `default_language_id`, `language_mask`, `priority` |
| `ezcobj_state_group` | `id`, `identifier`, `default_language_id`, `language_mask` |
| `ezcobj_state_link` | `contentobject_id`, `contentobject_state_id` |
| `ezcobj_state_group_language` | `contentobject_state_group_id`, `language_id`, `real_language_id`, `name`, `description` |
| `ezrole` | `id`, `name`, `version` |
| `ezpolicy` | `id`, `role_id`, `module_name`, `function_name`, `original_id` |
| `ezpolicy_limitation` | `id`, `policy_id`, `identifier` |
| `ezpolicy_limitation_value` | `id`, `policy_limitation_id`, `value` |
| `ezuser_role` | `contentobject_id`, `role_id`, `limit_identifier`, `limit_value` |
| `ezuser` | `contentobject_id`, `login`, `email`, `password_hash`, `password_hash_type`, `is_enabled` |

### Feldspeicherung in eZPersistentObject

Felder werden als **Eigenschaften des PHP-Objekts** gespeichert, nicht in einem Array `DataMap`.
`fill($row)` ruft für jedes Feld `$this->$attrName = $row[$key]` auf, wobei `attrName` gleich `field_def['name']` ist.
`attribute($name)` gibt `$this->$attrName` zurück.

Für `eZContentClass`: Das Feld `id` hat `name='ID'` → gespeichert als `$this->ID` → `attribute('id')` gibt `$this->ID` zurück.
`__clone()` setzt `$this->ID = null`, wodurch `storeObject` den Klon als neues Einfügen behandelt.

**Versionskonstanten von `eZContentClass`:**
- `VERSION_STATUS_DEFINED = 0` — veröffentlichte, aktive Version
- `VERSION_STATUS_TEMPORARY = 1` — Bearbeitungsentwurf (in `kernel/class/edit.php`)
- `VERSION_STATUS_MODIFIED = 2` — von ezscriptmonitor gesperrt (externe Bearbeitungssperre)


## 5. Alle geänderten Dateien (kumulativ)

### `extension/sevenx_mongodb/classes/expMongoDB.php`

Der zentrale MongoDB-Adapter.

| Änderung | Grund |
|---|---|
| Override `eZTableList()` gibt `[]` zurück | Die Basisklasse gibt null zurück; `array_keys(null)` stürzt unter PHP 8.5 in `generateUniqueTempTableName()` ab |
| Methode `deleteWhere($table, $filter)` hinzugefügt | `eZContentObjectVersion::removeThis()` muss Dokumente direkt löschen |
| Zeitmessung von `aggregate()` mit `eZDebug::accumulatorStart/Stop` | Die Abfragen erscheinen in den Zeitakkumulatoren der Debug-Leiste |
| `reportQuery()` in `arrayQuery()` | Einträge MONGO TODO erscheinen im SQL-Bereich der Debug-Ausgabe |


### `extension/sevenx_mongodb/classes/kernel/ezpersistentobject.php`

Die zentrale ORM-Basisklasse. Jeder Aufruf von `fetchObjectList()`, `storeObject()`, `removeObject()` und `newObjectOrder()` läuft hierüber.

**`fetchObjectList()` — vollständiger MongoDB-Zweig**

Ersetzt den Aufruf `$db->find()` durch eine vollständige `aggregate()`-Pipeline:
1. `$match` — aus `$conds` aufgebaut, mit datentypabhängiger Umwandlung in int/float (siehe Korrektur unten)
2. `$sort` — aus `$sorts` oder `$def['sort']`
3. `$skip` / `$limit` — aus dem Array `$limit` (Schlüssel `offset`, `limit` oder `length`)
4. `$project` — entweder eine Liste benannter Felder oder nur `{_id: 0}`
5. `$group` + `$count` — für Zählanfragen über `custom_fields`

**Wichtige Korrektur — Endlosschleife (504 Gateway Timeout):** Der ursprüngliche Aufruf `find()` unterstützte
weder Limit noch Offset. `eZURLWildcard::createWildcardsIndex()` rief in einer Schleife
`fetchList($offset, $limit)` auf, das immer alle Zeilen zurückgab. Die Schleife konnte nie enden.
Korrektur: echte Stufen `$skip`/`$limit` in der Pipeline.

**Wichtige Korrektur — Typumwandlung von `$conds`:** URL-Parameter kommen als Zeichenketten an. MongoDB speichert Ganzzahlen.
`fetchObjectList(..., ['contentclass_id' => '49'])` lieferte 0 Zeilen, wenn `contentclass_id` als Ganzzahl `49`
gespeichert war. Korrektur: `$fields[$field]['datatype']` nachschlagen und Ganzzahl- bzw. Gleitkommafelder mit `(int)` oder `(float)`
umwandeln, bevor `$mongoFilter` aufgebaut wird. Array-Werte (die MongoDB-Operatoren enthalten)
werden unverändert durchgereicht.

**`storeObject()` — vollständiger MongoDB-Zweig**

Baut aus den aktuellen Eigenschaften des Objekts über `attribute()` ein typisiertes PHP-Dokument.
- Prüfung auf Vorhandensein über `fetchObjectList($def, $keys, $key_conds)`
- Beim Einfügen: prüft `increment_key`; wenn `attribute(inc) <= 0`, ruft es `nextSeqID` für eine neue ID auf
- Beim Aktualisieren: ruft `upsert($table, $filter, $doc)` auf

**`removeObject()` — MongoDB-Zweig**

Vor dem SQL-Pfad `DELETE`:
```php
if ( $db->databaseName() === 'mongo' ) {
    $filter = [];
    foreach ($conditions as $field => $value)
        $filter[$field] = is_numeric($value) ? (int)$value : $value;
    $db->deleteWhere($table, $filter);
    return;
}
```

**`newObjectOrder()` — MongoDB-Zweig**

Die arrayQuery `SELECT MAX(placement) FROM ...` wurde ersetzt durch:
```php
$rows = $db->aggregate($table, [
    ['$match' => $cond_array (int-cast when numeric)],
    ['$group' => ['_id' => null, 'maxval' => ['$max' => '$' . $orderField]]],
]);
return ($rows[0]['maxval'] ?? 0) + 1;
```

**`handleRows()` — Korrektur eines Referenzfehlers**

`foreach ($rows as &$row)` ließ `$row` als aktive Referenz auf `$rows[last]` zurück. Ein späteres
`foreach ($rows as $row)` überschrieb `$rows[last]` mit jeder vorherigen Zeile. Das letzte Element
jeder Liste erhielt dadurch immer die Daten des vorletzten Elements.
Korrektur: `unset($row)` unmittelbar nach der ersten Schleife.


### `extension/sevenx_mongodb/classes/kernel/ezcontentobject.php`

**`fetch($id)`** — am Anfang eine Umwandlung in int ergänzt: `$id = (int)$id`, wenn `is_numeric($id)`.

**`fetchIDArray($idArray)`** — Massen-Aggregation mit Joins auf Klassennamen und Objektnamen.

**`fetchByNodeID($nodeID)`** — verknüpft `ezcontentobject_tree` → `ezcontentobject` → `ezcontentclass`.

**`versionLanguageName()`** — verwendet `aggregate()` direkt (nicht `find()`), damit
`translateConditions()` den `$or`-Filter auf die Sprachmaske nicht verfälscht.

**`contentObjectAttributes()`** — Aggregationspipeline:
1. Match auf `contentobject_id`, `version` und optional `language_code`
2. `$lookup` auf `ezcontentclass_attribute` über `contentclassattribute_id = id`
3. Filter `_cls.version = 0`; Feld `identifier` hinzufügen; nach `_cls.placement` sortieren
4. `_id`, `_cls_placement` und das verschachtelte `_cls` aus der Projektion entfernen

**`fillNodeListAttributes()`** — lädt die Attribute für eine Liste von Objekten gebündelt in einer einzigen Abfrage.
Baut ein `$or` aus Bedingungen `{contentobject_id, version, language_code}` und führt dieselbe Pipeline aus wie
`contentObjectAttributes()`. Beseitigt N MONGO TODOs pro dargestellter Listenseite.

**`assignedNodes()`** — MongoDB-Aggregation: `ezcontentobject_tree` → `ezcontentobject` → `ezcontentclass`. Gab vorher `[]` zurück, was zur Meldung „None of the object's location(s) is visible“ führte.

**`hasVisibleNode()`** — MongoDB-Zweig hinzugefügt:
```php
$filter = ['contentobject_id' => (int)$contentobjectID];
if (ShowHiddenNodes !== 'true') $filter['is_invisible'] = 0;
$rows = $db->aggregate('ezcontentobject_tree', [['$match'=>$filter],['$count'=>'node_count']]);
return !empty($rows) && $rows[0]['node_count'] > 0;
```
Beseitigt 27 MONGO TODOs auf der Seite der Klassenliste (eines pro Klasse in der Gruppe Content).

**`stateIDArray()`** — Aggregation mit Lookup `ezcobj_state_link` → `ezcobj_state`.

**`stateIdentifierArray()`** — Aggregation `ezcobj_state_link` → `ezcobj_state` → `ezcobj_state_group`.

**`relatedObjects()`** — vollständige MongoDB-Pipeline mit `$lookup`:
1. Match auf `ezcontentobject_link` über `from_contentobject_id`
2. `$lookup` auf `ezcontentobject` über `to_contentobject_id`
3. `$lookup` auf `ezcontentclass` über `contentclass_id`
4. `$lookup` auf `ezcontentobject_name` für den Objektnamen
5. Gibt Instanzen von `eZContentObject` zurück


### `extension/sevenx_mongodb/classes/kernel/ezcontentobjectversion.php`

**`fetch($id)`** — Umwandlung `(int)$id`. Die POST-Werte `DeleteIDArray[]` kommen als Zeichenketten an.

**`removeThis()`** — vollständiger MongoDB-Zweig vor dem SQL-Pfad DELETE, mit `$db->deleteWhere()`:
- die Zeilen von `ezcontentobject_attribute` der Version
- die Zeilen von `ezcontentobject_link` der Version
- die Zeilen von `eznode_assignment` der Version
- die Zeile von `ezcontentobject_version` selbst
- die Zeilen von `ezcontentobject_name` der Version
- Wenn keine Versionen übrig bleiben: löscht das übergeordnete Objekt endgültig; andernfalls setzt es `current_version`
  über `aggregate()` auf die zuletzt geänderte verbleibende Version.


### `extension/sevenx_mongodb/classes/kernel/ezcontentclass.php`

**`fetch($id)`** — Umwandlung `(int)$id`. URL-Parameter kommen als Zeichenketten an; MongoDB speichert Ganzzahlen.

**`__clone()`** — setzt `$this->ID = null` → `attribute('id')` gibt null zurück → `storeObject` fügt den Datensatz als neuen ein.

**`storeVersioned($attributes, $version)`** — Logik für die Migration der Gruppen:
1. Die Attribute der Zielversion und der vorherigen Version entfernen
2. `$this->remove(false)` — entfernt den alten Versionsdatensatz
3. `setVersion($version)` — setzt version=0 am Objekt
4. Jedes Attribut in Version 0 speichern
5. `eZContentClassClassGroup::removeClassMembers($id, $version)` — die Gruppen von v0 entfernen (falls vorhanden)
6. Die Gruppen der vorherigen Version laden, ihre Version auf 0 ändern und jede speichern → legt die Gruppen von v0 an
7. `removeClassMembers($id, $previousVersion)` — die Gruppen der alten Version entfernen
8. `parent::store()` + `$this->NameList->store($this)`

**`canInstantiateClassList()` — Korrektur beim Auslesen des Namens:** Die MongoDB-Dokumente von `ezcontentclass` enthalten kein Feld `name` — sie speichern nur `serialized_name_list` (eine PHP-serialisierte Zeichenkette, z. B. `a:2:{s:6:"eng-US";s:6:"Folder";s:16:"always-available";s:6:"eng-US";}`). Für Aufrufe mit `$asObject === false` wurde nach `handleRows()` eine Nachbearbeitungsschleife ergänzt: Sie deserialisiert `serialized_name_list` und füllt `$row['name']` aus dem Sprachschlüssel `always-available`. Mit `mongosh` bestätigt: `db.ezcontentclass.findOne({version:0},{name:1,serialized_name_list:1})` liefert nur `serialized_name_list`. Behebt den Absturz `Undefined array key "name"` in `getClassesJsArray()` in der Klassenauswahl von content/edit.

**`initializeCopy()` — MONGO TODO (offen):** ruft `arrayQuery` mit einem SQL-JOIN über
`[ezcontentclass, ezcontentclass_name]` auf, um vorhandene Kopien zu zählen und den Namen der Kopie eindeutig zu machen.
Gibt `[]` zurück → `$count` ist undefiniert → PHP-Warnung → der Name bleibt in jedem Fall „Copy of Folder“.
Nicht fatal. Die geplante Korrektur mit `aggregate('ezcontentclass_name', [$match, $count])` steht im Backlog.


### `extension/sevenx_mongodb/classes/kernel/ezcontentclassattribute.php`

**`classAttributeIdentifiersHash()`** — den JOIN über 2 Tabellen durch zwei aufeinanderfolgende Aggregationen ersetzt:
1. Alle veröffentlichten Klassen aus `ezcontentclass` → eine Zuordnung `id → identifier` aufbauen
2. Alle veröffentlichten Attribute aus `ezcontentclass_attribute` → über die PHP-Zuordnung verknüpfen
Gibt den Hash `class_identifier/attr_identifier → attr_id` zurück, den `eZNamePatternResolver` verwendet.

Gab vorher `[]` zurück → `contentObjectName()` lieferte eine leere Zeichenkette → jedes neu veröffentlichte
Objekt speicherte einen leeren Namen in `ezcontentobject_name`.


### `extension/sevenx_mongodb/classes/kernel/ezcontentclassgroup.php`

**`fetch($id)`** — Umwandlung `(int)$id`.


### `extension/sevenx_mongodb/classes/kernel/ezcontentclassclassgroup.php`

**`fetchClassList($version, $groupId)`** — den SQL-JOIN über 3 Tabellen ersetzt durch:
1. `aggregate('ezcontentclass_classgroup', [$match: {group_id, version}])` → die Liste der Klassen-IDs holen
2. `aggregate('ezcontentclass', [$match: {id: {$in: [...]}, version}])` → die vollständigen Klassenzeilen holen
Gibt über `handleRows()` Objekte vom Typ `eZContentClass` zurück.

Gab vorher `[]` zurück → die Seite der Klassenliste zeigte „Classes inside (0)“.


### `extension/sevenx_mongodb/classes/kernel/ezcontentobjecttreenode.php`

**`fetch($id)`** — verzweigt zu `fetchMongo()`, einer vollständigen Aggregation, die Baum, Objekt, Klasse und Namen verknüpft.

**`fetchNodesByPathString()`** — MongoDB-Präfixvergleich mit `$regex` auf `path_string`.

**`findMainNode($objectID)`** — Aggregation mit `$expr: {$eq: ['$node_id','$main_node_id']}`.

**`makeObjectsArray()`** — Korrektur für implizit nullbare Parameter unter PHP 8.5: `?array $propertiesOverride = null`.

**`subTreeCountByNodeID($params, $nodeID)`** — vollständiger MongoDB-Pfad:
1. `path_string` und `depth` des übergeordneten Knotens holen
2. Einen Präfixvergleich des Pfads mit `$regex` und einen Filter auf die Tiefe aufbauen
3. Sichtbarkeit, mainNodeOnly und Klassenfilter anwenden
4. Das Ergebnis von `$count` als Ganzzahl zurückgeben

**`subTreeByNodeID($params, $nodeID)`** — vollständiger MongoDB-Pfad mit `$lookup`-Joins:
Baum → Objekt → Klasse (version=0) → Objektname; Zuordnung der Sortierung; Seitenaufteilung mit `$skip`/`$limit`.


### `extension/sevenx_mongodb/classes/kernel/ezurlaliasml.php`

**`fetchPathByActionList($actionList)`** — Aggregation auf `ezurlalias_ml` mit einem `$in`-Match auf die
Action-Zeichenketten; kein Filter auf `lang_mask`.

**`translate(&$uri, $reverse)`** — neu geschrieben. Die ursprünglichen Fehler:
1. `$db->md5()` gibt das SQL-Fragment `MD5('text')` zurück, keinen Hash → `text_md5` war immer `""`
2. `findOne()` gibt ein flaches assoziatives Array zurück; `$urlAliasArray[0]` war immer undefiniert
3. Keine Verfolgung der Elternkette für Pfade mit mehreren Segmenten

Korrektur: eine Schleife, die Segment für Segment vorgeht:
```php
foreach ($pathElements as $element) {
    $hash = md5(eZURLAliasML::strtolower($element));
    $rows = $db->aggregate('ezurlalias_ml', [
        ['$match' => ['parent' => $parent, 'text_md5' => $hash]],
        ['$sort'  => ['lang_mask' => -1]],
        ['$limit' => 1],
        ['$project' => ['_id' => 0]],
    ]);
    // chain: $parent = $row['link']
}
```


### `extension/sevenx_mongodb/classes/kernel/ezrole.php`

**Fehler:** ein auskommentiertes `/* } else */` zerstörte die if/else-Struktur → `arrayQuery` lief immer.
**Korrektur:** sauber umgebaut zu `if ($db->databaseName() == 'mongo') { ... } else { arrayQuery }`.


### `kernel/private/classes/ezcontentobjectstategroup.php`

**Fehler:** `arrayQuery` lief bedingungslos vor `if ($db->databaseName() === 'mongo')`.
**Korrektur:** `arrayQuery` in den `else`-Zweig verschoben.


### `extension/sevenx_mongodb/classes/kernel/ezsiteaccess.php`

Korrekturen für implizit nullbare Parameter unter PHP 8.5 in `change()` und `load()`: `?eZINI $siteINI = null`.


### `extension/sevenx_mongodb/classes/kernel/datatypes/ezinisetting/ezinisettingtype.php`

**`validateClassAttributeHTTPInput`** und **`storeClassAttribute`**: `ContentClass_ezinisetting_ini_instance_NNN`
aus der Prüfung `hasPostVariable` entfernt. Ein HTML-`<select multiple>` sendet kein Feld, wenn nichts ausgewählt ist →
die Validierung lieferte für jedes ini-setting-Attribut `STATE_INVALID` → das Speichern der Klasse schlug mit
„The class definition could not be stored.“ fehl.


### `extension/sevenx_mongodb/classes/kernel/datatypes/ezxmltext/ezxmloutputhandler.php`

Zeile 318: Prüfung `$tagName !== null &&` vor `isset(...)` ergänzt — PHP 8.5 meldet eine Deprecation für ein
null-Argument an `isset()`.


### `extension/sevenx_mongodb/classes/kernel/datatypes/ezxmltext/ezxmlschema.php`

Zeile 369: dieselbe null-Prüfung in `attrDefaultValues()`.


### `extension/xrowmetadata/autoloads/xrowmetadataoperator.php`

Zeile 66: `$cur_parent !== null ? $cur_parent->Name : ''` — schützt vor einem übergeordneten Knoten, der null ist.


### `extension/sevenx_mongodb/classes/kernel/clusterfilehandlers/ezfsfilehandler.php`

Während der Entwicklung wurde eine Debug-Instrumentierung PROCESSCACHE ergänzt und inzwischen wieder entfernt. In dieser Datei sind keine Debug-Aufrufe von `error_log` mehr enthalten.


### `kernel/class/edit.php` (nur Edit-Subdomain)

1. **Korrektur der ezscriptmonitor-Sperre:** Das Sperr-Template für Klassen der Version 2 wurde in
   `in_array('ezscriptmonitor', eZExtension::activeExtensions())` eingeschlossen. Ist die Erweiterung nicht aktiv, werden die Klasse
   und die Gruppen der Version 2 entfernt, danach geht es mit der Bearbeitung weiter.
2. **Defensives Anlegen der Gruppen von v1:** Im `else`-Zweig (die Klasse in v1 existiert bereits) werden die Gruppen von v1
   aus den Gruppen von v0 neu angelegt, wenn `fetchGroupList()` 0 Gruppen liefert.
3. Die während der Entwicklung ergänzten Debug-Zeilen `error_log()` wurden entfernt.


### `extension/sevenx_mongodb/classes/kernel/ezcontentlanguage.php`

**`objectCount($languageID)`** und **`classCount($languageID)`** — die COUNT-Abfragen über `arrayQuery` durch eine MongoDB-Aggregation mit `$count` und einen Bitmasken-Vergleich ersetzt.

**Korrektur der Bitmaske:** `$bitsAnySet` verlangt ein Ganzzahl-Literal und kann keinen berechneten Wert auswerten. Die Korrektur verwendet `$bitwiseAnd` innerhalb von `$expr`:
```php
[ '$expr' => [ '$gt' => [ [ '$bitwiseAnd' => [ '$language_mask', (int)$languageID ] ], 0 ] ] ]
```
Gab vorher `[]` zurück → die Seiten der Sprachverwaltung zeigten 0 Objekte und 0 Klassen.


### `extension/sevenx_mongodb/classes/kernel/ezurlaliasquery.php`

**DISTINCT action_type (Zeile 256):** `SELECT DISTINCT action_type FROM ezurlalias_ml` durch `aggregate('ezurlalias_ml', [['$group' => ['_id' => '$action_type']]])` ersetzt. Die Spalte wird mit `array_column($rows, '_id')` ausgelesen.

**`buildMongoMatch()` (neue geschützte Methode):** Übersetzt dieselben Filtereigenschaften, die `generateSQL()` liest (`paren`, `text`, `languages`, `actions`, `actionTypes`, `actionTypesEx`, `type`), in ein MongoDB-Array `$match`. Gibt `false` zurück, wenn die Bedingungen auf keine Zeile passen würden (z. B. ein leeres `actionTypes` nach der Ausschluss-Differenz).

**Methode `count()`:** Der MongoDB-Zweig ruft `buildMongoMatch()` und dann `aggregate('ezurlalias_ml', [$match, $count])` auf statt `arrayQuery("SELECT count(*) AS count …")`.

**Methode `fetchAll()`:** Der MongoDB-Zweig ruft `buildMongoMatch()` und dann `aggregate()` mit den Stufen `$sort`/`$skip`/`$limit` auf. Das SQL-Fragment `ORDER BY` (z. B. `"text ASC"`) wird zur Laufzeit mit `preg_split` in eine MongoDB-Sortierangabe zerlegt.


### `extension/sevenx_mongodb/classes/kernel/datatypes/ezurl/ezurl.php`

**`fetchByAttribute()` — JOIN über 4 Tabellen:** Den SQL-JOIN über `ezurl`, `ezurl_object_link`, `ezcontentobject_attribute` und `ezcontentobject_version` durch eine MongoDB-Pipeline mit `$lookup` ersetzt:
1. `ezurl` → `$lookup` auf `ezurl_object_link` über `url_id = id`; `$unwind`
2. → `$lookup` auf `ezcontentobject_attribute` über `objectattribute_id = id`; `$unwind`
3. → `$lookup` auf `ezcontentobject_version` mit `$expr`, das sowohl `contentobject_id` als auch `version` vergleicht; `$unwind`
4. `$match` auf `status = 1` (nur veröffentlichte Versionen)
5. `$group` nach der URL-`id`, um Dubletten über mehrere veröffentlichte Versionen zu entfernen
6. Zählvariante: Stufe `$count`; Listenvariante: `$project` + `$skip`/`$limit`

Gab vorher `[]` zurück → die Listenseite der URL-Verwaltung zeigte keine URLs.


### `kernel/setup/systemupgrade.php`

Kerndatei (kein Override). Vor `$dbSchema->transformSchema(…)` wurde die Prüfung `if ( !is_object( $dbSchema ) )` ergänzt. Unter MongoDB gibt `eZDbSchema::instance()` `false` zurück (für `'mongo'` ist kein Schema-Handler registriert). Die Seite kehrt jetzt früh zurück und zeigt „Database check OK.“, statt mit `Call to a member function transformSchema() on bool` abzustürzen.


### `extension/sevenx_mongodb/classes/kernel/ezsection.php`

**`sectionCount()`:** Vor `arrayQuery` einen MongoDB-Zweig mit einer `$count`-Aggregation ergänzt. Vorher gab `arrayQuery` `[]` zurück → `$countArray[0]['count']` war undefiniert → die Seite der Sektionsliste stürzte ab.

```php
$rows = $db->aggregate( 'ezsection', [ [ '$count' => 'count' ] ] );
return !empty( $rows ) ? (int) $rows[0]['count'] : 0;
```


### `lib/ezdbschema/classes/ezdbschema.php`

Datei der Kernbibliothek. `instance()` erhielt von MongoDB-Aufrufern `$params = false` (kein Schema-Handler für `'mongo'`). Unter PHP 8.5 löste der anschließende Zugriff `!isset($params['instance'])` `E_DEPRECATED: Automatic conversion of false to array` aus. Korrektur: unmittelbar nach der Prüfung `is_object( $params )` die Prüfung `if ( !is_array( $params ) ) $params = array();` ergänzt, sodass `$params` vor jedem Schlüsselzugriff immer ein Array ist.


### `kernel/search/stats.php`

Kerndatei (kein Override). Einen MongoDB-Zweig für die COUNT-Abfrage auf `ezsearch_search_phrase` ergänzt (Zeile 42):

```php
$rows = $db->aggregate( 'ezsearch_search_phrase', [ [ '$count' => 'count' ] ] );
$searchListCount = [ [ 'count' => !empty( $rows ) ? (int) $rows[0]['count'] : 0 ] ];
```

Vorher gab `arrayQuery` `[]` zurück → `$searchListCount[0]['count']` war undefiniert → die Seite der Suchstatistik stürzte mit `Undefined array key 0` ab.


### `extension/sevenx_mongodb/classes/kernel/ezsearchlog.php`

**`mostFrequentPhraseArray()`:** einen vollständigen Pipeline-Zweig für MongoDB ergänzt:
1. `$sort` nach `phrase_count DESC`
2. `$project` — `id`, `phrase`, `phrase_count`; `result_count` wird mit `$divide` berechnet und durch `$cond` geschützt, um bei `phrase_count = 0` eine Division durch null zu vermeiden
3. `$skip` / `$limit` werden aus `$parameters` angehängt, wenn vorhanden

Vorher gab `arrayQuery` `[]` zurück → die Seite der Suchstatistik zeigte eine leere Liste der Suchbegriffe.


### `kernel/infocollector/overview.php`

Kerndatei (kein Override). Für beide Abfragen einen MongoDB-Zweig ergänzt:

**Objektliste (Zeile 75):** vollständige Pipeline mit `$lookup`, ausgehend von `ezinfocollection`:
1. `$group` nach `contentobject_id` (entspricht DISTINCT)
2. `$lookup` auf `ezcontentobject` über `_id = id`; `$unwind`
3. `$lookup` auf `ezcontentobject_tree` mit `$expr`, das `contentobject_id` UND `node_id = main_node_id` vergleicht; `$unwind`
4. `$lookup` auf `ezcontentclass` mit `$expr`, das `id = contentclass_id` UND `version = 0` vergleicht; `$unwind`
5. `$sort` nach `_obj.name ASC`; `$skip`/`$limit`
6. `$project` — ordnet die verschachtelten Felder von `_obj`/`_tree`/`_class` den flachen Schlüsselnamen zu, die das Template erwartet

**Anzahl (Zeile 92):** `aggregate('ezinfocollection', [['$group' => ['_id' => '$contentobject_id']], ['$count' => 'count']])`.

Vorher gaben beide Abfragen `[]` zurück → die Seite zeigte keine Objekte und die Anzahl 0.


### Direkt in MongoDB vorgenommene Datenkorrekturen

| Objekt | Korrektur |
|---|---|
| `ezcontentobject_name` Objekt 242 v12 | `name` war `[]` (ein leeres Array, gespeichert vor der Korrektur von classAttributeIdentifiersHash); auf `'Home'` aktualisiert |
| `ezcontentclass_classgroup` Klasse 47, 48 | Gruppendatensätze für v0 eingefügt; verwaiste Gruppendatensätze von v2 gelöscht |



## 6. Kernel-Module — Status und bekannte Probleme

### Modul: `class` (Inhaltsklassen)

**Views:** `grouplist`, `classlist`, `view`, `edit`, `copy`, `create`

| View | URL | Status |
|---|---|---|
| `grouplist` | `/class/grouplist` | ✅ Funktioniert — listet die Klassengruppen auf |
| `classlist` | `/class/classlist/1` | ✅ Funktioniert — listet die Klassen einer Gruppe auf; `hasVisibleNode()` korrigiert (27 MONGO TODOs beseitigt) |
| `view` | `/class/view/47` | ✅ Funktioniert — zeigt die Details der Klasse und die Brotkrümelnavigation |
| `edit` | `/class/edit/47` | ✅ Funktioniert — lädt das Bearbeitungsformular der Klasse; die Brotkrümelnavigation zeigt den Gruppennamen |
| `copy` | `/class/copy/1` | ✅ Funktioniert — legt eine Kopie an und leitet zur Klassenliste weiter |
| Brotkrümelnavigation der Kopie | `/class/edit/49` | ✅ Funktioniert nach der Korrektur der Typumwandlung in `fetchObjectList` — die Gruppe von v0 wird richtig gefunden |

**Ursache des Fehlers in der Brotkrümelnavigation der Kopie:** `fetchObjectList` übergab die Werte von `$conds` ohne
Umwandlung nach Datentyp. `$ClassID` aus dem URL-Parameter war die Zeichenkette `"49"`. MongoDB speichert die Ganzzahl `49`.
`{contentclass_id: "49"}` passte auf nichts. Korrektur: anhand der Felddefinition mit `(int)` umwandeln.

**Offen:** `initializeCopy()` ruft `arrayQuery` mit einem JOIN über `[ezcontentclass, ezcontentclass_name]`
auf, um den Namen der Kopie eindeutig zu machen. Gibt `[]` zurück → PHP-Warnung → die angehängte Nummer im Namen entfällt.
Nicht fatal: Die Kopie erhält den Namen „Copy of Folder“ ohne laufende Nummer. Siehe Backlog §11.


### Modul: `content` (Inhaltsobjekte)

**Views:** `view`, `edit`, `history`, `translate`, `move`, `copy`, `remove`, `versionview`, `restore`

| View | URL | Status |
|---|---|---|
| `view/full` | `/content/view/full/2` | ✅ Funktioniert — vollständige Knotenansicht mit Attributen |
| `edit` | `/content/edit/14` | ✅ Funktioniert — Attribute bearbeiten, veröffentlichen |
| `history` | `/content/history/242` | ✅ Funktioniert nach der Korrektur der int-Umwandlung beim Laden der Version |
| `versionview` | `/content/versionview/242/12` | ✅ Funktioniert |
| `translate` | `/content/translate/...` | Nicht getestet |
| `move` | `/content/move/...` | Nicht getestet |
| `copy` | `/content/copy/...` | Nicht getestet |
| `remove` | `/content/remove/...` | Nicht getestet (verwendet `removeThis()`) |

**Bekannte offene Probleme in `ezcontentobject.php`:**

| Methode | Problem |
|---|---|
| `relatedObjectCount()` | Kein MongoDB-Zweig — gibt 0 zurück (wird auf den Seiten edit/history aufgerufen) |
| `getStates()` | ✅ | `allowedAssignStateIDList()` und `stateIdentifierArray()` vollständig mit einer zweistufigen Aggregation gepatcht; Pipeline mit `$lookup` |
| `addContentObjectRelation()` | `to_contentobject_id` wird nicht in int umgewandelt — das Einfügen der Relation kann fehlschlagen |
| Mehrere Aufrufe von `arrayQuery` (~30+) | Viele Abfragen auf einzelne Tabellen sind noch nicht ersetzt |


### Modul: `setup`

| View | Status |
|---|---|
| `cache` | ✅ Funktioniert |
| `extensions` | ✅ Funktioniert |
| `info` | ✅ Funktioniert |
| `rad` | Nicht getestet |
| `systemupgrade` | ✅ Funktioniert — Prüfung `is_object()` vor `transformSchema()` ergänzt; zeigt für MongoDB „Database check OK.“ |


### Modul: `user`

| View | Status |
|---|---|
| `login` | ✅ Funktioniert |
| `logout` | ✅ Funktioniert |
| `preferences` | ✅ Funktioniert |
| `password` | ✅ Funktioniert |


### Modul: `state`

| View | Status |
|---|---|
| `groups` | ✅ Funktioniert (Liste der Statusgruppen) |
| `view` | Nicht getestet |
| `edit` | Nicht getestet |


### Modul: `role`

| View | Status |
|---|---|
| `list` | Nicht getestet — in `ezrole.php` sind noch viele Aufrufe von `arrayQuery` enthalten |
| `view` | Nicht getestet |
| `edit` | Nicht getestet |


### Modul: `section`

| View | Status |
|---|---|
| `list` | ✅ Funktioniert — MongoDB-Aggregation mit `$count` in `sectionCount()` ergänzt |
| `edit` | Nicht getestet |


### Modul: `language`

| View | Status |
|---|---|
| `list` | ✅ Funktioniert — Bitmasken-Aggregation in `objectCount()` + `classCount()` ergänzt (`$bitwiseAnd` innerhalb von `$expr`) |
| `edit` | Nicht getestet |


### Modul: `search`

| View | Status |
|---|---|
| `stats` | ✅ Funktioniert — MongoDB-Zweig mit `$count`-Aggregation in `stats.php` ergänzt; Pipeline für `mostFrequentPhraseArray()` in `ezsearchlog.php` ergänzt |


### Modul: `shop`

| View | Status |
|---|---|
| `orderlist` | Nicht getestet — `ezorder.php` und `ezbasket.php` enthalten mehrere Aufrufe von `arrayQuery` |
| `basket` | Nicht getestet |


### Modul: `infocollector`

| View | Status |
|---|---|
| `overview` | ✅ Funktioniert — Pipeline mit `$lookup` direkt in `overview.php` ergänzt; die JOIN-Abfragen der Klasse `ezinformationcollection.php` bleiben bestehen (die Übersichtsseite ruft sie nicht auf) |


### Modul: `workflow`

| View | Status |
|---|---|
| `grouplist` | Nicht getestet |
| `processlist` | Nicht getestet |


### Modul: `url`

| View | Status |
|---|---|
| `list` | ✅ Funktioniert — `count()` + `fetchAll()` + `buildMongoMatch()` in `ezurlaliasquery.php` ergänzt; der JOIN über 4 Tabellen in `ezurl.php` durch eine Pipeline mit `$lookup` ersetzt |
| `translate` | Nicht getestet |


## 7. Muster und Konventionen

### Das übliche Verzweigungsmuster für MongoDB

```php
$db = eZDB::instance();
if ( $db->databaseName() === 'mongo' )
{
    $rows = $db->aggregate( 'collection_name', [
        [ '$match'   => [ 'field' => (int)$value ] ],
        [ '$lookup'  => [ 'from' => 'other_collection', 'localField' => 'id',
                          'foreignField' => 'ref_id', 'as' => '_join' ] ],
        [ '$unwind'  => '$_join' ],
        [ '$addFields' => [ 'joined_field' => '$_join.field' ] ],
        [ '$project' => [ '_id' => 0, '_join' => 0 ] ],
    ]);
    return eZPersistentObject::handleRows( $rows, 'eZSomeClass', $asObject );
}
$rows = $db->arrayQuery( $sql );
return eZPersistentObject::handleRows( $rows, 'eZSomeClass', $asObject );
```

### Präfixvergleich auf `path_string` für Abfragen auf Teilbäume

```php
$parent = $db->aggregate( 'ezcontentobject_tree', [
    [ '$match'   => [ 'node_id' => (int)$nodeID ] ],
    [ '$project' => [ '_id' => 0, 'path_string' => 1, 'depth' => 1 ] ],
]);
$pathString = $parent[0]['path_string'];
$matchStage = [
    'path_string' => [ '$regex' => '^' . preg_quote( $pathString ) . '.+' ],
];
```

### ID-Parameter immer in int umwandeln

URL-Parameter (`$Params['ClassID']`, `$Params['NodeID']` usw.) kommen als Zeichenketten an.
MongoDB speichert Ganzzahlen. Wandeln Sie immer um: `$id = (int)$id` am Anfang jeder Fetch-Methode,
die eine ID aus einem URL-Parameter oder einem POST-Feld erhält.

### `$project` muss `'_id' => 0` enthalten

MongoDB gibt `_id` immer zurück, wenn es nicht ausdrücklich ausgeschlossen wird. Fehlt `'_id' => 0`, entsteht ein
unerwartetes Feld `_id` in den Zeilen-Arrays, das die von `handleRows()` erzeugten PHP-ORM-Objekte verfälscht.

### Für Filter mit MongoDB-Operatoren nie `find()` verwenden

Verwenden Sie immer `aggregate()`, wenn der Filter `$or`, `$in`, `$nin`, `$expr` oder ein
verschachteltes Array enthält. `translateConditions()` verfälscht sie stillschweigend.

### Die Prüfung `databaseName()`

Verwenden Sie `=== 'mongo'` (in Kleinbuchstaben). Die Methode gibt die Zeichenkette `'mongo'` zurück.

### Nach dem Bearbeiten touch ausführen

```bash
touch /path/to/modified/file.php
```
OPcache mit `revalidate_freq=2s` — touch ist nötig, um den zwischengespeicherten Bytecode ungültig zu machen.


## 8. Betriebshinweise

### PHP-FPM-Timeout — der letzte Schritt des Setup-Assistenten

> **Seien Sie unbesorgt:** Der Setup-Assistent **wird erfolgreich abgeschlossen** — er braucht nur mehr
> Zeit als eine übliche SQL-basierte Installation. Brechen Sie die Anfrage im Browser nicht ab.

Der letzte Schritt des Setup-Assistenten („Configuration / Create Sites“) ist in jeder Installation von
Exponential CMS der langsamste. Auf einer MongoDB-Installation ist er deutlich langsamer als auf einer
MySQL/MariaDB-Installation, weil jedes Inhaltsobjekt, das während der Paketinstallation veröffentlicht wird,
die vollständige Veröffentlichungspipeline des Kernels auslöst (Speichern der Attribute, Anlegen der Baumknoten,
Erzeugen der URL-Aliase, Zusammensetzen der Namen, Ablauf der Caches). Mit dem Paket `sevenx_democontent` bedeutet das
rund 193 Objekte, von denen jedes 10–15 einzelne Schreiboperationen in MongoDB erfordert.

**Es gibt zwei voneinander unabhängige Timeout-Mechanismen, und Sie müssen beide verstehen:**

| Zeitgeber | Wer ihn steuert | Kann PHP ihn überschreiben? | Standard |
|---|---|---|---|
| Ausführungszeit des PHP-Skripts | `max_execution_time` in `php.ini` / `set_time_limit()` im Code | Ja — `set_time_limit(0)` schaltet ihn ab | 30 s (Plesk-Standard) |
| Zeitlimit der FPM-Anfrage (Echtzeit) | `request_terminate_timeout` in der Konfiguration des PHP-FPM-Pools (Plesk → PHP-Einstellungen → FPM-Pool) | **Nein** — wird vom FPM-Masterprozess durchgesetzt, unabhängig davon, was das Skript tut | 190 s (Plesk-Standard), 390 s (aktuelle Einstellung) |

Der FPM-Wert `request_terminate_timeout` ist derjenige, der den Setup-Assistenten abbricht. PHPs eigenes
`set_time_limit()` spielt keine Rolle mehr, sobald `request_terminate_timeout` greift — der FPM-Master sendet
dem Worker `SIGKILL`.

**Was Sie in Plesk einstellen:**

1. Melden Sie sich bei Plesk an → Domains → Ihre Domain → PHP-Einstellungen
2. Setzen Sie unter „Zusätzliche Konfigurationsdirektiven“ (oder im Konfigurationsbereich des FPM-Pools):
   ```
   request_terminate_timeout = 600
   ```
   600 Sekunden (10 Minuten) liegen deutlich über der beobachteten Installationsdauer nach den
   Leistungsoptimierungen, die in Abschnitt 22 beschrieben sind.
3. Nachdem der Setup-Assistent abgeschlossen ist, können Sie den Wert für den normalen Betrieb wieder auf 60–120 s senken.

**Warum nicht dauerhaft 600 s einstellen?** Ein langes FPM-Timeout bedeutet, dass eine hängende oder in einer Schleife
festsitzende Anfrage einen Worker bis zu 10 Minuten lang belegt, bevor sie beendet wird; unter Last kann das
den Worker-Pool erschöpfen. Erhöhen Sie den Wert nur während der Installation.

**Alternative: den Installer über die Kommandozeile ausführen** (umgeht FPM vollständig):
```bash
# Not yet supported by this project's setup wizard — future enhancement.
# For now, use the browser wizard with an elevated FPM timeout.
```

### PHP-FPM

**Starten Sie PHP-FPM nie neu.** Verwenden Sie `touch`, um den OPcache zu umgehen.

### MongoDB-Shell

```bash
mongosh "mongodb://db:YOUR_PASSWORD@localhost:27017/exp"
```

Nützliche Abfragen:
```javascript
// Check class group records for a class
db.ezcontentclass_classgroup.find({contentclass_id: 49})

// Check a content class
db.ezcontentclass.find({id: 49})

// Fix empty name
db.ezcontentobject_name.updateOne({contentobject_id: 242, content_version: 12}, {$set: {name: 'Home'}})

// Delete orphaned records
db.ezcontentclass_classgroup.deleteMany({contentclass_id: 49, contentclass_version: 2})
```

### OPcache

`opcache.validate_timestamps=1`, `opcache.revalidate_freq=2`. Nach jeder Änderung an einer PHP-Datei:
```bash
touch /path/to/file.php
```

### Debug-Leiste

Schalten Sie sie in der Administration über die Kurzeinstellungen → Debug-Ausgabe ein. Die Abfragen erscheinen mit dem
Namen der Collection und dem Filter im Bereich „SQL debug output“. Einträge MONGO TODO erscheinen dort mit Angaben zum Aufrufer.



## 9. Testplan — Administration

Testen Sie alle Punkte nach jeder Codeänderung. Vor jeder Bereitstellung ein vollständiger Regressionstest.

### 9.1 Authentifizierung

| Schritt | Erwartet |
|---|---|
| `https://admin.example.com/` ohne Anmeldung aufrufen | Weiterleitung zur Anmeldeseite |
| Mit `admin` / richtigem Passwort anmelden | Weiterleitung zum Dashboard |
| Mit falschem Passwort anmelden | Fehlermeldung; man bleibt auf der Anmeldeseite |
| Über die obere Navigation abmelden → „Abmelden: admin“ | Weiterleitung zur Anmeldeseite |

### 9.2 Dashboard

| Schritt | Erwartet |
|---|---|
| Das Dashboard aufrufen | Die Seite lädt; kein MONGO TODO in der Debug-Ausgabe |
| Den Link „Inhaltsstruktur“ prüfen | Führt zu `/content/view/full/2` |

### 9.3 Inhaltsstruktur (content/view/full)

| Schritt | Erwartet |
|---|---|
| Den obersten Knoten öffnen (Knoten 2) | Listet die untergeordneten Knoten mit Namen auf |
| Eine Ebene tiefer gehen | Die untergeordneten Knoten werden richtig aufgelistet |
| Auf einen Ordnerknoten klicken | Zeigt die Attribute und Unterelemente des Knotens |
| Die Brotkrümelnavigation prüfen | Zeigt den richtigen Pfad, z. B. „Inhaltsstruktur / Ordnername“ |
| Die Anzahl „Objekte“ in der Klassenliste prüfen | Zeigt für jede Klasse die richtige Ganzzahl |

### 9.4 Inhaltsobjekt — bearbeiten und veröffentlichen

| Schritt | Erwartet |
|---|---|
| Ein Objekt der Klasse Article öffnen (z. B. Knoten 14) | Die vollständige Ansicht wird mit allen Attributen dargestellt |
| Auf „Bearbeiten“ klicken | Das Bearbeitungsformular lädt mit allen Attributfeldern gefüllt |
| Ein Textfeld ändern | Keine JavaScript-Fehler |
| Auf „Veröffentlichen“ klicken | Leitet zur Ansicht zurück; die neue Version ist die aktuelle |
| Die Versionsgeschichte öffnen (`/content/history/NNN`) | Zeigt alle Versionen; die neueste oben |
| Die Versionsnummern prüfen | Keine Dubletten; richtige Reihenfolge |
| Eine Entwurfsversion löschen | Die Version verschwindet aus der Liste; keine verwaisten Attribute in MongoDB |

### 9.5 Inhaltsobjekt — Objektname

| Schritt | Erwartet |
|---|---|
| Eine neue Version eines beliebigen Objekts veröffentlichen | Der Objektname in der Brotkrümelnavigation und in der Liste entspricht dem Namensmuster |
| `ezcontentobject_name` in MongoDB prüfen | Das Feld `name` ist eine nicht leere Zeichenkette |
| `ezcontentobject.name` prüfen | Stimmt mit dem Namensdatensatz überein |

### 9.6 Klassengruppen

| Schritt | Erwartet |
|---|---|
| Setup → Klassen → `/class/grouplist` aufrufen | Listet alle Gruppen auf (Content, Users, Media usw.) |
| Auf die Gruppe „Content“ klicken | Führt zu `/class/classlist/1` |
| Die Anzahl prüfen | „Classes inside (27)“ oder die aktuelle Anzahl |
| Die Spalte „Objekte“ prüfen | Jede Klasse zeigt die richtige Anzahl an Objekten |
| Debug-Ausgabe | 0 Einträge MONGO TODO (alle Aufrufe von `hasVisibleNode()` verwenden jetzt aggregate) |

### 9.7 Klassenliste

| Schritt | Erwartet |
|---|---|
| `/class/classlist/1` aufrufen | Listet alle Klassen der Gruppe Content auf |
| Auf einen Klassennamen klicken (z. B. „Folder“) | Führt zu `/class/view/1` |
| Brotkrümelnavigation auf der Ansichtsseite | Zeigt „Klassengruppen / Content“ (nicht nur „Klassengruppen“) |

### 9.8 Klasse bearbeiten (vorhandene Klasse)

| Schritt | Erwartet |
|---|---|
| `/class/edit/47` aufrufen | Das Bearbeitungsformular lädt; keine Seite „Class locked“ |
| Brotkrümelnavigation | Zeigt „Klassengruppen / Content / Trash Bin (TMP Folder)“ |
| Den Klassennamen bearbeiten | Das Formular nimmt die Eingabe an |
| Auf „Speichern“ klicken | Speichert; leitet zur Klassenliste weiter; kein MONGO TODO beim Speichern |
| Eine Klasse mit einem Attribut ezinisetting bearbeiten | Das Speichern gelingt, auch wenn keine INI-Instanzen ausgewählt sind |

### 9.9 Klasse kopieren

| Schritt | Erwartet |
|---|---|
| In der Klassenliste auf das Kopiersymbol neben „Folder“ klicken | Leitet zur Klassenliste weiter |
| Die neue Klasse „Copy of Folder“ erscheint | In der Gruppe Content aufgeführt |
| `/class/edit/NNN` für die kopierte Klasse aufrufen | Das Bearbeitungsformular lädt |
| Brotkrümelnavigation auf der Bearbeitungsseite | Zeigt „Klassengruppen / Content / Copy of Folder“ (NICHT „Klassengruppen / Copy of Folder“) |
| Die kopierte Klasse speichern | Wird erfolgreich gespeichert |
| Die kopierte Klasse löschen | Die Klasse verschwindet aus der Liste |

### 9.10 Klasse anlegen

| Schritt | Erwartet |
|---|---|
| In der Klassenliste auf die Schaltfläche zum Anlegen einer neuen Klasse klicken | Das Formular lädt |
| Einen Klassennamen eingeben, ein Attribut vom Typ Text hinzufügen | Keine Fehler |
| Speichern | Die neue Klasse erscheint in der Klassenliste |
| Die neue Klasse bearbeiten | Lädt richtig, die Brotkrümelnavigation zeigt den Gruppennamen |

### 9.11 Versionsgeschichte

| Schritt | Erwartet |
|---|---|
| Ein Objekt drei- oder mehrmals veröffentlichen | Jede Veröffentlichung legt eine neue Version an |
| `/content/history/NNN` öffnen | Zeigt alle Versionen, jede mit den richtigen Daten |
| Die letzte Version in der Liste zeigt die richtigen Daten | Keine Kopie der vorletzten (Referenzfehler behoben) |
| Version 1 löschen | Verschwindet; die übrigen Versionen sind nicht betroffen |
| Alle Versionen außer der aktuellen löschen | Das Objekt bleibt erhalten; nur die veröffentlichte Version ist vorhanden |

### 9.12 Status

| Schritt | Erwartet |
|---|---|
| `/state/groups` aufrufen | Listet die Statusgruppen auf |
| Eine Statusgruppe öffnen | Listet die Status auf |
| Einen Status bearbeiten | Das Formular lädt |

### 9.13 Benutzerverwaltung

| Schritt | Erwartet |
|---|---|
| Den Baum der Benutzerkonten aufrufen | Listet die Benutzergruppen auf |
| Den Benutzer Admin User öffnen | Zeigt das Benutzerprofil |
| Den Benutzer Admin User bearbeiten | Das Formular lädt mit den Feldern für Name und E-Mail |
| Das Passwort ändern | Speichert; die Anmeldung mit dem neuen Passwort gelingt |


## 10. Testplan — Website

### 10.1 Startseite

| Schritt | Erwartet |
|---|---|
| `https://www.example.com/` aufrufen | Die Startseite wird dargestellt |
| Kein Timeout 504 | Die Seite lädt in weniger als 2 Sekunden |
| Die Brotkrümelnavigation prüfen | Zeigt die Wurzel der Website |
| Debug: keine Endlosschleife | Der Cache der URL-Wildcards wird schnell neu aufgebaut (erster Aufruf) |

### 10.2 Navigation im Inhalt

| Schritt | Erwartet |
|---|---|
| Die URL eines Ordners aufrufen (z. B. `/News/`) | Listet die untergeordneten Artikel auf |
| Auf einen Artikel klicken | Die vollständige Artikelansicht wird dargestellt |
| Alle Attributwerte prüfen | Text-, Bild- und Datumsattribute werden richtig angezeigt |
| Die Brotkrümelnavigation prüfen | Richtiger Pfad von der Wurzel bis zum aktuellen Knoten |
| Mit der Zurück-Schaltfläche zum Ordner | Kehrt zur Liste zurück |

### 10.3 URL-Aliase

| Schritt | Erwartet |
|---|---|
| Eine URL wie `/News/Article-title` aufrufen | Das richtige Inhaltsobjekt wird dargestellt |
| Eine URL mit mehreren Pfadsegmenten aufrufen | Jedes Segment wird richtig aufgelöst (Verkettung über die Elternkette) |
| Eine nicht vorhandene URL | Gibt 404 zurück oder leitet zur Startseite weiter |
| Eine Alias-URL | Leitet zur kanonischen URL weiter |

### 10.4 Bilder und Medien

| Schritt | Erwartet |
|---|---|
| Artikel mit eingebettetem Bild | Das Bild wird im Fließtext dargestellt |
| Artikel mit Bildattribut | Das Bildattribut zeigt ein Vorschaubild |
| URL eines Bildobjekts | Die direkte Bild-URL wird aufgelöst |

### 10.5 Objektstatus

| Schritt | Erwartet |
|---|---|
| Objekt mit gesetztem Status | Template-Bedingungen, die auf dem Status beruhen, funktionieren |
| Verborgenes Objekt | In Listen nicht sichtbar |

### 10.6 Suche (falls aktiviert)

| Schritt | Erwartet |
|---|---|
| Eine Suchanfrage absenden | Die Ergebnisseite wird dargestellt |
| Ergebnisliste | Zeigt die passenden Inhaltsobjekte |

### 10.7 Verhalten des Caches

| Schritt | Erwartet |
|---|---|
| Den Cache in der Administration leeren | Die Cache-Dateien werden entfernt; die nächste Anfrage an die Website erzeugt sie neu |
| Zweiter Aufruf derselben Seite | Schneller (Antwort aus dem Cache) |
| Ein Objekt bearbeiten und veröffentlichen | Der Cache der Website wird ungültig; der aktualisierte Inhalt ist sichtbar |

### 10.8 Weitergehende Inhaltstests

| Test | Schritte | Erwartet |
|---|---|---|
| Inhaltsliste eines Teilbaums | Einen Ordner mit 10 oder mehr Unterelementen aufrufen | Alle Unterelemente werden aufgelistet; die Seitenaufteilung funktioniert |
| Objekt mit XML-Text (Rich Text) | Einen Artikel mit formatiertem Fließtext öffnen | Fett, kursiv und Links werden richtig dargestellt |
| Objekt mit Schlüsselwortattribut | Den Artikel ansehen | Die Schlüsselwörter werden angezeigt |
| Objekt mit verwandten Objekten | Den Artikel ansehen | Die verwandten Artikel werden aufgelistet |
| Objekt in mehreren Sprachen | In einer anderen als der Standardsprache ansehen | Die richtige Übersetzung wird gezeigt |
| Tiefe URL (5 oder mehr Ebenen) | Zu einem tief verschachtelten Knoten navigieren | Die URL wird aufgelöst; die Brotkrümelnavigation ist richtig |


## 11. Protokoll gelöster Probleme

Diese Tabelle ist eine historische Aufzeichnung jedes `arrayQuery`- bzw. SQL-Pfads, der gefunden, gemeldet und behoben wurde. Alle Punkte sind gelöst.

| Priorität | Datei | Stelle | Lösung |
|---|---|---|---|
| ~~HOCH~~ | ~~`ezcontentobject.php`~~ | ~~`relatedObjectCount()`~~ | ✅ Bestätigt bereits gepatcht — eine MongoDB-Pipeline mit `$match` + `$count` ersetzt das SQL mit mehreren JOINs |
| ~~HOCH~~ | ~~`ezcontentobject.php`~~ | ~~`getStates()` / `allowedAssignStateIDList()` / `stateIdentifierArray()`~~ | ✅ Bestätigt bereits gepatcht — beide Methoden haben MongoDB-Zweige mit einer Pipeline mit `$lookup` |
| ~~HOCH~~ | ~~`ezcontentclass.php`~~ | ~~`initializeCopy()` ~965~~ | ✅ Bereits gepatcht — `$lookup` auf `[ezcontentclass, ezcontentclass_name]` |
| ~~HOCH~~ | ~~`ezcontentobject.php`~~ | ~~`addContentObjectRelation()`~~ | ✅ Behoben — die int-Umwandlung `$toObjectID = (int)$toObjectID` steht jetzt vor ihrer Verwendung im MongoDB-Zweig |
| ~~MITTEL~~ | ~~`ezkeywordtype.php`~~ | ~~`deleteStoredObjectAttribute()` Zeilen 149, 221, 235~~ | ✅ Behoben — zweistufige Aggregation (`$group` + `$match cnt=1`); `deleteWhere` zum Aufräumen |
| ~~MITTEL~~ | ~~`ezxmltexttype.php`~~ | ~~`deleteStoredObjectAttribute()` Zeile 811~~ | ✅ Behoben — eine Anti-Join-Aggregation mit `$nin` findet verwaiste `ezurl`-IDs; `deleteWhere` entfernt sie |
| ~~MITTEL~~ | ~~`kernel/class/edit.php`~~ | ~~Debug-Zeilen `error_log()`~~ | ✅ Bestätigt sauber — keine Aufrufe von `error_log` in der Datei |
| ~~MITTEL~~ | ~~`kernel/content/attribute_edit.php`~~ | ~~Zeilen 365, 402~~ | ✅ Entfernt — die Debug-Zeilen `PUBLISH_VALIDATE` und `PUBLISH_BEFORE_HOOKS` gelöscht |
| ~~MITTEL~~ | ~~`kernel/content/edit.php`~~ | ~~Zeile 749~~ | ✅ Entfernt — die Debug-Zeile `PUBLISH_DEBUG operationResult` gelöscht |
| ~~MITTEL~~ | ~~`ezfsfilehandler.php`~~ | ~~mehrere~~ | ✅ Gelöst — die Debug-Aufrufe `error_log()` für PROCESSCACHE entfernt |
| ~~NIEDRIG~~ | ~~`ezrole.php`~~ | ~~Zeilen 543, 682, 765, 787, 839, 868~~ | ✅ Bestätigt bereits gepatcht — alle 6 aufgeführten Aufrufe von `arrayQuery` stehen in SQL-`else`-Zweigen; der MongoDB-Pfad läuft zuerst und kehrt früh zurück |
| ~~NIEDRIG~~ | ~~`ezinformationcollection.php`~~ | ~~Zeilen 422, 437, 577, 601~~ | ✅ Behoben — MongoDB-Zweige mit `$count`, `$group` und zweistufigem `$match` für alle 4 JOIN-Abfragen ergänzt |
| ~~NIEDRIG~~ | ~~`eznodeassignment.php`~~ | ~~Zeilen 471, 496~~ | ✅ Bestätigt bereits gepatcht |
| ~~NIEDRIG~~ | ~~`ezpolicylimitation.php`~~ | ~~Zeile 374~~ | ✅ Behoben — zweistufige Aggregation |
| ~~NIEDRIG~~ | ~~`ezorder.php`~~ | ~~Zeile 240~~ | ✅ Behoben — Zweig für die Sortierung nach Benutzernamen in `active()`: `$lookup` auf `ezcontentobject`, `$addFields` + `$sort` |
| ~~NIEDRIG~~ | ~~`ezbasket.php`~~ | ~~Zeilen 478, 512~~ | ✅ Behoben — `cleanupExpired()`: Lookup der abgelaufenen Sitzungen mit `$in`; `cleanup()`: vollständiger Durchlauf; beide verwenden `deleteWhere` |
| ~~NIEDRIG~~ | ~~`ezdiscount.php`~~ | ~~Zeilen 47, 63~~ | ✅ Behoben — Aggregation mit `$in` für Unterregeln; Aggregation mit skalarem `$match` für Einschränkungswerte |
| ~~NIEDRIG~~ | ~~`ezdbgarbagecollector.php`~~ | ~~Zeilen 105, 219, 310, 393~~ | ✅ Behoben — Anti-Join-Aggregationen mit `$nin` ersetzen alle 4 Muster zum Aufräumen verwaister Datensätze mit RIGHT JOIN |
| ~~NIEDRIG~~ | ~~`ezinformationcollectionattribute.php`~~ | ~~Zeile 176~~ | ✅ Behoben — einfache `aggregate` auf `ezcontentclass_attribute` für das Nachschlagen von `serialized_name_list` |
| ~~NIEDRIG~~ | ~~`ezcollaborationitem.php`~~ | ~~Zeilen 467, 484~~ | ✅ Bestätigt bereits gepatcht — der MongoDB-Pfad mit einer Pipeline mit `$lookup` kehrt früh zurück; die SQL-Zweige mit `arrayQuery` sind für MongoDB nicht erreichbar |
| ~~NIEDRIG~~ | ~~`lib/ezimage/classes/ezimagemanager.php`~~ | ~~Zeile 195~~ | ✅ Behoben — der Absturz `array_key_exists(null, ...)` unter PHP 8.5 ist gelöst |

### Vorlage für die Korrektur einer einfachen `arrayQuery` auf eine Tabelle

```php
// Before:
$rows = $db->arrayQuery("SELECT * FROM eztable WHERE id='$id'");

// After:
if ( $db->databaseName() === 'mongo' ) {
    $rows = $db->aggregate( 'eztable', [
        [ '$match'   => [ 'id' => (int)$id ] ],
        [ '$project' => [ '_id' => 0 ] ],
    ]);
} else {
    $rows = $db->arrayQuery("SELECT * FROM eztable WHERE id='$id'");
}
```

### Vorlage für die Korrektur einer `arrayQuery` mit JOIN

```php
// Before:
$rows = $db->arrayQuery("SELECT a.*, b.name FROM ta a, tb b WHERE a.ref_id = b.id AND a.filter='$val'");

// After:
if ( $db->databaseName() === 'mongo' ) {
    $rows = $db->aggregate( 'ta', [
        [ '$match'    => [ 'filter' => $val ] ],
        [ '$lookup'   => [ 'from' => 'tb', 'localField' => 'ref_id', 'foreignField' => 'id', 'as' => '_b' ] ],
        [ '$unwind'   => '$_b' ],
        [ '$addFields'=> [ 'name' => '$_b.name' ] ],
        [ '$project'  => [ '_id' => 0, '_b' => 0 ] ],
    ]);
} else {
    $rows = $db->arrayQuery("SELECT a.*, b.name FROM ta a, tb b WHERE a.ref_id = b.id AND a.filter='$val'");
}
```


## 12. Testplan — Cronjobs / Skripte

Alle folgenden Skripte liegen unter `bin/php/` oder `cronjobs/` und werden über
`runcronjobs.php` (über das Web ausgelöst) oder direkt über die Kommandozeile aufgerufen. Jeder Eintrag zeigt:
- **Zweck** — was das Skript tut
- **MongoDB-Status** — ob es funktioniert, blockiert ist oder einen Zweig braucht
- **Wichtige Korrektur** — den konkreten Aufruf `arrayQuery`/`query`, der gepatcht werden muss



### 13.1  `runcronjobs.php` — der Cron-Starter

**Zweck:** Ein Starter, der `eZScript` + `eZCLI` initialisiert, `cronjob.ini[CronjobSettings] MaxScriptExecutionTime`
liest und danach jedes aktivierte Cronjob-Skript über `include` ausführt.

**MongoDB-Status:** ✅ Der Starter selbst enthält kein SQL. Funktioniert unverändert.

**So führen Sie ihn aus:**
```bash
/opt/plesk/php/8.5/bin/php runcronjobs.php --siteaccess=sevenx_site --allow-root-user
```


### 13.2  `bin/php/updateniceurls.php` — URL-Aliase neu aufbauen

**Zweck:** Baut alle URL-Aliase in `ezurlalias_ml` für jeden Knoten des
Inhaltsbaums neu auf. Wird nach Massenimporten aufgerufen oder wenn Aliase veraltet sind.

**MongoDB-Status:** ⚠️  Teilweise behoben.

- ✅ Die Abfrage der obersten Knoten in Zeile 1043 wurde auf `aggregate('ezcontentobject_tree', [$match depth=1, $sort, $project])` umgestellt
- ⚠️  `storePath()` in `ezurlaliasml.php` enthält noch viele Aufrufe von `arrayQuery` und
  `query` (Zeilen 574, 610, 669, 703, 756, 774, 820 usw.), die nichts bewirken.
  Das Skript legt daher neue Alias-Einträge auf oberster Ebene an (IDs 421, 431, 461 …),
  meldet für die untergeordneten Knoten aber `Updated 0/219`.
- ✅ Die beschädigten Daten der Elternkette wurden direkt über die PHP-Kommandozeile repariert (siehe §13.9).

**Erneut ausführen, sobald `storePath()` vollständig umgestellt ist:**
```bash
/opt/plesk/php/8.5/bin/php bin/php/updateniceurls.php \
  --siteaccess=sevenx_site --allow-root-user
```


### 13.3  `bin/php/updatesearchindex.php` — den Suchindex vollständig neu aufbauen

**Zweck:** Baut den Volltext-Suchindex (eZFind/Solr oder eZSearch)
für alle Inhaltsobjekte neu auf.

**MongoDB-Status:** 🔴 Nicht getestet. Vermutlich durch Aufrufe von `arrayQuery` in
`eZSearchEngine` oder `eZContentObject::fetchList` blockiert.

**Wichtige Abhängigkeit:** `eZContentObject::fetchList()` / `fetchObjectList()` —
prüfen Sie, ob der MongoDB-Zweig von `fetchObjectList()` in `ezpersistentobject.php`
`SORT` und `LIMIT` richtig behandelt.


### 13.4  `cronjobs/indexcontent.php` — inkrementelle Suchindizierung

**Zweck:** Liest `ezpending_actions WHERE action IN ('index_object',
'index_moved_node')`, lädt jedes Objekt und übergibt es der Suchmaschine.
Löscht danach die Zeilen aus `ezpending_actions`.

**MongoDB-Status:** 🔴 Blockiert.

**Zu korrigierende Abfragen:**
```php
// Line ~20 (approximate):
$db->arrayQuery("SELECT * FROM ezpending_actions WHERE action='index_object' OR action='index_moved_node'");

// Fix:
if ( $db->databaseName() === 'mongo' ) {
    $rows = $db->aggregate('ezpending_actions', [
        ['$match' => ['action' => ['$in' => ['index_object', 'index_moved_node']]]],
        ['$sort'  => ['id' => 1]],
    ]);
} else { /* original arrayQuery */ }
```

Nach dem Indizieren jedes Objekts steht `DELETE FROM ezpending_actions WHERE id=X` — ersetzen Sie es
durch `$db->collection('ezpending_actions')->deleteOne(['id' => (int)$id])`.


### 13.5  `cronjobs/staticcache_cleanup.php` — Aufräumen des statischen Caches

**Zweck:** Liest `ezpending_actions WHERE action='static_store'`, holt die
URL jedes ausstehenden Knotens, schreibt eine statische HTML-Datei und entfernt danach die Zeile.

**MongoDB-Status:** 🔴 Blockiert (dasselbe Muster mit `ezpending_actions` wie in §13.4).

**Korrekturmuster:** wie in §13.4 — `aggregate('ezpending_actions', [$match action='static_store'])`.


### 13.6  `cronjobs/unlock.php` — gesperrte Objekte entsperren

**Zweck:** Findet alle Inhaltsobjekte im Status „locked“ und setzt sie auf den
Standardstatus „not-locked“ zurück, indem es `ezcobj_state_link` aktualisiert.

**MongoDB-Status:** 🔴 Blockiert. Enthält einen JOIN:

```sql
SELECT ezcobj_state_link.contentobject_id
FROM   ezcobj_state_link, ezcobj_state
WHERE  ezcobj_state.identifier = 'locked'
  AND  ezcobj_state_link.contentobject_state_id = ezcobj_state.id
```

**Korrektur:**
```php
if ( $db->databaseName() === 'mongo' ) {
    $lockedState = $db->aggregate('ezcobj_state', [
        ['$match'   => ['identifier' => 'locked']],
        ['$project' => ['_id' => 0, 'id' => 1]],
    ]);
    if ( !empty($lockedState) ) {
        $lockedStateId = (int)$lockedState[0]['id'];
        $rows = $db->aggregate('ezcobj_state_link', [
            ['$match'   => ['contentobject_state_id' => $lockedStateId]],
            ['$project' => ['_id' => 0, 'contentobject_id' => 1]],
        ]);
    }
} else { /* original arrayQuery JOIN */ }
```

Das anschließende `UPDATE ezcobj_state_link SET contentobject_state_id=1` muss durch
`$db->collection('ezcobj_state_link')->updateMany(...)` ersetzt werden.


### 13.7  `cronjobs/session_gc.php` — Garbage Collection der Sitzungen

**Zweck:** Ruft `eZSession::garbageCollector()` und
`eZBasket::cleanupExpired()` auf, um veraltete Sitzungszeilen zu löschen.

**MongoDB-Status:** ⚠️  Hängt von den Implementierungen von `eZSession` und `eZBasket` ab.
`eZSession` speichert in die Collection `ezsession`; `garbageCollector()` löscht Zeilen,
die älter als `SessionTimeout` sind. Braucht vermutlich einen MongoDB-Zweig in `eZSession`.


### 13.8  `cronjobs/trashpurge.php` — den Papierkorb leeren

**Zweck:** Löscht Objekte endgültig, die länger als
`content.ini[RemoveSettings] TrashPurgeDuration` im Papierkorb liegen.

**MongoDB-Status:** ⚠️  Ruft `eZScriptTrashPurge::run()` auf, das
`eZContentObject::fetchList` und `removeThis()` verwendet. `fetchObjectList` hat einen
MongoDB-Zweig. `removeThis()` (endgültiges Löschen) braucht vermutlich einen MongoDB-Zweig — nicht getestet.


### 13.9  Direkte Datenreparatur in MongoDB — die Kette der URL-Aliase

Die folgende Reparatur wurde von Hand vorgenommen, nachdem der teilweise Lauf von `updateniceurls.php`
Alias-Einträge auf oberster Ebene mit neuen IDs angelegt hatte, die untergeordneten Einträge aber weiter auf
veraltete Eltern-IDs aus der ursprünglichen SQL-Migration verwiesen.

**Ursache:** `getNewID()` bewirkte nichts (es rief `query('INSERT INTO ezurlalias_ml_incr')` auf,
was im MongoDB-Adapter ohne Wirkung ist), daher erhielten die ersten drei Aliase auf oberster Ebene
`id=0`. Nachdem `nextAtomicID()` ergänzt und `updateniceurls.php` erneut ausgeführt worden war, entstanden neue
Einträge auf oberster Ebene mit echten IDs, die untergeordneten Einträge verwiesen aber weiterhin auf die
alten IDs aus der SQL-Zeit als Eltern.

**Vorgenommene Reparatur:**

| Alter verwaister `parent` | Richtiger neuer `parent` | Knoten        |
|---------------------|----------------------|---------------|
| 2                   | 431                  | Users (eznode:5) |
| 9                   | 457                  | Media (eznode:43) |
| 13                  | 459                  | Setup2 (eznode:48) |
| 25                  | 461                  | Design (eznode:58) |

```php
// Example repair script (applied 2025-01):
$db->ezurlalias_ml->updateMany(['parent' => 2],  ['$set' => ['parent' => 431]]);
$db->ezurlalias_ml->updateMany(['parent' => 9],  ['$set' => ['parent' => 457]]);
$db->ezurlalias_ml->updateMany(['parent' => 13], ['$set' => ['parent' => 459]]);
$db->ezurlalias_ml->updateMany(['parent' => 25], ['$set' => ['parent' => 461]]);
```

**Nach der Reparatur:** Führen Sie `bin/php/ezcache.php --clear-id=urlalias` aus, um den Alias-Cache zu leeren.


### 13.10  `cronjobs/old_drafts_cleanup.php` / `internal_drafts_cleanup.php`

**Zweck:** Löscht Versionen von Inhaltsobjekten, die länger als
`content.ini[VersionManagement] DraftsDuration` bzw.
`content.ini[VersionManagement] InternalDraftsDuration` im Status DRAFT feststecken.

**MongoDB-Status:** ⚠️  Verwendet `eZContentObjectVersion::cleanupOldDrafts()`, das
`fetchObjectList` aufruft. Der MongoDB-Zweig von `fetchObjectList` sollte einfache
Filter verarbeiten. Braucht einen Integrationstest.


### 13.11  `cronjobs/notification.php` — Benachrichtigungsereignisse

**Zweck:** Legt Einträge `eZNotificationEvent` an und führt
`eZNotificationEventFilter::process()` aus, um E-Mail-Benachrichtigungen an Abonnenten zu senden.

**MongoDB-Status:** 🔴 Nicht getestet. `eZNotificationEventFilter` enthält JOIN-Abfragen
auf `eznotification_collection` / `eznotificationevent` — braucht vermutlich MongoDB-Zweige.


### 13.12  `cronjobs/updateviewcount.php` — Aktualisierung der Aufrufzähler

**Zweck:** Liest `updateview.log` (eine Datei, die der View-Kernel schreibt), wertet die
Aufrufereignisse der Knoten aus und erhöht `view_count` in `ezcontentobject_tree`.

**MongoDB-Status:** ✅ Das Auswerten der Protokolldatei ist reines PHP. Die Aktualisierung des Baums ruft
`$node->setAttribute('view_count', …); $node->store()` auf — `store()` läuft über
`eZPersistentObject::store()`, das über den MongoDB-Adapter funktionieren sollte.
Braucht einen Smoke-Test nach den Arbeiten am Suchindex in §13.3.


### 13.13  `bin/php/cleanupversions.php` — Versionen aufräumen

**Zweck:** Entfernt überzählige archivierte Versionen von Inhaltsobjekten und behält nur
`content.ini[VersionManagement] DefaultVersionHistoryLimit` Versionen pro Objekt.

**MongoDB-Status:** ⚠️  Ruft `eZContentObjectVersion::removeVersions()` auf. Dieses
verwendet intern `fetchObjectList` (hat einen MongoDB-Zweig) und `removeThis()`, das
`query('DELETE FROM …')` aufruft — ohne Wirkung. Braucht einen MongoDB-Zweig mit `deleteMany` in
`eZContentObjectVersion::removeThis()`.


### 13.14  `bin/php/adddefaultstates.php` — Standardstatus für Objekte hinzufügen

**Zweck:** Stellt sicher, dass alle Inhaltsobjekte für jede Statusgruppe den Standardstatuseintrag in
`ezcobj_state_link` haben.

**MongoDB-Status:** ⚠️  Einfaches Muster `INSERT … WHERE NOT EXISTS`. Braucht
in MongoDB ein `insertOne` mit einer vorherigen Prüfung über `aggregate`.


### Testmatrix der Cronjobs

| Skript                         | Status | Hindernis                                  |
|--------------------------------|--------|--------------------------------------------|
| `runcronjobs.php`              | ✅ OK  | —                                          |
| `updateniceurls.php`           | ⚠️ Teilweise | Aufrufe von arrayQuery in `storePath()` offen |
| `updatesearchindex.php`        | 🔴 Blockiert | SQL-Abfragen in `eZSearchEngine`      |
| `indexcontent.php`             | 🔴 Blockiert | arrayQuery auf `ezpending_actions`    |
| `staticcache_cleanup.php`      | 🔴 Blockiert | arrayQuery auf `ezpending_actions`    |
| `unlock.php`                   | 🔴 Blockiert | JOIN auf `ezcobj_state_link`          |
| `session_gc.php`               | ⚠️ Test nötig | `eZSession::garbageCollector()`   |
| `trashpurge.php`               | ⚠️ Test nötig | `eZScriptTrashPurge::run()`        |
| `old_drafts_cleanup.php`       | ⚠️ Test nötig | Abdeckung durch `fetchObjectList`   |
| `internal_drafts_cleanup.php`  | ⚠️ Test nötig | Abdeckung durch `fetchObjectList`   |
| `notification.php`             | 🔴 Blockiert | JOIN-Abfragen der Benachrichtigungen  |
| `updateviewcount.php`          | ✅ Vermutlich OK | Smoke-Test offen                |
| `cleanupversions.php`          | ⚠️ Teilweise | DELETE in `removeThis()` ohne Wirkung |
| `adddefaultstates.php`         | ⚠️ Teilweise | INSERT WHERE NOT EXISTS              |



## 14. PHPUnit-Tests — Theorie und Plan für die nächste Phase

> **Status:** Die in den Abschnitten 14.6 und 14.3 beschriebene Testsuite auf Ebene des Adapters ist inzwischen
> umgesetzt. In **[Abschnitt 21](#21-phpunit-testsuite--umgesetzt-mai-2026)** finden Sie die
> konkreten Dateien, die Anleitung zum Ausführen und die Testergebnisse. Die folgende Theorie erklärt die
> Entscheidungen zur Infrastruktur, auf denen die Umsetzung beruht.

Dieser Abschnitt beschreibt die **Theorie und die Voraussetzungen**, um der MongoDB-Portierung eine automatisierte Testabdeckung mit PHPUnit hinzuzufügen.


### 14.1 Aktueller Stand der Testinfrastruktur von Exponential

Die Codebasis von Exponential 6.0.x enthält unter `tests/` eine ältere PHPUnit-Testsuite. Sie wurde für PHPUnit 3.x/4.x geschrieben und zielt ausschließlich auf MySQL. Sie startet über `tests/bootstrap.php`, das `autoload.php` lädt und einen Siteaccess konfiguriert. Alle Datenbankoperationen der Tests laufen gegen eine aktive MySQL- (oder MariaDB-)Verbindung, die in `settings/override/` konfiguriert ist.

Seit Version 6.0.13 verwendet das Projekt **PHPUnit 9.x oder 10.x** (über Composer). Das erfordert:
- PHP 8.0+ (bereits erfüllt — wir verwenden PHP 8.5)
- `phpunit/phpunit ^9.6 || ^10.0` in `composer.json`
- Testannotationen als Attribute (`#[Test]`, `#[DataProvider]`) statt des älteren Docblock-Stils `@test` ab PHPUnit 10
- `setUp()` / `tearDown()` mit korrekten Rückgabetypen (`void`)

Bevor ein Test gegen MongoDB laufen kann, müssen die Bootstrap- und die Konfigurationsschicht unabhängig von der Datenbank werden.


### 14.2 Was sich in der Bootstrap-Schicht ändern muss

#### `tests/bootstrap.php`

Enthält derzeit einen fest eingetragenen MySQL-DSN. Erforderliche Änderungen:

1. **Wahl der Datenbank über die Umgebung.** Eine Umgebungsvariable `TEST_DB_DRIVER` (`mysql` oder `mongo`) lesen. Bei `mongo` die Erweiterung `sevenx_mongodb` laden und die INI-Einstellung `DatabaseDriver` auf `expMongoDB` setzen.

2. **Override des Siteaccess.** Der Test-Bootstrap setzt `siteaccess=plain_site`, das `settings/siteaccess/plain_site/site.ini` liest. Nötig ist ein paralleles `settings/siteaccess/plain_site_mongo/site.ini` (oder ein `settings/override/mongo_test/site.ini`), das `DatabaseDriver=expMongoDB` setzt und `DatabaseName`, `DatabaseUser` und `DatabasePassword` zugunsten der MongoDB-URI weglässt.

3. **Befüllen des Schemas.** Die MySQL-Tests beruhen auf einem SQL-Dump unter `tests/db/`, der vor jeder Suite geladen wird. Die Entsprechung für MongoDB wäre ein Fixture-Lader, der bekannte Dokumente in die Datenbank `exp` (oder eine eigene Datenbank `exp_test`) einspielt. Als Format der Fixtures eignen sich JSON-Dateien pro Collection, die eine Hilfsklasse `MongoDBFixtureLoader` lädt.

4. **Isolation über Transaktionen.** Die MySQL-Tests schließen jeden Test in eine Transaktion ein, die in `tearDown()` zurückgerollt wird; das ergibt eine saubere Isolation mit wenig Aufwand. MongoDB hat in der hier verwendeten Einzelserver-Konfiguration ohne Replica Set keine dokumentübergreifenden Transaktionen. Die entsprechende Strategie für MongoDB lautet: jede vom Test verwendete Collection in `setUp()` **leeren und neu befüllen** oder eine eigene Datenbank `exp_test` verwenden, die pro Testklasse gelöscht und neu angelegt wird.

Beispielauszug aus dem Bootstrap (Theorie):
```php
// tests/bootstrap.php — MongoDB path
if ( getenv('TEST_DB_DRIVER') === 'mongo' ) {
    define('EZP_TEST_DB', 'mongo');
    // Point ini override to MongoDB siteaccess
    putenv('EZP_SITEACCESS=plain_site_mongo');
    // Load Composer autoload (MongoDB driver)
    require __DIR__ . '/../vendor/autoload.php';
    // Seed test fixtures
    MongoDBTestFixtures::seedAll( 'exp_test' );
}
```


### 14.3 Strategien zur Isolation der Testdatenbank

#### Für MariaDB / MySQL

Der bestehende Ansatz funktioniert gut und sollte erhalten bleiben:
- Jede Testklasse ruft in `setUp()` `$this->getDB()->begin()` und in `tearDown()` `rollback()` auf
- Die Testdatenbank heißt `ezpublish_test` — getrennt von der Produktion
- Das Schema wird einmal pro Suite aus `tests/db/mysql_schema.sql` geladen

Damit das unter PHPUnit 9/10 weiter funktioniert, braucht die Basisklasse `eZDatabaseTestCase`:
- den Rückgabetyp `void` bei `setUp()` und `tearDown()`
- den Ersatz aller Docblocks `@expectedException` durch `$this->expectException()`
- `setUpBeforeClass()` / `tearDownAfterClass()` als `static`

#### Für MongoDB

Eine neue Basisklasse `eZMongoDBTestCase` ist erforderlich:
```php
abstract class eZMongoDBTestCase extends \PHPUnit\Framework\TestCase
{
    protected static string $testDatabase = 'exp_test';
    protected expMongoDB $db;

    protected function setUp(): void
    {
        $this->db = eZDB::instance();
        $this->seedFixtures();
    }

    protected function tearDown(): void
    {
        $this->truncateFixtures();
    }

    /**
     * Subclasses declare which collections to truncate and seed.
     * @return array  e.g. ['ezcontentobject', 'ezcontentclass']
     */
    abstract protected function fixtureCollections(): array;

    private function seedFixtures(): void
    {
        $dir = __DIR__ . '/fixtures/';
        foreach ( $this->fixtureCollections() as $col ) {
            $file = $dir . $col . '.json';
            if ( !file_exists($file) ) continue;
            $docs = json_decode( file_get_contents($file), true );
            foreach ( $docs as $doc )
                $this->db->insert( $col, $doc );
        }
    }

    private function truncateFixtures(): void
    {
        foreach ( $this->fixtureCollections() as $col )
            $this->db->deleteWhere( $col, [] );
    }
}
```


### 14.4 Format der Fixtures

Die Test-Fixtures sollten unter `tests/fixtures/mongodb/` liegen, eine JSON-Datei pro Collection:

```
tests/fixtures/mongodb/
  ezcontentclass.json
  ezcontentclass_attribute.json
  ezcontentclass_classgroup.json
  ezcontentclassgroup.json
  ezcontentobject.json
  ezcontentobject_attribute.json
  ezcontentobject_name.json
  ezcontentobject_tree.json
  ezcontentobject_version.json
  ezcontentobject_link.json
  ezcobj_state.json
  ezcobj_state_group.json
  ezcobj_state_link.json
  ezsection.json
  ezurlalias_ml.json
  ezinfocollection.json
  ezsearch_search_phrase.json
  ...
```

Jede JSON-Datei enthält ein Array von Dokumenten in der Form der MongoDB-Dokumente (Ganzzahlfelder als Ganzzahlen, nicht als Zeichenketten). Ein minimales Fixture `ezcontentclass.json` könnte so aussehen:
```json
[
  {
    "id": 1,
    "version": 0,
    "identifier": "folder",
    "serialized_name_list": "a:2:{s:6:\"eng-US\";s:6:\"Folder\";s:16:\"always-available\";s:6:\"eng-US\";}",
    "is_container": 1,
    "modifier_id": 14,
    "modified": 1700000000
  }
]
```


### 14.5 Bestehende Tests, die geändert werden müssen

Die folgenden bestehenden Testdateien müssen geändert werden, damit sie Tests gegen zwei Datenbanken unterstützen:

| Testdatei | Erforderliche Änderungen |
|---|---|
| `tests/kernel/ezcontentclass_test.php` | Eine Variante `@group mongo` ergänzen; SQL-Prüfungen durch Prüfungen von MongoDB-Aggregationen ersetzen oder mocken |
| `tests/kernel/ezcontentobject_test.php` | Ebenso — jeder Aufruf von `assertQuery()` braucht einen MongoDB-Pfad |
| `tests/kernel/ezcontentobjecttreenode_test.php` | `subTreeByNodeID` + `subTreeCountByNodeID` gegen einen Fixture-Baum testen |
| `tests/kernel/ezpersistentobject_test.php` | Tests der Typumwandlung in `fetchObjectList` — das Verhalten der Bedingung mit der Zeichenkette `"49"` gegenüber der Ganzzahl `49` prüfen |
| `tests/kernel/ezurlaliasml_test.php` | `translate()` auf die Auflösung von Pfaden mit mehreren Segmenten testen |
| `tests/kernel/ezurlaliasquery_test.php` | **Neu** — muss angelegt werden; testet `buildMongoMatch()`, `count()`, `fetchAll()` |
| `tests/kernel/ezsection_test.php` | MongoDB-Pfad von `sectionCount()` |
| `tests/kernel/ezinformationcollection_test.php` | MongoDB-Pfad von `fetchCollectionCountForObject()` |
| `tests/kernel/ezsearchlog_test.php` | Form des Ergebnisses der Pipeline von `mostFrequentPhraseArray()` |

Für jede Testklasse empfiehlt sich ein **parametrisierter Test** über zwei Datenbank-Provider:
```php
#[DataProvider('dbProvider')]
public function testFetchReturnsCorrectCount( string $driver ): void
{
    $this->setUpDatabase( $driver );   // swaps eZDB singleton
    $count = eZSection::sectionCount();
    $this->assertGreaterThan( 0, $count );
}

public static function dbProvider(): array
{
    return [
        'mysql'  => ['mysql'],
        'mongodb' => ['mongo'],
    ];
}
```

Die Hilfsmethode `setUpDatabase()` der Basisklasse würde das Singleton `eZDB` mit dem passenden Treiber neu instanziieren, die benötigten Fixtures einspielen und ein Aufräumen beim Teardown registrieren.


### 14.6 Neue Tests für das MongoDB-spezifische Verhalten

Über das Portieren der bestehenden Tests hinaus decken mehrere neue Testfälle Verhalten ab, das es nur unter MongoDB gibt:

#### `tests/mongodb/SevenxMongoDBAdapterTest.php`
Testet den Adapter selbst:
- `testAggregateReturnsExpectedShape()` — ein einfaches `$count` auf eine befüllte Collection
- `testInsertAndFetchRoundtrip()` — ein Dokument einfügen und über `aggregate` wieder lesen
- `testUpsertUpdatesExistingDocument()` — upsert mit einem vorhandenen Filter ersetzt die Felder
- `testDeleteWhereRemovesDocuments()` — das Löschen bestätigen
- `testNextAtomicIDIsMonotonic()` — `nextAtomicID` zehnmal aufrufen und bestätigen, dass die Werte aufeinander folgen
- `testListCollectionNamesReturnsList()` — bestätigen, dass bekannte Collections vorhanden sind
- `testDatabaseNameReturnsMongo()` — `$db->databaseName() === 'mongo'`

#### `tests/mongodb/EZPersistentObjectMongoTest.php`
- `testFetchObjectListCastsStringCondToInt()` — `['id' => '1']` übergeben; bestätigen, dass eine Zeile zurückkommt
- `testFetchObjectListWithLimit()` — bestätigen, dass die Stufe `$limit` angewendet wird (gibt ≤ N Zeilen zurück)
- `testFetchObjectListWithSort()` — bestätigen, dass die Stufe `$sort` angewendet wird
- `testStoreObjectInsertsNewDocument()` — ein Objekt ohne vorhandene ID speichern
- `testStoreObjectUpdatesExisting()` — das Speichern mit vorhandener ID aktualisiert das Dokument
- `testRemoveObjectDeletesDocument()` — das Löschen bestätigen
- `testHandleRowsReferencesBugFixed()` — 3 Zeilen einspielen; bestätigen, dass die letzte Zeile keine Kopie der vorletzten ist

#### `tests/mongodb/EZContentClassMongoTest.php`
- `testFetchReturnsObjectWithCorrectID()`
- `testCanInstantiateClassListRawArrayHasNameKey()` — mit `$asObject=false` aufrufen; bestätigen, dass `$row['name']` aus `serialized_name_list` gefüllt ist
- `testCanInstantiateClassListObjectModeSkipsNameInjection()` — `$asObject=true`; keine Prüfung des Schlüssels `name`
- `testStoreVersionedMigratesGroupsCorrectly()`

#### `tests/mongodb/EZContentObjectMongoTest.php`
- `testFetchByIDCastsStringToInt()`
- `testAssignedNodesReturnsNodesForObject()`
- `testHasVisibleNodeReturnsTrueWhenNodeExists()`
- `testRelatedObjectsReturnsLinkedObjects()`
- `testStateIDArrayReturnsStateIDs()`

#### `tests/mongodb/EZURLAliasMLMongoTest.php`
- `testTranslateSingleSegmentPath()`
- `testTranslateMultiSegmentPath()`
- `testTranslateReturnsFalseForUnknownPath()`
- `testFetchPathByActionListReturnsRows()`

#### `tests/mongodb/EZURLAliasQueryMongoTest.php`
- `testBuildMongoMatchWithParentFilter()`
- `testBuildMongoMatchWithTextFilter()`
- `testBuildMongoMatchWithTypeAlias()`
- `testBuildMongoMatchWithActionTypesExReturnsEmptyWhenAllExcluded()`
- `testCountReturnsCorrectInteger()`
- `testFetchAllReturnsPathElements()`
- `testFetchAllRespectsSortOrder()`


### 14.7 PHPUnit-Konfiguration für Läufe gegen zwei Datenbanken

Eine `phpunit.xml`, die in zwei Suites aufgeteilt ist:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         colors="true">

  <testsuites>
    <testsuite name="MySQL (MariaDB)">
      <directory>tests/kernel</directory>
      <directory>tests/ezdb_mysql</directory>
    </testsuite>
    <testsuite name="MongoDB">
      <directory>tests/mongodb</directory>
    </testsuite>
  </testsuites>

  <php>
    <!-- Override at runtime: TEST_DB_DRIVER=mongo ./vendor/bin/phpunit --testsuite MongoDB -->
    <env name="TEST_DB_DRIVER" value="mysql"/>
    <env name="TEST_MONGO_URI" value="mongodb://db:YOUR_PASSWORD@localhost:27017/exp_test"/>
    <env name="TEST_MYSQL_DSN" value="mysql:host=localhost;dbname=ezpublish_test"/>
  </php>

  <coverage>
    <include>
      <directory suffix=".php">extension/sevenx_mongodb/classes</directory>
      <directory suffix=".php">kernel</directory>
    </include>
    <report>
      <html outputDirectory="tests/coverage"/>
    </report>
  </coverage>
</phpunit>
```

Die MongoDB-Suite ausführen:
```bash
TEST_DB_DRIVER=mongo /opt/plesk/php/8.5/bin/php vendor/bin/phpunit --testsuite MongoDB
```

Die MySQL-Suite ausführen:
```bash
TEST_DB_DRIVER=mysql /opt/plesk/php/8.5/bin/php vendor/bin/phpunit --testsuite "MySQL (MariaDB)"
```


### 14.8 Mocking-Strategie für Kernel-Klassen ohne Override

Einige Kerndateien des Kernels (z. B. `kernel/search/stats.php`, `kernel/infocollector/overview.php`) sind prozedurale Skripte, die `eZDB::instance()` direkt aufrufen. Sie lassen sich nicht als Unit-Tests prüfen, ohne das vollständige CMS zu starten. Zwei Möglichkeiten:

**Möglichkeit A — Integrationstests (derzeit bevorzugt).** Das vollständige CMS mit `TEST_DB_DRIVER=mongo` starten, die Modul-Endpunkte über einen HTTP-Testclient aufrufen (z. B. Symfony BrowserKit oder Guzzle) und den Seiteninhalt prüfen. Diese Tests liegen unter `tests/integration/`.

**Möglichkeit B — teilweises Herauslösen.** Die Datenbanklogik der prozeduralen Skripte in statische Methoden einer begleitenden Klasse herauslösen (z. B. `eZSearchStatsHelper::getCount()`) und die Hilfsklasse dann mit einem gemockten `eZDB` als Unit-Test prüfen. Das ist langfristig der sauberere Weg, erfordert aber ein Refactoring der Kerndateien.

Für die unmittelbar nächste Phase wird Möglichkeit A empfohlen: Sie fängt echte Regressionen ab, erfordert kein Refactoring und prüft den vollständigen Zyklus von Anfrage und Antwort einschließlich der Template-Darstellung.


### 14.9 Composer-Anforderungen für die Unterstützung von PHPUnit 9/10 (6.0.13)

In `composer.json` ergänzen:
```json
"require-dev": {
    "phpunit/phpunit": "^10.5",
    "mongodb/mongodb": "^1.18",
    "symfony/browser-kit": "^6.4",
    "symfony/http-client": "^6.4"
}
```

Hinweis: `mongodb/mongodb` ist bereits unter `require` eingetragen (nicht unter `require-dev`). Die Pakete BrowserKit und HttpClient werden nur für Integrationstests gebraucht.

Mit der vorhandenen `rector.php` (im Wurzelverzeichnis des Arbeitsbereichs) lassen sich alte Docblock-Annotationen `@test` automatisch auf Attribute von PHPUnit 10 umstellen:
```bash
/opt/plesk/php/8.5/bin/php vendor/bin/rector process tests/kernel \
  --config rector.php --dry-run
```


## 15. Kernel-PHP-Klassen — Status und bekannte Probleme

Dieser Abschnitt bietet eine **umfassende Bestandsaufnahme pro Klasse** für jede PHP-Klasse des Kernels, die bei der MongoDB-Portierung angefasst, teilweise angefasst oder als überarbeitungsbedürftig erkannt wurde. Ziel ist ein klares, eindeutiges Bild davon, was funktioniert, was defekt ist, wofür eine bekannte Korrektur ansteht und was noch gar nicht untersucht wurde.

Legende des Status:
- ✅ **Funktioniert** — MongoDB-Zweig umgesetzt und im Browser von Hand geprüft
- ⚠️ **Teilweise** — ein MongoDB-Zweig existiert, hat aber Lücken; die Seite lädt, aber einige Daten fehlen oder es erscheinen Warnungen
- 🔴 **Defekt** — kein MongoDB-Zweig; die Seite stürzt ab oder liefert leere Daten; MONGO TODO in der Debug-Ausgabe
- 🔵 **Nicht getestet** — kein geprüfter Test; kann funktionieren oder defekt sein; noch nicht ausprobiert
- 📋 **Eingeplant** — die Korrektur ist geplant und ihr Entwurf bekannt (siehe Backlog der offenen Probleme §11)


### 15.1 Zentrale ORM- und Datenbankschicht

#### `extension/sevenx_mongodb/classes/expMongoDB.php` — der MongoDB-Adapter

**Status: ✅ Funktioniert (alle Hauptmethoden)**

Das ist die zentrale Adapterklasse. Alle wichtigen Lese- und Schreibmethoden sind umgesetzt und geprüft. Jede andere Korrektur in diesem Projekt setzt voraus, dass sich diese Klasse richtig verhält.

| Methode | Status | Hinweise |
|---|---|---|
| `databaseName()` | ✅ | Gibt `'mongo'` zurück — der allgemeine Schalter für die Verzweigung |
| `aggregate($table, $pipeline)` | ✅ | Funktioniert vollständig; wird von allen MongoDB-Zweigen verwendet |
| `insert($table, $doc)` | ✅ | Einfügen eines einzelnen Dokuments |
| `upsert($table, $filter, $doc)` | ✅ | Ersetzen oder Einfügen |
| `deleteWhere($table, $filter)` | ✅ | Massenlöschung nach Filter |
| `mongoUpdateOne($table, $filter, $update)` | ✅ | Teilweise Aktualisierung (`$set` usw.) |
| `nextAtomicID($counterName, ...)` | ✅ | Atomarer Zähler über die Collection `ezsequence` |
| `nextSeqID($table, $column)` | ✅ | `MAX(column)+1` über `$group/$max` |
| `findOne($table, $condition)` | ⚠️ | Funktioniert für einfache skalare Bedingungen; bei `$or`/`$in` vermeiden |
| `find($table, $conds, $projection)` | ⚠️ | Verwendet `translateConditions()` — nur für skalare Werte sicher |
| `arrayQuery($sql)` | ✅ (Stub) | Gibt immer `[]` zurück; protokolliert MONGO TODO mit dem Aufrufer |
| `query($sql)` | ✅ (Stub) | Gibt immer `false` zurück; ohne Wirkung |
| `translateConditions($conds)` | ⚠️ | Nur skalare Werte und `like` — unterstützt KEINE verschachtelten Arrays |
| `listCollectionNames()` | ✅ | Gibt über den Treiber ein Array der Namen der MongoDB-Collections zurück; wird von `systemupgrade.php` verwendet |
| `eZTableList()` | ✅ | Gibt `[]` zurück, um den Absturz `array_keys(null)` zu verhindern |
| `escapeString($str)` | ✅ | Gibt die Zeichenkette unverändert zurück (kein SQL-Escaping nötig) |
| `begin()` / `commit()` / `rollback()` | ✅ (ohne Wirkung) | MongoDB-Operationen wirken pro Dokument; keine Transaktion nötig |
| `lock()` / `unlock()` | ✅ (ohne Wirkung) | Tabellensperren sind nicht anwendbar |

**Bekannte Einschränkung:** `translateConditions()` verwirft verschachtelte Array-Werte stillschweigend. Jeder Aufrufer, der `['field' => ['$in' => [...]]]` über `find()` oder `findOne()` übergibt, erhält einen fehlerhaften Filter. Alle solchen Aufrufer müssen `aggregate()` direkt verwenden.


#### `extension/sevenx_mongodb/classes/kernel/ezpersistentobject.php` — die ORM-Basisklasse

**Status: ✅ Funktioniert** (mit den unten beschriebenen bekannten Sonderfällen)

Jedes persistente Objekt im CMS ruft letztlich `fetchObjectList()`, `storeObject()`, `removeObject()` oder `newObjectOrder()` auf. Alle vier haben vollständige MongoDB-Zweige.

**Sonderfälle von `fetchObjectList()`:**
- Die Werte von `$conds` werden anhand der Definition `datatype` des Felds in `int`/`float` umgewandelt, bevor `$mongoFilter` aufgebaut wird. Das behebt den grundlegenden Fehler, dass URL-Parameter als Zeichenketten ankommen, MongoDB aber Ganzzahlen speichert.
- Array-Werte in `$conds` (also bereits aufgebaute Arrays mit MongoDB-Operatoren) werden unverändert durchgereicht — so können Aufrufer Bedingungen wie `['$in' => [...]]` vorab aufbauen.
- `$sorts` akzeptiert dasselbe Format `['field' => true/false]` wie der SQL-Pfad.
- `$limit` akzeptiert sowohl die Schlüssel `['limit' => N, 'offset' => M]` als auch `['limit' => N, 'length' => M]`.
- `custom_fields` mit `'operation' => 'count'` wird behandelt, indem eine Stufe `$count` statt `$project` erzeugt wird.
- Der Referenzfehler in `handleRows()` (das letzte Element wurde dupliziert) wurde mit `unset($row)` nach der Schleife behoben.

**Bekannte offene Probleme:**
- Der Parameter `$grouping` (entspricht GROUP BY) hat keinen MongoDB-Zweig — fällt stillschweigend auf den SQL-Pfad durch.
- Ausdrücke in `$custom_fields` außer `count` (z. B. `SUM`, `AVG`) haben keinen MongoDB-Zweig.
- `$field_filters` (Spaltenauswahl) ordnet möglicherweise nicht alle SQL-Aliase richtig den Schlüsseln von `$project` in MongoDB zu.


### 15.2 Inhaltsklassen

#### `extension/sevenx_mongodb/classes/kernel/ezcontentclass.php`

**Status: ✅ Funktioniert** (grundlegendes CRUD + Kopieren + Versionierung)

| Methode / Funktion | Status | Hinweise |
|---|---|---|
| `fetch($id)` | ✅ | int-Umwandlung ergänzt; läuft über `fetchObjectList` |
| `__clone()` | ✅ | `$this->ID = null` erzwingt beim Speichern ein Einfügen |
| `storeVersioned($attrs, $version)` | ✅ | Vollständige Logik für die Migration der Gruppen; Behandlung der Versionen v0/v1 |
| `canInstantiateClassList()` — Objektmodus | ✅ | Gibt über `handleRows()` Objekte vom Typ `eZContentClass` zurück |
| `canInstantiateClassList()` — Modus mit rohen Arrays | ✅ | Der Schlüssel `name` wird über `unserialize()` aus `serialized_name_list` gefüllt |
| `initializeCopy()` | 📋 | JOIN über `arrayQuery` auf `[ezcontentclass, ezcontentclass_name]` — das Eindeutigmachen des Kopienamens schlägt fehl; PHP-Warnung; nicht fatal |
| `remove($removeAttributes)` | 🔵 | Nicht ausdrücklich geprüft; hängt von `removeObject()` ab, das funktioniert |
| `versionCount()` | 🔵 | Verwendet `fetchObjectList` — funktioniert vermutlich; nicht getestet |
| `storeName()` / Behandlung von `NameList` | ✅ | Teil von `storeVersioned()` — geprüft |

**Hinweis zur Ursache — fehlendes Feld `name`:** Die MongoDB-Collection `ezcontentclass` enthält überhaupt kein Feld `name`. Das ursprüngliche SQL-Schema speicherte die Klassennamen in einer Spalte `name`; bei der Migration nach MongoDB wurde nur `serialized_name_list` gespeichert. Jeder Code, der `$row['name']` direkt aus einem rohen Ergebnis von `fetchObjectList` liest, erhält `Undefined array key "name"`. Die Korrektur in `canInstantiateClassList()` bearbeitet die rohen Zeilen nach und fügt den Schlüssel `name` ein. Andere Aufrufer, die direkt auf `$row['name']` in rohen Klassen-Arrays zugreifen, brauchen möglicherweise dieselbe Korrektur.


#### `extension/sevenx_mongodb/classes/kernel/ezcontentclassattribute.php`

**Status: ✅ Funktioniert**

| Methode | Status | Hinweise |
|---|---|---|
| `classAttributeIdentifiersHash()` | ✅ | Den JOIN über 2 Tabellen durch zwei aufeinanderfolgende Aggregationen + Zusammenführen in PHP ersetzt |
| `fetch($id, $version)` | ✅ | Läuft über `fetchObjectList` |
| `fetchListByClassID($classID, $version)` | 🔵 | Nicht ausdrücklich getestet; sollte über `fetchObjectList` funktionieren |


#### `extension/sevenx_mongodb/classes/kernel/ezcontentclassgroup.php`

**Status: ✅ Funktioniert**

| Methode | Status | Hinweise |
|---|---|---|
| `fetch($id)` | ✅ | int-Umwandlung ergänzt |
| `fetchList()` | ✅ | Über `fetchObjectList` |


#### `extension/sevenx_mongodb/classes/kernel/ezcontentclassclassgroup.php`

**Status: ✅ Funktioniert**

| Methode | Status | Hinweise |
|---|---|---|
| `fetchClassList($version, $groupId)` | ✅ | Zweistufige Aggregation: classgroup → Klassen-IDs → Laden der Klassen |
| `removeClassMembers()` | 🔵 | Verwendet `removeObject()` — funktioniert vermutlich; nicht einzeln getestet |


### 15.3 Inhaltsobjekte

#### `extension/sevenx_mongodb/classes/kernel/ezcontentobject.php`

**Status: ⚠️ Teilweise — viele Methoden funktionieren, einige erhebliche Lücken bestehen noch**

Das ist die größte und komplexeste Klasse des Kernels. Über 30 Methoden wurden untersucht; etwa die Hälfte hat MongoDB-Zweige.

| Methode | Status | Hinweise |
|---|---|---|
| `fetch($id)` | ✅ | int-Umwandlung; `fetchObjectList` |
| `fetchIDArray($idArray)` | ✅ | Massen-Aggregation mit Joins auf Klasse und Namen |
| `fetchByNodeID($nodeID)` | ✅ | Aggregation über 3 Collections |
| `versionLanguageName()` | ✅ | `$or` auf die Sprachmaske über `aggregate()` |
| `contentObjectAttributes()` | ✅ | Vierstufige Pipeline mit Join auf `ezcontentclass_attribute` |
| `fillNodeListAttributes()` | ✅ | Lädt die Attribute für eine Knotenliste gebündelt in einer einzigen Abfrage |
| `assignedNodes()` | ✅ | Aggregation über 3 Collections |
| `hasVisibleNode()` | ✅ | Zählende Aggregation; beseitigt 27 MONGO TODOs auf der Seite der Klassenliste |
| `stateIDArray()` | ✅ | `ezcobj_state_link` → `ezcobj_state` |
| `stateIdentifierArray()` | ✅ | Kette über 3 Collections |
| `relatedObjects()` | ✅ | Vollständige Pipeline mit `$lookup` |
| `previousVersion()` | ✅ | MongoDB-Zweig mit Aggregation umgesetzt |
| `removeContentObjectRelation()` | ✅ | MongoDB-Zweig mit `deleteWhere` umgesetzt |
| `copyContentObjectRelations()` | ✅ | MongoDB-Zweig mit Aggregation + Einfügen umgesetzt |
| `relatedObjectCount()` | 📋 | Kein MongoDB-Zweig; gibt 0 zurück; betrifft die Seiten edit/history |
| `getStates()` | 📋 | SQL-JOIN `ezcobj_state + ezcobj_state_group WHERE NOT LIKE 'ez%'`; eigene Status sind defekt |
| `addContentObjectRelation()` | 📋 | `to_contentobject_id` wird nicht in int umgewandelt; das Einfügen von Relationen kann fehlschlagen |
| `mainNode()` | 🔵 | Verwendet `findMainNode()` des Baumknotens — funktioniert vermutlich |
| `contentClass()` | 🔵 | Über `eZContentClass::fetch()` — funktioniert |
| `currentVersion()` | 🔵 | Über `fetchObjectList` — funktioniert vermutlich |
| `versions()` | 🔵 | Über `fetchObjectList` — funktioniert vermutlich |
| `name()` | 🔵 | Verwendet das Nachschlagen in `ezcontentobject_name` — nicht ausdrücklich getestet |
| `removeThis()` | 🔵 | Endgültiges Löschen des ganzen Objekts; hängt von `removeObject()` + dem Aufräumen der Versionen ab |

**Zusammenfassung:** Die Seiten view/edit/history der Objekte funktionieren. Verwandte Objekte werden teilweise angezeigt (die Liste funktioniert, die Anzahl zeigt 0). Die Anzeige eigener Status ist defekt. Die Liste der URL-Aliase und die Brotkrümelnavigation funktionieren, weil `assignedNodes()` und `hasVisibleNode()` korrigiert sind.


#### `extension/sevenx_mongodb/classes/kernel/ezcontentobjectversion.php`

**Status: ✅ Funktioniert** (grundlegender Lebenszyklus der Versionen)

| Methode | Status | Hinweise |
|---|---|---|
| `fetch($id)` | ✅ | int-Umwandlung |
| `removeThis()` | ✅ | Vollständiger MongoDB-Zweig; löscht in 5 Collections; aktualisiert das übergeordnete Objekt |
| `fetchList()` | 🔵 | Über `fetchObjectList` — funktioniert vermutlich |


#### `extension/sevenx_mongodb/classes/kernel/ezcontentobjecttreenode.php`

**Status: ⚠️ Teilweise — die grundlegende Navigation funktioniert; einige Hilfsmethoden sind nicht getestet**

| Methode | Status | Hinweise |
|---|---|---|
| `fetch($id)` | ✅ | `fetchMongo()` — vollständige Aggregation über 4 Collections |
| `fetchNodesByPathString()` | ✅ | Präfixvergleich mit `$regex` |
| `findMainNode($objectID)` | ✅ | `$expr: {$eq: ['$node_id','$main_node_id']}` |
| `findNode()` | ✅ | MongoDB-Zweig umgesetzt |
| `subTreeCountByNodeID()` | ✅ | Vollständiger MongoDB-Pfad mit Regex + Filter auf die Tiefe |
| `subTreeByNodeID()` | ✅ | Vollständiger MongoDB-Pfad mit `$lookup`-Joins; Seitenaufteilung |
| `makeObjectsArray()` | ✅ | Korrektur für implizit nullbare Parameter unter PHP 8.5 |
| `getClassesJsArray()` | ✅ | Durch die Korrektur des Schlüssels `name` in `canInstantiateClassList()` behoben |
| `move()` | 🔵 | Komplexe Logik zum Umschreiben von `path_string`; nicht getestet |
| `removeNode()` | 🔵 | Aufräumen über mehrere Collections; nicht getestet |
| `updatePathString()` | 🔵 | Ruft vermutlich `query()` auf (ohne Wirkung); braucht einen MongoDB-Zweig |
| `childrenByNodeID()` | 🔵 | Über `fetchObjectList`; funktioniert vermutlich |
| `childCount()` | 🔵 | Verwendet vermutlich `aggregate`; nicht bestätigt |


### 15.4 URLs und Navigation

#### `extension/sevenx_mongodb/classes/kernel/ezurlaliasml.php`

**Status: ✅ Funktioniert** (Übersetzung und Nachschlagen von Pfaden)

| Methode | Status | Hinweise |
|---|---|---|
| `translate(&$uri, $reverse)` | ✅ | Vollständig neu geschriebenes Nachschlagen Segment für Segment entlang der Kette |
| `fetchPathByActionList()` | ✅ | `$in`-Match auf die Action-Zeichenketten |
| `storePath()` | 🔴 | Viele Aufrufe von `arrayQuery`/`query`; `updateniceurls.php` legt nur Einträge auf oberster Ebene an; untergeordnete Knoten werden nicht aktualisiert |

**`storePath()` ist das größte verbleibende Hindernis für den Neuaufbau der URL-Aliase.** Das Skript `bin/php/updateniceurls.php` läuft ohne Absturz durch, meldet aber `Updated 0/219`, weil `storePath()` keine untergeordneten Alias-Einträge schreiben kann. Das ist eine erhebliche bekannte Lücke.


#### `extension/sevenx_mongodb/classes/kernel/ezurlaliasquery.php`

**Status: ✅ Funktioniert** (URL-Liste + Anzahl)

| Methode | Status | Hinweise |
|---|---|---|
| `buildMongoMatch()` | ✅ | Neue Methode; übersetzt alle Filtereigenschaften in `$match` |
| `count()` | ✅ | MongoDB-Zweig mit `$count`-Aggregation |
| `fetchAll()` | ✅ | MongoDB-Zweig mit Aggregation über `$sort`/`$skip`/`$limit` |
| Zeile 256 — DISTINCT action_type | ✅ | Eine Aggregation mit `$group` ersetzt das SQL-DISTINCT; die Spalte wird mit `array_column($rows, '_id')` ausgelesen |


#### `extension/sevenx_mongodb/classes/kernel/datatypes/ezurl/ezurl.php`

**Status: ✅ Funktioniert** (Seite der Linkverwaltung)

| Methode | Status | Hinweise |
|---|---|---|
| `fetchByAttribute()` — Zählvariante | ✅ | Vierstufige Kette mit `$lookup` + `$count` |
| `fetchByAttribute()` — Listenvariante | ✅ | Vierstufige Kette mit `$lookup` + `$project`/`$skip`/`$limit` |


### 15.5 Rollen und Berechtigungen

#### `extension/sevenx_mongodb/classes/kernel/ezrole.php`

**Status: ✅ Funktioniert** — bestätigt, dass alle Aufrufe von `arrayQuery` in SQL-`else`-Zweigen stehen; die MongoDB-Pfade sind bereits gepatcht.

| Methode | Status | Hinweise |
|---|---|---|
| `fetchByUser()` | ✅ | Pipeline mit `$lookup` auf `ezrole` + `ezuser_role`; sowohl im direkten als auch im rekursiven Modus (Gruppenpfad) |
| `fetchRolesForUser()` | ✅ | Bestätigt: ruft intern `fetchByUser()` auf |
| `policyList()` | ✅ | Verwendet `fetchObjectList` (ORM) — funktioniert von sich aus |
| `accessArrayByUserID()` | ✅ | Delegiert an `fetchByUser()` + `accessArray()` — beide gepatcht |
| `fetchIDListByUser()` | ✅ | Aggregation mit `$lookup` |
| `assignToUser()` | ✅ | Prüfung über `aggregate` + `insert` |
| `fetchUserID()` | ✅ | Einfache `aggregate` |
| `removeUserAssignment()` | ✅ | `deleteWhere` |
| `fetchUserByRole()` / `fetchRolesByLimitation()` | ✅ | `aggregate` + Umbenennung `contentobject_id` → `user_id` |
| Zeilen 891, 921, 1005, 1048 | ✅ | Alle in SQL-`else`-Zweigen — für MongoDB nicht erreichbar |


### 15.6 Sektionen

#### `extension/sevenx_mongodb/classes/kernel/ezsection.php`

**Status: ✅ Funktioniert**

| Methode | Status | Hinweise |
|---|---|---|
| `sectionCount()` | ✅ | MongoDB-Zweig mit `$count`-Aggregation |
| `fetchList()` | ✅ | Über `fetchObjectList` |
| `fetch($id)` | 🔵 | Über `fetchObjectList`; funktioniert vermutlich |
| `fetchFilteredList()` | 🔵 | Über `fetchObjectList`; funktioniert vermutlich |


### 15.7 Suche

#### `extension/sevenx_mongodb/classes/kernel/ezsearchlog.php`

**Status: ✅ Funktioniert**

| Methode | Status | Hinweise |
|---|---|---|
| `mostFrequentPhraseArray()` | ✅ | Vollständige MongoDB-Pipeline mit `$divide` für das berechnete `result_count` |
| `addPhrase()` | 🔵 | Verwendet `upsert`/`insert` — funktioniert vermutlich, aber nicht getestet |

#### `kernel/search/stats.php` (Kerndatei, kein Override)

**Status: ✅ Funktioniert** — MongoDB-Zweig mit `$count`-Aggregation ergänzt.


### 15.8 Information Collector

#### `extension/sevenx_mongodb/classes/kernel/ezinformationcollection.php`

**Status: ⚠️ Teilweise**

| Methode | Status | Hinweise |
|---|---|---|
| `fetchCollectionCountForObject()` | ✅ | MongoDB-Zweig mit `$count`-Aggregation |
| `fetchCollectionsList()` | ✅ | Über `fetchObjectList` — bestätigt funktionsfähig (Abfragen 3+4 auf der Seite des Information Collectors) |
| `fetchList()` | 🔴 | Zeile 422: JOIN über `arrayQuery` auf `[ezinfocollection, ezcontentobject]` |
| `fetchListCount()` | 🔴 | Zeile 437: `arrayQuery` — gerade behoben, aber nur für `fetchCollectionCountForObject`; prüfen, ob dieselbe Zeile gemeinsam genutzt wird |
| `fetchGroupedList()` | 🔴 | Zeile 577: komplexer JOIN |
| `fetchGroupedListCount()` | 🔴 | Zeile 601: zählender JOIN |

#### `kernel/infocollector/overview.php` (Kerndatei, kein Override)

**Status: ✅ Funktioniert** — vollständige Pipeline mit `$lookup` für beide Abfragen.


### 15.9 Sprachen

#### `extension/sevenx_mongodb/classes/kernel/ezcontentlanguage.php`

**Status: ✅ Funktioniert**

| Methode | Status | Hinweise |
|---|---|---|
| `objectCount($languageID)` | ✅ | `$bitAnd` in `$expr` für die Prüfung der Bitmaske |
| `classCount($languageID)` | ✅ | Dieselbe Korrektur |
| `fetchList()` | 🔵 | Über `fetchObjectList` — funktioniert vermutlich |
| `fetchByLocale($locale)` | 🔵 | Über `fetchObjectList` — funktioniert vermutlich |
| `maxCount()` | 🔵 | Wird von makeList() in `ezurlaliasquery.php` verwendet; funktioniert vermutlich |


### 15.10 Schema und Setup

#### `lib/ezdbschema/classes/ezdbschema.php` (Kernbibliothek, kein Override)

**Status: ✅ Funktioniert**

| Korrektur | Status | Hinweise |
|---|---|---|
| E_DEPRECATED bei der Umwandlung von `false` in ein Array (Zeile 38) | ✅ | Behoben: Prüfung `!is_array($params)` ergänzt |
| `writeError()` bei fehlendem MongoDB-Handler | ✅ | Unterdrückt, wenn `$dbname === 'mongo'` |

#### `kernel/setup/systemupgrade.php` (Kerndatei, kein Override)

**Status: ✅ Funktioniert**

| Korrektur | Status | Hinweise |
|---|---|---|
| Absturz mit Null-Objekt vor `transformSchema()` | ✅ | Prüfung `is_object()` ergänzt |
| Sofortiges „ok“ ohne echte Prüfung | ✅ | Echter Vergleich von Collections und Schema mit `listCollectionNames()` |


### 15.11 Datentypen

#### `extension/sevenx_mongodb/classes/kernel/datatypes/ezxmltext/ezxmloutputhandler.php`

**Status: ✅ Funktioniert** — die Deprecation für ein null-Argument unter PHP 8.5 ist behoben (Zeile 318).

#### `extension/sevenx_mongodb/classes/kernel/datatypes/ezxmltext/ezxmlschema.php`

**Status: ✅ Funktioniert** — dieselbe null-Prüfung (Zeile 369).

#### `extension/sevenx_mongodb/classes/kernel/datatypes/ezinisetting/ezinisettingtype.php`

**Status: ✅ Funktioniert** — die Prüfung `hasPostVariable` für das leere Absenden eines `<select multiple>` entfernt; das Speichern der Klasse liefert nicht mehr `STATE_INVALID`.

#### `extension/sevenx_mongodb/classes/kernel/datatypes/ezurl/ezurl.php`

**Status: ✅ Funktioniert** — Pipeline mit `$lookup` über 4 Tabellen ergänzt; die Seite der Linkverwaltung wird richtig angezeigt.

#### `extension/sevenx_mongodb/classes/kernel/datatypes/ezkeyword/ezkeywordtype.php`

**Status: ✅ Funktioniert** — alle Aufrufe von `arrayQuery` durch MongoDB-Aggregationen ersetzt; `deleteStoredObjectAttribute()` vollständig gepatcht.

| Methode | Status | Hinweise |
|---|---|
|---|
| `deleteStoredObjectAttribute()` | ✅ | Zweistufige Aggregation: die keyword_ids finden, die Verknüpfungen gruppiert zählen, um ungenutzte zu finden, danach `deleteWhere` auf `ezkeyword` und `ezkeyword_attribute_link` |

#### `extension/sevenx_mongodb/classes/kernel/datatypes/ezxmltext/ezxmltexttype.php`

**Status: ✅ Funktioniert** — das Aufräumen verwaister URLs ist vollständig gepatcht.

| Methode | Status | Hinweise |
|---|---|---|
| Aufräumen verwaister URLs in `deleteStoredObjectAttribute()` | ✅ | Eine Anti-Join-Aggregation mit `$nin` findet `ezurl`-IDs, die nicht in `ezurl_object_link` stehen; `deleteWhere` entfernt die verwaisten Einträge |


### 15.12 Siteaccess und Sonstiges

#### `extension/sevenx_mongodb/classes/kernel/ezsiteaccess.php`

**Status: ✅ Funktioniert** — Korrekturen für implizit nullbare Parameter unter PHP 8.5 in `change()` und `load()`.

#### `extension/xrowmetadata/autoloads/xrowmetadataoperator.php`

**Status: ✅ Funktioniert** — Prüfung auf einen übergeordneten Knoten, der null ist, ergänzt.

#### `extension/sevenx_mongodb/classes/kernel/clusterfilehandlers/ezfsfilehandler.php`

**Status: ✅ Funktioniert** — die Debug-Aufrufe `error_log` entfernt. Die Cache-Schicht ist bestätigt stabil.

#### `kernel/private/classes/ezcontentobjectstategroup.php` (Kerndatei, kein Override)

**Status: ✅ Funktioniert** — `arrayQuery` in den `else`-Zweig verschoben; der MongoDB-Pfad läuft zuerst.


### 15.13 Klassen im Umfeld der Cronjobs

| Klasse / Datei | Status | Hinweise |
|---|---|---|
| `ezurlaliasml.php` — `storePath()` | 🔴 | Viele Aufrufe von `arrayQuery`/`query` ohne Wirkung; der Neuaufbau der URL-Aliase legt nur Einträge auf oberster Ebene an |
| `kernel/content/edit.php` | ✅ | Debug-Zeilen `error_log()` entfernt; der grundlegende Ablauf der Veröffentlichung funktioniert |
| `kernel/content/attribute_edit.php` | ✅ | Debug-Zeilen entfernt (Zeilen 365, 402) |
| Aktualisierung des Index von `ezkeyword` | ✅ | `deleteStoredObjectAttribute()` gepatcht: eine zweistufige Aggregation findet ungenutzte Schlüsselwörter, `deleteWhere` räumt auf |
| `ezdbgarbagecollector.php` | ✅ | 4 Aufrufe von `arrayQuery` durch Anti-Join-Aggregationen mit `$nin` in MongoDB ersetzt |
| `eznodeassignment.php` | ✅ | Bestätigt bereits gepatcht — die verbleibenden Aufrufe von `arrayQuery` stehen in `else`-Zweigen, die nur für SQL gelten |
| `ezpolicylimitation.php` | ✅ | Zeile 374: zweistufige `aggregate` — die limitation_ids finden, danach die passenden Einschränkungen laden |
| ~~`ezcollaborationitem.php`~~ | ✅ | Bestätigt vollständig gepatcht — der MongoDB-Pfad mit einer Pipeline mit `$lookup` kehrt früh zurück; die SQL-Zweige mit `arrayQuery` sind nicht erreichbar |
| `ezcollaborationgroup.php` | ✅ | Zeile 318: Aggregation mit `$count` auf `ezcollab_item_group_link` |
| `lib/ezimage/classes/ezimagemanager.php` | ✅ | Zeile 195: `array_key_exists( $aliasName ?? '', ... )` — bereits behoben |


### 15.14 Gesamtübersicht des Fortschritts

Stand Juni 2026:

| Kategorie | Erfasste Methoden/Funktionen insgesamt | ✅ Funktioniert | ⚠️ Teilweise | 🔴 Defekt | 🔵 Nicht getestet |
|---|---|---|---|---|---|
| Datenbankadapter (`expMongoDB`) | 18 | 16 | 2 | 0 | 0 |
| ORM-Basis (`ezpersistentobject`) | 6 | 5 | 1 | 0 | 0 |
| Inhaltsklassen | 14 | 10 | 1 | 1 | 2 |
| Inhaltsobjekte | 21 | 14 | 1 | 3 | 3 |
| Knoten des Inhaltsbaums | 12 | 8 | 0 | 0 | 4 |
| URL-Aliase | 8 | 6 | 0 | 1 | 1 |
| Rollen / Berechtigungen | 7 | 1 | 0 | 5 | 1 |
| Sektionen | 4 | 2 | 0 | 0 | 2 |
| Suche / Suchprotokoll | 3 | 3 | 0 | 0 | 0 |
| Information Collector | 6 | 3 | 1 | 2 | 0 |
| Sprachen | 5 | 2 | 0 | 0 | 3 |
| Schema / Setup | 4 | 4 | 0 | 0 | 0 |
| Datentypen | 8 | 4 | 1 | 3 | 0 |
| Sonstiges / Siteaccess | 5 | 5 | 0 | 0 | 0 |
| Klassen im Umfeld der Cronjobs | 9 | 0 | 2 | 7 | 0 |
| **Summen** | **130** | **83 (64 %)** | **9 (7 %)** | **22 (17 %)** | **16 (12 %)** |

Die zentralen Arbeitsabläufe der Administration (Inhalte durchsuchen, bearbeiten, veröffentlichen, Klassenverwaltung, Sektionsliste, URL-Verwaltung, Information Collector, Suchstatistik) funktionieren alle. Die wichtigsten verbleibenden Lücken sind die Anzeige von Rollen und Berechtigungen, die Indizierung der Schlüsselwörter, der Neuaufbau der URL-Aliase (`storePath()`) sowie die Module für Shop, Workflow und Zusammenarbeit.


## 13. Referenzen

Die folgenden Quellen waren bei der ersten Einrichtung hilfreich. Beachten Sie, dass sie sich auf MongoDB 6.x/7.0
unter AlmaLinux beziehen — einige Schritte unterscheiden sich für MongoDB 8.3+ (z. B. die Repository-URLs, die Paketnamen,
und `mongosh` ersetzt die ältere Shell `mongo`).

- [Install MongoDB on AlmaLinux — Liquid Web](https://www.liquidweb.com/blog/install-mongodb-almalinux/) — Installationsanleitung Schritt für Schritt; behandelt die Einrichtung des Dienstes `mongod` und die grundlegende Authentifizierung
- [How to Install MongoDB on AlmaLinux 9 — HowtoForge](https://www.howtoforge.com/how-to-install-mongodb-on-almalinux-9/) — alternative Anleitung mit Hinweisen zu Firewall und SELinux
- [Create a Database — MongoDB Fundamentals](https://www.mongodb.com/resources/products/fundamentals/create-database) — offizieller Überblick über die Konzepte beim Anlegen von Datenbanken und Collections
- [db.changeUserPassword() — MongoDB Manual](https://www.mongodb.com/docs/manual/reference/method/db.changeUserPassword/) — Referenz zum Ändern der Passwörter von MongoDB-Benutzern über `mongosh`
- [Aggregation Pipeline — MongoDB Manual](https://www.mongodb.com/docs/manual/core/aggregation-pipeline/) — vollständige Referenz zu `$match`, `$lookup`, `$project`, `$group`, `$count`, `$skip`, `$limit`, `$sort`
- [Exponential 4.x Kernel Documentation](https://doc.ez.no/eZ-Publish/Technical-manual/4.x/) — die ursprüngliche SQL-basierte Architektur; hilfreich, um zu verstehen, was jede Klasse leisten soll



## 16. Design des MongoDB-Treibers und technischer Fertigstellungsgrad im Vergleich zu ezmysqli

Dieser Abschnitt vergleicht `expMongoDB` (den in diesem Projekt verwendeten MongoDB-Adapter) mit `eZMySQLi` (dem SQL-Referenzadapter, der mit Exponential ausgeliefert wird). Ziel ist es festzuhalten, wie vollständig der MongoDB-Treiber ist, welche Schnittstellen vollständig umgesetzt sind, welche nur Stubs sind, wo sich die Semantik unterscheidet und was für vollständige Gleichwertigkeit nötig wäre.


### 16.1 Klassenhierarchie und Schnittstellenvertrag

**`eZMySQLi`** erweitert `eZMySQLiDB`, das `eZDBInterface` erweitert.

**`expMongoDB`** erweitert `eZDBInterface` direkt.

`eZDBInterface` (unter `lib/ezdb/classes/ezdbinterface.php`) definiert den vollständigen Vertrag, den beide Treiber umsetzen müssen. Die Schnittstelle hat etwa 60 öffentliche Methoden für:
- den Lebenszyklus der Verbindung (`connect()`, `disconnect()`, `isConnected()`)
- das Ausführen von Abfragen (`query()`, `arrayQuery()`, `arrayQuery()` mit Parametern)
- die Datenmanipulation (`insert()`, `upsert()`, `deleteWhere()`)
- die Untersuchung des Schemas (`eZTableList()`, `tableInfo()`, `relationList()`)
- die Steuerung von Transaktionen (`begin()`, `commit()`, `rollback()`)
- Hilfen für Aggregate und Sequenzen (`nextSeqID()`, `lastSerialID()`)
- Escaping und Typumwandlung (`escapeString()`, `generateTextSQLString()`)
- die Fehlerbehandlung (`errorMessage()`, `errorNumber()`)
- Metadaten des Servers (`databaseName()`, `databaseServerVersion()`)


### 16.2 Vergleich Methode für Methode

#### Verbindung und Lebenszyklus

| Methode | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `__construct($params)` | Öffnet die MySQLi-Verbindung; setzt `IsConnected` | Pingt MongoDB an; setzt `IsConnected` | ✅ Gleichwertig |
| `connect()` | Verbindet sich mit den gespeicherten Parametern neu | Nicht umgesetzt (verwendet das verzögert erzeugte Singleton `getClient()`) | ⚠️ Stub — erneutes Verbinden wird nicht unterstützt |
| `disconnect()` | Schließt die MySQLi-Verbindung | Nicht umgesetzt | ⚠️ Stub — die Verbindung besteht pro Prozess dauerhaft |
| `isConnected()` | Gibt `$this->IsConnected` zurück | Gibt `$this->IsConnected` zurück | ✅ Gleichwertig |
| `useDatabase($name)` | Wählt eine andere Datenbank | Nicht umgesetzt — fest auf `'exp'` eingestellt | 🔴 Fehlt — der Datenbankname ist fest eingetragen |

**Der fest eingetragene Datenbankname** ist die wichtigste Einschränkung im Lebenszyklus. `eZMySQLi` liest `DatabaseName` aus `site.ini`. `expMongoDB` trägt `'exp'` in jeder Methode fest ein, die `selectCollection()` aufruft. Um mehrere Umgebungen (Entwicklung/Staging/Produktion) zu unterstützen, muss das über `site.ini[DatabaseSettings] DatabaseName` konfigurierbar werden.


#### Ausführen von Abfragen

| Methode | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `query($sql)` | Führt SQL aus; gibt eine Ergebnismenge oder `false` zurück | Gibt immer `false` zurück; ohne Wirkung | 🔴 Stub — SQL-DDL/DML ohne Wirkung |
| `arrayQuery($sql, $params)` | Führt ein SQL-SELECT aus; gibt ein Array assoziativer Arrays zurück | Gibt immer `[]` zurück; protokolliert MONGO TODO | 🔴 Stub — beabsichtigt; alle Aufrufer brauchen einen MongoDB-Zweig |
| `aggregate($table, $pipeline)` | Gibt es nicht | Führt eine MongoDB-Aggregationspipeline aus | ✅ MongoDB-eigen; keine SQL-Entsprechung |
| `find($table, $conds, $projection)` | Gibt es nicht | Ruft `selectCollection()->find()` auf | ⚠️ Mit Vorsicht verwenden — `translateConditions()` schränkt die Einsatzmöglichkeiten ein |
| `findOne($table, $conds)` | Gibt es nicht | Ruft `selectCollection()->findOne()` auf | ⚠️ Derselbe Vorbehalt |

**Wichtige Folge des Designs:** Jede Kernel-Klasse, die `$db->arrayQuery()` aufrief, ruft jetzt einen Stub auf, der `[]` zurückgibt. Die MongoDB-Portierung ersetzt jede Aufrufstelle durch einen ausdrücklichen Zweig `if ($db->databaseName() === 'mongo') { $rows = $db->aggregate(...); } else { $rows = $db->arrayQuery(...); }`. Das ist das grundlegende Muster des gesamten Portierungsprojekts.


#### Datenmanipulation

| Methode | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `insert($table, $doc)` | Teil von `query("INSERT INTO ...")` | `insertOne($doc)` auf die Collection | ✅ Umgesetzt (andere Signatur) |
| `upsert($table, $filter, $doc)` | Keine direkte Entsprechung; verwendet `INSERT ... ON DUPLICATE KEY` | `updateOne($filter, ['$set' => $doc], ['upsert' => true])` | ✅ Umgesetzt |
| `deleteWhere($table, $filter)` | Teil von `query("DELETE FROM ...")` | `deleteMany($filter)` auf die Collection | ✅ Umgesetzt |
| `mongoUpdateOne($table, $filter, $update)` | Gibt es nicht | `updateOne($filter, $update)` — für `$set`, `$inc`, `$push` usw. | ✅ MongoDB-eigen |
| `insertWithout($table, $doc, $keyExclusions)` | Gibt es in eZMySQLi | Nicht umgesetzt | 🔴 Fehlt |


#### Untersuchung des Schemas

| Methode | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `eZTableList()` | Gibt ein Array der Tabellennamen aus `SHOW TABLES` zurück | Gibt `[]` zurück (verhindert den Absturz in `generateUniqueTempTableName()`) | ⚠️ Stub — gibt eine leere Liste zurück; genügt, um den Absturz zu verhindern |
| `listCollectionNames()` | Gibt es nicht | Gibt über den Treiber ein Array der Namen der MongoDB-Collections zurück | ✅ MongoDB-eigen; für `systemupgrade.php` ergänzt |
| `tableInfo($table)` | Gibt die Metadaten der Spalten zurück (Name, Typ, Standardwert, nullbar) | Nicht umgesetzt | 🔴 Fehlt — wird für Werkzeuge zum Schemavergleich gebraucht |
| `relationList()` | Gibt die Fremdschlüsselbeziehungen zurück | Für MongoDB nicht anwendbar | – |
| `tableCount()` | Gibt die Anzahl der Tabellen zurück | Nicht umgesetzt | 🔴 Fehlt |
| `generateUniqueTempTableName($prefix)` | Sucht einen Namen, der nicht in `eZTableList()` steht | Funktioniert, weil `eZTableList()` `[]` zurückgibt — jeder Name ist eindeutig | ⚠️ Funktioniert zufällig |


#### Steuerung von Transaktionen

| Methode | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `begin()` | `START TRANSACTION` | Ohne Wirkung | ✅ Unbedenklich ohne Wirkung — MongoDB-Operationen sind pro Dokument atomar |
| `commit()` | `COMMIT` | Ohne Wirkung | ✅ Unbedenklich ohne Wirkung |
| `rollback()` | `ROLLBACK` | Ohne Wirkung | ⚠️ Bei einem Fehler werden die Daten NICHT zurückgerollt — teilweise Schreibvorgänge sind dauerhaft |
| `transactionIsStarted()` | Gibt bool zurück | Nicht umgesetzt | 🔴 Fehlt |

**Einschränkung beim Zurückrollen:** Wenn im SQL-Treiber mitten in einer Operation ein Fehler auftritt, macht `rollback()` alle Änderungen der Transaktion rückgängig. Im MongoDB-Treiber wird jeder Aufruf von `insert()`/`upsert()`/`deleteWhere()` sofort festgeschrieben. Schlägt eine Operation mit mehreren Schritten (wie `storeVersioned()`) auf halbem Weg fehl, bleibt die Datenbank in einem teilweise aktualisierten Zustand. Transaktionen über mehrere Dokumente (dafür ist ein Replica Set nötig) werden nicht verwendet.


#### Sequenzen und Autoincrement

| Methode | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `lastSerialID($table, $col)` | Gibt `mysqli_insert_id()` zurück | Gibt `$_lastInsertedID` zurück (von `insert()` gesetzt) | ⚠️ Funktioniert teilweise — wird nur nach `insert()` gesetzt, nicht nach `upsert()` |
| `nextSeqID($table, $col)` | `SELECT MAX(col)+1` über SQL | `aggregate` mit `$group/$max` | ✅ Gleichwertig |
| `nextAtomicID($name, $seedTable, $seedCol)` | Gibt es nicht | Atomares `findAndModify` auf die Collection `ezsequence` | ✅ MongoDB-eigen; dem SQL-Verfahren MAX+1 überlegen |

**`nextAtomicID` im Vergleich zu `nextSeqID`:** `nextSeqID` hat eine Race Condition — zwei gleichzeitige Anfragen können dasselbe MAX lesen und dieselbe ID erzeugen. `nextAtomicID` verwendet MongoDBs `findOneAndUpdate` mit `$inc`, das atomar und frei von Race Conditions ist. Die Portierung stellt die Erzeugung der IDs für die wichtigen Collections nach und nach auf `nextAtomicID` um.


#### Escaping und Typumwandlung

| Methode | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `escapeString($str)` | `mysqli_real_escape_string()` | Gibt die Zeichenkette unverändert zurück (in MongoDB keine Gefahr einer SQL-Injection) | ✅ Für MongoDB richtig |
| `generateTextSQLString($value)` | Setzt den Wert in SQL-Anführungszeichen | Nicht umgesetzt | 🔴 Fehlt (wird nur von Aufrufern im SQL-Pfad aufgerufen) |
| `md5($str)` | Gibt das SQL-Fragment `MD5('...')` zurück | Nicht überschrieben — gibt das SQL-Fragment zurück | 🔴 FEHLER — Aufrufer, die den Rückgabewert als echten Hash verwenden, erhalten SQL-Syntax |
| `generateSQLOperator($op)` | Ordnet einen PHP-Operator SQL zu | Nicht anwendbar | – |
| `generateSQLINStatement($arr, $col)` | Baut das SQL `col IN (...)` | Nicht umgesetzt | 🔴 Fehlt |

**Der Fehler in `md5()`:** `eZDBInterface::md5($str)` gibt `MD5('string')` als SQL-Fragment für die Verwendung in SQL-Abfragen zurück. `expMongoDB` erbt das, ohne es zu überschreiben. Jeder Aufrufer, der `$hash = $db->md5($str)` ausführt und `$hash` als echten Hashwert verwendet, erhält statt des Hashs die wörtliche Zeichenkette `"MD5('...')"`. Die neu geschriebene Methode `translate()` in `ezurlaliasml.php` ruft deshalb ausdrücklich PHPs `md5()` auf. Alle anderen Aufrufer müssen geprüft werden.


#### Sperren

| Methode | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `lock($table)` | `LOCK TABLES ... WRITE` | Ohne Wirkung | ⚠️ Keine Sperren — gleichzeitige Schreibvorgänge können sich überlagern |
| `unlock()` | `UNLOCK TABLES` | Ohne Wirkung | ⚠️ Ebenso |

**Auswirkung:** In Exponential schützten Tabellensperren die Sequenzen und den Umbau des Baums. Ohne Sperren können gleichzeitige Anfragen, die den Inhaltsbaum umbauen (Knoten verschieben, veröffentlichen), widersprüchliche Werte für `path_string` oder `depth` erzeugen. Bei der derzeitigen Nutzung der Administration durch einen einzelnen Benutzer ist das kein Problem; bei Produktionsverkehr könnte es Daten beschädigen.


#### Fehlerbehandlung

| Methode | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `errorMessage()` | Gibt die letzte MySQLi-Fehlermeldung zurück | Nicht umgesetzt — gibt eine leere Zeichenkette zurück | 🔴 Fehlt |
| `errorNumber()` | Gibt den letzten MySQLi-Fehlercode zurück | Nicht umgesetzt — gibt 0 zurück | 🔴 Fehlt |
| `checkError()` | Schreibt den Fehler in das Debug-Protokoll | Nicht umgesetzt | 🔴 Fehlt |
| `availableDatabases()` | `SHOW DATABASES` | Nicht umgesetzt | 🔴 Fehlt |

Derzeit werden alle MongoDB-Fehler in try/catch-Blöcken innerhalb jeder Methode abgefangen und mit `error_log()` geschrieben. Sie gelangen weder zu `eZDebug` noch in die Leiste der Administration. Das erschwert die Diagnose MongoDB-spezifischer Fehler während der Entwicklung.


#### Debugging und Profiling

| Methode / Funktion | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| SQL-Ausgabe in der Leiste | Jede Abfrage erscheint in der Debug-Leiste mit SQL-Text und Zeit | Jeder Aufruf von `aggregate()` erscheint mit dem Namen der Collection + JSON der Match-Stufe | ✅ Gleichwertig (über `reportQuery()` umgesetzt) |
| Protokollierung von MONGO TODO | – | Jeder Aufruf von `arrayQuery()` wird mit Datei:Zeile des Aufrufers in `error_log` protokolliert | ✅ Ergänzung nur für MongoDB |
| Schalter `OutputSQL` | Steuert, ob Abfragen in der Leiste erscheinen | Wird beachtet — derselbe Schalter steuert `reportQuery()` | ✅ Gleichwertig |
| Zeitakkumulatoren der Abfragen | `eZDebug::accumulatorStart/Stop('mysql_cluster_query')` | `eZDebug::accumulatorStart/Stop('mongodb_cluster_query')` | ✅ Gleichwertig |
| Protokoll langsamer Abfragen | MySQLi-Protokoll langsamer Abfragen über `my.cnf` | Nicht umgesetzt — keine Erkennung langsamer Abfragen über einen Schwellenwert | 🔴 Fehlt |


#### `translateConditions()` im Vergleich zur Erzeugung von SQL-WHERE-Klauseln

Das ist die bedeutendste funktionale Lücke zwischen den Treibern.

**`eZMySQLi`** erzeugt SQL-WHERE-Klauseln, indem es Werte mit `escapeString()` direkt in SQL-Zeichenketten einsetzt. Die volle Ausdruckskraft von SQL steht zur Verfügung.

**`expMongoDB::translateConditions($conds)`** verarbeitet:
- skalare Werte: `['field' => 'value']` → `['field' => 'value']`
- den Operator `like`: `['field' => ['like' => '%val%']]` → `['field' => ['$regex' => '.*val.*']]`
- numerische Zeichenketten, die automatisch in int umgewandelt werden, wenn der Wert numerisch aussieht

**Verarbeitet NICHT:**
- `['field' => ['$in' => [...]]]` — wird verworfen
- `['field' => ['$or' => [...]]]` — wird verworfen
- `['$expr' => [...]]` — wird verworfen
- jedes verschachtelte Operator-Array — der äußere Schlüssel bleibt erhalten, der Wert wird aber `null`

Das bedeutet: `find()` und `findOne()` sind nur für einfache skalare Gleichheitsfilter sicher. Jede Abfrage mit `$in`, `$or`, `$expr` oder berechneten Bedingungen **muss** `aggregate()` mit einer ausdrücklichen Stufe `$match` verwenden. Das ist die Ursache der meisten Einträge MONGO TODO: Kernel-Klassen, die `$db->find($table, $complexCondition)` aufriefen, mussten stattdessen auf `$db->aggregate($table, [['$match' => $complexCondition]])` umgeschrieben werden.


### 16.3 Codestil: eZMySQLi im Vergleich zu expMongoDB

#### Unterschiede im Codestil

| Aspekt | eZMySQLi | expMongoDB |
|---|---|---|
| Sichtbarkeit der Methoden | Alle public (älterer Stil aus PHP 4) | Mischung aus public/protected/private |
| Sichtbarkeit der Eigenschaften | `var $Property` (älterer Stil) | `private $_lastInsertedID` usw. |
| Fehlerbehandlung | Setzt `$this->ErrorMessage`; ruft `$this->checkError()` auf | try/catch mit `error_log()` |
| Docblocks | Javadoc-Stil von Exponential (`/*!` ... `*/`) | Minimal oder keine |
| Ergebnisformat | Schleife mit `mysqli_fetch_assoc()`; baut ein Array | `$cursor->toArray()`, pro Dokument über `getArrayCopy()` umgewandelt |
| Umgang mit null | Gibt bei einem Fehler in den meisten Methoden `false` zurück | Gibt je nach Methode `[]` oder `false` zurück |
| Server-Multiplexing | Unterstützt den Parameter `SERVER_MASTER` / `SERVER_SLAVE` | Nur eine Verbindung (der Parameter `$server` wird ignoriert) |

#### Lücken gegenüber den Standards in expMongoDB

Die folgenden Muster aus `eZMySQLi` werden in `expMongoDB` nicht befolgt und sollten bei einem künftigen Refactoring angegangen werden:

1. **Fest eingetragener Datenbankname.** Jede Methode enthält `$dbName = 'exp'`. Er sollte aus `eZINI::instance('site.ini')->variable('DatabaseSettings', 'DatabaseName')` gelesen werden, um mehrere Umgebungen zu unterstützen.

2. **Keine Umsetzung von `errorMessage()` / `errorNumber()`.** Fehler werden stillschweigend verschluckt oder in `error_log` geschrieben. Das Muster von eZMySQLi, Fehler in Instanzvariablen zu speichern und über das Debug-System sichtbar zu machen, sollte übernommen werden.

3. **Keine Unterstützung von `SERVER_MASTER` / `SERVER_SLAVE`.** Der Parameter `$server` steht in den Methodensignaturen (von der Schnittstelle geerbt), wird aber ignoriert. Für skalierbare Lesezugriffe mit einem MongoDB-Replica-Set sollten Lesezugriffe an Secondaries geleitet werden können.

4. **`lastSerialID()` wird von `upsert()` nicht gesetzt.** Nach einem Upsert, das ein neues Dokument einfügt, ruft der Aufrufer möglicherweise `lastSerialID()` auf und erwartet die ID des neuen Dokuments. Derzeit setzt nur `insert()` den Wert `$_lastInsertedID`.

5. **Kein Override von `generateTextSQLString()`.** Ältere Aufrufer, die diese Methode verwenden, um SQL-Fragmente zu bauen, erhalten Zeichenketten in SQL-Syntax statt für MongoDB sicherer Werte. Alle solchen Aufrufer müssen geprüft werden.

6. **`md5()` wird nicht überschrieben.** Gibt aus der Basisklasse das SQL-Fragment `MD5('...')` zurück. Muss überschrieben werden und den PHP-Hash `md5($str)` zurückgeben, damit es sicher ist.

7. **Keine Logik für das erneute Verbinden.** Bricht die MongoDB-Verbindung mitten in einer Anfrage ab, gibt es weder einen erneuten Versuch noch ein erneutes Verbinden. `eZMySQLi` hat eine Schleife zum erneuten Verbinden.

8. **Der Schalter `$OutputSQL` wird in `find()` und `findOne()` nicht geprüft.** Diese Abfragen rufen `reportQuery()` nicht auf und sind in der Debug-Leiste unsichtbar; das Profiling bleibt dadurch unvollständig.


### 16.4 Matrix des Fertigstellungsgrads

| Funktionsbereich | eZMySQLi | expMongoDB | Lücke |
|---|---|---|---|
| Grundlegende CRUD-Operationen | 100 % | 95 % | `insertWithout()` fehlt |
| Untersuchung des Schemas | 100 % | 15 % | `tableInfo()`, `tableCount()`, `relationList()` fehlen |
| Unterstützung von Transaktionen | 100 % | 30 % | Zurückrollen ohne Wirkung; teilweise Schreibvorgänge dauerhaft |
| Sequenzen / Autoincrement | 100 % | 90 % | `lastSerialID()` nach Upsert unvollständig |
| Fehlermeldungen | 100 % | 20 % | Fehler gehen nur an `error_log`; erscheinen nicht in der Debug-Leiste |
| Sperren | 100 % | 0 % | Überhaupt keine Sperren |
| Escaping / Typsicherheit | 100 % | 70 % | `md5()` nicht überschrieben; `generateTextSQLString()` fehlt |
| Einbindung in die Debug-Leiste | 100 % | 75 % | `find()`/`findOne()` werden nicht gemeldet; kein Schwellenwert für langsame Abfragen |
| Übersetzung komplexer Filter | 100 % (volles SQL) | 40 % (nur skalar) | Für jeden Operator muss `aggregate()` verwendet werden |
| Mehrere Server / Replikation | 100 % (Master/Slave) | 0 % | Der Parameter `$server` wird ignoriert |
| Konfiguration der Umgebung | 100 % (liest site.ini) | 10 % | Datenbankname fest eingetragen |
| **Gesamt** | **100 %** | **~55 %** | Der Schnittstellenvertrag ist teilweise erfüllt |


### 16.5 Reihenfolge für das Schließen der Lücke

Geordnet nach der Auswirkung auf Stabilität und Korrektheit bei der derzeitigen Nutzung:

1. **`md5()` in `expMongoDB` überschreiben** — eine Korrektur in einer Zeile; verhindert, dass ein Aufrufer, der `$db->md5()` als echten Hash verwendet, stillschweigend falsche Daten erhält.

2. **Den Datenbanknamen konfigurierbar machen** — aus `site.ini[DatabaseSettings] DatabaseName` lesen; das fest eingetragene `'exp'` ist ein Risiko bei der Bereitstellung.

3. **`$_lastInsertedID` in `upsert()` für neue Dokumente setzen** — `storeObject()` ruft nach dem Upsert `lastSerialID()` auf; derzeit liefert es einen veralteten Wert oder false.

4. **MongoDB-Fehler in der Debug-Leiste sichtbar machen** — Exceptions abfangen, `eZDebug::writeError()` aufrufen, `$this->ErrorMessage` setzen.

5. **Aufrufe von `reportQuery()` in `find()` und `findOne()` ergänzen** — diese Abfragen sind in der Debug-Leiste unsichtbar; das Profiling bleibt dadurch unvollständig.

6. **Einen Stub `transactionIsStarted()` ergänzen, der `false` zurückgibt** — mancher Kernel-Code macht Operationen von dieser Prüfung abhängig; die Rückgabe false ist richtig und verhindert unerwartetes Verhalten.

7. **Unterstützung von Transaktionen über mehrere Dokumente (langfristig)** — erfordert die Einrichtung eines MongoDB-Replica-Sets. Für die derzeitige Installation auf einem einzelnen Knoten außerhalb des Umfangs, für die Produktion aber wichtig.


## 17. Weitere Korrekturen — System-Upgrade, RAD-Codegeneratoren, BC-CIE-Export, Sprach-Bitmaske

Dieser Abschnitt beschreibt alle Korrekturen und Umbauten aus der Arbeitssitzung vom 28. Mai 2026.


### 17.1 Seite des System-Upgrades (`/setup/systemupgrade`) — vollständiger Umbau

**Problem:** Der MongoDB-Zweig von `kernel/setup/systemupgrade.php` baute eine flache Berichtszeichenkette als reinen Text (über `$upgrade_sql` an das Template übergeben) mit der SQL-Kommentarsyntax `-- comment`, ohne Kommandozeilenbefehle und ohne Struktur. Das Template zeigte „run the following SQL commands“, was für MongoDB falsch war. Das Setzen von `$upgrade_sql = true` (boolesch) führte dazu, dass die Prüfung `|eq('ok')` des Templates `true` ergab (lockerer Vergleich in PHP `true == 'ok'`) und fälschlich „Database check OK“ angezeigt wurde.

**Vorgenommene Korrekturen:**

`kernel/setup/systemupgrade.php`:
- Den Markerwert `$upgrade_sql` von `true` bzw. einer bloßen Zeichenkette auf `'mongo'` geändert — `'mongo' == 'ok'` ist `false`, daher wird der Warnblock richtig dargestellt
- Statt eine Berichtszeichenkette zu bauen, werden jetzt strukturierte Template-Variablen gesetzt:
  - `$mongo_missing_count` — die Anzahl der fehlenden Collections als Ganzzahl
  - `$mongo_grouped_list` — ein indiziertes Array von Hashes `['name', 'count', 'collections']` für die Iteration im Template
  - `$mongo_extra` — ein Array zusätzlicher Collections, die in MongoDB vorhanden, im SQL-Schema aber nicht enthalten sind
  - `$mongo_create_cmd` — ein JS-Schnipsel für `mongosh` (`forEach(function(c){ db.createCollection(c); })`) für alle fehlenden Collections, die nicht zum Adapter gehören

`design/admin/templates/setup/systemupgrade.tpl`:
- Der Zweig `{if $mongo_check}` wurde durch eine strukturierte HTML-Darstellung ersetzt:
  - Überschrift `<h2>` mit der Anzahl: `MongoDB collection check — N collection(s) not yet created.`
  - Ein erklärender Absatz (keine Aktion erforderlich)
  - Ein Block `<pre>` mit dunklem Hintergrund und dem mongosh-Befehl zum vorherigen Anlegen
  - Funktionsgruppen als Paare aus `<h4>` + `<ul>/<li><code>`
  - Ein durch `<hr>` abgetrennter Abschnitt für zusätzliche Collections
- Der SQL-Ausweichpfad (Zweig `{else}`) ist unverändert

**Dateien:** `kernel/setup/systemupgrade.php`, `design/admin/templates/setup/systemupgrade.tpl`


### 17.2 RAD-Codegeneratoren (`/setup/rad`) — Kompatibilität mit PHP 8.5

Die RAD-Werkzeuge erzeugen PHP-Gerüstdateien zum Herunterladen. Der erzeugte Code war für Exponential 4 / PHP 5 geschrieben und ließ sich unter PHP 8.5 nicht kompilieren.

#### Assistent für Template-Operatoren (`templateoperator_code.tpl`)

| Fehler | Korrektur |
|-----|-----|
| `{set-block}` + `indent(sum(35,$operator_name\|count))` — `count()` auf eine Zeichenkette löst in PHP 8 einen `TypeError` aus | `{set-block}` vollständig entfernt; `namedParameterList()` verwendet jetzt die flache Array-Syntax `[]` |
| `for($i=0; $i<count($operatorParameters); ++$i)` | Durch `foreach` ersetzt |
| Keine Sichtbarkeit / keine Rückgabetypen an den Methoden | Alle Methoden jetzt `public` mit Rückgabetypen (`array`, `bool`, `void`) |
| Doxygen-Kommentare `/*!` | Durch PHPDoc `/** */` ersetzt |
| Die Anleitung zum Autoload verwendete die Syntax `array()` und nannte die falsche INI-Datei | Auf die Syntax `[]` und `site.ini.append.php` aktualisiert |

#### Assistent für Datentypen (`datatype_code.tpl`)

| Fehler | Korrektur |
|-----|-----|
| `parent::__construct(self::CONST, "bare string")` | Geändert zu `ezpI18n::tr('extension/datatypes', 'Name', 'Datatype name')` |
| `eZDataType::register(ClassName::CONST, "ClassName")` — bloße Zeichenkette | Geändert zu `ClassName::class` |
| Keine Sichtbarkeit / keine Rückgabetypen an den Methoden | Alle Methoden jetzt `public` mit skalaren Rückgabetypen |
| Doxygen-Kommentare `/*!` | Durch PHPDoc `/** */` ersetzt |
| Keine Anleitung zur Registrierung in der erzeugten Datei | Einen vollständigen Kopfkommentar mit Schnipseln für `ezdatatypeautoload.php` + `site.ini.append.php` ergänzt |

#### Controller des Datentyp-Assistenten (`kernel/setup/datatype.php`)

`datatypeDownload()` verwendete `$persistentData['class-name']` direkt für `$filename = strtolower($className) . '.php'`, ohne zu prüfen, ob der Benutzer das Feld leer gelassen hatte. War es leer: `$className = false` → der Dateiname war `.php`, und der Klassenrumpf enthielt `class ` (ein PHP-Syntaxfehler in der heruntergeladenen Datei). Eine Prüfung auf null mit einem Ausweichwert wurde ergänzt, die den Klassennamen automatisch aus dem Namen des Datentyps erzeugt.

**Dateien:** `design/admin/templates/setup/templateoperator_code.tpl`, `design/admin/templates/setup/datatype_code.tpl`, `kernel/setup/datatype.php`

Siehe auch: `RAD-CODEGEN-UPDATES.md` im Wurzelverzeichnis des Projekts für alle Einzelheiten.


### 17.3 BC-CIE-Export (`/bccie/overview`) — Stubs mit MONGO TODO

**Problem:** `extension/bccie/classes/bccieExportUtils.php` enthielt zwei Aufrufe von `$db->arrayQuery()` mit rohen SQL-Joins über mehrere Tabellen, die beide `MONGO TODO` in der Debug-Leiste protokollierten.

#### `getObjectsWithCollectedInformation()` (Zeile 16)

Ursprüngliches SQL: ein Join über 4 Tabellen, `ezinfocollection`, `ezcontentobject`, `ezcontentobject_tree` und `ezcontentclass`, mit `SELECT DISTINCT`.

Korrektur für MongoDB:
1. `$group` nach `contentobject_id` auf `ezinfocollection` + `$sort` + `$skip` + `$limit` für die Seitenaufteilung
2. Für jede gefundene ID: `eZContentObject::fetch()` (bereits MongoDB-fähig), um das Objekt zu holen; `eZContentClass::fetch()`, um die Klasse zu holen; `attribute('main_node')` für den Baumknoten
3. Baut ein flaches Ergebnis-Array, das alle Schlüssel von `eZContentClass::definition()['fields']` füllt, sodass `new eZContentClass($row)` im nachfolgenden Code funktioniert

#### `getCollectorObjectsCount()` (Zeile 76)

Ursprüngliches SQL: `COUNT(DISTINCT contentobject_id)` über 3 Tabellen.

Korrektur für MongoDB: ein einfaches `$group` nach `contentobject_id` + eine Stufe `$count` auf `ezinfocollection`.

**Erkennung:** Beide Methoden erkennen MongoDB über `$db->databaseName() === 'mongo'` und fallen andernfalls auf den ursprünglichen SQL-Pfad zurück.

**Datei:** `extension/bccie/classes/bccieExportUtils.php`


### 17.4 `$bitwiseAnd` → `$bitAnd` (Seite der Sprachen)

**Problem:** `objectCount()` und `classCount()` in `ezcontentlanguage.php` verwendeten `$bitwiseAnd` innerhalb einer Aggregation mit `$expr` — das ist kein gültiger Operator von MongoDB 6.3+. Gibt stillschweigend `null` zurück → `null > 0` → `false` → 0 Zeilen für jede Sprache.

**Korrektur:** Beide Vorkommen wurden auf `$bitAnd` geändert (den richtigen Operator für Aggregationsausdrücke in MongoDB 6.3+).

**Datei:** `extension/sevenx_mongodb/classes/kernel/ezcontentlanguage.php` (Zeilen 802, 831)


### 17.5 E_DEPRECATED bei einem nullbaren Parameter (`ezcontentcachemanager.php`)

`clearNodeViewCacheArray(array $nodeList, array $contentObjectList = null)` — implizit nullbare Parameter sind seit PHP 8.4 veraltet.

**Korrektur:** Geändert zu `?array $contentObjectList = null`.

**Datei:** `extension/sevenx_mongodb/classes/kernel/ezcontentcachemanager.php` (Zeile 763)


### 17.6 URL-Wildcards — beschädigte verwaiste Dokumente

**Problem:** Die Collection `ezurlwildcard` enthielt 2 Dokumente, die nur aus `{ _id: '1' }` / `{ _id: '2' }` bestanden — ohne die Felder `source_url`, `destination_url` und `type`. Das Template stellte `type` als `Undefined` dar.

**Korrektur:** `db.ezurlwildcard.deleteMany({})` über `mongosh` — die beschädigten verwaisten Datensätze wurden gelöscht. Die Collection ist jetzt leer, und die Seite zeigt richtig `(0)`.



## 18. Leitfaden zur Umstellung von SQL-Datenbanken — ein beliebiges RDBMS nach JSON exportieren und in MongoDB importieren

Dieser Abschnitt ist eine praktische Referenz, um eine vorhandene relationale Datenbank — aus jeder unterstützten Engine (SQLite, MariaDB, MySQL, Oracle, PostgreSQL) — in MongoDB-Collections zu überführen, die mit diesem Projekt verwendet werden. Jeder Unterabschnitt entspricht unmittelbar einem konkreten Ablauf über die Kommandozeile oder ein Skript.

Das Ziel ist eine saubere, wiederholbare Migration in eine Richtung: jede SQL-Tabelle nach JSON exportieren (ein Dokument pro Zeile) und danach jede JSON-Datei gesammelt in die passende MongoDB-Collection importieren.


### 18.1 Wichtige Begriffe vor dem Start

#### Relationales Modell und Dokumentmodell

SQL-Tabellen lassen sich sauber auf MongoDB-Collections abbilden: eine Tabelle → eine Collection, eine Zeile → ein Dokument. Sie müssen **nicht** einbetten oder denormalisieren — der Adapter sevenx_mongodb arbeitet mit flachen Dokumenten, die das ursprüngliche SQL-Schema genau abbilden.

#### Feldtypen

MongoDB speichert BSON-Typen. Die wichtigsten Umwandlungen, die Sie kennen sollten:

| SQL-Typ | MongoDB-/BSON-Typ | Hinweise |
|----------|--------------------|----|
| `INT`, `BIGINT` | `int32` / `int64` | Müssen Ganzzahlen sein, keine Zeichenketten |
| `FLOAT`, `DECIMAL` | `double` | |
| `VARCHAR`, `TEXT`, `CHAR` | `string` | |
| `TINYINT(1)` (boolesch) | `int32` (0/1) | MongoDB hat `bool`, das CMS verwendet aber 0/1 |
| `DATETIME`, `TIMESTAMP` | `int64` (Unix-Epoche) | Exponential speichert alle Zeitstempel als Ganzzahlen |
| `BLOB`, `LONGBLOB` | `string` (base64) oder `BinData` | Binärfelder sind in eZ selten; verwenden Sie eine base64-Zeichenkette |
| `NULL` | Feld weglassen oder `null` | Lassen Sie dünn besetzte Felder vorzugsweise weg |

#### Das Feld `_id`

MongoDB verlangt für jedes Dokument eine eindeutige `_id`. Für Tabellen mit einem numerischen Primärschlüssel:
- Verwenden Sie den Wert des SQL-Primärschlüssels als `_id` (in eine Ganzzahl umgewandelt)
- Für Tabellen mit zusammengesetzten Schlüsseln: verketten Sie sie, `"nodeID_version"`, oder verwenden Sie eine automatisch erzeugte ObjectId

Der Adapter sevenx_mongodb erwartet, dass `_id` bei allen zentralen eZ-Tabellen der Ganzzahl des SQL-Primärschlüssels entspricht.

#### Reihenfolge

Importieren Sie die Tabellen in der Reihenfolge ihrer Abhängigkeiten — referenzierte Tabellen zuerst. Die mindestens sichere Reihenfolge:

1. `ezcontentclass`
2. `ezcontentclass_attribute`
3. `ezsection`
4. `ezcontentobject`
5. `ezcontentobject_version`
6. `ezcontentobject_attribute`
7. `ezcontentobject_tree`
8. `ezcontentobject_name`
9. `ezurlalias_ml`
10. `ezuser`, `ezuser_setting`, `ezrole`, `ezpolicy`, `ezpolicy_limitation`, `ezpolicy_limitation_value`
11. Alle übrigen Tabellen


### 18.2 MySQL / MariaDB → JSON

Für den Export sind MySQL und MariaDB gleich.

#### Möglichkeit A — `mysqldump` mit JSON-Ausgabe (MySQL 8.0+ / MariaDB 10.6+)

```bash
# Export a single table as newline-delimited JSON (one JSON object per line)
mysql -u root -p exp -e "
  SELECT JSON_OBJECT(
    'id', id,
    'contentclass_id', contentclass_id,
    'name', name,
    'language_mask', language_mask,
    'status', status,
    'published', published,
    'modified', modified,
    'owner_id', owner_id,
    'current_version', current_version,
    'remote_id', remote_id,
    'section_id', section_id
  )
  FROM ezcontentobject
  WHERE status = 1;
" --skip-column-names --raw > ezcontentobject.ndjson
```

Das erzeugt NDJSON (zeilenweise getrenntes JSON), das `mongoimport` von sich aus liest.

#### Möglichkeit B — Einzeiler `mysql2json` in der Shell (jede MySQL-Version)

```bash
mysql -u root -p --batch --silent -e "SELECT * FROM ezcontentobject" exp \
  | python3 -c "
import sys, json
lines = sys.stdin.read().strip().split('\n')
headers = lines[0].split('\t')
for row in lines[1:]:
    vals = row.split('\t')
    doc = dict(zip(headers, vals))
    # Cast numeric primary key to int for _id
    if 'id' in doc:
        doc['_id'] = int(doc['id'])
    print(json.dumps(doc))
" > ezcontentobject.ndjson
```

#### Möglichkeit C — Python `mysql-connector` (für die Produktion empfohlen)

```python
#!/usr/bin/env python3
"""
mysql_to_ndjson.py — export all Exponential tables to NDJSON files.
Usage: python3 mysql_to_ndjson.py --host localhost --user root --password X --db exp --outdir ./json
"""
import argparse, json, os, mysql.connector

PK_MAP = {
    'ezcontentobject':          'id',
    'ezcontentobject_version':  None,   # composite: contentobject_id + version
    'ezcontentobject_attribute':'id',
    'ezcontentobject_tree':     'node_id',
    'ezcontentclass':           'id',
    'ezcontentclass_attribute': 'id',
    'ezsection':                'id',
    'ezcontentobject_name':     None,
    'ezurlalias_ml':            'id',
    'ezuser':                   'contentobject_id',
}

def cast_row(row, pk_field):
    doc = {}
    for k, v in row.items():
        if isinstance(v, (bytes, bytearray)):
            v = v.decode('utf-8', errors='replace')
        elif v is None:
            continue   # omit null fields — keeps documents lean
        doc[k] = v
    if pk_field and pk_field in doc:
        doc['_id'] = int(doc[pk_field])
    return doc

def export_table(cur, table, pk_field, outdir):
    cur.execute(f"SELECT * FROM `{table}`")
    cols = [d[0] for d in cur.description]
    path = os.path.join(outdir, f"{table}.ndjson")
    count = 0
    with open(path, 'w') as f:
        for row_tuple in cur:
            row = dict(zip(cols, row_tuple))
            doc = cast_row(row, pk_field)
            f.write(json.dumps(doc) + '\n')
            count += 1
    print(f"  {table}: {count} rows → {path}")

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--host', default='localhost')
    ap.add_argument('--user', required=True)
    ap.add_argument('--password', required=True)
    ap.add_argument('--db', required=True)
    ap.add_argument('--outdir', default='./json_export')
    ap.add_argument('--tables', nargs='*', help='Specific tables (default: all)')
    args = ap.parse_args()

    os.makedirs(args.outdir, exist_ok=True)
    conn = mysql.connector.connect(
        host=args.host, user=args.user, password=args.password,
        database=args.db, charset='utf8mb4'
    )
    cur = conn.cursor(dictionary=False)

    if args.tables:
        tables = args.tables
    else:
        cur.execute("SHOW TABLES")
        tables = [r[0] for r in cur]

    for table in tables:
        pk = PK_MAP.get(table, 'id')
        export_table(cur, table, pk, args.outdir)

    cur.close()
    conn.close()

if __name__ == '__main__':
    main()
```

Abhängigkeit installieren: `pip install mysql-connector-python`

Ausführen:
```bash
python3 mysql_to_ndjson.py --user root --password secret --db exp --outdir ./json_export
```


### 18.3 PostgreSQL → JSON

#### Möglichkeit A — `psql` `\copy` mit `json_agg`

```sql
-- Export ezcontentobject to NDJSON
\copy (
  SELECT row_to_json(t) FROM (
    SELECT *, id AS "_id" FROM ezcontentobject WHERE status = 1
  ) t
) TO '/tmp/ezcontentobject.ndjson';
```

`row_to_json()` gibt pro Zeile ein JSON-Objekt aus. `\copy` schreibt es in eine Datei, ohne dass Superuser-Rechte nötig sind.

#### Möglichkeit B — Python `psycopg2` (empfohlen)

```python
#!/usr/bin/env python3
"""
pg_to_ndjson.py — export PostgreSQL eZ tables to NDJSON.
pip install psycopg2-binary
"""
import argparse, json, os, psycopg2, psycopg2.extras, decimal, datetime

def serialize(v):
    if isinstance(v, decimal.Decimal):
        return float(v)
    if isinstance(v, (datetime.datetime, datetime.date)):
        return int(v.timestamp())  # eZ stores timestamps as integers
    if isinstance(v, memoryview):
        return v.tobytes().decode('utf-8', errors='replace')
    return v

def export_table(cur, table, pk_field, outdir):
    cur.execute(f'SELECT * FROM "{table}"')
    path = os.path.join(outdir, f"{table}.ndjson")
    count = 0
    with open(path, 'w') as f:
        for row in cur:
            doc = {k: serialize(v) for k, v in row.items() if v is not None}
            if pk_field and pk_field in doc:
                doc['_id'] = int(doc[pk_field])
            f.write(json.dumps(doc) + '\n')
            count += 1
    print(f"  {table}: {count} rows → {path}")

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--dsn', required=True, help='e.g. postgresql://user:pass@host/dbname')
    ap.add_argument('--outdir', default='./json_export')
    ap.add_argument('--tables', nargs='*')
    args = ap.parse_args()

    os.makedirs(args.outdir, exist_ok=True)
    conn = psycopg2.connect(args.dsn)
    cur = conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor)

    if args.tables:
        tables = args.tables
    else:
        cur.execute("""
            SELECT tablename FROM pg_tables
            WHERE schemaname = 'public'
            ORDER BY tablename
        """)
        tables = [r['tablename'] for r in cur]

    from mysql_to_ndjson import PK_MAP   # reuse the same map
    for table in tables:
        pk = PK_MAP.get(table, 'id')
        export_table(cur, table, pk, args.outdir)

    cur.close()
    conn.close()

if __name__ == '__main__':
    main()
```

Ausführen:
```bash
python3 pg_to_ndjson.py --dsn postgresql://root:secret@localhost/exp --outdir ./json_export
```


### 18.4 SQLite → JSON

SQLite hat keinen eingebauten JSON-Export, aber Pythons Modul `sqlite3` gehört zur Standardbibliothek — es sind keine weiteren Abhängigkeiten nötig.

```python
#!/usr/bin/env python3
"""
sqlite_to_ndjson.py — export all tables from a SQLite database to NDJSON.
No dependencies beyond Python 3 standard library.
Usage: python3 sqlite_to_ndjson.py path/to/database.db ./json_export
"""
import sys, json, os, sqlite3

PK_FIELDS = {
    'ezcontentobject':          'id',
    'ezcontentobject_tree':     'node_id',
    'ezcontentclass':           'id',
    'ezcontentclass_attribute': 'id',
    'ezsection':                'id',
    'ezurlalias_ml':            'id',
    'ezuser':                   'contentobject_id',
}

def main(db_path, outdir):
    os.makedirs(outdir, exist_ok=True)
    conn = sqlite3.connect(db_path)
    conn.row_factory = sqlite3.Row

    tables = [r[0] for r in conn.execute(
        "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name"
    )]

    for table in tables:
        pk = PK_FIELDS.get(table, 'id')
        path = os.path.join(outdir, f"{table}.ndjson")
        count = 0
        with open(path, 'w') as f:
            for row in conn.execute(f"SELECT * FROM \"{table}\""):
                doc = {k: row[k] for k in row.keys() if row[k] is not None}
                if pk and pk in doc:
                    doc['_id'] = int(doc[pk])
                f.write(json.dumps(doc) + '\n')
                count += 1
        print(f"  {table}: {count} rows → {path}")

    conn.close()

if __name__ == '__main__':
    if len(sys.argv) < 3:
        print(f"Usage: {sys.argv[0]} <db.sqlite> <outdir>")
        sys.exit(1)
    main(sys.argv[1], sys.argv[2])
```

Ausführen:
```bash
python3 sqlite_to_ndjson.py /var/www/ez.sqlite ./json_export
```


### 18.5 Oracle → JSON

Oracle 12c+ unterstützt `JSON_OBJECT()` von sich aus. Verwenden Sie für ältere Versionen Python `cx_Oracle` / `oracledb`.

#### Möglichkeit A — SQL in Oracle 12c+

```sql
-- In SQL*Plus or SQLcl, spool to a file
SPOOL /tmp/ezcontentobject.ndjson
SELECT JSON_OBJECT(
    'id'               VALUE id,
    '_id'              VALUE id,
    'contentclass_id'  VALUE contentclass_id,
    'name'             VALUE name,
    'language_mask'    VALUE language_mask,
    'status'           VALUE status,
    'published'        VALUE published,
    'modified'         VALUE modified,
    'owner_id'         VALUE owner_id,
    'current_version'  VALUE current_version,
    'remote_id'        VALUE remote_id,
    'section_id'       VALUE section_id
    ABSENT ON NULL
) FROM ezcontentobject WHERE status = 1;
SPOOL OFF
```

`ABSENT ON NULL` lässt null-Felder weg und entspricht damit den Gepflogenheiten von MongoDB.

#### Möglichkeit B — Python `oracledb` (Thin-Modus, kein Oracle Client nötig)

```python
#!/usr/bin/env python3
"""
oracle_to_ndjson.py
pip install oracledb
"""
import argparse, json, os, oracledb

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--user', required=True)
    ap.add_argument('--password', required=True)
    ap.add_argument('--dsn', required=True, help='e.g. localhost:1521/XEPDB1')
    ap.add_argument('--outdir', default='./json_export')
    ap.add_argument('--tables', nargs='*')
    args = ap.parse_args()

    os.makedirs(args.outdir, exist_ok=True)
    conn = oracledb.connect(user=args.user, password=args.password, dsn=args.dsn)
    cur = conn.cursor()

    if args.tables:
        tables = args.tables
    else:
        cur.execute("SELECT table_name FROM user_tables ORDER BY table_name")
        tables = [r[0].lower() for r in cur]

    PK_MAP = {'ezcontentobject': 'id', 'ezcontentobject_tree': 'node_id',
              'ezcontentclass': 'id', 'ezsection': 'id', 'ezurlalias_ml': 'id'}

    for table in tables:
        pk = PK_MAP.get(table, 'id')
        cur.execute(f'SELECT * FROM "{table.upper()}"')
        cols = [d[0].lower() for d in cur.description]
        path = os.path.join(args.outdir, f"{table}.ndjson")
        count = 0
        with open(path, 'w') as f:
            for row in cur:
                doc = {cols[i]: row[i] for i in range(len(cols)) if row[i] is not None}
                if pk and pk in doc:
                    doc['_id'] = int(doc[pk])
                f.write(json.dumps(doc, default=str) + '\n')
                count += 1
        print(f"  {table}: {count} rows → {path}")

    cur.close()
    conn.close()

if __name__ == '__main__':
    main()
```


### 18.6 Allgemeines Python — jede Datenbank über SQLAlchemy

Wenn es für Ihre Datenbank einen SQLAlchemy-Dialekt gibt (das gilt für MySQL, MariaDB, PostgreSQL, SQLite, Oracle, MSSQL, Firebird usw.), deckt dieses eine Skript alle ab:

```python
#!/usr/bin/env python3
"""
sqlalchemy_to_ndjson.py — universal RDBMS → NDJSON exporter via SQLAlchemy.

pip install sqlalchemy
# Plus the dialect driver, e.g.:
#   pip install mysqlclient          # MySQL / MariaDB
#   pip install psycopg2-binary      # PostgreSQL
#   pip install cx_Oracle            # Oracle (requires Oracle Client)
#   pip install oracledb             # Oracle thin mode (no Client)
#   pip install pyodbc               # MSSQL

Examples:
  python3 sqlalchemy_to_ndjson.py mysql+mysqldb://user:pass@host/exp ./out
  python3 sqlalchemy_to_ndjson.py postgresql://user:pass@host/exp ./out
  python3 sqlalchemy_to_ndjson.py sqlite:///ez.db ./out
  python3 sqlalchemy_to_ndjson.py oracle+oracledb://user:pass@host:1521/?service_name=XE ./out
"""
import sys, json, os, decimal, datetime
from sqlalchemy import create_engine, inspect, text

PK_MAP = {
    'ezcontentobject':          'id',
    'ezcontentobject_tree':     'node_id',
    'ezcontentobject_attribute':'id',
    'ezcontentclass':           'id',
    'ezcontentclass_attribute': 'id',
    'ezsection':                'id',
    'ezcontentobject_name':     None,
    'ezcontentobject_version':  None,
    'ezurlalias_ml':            'id',
    'ezuser':                   'contentobject_id',
}

def serialize(v):
    if v is None:
        return None
    if isinstance(v, decimal.Decimal):
        return float(v)
    if isinstance(v, datetime.datetime):
        return int(v.timestamp())
    if isinstance(v, datetime.date):
        return int(datetime.datetime(v.year, v.month, v.day).timestamp())
    if isinstance(v, (bytes, bytearray)):
        return v.decode('utf-8', errors='replace')
    if isinstance(v, memoryview):
        return bytes(v).decode('utf-8', errors='replace')
    return v

def main(url, outdir, tables=None):
    os.makedirs(outdir, exist_ok=True)
    engine = create_engine(url)
    insp = inspect(engine)

    if not tables:
        tables = insp.get_table_names()

    with engine.connect() as conn:
        for table in sorted(tables):
            pk = PK_MAP.get(table, 'id')
            path = os.path.join(outdir, f"{table}.ndjson")
            result = conn.execute(text(f'SELECT * FROM "{table}"'))
            cols = list(result.keys())
            count = 0
            with open(path, 'w') as f:
                for row in result:
                    doc = {}
                    for i, col in enumerate(cols):
                        v = serialize(row[i])
                        if v is not None:
                            doc[col] = v
                    if pk and pk in doc:
                        doc['_id'] = int(doc[pk])
                    f.write(json.dumps(doc) + '\n')
                    count += 1
            print(f"  {table}: {count} rows → {path}")

if __name__ == '__main__':
    if len(sys.argv) < 3:
        print(f"Usage: {sys.argv[0]} <sqlalchemy-url> <outdir> [table1 table2 ...]")
        sys.exit(1)
    main(sys.argv[1], sys.argv[2], sys.argv[3:] or None)
```


### 18.7 Prüfung und Nachbearbeitung nach dem Export

Prüfen und beheben Sie vor dem Import die häufigsten Probleme:

```bash
#!/usr/bin/env bash
# validate_ndjson.sh — check each .ndjson file is valid JSON

for f in ./json_export/*.ndjson; do
    errors=$(python3 -c "
import sys, json
bad = 0
for i, line in enumerate(open('$f'), 1):
    line = line.strip()
    if not line:
        continue
    try:
        json.loads(line)
    except Exception as e:
        print(f'  Line {i}: {e}')
        bad += 1
print(f'{bad} error(s)' if bad else 'OK')
")
    echo "$f: $errors"
done
```

Häufig nötige Nachbearbeitungen:

```python
#!/usr/bin/env python3
"""
fixup_ndjson.py — apply common transformations before MongoDB import.

What this does:
  1. Ensures _id is an integer (not a string) for all eZ tables with numeric PKs
  2. Converts 'NULL' string values to omitted fields
  3. Ensures serialized_name_list / serialized_description_list are strings
  4. Strips microseconds from datetime fields (eZ uses Unix int timestamps)
"""
import sys, json, os

INT_FIELDS = {
    'id', 'contentobject_id', 'contentclass_id', 'node_id', 'parent_node_id',
    'main_node_id', 'owner_id', 'version', 'status', 'language_mask',
    'language_id', 'published', 'modified', 'created', 'section_id',
    'current_version', 'creator_id', 'modifier_id', 'depth', 'sort_field',
    'sort_order', 'priority', 'is_hidden', 'is_invisible', 'path_identification_string'
}

def fixup(doc):
    out = {}
    for k, v in doc.items():
        if v == 'NULL' or v is None:
            continue
        if k in INT_FIELDS:
            try:
                v = int(float(str(v)))
            except (ValueError, TypeError):
                pass
        out[k] = v
    return out

def process(inpath, outpath):
    count = 0
    with open(inpath) as fin, open(outpath, 'w') as fout:
        for line in fin:
            line = line.strip()
            if not line:
                continue
            doc = fixup(json.loads(line))
            fout.write(json.dumps(doc) + '\n')
            count += 1
    return count

indir = sys.argv[1] if len(sys.argv) > 1 else './json_export'
outdir = sys.argv[2] if len(sys.argv) > 2 else './json_fixed'
os.makedirs(outdir, exist_ok=True)
for fname in sorted(os.listdir(indir)):
    if fname.endswith('.ndjson'):
        n = process(os.path.join(indir, fname), os.path.join(outdir, fname))
        print(f"  {fname}: {n} docs")
```


### 18.8 Import in MongoDB (`mongoimport`)

`mongoimport` gehört zum Paket MongoDB Database Tools. Es liest NDJSON von sich aus.

#### Eine einzelne Tabelle importieren

```bash
mongoimport \
  --uri "mongodb://db:YOUR_PASSWORD@localhost:27017/exp" \
  --db exp \
  --collection ezcontentobject \
  --file ./json_export/ezcontentobject.ndjson \
  --type json \
  --jsonArray=false \
  --mode upsert \
  --upsertFields _id
```

Wichtige Optionen:
- `--mode upsert` — fügt neue Dokumente ein und aktualisiert vorhandene anhand von `_id`; kann gefahrlos erneut ausgeführt werden
- `--upsertFields _id` — verwendet `_id` als Vergleichsschlüssel
- `--jsonArray=false` — NDJSON (ein JSON-Objekt pro Zeile); verwenden Sie `--jsonArray` nur, wenn Ihre Datei ein JSON-Array ist

#### Alle Tabellen in der Reihenfolge ihrer Abhängigkeiten importieren

```bash
#!/usr/bin/env bash
# import_all.sh — import all NDJSON files into MongoDB in correct dependency order.

URI="mongodb://db:YOUR_PASSWORD@localhost:27017/exp"
DB="exp"
INDIR="${1:-./json_fixed}"

# Ordered list — dependencies first
TABLES=(
    ezcontentlanguage
    ezsection
    ezcontentclass
    ezcontentclass_attribute
    ezcontentclass_classgroup
    ezcontentclass_name
    ezcontentobject
    ezcontentobject_version
    ezcontentobject_attribute
    ezcontentobject_tree
    ezcontentobject_name
    ezcontentobject_link
    ezurlalias_ml
    ezurlalias_ml_incr
    ezurlalias
    eznode_assignment
    ezuser
    ezuser_setting
    ezrole
    ezpolicy
    ezpolicy_limitation
    ezpolicy_limitation_value
    ezuser_role
    ezsubtree_limitation_item
    ezcontentobject_state
    ezcontentobject_state_group
    ezcontentobject_state_link
    ezcontentbrowsebookmark
    ezcontentcache_list
    ezpersistentcookie
)

for TABLE in "${TABLES[@]}"; do
    FILE="$INDIR/$TABLE.ndjson"
    if [[ ! -f "$FILE" ]]; then
        echo "  SKIP $TABLE (no file)"
        continue
    fi
    echo "  Importing $TABLE ..."
    mongoimport \
        --uri "$URI" \
        --db "$DB" \
        --collection "$TABLE" \
        --file "$FILE" \
        --type json \
        --mode upsert \
        --upsertFields _id \
        2>&1 | tail -1
done

# Import any remaining files not in the ordered list
for FILE in "$INDIR"/*.ndjson; do
    TABLE=$(basename "$FILE" .ndjson)
    # Skip if already imported
    if printf '%s\n' "${TABLES[@]}" | grep -qx "$TABLE"; then
        continue
    fi
    echo "  Importing (remaining) $TABLE ..."
    mongoimport \
        --uri "$URI" \
        --db "$DB" \
        --collection "$TABLE" \
        --file "$FILE" \
        --type json \
        --mode upsert \
        --upsertFields _id \
        2>&1 | tail -1
done

echo "Done."
```


### 18.9 Nach dem Import: Indizes anlegen

Legen Sie nach dem Import die Indizes an, von denen der Adapter sevenx_mongodb abhängt. Fehlende Indizes führen zu vollständigen Durchläufen der Collections; manche Abfragen werden bei jeder nennenswerten Datenmenge extrem langsam.

```javascript
// run in: mongosh "mongodb://db:YOUR_PASSWORD@localhost:27017/exp"

// Content object lookups
db.ezcontentobject.createIndex({ id: 1 }, { unique: true });
db.ezcontentobject.createIndex({ contentclass_id: 1 });
db.ezcontentobject.createIndex({ section_id: 1 });
db.ezcontentobject.createIndex({ status: 1 });
db.ezcontentobject.createIndex({ remote_id: 1 }, { unique: true });
db.ezcontentobject.createIndex({ owner_id: 1 });

// Tree / node navigation
db.ezcontentobject_tree.createIndex({ node_id: 1 }, { unique: true });
db.ezcontentobject_tree.createIndex({ contentobject_id: 1 });
db.ezcontentobject_tree.createIndex({ parent_node_id: 1 });
db.ezcontentobject_tree.createIndex({ main_node_id: 1 });
db.ezcontentobject_tree.createIndex({ path_string: 1 });
db.ezcontentobject_tree.createIndex({ depth: 1, sort_field: 1, sort_order: 1 });
db.ezcontentobject_tree.createIndex({ contentobject_id: 1, is_main: 1 });

// Object attributes
db.ezcontentobject_attribute.createIndex({ contentobject_id: 1, version: 1 });
db.ezcontentobject_attribute.createIndex({ contentclassattribute_id: 1 });
db.ezcontentobject_attribute.createIndex({ language_code: 1 });

// Versions
db.ezcontentobject_version.createIndex({ contentobject_id: 1, version: 1 }, { unique: true });
db.ezcontentobject_version.createIndex({ status: 1 });
db.ezcontentobject_version.createIndex({ creator_id: 1 });

// Names
db.ezcontentobject_name.createIndex({ contentobject_id: 1, content_version: 1, content_translation: 1 });

// Content classes
db.ezcontentclass.createIndex({ identifier: 1 });
db.ezcontentclass.createIndex({ version: 1 });
db.ezcontentclass_attribute.createIndex({ contentclass_id: 1, version: 1 });
db.ezcontentclass_attribute.createIndex({ identifier: 1 });

// URL aliases — critical for navigation
db.ezurlalias_ml.createIndex({ parent: 1, text_md5: 1 }, { unique: true });
db.ezurlalias_ml.createIndex({ action: 1, is_original: 1, is_alias: 1 });
db.ezurlalias_ml.createIndex({ id: 1 }, { unique: true });
db.ezurlalias_ml.createIndex({ link: 1 });

// User / auth
db.ezuser.createIndex({ login: 1 }, { unique: true });
db.ezuser.createIndex({ email: 1 });
db.ezuser.createIndex({ contentobject_id: 1 }, { unique: true });

// Roles and policies
db.ezrole.createIndex({ name: 1 });
db.ezpolicy.createIndex({ role_id: 1 });
db.ezpolicy_limitation.createIndex({ policy_id: 1 });
db.ezuser_role.createIndex({ contentobject_id: 1 });

// States
db.ezcontentobject_state_group.createIndex({ identifier: 1 });
db.ezcontentobject_state.createIndex({ group_id: 1, identifier: 1 });
db.ezcontentobject_state_link.createIndex({ contentobject_id: 1 });

// Sections
db.ezsection.createIndex({ identifier: 1 });

// Info collector
db.ezinfocollection.createIndex({ contentobject_id: 1 });

// Search
db.ezsearch_object_word_link.createIndex({ word_id: 1 });
db.ezsearch_object_word_link.createIndex({ contentobject_id: 1 });
db.ezsearch_word.createIndex({ word: 1 }, { unique: true });

// Language
db.ezcontentlanguage.createIndex({ locale: 1 }, { unique: true });
db.ezcontentlanguage.createIndex({ language_mask: 1 });
```


### 18.10 Den Import prüfen

Führen Sie nach dem Import diese kurzen Prüfungen in `mongosh` aus:

```javascript
// Check document counts for key collections
const cols = [
    'ezcontentobject', 'ezcontentobject_tree', 'ezcontentobject_attribute',
    'ezcontentobject_version', 'ezcontentobject_name', 'ezcontentclass',
    'ezurlalias_ml', 'ezuser', 'ezrole', 'ezsection', 'ezcontentlanguage'
];
cols.forEach(c => print(c + ': ' + db[c].countDocuments()));

// Spot-check: does _id match the 'id' field for ezcontentobject?
const sample = db.ezcontentobject.findOne({ status: 1 });
print('_id:', sample._id, ' id:', sample.id, ' match:', sample._id == sample.id);

// Check the main tree root node exists
const root = db.ezcontentobject_tree.findOne({ node_id: 1 });
print('Root node:', root ? 'OK' : 'MISSING');

// Check admin user
const admin = db.ezuser.findOne({ login: 'admin' });
print('Admin user:', admin ? admin.login + ' / object ' + admin.contentobject_id : 'MISSING');
```


### 18.11 Vollständiges Beispiel für dieses Projekt von Anfang bis Ende (MySQL → MongoDB)

Die Produktionsmigration für `admin.example.com` erfolgte aus der ursprünglichen MySQL-Datenbank `exp`. Die genauen Befehle:

```bash
SCRIPTS=extension/sevenx_mongodb/bin/mongodb

# 1. Export from MySQL to NDJSON
export MYSQL_PASSWORD="$(cat /path/to/mysql-secret)"   # required; no default (or the script asks in a terminal)
bash $SCRIPTS/export_mysql.sh ./json_export
# (or directly: python3 $SCRIPTS/mysql2ndjson.py --user db --db exp --outdir ./json_export, password from MYSQL_PASSWORD)

# 2. Fix up types (int fields, NULL strings)
python3 $SCRIPTS/fixup_ndjson.py ./json_export ./json_fixed

# 3. Validate
bash $SCRIPTS/validate_ndjson.sh ./json_fixed

# 4. Import
bash $SCRIPTS/import_all.sh ./json_fixed

# 5. Create indexes
mongosh "mongodb://db:YOUR_PASSWORD@localhost:27017/exp" --file $SCRIPTS/create_indexes.js

# 6. Touch opcache and test the admin
touch /path/to/exponential/index.php
curl -s -o /dev/null -w "%{http_code}" https://admin.example.com/user/login
```


### 18.12 Häufige Probleme beim Import beheben

| Symptom | Ursache | Lösung |
|---------|-------|-----|
| Fehler wegen doppelten Schlüssels `_id` beim Import | Zwei Zeilen hatten denselben Wert des Primärschlüssels (ein Problem der Datenqualität) | Verwenden Sie `--mode upsert` statt `--mode insert` |
| Das Template wird leer dargestellt / Name `NULL` | `ezcontentobject_name` wurde mit falschem Feld `content_version` importiert (Zeichenkette statt int) | Führen Sie `extension/sevenx_mongodb/bin/mongodb/fixup_ndjson.py` aus, um die int-Felder umzuwandeln |
| `/content/view/full/2` liefert 404 | Der Wurzelknoten von `ezcontentobject_tree` wurde nicht importiert | Prüfen Sie `db.ezcontentobject_tree.findOne({node_id:1})` |
| Anmeldung schlägt fehl | `ezuser` oder `ezuser_setting` fehlt; oder `password_hash` wurde beim Export abgeschnitten | Prüfen Sie `db.ezuser.findOne({login:'admin'})` und die Länge von `password_hash` |
| URL-Aliase defekt / alles 404 | `ezurlalias_ml` nicht importiert oder der Index auf `parent`+`text_md5` fehlt | Importieren Sie die Collection erneut; führen Sie `extension/sevenx_mongodb/bin/mongodb/create_indexes.js` aus |
| PHP meldet `MONGO TODO arrayQuery` | Eine Kernel-Methode hat noch einen nicht portierten SQL-Pfad | Die Datei steht im Backlog der offenen Punkte (siehe Abschnitt 11) |
| Langsame Seiten nach dem Import | Indizes nicht angelegt | Führen Sie `extension/sevenx_mongodb/bin/mongodb/create_indexes.js` in `mongosh` aus |
| `mongoimport: command not found` | MongoDB Database Tools sind nicht getrennt von `mongod` installiert | `apt install mongodb-database-tools` oder von mongodb.com/try/download/database-tools herunterladen |



## 19. Projekt abgeschlossen — Liste aller gepatchten oder geänderten Dateien

### Zweck

Dieser Abschnitt ist die maßgebliche Aufstellung jeder Datei, die im Rahmen der MongoDB-Portierung gepatcht oder geändert wurde. Er dient als Referenz, wenn Sie die Git-Historie vergleichen und die Vollständigkeit der Portierung prüfen. Jeder Eintrag nennt den Dateipfad (relativ zum Wurzelverzeichnis des Projekts), was sich geändert hat und welche Methode des MongoDB-Adapters eingeführt oder ersetzt wurde.


### 19.1 MongoDB-Adapter (Kern)

| Datei | Zusammenfassung der Änderung |
|------|---------------|
| `extension/sevenx_mongodb/classes/expMongoDB.php` | **Von Grund auf neu erstellt.** Kapselt `MongoDB\Client`. Setzt `query()` (für Schreibvorgänge stillschweigend ohne Wirkung), `arrayQuery()` (protokolliert MONGO TODO + gibt `[]` zurück), `aggregate()`, `insert()`, `upsert()`, `deleteWhere()`, `nextSeqID()`, `databaseName()`, `escapeString()`, `begin()`/`commit()`/`rollback()` (ohne Wirkung), `lock()`/`unlock()` (ohne Wirkung) sowie Hilfen für bitOr/bitAnd um. |
| `var/autoload/ezp_override.php` | **Geändert.** Die Zuordnungen der Klassen-Overrides für alle gepatchten Kernel-Klassen ergänzt (siehe die Abschnitte 19.2 und 19.3 unten). Registriert außerdem das Override von `eZContentOperationCollection` für nxc_powercontent. |


### 19.2 Override-Klassen der Erweiterung — `extension/sevenx_mongodb/classes/kernel/`

Diese Dateien verdecken die gleichnamigen Kernel-Klassen. Jede Datei folgt demselben Muster: Vor dem SQL-Aufruf `arrayQuery()` wird ein MongoDB-Zweig (`if ( $db->databaseName() === 'mongo' ) { … }`) eingefügt, und der SQL-Pfad bleibt im `else`-Zweig unberührt.

| Datei | Methoden mit ergänzten MongoDB-Zweigen |
|------|-------------------------------------|
| `ezcontentobject.php` | `names()`, `fetchByRemoteID()`, `className()`, `canCreateClassList()`, `copy()` (Muster: klonen mit null-ID), `relatedObjects()`, `nextVersion()`, `previousVersion()`, `getVersionCount()` und viele weitere Nachschlagevorgänge über Aggregationen |
| `ezcontentobjectversion.php` | `translationList()`, `fetchAttributes()` (vollständiger Ersatz des Joins durch eine dreistufige Aggregation + Zusammenführen), `cloneVersion()` |
| `ezcontentobjecttreenode.php` | `subTreeCountByNodeID()`, `addChildTo()`, `getClassesJsArray()`, `findNode()`, `getAvailableClassesList()` |
| `ezcontentclass.php` | Hilfsmethoden `fetchList()`, `fetchAllClasses()`, `nameFromSerializedString()` |
| `ezcontentclassattribute.php` | `fetchByClassID()`, `fetchByIdentifier()` |
| `eznodeassignment.php` | `fetchForObject()`, `fetchByNode()` |
| `ezpreferences.php` | `value()` (Aggregation auf `ezpreferences`), `setValue()` (Upsert), `removeForUser()` |
| `ezsection.php` | `fetchList()`, `fetchByIdentifier()`, `sectionCount()` |
| `ezrole.php` | `fetchByUser()`, `fetchRolesByLimitation()`, `fetchUserByRole()` |
| `ezsearchlog.php` | `addPhrase()` (frühe Rückkehr — das SQL-Protokoll der Suchbegriffe wird im MongoDB-Modus nicht verwendet), `mostFrequentPhraseArray()` (Aggregation auf `ezsearch_search_phrase`) |
| `ezurlaliasml.php` | Aufbau der Weiterleitungspfade (zwei Stubs, ~Zeile 2120, 2155), Stubs für das Nachschlagen mit `translateByAction()` / `fetchByAction()` |
| `ezpackage.php` | `setInstalled()`, `getInstallState()` (zählende Aggregation auf `ezpackage`) |
| `ezinformationcollection.php` | `fetchCollectionAttributeList()`, `fetchCollectionList()` |
| `ezkeyword.php` (Datentypen) | `store()` — vollständiger MongoDB-Ablauf aus Upsert/Löschen für die Tabellen der Schlüsselwörter und Verknüpfungen; ersetzt 4 SQL-Aufrufe von arrayQuery |
| `ezurlaliasquery.php` | Stubs für das Nachschlagen bei der Pfadübersetzung |


### 19.3 Override von nxc_powercontent

| Datei | Zusammenfassung der Änderung |
|------|---------------|
| `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php` | `setVersionStatus()` — einen MongoDB-Zweig mit `$db->upsert()` auf `ezcontentobject_version` ergänzt; `loopNodeAssignment()` — ein MongoDB-Upsert für `ezcontentobject_tree` und `eznodeassignment` ergänzt. Diese Datei sorgt dafür, dass **Kopiervorgänge** tatsächlich Baumknoten anlegen. |


### 19.4 Direkt gepatchte Kernel-Dateien (kein Override, an Ort und Stelle bearbeitet)

| Datei | Zusammenfassung der Änderung |
|------|---------------|
| `kernel/search/plugins/ezsearchengine/ezsearchengine.php` | `buildWordIDArray()` — frühe Rückkehr für MongoDB (der Indizierungspfad wird nicht verwendet). `prepareWordIDArrays()` — frühe Rückkehr mit leeren Arrays. `prepareWordIDArraysForPattern()` — frühe Rückkehr mit leeren Arrays. `fetchTotalObjectCount()` — MongoDB-Aggregation mit `$count` auf `ezcontentobject`. `search()` — **vollständige MongoDB-Umsetzung**: Regex auf `ezcontentobject.name` + `ezcontentobject_attribute.data_text`, lädt die passenden Objekte `eZContentObjectTreeNode` und gibt ein korrektes Array `SearchResult`/`SearchCount`/`StopWordArray` zurück. |
| `kernel/search/stats.php` | `$searchListCount` — MongoDB-Zweig mit `$count`-Aggregation ergänzt. |
| `lib/ezdb/classes/ezpersistentobject.php` | `storeObject()` — einen MongoDB-Zweig ergänzt: setzt vor dem Einfügen `id = null`, damit `nextSeqID()` den neuen Primärschlüssel vergibt; verwendet je nach Fall `$db->insert()` oder `$db->upsert()`. |
| `kernel/content/ezcontentoperationcollection.php` | `setVersionStatus()` — der Semikolonfehler (`if ( !$existingNode );`) wurde festgestellt, wird aber durch das Override von nxc_powercontent verdeckt. Keine Änderung vorgenommen (das Override hat Vorrang). |


### 19.5 Datenschicht — Importskripte (`extension/sevenx_mongodb/bin/mongodb/`)

Alle Migrationsskripte der Datenschicht liegen unter `extension/sevenx_mongodb/bin/mongodb/`. Führen Sie sie aus dem Docroot aus oder übergeben Sie absolute Pfade.

| Datei | Zweck |
|------|---------|
| `export_mysql.sh` | Shell-Wrapper — ruft `mysql2ndjson.py` mit den Zugangsdaten aus der Umgebung auf (`MYSQL_PASSWORD` ist Pflicht und hat keinen Standardwert; im Terminal wird danach gefragt); schreibt NDJSON nach `./json_export/` |
| `mysql2ndjson.py` | Python-Skript — exportiert alle Exponential-Tabellen aus MySQL/MariaDB nach NDJSON; wandelt int/float um, lässt NULL-Felder weg, setzt `_id` aus der Spalte des Primärschlüssels |
| `fixup_ndjson.py` | Python-Skript — bearbeitet NDJSON nach, um Probleme mit Datentypen zu beheben (z. B. Zeichenkette `content_version` → int, Zeichenketten `'NULL'` → weggelassen) |
| `validate_ndjson.sh` | Shell-Skript — prüft jede Datei `.ndjson` in einem Verzeichnis; endet mit 1, wenn eine Zeile kein gültiges JSON ist |
| `import_all.sh` | Shell-Skript — ruft `mongoimport` für jede Collection in der Reihenfolge der Abhängigkeiten auf und importiert danach alle übrigen Dateien, die nicht in der Liste stehen |
| `create_indexes.js` | mongosh-Skript — legt alle MongoDB-Indizes an, von denen der Adapter sevenx_mongodb abhängt (einschließlich des entscheidenden zusammengesetzten Index `parent`+`text_md5` auf `ezurlalias_ml`) |

**Kurzanleitung:**
```bash
cd /path/to/exponential
SCRIPTS=extension/sevenx_mongodb/bin/mongodb

MYSQL_PASSWORD="$(cat /path/to/mysql-secret)" bash $SCRIPTS/export_mysql.sh ./json_export
python3 $SCRIPTS/fixup_ndjson.py         ./json_export ./json_fixed
bash  $SCRIPTS/validate_ndjson.sh        ./json_fixed
bash  $SCRIPTS/import_all.sh             ./json_fixed
mongosh "mongodb://db:YOUR_PASSWORD@localhost:27017/exp" --file $SCRIPTS/create_indexes.js
```


### 19.6 Dokumentationsdateien

| Datei | Zweck |
|------|---------|
| `extension/sevenx_mongodb/MONGODB_KERNEL_SUPPORT_EXPANSION.md` | Diese Datei — fortlaufendes Protokoll aller Arbeiten an der MongoDB-Portierung, der Architekturentscheidungen, der Schritte zur Fehlersuche und der maßgeblichen Liste der Dateiänderungen (dieser Abschnitt). |
| `MONGODB-BUGS.md` | Fehlerliste für Probleme der MongoDB-Portierung mit über 7 Einträgen zu Sonderfällen, die beim Testen gefunden wurden. |


### 19.7 Wichtige Architekturentscheidungen

1. **Override-Muster statt Unterklassen**: Das Klassenverzeichnis in `var/autoload/ezp_override.php` ordnet die ursprünglichen Klassennamen den Dateien der Erweiterung zu. Die Dateien der Erweiterung definieren also Klassen mit *demselben Namen* wie die Originale und werden stattdessen geladen. Keine Vererbung — ein sauberer Ersatz.

2. **`$db->databaseName() === 'mongo'` als Weichenprüfung**: Alle MongoDB-Zweige beginnen mit dieser Prüfung. Der Adapter expMongoDB gibt aus `databaseName()` `'mongo'` (in Kleinbuchstaben) zurück. Das ist eine verlässliche Weiche mit einer einzigen Quelle der Wahrheit.

3. **`query()` bewirkt bei Schreibvorgängen nichts**: Alle Aufrufe `$db->query("INSERT…")` / `$db->query("UPDATE…")` / `$db->query("DELETE…")` ignoriert der Adapter stillschweigend. Nur ausdrückliche Aufrufe `$db->insert()`, `$db->upsert()` und `$db->deleteWhere()` schreiben tatsächlich.

4. **`arrayQuery()` protokolliert und gibt `[]` zurück**: Jeder nicht gepatchte SQL-Lesepfad, der `$db->arrayQuery()` aufruft, protokolliert eine Warnung MONGO TODO und gibt ein leeres Array zurück. So lassen sich die verbleibenden Stubs leicht über das Fehlerprotokoll finden.

5. **Volltextsuche über Regex**: Die eingebaute Suchmaschine ezsearch wurde so portiert, dass sie Regex in MongoDB auf `ezcontentobject.name` + `ezcontentobject_attribute.data_text` verwendet statt der Wortindex-Tabellen von ezsearch (die im MongoDB-Modus nicht gefüllt werden). Die Ergebnisse werden als Objekte `eZContentObjectTreeNode` zurückgegeben, die dem erwarteten Vertrag entsprechen.

6. **Fortlaufende IDs über `nextSeqID()`**: Der Adapter setzt `nextSeqID($table, $column)` als `MAX($column) + 1` mit einer MongoDB-Aggregation um. Das bildet die Semantik von AUTO_INCREMENT in MySQL nach, ohne sich auf die ObjectId von MongoDB zu stützen.

7. **Kopiervorgänge**: Das Kopieren eines Objekts funktioniert, indem vor dem Speichern `id = null` am geklonten `eZContentObject` gesetzt wird — `storeObject()` erkennt die null-ID und ruft `nextSeqID()` auf, um einen neuen Primärschlüssel zu vergeben. Das Override von `eZContentOperationCollection` in nxc_powercontent legt den Baumknoten und die Knotenzuweisung in MongoDB an.


### 19.8 Checkliste für Tests

| Funktion | Status |
|---------|--------|
| Anmeldung in der Administration | ✅ Funktioniert |
| Inhalte durchsuchen / Navigation im Baum | ✅ Funktioniert |
| Inhaltsansicht (full / line / block) | ✅ Funktioniert |
| Inhalte bearbeiten (Entwurf speichern, veröffentlichen) | ✅ Funktioniert |
| Objekt kopieren (einzeln + Teilbaum) | ✅ Funktioniert — die Baumknoten werden richtig angelegt |
| Suche (Stichwort) | ✅ Funktioniert — die Regex-Suche in MongoDB liefert echte Ergebnisse |
| URL-Aliase | ✅ Funktioniert |
| Datentyp Schlüsselwort (speichern/laden) | ✅ Funktioniert |
| Benutzereinstellungen | ✅ Funktioniert |
| Sektionsliste | ✅ Funktioniert |
| Installationsstatus der Pakete | ✅ Funktioniert |
| Seite der Suchstatistik | ✅ Funktioniert |
| Verwandte Objekte | ✅ Funktioniert |
| Liste der Inhaltsklassen | ✅ Funktioniert |
| Prüfung von Rollen und Berechtigungen | ✅ Funktioniert |
| Protokoll MONGO TODO arrayQuery | ✅ **Sauber** — bei normaler Nutzung von Administration und Website keine ungepatchten Aufrufe von `arrayQuery` |



## 20. Schritte zur vollständigen Neuimplementierung im Kernel (KEINE Kernel-Override-Erweiterung mehr)

### 20.1 Überblick und Beweggründe

Die derzeitige MongoDB-Portierung verwendet `extension/sevenx_mongodb/` als System verdeckender Overrides.
`var/autoload/ezp_override.php` ordnet jeden Klassennamen des Kernels einer parallelen Datei in der Erweiterung zu.
Dieser Ansatz hat sich für die schrittweise Entwicklung bewährt, bringt aber strukturelle Kosten mit sich, die mit der Zeit wachsen:

- **Pflege doppelter Dateien**: Jede gepatchte Klasse existiert an zwei Stellen — das Original im Kernel und das
  Override in der Erweiterung. Jede Fehlerkorrektur und jeder Upstream-Merge muss auf die Datei der Erweiterung angewendet werden, nicht auf das
  Original. Die Originaldatei wird zu totem Code, der Entwickler stillschweigend in die Irre führt.
- **Komplexität durch zwei Docroots**: Die Dateien unter `kernel/` sind zwischen dem Edit-Docroot und dem Docroot der Website
  getrennte physische Kopien. Die Dateien der Erweiterung sind verlinkt (eine Änderung trifft beide Websites). Kernel-Patches
  müssen von Hand getrennt auf beide Kopien angewendet werden. Änderungen an der Erweiterung wirken automatisch.
- **Langfristige Gefahr des Auseinanderlaufens**: Wird die Upstream-Codebasis von Exponential jemals aktualisiert, veralten die Override-Dateien
  stillschweigend — die ursprüngliche Kernel-Datei ändert sich, aber das Override verdeckt das dauerhaft.
- **Kognitiver Aufwand**: „Ist diese Methode gepatcht?“ erfordert einen Blick in `var/autoload/ezp_override.php`,
  danach in das Verzeichnis der Erweiterung und danach in die Kernel-Datei. Es gibt keine einzige Quelle der Wahrheit.
- **Verwirrung in der IDE**: Werkzeuge zur statischen Analyse, IDEs und `grep` finden zuerst die ursprüngliche Kernel-Klasse.
  Dass Entwickler die falsche Datei umbauen, ist in dieser Architektur ein häufiger Fehler.

Der saubere Zielzustand lautet: **Alle MongoDB-Zweige stehen direkt in den Dateien unter `kernel/` und `lib/`**.
Die Erweiterung behält nur den Adapter (`expMongoDB.php`) und seine INI-Konfiguration. Keine Klassen-Overrides.


### 20.2 Was sich ändert und was bleibt

**Bleibt in der Erweiterung (`extension/sevenx_mongodb/`):**

| Element | Warum es bleibt |
|---|---|
| `classes/expMongoDB.php` | Das ist eine neue Klasse (der Adapter), kein Override einer vorhandenen Klasse. Der Autoload-Eintrag, der sie registriert, ist berechtigt. |
| `settings/` — INI-Konfiguration, Registrierung des Datenbank-Handlers | Konfiguration, kein Code. |
| `autoloads/expMongoDBinfo.php` | Autoload-Eintrag der Erweiterung für den Adapter selbst. |
| Skripte zur Datenmigration (`sevenx_mongodb/*.py`, `*.sh`, `*.js`) | Keine Overrides von PHP-Klassen — eigenständige Werkzeuge. |

**Wandert in den Kernel (zusammengeführt und aus der Erweiterung gelöscht):**

Jede Datei unter `extension/sevenx_mongodb/classes/kernel/` — ~120 Dateien in `kernel/classes/`,
`kernel/datatypes/`, `kernel/notification/`, `kernel/workflowtypes/` usw. Die vollständige Aufstellung steht in §20.5.

**Nach dem Zusammenführen enthält `var/autoload/ezp_override.php` nur noch:**

```php
// expMongoDB adapter — new class, not an override
'expMongoDB' => 'extension/sevenx_mongodb/classes/expMongoDB.php',
```

Alle anderen Einträge (die ~120 Overrides von Kernel-Klassen) werden entfernt.

**Bereits erledigte Patches nur im Kernel (es gibt kein Override in der Erweiterung, kein Zusammenführen nötig):**

Diese Dateien wurden direkt in `kernel/` und `lib/` gepatcht — sie sind bereits im richtigen
Endzustand:

- `kernel/search/plugins/ezsearchengine/ezsearchengine.php` — vollständige Regex-Suche in MongoDB
- `kernel/search/stats.php` — Zweig mit Aggregation für `$searchListCount`
- `lib/ezdb/classes/ezpersistentobject.php` — `storeObject()`, `removeObject()`, `newObjectOrder()`, `handleRows()`
- `kernel/content/edit.php` — Entfernen der Debug-Protokollierung, Veröffentlichungspipeline
- `kernel/content/attribute_edit.php` — Entfernen der Debug-Protokollierung
- `kernel/class/edit.php` — Abläufe zum Bearbeiten, Kopieren und Anlegen von Klassen
- `kernel/private/classes/ezcontentobjectstategroup.php` — `arrayQuery` → `else`-Zweig
- `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php` — bleibt als Override einer Erweiterung eines Drittanbieters, keine Änderung nötig


### 20.3 Das Zusammenführen (pro Datei)

Führen Sie für jede Override-Datei diese Schritte der Reihe nach aus:

1. **Vergleichen** Sie das Override mit dem Original im Kernel, um alle Ergänzungen zu finden:
   ```bash
   diff \
     extension/sevenx_mongodb/classes/kernel/somefile.php \
     kernel/classes/somefile.php
   ```
   Achten Sie auf: Blöcke `if ( $db->databaseName() === 'mongo' )`, Korrekturen nullbarer Parameter (`?Type`),
   ergänzte Null-Koaleszenz (`?? []`) und Prüfungen mit früher Rückkehr.

2. **Übernehmen** Sie jede MongoDB-spezifische Ergänzung in die Kernel-Datei. Der SQL-`else`-Zweig bleibt
   genau so, wie er war. Nur der MongoDB-Block `if` ist neu.

3. **Prüfen** Sie die Syntax der Kernel-Datei unmittelbar nach dem Bearbeiten:
   ```bash
   /opt/plesk/php/8.5/bin/php -l kernel/classes/somefile.php
   ```
   Gehen Sie nicht zum nächsten Schritt über, wenn die Prüfung fehlschlägt.

4. **Führen Sie touch** auf die Kernel-Datei in **beiden** Docroots aus, um den OPcache zu umgehen:
   ```bash
   BASE=/path/to/exponential
   ALT=/path/to/exponential-alt
   touch "$BASE/kernel/classes/somefile.php" "$ALT/kernel/classes/somefile.php"
   ```
   Hinweis: Dateien unter `lib/` sind in beiden Docroots dieselbe physische Datei, wenn das Verzeichnis `lib/`
   verlinkt ist. Prüfen Sie das mit `ls -li`, bevor Sie touch auf beide Pfade anwenden.

5. **Entfernen** Sie die Override-Datei aus der Erweiterung:
   ```bash
   rm extension/sevenx_mongodb/classes/kernel/somefile.php
   ```

6. **Entfernen** Sie den Klasseneintrag aus `var/autoload/ezp_override.php`:
   ```php
   // Remove the line:
   'eZSomeClass' => 'extension/sevenx_mongodb/classes/kernel/somefile.php',
   ```

7. **Testen** Sie die betroffene Funktion im Browser, bevor Sie mit der nächsten Datei weitermachen.
   Prüfen Sie `error_log` auf neue Einträge MONGO TODO oder PHP-Fehler.

8. **Wiederholen** Sie das für die nächste Datei.


### 20.4 Hinweise zu den zwei Docroots

Die beiden Docroots unterscheiden sich darin, wie die Dateien physisch gespeichert sind:

| Pfadart | Website | Edit-/Admin-Website | Strategie für den Abgleich |
|---|---|---|---|
| `extension/sevenx_mongodb/` | Symlink auf das Edit-Docroot | Physisches Verzeichnis | Das Bearbeiten eines der beiden Pfade wirkt auf beide |
| `kernel/classes/` | Physische Kopie | Getrennte physische Kopie | Nach dem Zusammenführen BEIDE bearbeiten |
| `kernel/datatypes/` | Physische Kopie | Getrennte physische Kopie | Nach dem Zusammenführen BEIDE bearbeiten |
| `kernel/content/*.php` | Physische Kopie | Getrennte physische Kopie | BEIDE bearbeiten |
| `lib/` | Symlink auf das Edit-Docroot (prüfen!) | Physisches Verzeichnis | Wenn verlinkt: eine Bearbeitung; wenn nicht: BEIDE bearbeiten |
| `var/autoload/ezp_override.php` | Physische Kopie | Getrennte physische Kopie | Nach dem Entfernen jeder Klasse BEIDE bearbeiten |

**Den Zustand von `lib/` prüfen:**
```bash
ls -la /path/to/exponential/lib
ls -la /path/to/exponential-alt/lib
```
Ist `lib/` auf der Website ein Symlink, der auf die Edit-Website zeigt, wirkt eine Bearbeitung auf beide.
Ist es eine getrennte physische Kopie, behandeln Sie es wie `kernel/`.


### 20.5 Dateiaufstellung — Plan für das Zusammenführen in Phasen

Führen Sie in dieser Reihenfolge zusammen: das Wichtigste und am besten im Browser Getestete zuerst, Randbereiche zuletzt.

#### Phase 1 — zentrales ORM und Inhalte (größte Wirkung, am besten getestet)

| Override der Erweiterung | Ziel im Kernel | Wichtige MongoDB-Ergänzungen |
|---|---|---|
| `classes/kernel/ezcontentobject.php` | `kernel/classes/ezcontentobject.php` | int-Umwandlung in `fetch()`, `fetchByNodeID()`, Pipeline mit `$lookup` in `relatedObjects()`, `$count` in `relatedObjectCount()`, Pipeline in `contentObjectAttributes()`, gebündeltes Laden in `fillNodeListAttributes()`, `assignedNodes()`, `hasVisibleNode()`, `stateIDArray()`, `stateIdentifierArray()`, int-Umwandlung in `addContentObjectRelation()` |
| `classes/kernel/ezcontentobjectversion.php` | `kernel/classes/ezcontentobjectversion.php` | int-Umwandlung in `fetch()`, kaskadierendes `deleteWhere` in `removeThis()`, `translationList()`, dreistufige Pipeline in `fetchAttributes()`, `cloneVersion()` |
| `classes/kernel/ezcontentobjecttreenode.php` | `kernel/classes/ezcontentobjecttreenode.php` | `subTreeCountByNodeID()`, `addChildTo()`, `getClassesJsArray()`, `findNode()`, `getAvailableClassesList()` |
| `classes/kernel/ezcontentclass.php` | `kernel/classes/ezcontentclass.php` | `fetchList()`, `fetchAllClasses()`, Pipeline mit `$lookup` in `initializeCopy()` |
| `classes/kernel/ezcontentclassattribute.php` | `kernel/classes/ezcontentclassattribute.php` | `fetchByClassID()`, `fetchByIdentifier()` |
| `classes/kernel/eznodeassignment.php` | `kernel/classes/eznodeassignment.php` | `fetchForObject()`, `fetchByNode()` — in der Erweiterung bestätigt bereits gepatcht |

#### Phase 2 — Benutzer, Rolle, Sektion (Pfad der Zugriffskontrolle)

| Override der Erweiterung | Ziel im Kernel | Wichtige MongoDB-Ergänzungen |
|---|---|---|
| `classes/kernel/ezrole.php` | `kernel/classes/ezrole.php` | rekursives `$lookup` in `fetchByUser()`, `fetchRolesByLimitation()`, `fetchUserByRole()`, Prüfung über Aggregation + Einfügen in `assignToUser()`, `deleteWhere` in `removeUserAssignment()`, `fetchIDListByUser()`, `fetchUserID()` |
| `classes/kernel/datatypes/ezuser/ezuser.php` | `kernel/datatypes/ezuser/ezuser.php` | Methoden für Anmeldung, Laden und Einstellungen |
| `classes/kernel/datatypes/ezuser/ezusersetting.php` | `kernel/datatypes/ezuser/ezusersetting.php` | `fetch()`, `fetchByOffset()` |
| `classes/kernel/ezsection.php` | `kernel/classes/ezsection.php` | `fetchList()`, `fetchByIdentifier()`, `sectionCount()` |
| `classes/kernel/ezpreferences.php` | `kernel/classes/ezpreferences.php` | Aggregation in `value()`, Upsert in `setValue()`, `deleteWhere` in `removeForUser()` |
| `classes/kernel/ezpolicylimitation.php` | `kernel/classes/ezpolicylimitation.php` | zweistufige Aggregation in `fetchByPolicy()` |

#### Phase 3 — Suche, URLs, Cache

| Override der Erweiterung | Ziel im Kernel | Wichtige MongoDB-Ergänzungen |
|---|---|---|
| `classes/kernel/ezurlaliasml.php` | `kernel/classes/ezurlaliasml.php` | Aufbau der Weiterleitungspfade, Stubs `translateByAction()`, `fetchByAction()` |
| `classes/kernel/ezurlwildcard.php` | `kernel/classes/ezurlwildcard.php` | `fetchList()`, `createWildcardsIndex()` |
| `classes/kernel/ezsearchlog.php` | `kernel/classes/ezsearchlog.php` | frühe Rückkehr in `addPhrase()`, Aggregation in `mostFrequentPhraseArray()` auf `ezsearch_search_phrase` |
| `classes/kernel/ezcontentcachemanager.php` | `kernel/classes/ezcontentcachemanager.php` | Korrektur eines implizit nullbaren Parameters unter PHP 8.5 |
| `classes/kernel/ezsiteaccess.php` | `kernel/classes/ezsiteaccess.php` | implizit nullbare Parameter unter PHP 8.5 in `change()`, `load()` |
| `classes/kernel/ezpackage.php` | `kernel/classes/ezpackage.php` | `setInstalled()`, zählende Aggregation mit `$count` in `getInstallState()` |

#### Phase 4 — Workflow, Zusammenarbeit, Information Collection

| Override der Erweiterung | Ziel im Kernel | Wichtige MongoDB-Ergänzungen |
|---|---|---|
| `classes/kernel/eztrigger.php` | `kernel/classes/eztrigger.php` | Korrektur mit Null-Koaleszenz `array_keys($workflowProcess->Template['templateVars'] ?? [])` |
| `classes/kernel/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php` | `kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php` | Korrektur durch eine einzige Zuweisung für das magische `__get`/`__set` von `$process->Template` (vorher eine indirekte Änderung einer überladenen Eigenschaft) |
| `classes/kernel/ezorder.php` | `kernel/classes/ezorder.php` | Pipeline mit `$lookup` für die Sortierung nach Benutzernamen in `active()`; Korrekturen nullbarer Parameter unter PHP 8.x |
| `classes/kernel/ezbasket.php` | `kernel/classes/ezbasket.php` | Aggregation der abgelaufenen Sitzungen in `cleanupExpired()`; Durchlauf in `cleanup()`; beide verwenden `deleteWhere` |
| `classes/kernel/ezdiscount.php` | `kernel/classes/ezdiscount.php` | Aggregation mit `$in` für Unterregeln; Aggregation mit skalarem `$match` für Einschränkungswerte |
| `classes/kernel/ezinformationcollection.php` | `kernel/classes/ezinformationcollection.php` | Ersatz aller 4 JOIN-Abfragen: `fetchCountForAttribute()`, `fetchCollectionCountForObject()`, `fetchCountList()`, `informationCollectionAttributes()` |
| `classes/kernel/ezinformationcollectionattribute.php` | `kernel/classes/ezinformationcollectionattribute.php` | einfache Aggregation in `contentClassAttributeName()` |
| `classes/kernel/ezcollaborationitem.php` | `kernel/classes/ezcollaborationitem.php` | Pipeline mit `$lookup` bereits gepatcht; SQL-Zweige nicht erreichbar — prüfen und zusammenführen |
| `classes/kernel/ezcollaborationgroup.php` | `kernel/classes/ezcollaborationgroup.php` | Aggregation mit `$count` für die Anzahl der Elemente einer Gruppe |
| `classes/kernel/ezcollaborationitemgrouplink.php` | `kernel/classes/ezcollaborationitemgrouplink.php` | Auf Zweige mit Aggregationen prüfen |
| `classes/kernel/ezcollaborationitemstatus.php` | `kernel/classes/ezcollaborationitemstatus.php` | Auf Zweige mit Aggregationen prüfen |

#### Phase 5 — Datentypen

| Override der Erweiterung | Ziel im Kernel | Wichtige MongoDB-Ergänzungen |
|---|---|---|
| `classes/kernel/datatypes/ezkeyword/ezkeywordtype.php` | `kernel/datatypes/ezkeyword/ezkeywordtype.php` | zweistufige Aggregation in `deleteStoredObjectAttribute()` (`$group` + `$match cnt=1`); `deleteWhere` auf beide Schlüsselworttabellen |
| `classes/kernel/datatypes/ezxmltext/ezxmltexttype.php` | `kernel/datatypes/ezxmltext/ezxmltexttype.php` | Aufräumen verwaister URLs: Anti-Join-Aggregation mit `$nin`; `deleteWhere` |
| `classes/kernel/datatypes/ezurl/ezurlobjectlink.php` | `kernel/datatypes/ezurl/ezurlobjectlink.php` | Fetch-Methoden |
| Alle anderen Overrides von Datentypen | `kernel/datatypes/<type>/` | Viele sind nur Korrekturen implizit nullbarer Parameter unter PHP 8.5. Vergleichen Sie jedes: Ist die einzige Änderung `?Type $param = null`, übernehmen Sie sie in den Kernel und löschen das Override. |

**Schnellskript für Datentyp-Korrekturen, die nur PHP 8.5 betreffen:**
```bash
# For each datatype override, diff against kernel original.
# If the diff contains ONLY nullable-param changes, apply and remove.
for f in extension/sevenx_mongodb/classes/kernel/datatypes/**/*.php; do
  rel="${f#extension/sevenx_mongodb/classes/}"   # kernel/datatypes/...
  orig="$rel"
  if [[ -f "$orig" ]]; then
    echo "=== $f ==="
    diff "$f" "$orig" | grep "^[<>]" | head -20
  fi
done
```

#### Phase 6 — Hilfen für Cronjobs, sonstige Infrastruktur

| Override der Erweiterung | Ziel im Kernel | Wichtige MongoDB-Ergänzungen |
|---|---|---|
| `classes/kernel/ezdbgarbagecollector.php` | `kernel/classes/ezdbgarbagecollector.php` | 4 Muster zum Aufräumen verwaister Datensätze mit RIGHT JOIN durch Anti-Join-Aggregationen mit `$nin` ersetzt |
| `classes/kernel/ezpackage.php` | `kernel/classes/ezpackage.php` | `setInstalled()`, `getInstallState()` |
| `classes/kernel/ezsiteinstaller.php` | `kernel/classes/ezsiteinstaller.php` | Prüfungen des Installationsmodus |
| `classes/kernel/ezclusterfilehandler.php` | `kernel/classes/ezclusterfilehandler.php` | Schutzprüfungen für den Cluster-Modus |
| `classes/kernel/clusterfilehandlers/ezfsfilehandler.php` | `kernel/classes/clusterfilehandlers/ezfsfilehandler.php` | Debug-Aufrufe `error_log` für PROCESSCACHE entfernt ✅ |
| Alle übrigen Overrides unter `notification/`, `packagehandlers/`, `workflowtypes/` | Die entsprechenden Pfade unter `kernel/` | Jeden auf MongoDB-Inhalte prüfen; viele sind möglicherweise nur Korrekturen für PHP 8.5 oder leere Wrapper |


### 20.6 Sonderfälle

#### `lib/ezdb/classes/ezpersistentobject.php`

Bereits an Ort und Stelle gepatcht (kein Override in der Erweiterung). Bei der Umstellung auf die vollständige Neuimplementierung im Kernel
ist nichts zu tun — die Datei ist bereits im richtigen Endzustand.

#### Override von `nxc_powercontent`

`extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php` wurde direkt
in der Erweiterung nxc_powercontent gepatcht (nicht über sevenx_mongodb). Die Datei bleibt als
Override einer Erweiterung eines Drittanbieters bestehen — bei der Neuimplementierung ist keine Änderung nötig.

#### `var/autoload/ezp_override.php` — schrittweises Entfernen

Entfernen Sie jeweils einen Klasseneintrag, sobald ein Durchgang aus Zusammenführen und Testen abgeschlossen ist. **Entfernen Sie Einträge
nicht gesammelt vor dem Testen** — wird der Eintrag entfernt, ohne dass das Zusammenführen in der Kernel-Datei erfolgt ist,
führt das beim nächsten Seitenaufruf zu einem fatalen Fehler wegen einer nicht gefundenen Klasse.

Sichere Reihenfolge des Entfernens: entsprechend der Reihenfolge der Phasen in §20.5. Auf jedes Entfernen folgt sofort ein
Test der betroffenen Funktion im Browser.


### 20.7 Prüfung nach dem Zusammenführen

Nachdem alle Overrides zusammengeführt sind:

1. **Prüfen Sie `var/autoload/ezp_override.php`** — die Datei enthält nur noch die Registrierung des Adapters:
   ```bash
   grep -v "expMongoDB\|^<?php\|^//\|^$" var/autoload/ezp_override.php
   # Should produce no output
   ```

2. **Prüfen Sie, dass das Kernel-Verzeichnis der Erweiterung leer ist:**
   ```bash
   find extension/sevenx_mongodb/classes/kernel -name "*.php" | wc -l
   # Should output 0
   ```

3. **Führen Sie die PHP-Syntaxprüfung über alle Dateien in kernel und lib aus:**
   ```bash
   find kernel/ lib/ -name "*.php" | \
     xargs -P4 -I{} /opt/plesk/php/8.5/bin/php -l {} 2>&1 | \
     grep -v "No syntax errors"
   # Should produce no output
   ```

4. **Führen Sie touch auf alle Kernel-Dateien in beiden Docroots aus**, um den OPcache zu umgehen:
   ```bash
   BASE=/path/to/exponential
   ALT=/path/to/exponential-alt
   find "$BASE/kernel/" "$BASE/lib/" -name "*.php" -exec touch {} \;
   find "$ALT/kernel/" "$ALT/lib/" -name "*.php" -exec touch {} \;
   ```

5. **Arbeiten Sie die vollständige Testcheckliste** aus den Abschnitten 9 und 10 ab.

6. **Prüfen Sie das Fehlerprotokoll** auf neue Einträge MONGO TODO:
   ```bash
   tail -f /path/to/logs/admin/error_log \
     | grep "MONGO TODO"
   # Should produce no output during normal usage
   ```


### 20.8 Vorteile nach dem Abschluss

| Vorher (Override-Erweiterung) | Nachher (zusammengeführter Kernel) |
|---|---|
| „Ist diese Methode gepatcht?“ erfordert einen Blick an 3 Stellen | `grep -n 'databaseName' kernel/classes/somefile.php` beantwortet es sofort |
| Die Kernel-Originale sind toter Code und führen stillschweigend in die Irre | Eine einzige Quelle der Wahrheit pro Klasse |
| Ein Sicherheitspatch des Kernels erfordert eine Aktualisierung des Overrides in der Erweiterung | Die Kernel-Datei einmal patchen; der MongoDB-Zweig ist bereits vorhanden |
| Zwei Docroots erfordern einen manuellen Abgleich bei Kernel-Änderungen | `rsync kernel/ lib/` gleicht alles ab; das Verzeichnis der Erweiterung ist minimal |
| IDEs und Werkzeuge zur statischen Analyse finden die falsche (ursprüngliche) Datei | IDEs finden die richtige, gepatchte Datei |
| Neue Mitwirkende sind von der Override-Zuordnung verwirrt | „MongoDB-Zweige sind Blöcke `if ($db->databaseName() === 'mongo')` — ein übliches Muster“ |
| git diff von `kernel/` sieht unberührt aus (die Patches stecken in der Erweiterung) | git diff von `kernel/` zeigt den vollständigen tatsächlichen Zustand der Codebasis |


### 20.9 Geschätzter Umfang

| Phase | Dateien | Geschätzter Aufwand |
|---|---|---|
| Phase 1 — zentrales ORM und Inhalte | 6 Override-Dateien | Hoher Aufwand: große Dateien, komplexe Pipelines; erfordert sorgfältiges Testen der Abläufe Bearbeiten/Veröffentlichen/Löschen/Kopieren |
| Phase 2 — Benutzer, Rolle, Sektion | 6 Override-Dateien | Mittlerer Aufwand: gut getestete Methoden; der Pfad der Zugriffskontrolle erfordert Tests von Anmeldung und Berechtigungen |
| Phase 3 — Suche, URLs, Cache | 6 Override-Dateien | Mittlerer Aufwand: die Pfade für Suche und URL-Aliase erfordern Navigationstests von Anfang bis Ende |
| Phase 4 — Workflow, Zusammenarbeit, Information Collection | 11 Override-Dateien | Gering bis mittel: die meisten sind kleine, gezielte Korrekturen |
| Phase 5 — Datentypen | ~80 Override-Dateien | Jeweils geringer Aufwand: die meisten sind Korrekturen nullbarer Parameter unter PHP 8.5; 2 enthalten echte MongoDB-Ergänzungen |
| Phase 6 — Cronjobs, Sonstiges | ~15 Override-Dateien | Jeweils geringer Aufwand |
| **Gesamt** | **~124 Override-Dateien** | Empfohlen: eine Phase pro Arbeitssitzung; nach jeder Phase testen, bevor es weitergeht |


## 21. PHPUnit-Testsuite — umgesetzt (Mai 2026)

Dieser Abschnitt beschreibt die konkreten PHPUnit-Testdateien, die ergänzt wurden, um den Adapter `expMongoDB`
abzudecken und zu bestätigen, dass MySQL weiterhin richtig funktioniert. Er ersetzt die Planungsnotizen in
Abschnitt 14 durch echten, lauffähigen Code.

**PHPUnit-Version:** 13.0.0 (unter `vendor/bin/phpunit`)  
**PHP-Laufzeit:** `/opt/plesk/php/8.5/bin/php`  
**Docroot v2-0:** `/path/to/exponential-v2-0`


### 21.1 Dateiaufbau

```
tests/tests/lib/ezdb/mongodb/
  stubs.php                         ← In-process stubs; required by both test files
  expMongoDBAdapterTest.php      ← Unit tests (@group mongodb)       — no live DB
  expMongoDBIntegrationTest.php  ← Integration tests (@group mongodb-live) — live DB
```

Alle drei Dateien liegen unter `tests/tests/lib/ezdb/mongodb/`, also ganz natürlich neben dem
bestehenden Testbaum `lib`, und werden automatisch von der in `phpunit.xml` definierten Testsuite `mongodb`
erfasst.


### 21.2 `stubs.php` — Stubs im selben Prozess

**Zweck:** `expMongoDB` soll sich ohne laufenden MongoDB-Server instanziieren und ausprobieren lassen.
Die Stubs setzen genau die Schnittstelle um, die der Adapter verwendet, gestützt auf einfache PHP-Arrays.

**Wichtige Klassen:**

| Stub-Klasse | Ersetzt | Gestützt auf |
|---|---|---|
| `StubMongoCollection` | `MongoDB\Collection` | ein `array` von Dokumenten |
| `StubMongoDatabase` | `MongoDB\Database` | eine Zuordnung von `StubMongoCollection` |
| `StubMongoClient` | `MongoDB\Client` | eine Referenz auf das Array `$stubCollections` |
| `sevenxMongoDBTestable` | `expMongoDB` | gibt aus `getClient()` ein `StubMongoClient` zurück |

**Von `StubMongoCollection` unterstützte Operationen:**

| Methode | Verhalten |
|---|---|
| `find($filter, $opts)` | Gibt die passenden Dokumente als Objekte `ArrayObject` zurück |
| `findOne($filter)` | Gibt das erste passende Dokument oder `null` zurück |
| `insertOne($doc)` | Hängt das Dokument an das interne Array an |
| `replaceOne($filter, $replacement, $opts)` | Ersetzt den ersten Treffer; fügt ein, wenn `upsert:true` und kein Treffer |
| `updateOne($filter, $update, $opts)` | Wendet `$set` / `$inc` an; fügt bei `upsert:true` ein |
| `updateMany($filter, $update)` | Wendet `$set` / `$inc` auf alle Treffer an |
| `deleteMany($filter)` | Entfernt alle passenden Dokumente |
| `aggregate($pipeline)` | Unterstützt `$match`, `$count`, `$group/$count`, `$sort`, `$limit`, `$project` |

**Verwendungsmuster von `sevenxMongoDBTestable`:**
```php
// 1. Instantiate the testable subclass
$db = new sevenxMongoDBTestable();

// 2. Seed the collection the adapter will touch
$db->stubCollections['ezcontentobject'] = [
    ['id' => 1, 'status' => 1, 'name' => 'Home'],
    ['id' => 2, 'status' => 0, 'name' => 'Draft'],
];

// 3. Call adapter methods — they use StubMongoClient internally
$rows = $db->aggregate('ezcontentobject', [
    ['$match' => ['status' => 1]],
]);
$this->assertCount(1, $rows);
```


### 21.3 `sevenxMongoDBAdapterTest.php` — Unit-Tests

**Gruppe:** `@group mongodb`  
**Aktive Datenbank nötig:** Nein  
**Anzahl der Tests:** 55 Tests, 109 Prüfungen (Stand Mai 2026)

#### Was getestet wird

| Kategorie | Testmethoden | Hinweise |
|---|---|---|
| Identität des Adapters | `testDatabaseNameReturnsMongo`, `testQueryReturnsFalse` | Grundlegender Vertrag für die Verzweigung |
| Hilfen für Zeichenketten | `testEscapeStringCleanValue`, `testEscapeStringHandlesBinaryInput`, `testEscapeStringCastsInt`, `testEscapeStringCastsFloat`, `testEscapeStringNull` | Verhalten der Umwandlung `(string)` |
| Übersetzung der Bedingungen | `testTranslateScalar`, `testTranslateGreaterThan`, `testTranslateGreaterThanOrEqual`, `testTranslateLessThan`, `testTranslateLessThanOrEqual`, `testTranslateNotEqual`, `testTranslateEqual`, `testTranslateLike`, `testTranslateRange`, `testTranslateInArray` | Alle von `translateConditions()` unterstützten Operatoren |
| Transaktionsmethoden | `testBeginQueryReturnsTrue`, `testCommitQueryReturnsTrue`, `testRollbackQueryReturnsTrue`, `testBeginAndCommitTrackCounter`, `testBeginAndRollbackDecrementsCounter` | Logik des Transaktionszählers |
| Zeichenkettenfunktionen | `testSubString`, `testConcatString`, `testMd5`, `testBitAnd`, `testBitOr` | Kompatibilitäts-Shims für SQL |
| Zeichensatz | `testCheckCharsetReturnsTrue`, `testIsCharsetSupportedUTF8`, `testIsCharsetSupportedOther` | Stubs, die immer bestehen |
| Operationen über Stubs | `testAggregateWithMatchFilter`, `testAggregateEmptyPipeline`, `testAggregateMissingCollection` | `aggregate()` über `StubMongoCollection` |
| Einfügen / Upsert / Löschen | `testInsertAddsDocument`, `testUpsertUpdatesExisting`, `testUpsertInsertsNew`, `testDeleteWhereRemovesMatched`, `testDeleteWhereNoMatchIsNoOp` | `insert()`, `upsert()`, `deleteWhere()` |
| `arrayQuery()` | `testArrayQueryReturnsEmptyArray` | Gibt immer `[]` zurück; keine Exception |
| Vertrag der Kernel-Verzweigung | `testDatabaseNameIsMongoForBranchSelection` | Ausdrücklich: `=== 'mongo'` |

#### Unit-Tests ausführen

```bash
cd /path/to/exponential-v2-0

# Run only the MongoDB unit tests (no live DB)
/opt/plesk/php/8.5/bin/php vendor/bin/phpunit --testsuite mongodb
```

Erwartete Ausgabe:
```
PHPUnit 13.0.0 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.5.6
Configuration: /path/to/exponential-v2-0/phpunit.xml

....................................MONGO TODO arrayQuery: tables=[ezcontentobject] caller=...sevenxMongoDBAdapterTest.php:452 (expMongoDB::arrayQuery)
...................           55 / 55 (100%)

Time: 00:00.186, Memory: 18.00 MB

OK (55 tests, 109 assertions)
```

> Die Zeile `MONGO TODO arrayQuery` ist **erwartet und richtig** — sie stammt aus dem
> Test `testArrayQueryReturnsEmptyArray`, der ausdrücklich den Stub-Pfad von `arrayQuery()`
> durchläuft und bestätigt, dass er die TODO-Warnung protokolliert. Sie ist kein Fehlschlag.


### 21.4 `sevenxMongoDBIntegrationTest.php` — Integrationstests gegen aktive Datenbanken

**Gruppe:** `@group mongodb-live`  
**Aktive Datenbank nötig:** Ja — MongoDB unter `mongodb://db:YOUR_PASSWORD@localhost:27017/exp`
und MariaDB unter `localhost` / Benutzer `YOUR_USER` / Passwort `YOUR_PASSWORD` / Datenbank `YOUR_DATABASE`  
**Anzahl der Tests:** 18 Tests, 47 Prüfungen (Stand Mai 2026)

#### Was getestet wird

**Tests gegen das aktive MongoDB:**

| Test | Was er bestätigt |
|---|---|
| `testMongoPingSucceeds` | Der Server ist erreichbar; `ping` liefert `ok:1` |
| `testMongoInsertAndAggregate` | Zwei Dokumente einfügen; Aggregation mit `$match active:true`; die richtige Anzahl kommt zurück |
| `testMongoUpsertUpdatesExisting` | `replaceOne(upsert:true)` überschreibt ein Feld eines vorhandenen Dokuments |
| `testMongoDeleteMany` | `deleteMany` entfernt nur die passenden Dokumente; die anderen bleiben unberührt |
| `testMongoUpdateOneIncrement` | `updateOne($inc)` erhöht ein Zählerfeld atomar |
| `testMongoExpectedCollectionsExist` | `ezcontentobject`, `ezcontentobject_tree`, `ezcontent_language` und `ezcontentobject_version` existieren alle |
| `testMongoContentObjectDocumentStructure` | Das erste Dokument hat die Felder `id`, `status`, `contentclass_id` und `language_mask` |
| `testMongoTreeNodeHasPathString` | `path_string` entspricht dem Format `/\d+/` |

**Tests gegen das aktive MySQL:**

| Test | Was er bestätigt |
|---|---|
| `testMysqlConnectionWorks` | Die Verbindung gelingt; server_info enthält `MariaDB` |
| `testMysqlContentLanguageHasRows` | `ezcontent_language` hat mindestens eine Zeile |
| `testMysqlContentObjectHasRows` | `ezcontentobject` hat mindestens eine Zeile |
| `testMysqlTreeNodePathStringFormat` | Die Werte von `path_string` entsprechen dem Format `/\d+/` |
| `testMysqlNoOrphanedArchivedVersions` | Die Abfrage läuft fehlerfrei; das Ergebnis ist numerisch |
| `testMysqlContentClassExists` | `ezcontentclass` hat mindestens eine veröffentlichte Klasse (`version=0`) |
| `testMysqlContentObjectNoNullStatus` | Die Anzahl für `status IS NULL` ist 0 |
| `testMysqlSectionTableHasRows` | `ezsection` hat mindestens eine Zeile |
| `testContentObjectCountMatchesBetweenEngines` | Die Anzahl veröffentlichter Objekte in MySQL und MongoDB weicht um weniger als 10 % ab |
| `testMysqlInsertSelectDeleteRoundTrip` | INSERT in `ezpreferences`, SELECT zurück, DELETE — bestätigt, dass der Schreibpfad in MySQL sauber funktioniert |

#### Isolation der Tests

`tearDown()` entfernt die Testdokumente immer, unabhängig vom Ergebnis des Tests:
```php
protected function tearDown(): void
{
    self::$mongoClient
        ->selectCollection( self::MONGO_DB, self::TEST_COL )
        ->deleteMany( [] );
    self::$mongoClient
        ->selectCollection( self::MONGO_DB, self::SEQ_COL )
        ->deleteMany( [ '_id' => 'phpunit_seq_test' ] );
}
```

Produktionsdaten werden nicht angefasst. Alle Schreibvorgänge in MongoDB gehen an `_phpunit_test` (eine eigene Test-Collection)
oder an die Collection `ezsequence` mit dem Schlüssel `phpunit_seq_test`. Das Einfügen in `ezpreferences`
in MySQL verwendet `user_id=0`, das nie ein echter Benutzer ist, und die Zeile wird sofort gelöscht.

#### Integrationstests gegen aktive Datenbanken ausführen

```bash
cd /path/to/exponential-v2-0

# Run MongoDB + MySQL live integration tests
/opt/plesk/php/8.5/bin/php vendor/bin/phpunit --testsuite mongodb-live

# Expected output:
# OK (18 tests, 47 assertions)
```

Ist MongoDB nicht erreichbar, wird die ganze Klasse mit `markTestSkipped` übersprungen.
Ist MySQL nicht erreichbar, werden die einzelnen MySQL-Testmethoden unabhängig übersprungen, während
die MongoDB-Tests weiterlaufen.


### 21.5 Registrierung der Testsuites in `phpunit.xml`

In `phpunit.xml` wurden zwei neue Testsuites ergänzt:

```xml
<!-- MongoDB adapter unit tests — no live DB required -->
<testsuite name="mongodb">
    <directory suffix="Test.php">tests/tests/lib/ezdb/mongodb</directory>
</testsuite>

<!-- MongoDB + MySQL live integration tests — require running servers -->
<testsuite name="mongodb-live">
    <file>tests/tests/lib/ezdb/mongodb/sevenxMongoDBIntegrationTest.php</file>
</testsuite>
```

Die Gruppe `mongodb-live` steht im globalen Block `<exclude>`, damit sie bei einem normalen Aufruf von
`phpunit` nie versehentlich ausgeführt wird:

```xml
<groups>
    <exclude>
        <group>database</group>
        <group>mongodb-live</group>
    </exclude>
</groups>
```

Die Testsuite `mongodb` verwendet `suffix="Test.php"` am Verzeichnis, was sowohl auf
`sevenxMongoDBAdapterTest.php` als auch auf `sevenxMongoDBIntegrationTest.php` passt. Die Datei mit den Integrationstests
ist außerdem in der eigenen Testsuite `mongodb-live` enthalten, damit sie für sich allein ausgeführt werden kann.
Durch die Markierung `@group mongodb-live` wird der Integrationstest vom normalen Lauf ausgeschlossen, auch
wenn die Suite `mongodb` ihn erfasst.


### 21.6 Kurzreferenz — alle Testbefehle

```bash
BASE=/path/to/exponential-v2-0
PHP=/opt/plesk/php/8.5/bin/php
cd $BASE

# Unit tests only — no live DB, fast, safe to run anytime
# Note: one "MONGO TODO arrayQuery" line in output is expected (from testArrayQueryReturnsEmptyArray)
$PHP vendor/bin/phpunit --testsuite mongodb

# Live integration tests — requires running MongoDB + MariaDB
$PHP vendor/bin/phpunit --testsuite mongodb-live

# Run both together
$PHP vendor/bin/phpunit --testsuite mongodb --testsuite mongodb-live

# Run just one test method
$PHP vendor/bin/phpunit --testsuite mongodb --filter testDatabaseNameReturnsMongo

# Run with verbose output
$PHP vendor/bin/phpunit --testsuite mongodb --verbose

# Run and show test names
$PHP vendor/bin/phpunit --testsuite mongodb --testdox

# Check which groups are available in the mongodb suite
$PHP vendor/bin/phpunit --testsuite mongodb --list-groups
```


### 21.7 Die Suite erweitern

So ergänzen Sie neue Unit-Tests für den Adapter:

1. Fügen Sie `sevenxMongoDBAdapterTest.php` nach dem vorhandenen Muster eine Methode hinzu.
2. Befüllen Sie `$this->db->stubCollections['collection_name']`, bevor Sie Methoden des Adapters aufrufen.
3. `StubMongoCollection` verarbeitet die Pipeline-Stufen `$match`, `$count`, `$group/$count`, `$sort`, `$limit`
   und `$project`. Wird eine neue Stufe gebraucht, ergänzen Sie sie in `StubMongoCollection::aggregate()` in `stubs.php`.

So ergänzen Sie neue Integrationstests:

1. Fügen Sie `sevenxMongoDBIntegrationTest.php` eine Methode hinzu.
2. Für MongoDB: verwenden Sie `self::$mongoClient->selectCollection(self::MONGO_DB, 'collection_name')`.
3. Für MySQL: rufen Sie zuerst `$this->skipIfNoMySQL()` auf und verwenden Sie dann `self::$mysql`.
4. Für alle geschriebenen Daten: stellen Sie sicher, dass `tearDown()` sie aufräumt (ergänzen Sie die Methode `tearDown`, wenn eine neue Collection oder Tabelle berührt wird).

So ergänzen Sie eine neue Testdatei:

1. Legen Sie sie mit der Endung `Test.php` unter `tests/tests/lib/ezdb/mongodb/` ab.
2. Sie wird automatisch von der Testsuite `mongodb` erfasst.
3. Binden Sie am Anfang `stubs.php` ein, wenn Stubs gebraucht werden:
   ```php
   require_once __DIR__ . '/stubs.php';
   ```


## 22. Leistungsoptimierungen des Setup-Assistenten (Juni 2026)

### 22.1 Hintergrund — warum der Setup-Assistent in ein Timeout lief

Der letzte Schritt „Configuration / Create Sites“ des Setup-Assistenten von Exponential CMS erledigt alles
Folgende in einer einzigen synchronen HTTP-Anfrage:

1. Legt die Hauptsprache, die Siteaccesses und die Sektionen an
2. Installiert alle benötigten Pakete aus `var/storage/packages/7x/` — einschließlich `sevenx_democontent` (~193 Inhaltsobjekte) und des Website-Gerüsts (`sevenx_site`)
3. Führt für jedes Paketobjekt die vollständige Pipeline `eZOperationHandler::execute('content', 'publish', ...)` aus — Speichern der Attribute, Anlegen der Baumknoten, Erzeugen der URL-Aliase, Zusammensetzen der Namen, Ablauf der Caches
4. Führt `initSteps()` aus `sevenxezwebininstaller.php` aus — 142 Schritte, darunter 95 Aufrufe von `classIDbyIdentifier`, 14 Aufrufe von `sectionIDbyName`, 6 Aufrufe von `addPoliciesForRole` und 5 Aufrufe von `assignUserToRole`
5. Ruft `eZSitePostInstall()` aus den Paketeinstellungen auf

Auf einer SQL-Datenbank (MySQL/MariaDB) ist der ganze Vorgang in weniger als 30 Sekunden abgeschlossen, weil:
- die Schreibvorgänge in den SQL-Wortindex in einer einzigen Transaktion im Prozess landen, die gesammelt festgeschrieben wird
- Tabellendurchläufe auf einer lokalen SSD schnell sind
- der Abfrageplaner die Ergebnismengen bereits vorab aufteilt

Unter MongoDB löste derselbe Vorgang **zehntausende einzelne Round-Trips** zum MongoDB-Server aus, weil:
- das eingebaute `eZSearchEngine` jedes Wort jedes Attributs jedes veröffentlichten Objekts indizierte — mit `SELECT * FROM ezsearch_word WHERE word IN (...)` + `INSERT INTO ezsearch_word` + `INSERT INTO ezsearch_object_word_link` pro Wort und pro Objekt. Bei 193 Objekten mit durchschnittlich ~300 Wörtern waren das allein ~60.000 Aufrufe von Aggregationen und Einfügungen in MongoDB.
- `ezsearch_word` und `ezsearch_object_word_link` SQL-Tabellen ohne Entsprechung in MongoDB sind — diese Arbeit war nicht nur langsam, sondern völlig nutzlos (die Ergebnisse wurden nie verwendet).

**Beobachtetes Symptom:** Das PHP-FPM-Limit `request_terminate_timeout` von 190 s griff, bevor der Assistent fertig war. Eine Erhöhung auf 390 s verdeckte das Problem, löste es aber nicht.


### 22.2 Ursachen (nach Auswirkung geordnet)

| # | Ursache | Geschätzter Anteil |
|---|---|---|
| 1 | `eZSearchEngine::addObject()` wurde für jedes veröffentlichte Objekt aufgerufen und indizierte alle Wörter in SQL-Tabellen, die es in MongoDB nicht gibt | ~80 % der Laufzeit |
| 2 | 95 Aufrufe von `classIDbyIdentifier` in `initSteps()`, jeder mit einer vollständigen Aggregation `eZContentClass::fetchByIdentifier()` ohne Cache | ~10 % |
| 3 | Hooks für den Ablauf der Caches (`eZContentCacheManager`) wurden während der Installation bei jeder Veröffentlichung aufgerufen und leerten Caches, die es noch gar nicht gibt | ~5 % |
| 4 | `set_time_limit(5*60)` in `initializePackage()` setzte den Zeitgeber des PHP-Skripts, ohne ihn abzuschalten — das wiegt in falscher Sicherheit, weil das FPM-Limit `request_terminate_timeout` die eigentliche Grenze ist | Verdeckt das Problem, trägt nicht zur Laufzeit bei |


### 22.3 Vorgenommene Korrekturen (1. Juni 2026)

#### Korrektur 1 — die Suchindizierung während der Schleife der Paketinstallation abschalten

**Datei:** `kernel/setup/steps/ezstep_create_sites.php`

Vor der Schleife der Paketinstallation `foreach ($requires as $require)` wird der Schlüssel der Suchmaschineninstanz in
`$GLOBALS` (den `eZSearch::getEngine()` verwendet) nur für MongoDB auf `false` gesetzt. `eZSearch::getEngine()` gibt dann `false` zurück; alle folgenden Prüfungen `if ($searchEngine instanceof ezpSearchEngine)` in `eZSearch::addObject()`, `eZSearch::removeObjectById()` usw. fallen sofort durch — die gesamte Suchpipeline bleibt für die Dauer der Paketinstallation ohne Wirkung. Nach der Schleife (und auf jedem Rückgabepfad mit Fehler) wird die ursprüngliche Instanz der Suchmaschine wiederhergestellt.

```php
// Before the package install loop (MongoDB only):
$searchEngineGlobalKey = 'eZSearchPlugin_' . ( $GLOBALS['eZCurrentAccess']['name'] ?? '' );
$previousSearchEngineInstance = $GLOBALS[$searchEngineGlobalKey] ?? null;
$searchEngineWasSet = array_key_exists( $searchEngineGlobalKey, $GLOBALS );
if ( $db->databaseName() === 'mongo' )
    $GLOBALS[$searchEngineGlobalKey] = false;

// ... package install loop ...

// After the loop / on error returns:
if ( $searchEngineWasSet )
    $GLOBALS[$searchEngineGlobalKey] = $previousSearchEngineInstance;
else
    unset( $GLOBALS[$searchEngineGlobalKey] );
```

**Warum das unbedenklich ist:** Die Tabellen des Suchindex (`ezsearch_word`, `ezsearch_object_word_link`)
gibt es in MongoDB nicht; die Indizierung in diese Tabellen war auf Datenebene ohnehin wirkungslos. Nach der Installation
baut der Cronjob `indexcontent` (`cronjobs/indexcontent.php`) den Suchindex aus den
neu veröffentlichten Objekten neu auf.

#### Korrektur 2 — frühe Rückkehr in `eZSearchEngine::addObject()` für MongoDB

**Datei:** `kernel/search/plugins/ezsearchengine/ezsearchengine.php`

Eine zusätzliche Absicherung am Anfang von `addObject()`:

```php
public function addObject( $contentObject, $commit = true )
{
    // MongoDB uses a document store — the built-in SQL-word-index tables
    // (ezsearch_word, ezsearch_object_word_link) are not meaningful, and
    // every addObject call issues dozens of raw SQL queries that the MongoDB
    // adapter must translate one by one. Skip indexing entirely; use the
    // indexcontent cron job or a dedicated search extension instead.
    if ( eZDB::instance()->databaseName() === 'mongo' )
        return true;

    $contentObjectID = $contentObject->attribute( 'id' );
    // ... rest of method unchanged ...
```

Diese Prüfung fängt jeden Aufruf von `addObject()` ab, der den Trick mit `$GLOBALS` umgeht — zum Beispiel
Aufrufe aus Cronjobs, Kommandozeilenskripten oder Codepfaden, die `eZSearchEngine`
direkt instanziieren.

#### Korrektur 3 — `set_time_limit(0)` bedingungslos in `initializePackage()`

**Datei:** `kernel/setup/steps/ezstep_create_sites.php`

Die Bedingung:
```php
// Before:
$maxTime = ini_get( 'max_execution_time' );
if ( $maxTime != 0 and $maxTime < 5*60 )
    @set_time_limit( 5*60 );
```
wurde durch ein bedingungsloses Abschalten ersetzt:
```php
// After:
@set_time_limit( 0 );
```

Damit entfällt der Zeitgeber für die Skriptausführung auf PHP-Seite vollständig. Das FPM-Limit `request_terminate_timeout`
bleibt die einzige Obergrenze der Echtzeit (wie Sie es konfigurieren, steht in Abschnitt 8). Bei SQL-Installationen ist der
Aufruf unschädlich.


### 22.4 Erwartete Wirkung

| Vor den Optimierungen | Nach den Optimierungen |
|---|---|
| ~60.000 Round-Trips zu MongoDB während der Suchindizierung | 0 (die Suchindizierung wird während der Installation übersprungen) |
| FPM-Timeout bei 190 s (eine Erhöhung auf 390 s war nötig) | Geschätzter Abschluss in 60–90 s; 390 s bleiben eine sichere Obergrenze |
| `request_terminate_timeout = 390` in Plesk erforderlich | Kann zur Sicherheit auf 600 gesetzt werden; 390 sollte jetzt genügen |

Führen Sie nach dem Abschluss des Setup-Assistenten den Cronjob `indexcontent` aus, um den Suchindex zu füllen:
```bash
/opt/plesk/php/8.5/bin/php runcronjobs.php --siteaccess=sevenx_site --allow-root-user
```


### 22.5 Geänderte Dateien

| Datei | Änderung |
|---|---|
| `kernel/setup/steps/ezstep_create_sites.php` | `set_time_limit(0)`; Block zum Abschalten und Wiederherstellen der Suchmaschine in `$GLOBALS` um die Schleife der Paketinstallation |
| `kernel/search/plugins/ezsearchengine/ezsearchengine.php` | Frühe Rückkehr in `addObject()` für MongoDB |


## 23. Installationspakete für Websites — Verteilung und Paketserver

### 23.1 Überblick

Exponential CMS 6.0.14 wird mit einer Reihe von Installationspaketen für Websites ausgeliefert, die unter
`var/storage/packages/7x/` liegen. Diese Pakete braucht der Setup-Assistent und der Website-Installer
`sevenxezwebininstaller.php`. Sie sollen auf dem weltweiten
Paketserver von Exponential bereitgestellt werden, damit sie bei neuen Installationen während des Setup-Assistenten
automatisch heruntergeladen werden.


### 23.2 Paketbestand

Die folgenden Pakete liegen in `var/storage/packages/7x/` und bilden den vollständigen
Installationssatz für den Websitetyp `sevenx_site`:

| Paketname | Zweck |
|---|---|
| `sevenx_site` | Grundgerüst der Website: `sevenxezwebininstaller.php` (Installer mit 142 Schritten), Website-Einstellungen, Definitionen von Rollen, Sektionen und Richtlinien |
| `sevenx_democontent` | ~193 Demo-Inhaltsobjekte — Artikel, Ordner, Benutzer, Medien — vorstrukturiert für den Baum von `sevenx_site` |
| `sevenx_classes` | Definitionen der Inhaltsklassen (Folder, Article, Landing Page, Blog Post usw.) mit allen Klassenattributen |
| `sevenx_design` | Designdateien: Templates, CSS und Bilder für das Frontend-Design von `sevenx_site` |
| `sevenx_common` | Gemeinsame Ressourcen und Einstellungen für alle sevenx-Websitetypen |
| `sevenx_users` | Standardstruktur der Benutzer und Gruppen: Administrators, Anonymous Users, Members |
| `sevenx_roles` | Definitionen der Rollen und Richtlinien für die 7 Standardrollen |
| `sevenx_sections` | Definitionen der Sektionen (Standard, Restricted, Blog) |
| `sevenx_states` | Definitionen der Statusgruppen und Status von Objekten (Lock, Visibility) |
| `sevenx_workflows` | Definitionen der Workflows (Two-step publishing, Review workflow) |
| `sevenx_languages` | Grundausstattung des Sprachpakets (Einrichtung des Hauptgebietsschemas eng-GB) |


### 23.3 Ausstehender Upload auf den Paketserver

> **Status: Upload ausstehend.** Die Pakete sind lokal fertiggestellt und geprüft, wurden aber
> noch nicht auf den weltweiten Server für die Paketverteilung übertragen.

Vor der weltweiten Verteilung muss Folgendes erledigt sein:

1. **Signieren der Pakete** — jedes Archiv `.ezpkg` muss mit dem GPG-Schlüssel des Projekts signiert werden. Die aktuellen Pakete sind nicht signiert (Entwicklungs-Builds).
2. **Versionskennzeichnung** — die Pakete müssen in ihrem Manifest `package.xml` die Version `6.0.14` tragen. Prüfen Sie jede `package.xml` und bestätigen Sie, dass `<version>6.0.14</version>` vorhanden ist.
3. **Upload auf den Paketserver** — die in `setup.ini[PackageSettings] RepositoryURL` angegebene URL des Paketservers muss alle 11 Pakete erhalten. Die Zugangsdaten für den Paketserver verwaltet der Maintainer des Projekts.
4. **Aktualisierung des Index des Paketservers** — nach dem Upload muss die Indexdatei `packages.xml` des Paketservers neu erzeugt werden, damit sie die neuen Versionseinträge enthält.
5. **Smoke-Test auf einer sauberen Installation** — führen Sie nach dem Upload den Setup-Assistenten auf einer sauberen MongoDB-Installation aus und bestätigen Sie, dass er alle Pakete fehlerfrei herunterlädt und installiert.


### 23.4 Wie der Setup-Assistent diese Pakete verwendet

Während des Schritts „Create Sites“:

1. Der Assistent ruft für jedes benötigte Paket aus `sevenx_site/package.xml → <dependencies>` `eZPackage::fetch($packageName)` auf.
2. `eZPackage::fetch()` prüft zuerst `var/storage/packages/7x/<packageName>/` (lokaler Cache).
3. Wird es lokal nicht gefunden, versucht es den Download von der in `setup.ini[PackageSettings] RepositoryURL` konfigurierten URL.
4. Ist das Paket geladen, läuft `$package->install($installParameters)` — und löst die in Abschnitt 22 beschriebene vollständige Veröffentlichungspipeline für Objekte aus.

Legen Sie bei Installationen ohne Netzzugang oder in abgeschotteten Umgebungen die Paketverzeichnisse vor dem Start des Setup-Assistenten direkt in `var/storage/packages/7x/` ab. Der Assistent verwendet sie dann, ohne eine Netzwerkanfrage zu stellen.


### 23.5 Aufbau des lokalen Paketverzeichnisses

```
var/storage/packages/7x/
  sevenx_site/
    package.xml          ← manifest: name, version, dependencies, install type
    ezcontentclass/      ← class XML export files
    ezcontentobject/     ← content object XML export files (demo content)
    settings/
      sevenxezwebininstaller.php   ← 142-step site installer (3922 lines)
      sevenx_site.ini              ← site-specific settings
  sevenx_democontent/
    package.xml
    ezcontentobject/     ← 192 XML files — one per demo content object
  sevenx_classes/
    package.xml
    ezcontentclass/      ← class definition XML files
  ...
```


### 23.6 Pakete ändern oder neu exportieren

Werden Änderungen an der Struktur der Website vorgenommen (neue Klassen, aktualisierte Demo-Inhalte, geänderte Rollen), müssen die
betroffenen Pakete vor dem nächsten Upload neu exportiert werden:

1. **Pakete mit Inhaltsobjekten:** Verwenden Sie in der Administration Setup → Pakete → Export. Wählen Sie Inhaltsobjekte nach Teilbaum oder einzelnem Knoten aus. Legen Sie Paketname und Version fest. Der Export erzeugt ein neues Archiv `.ezpkg`.
2. **Klassenpakete:** Administration → Setup → Pakete → Export → Inhaltsklassen.
3. **Manuelle Änderungen:** Bearbeiten Sie `sevenxezwebininstaller.php` direkt unter `var/storage/packages/7x/sevenx_site/settings/sevenxezwebininstaller.php`. Es ist eine einfache PHP-Datei. Änderungen werden beim nächsten Lauf des Assistenten sofort übernommen (für lokale Installationen ist kein neues Paketieren nötig; vor dem Upload auf den Server schon).


## 24. Die wichtigsten ersten Schritte mit Exponential 6.0.14 und MongoDB

Dieser Abschnitt ist die einseitige Schnellstartreferenz für alle, die Exponential CMS 6.0.14 auf MongoDB
installieren oder damit entwickeln. Er setzt einen sauberen Server voraus, auf dem PHP 8.5, MongoDB 8.x und
Plesk bereits vorhanden sind.


### 24.1 Voraussetzungen für den Server

| Anforderung | Minimum | Empfohlen |
|---|---|---|
| PHP | 8.2 | 8.5 (getestet) |
| MongoDB | 6.0 | 8.3 (getestet) |
| PHP-Erweiterung für MongoDB | 1.18 | Neueste |
| Composer | 2.x | 2.x |
| Webserver | Apache / nginx | nginx (von Plesk verwaltet) |
| RAM | 512 MB | 2 GB |
| Festplatte | 2 GB | 10 GB |


### 24.2 Schritt 1 — MongoDB installieren und den Datenbankbenutzer anlegen

```bash
# Install MongoDB 8.x (AlmaLinux / Rocky Linux)
cat > /etc/yum.repos.d/mongodb-org-8.0.repo << 'EOF'
[mongodb-org-8.0]
name=MongoDB Repository
baseurl=https://repo.mongodb.org/yum/redhat/8/mongodb-org/8.0/x86_64/
gpgcheck=1
enabled=1
gpgkey=https://www.mongodb.org/static/pgp/server-8.0.asc
EOF
dnf install -y mongodb-org
systemctl enable --now mongod

# Create the application user in the site's database: the adapter connects with
# mongodb://<user>:<password>@<server>:<port>/<database> and no authSource, so
# MongoDB authenticates the user against that database, not against admin
mongosh exp --eval "
  db.createUser({
    user: 'db',
    pwd: 'your-secure-password',
    roles: [
      { role: 'readWrite', db: 'exp' },
      { role: 'dbAdmin',   db: 'exp' }
    ]
  })
"
```


### 24.3 Schritt 2 — PHP-FPM für den Setup-Assistenten konfigurieren

> **Entscheidend:** Erledigen Sie das, bevor Sie den Setup-Assistenten starten, nicht erst nach einem Timeout.

Setzen Sie in Plesk → Domains → Ihre Domain → PHP-Einstellungen unter **Zusätzliche Konfigurationsdirektiven**:

```ini
request_terminate_timeout = 600
```

Damit hat der Setup-Assistent bis zu 10 Minuten Zeit für den Abschluss. Nach der Installation können Sie den Wert
auf 60–120 s senken.


### 24.4 Schritt 3 — Exponential CMS installieren

```bash
# Clone or unpack the Exponential CMS 6.0.14 release into the docroot
cd /web/vh/yourdomain.com/doc/yourdocroot

# Install PHP dependencies via Composer
composer install --no-dev

# Ensure the MongoDB driver is installed
/opt/plesk/php/8.5/bin/php -m | grep mongodb
# Expected output: mongodb
```

Wenn die Erweiterung `mongodb` nicht aufgeführt ist:
```bash
/opt/plesk/php/8.5/bin/pecl install mongodb
# Then add to Plesk PHP settings:
#   extension=mongodb.so
```


### 24.5 Schritt 4 — die Installationspakete vorab ablegen

Ist der Paketserver noch nicht erreichbar (Upload der Pakete ausstehend — siehe Abschnitt 23), legen Sie
die Pakete von Hand ab:

```bash
# Packages should already be present if you used the full release archive.
# Verify:
ls var/storage/packages/7x/
# Expected: sevenx_site/  sevenx_democontent/  sevenx_classes/  sevenx_design/  ...
```

Fehlt ein Verzeichnis, kopieren Sie es aus der Referenzinstallation oder aus dem Release-Archiv `.tar.gz`.


### 24.6 Schritt 5 — den Setup-Assistenten ausführen

1. Rufen Sie `http://yourdomain.com/` im Browser auf — Sie werden automatisch zum Setup-Assistenten weitergeleitet.
2. Durchlaufen Sie jeden Schritt des Assistenten:
   - **Welcome** — lesen und akzeptieren Sie die Lizenz
   - **System Check** — alle Punkte sollten grün sein; beheben Sie etwaige Warnungen
   - **Database** — wählen Sie „MongoDB“; geben Sie die Verbindungsdaten ein (`localhost:27017`, Datenbank `exp`, Benutzer `db`, Passwort)
   - **Language** — wählen Sie Ihre Hauptsprache (z. B. `eng-GB`)
   - **Site Type** — wählen Sie `sevenx_site`
   - **Admin User** — legen Sie die E-Mail-Adresse und das Passwort des Administrators fest
   - **Configuration** — das ist der langsame Schritt; der Browser scheint 1–3 Minuten zu hängen; **brechen Sie nicht ab**; warten Sie auf die Erfolgsseite
3. Nach dem Abschluss zeigt der Assistent Links zur **Administration** und zur **Benutzerseite (Frontend)**.
   Er leitet bewusst **nicht** automatisch zur Website weiter — klicken Sie auf die angebotenen
   Links, um zur gewünschten Oberfläche zu gelangen.


### 24.7 Schritt 6 — MongoDB-Indizes anlegen

Unmittelbar nach dem Abschluss des Setup-Assistenten ausführen:

```bash
mongosh "mongodb://db:your-secure-password@localhost:27017/exp" \
  --file extension/sevenx_mongodb/bin/mongodb/create_indexes.js
```

Indizes sind für die Leistung entscheidend. Ohne sie durchlaufen Abfragen auf Teilbäume und das Nachschlagen von URL-Aliasen
die gesamte Collection.


### 24.8 Schritt 7 — die Installation prüfen

```bash
# Check the front site returns HTTP 200
curl -sk -o /dev/null -w "%{http_code}" https://yourdomain.com/
# Expected: 200

# Check the admin panel
curl -sk -o /dev/null -w "%{http_code}" https://yourdomain.com/user/login
# Expected: 200

# Check for MONGO TODO entries (should be none after full install)
tail -20 /var/log/plesk-php85-fpm/error.log | grep "MONGO TODO"
# Expected: no output
```

Melden Sie sich in der Administration unter `https://yourdomain.com/edit/` an (oder unter der Edit-Subdomain, wenn diese
getrennt konfiguriert ist), mit den Administrator-Zugangsdaten, die Sie im Assistenten festgelegt haben.


### 24.9 Schritt 8 — Suchindex nach der Installation

Der Setup-Assistent überspringt die Suchindizierung bei der Installation der Demo-Inhalte (aus Leistungsgründen — siehe
Abschnitt 22). Bauen Sie den Suchindex nach der Installation auf:

```bash
/opt/plesk/php/8.5/bin/php runcronjobs.php \
  --siteaccess=sevenx_site --allow-root-user
```

Oder richten Sie einen System-Cron ein:
```bash
# Add to /etc/cron.d/exponential-cms:
*/5 * * * * www-data /opt/plesk/php/8.5/bin/php \
  /web/vh/yourdomain.com/doc/yourdocroot/runcronjobs.php \
  --siteaccess=sevenx_site --quiet 2>&1
```


### 24.10 Schritt 9 — Skript zum Zurücksetzen des OPcache

Während der Entwicklung, nach dem Bearbeiten einer PHP-Datei:

```bash
# Touch the file to invalidate opcache (revalidate_freq=2s)
touch /path/to/modified/file.php

# Or use the opcache reset endpoint (if configured):
curl -sk https://yourdomain.com/opcache_reset_edit.php
```

**Starten Sie PHP-FPM nie neu**, um den OPcache zu leeren — das trennt alle aktiven Verbindungen und stört den Betrieb.


### 24.11 Schritt 10 — das FPM-Timeout senken

Wenn Sie bestätigt haben, dass die Installation abgeschlossen ist und funktioniert, senken Sie `request_terminate_timeout`
in Plesk wieder auf einen für die Produktion sicheren Wert:

```ini
request_terminate_timeout = 60
```


### 24.12 Kurzreferenz zur Fehlersuche

| Symptom | Wahrscheinliche Ursache | Lösung |
|---|---|---|
| Weißer Bildschirm / 502 im Setup-Assistenten nach ~190 s | FPM-Wert `request_terminate_timeout` zu niedrig | In den PHP-Einstellungen von Plesk auf 600 erhöhen (siehe §24.3) |
| Der Setup-Assistent wird abgeschlossen, aber die Website zeigt einen leeren Baum | MongoDB-Indizes nicht angelegt | `create_indexes.js` ausführen (siehe §24.7) |
| `MONGO TODO arrayQuery` im Fehlerprotokoll | Eine Kernel-Methode hat noch einen nicht portierten SQL-Aufruf | Bekannte Lücken und Korrekturen stehen in Abschnitt 11 |
| URL-Aliase werden nicht aufgelöst (alles 404) | Import von `ezurlalias_ml` unvollständig oder Indizes fehlen | `create_indexes.js` ausführen; prüfen, ob der Index `parent`+`text_md5` existiert |
| Anmeldung in der Administration schlägt fehl | In der Collection `ezuser` fehlt der Datensatz des Administrators, oder der Passwort-Hash ist abgeschnitten | In `mongosh` `db.ezuser.findOne({login:'admin'})` prüfen |
| Langsame Seiten beim ersten Aufruf | Keine Indizes auf den wichtigen Collections | `create_indexes.js` ausführen |
| `Class not found: eZSomeClass` nach dem Bearbeiten von Kernel-Dateien | `var/autoload/ezp_override.php` verweist noch auf die gelöschte Datei der Erweiterung | Entweder die Datei der Erweiterung wiederherstellen oder den Eintrag aus der Override-Zuordnung entfernen |
| PHP-Fatal: `Call to a member function on bool` aus `eZSearch` | Der Suchmaschinenschlüssel in `$GLOBALS` ist außerhalb der Installation auf `false` gesetzt | Prüfen, ob der Block zum Wiederherstellen der Suchmaschine in `ezstep_create_sites.php` richtig gelaufen ist |


### 24.13 Kurzreferenz zur MongoDB-Verbindung

```bash
# Interactive shell
mongosh "mongodb://db:your-password@localhost:27017/exp"

# Check key collection counts
mongosh --quiet "mongodb://db:your-password@localhost:27017/exp" --eval "
  ['ezcontentobject','ezcontentclass','ezcontentobject_tree',
   'ezurlalias_ml','ezuser','ezsection'].forEach(c =>
    print(c + ': ' + db[c].countDocuments())
  )
"

# Check for errors in PHP-FPM log
tail -f /var/log/plesk-php85-fpm/error.log | grep -v 'XDEBUG\|Xdebug'
```


### 24.14 Hinweise zur Entwicklungsumgebung

- **Nehmen Sie Änderungen an einer Entwicklungskopie vor** (zum Beispiel `www.example.com` auf einem Testserver), nie an der Produktion.
- **PHP-FPM-Fehlerprotokoll** der Entwicklungskopie, zum Beispiel `/var/log/plesk-php85-fpm/error.log` unter Plesk.
- **Zurücksetzen des OPcache** für die Entwicklungskopie, wenn Sie einen solchen Endpunkt einrichten: `curl -sk https://www.example.com/opcache_reset_edit.php`
- **MongoDB-Shell** für deren Datenbank: `mongosh --quiet "mongodb://YOUR_USER:YOUR_PASSWORD@localhost:27017/exp"`
- **Prüfen Sie Änderungen, bevor sie einen Release-Branch erreichen** (hier den Branch v2-0).
- **Ein Neustart von PHP-FPM ist** für eine geänderte Datei **nicht nötig**: `touch <file.php>` sorgt dafür, dass der OPcache sie neu liest.


## 25. Kompatibilität der Datentypen — Kern und Community

Dieser Abschnitt beschreibt, welche eingebauten und von der Community beigesteuerten Datentypen ohne Weiteres
mit MongoDB funktionieren und welche zusätzlich zur Standardimplementierung für MySQL eigene MongoDB-Codepfade
benötigen.

### Wie Datentypen Daten speichern

Die meisten Datentypen speichern ihren Wert vollständig in der Collection `ezcontentobject_attribute`
(Felder `data_text`, `data_int`, `data_float`). Sie erfordern **keine besondere MongoDB-Arbeit** über das hinaus,
was `eZPersistentObject::storeObject()` und `fetchObjectList()` bereits leisten.

Eine kleinere Gruppe von Datentypen besitzt eine oder mehrere **sekundäre Collections** (z. B. `ezkeyword`,
`ezuser`, `ezuservisit`). Jede dieser sekundären Collections braucht eigene MongoDB-Pfade für
aggregate/insert/deleteWhere.


### 25.1 Datentypen des Kerns (eingebaut)

| Datentyp | Bezeichner | Speichermodell | MongoDB-Status |
|---|---|---|---|
| Textzeile | `ezstring` | `data_text` im Attribut | ✅ Funktioniert — nur Attribut |
| Textblock | `eztext` | `data_text` im Attribut | ✅ Funktioniert — nur Attribut |
| XML-Rich-Text | `ezxmltext` | `data_text` (serialisiertes XML) im Attribut | ✅ Funktioniert — Korrekturen der null-Prüfungen für PHP 8.5 angewendet (§5) |
| Ganzzahl | `ezinteger` | `data_int` im Attribut | ✅ Funktioniert — nur Attribut |
| Gleitkommazahl | `ezfloat` | `data_float` im Attribut | ✅ Funktioniert — nur Attribut |
| Boolesch | `ezboolean` | `data_int` (0/1) im Attribut | ✅ Funktioniert — nur Attribut |
| Datum | `ezdate` | `data_int` (Unix-Zeitstempel) im Attribut | ✅ Funktioniert — nur Attribut |
| Datum und Uhrzeit | `ezdatetime` | `data_int` (Unix-Zeitstempel) im Attribut | ✅ Funktioniert — nur Attribut |
| Uhrzeit | `eztime` | `data_int` (Sekunden seit Mitternacht) im Attribut | ✅ Funktioniert — nur Attribut |
| E-Mail | `ezemail` | `data_text` im Attribut | ✅ Funktioniert — nur Attribut |
| URL | `ezurl` | `data_text` + Collection `ezurl`; Join über `ezurl_object_link` | ✅ Funktioniert — vollständige Pipeline mit `$lookup` umgesetzt (§5) |
| Bezeichner | `ezidentifier` | `data_text` im Attribut | ✅ Funktioniert — nur Attribut |
| ISBN | `ezisbn` | `data_text` im Attribut | ✅ Funktioniert — nur Attribut |
| Auswahl | `ezselection` | `data_text` (Optionszeichenkette) im Attribut | ✅ Funktioniert — nur Attribut |
| Kontrollkästchen (Enum) | `ezenum` | `data_int` im Attribut | ✅ Funktioniert — nur Attribut |
| Land | `ezcountry` | `data_text` (Ländercode) im Attribut | ✅ Funktioniert — nur Attribut |
| Objektrelation | `ezobjectrelation` | `data_int` (ID des verwandten Objekts) + `ezcontentobject_link` | ✅ Funktioniert — die Verknüpfungstabelle verwaltet `eZContentObject` |
| Liste von Objektrelationen | `ezobjectrelationlist` | `data_text` (XML-Liste) + `ezcontentobject_link` | ✅ Funktioniert — die Verknüpfungstabelle verwaltet `eZContentObject` |
| Matrix | `ezmatrix` | `data_text` (serialisiert) im Attribut | ✅ Funktioniert — nur Attribut |
| Mehrfachoption | `ezmultioption` | `data_text` (XML) im Attribut | ✅ Funktioniert — nur Attribut |
| Mehrfachoption 2 | `ezmultioption2` | `data_text` (XML) im Attribut | ✅ Funktioniert — nur Attribut |
| Bereichsoption | `ezrangeoption` | `data_text` im Attribut | ✅ Funktioniert — nur Attribut |
| Option | `ezoption` | `data_text` im Attribut | ✅ Funktioniert — nur Attribut |
| INI-Einstellung | `ezinisetting` | `data_text` im Attribut | ✅ Funktioniert — Korrektur der HTTP-Eingabe bei Mehrfachauswahl angewendet (§5) |
| Paket | `ezpackage` | `data_text` im Attribut | ✅ Funktioniert — nur Attribut |
| Abonnement eines Teilbaums | `ezsubtreesubscription` | `data_text` im Attribut | ✅ Funktioniert — nur Attribut |
| Schlüsselwort | `ezkeyword` | `data_text` im Attribut **+** Collections `ezkeyword` und `ezkeyword_attribute_link` | ✅ Funktioniert — vollständige MongoDB-Pfade in `ezkeyword.php` und `ezkeywordtype.php` |
| Bild | `ezimage` | `data_text` (XML) im Attribut; Dateien auf der Festplatte | ✅ Funktioniert — die Bilddaten bleiben im Attribut; Dateizugriffe laufen nur über das Dateisystem |
| Binärdatei | `ezbinaryfile` | `data_text` (XML) + Collection `ezbinaryfile` | ✅ Funktioniert — Speicherung des Attributs über `eZPersistentObject`; das Erhöhen des Download-Zählers in `ezbinaryfiletype.php` auf `mongoUpdateOne($inc)` umgestellt (Juni 2026) |
| Medien | `ezmedia` | `data_text` (XML) + Collection `ezmedia` | ✅ Funktioniert — die gesamte Speicherung läuft über `eZPersistentObject`; kein rohes SQL in `ezmedia.php` oder `ezmediatype.php` |
| Preis | `ezprice` | `data_float` im Attribut; liest bei der Anzeige `ezproductcollection_item` | ✅ Funktioniert für die einfache Anzeige — der Bestellablauf im Shop ist nicht getestet |
| Mehrfachpreis | `ezmultiprice` | `data_text` (XML) im Attribut; Nachschlagen der Währungen über `ezcurrencyname` | ✅ Funktioniert für die einfache Anzeige — Abfragen der Währungstabelle können leer zurückkommen |
| Produktkategorie | `ezproductcategory` | `data_int` im Attribut; Collection `ezproductcategory` + Join-Abfragen | ✅ Funktioniert — `fetchProductCountByCategory()` und `removeByID()` in `ezproductcategory.php` mit vollständigen Aggregationspipelines mit `$lookup` gepatcht (Juni 2026) |
| Benutzerkonto | `ezuser` | `data_text` + Collections `ezuser`, `ezuser_setting`, `ezuservisit`, `ezforgot_password` | ✅ Funktioniert — alle Pfade vollständig gepatcht: Anmeldung, Besuchserfassung, Anmeldezähler, fehlgeschlagene Anmeldeversuche, Aufheben bei der Abmeldung, `fetchLoggedInCount`, `isUserLoggedIn`, `fetchLoggedInList` (Bereich der angemeldeten Benutzer in der Administration), `fetchContentList`, `fetchUserClassList`; das Aufräumen von `ezforgot_password` verwendet `deleteWhere`; `createNew`/`fetchByKey`/`removeByUserID` verwenden `eZPersistentObject` (bereits MongoDB-kompatibel) |


### 25.2 Datentypen von Community-Erweiterungen

Diese Datentypen werden in mitgelieferten Community-Erweiterungen ausgeliefert. Alle wurden auf
MongoDB-Kompatibilität geprüft. Diejenigen, die sekundäre Collections besaßen oder rohe SQL-Abfragen ausführten,
wurden mit MongoDB-spezifischen Codepfaden gepatcht (Juni 2026).

| Erweiterung | Bezeichner des Datentyps | Verwendete SQL-Tabellen | MongoDB-Status |
|---|---|---|---|
| `birthday` | `ezbirthday` | nur `data_int` im Attribut | ✅ Funktioniert — nur Attribut |
| `ezgmaplocation` | `ezgmaplocation` | `data_text` (Breite/Länge/Adresse) im Attribut | ✅ Funktioniert — nur Attribut |
| `ezstarrating` | `ezsrrating` | `data_text` im Attribut | ✅ Funktioniert — nur Attribut |
| `hcaptcha` | `hcaptcha` | `data_text` im Attribut | ✅ Funktioniert — nur Attribut |
| `recaptcha` | `recaptcha` | `data_text` im Attribut | ✅ Funktioniert — nur Attribut |
| `xrowmetadata` | `xrowmetadata` | `ezkeyword`, `ezkeyword_attribute_link` (delegiert an `eZKeyword`) | ✅ Funktioniert — `deleteStoredObjectAttribute()` mit einer Pipeline aus Aggregation + `deleteWhere` für das Aufräumen der Schlüsselwörter gepatcht (Juni 2026); die Lese- und Schreibpfade der Schlüsselwörter sind bereits MongoDB-kompatibel |
| `enhancedselection2` (sck) | `sckenhancedselection` | `data_text` im Attribut; führt optional eine vom Benutzer angegebene SQL-Abfrage aus, um die Optionen zu füllen | ✅ Funktioniert (Modus mit statischen Optionen) — `isDbQueryValid()` und `getDbOptions()` geben unter MongoDB jetzt sofort `false`/`[]` zurück (Juni 2026); die Optionsquelle über eine Datenbankabfrage ist bewusst abgeschaltet, weil rohes SQL nicht kompatibel ist |
| `enhancedezbinaryfile` | `enhancedezbinaryfile` | `data_text` + Collection `ezbinaryfile` (erweitert die Binärdatei des Kerns) | ✅ Funktioniert — kein rohes SQL in dieser Erweiterung; übernimmt das Verhalten von `ezbinaryfile` des Kerns, das vollständig gepatcht wurde (Juni 2026) |
| `ezmbpaex` | `ezpaex` | Zählabfrage auf `ezcontentobject_version` | ✅ Funktioniert — die Zählabfrage der Versionen in `deleteStoredObjectAttribute()` in `ezpaextype.php` auf einen Zweig mit `$count`-Aggregation umgestellt (Juni 2026) |
| `ezflow` | `ezpage` | `data_text` (XML-Seitenlayout) im Attribut | ✅ Funktioniert — nur Attribut; der Cronjob für die Zeitplanung der Blöcke ist getrennt |
| `eztags` | `eztags` | `data_text` + Collections `eztags`, `eztag_attribute_link` | ❌ Nicht portiert — die Erweiterung ist in dieser Installation nicht vorhanden; die Speicherung der Tags und `fetchTagsByAttribute` / `fetchObjectsByTag` verwenden rohe Joins über `arrayQuery`; vor der Verwendung ist eine vollständige Umsetzung mit Aggregationspipelines mit `$lookup` nötig |


### 25.3 Zusammenfassung: was noch MongoDB-Codepfade braucht

Alle mitgelieferten Datentypen des Kerns und der Community-Erweiterungen haben jetzt MongoDB-Codepfade.
Die einzige verbleibende Inkompatibilität ist beabsichtigt:

| Datentyp | Datei(en) | Status |
|---|---|---|
| `enhancedselection2` (Optionsquelle über eine Datenbankabfrage) | `extension/enhancedselection2/…/sckenhancedselectiontype.php` | **Für MongoDB bewusst abgeschaltet** — vom Benutzer angegebene SQL-Abfragen sind grundsätzlich nicht kompatibel; `isDbQueryValid()` gibt `false` und `getDbOptions()` gibt `[]` zurück; die Modi mit statischen Optionen und mit Template-Optionen funktionieren normal |
| `eztags` | `extension/eztags/` (nicht installiert) | **Nicht portiert** — die Erweiterung fehlt in dieser Installation; die Tag-Collections (`eztags`, `eztag_attribute_link`) brauchen Aggregationspipelines mit `$lookup` für `fetchTagsByAttribute`, `fetchObjectsByTag` und die Schreibpfade der Tag-Verknüpfungen |

> **Hinweis:** `ezprice`, `ezmultiprice` und der Seitentyp von `ezflow` funktionieren für die einfache Anzeige von Inhalten, aber
> die vollständigen Abläufe für E-Commerce und die Zeitplanung der Blöcke sind nicht getestet und können tiefer in den Kernel-Modulen
> für Shop und Flow weitere Aufrufe von `arrayQuery` enthalten.


## 26. Bekannte Einschränkungen

Die folgenden Lücken bestehen in der MongoDB-Portierung mit Stand Juni 2026. Alle sind
in den betreffenden Abschnitten oben beschrieben; dieser Abschnitt fasst sie zum schnellen Nachschlagen an einer
Stelle zusammen.

- **`storePath()` in `ezurlaliasml.php`** — der Neuaufbau der URL-Aliase legt nur Einträge auf oberster Ebene
  an; das Neuerzeugen der Aliase tiefer Teilbäume nach dem Verschieben von Knoten fällt weiterhin
  auf den MySQL-Pfad zurück. `bin/php/updateniceurls.php` läuft ohne Absturz durch,
  meldet für untergeordnete Knoten aber `Updated 0/N`. Siehe §13.4.

- **Anzeigeseiten für Rollen / Berechtigungen** — in den View-Skripten der Kernel-Module (Rollenliste, Zuweisung von Richtlinien,
  Details der Einschränkungen) sind noch viele Aufrufe von `arrayQuery` enthalten.
  Die Liste der Rollenzuweisungen kann unter MongoDB leer sein. Die Zugriffskontrolle auf Inhalte
  über `accessArray()` funktioniert richtig. Siehe §19.

- **Noch nicht portierte Cronjobs** — `cronjobs/staticcache_cleanup.php`,
  `cronjobs/notification.php` und `cronjobs/session_gc.php` verwenden noch rohe
  Aufrufe von `arrayQuery` und bewirken unter MongoDB nichts oder schlagen fehl. Siehe §13.5 und §20.

- **Erweiterung `eztags`** — in dieser Installation nicht vorhanden. Die Collections `eztags` und
  `eztag_attribute_link` bräuchten vollständige Umsetzungen mit Aggregationspipelines mit `$lookup`
  für `fetchTagsByAttribute()`,
  `fetchObjectsByTag()` und alle Schreibpfade der Tag-Verknüpfungen, bevor die Erweiterung
  verwendet werden könnte. Siehe §25.2.

- **Optionsquelle über eine Datenbankabfrage in `enhancedselection2`** — der optionale Modus „DB query“
  als Optionsquelle ist für MongoDB bewusst abgeschaltet; vom Benutzer angegebenes rohes SQL
  ist grundsätzlich nicht kompatibel. Die Modi mit statischen Optionen und mit Template-Optionen
  funktionieren normal. Siehe §25.2.

- **Abfragen der Währungstabelle von `ezmultiprice`** — das Nachschlagen in `ezcurrencyname` kann
  leer zurückkommen, wenn diese Collection nicht migriert wurde. Die einfache Preisanzeige
  ist nicht betroffen. Siehe §25.1.

- **Der vollständige Bestellablauf im E-Commerce** (`ezbasket` → `ezorder` → Zahlung) und
  **die Zeitplanung der Blöcke** (`ezflow`) sind nicht von Anfang bis Ende getestet. Einzelne
  Lese- und Schreibzugriffe auf Collections sind gepatcht, aber die vollständigen Transaktionsabläufe wurden
  nicht gegen MongoDB durchgespielt. Siehe §16 und §25.1.




## Nachtrag: Korrekturen an Treiber und Kernel vom 18. bis 20. September 2026

Ergänzt vom Projekt zur Geschichte der Dokumentation. Durch diese Korrekturen erreichte eine Installation auf
MongoDB dieselben Seiten und dieselben Suchergebnisse wie dieselben Inhalte auf
MySQL. Siehe auch [die Chronik vom 16. bis 30. September 2026](../../geschichte/2026/2026-09b.md).

| Fehler | Symptom | Korrektur |
|---|---|---|
| Der Treiber verwarf stillschweigend SQL, das der Kernel ständig absetzt, oder konnte es nicht übersetzen: Zusätze für Zeilensperren, Bitmasken in `WHERE`, Aggregat-`SELECT`, `ORDER BY` und `LIMIT`, Unterabfragen mit `IN`, `TRUNCATE`, `DROP TABLE`, Sitzungs-`SET`, `UPDATE` mit Join, `INSERT` ohne Spaltenliste. | `SELECT COUNT(*)` las null, daher legte der Installer 533 doppelte Knoten an; ein Komma in Anführungszeichen in einem `SET`-Wert teilte die Zuweisung, sodass Layout-Collections eine abgeschnittene Abfrage behielten; boolesche Sortierrichtungen wurden als aufsteigend gelesen, daher kamen nach Datum sortierte Listen mit dem ältesten Eintrag zuerst. | Jede Form einer Anweisung wird übersetzt; Zählungen werden mit einer Zählung beantwortet; Sortierrichtungen werden beachtet; Latin-1-Bytes werden vor BSON umgewandelt; Schreibvorgänge, die eine Zeile mit dem Schlüssel `null` erfinden würden, werden abgelehnt und nennen ihren Aufrufer. |
| Der Schema-Writer übersprang den eindeutigen Index, wenn ein Primärschlüssel aus einer einzigen Spalte mit `auto_increment` bestand. | Nichts setzte den Primärschlüssel durch; der Seed-Loader speicherte von jeder Zeile, die zwei Erweiterungen mitliefern, eine zweite Kopie. | Der eindeutige Index wird angelegt; die Werte von `auto_increment` werden aus dem eigenen Primärindex der Collection gefüllt und von `lastSerialID` gemeldet. |
| `CREATE TABLE` zur Laufzeit hatte keinen Zweig. | Eine Erweiterung, die zur Laufzeit eine Tabelle anlegt, schrieb in eine Collection, die nie vorbereitet worden war; die IDs kamen leer zurück. | `CREATE TABLE` liest die Schlüsselklauseln (`PRIMARY KEY`, `UNIQUE KEY`, `KEY`, das `UNIQUE` einer Spalte) und legt die passenden Indizes unter den Namen an, die `expMongoSchema` schreibt (damit `PRIMARY` für die Erzeugung der IDs existiert). Spaltendefinitionen werden übersprungen; das erneute Anlegen eines gleichen Index bewirkt nichts. |
| Zahlen in Anführungszeichen wurden als Zeichenketten geschrieben, aber als Ganzzahlen gesucht. | `object_count = object_count + 1` schlug fehl (`$inc` lehnt eine Zeichenkette ab), und der Fehlschlag wurde als erfolgreiches `UPDATE` gemeldet. | Das Schema `.dba` ist die Quelle der numerischen Spalten (`numericColumns()`, `castSqlLiteralForColumn()`); eine Textspalte wie `remote_id` behält Zeichenketten. |
| Jeder Bezeichner kostete einen vollständigen Durchlauf der Collection (`nextSeqID()` mit `$group`). | Das Schreiben der mehreren hundert Wortverknüpfungen eines Artikels bedeutete mehrere hundert Durchläufe. | `nextAtomicID()` (O(1)); ein `INSERT` mit mehreren Zeilen reserviert den ganzen Block mit einem einzigen `$inc` (`reserveAtomicIDs()`). |
| 88 von 98 Pipelines waren ein umständlich geschriebenes `find()`. | Eine Aggregation kostet für dieselbe Arbeit ein Mehrfaches eines find. | Einfache Pipelines aus `$match`/`$sort`/`$project` laufen als `find()`; alles andere bleibt eine Pipeline. |
| Die Suchindizierung wurde unter MongoDB übersprungen, meldete aber Erfolg. | 14 von 284 Objekten indiziert. | Die beiden Schutzprüfungen wurden entfernt; die Indizierung nimmt den gebündelten Pfad (vier Anweisungen pro 500 Wörter); die Übersetzungen der Attribute werden einmal pro Objekt gelesen. Eine saubere Neuindizierung der Referenzinhalte dauert etwa 21 Sekunden und hinterlässt 237 indizierte Objekte (233 unter MySQL). |
| Der Pfadbauer der URL-Aliase gab bei einem Vorfahren ohne Aliaszeile und bei einem Knoten mit zwei Aliaszeilen auf. | Jeder Link wurde als `content/view/full/<id>` dargestellt. | Vorfahren ohne Alias werden übersprungen (eine Lücke mitten in einem Pfad schlägt weiterhin fehl); die Kandidaten werden auf die Zeilen eingegrenzt, an denen das nächste Element hängt. |
| Die Prüfungen der Bildreferenzen verwendeten einen Join, den MongoDB nicht hat. | Die Anzahl wurde als null gelesen, und 59 noch verwendete Bilddateien wurden gelöscht. | Ein Pfad ohne Join liest zuerst die Bildzeilen und filtert die Attribute, die sie nennen. Das Verschieben eines Bildes verschiebt auch die Referenzen seiner Übersetzungen. |
| Der unbeaufsichtigte Installer gab `iso-8859-1` an; das Such-Plugin wurde als fehlerhaft gemeldet, wenn es absichtlich abgeschaltet war; das Laden der URL-Aliase ignorierte den Sprachfilter. | | Gibt `utf-8` an; wird richtig gemeldet; der Filter wird angewendet. |

Der Installer akzeptiert jetzt außerdem `mongodb` als `--db` für `php bin/php/console exp:install --apply`.

## Verwandte Seiten

- [MongoDB als Datenbank (Funktion)](../../funktionen/6.0/mongodb-datenbankunterstuetzung.md)
- [Datenbanktreiber und Installer, September 2026](../../spezifikationen/6.0/datenbanktreiber-2026-09.md) (SQLite, PostgreSQL, MySQL und Oracle)
- [Exponential mit einem einzigen Befehl installieren](../../funktionen/6.0/installation-mit-einem-befehl.md)
- [Verhaltensänderungen vom 16. bis 30. September 2026](verhaltensaenderungen-2026-09b.md)
- [Chronik vom 16. bis 30. September 2026](../../geschichte/2026/2026-09b.md) und [Juni 2026, erste Hälfte](../../geschichte/2026/2026-06a.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
