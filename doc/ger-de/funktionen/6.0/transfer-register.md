# Das Transfer-Register: Import- und Export-Werkzeuge, die einander kennen (6.0.15)

Lesen Sie diese Seite, wenn Sie ein Import- oder Export-Werkzeug schreiben oder pflegen (im Kernel oder in einer
Extension), oder ein Template, das auf eines verweist. Anwender lesen
[Welches Import- oder Export-Werkzeug brauche ich?](../../hilfe/import-oder-export-werkzeug.md).

## Kurz gesagt

| | |
|---|---|
| Was es ist | Eine Liste aller Import- und Export-Werkzeuge der Installation, geführt vom Service-Container des Kernels. Jedes Werkzeug meldet sich in `kernelservices.ini` an: was es tut, in welche Richtung, worauf, in welchen Formaten, wo es beginnt, welche Richtlinie es braucht, seine Hilfeseite und seine Partner für Hin- und Rückwege. |
| Wer es liest | Der Kasten „Suchen Sie …?" auf der Hauptseite jedes Werkzeugs, das Menü **Import/Export** der Inhaltsansicht (Einträge je Kontext), die Klassenseite, die Liste **Import und Export** im Setup-Menü und in den linken Menüs der Werkzeuge, die Werkzeugvorschläge der Suche im Kopf und das Hilfemenü **?** auf den Seiten eines Werkzeugs. |
| Was es zusichert | Kein Verweis auf ein Werkzeug, das nicht aktiv ist (Extension aus, Modul fehlt) oder das der Benutzer nicht benutzen darf (Richtlinie). Ein neues Werkzeug erscheint überall, sobald es einen Einstellungsblock hinzufügt; sonst wird nichts geändert. |
| Klassen | `expKernelTransferRegistry` (Service `transfer.registry`), `expKernelTransferTool` (eines je Werkzeug, Tag `transfer.tool`), `expKernelTransferOperator` (die Template-Operatoren) |

## Ein Werkzeug anmelden

Ein Block in `settings/kernelservices.ini` (die Werkzeuge des Kernels) oder in
`extension/<name>/settings/kernelservices.ini.append.php` (die einer Extension):

```ini
[Service_transfer.xrowextract]
Class=expKernelTransferTool
Factory=expKernelTransferTool::fromSettings
Arguments[]=transfer.xrowextract
Scope=worker
Tags[]=transfer.tool
Description=Download the items of a class, a folder or the whole site as CSV, XML or JSON, and import them back.
Name=Export (xrowextract)
Direction=both
Scopes[]=site
Scopes[]=subtree
Scopes[]=class
Formats[]=CSV
Formats[]=XML
Formats[]=JSON
EntryURL=xrowextract/csv
Permission=xrowextract/csv|xrowextract/import
HelpPage=ext/xrowextract/README.md
RoundTrip[]=transfer.xrowextract
RoundTrip[]=transfer.sqliimport
Keywords[]=spreadsheet
Keywords[]=Tabelle
Actions[]=class|export|Export the items of this class…|xrowextract/csv/(class)/%classID%
Weight=20
```

| Einstellung | Bedeutung |
|---|---|
| `Description` | Ein Satz: wofür es da ist (auch in `exp:services:list`) |
| `Name` | Sein Name in Listen |
| `Direction` | `import`, `export` oder `both` |
| `Scopes[]` | `site`, `subtree`, `object`, `class`, `data` (Datensätze, die keine Inhalte sind: Einsendungen, Abonnenten, Protokolle) |
| `Formats[]` | Die Dateiformate, die es liest oder schreibt |
| `EntryURL` | Seine Hauptseite; der erste Teil ist das Modul des Werkzeugs |
| `Permission` | `modul/funktion`, die zum Benutzen nötig ist; Alternativen durch `\|` getrennt; ohne sie genügt eine Richtlinie des Moduls |
| `Modules[]` | Weitere Module, deren Seiten zum Werkzeug gehören |
| `HelpPage` | Eine Hilfeseite (unter `doc/eng-us/`, `ext/<extension>/<pfad>` für die einer Extension), `#anker` erlaubt |
| `RoundTrip[]` | Die Werkzeuge, die lesen, was es schreibt, oder schreiben, was es liest (jede Seite darf die andere nennen) |
| `Keywords[]` | Aufgabenwörter, nach denen die Suche im Kopf es findet, auf Englisch und Deutsch |
| `Actions[]` | `kontext\|import oder export\|beschriftung\|URL[\|modul/funktion]`, ein Eintrag in einem Kontext: `node` (jedes veröffentlichte Objekt), `container` (ein Objekt, unter dem der Benutzer anlegen darf), `class` (eine Klassenseite), `setup` (die Setup-Seiten). Die URL darf `%nodeID%`, `%objectID%`, `%classID%`, `%classIdentifier%` enthalten; ein Eintrag, dessen Platzhalter der Kontext nicht liefert, entfällt. Die optionale Richtlinie ersetzt die des Werkzeugs. |
| `Weight` | Reihenfolge in Listen (kleiner zuerst, dann nach Name; Standard 100) |

Namen, Beschreibungen und Beschriftungen werden im Kontext `kernel/transfer` übersetzt (die Texte gehören in die
Übersetzungsdatei der Extension). `Enabled=false` in einer Überschreibung schaltet ein Werkzeug ab, wie jeden Service.

Nach dem Hinzufügen oder Ändern eines Blocks: `./console exp:services:compile` (das Deployment tut es), dann listet
`./console exp:services:list --tag=transfer.tool` die Werkzeuge, und `exp:services:check` prüft sie.

## Welche Werkzeuge gezeigt werden

