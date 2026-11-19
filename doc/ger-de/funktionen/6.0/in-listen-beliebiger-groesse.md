# Abfragen über ID-Mengen beliebiger Größe: expDBInList und InListChunkSize

Diese Seite ist für Sie, wenn Sie Kernel- oder Erweiterungscode schreiben, der die Datenbank nach Zeilen zu einer
Liste von IDs fragt, die mit der Installation wächst (die Benutzer einer Rolle, die Abonnenten eines Teilbaums, die
Objekte einer Löschung), wenn Sie Exponential auf Oracle betreiben oder wenn eine Seite einer großen Installation mit
ORA-00913, ORA-01795, „MySQL server has gone away“ oder „string or blob too big“ abbrach. Sie beschreibt die
Hilfsklasse `expDBInList`, die Einstellung `site.ini [DatabaseSettings] InListChunkSize`, den Kernel-Code, der sie
verwendet, und die Stellen, die geprüft und belassen wurden.

## Kurz gesagt

| | |
|---|---|
| Was sich änderte | Kernel-Code, der nach einer ID-Menge unbekannter Größe fragt, fragt in IN-Listen von höchstens 1.000 IDs, eine Anweisung je Liste, und prüft jedes Ergebnis, bevor er Zeilen daraus liest. |
| Warum | Eine Anweisung mit Zehntausenden IDs scheitert: Oracle nimmt höchstens 1.000 Werte je IN-Liste, MySQL und MariaDB lehnen eine Anweisung über `max_allowed_packet` ab, SQLite eine über `SQLITE_MAX_SQL_LENGTH` (1 MB). Das gescheiterte Ergebnis wurde dann als Zeilen gelesen: `(array)false` ist eine eigene Zeile. |
| Woher es kommt | Die Rollenseite einer Rolle mit Zehntausenden Zuweisungen auf Oracle (ORA-00913), behoben in Pull Request #365 von Ekkehard Dörre. |
| Für Code | `expDBInList::rows()`, `column()`, `count()`, `query()`, `chunks()`, `condition()` (`lib/ezdb/classes/expdbinlist.php`). |
| Einstellung | `site.ini [DatabaseSettings] InListChunkSize=1000`, je Datenbank `InListChunkSizes[<datenbank>]`; Oracle nie mehr als 1.000. |
| Wer handeln muss | Niemand. Erweiterungscode mit eigenen IN-Listen kann auf die Hilfsklasse umsteigen. |

## Woher es kommt

