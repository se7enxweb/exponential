# Der Tab Orte: Blättern und Sortieren

Der Tab **Orte** in der Ansicht eines Elements listet jede Stelle auf, an der ein Inhalt platziert ist. Früher listete
er alle auf, auf einem Bildschirm, ohne Möglichkeit, sie zu ordnen.

Für die drei oder vier Orte, die die meisten Elemente haben, ist das in Ordnung. Nicht in Ordnung ist es für Inhalte,
die wiederverwendet werden: eine Landingpage, die unter jedem Produkt platziert ist, ein gemeinsames Formular, ein
Textbaustein. Solche Objekte erreichen Tausende von Orten, und in dieser Größe wurde der Tab nicht nur lang — er wurde
gar nicht gerendert. Das Template holte die ganze Liste der Zuweisungen, fragte dann die Datenbank nach dem Pfad der
Vorfahren jeder Zeile, um ihn auszugeben, und fragte dann noch einmal nach der Zahl der Unterelemente jeder Zeile.
Fünfzigtausend Orte bedeuteten hunderttausend Abfragen vor dem ersten Byte, und die Anfrage starb.

Der Tab holt jetzt eine Seite und sortiert in der Datenbank. Lesen Sie diese Seite, wenn Sie Inhalte mit vielen Orten
haben oder die in der Tabelle unten genannten Admin-Templates überschreiben.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Der Tab Orte teilt in Seiten auf (`content.ini [LocationsSettings] LocationsPerPage`, Standard `25`) und sortiert in der Datenbank; `assignedNodes()` nimmt ein Fenster und eine Sortierung an; neu sind `assignedNodeCount()` und der Fetch `assigned_node_count`; die Beschriftungen der Tabs zählen mit einer Abfrage. |
| Wer betroffen ist | Overrides von `design/admin/templates/locations.tpl`, `window_controls.tpl` (admin und admin3) und `navigator/google.tpl`; Templates, die `rss/sortheader.tpl` einbanden (es ist nach `parts/sortheader.tpl` umgezogen). Bestehende PHP-Aufrufer von `assignedNodes()` sind unverändert. |
| Wie Sie es prüfen | `grep -n "function assignedNodeCount\|function sortColumnsForAssignedNodes" kernel/classes/ezcontentobject.php` |
| Wie Sie es beheben | Führen Sie Ihre Template-Overrides mit den ausgelieferten zusammen; ersetzen Sie `$node.object.assigned_nodes\|count` durch `fetch( 'content', 'assigned_node_count', ... )`. |

## Verwendung

### Blättern

Die Seitengröße steht in `content.ini`:

```ini
[LocationsSettings]
LocationsPerPage=25
```

Überschreiben Sie sie pro Siteaccess wie jede andere Einstellung. Die Seitennavigation erscheint nur, wenn es mehr als
eine Seite gibt, und es ist dieselbe Seitennavigation, die der Rest der Administrationsoberfläche verwendet.

Die Position wird in der Adresse als `(location_offset)` mitgeführt:

```
/content/view/full/133/(location_offset)/25/(tab)/locations
```

Sie heißt bewusst **nicht** `(offset)`. Die Ansicht eines Elements teilt ihre Liste der Unterelemente bereits mit
`(offset)` in Seiten auf, und denselben Namen zu teilen hätte bedeutet, dass das Blättern auf die zweite Seite der
Orte auch die Liste der Unterelemente weiterblättert, und umgekehrt.

### Sortieren

Jede Spaltenüberschrift außer dem Auswahlkästchen ist ein Link. Wer ihm folgt, sortiert die ganze Liste — nicht die
fünfundzwanzig Zeilen auf dem Bildschirm — und kehrt zur ersten Seite zurück, denn Seite elf der alten Reihenfolge
sagt nichts über die neue. Wer der bereits sortierten Überschrift folgt, kehrt die Reihenfolge um. Die sortierte
Überschrift ist mit dem eigenen Pfeil der Administrationsoberfläche markiert.

