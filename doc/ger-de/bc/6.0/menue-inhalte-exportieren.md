# Das Menü Export des Inhaltsbaums

Exponential 6.0.15 fügt dem Untermenü **Fortgeschritten** der Kontextmenüs der Administration ein Untermenü **Export**
hinzu: dem Menü des Inhaltsbaums und dem Menü „…“ jeder Zeile in der Liste der Unterelemente; dieselben Einträge
stehen im Menü der Schaltfläche **Import/Export** der Knotenansicht. Beide verwenden dasselbe Menü `Advanced`
(`popupmenu/popup_sub_advance_menu.tpl`), daher sind die Einträge an beiden Stellen gleich. Das Menü gibt es in
design/admin4 (und admin4l, das darauf zurückfällt), design/admin (eine identische Kopie) und im Design adminui von
exp_adminui (das die Templates von admin4 verwendet).

Jeder Eintrag öffnet das Formular seines Werkzeugs, **ausgefüllt** für das Element. Aus dem Menü heraus wird nichts
exportiert: Kein Eintrag sendet ein Formular ab, und keine Einstiegs-URL schreibt Inhalte oder startet einen Auftrag.
Der Benutzer prüft das Formular und startet den Export dort.

## Die Einträge

| Eintrag | Öffnet |
|---|---|
| Dieses Element als Paket… | den Paket-Assistenten für Inhaltsobjekte mit dem ausgewählten Element (nur der Knoten) |
| Dieses Element mit Teilbaum als Paket… | den Assistenten mit dem ausgewählten Teilbaum des Elements |
| Teilbaum als Paket, ohne Klassendefinitionen… | den Teilbaum; „Include class definitions“ im zweiten Schritt nicht angehakt |
| Teilbaum als Paket, mit allen Versionen… | den Teilbaum; „Alle Versionen“ im zweiten Schritt gewählt |
| Paket-Assistenten öffnen… | `package/create`, die Auswahl des Assistenten |
| Unterelemente als CSV… | das CSV-Formular von xrowextract: die direkten Unterelemente des Elements |
| Teilbaum als CSV… | das CSV-Formular: der ganze Teilbaum des Elements |
| Teilbaum einer Klasse ▸ | ein Eintrag pro Klasse mit Objekten unterhalb des Elements (die Anzahl in Klammern), jeder öffnet das CSV-Formular für den Teilbaum und diese Klasse; „Weitere Klassen…“ nach den ersten 12 |
| Teilbaum als CSV, alle Attribute… | das CSV-Formular mit jedem Attribut der Klasse als Spalten |
| Teilbaum als CSV, Migrationsspalten… | das CSV-Formular mit dem Spaltensatz „Migration“ (Identität, Elternknoten, Sprachen, Daten, jedes Attribut) |

Die Auswahl „Verwandte Objekte“ des Assistenten wird nicht als Eintrag angeboten: Der Paket-Handler für Inhaltsobjekte
liest sie nicht (die Relationen eines Objekts werden ohnehin mit seinen Attributen exportiert).

## Einstiegsparameter

### package/create

```
package/create/(creator)/ezcontentobject/(node)/<node id>[/(subtree)/1][/(classes)/0|1][/(versions)/current|all]
```

- `creator` muss `ezcontentobject` sein (der einzige Assistent mit einem Einstieg); `node` eine Knoten-ID, 1 bis
  2147483647 in einfachen Ziffern; `subtree` `0` oder `1` (Standard `0`: nur der Knoten); `classes` und `versions`
  belegen den zweiten Schritt vor.
- Jeder andere Wert ergibt „nicht verfügbar“. Ein Knoten, der nicht existiert, ergibt „nicht verfügbar“, einer, den
  der Benutzer nicht lesen darf, „Zugriff verweigert“, ebenso ein Benutzer ohne die Richtlinien unten.
- Der Assistent beginnt neu (seine Session-Daten werden ersetzt), mit dem Knoten oder Teilbaum in seinem ersten
  Schritt. Das Paket entsteht erst, wenn der Benutzer die Schritte durchläuft. Die Prüfungen stehen in
  `eZContentObjectPackageCreator::entryParameters()` und `::currentUserCanExportContent()`; die View ist
  `kernel/private/classes/views/package/create.php`.

### xrowextract/csv (xrowextract 2.5.11)