Ekkehard Dörre (CJW) fand den Fehler auf einer Oracle-Installation und behob ihn in
[Pull Request #365](https://github.com/se7enxweb/exponential/pull/365) von `se7enxweb/exponential`: `role/view` einer
Rolle, die Zehntausenden Benutzern zugewiesen ist, schrieb jede zugewiesene ID in eine einzige IN-Liste. Der
Oracle-Treiber teilt eine lange Liste in Listen von 1.000, verbunden mit OR, und Oracle lehnte die Anweisung dennoch
ab (ORA-00913 in `arrayQuery()`). Die Abfrage lieferte false, `(array)false` zählte als eine Zeile, jede zugewiesene
ID galt als Benutzergruppe, und die Knoten aller wurden in einer zweiten Anweisung derselben Größe abgefragt. Seine
Korrektur fragt in Listen von 1.000, prüft jedes Ergebnis mit `is_array()` und liest die Knoten der Gruppen nur
unterhalb der Gruppengrenze. Sein Test, `expRolePageAffectedTest`, lässt die Zählung der Seite gegen eine Datenbank
laufen, die die Anweisungen aufzeichnet, ohne Datenbankserver; die Tests dieser Arbeit verwenden dasselbe Muster.

Diese Seite macht die Erkenntnis systemweit: Die Abfrage in Listen wurde eine Kernel-Hilfsklasse, ihre Größe eine
Einstellung, und der Kernel-Code derselben Art verwendet sie. Danke, Ekke.

## Die Grenzen

| Datenbank | Was scheitert | Grenze |
|---|---|---|
| Oracle | ORA-01795 (mehr als 1.000 Werte in einer Liste), ORA-00913 (eine Anweisung aus vielen mit OR verbundenen Listen) | 1.000 Werte je Liste; `InListChunkSize` ist auf 1.000 begrenzt |
| MySQL, MariaDB | „MySQL server has gone away“, „Got a packet bigger than max_allowed_packet“ | `max_allowed_packet` (4 MB bis 64 MB in der Voreinstellung) |
| SQLite | „string or blob too big“ | `SQLITE_MAX_SQL_LENGTH`, 1.000.000 Byte in der Voreinstellung |
| PostgreSQL | keine feste Grenze für Literale; Planungszeit und Speicher wachsen mit der Liste | |
| MongoDB (SQL-Ausweichweg) | die Anweisung wird gelesen und übersetzt; lange Listen kosten Speicher | |

Tausend IDs sind etwa 10 KB SQL, weit innerhalb jeder Grenze, und eine Anweisung je tausend IDs kostet je einen
Umlauf: 50 Anweisungen für 50.000 zugewiesene Benutzer.

## Die Hilfsklasse

`expDBInList` (in `lib/ezdb`, mit dem Kernel automatisch geladen) führt `"<select> <spalte> IN ( ... ) <rest>"` einmal
je Liste aus und liefert **false, wenn eine Anweisung scheitert**, nie einen Teil der Zeilen oder eine Zählung aus
einem gescheiterten Ergebnis:

```php
$db = eZDB::instance();

// Zeilen: die Anweisung bis WHERE (oder AND), die IDs, die Spalte, dann 'int' oder 'string', dann was der Liste folgt
$rows = expDBInList::rows( $db, 'SELECT contentobject_id FROM ezuser WHERE', $objectIDs, 'contentobject_id' );
if ( $rows === false )
    return $unknown;                       // eine gescheiterte Liste: sagen, nicht daraus zählen

// ein Feld jeder Zeile
$ids = expDBInList::column( $db, 'SELECT node_id FROM ezcontentobject_tree WHERE', $objectIDs, 'contentobject_id',
                            'node_id', 'int', 'AND node_id = main_node_id' );

// COUNT(*) je Liste, summiert
$n = expDBInList::count( $db, 'FROM ezcontentobject WHERE section_id <> 3 AND', $objectIDs, 'id' );

// UPDATE oder DELETE je Liste; hält bei der ersten an, die scheitert
expDBInList::query( $db, 'DELETE FROM ezuser_role WHERE', $objectIDs, 'contentobject_id' );

// Zeichenketten maskiert und setzt die Hilfsklasse selbst in Anführungszeichen
$rows = expDBInList::rows( $db, 'SELECT id, remote_id FROM ezcontentobject WHERE', $remoteIDs, 'remote_id', 'string' );

// Code, der seine Anweisungen selbst baut: die Listen und die Bedingung einer Liste
foreach ( expDBInList::chunks( $nodeIDs, 'int', $db ) as $chunk )
    $sql = 'SELECT * FROM t WHERE ' . expDBInList::condition( $db, $chunk, 'node_id' );
```

Regeln, die aus einer Anweisung je Liste folgen:

- Die Werte werden eindeutig gemacht und umgewandelt (`(int)` oder `(string)`), bevor sie geteilt werden, sodass eine
  über ihren Wert gefundene Zeile einmal zurückkommt. Keine Werte: keine Anweisung, `array()` von `rows()` und
  `column()`, `0` von `count()`, `true` von `query()`.
- `ORDER BY`, `LIMIT`, `GROUP BY` und Aggregate im Rest gelten für jede Liste für sich. Sortieren Sie die Zeilen
  danach; verwenden Sie `count()` (eine Summe von `COUNT(*)`) statt `COUNT( DISTINCT ... )`. Eine Abfrage, die über
  die ganze Menge blättert oder sortiert, lässt sich so nicht teilen (siehe [Belassen](#belassen)).
- Ein Schreiben über `query()` ist eine Anweisung je Liste und über die Listen hinweg nicht atomar: Führen Sie es in
  der Transaktion des Aufrufers aus, wenn das zählt.
- Die Bedingung schreibt `generateSQLINStatement()` des Treibers, also schreibt jede Datenbank sie auf ihre Weise.

`expDBInList::chunkSize( $db )` ist die Listengröße einer Verbindung; `setChunkSize( $n )` legt sie für Tests und
Skripte fest, `setChunkSize( null )` kehrt zu den Einstellungen zurück.

## Die Einstellung

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
# höchstens so viele Werte in einer IN-Liste (Voreinstellung 1000)
InListChunkSize=1000
# je Datenbank, unter dem Namen, den der Treiber nennt: mysql, postgresql, sqlite, oracle, mongo, pdo_<treiber>
InListChunkSizes[]
InListChunkSizes[postgresql]=5000
*/ ?>
```

Oracle (`oracle`, `pdo_oci`) nimmt nie mehr als 1.000, was immer eingestellt ist. Ein Wert, der keine positive Zahl
ist, gilt als Voreinstellung. Die Einstellung wird einmal je Prozess gelesen;
[die erzeugte Einstellungsseite](../../beispiele/ini-einstellungen/site/DatabaseSettings.md) zitiert sie.

## Der Kernel-Code, der sie verwendet

| Code | Wonach er fragt | Vorher |
|---|---|---|
| `expRolePage::affected()` (role/view) | die Benutzer unter den zugewiesenen Objekten, die Knoten der Gruppen | eigene Kopie der Abfrage in Listen aus #365 |
| `eZSubtreeNotificationRule::fetchUserList()` | die Knoten, Rollen und Regeln der Abonnenten eines Teilbaums | eine Liste je Anweisung; eine gescheiterte Anweisung endete in einem TypeError in `array_filter()` |
| `eZContentObject::fetchIDArray()` | Objekte nach ID, beliebig viele | eine Liste; eine leere Liste war eine scheiternde Anweisung |
| `eZContentObjectTreeNode::findMainNodeArray()`, `fetchAliasesFromNodeList()`, `getParentNodeIdListByContentObjectID()` | Hauptknoten, URL-Pfade, Elternknoten einer beliebigen Liste | eine Liste; die letzte lief über false |
| `ezpSessionHandlerDB::deleteByUserIDs()` | die Sitzungen der Benutzer einer Massenlöschung | eine Liste; ein gescheitertes Lesen lief über false |
| `eZSearchEngine::updateObjectsSection()` | die Zeilen des Suchindex der Objekte, die die Sektion wechselten | ein UPDATE für jedes Objekt, das eine Migration übergab |
| Teilbaum kopieren: `eZContentObjectTreeNodeOperations::copySubtree()`, `content/copysubtree`, `bin/php/ezsubtreecopy.php` | die Relationen jedes kopierten Objekts | eine Liste für den ganzen Teilbaum (die Kommandozeile hat keine Größengrenze) |
| `TrashList::classOptions()` auf MongoDB | die Klassen jedes Objekts im Papierkorb | eine Liste; ein gescheitertes Lesen des Papierkorbs wurde Objekt 0 |
| `expUserApprovalSection::changes()` | die Objekte unter einer Mitarbeitergruppe, die die Sektion wechseln | ein COUNT über jedes Objekt des Teilbaums |
| `expStaticPublishQueue::nodes()` | die statischen Seiten der Knoten einer Veröffentlichung | eine Liste aller Knoten, die das Cache-Leeren nennt |

Verhalten, Templates und Rückgabewerte bleiben sonst gleich: Ein Abruf, der bei einer gescheiterten Anweisung false
lieferte, tut es weiter; einer, der über das gescheiterte Ergebnis lief, liefert jetzt eine leere Liste.

## Belassen

Kernel, `lib/` und die mitgelieferten Kernel-Erweiterungen wurden nach IN-Listen durchsucht, die mit
`generateSQLINStatement()` oder `implode()` gebaut werden (etwa 100 und 120 Zeilen). Diese wurden geprüft und mit
Grund belassen:

| Art | Stellen | Warum sie bleiben |
|---|---|---|
| Schon in Listen | Inhaltsaufträge (`expContentJob*`: Stapel von `[ContentJobSettings] BatchSize`, 50), `eZContentObjectTreeNode::assignSectionToSubTree()` (100 je Anweisung), `eZContentObject::attributeListConditions()` (500 IDs je Liste), Hilfe-Inhaltsablage, Paketvergleich, Audit-Indexer, Prüfsperre, Aufräumen der Benachrichtigungsereignisse (100), Inhaltsprüfungen (200), Aufräumen von Warenkörben und Produktsammlungen (ein Stapel von `$limit`) | schon begrenzt |
| Eine Seite oder feste Grenze | Knoten der Gruppen in `expRolePage` (höchstens `AFFECTED_GROUP_LIMIT`, 200), `eZRole::assignmentPageFromRows()`, Liste der Benachrichtigungsregeln, Prüfung der Rückwärtsrelationen (`LIST_LIMIT`; der Rest sind Unterabfragen), Vorfahren im Papierkorb, nicht aktivierte Benutzer, Namen der Benutzerpflege, Moderationsliste, Sitzungsübersicht, Spalten der Unterelemente, Prioritäten der Kinder in ezjscore, Content Finder | die Liste ist eine Seite |
| Die eigenen Mitgliedschaften eines Benutzers | `eZRole::fetchByUser()` und `fetchIDListByUser()`, `eZUser::limitList()` und `generateGroupIdList()`, Rabattregeln, `expUserManager::roleAssignments()`, `expUserGroupManager`, Benutzerbereich der Debug-Leiste, die Pfadknoten der Benachrichtigung | die Gruppen und Knoten eines Benutzers oder die Tiefe eines Knotens |
| Das Schema | Listen von Klassen, Klassengruppen, Sektionen, Zuständen, Sprachen, Status und Bezeichnern (Klassenfilter, Suchfilter, Mehrwertsteuerregeln, Klassenfilter der Schlagwörter, Entfernen von Zustandsgruppen, Inhaltseinschränkungen) | so viele Werte, wie es Klassen, Sektionen oder Zustände gibt |
| Ein Filter in einer sortierten, blätternden oder zählenden Abfrage | Papierkorbfilter (`ContentObjectIDList`, IDs von „gelöscht von“), Bedingungen der Baumabrufe, Moderationsfilter | Teilen würde das Blättern und die Reihenfolge ändern; die Größe kommt aus einem Formular |
| Ein Objekt oder ein Element | Schlagwörter und URLs eines Attributs, Teilnehmer eines Kollaborationselements, Lesezeichen eines Benutzers, `expUserManager::administratorIDs()` (die Konten unter den Administratorgruppen) | von Natur aus klein |
| Demodaten | `expCollaborationSampleData` | nur die erzeugten Beispieldaten |

Das Muster `(array)$db->arrayQuery( ... )`, das eine gescheiterte Abfrage in eine falsche Zeile verwandelt, kommt
außerhalb der obigen Stellen etwa 90 Mal vor, meist in Berichten (Inhaltsprüfungen, Paketvergleich,
Migrationssimulation), wo eine gescheiterte Anweisung schon als Fehler erscheint. Diese bleiben; neuer Code prüft das
Ergebnis mit `is_array()` oder verwendet `expDBInList`, das nie ein gescheitertes Ergebnis herausgibt.

## Tests

- `tests/tests/lib/ezdb/expDBInListTest.php`: Listen von 1.000, 1.001 und 2.500 Werten, ein Fehler in einer mittleren
  Liste, keine Werte, eindeutige und maskierte Werte, der Rest, Zählungen (auch in Oracles Großschreibung), die
  Einstellungen je Datenbank und die Oracle-Grenze, Schreiben.
- `tests/tests/kernel/classes/expDBInListCallersTest.php`: die Objekt- und Knotenabrufe, die Sektionsänderung des
  Suchindex, das Löschen der Sitzungen, die Empfänger der Teilbaum-Benachrichtigung und die Klassen des Papierkorbs
  auf MongoDB, gegen eine Datenbank, die die Anweisungen aufzeichnet: Listen von höchstens 1.000 IDs, die Bedingungen
  nach jeder Liste, eine gescheiterte Liste. Die Warteschlange des statischen Veröffentlichens läuft in
  `expStaticPublishSqliteTest` auf SQLite; das Kopieren eines Teilbaums und die Zählung der Freigabesektion gehen
  über dieselben Aufrufe der Hilfsklasse.
- `tests/tests/kernel/classes/expRolePageAffectedTest.php` (aus #365): die Zählungen der Rollenseite,
  Listengrenzen und Fehler.

Alle laufen ohne Datenbank (`vendor/bin/phpunit tests/tests/lib/ezdb/expDBInListTest.php`).

## Verwandte Seiten

- [Berechtigungsprüfungen für Benutzer mit vielen Rollenzuweisungen](berechtigungspruefung-bei-vielen-zuweisungen.md)
- [Die Benutzer und Gruppen einer Rolle, eine Seite auf einmal](seitenweise-rollenzuweisungen.md)
- [Unterstützung für die Datenbank SQLite](sqlite-datenbank.md)
- [MongoDB als Datenbank](mongodb-datenbankunterstuetzung.md)