| Überschrift | Sortiert nach |
|---|---|
| Ort | dem Pfad des Ortes, alphabetisch |
| Unterelemente | wie viele Kinder jeder Ort hat |
| Sichtbarkeit | sichtbar, dann durch einen Elternknoten versteckt, dann versteckt |
| Haupt | absteigend zuerst der Hauptort |

Die Reihenfolge wird als `(location_sort)` und `(location_sort_order)` mitgeführt:

```
/content/view/full/133/(location_sort)/children/(location_sort_order)/desc/(tab)/locations
```

Sortieren und Blättern lassen sich kombinieren: Die Seitennavigation behält die Sortierung, und die Überschriften
behalten alles andere in der Adresse, den Tab eingeschlossen.

Ohne angeforderte Sortierung steht die Liste genau wie immer in der Reihenfolge des Baums.


## Was sich geändert hat

### `eZContentObject::assignedNodes()`

```php
function assignedNodes( $asObject = true, $checkVisibility = false, $offset = false, $limit = false,
                        $sortField = false, $sortOrder = 'asc' )
```

Die vier vorhandenen Argumente verhalten sich wie bisher, und jeder vorhandene Aufrufer — im Kernel sind es etwa
zwanzig — übergibt höchstens zwei, daher erhalten alle weiterhin die ganze Liste in der Reihenfolge des Baums.

`$offset` und `$limit` erreichen die Abfrage, nicht das Ergebnis. Nachträglich zu schneiden hätte den Fetch behalten,
und der ist die teure Hälfte.

`$sortField` ist ein Schlüssel von `eZContentObject::sortColumnsForAssignedNodes()`:

```php
static function sortColumnsForAssignedNodes()
{
    return array(
        'path'       => 'ezcontentobject_tree.path_identification_string',
        'children'   => 'sort_children_count',
        'visibility' => 'ezcontentobject_tree.is_invisible, ezcontentobject_tree.is_hidden',
        'main'       => 'sort_is_main',
    );
}
```

Alles, was kein Schlüssel dieser Tabelle ist, wird ignoriert, und die Liste kommt in der Reihenfolge des Baums zurück.
Das erlaubt dem Template, einen View-Parameter direkt weiterzugeben: Der Wert kommt aus der Adresszeile, und kein Teil
davon wird je in SQL eingefügt. `$sortOrder` wird ebenso auf eines von zwei Literalen reduziert.

Zwei der vier Spalten sind nicht gespeichert. `children` braucht eine Zählung der Kinder jedes Ortes und `main` einen
Vergleich mit dem Hauptknoten, daher werden sie der Abfrage **nur dann hinzugefügt, wenn nach dieser Spalte sortiert
wird** — die Kinder von fünfzigtausend Orten zu zählen ist nichts, was eine gewöhnliche Auflistung bezahlen sollte.

Jede Sortierung endet mit `path_string`. Ohne einen abschließenden eindeutigen Schlüssel überlässt eine Sortierung
nach einer Spalte, in der die meisten Zeilen gleich sind — jeder Ort sichtbar, ein Ort Hauptort —, die Reihenfolge der
gleichen Zeilen der Datenbank, und die darf bei jeder Abfrage anders wählen. Beim Blättern durch eine solche Liste
fallen dann einige Zeilen weg und andere wiederholen sich.

Der Mongo-Zweig leistet dieselbe Arbeit: dasselbe Fenster, dieselben vier Sortierungen, mit `sort_is_main` und
`sort_children_count`, die in der Pipeline gebildet werden.

### `eZContentObject::assignedNodeCount()`

Neu: `assignedNodeCount( $checkVisibility = false )`. Ein `COUNT(*)`, weil die Seitennavigation die Gesamtzahl braucht
und die Seite sie nicht enthält.