```
xrowextract/csv/(node)/<node id>[/(scope)/list|tree][/(class)/<class id>][/(columns)/all|migration]
```

- `scope` ist standardmäßig `tree` (der Teilbaum); ohne `class` wählt das Formular wie bisher (export.ini
  DefaultClassID, sonst die Klasse mit den meisten Objekten); ohne `columns` bleiben die gespeicherten Spalten des
  Benutzers für die Klasse.
- Die gespeicherten Einstellungen des Formulars werden ausgefüllt (Knoten, Bereich, Klasse; Filter und Offset geleert;
  Ausgabeeinstellungen beibehalten), und die View leitet auf `xrowextract/csv` weiter. Ungültige Werte und eine
  Klasse, die export.ini ExportClasses nicht anbietet, ergeben „nicht verfügbar“, ein Knoten, den der Benutzer nicht
  lesen darf, „Zugriff verweigert“. Code: `XrowExtractEntry`.
- `xrowextract/subtree_classes/<node id>` (Richtlinie xrowextract/csv) antwortet mit JSON für die Liste „Teilbaum
  einer Klasse“: `{ node, classes: [ { id, identifier, name, count } ], more, total }`, gezählt mit dem Lesezugriff des
  Benutzers; ein Knoten, der nicht existiert oder nicht lesbar ist, ergibt 404.

## Wer was sieht

- Die Paket-Einträge: `fetch( 'package', 'content_export_access' )`, wahr für einen Benutzer mit den Richtlinien
  package/create (eine Einschränkung CreatorType muss `ezcontentobject` enthalten, eine Einschränkung Type
  `contentobject`) und package/export (zum Herunterladen des Pakets; eine Einschränkung Type muss `contentobject`
  enthalten).
- Die CSV-Einträge: die Richtlinie xrowextract/csv.
- **Export** wird nur angezeigt, wenn es einen Eintrag für den Benutzer hat; ein Editor (ohne Richtlinien für Pakete
  oder xrowextract) sieht keine Änderung. Der Baum und die Liste der Unterelemente zeigen nur Elemente, die der
  Benutzer lesen darf, und jede Einstiegs-URL prüft den Knoten erneut.