`expKernelTransferRegistry::available()` behält ein Werkzeug, wenn

1. sein Block in diesem Siteaccess gelesen wird (eine nicht aktive Extension oder eine Access-Extension eines anderen
   Siteaccess fügt nichts hinzu),
2. sein Modul existiert (`eZModule::exists()`) und
3. der aktuelle Benutzer eine seiner `Permission`-Funktionen hat (oder, ohne sie, eine Funktion seines Moduls).

Das Register ist ein Service mit Worker-Scope: Es hält nur die Werkzeuge. Der Zugriff wird bei jedem Aufruf geprüft.

## Benutzung

PHP:

```php
$registry = expKernelTransferRegistry::instance();
$registry->available();                                           // id => expKernelTransferTool
$registry->select( array( 'direction' => 'import', 'scope' => 'subtree', 'format' => 'csv' ) );
$registry->actions( 'container', array( '%nodeID%' => 42 ) );     // context entries
$registry->related( 'transfer.xrowextract' );                     // for a "Looking for…?" box
$registry->search( 'import csv' );                                // by task words, best first
```

Templates (Operatoren des Kernels, jeder gibt nur die verfügbaren Werkzeuge als Hashes zurück):

```
{def $import_tools = transfer_tools( hash( 'direction', 'import', 'scope', 'subtree' ) )}
{def $entries = transfer_actions( 'container', hash( '%nodeID%', $node.node_id ), 'import' )}
{def $others = transfer_related( 'transfer.bccie' )}
{def $tool = transfer_tool( 'xrowextract' )}   {* also an inactive one: $tool.active *}
```

Ein Werkzeug-Hash hat `id`, `name`, `description`, `direction`, `scopes`, `formats`, `url`, `module`, `help_page`,
`help_url`, `round_trip`, `keywords` (dazu `partner` aus `transfer_related` und `active` aus `transfer_tool`). Ein
Eintrags-Hash hat `tool`, `name`, `context`, `direction`, `label`, `url`.

## Die gemeinsamen Template-Teile

Der Kernel liefert sie in `design/standard/templates/transfer/`, so finden jedes Admin-Design und jede Extension sie:

| Template | Verwendung | Parameter |
|---|---|---|
| `transfer/looking_for.tpl` | Der Kasten „Suchen Sie …?" oben oder unten auf der Hauptseite eines Werkzeugs | `tool` (seine Id) |
| `transfer/left_menu.tpl` | Die Liste **Import und Export** für ein linkes Menü (das Setup-Menü hat sie; das linke Menü einer Extension bindet sie nach den eigenen Links ein) | `current` (die Id des Werkzeugs, optional) |
| `transfer/actions.tpl` | Die Einträge eines Kontexts als Liste (die Klassenseite) | `context`, `values`, `direction` (optional) |
| `transfer/search_suggestions.tpl` | Die Werkzeuge zu den Suchwörtern, über den Suchergebnissen | `query` |

Sie benutzen die gemeinsamen Komponenten der Administration (`a4-notice`, `leftmenu-items`, `a4-plain`) und kein
eigenes CSS. Eine Extension bindet sie so ein:

```
{include uri='design:transfer/looking_for.tpl' tool='transfer.bccie'}
```

## Wo der Kernel es benutzt

- **Menü Import/Export eines Objekts** (`popupmenu/popup_export_items.tpl`): nach den Paket-Einträgen und den
  Menü-Templates der Extensions die Einträge für `node`, auf einem Container, in dem der Benutzer anlegen darf, die
  Einträge für `container`, und als letzten Eintrag **Welches Werkzeug brauche ich?**.
- **Klassenseite** (`class/view.tpl`): die Einträge für `class`.
- **Setup-Menü** (`parts/setup/menu.tpl`): die Liste **Import und Export**.
- **Suchergebnisse** (`content/search.tpl`): bis zu drei Werkzeuge, die zu den Suchwörtern passen, über den
  Ergebnissen.
- **Hilfemenü** (`expHelpContextRegistry::menu()`): auf der Seite eines Werkzeugs gehören die Seite „Welches Werkzeug“
  und die Hilfeseite des Werkzeugs zu den verwandten Themen.

## Die Werkzeuge des Kernels

`transfer.package` (Pakete), `transfer.rss_import`, `transfer.rss_export`, `transfer.infocollector` (Gesammelte
Informationen), `transfer.audit` (Audit-Export), `transfer.pdf` (PDF-Export). Extensions melden ihre an:
`transfer.xrowextract`, `transfer.bccie`, `transfer.sqliimport`, `transfer.syndication`, `transfer.newsletter`
(cjw_newsletter), `transfer.ezodf`.

## Hintergrund-Läufe und die Lauf-Historie

Ein Werkzeug führt seine Hintergrund-Arbeit über `expKernelTransferRunner` aus (die Job-Warteschlange, sonst die
Inhalts-Jobs des Kernels, sonst den eigenen Prozess) und hält jeden Lauf in `exp_transfer_run` fest; drei weitere
Schlüssel seines Blocks sagen wie: `Engine=`, `QueueJobType=` und `HistoryAllPermission=`. Siehe
[Transfer-Läufe](transfer-laeufe.md).

## Tests

`tests/tests/kernel/classes/expKernelTransferRegistryTest.php` (ohne Datenbank, Testsuite kernel-classes): Die
mitgelieferten Blöcke sind gültig und ihre Hilfeseiten existieren, dazu das Einlesen, die Verfügbarkeit, die Filter,
die Einträge, die Reihenfolge der Hin- und Rückwege, die Suche, die Themen des Hilfemenüs, die Operatoren und dass die
Hilfeseite jedes Werkzeug des Kernels auf Englisch und Deutsch aufführt.