Das Template verwendet es auch für die Prüfungen „gibt es hier mehr als einen Ort“ — die zählten früher das geholte
Array, das jetzt eine einzige Seite ist, und hätten auf der letzten Seite einer langen Liste „nur ein Ort“ gemeldet
und die Bedienelemente deaktiviert.

### Fetch-Funktionen

```
{fetch( 'content', 'assigned_nodes',
        hash( 'object_id', 74, 'offset', 0, 'limit', 25,
              'sort_field', 'children', 'sort_order', 'desc' ) )}

{fetch( 'content', 'assigned_node_count', hash( 'object_id', 74 ) )}
```

`offset`, `limit`, `sort_field` und `sort_order` sind alle optional.

### `navigator/google.tpl`

Die gemeinsame Seitennavigation hat einen Parameter erhalten:

```
{include uri='design:navigator/google.tpl'
         offset_name='location_offset'
         page_uri=concat( '/content/view/full/', $node.node_id )
         item_count=$assignment_count
         view_parameters=$view_parameters
         item_limit=$locations_limit}
```

`offset_name` ist standardmäßig `'offset'`, daher bleiben die etwa zwanzig Templates, die diese Seitennavigation
einbinden, unberührt. Er steuert sowohl den Parameter, den die Seitennavigation schreibt, als auch den, den sie auslässt,
wenn sie die anderen View-Parameter weiterreicht — und das erlaubt einer Seite, zwei unabhängige Seitennavigationen zu
tragen.

Es wird kein `page_uri_suffix` angegeben: `view_parameters` trägt bereits `(tab)/locations`, und die Seitennavigation
hängt jeden Parameter außer ihrem eigenen Offset an, sodass ein Suffix den Tab zweimal in die Adresse setzen würde.

### Die Überschrift selbst

Die Überschriften sind `design/standard/templates/parts/sortheader.tpl`, dieselbe Komponente, die die RSS-Liste
verwendet, sodass beide Seiten gleich aussehen und sich gleich verhalten, statt dass jede ihre eigene entwickelt. Sie
nimmt den Spaltenschlüssel, die Beschriftung, die aktuelle Sortierung und ein `suffix`, das unverändert an die Adresse
angehängt wird — so behält der Tab Orte `(tab)/locations` in jedem Link einer Überschrift.

```
{include uri='design:parts/sortheader.tpl'
         key='children'
         label='Sub items'|i18n( 'design/admin/node/view/full' )
         sort=$locations_sort_state
         page_uri=$locations_sort_uri
         sort_name='location_sort'
         dir_name='location_sort_order'
         suffix=$locations_carried_parameters
         cell_class='tight'}
```

`sort` ist `hash( 'field', …, 'direction', 'asc'|'desc', 'opposite', … )`.

Die Überschrift ist ein einfacher Link, daher funktioniert sie auch mit ausgeschaltetem JavaScript.

Die Regeln für `th.sortable` / `th.sorted` / `.sort-arrow` stehen in `design/admin/stylesheets/content.css`, das jede
Seite der Administration lädt, sodass die beiden Listen nicht auseinanderlaufen können. Die sortierte Spalte behält
die gewöhnliche Farbe der Überschrift und wird durch die fette Beschriftung und den Pfeil markiert — nicht durch ein
Umfärben der Zelle.


## Dateien

| Datei | Änderung |
|---|---|
| `kernel/classes/ezcontentobject.php` | Fenster und Sortierung für `assignedNodes()`, `assignedNodeCount()`, `sortColumnsForAssignedNodes()` |
| `kernel/content/function_definition.php` | `assigned_nodes`, `assigned_node_count` |
| `kernel/content/ezcontentfunctioncollection.php` | `fetchAssignedNodes()`, `fetchAssignedNodeCount()` |
| `design/admin/templates/locations.tpl` | eine Seite, sortierbare Überschriften, Seitennavigation |
| `design/admin/templates/navigator/google.tpl` | `offset_name` |
| `design/standard/templates/parts/sortheader.tpl` | die gemeinsame sortierbare Überschrift, aus `rss/` hierher verschoben |
| `design/admin/templates/rss/list.tpl` | bindet sie von ihrem neuen Ort ein; ihre Regeln für Überschriften sind ins Stylesheet umgezogen |
| `design/admin/stylesheets/content.css` | `th.sortable`, `th.sorted`, `.sort-arrow` |
| `settings/content.ini` | `[LocationsSettings] LocationsPerPage` |