- package.ini `[ExtensionExportSettings]` (das Exportieren von *Erweiterungen* als Pakete, standardmäßig aus, #198)
  gilt nicht: Inhaltspakete entstehen mit dem Assistenten für Inhaltsobjekte, den es nicht ausschaltet.

## Einträge aus einer Erweiterung hinzufügen

admininterface.ini `[AdditionalMenuSettings]`:

```
ExportMenuTemplateArray[]=popupmenu/myextension_export_menu.tpl        # export items (both menus)
ExportSubMenuTemplateArray[]=popupmenu/myextension_export_submenus.tpl   # their submenus, after each menu
ImportExportMenuTemplateArray[]=popupmenu/myextension_import_menu.tpl    # import and job items (Import/Export only)
```

Ein Template für Einträge prüft seine eigene Richtlinie und gibt für einen Benutzer ohne sie nichts aus, fügt seine
URLs zu `menuArray[$menu_id]['elements']` hinzu (mit `%nodeID%`), benennt seine Einträge `$item_prefix`-…, gibt einem
Untermenü die Tiefe `$sub_menu_depth` und öffnet ein Formular, nie einen Export oder Import. Ein Untermenü, dessen
Einträge erst beim Öffnen bekannt sind, setzt `menuArray[id]['onshow'] = function( menu, values, refresh )`:
ezpopupmenu.js ruft die Funktion beim Öffnen des Untermenüs auf; sie füllt das Menü (als Text, nie Markup aus Daten)
und ruft `refresh()` auf, das den neuen Einträgen ihre Rollen gibt und das Menü neu platziert. Das
`xrowextract-exportmenu.js` von xrowextract ist das Beispiel.

## Die Schaltfläche Import/Export der Knotenansicht

Die Aktionen der Knotenansicht haben eine Schaltfläche **Import/Export** nach Hochladen und vor Verschieben
(design/admin4, admin4l darüber, design/admin; in exp_adminui ein Eintrag des Menüs „…“ der Werkzeugleiste, nach
Hochladen). Sie öffnet ein Menü mit:

- den Export-Einträgen von Fortgeschritten > Export (dieselbe Definition, `popupmenu/popup_export_items.tpl`, siehe
  unten);
- **In dieses Element importieren…** (xrowextract 2.5.12, Richtlinie xrowextract/import): xrowextract/import mit dem
  Element als Ziel, nur an einem Container, unter dem der Benutzer Inhalte anlegen darf (die View prüft es erneut);
- **Export- und Importaufträge**, **Exportverlauf** und **Zeitpläne** (Richtlinien xrowextract/jobs, /history,
  /schedule): die Seiten von xrowextract, die die Aufträge, Läufe und Zeitpläne des Benutzers aufführen (mit
  xrowextract/all_jobs die aller Benutzer), nicht nur die dieses Elements.

Die Schaltfläche (`node/view/import_export_button.tpl`, identisch in design/admin und design/admin4) wird nur für ein
veröffentlichtes Objekt angezeigt (nie bei einem Entwurf oder auf den Bearbeitungsseiten) und nur, wenn die gemeinsame
Definition einen Eintrag für den Benutzer hat: Ein Editor sieht nichts, ein Benutzer nur mit xrowextract/csv sieht nur
die CSV-Einträge. Sie ist eine Menüschaltfläche `.a4-btn` (`aria-haspopup="menu"`, `aria-expanded`) mit dem
Bootstrap-Icon arrow-down-up: Enter oder Leertaste öffnen das Menü mit dem Fokus auf seinem ersten Eintrag, Escape
schließt es und kehrt zur Schaltfläche zurück. Das Menü ist `popupmenu/popup_import_export_menu.tpl` (ein Menü der
obersten Ebene unter den Popup-Menüs der Seite); die Schaltfläche übergibt ihm `%nodeID%` und `%canImport%`, und der
Eintrag zum Importieren wird nur angezeigt, wenn `%canImport%` 1 ist (Elementoption `shown_if` von ezpopupmenu.js).

### xrowextract/import (xrowextract 2.5.12)

```
xrowextract/import/(parent)/<node id>
```

Das Importformular beginnt mit diesem Knoten als Ziel (für die Session beibehalten, bis ein anderer gewählt wird), und
die View leitet auf das Formular weiter; nichts wird importiert, bevor der Benutzer eine Datei hochlädt, die Vorschau
prüft und sie anwendet. Ein Knoten, der nicht existiert, ergibt „nicht verfügbar“; einer, den der Benutzer nicht lesen
darf, der kein Container ist oder unter dem der Benutzer keine Inhalte anlegen darf, ergibt „Zugriff verweigert“
(`XrowExtractEntry::importRefusal()`).

### Eine Definition für beide Menüs

`popupmenu/popup_export_items.tpl` enthält die Einträge; das Untermenü Export der Kontextmenüs und das Menü
Import/Export binden sie mit `menu_id`, `item_prefix`, `sub_menu_depth` und `with_import` ein, und die Schaltfläche
bindet sie ein, um festzustellen, ob sie einen Eintrag hat. Templates von Erweiterungen aus `ExportMenuTemplateArray[]`,
`ExportSubMenuTemplateArray[]` und `ImportExportMenuTemplateArray[]` (Einträge für Import und Aufträge, nur das Menü
Import/Export) erhalten dieselben Parameter und registrieren ihre URLs in `menuArray[$menu_id]`; ein Untermenü eines
Eintrags folgt dem Muster `<menu id>Classes`, eines pro Menü.

## Tastatur und Platzierung

Die Untermenüs folgen dem Menümuster aus #165: Rechts, Enter oder Leertaste öffnen ein Untermenü und setzen den Fokus
hinein, Links oder Escape schließen es und geben den Fokus an seinen Eintrag zurück; Einträge mit einem Untermenü
haben `aria-haspopup="menu"` und `aria-expanded`. Ein verschachteltes Untermenü öffnet sich neben seinem
übergeordneten Menü auf der Seite mit Platz und wird in das Fenster verschoben, wenn keine Seite Platz hat (960px bei
200 % Zoom, Telefone); es rückt vom unteren Rand nach oben und scrollt, wenn es höher als das Fenster ist
(`_horizontalSubPlace()`, `_subVerticalPlace()` in ezpopupmenu.js).

Tests: `tests/tests/kernel/classes/expContentExportEntryTest.php`,
`tests/tests/design/AdminContextMenuNestedSubmenuTest.php`, das `tests/unit/EntryTest.php` von xrowextract.