## Die Beschriftung des Tabs zählte die ganze Liste

Das Blättern machte die *Liste* günstig und ließ die *Beschriftung* darüber teuer. Sowohl
`design/admin/templates/window_controls.tpl` als auch die Kopie in `admin3` schrieben den Tab Orte so:

```
{'Locations (%count)'|i18n( ..., hash( '%count', $node.object.assigned_nodes|count ) )}
```

`assigned_nodes` ist `assignedNodes()` ohne Limit. Jede Ansicht jedes Knotens lud also jeden Ort dieses Objekts, um
eine Zahl auf einen Tab zu schreiben — egal welcher Tab offen war, auch die, die mit Orten nichts zu tun haben. Bei
einem Objekt mit fünfzigtausend Orten sind das genau die Kosten, die das Blättern vermeiden soll, bezahlt auf jeder
Seite der Administrationsoberfläche, die es zeigt.

Jetzt ist es `fetch( 'content', 'assigned_node_count', ... )`: ein `COUNT(*)`.

Der Tab Richtlinien daneben hatte dieselbe Form, nur schlimmer:

```
{def $assigned_policies = fetch( 'user', 'user_role', hash( 'user_id', ... ) )}
```

`user_role` führt `accessArray()` aus jeder Rolle des Benutzers zusammen und baut seine gesamten Berechtigungen in PHP
auf — hier nur angefragt, um sie zu zählen. Jetzt ist es die Summe von `policy_count` pro zugewiesener Rolle, also eine
kleine Abfrage pro Rolle und überhaupt keine Zeilen von Richtlinien.


## Tests

Ein privates Browser-Skript hat den Tab an einem Element mit 2001 Orten geprüft; es wird nicht mitgeliefert. Um ihn
von Hand zu prüfen, öffnen Sie `/content/view/full/<node id>/(tab)/locations` eines Elements mit mehr Orten, als eine
Seite fasst (mit weniger beweist die Prüfung nichts), und bestätigen Sie: Eine Seite wird gezeichnet und nicht die
ganze Liste; die Seitennavigation erscheint und verwendet `(location_offset)`; die zweite Seite enthält andere Zeilen;
jede Spalte sortiert in beide Richtungen, markiert sich und wird wie die RSS-Liste gezeichnet; ein Wert für
`(location_sort)`, der keine Spalte ist, wird ignoriert; die Seitennavigation behält die Sortierung und die
Überschriften behalten den Tab; und eine gewöhnliche Liste an anderer Stelle blättert weiterhin über `(offset)`.

Gemessen an einem Element mit 2001 Orten: Der Tab rendert in etwa einer Sekunde. Vorher wurde er gar nicht gerendert.

Prüfung ohne Schreibzugriff, dass die API vorhanden ist:
`grep -n "function assignedNodeCount\|function sortColumnsForAssignedNodes" kernel/classes/ezcontentobject.php`.

## Verwandte Seiten

- [Blättern, Sortieren und Seitengrößen](../../funktionen/6.0/seitenweise-listen-im-admin.md) und [Wo die Seitengrößen stehen](einstellungen-fuer-seitengroessen.md)
- [RSS: Die Liste der Feeds sortiert mit denselben Überschriften](../../funktionen/6.0/rss-podcast-und-feed-liste.md)
- [September 2026, erste Hälfte: 14. September](../../geschichte/2026/2026-09a.md#14-september-pdf-rss-und-die-rad-werkzeuge)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
