# explayouts_ui: die Admin-Bildschirme für Layouts

Diese Seite ist für Administratoren und Website-Entwickler, die Seitenlayouts verwalten. `explayouts_ui` fügt der
Administrationsoberfläche den Reiter **Layouts** hinzu. Es ist die Verwaltungsseite von Exponential Layouts:

- Listen von Layouts, gemeinsamen Layouts und Komponenten;
- die **Layout-Zuordnungen**, die entscheiden, welches Layout eine Seite bekommt;
- Importe, ein Template-Editor und eine Layout-Vorschau.

Jede Liste verlinkt in den visuellen Editor, den [explayouts_ui_api](explayouts_ui_api.md) ausliefert. Die Extension hat
keine eigenen Domänenklassen, nur schlanke Ansichtsklassen unter `classes/runnable/views/explayouts_ui/`. Ihre
Modulansichten sind Controller über den Diensten von `explayouts_core`, sodass die Bildschirme und der Editor immer
dieselben Daten zeigen.

Importiert am 30. Juli 2026 (1.0.0); 1.3.8 folgte am 2. Oktober 2026.

## Sich zurechtfinden

Öffnen Sie **Layouts** im oberen Admin-Menü (`/explayouts_ui/dashboard`). Der Reiter verlangt die Richtlinie
`explayouts/read`. Er wurde am 28. August 2026, vor 1.1.0, von „Exponential Layouts UI“ umbenannt.

| Link | Ansicht | Was Sie dort tun |
|---|---|---|
| Layouts | `layout_list` | Layouts als Karten durchsehen, sortieren, anlegen (**New layout**), ein Layout im Editor öffnen |
| Shared layouts | `shared_layouts_list` | Die Layouts, von denen andere Layouts Zonen erben (Kopf- und Fußbereich der Website). Ein gemeinsames Layout, auf das noch nichts verlinkt, wird ebenfalls aufgeführt (1.2.1), mit der Anzahl der Zonen, die darauf verlinken |
| Layout mappings | `rule_list` | Entscheiden, welches Layout für welche Seite ausgegeben wird; siehe unten |
| Components | `components` | Die Komponentenblöcke und wo sie verwendet werden, mit Links zum Bearbeiten; Namen verlinken auf den URL-Alias des Inhaltselements |
| Import | `transfer_import` | Layouts und Regeln aus Übertragungsdateien importieren |
| Template editor | `template_editor` | Layout-Templates bearbeiten (Wurzeln eingeschränkt, siehe Einstellungen) |
| Setup | `setup` | Einrichtung und Status der Layouts-Suite |

Weitere Ansichten: `layout_create`, `layout_edit`, `block_edit`, `rule_edit` und `layout_preview`
(`/explayouts_ui/layout_preview/<layout id>/<status>`).

Es gibt zwei Menüs, und Ihr Admin-Design verwendet eines davon:

- Die von `design/admin/templates/parts/explayouts_ui/menu.tpl` gezeichnete Seitenleiste hat fünf Links: Layout
  mappings, Layouts, Shared layouts, Components und Import. **Template editor** und **Setup** stehen nicht darin; öffnen
  Sie sie über die URL (`/explayouts_ui/template_editor`, `/explayouts_ui/setup`).
- Der Block `[Leftmenu_explayouts_ui_dashboard]` der mitgelieferten `menu.ini` führt Layout mappings, Layouts, Shared
  layouts (zeigt auf `layout_list`), Import, Template editor und Setup auf.

Öffnen Sie `/explayouts_ui/dashboard`, um zu sehen, welches Sie haben.

## Ein Layout einer Seite zuordnen

Der Bildschirm, der früher „rules“ hieß, ist seit 1.1.0 **Layout mappings**. Eine Zuordnung verbindet ein **Layout** mit
einem oder mehreren **Zielen** und **Bedingungen**, mit einer Priorität.

1. Öffnen Sie **Layouts > Layout mappings**.
2. Klicken Sie auf **Neue Regel**. Ein Bereich öffnet sich auf der Seite; eine neue Zuordnung beginnt mit einer Priorität,
   die tatsächlich greift.
3. Wählen Sie das Layout, fügen Sie ein Ziel und bei Bedarf Bedingungen hinzu.
4. Schalten Sie die Zuordnung ein und verwenden Sie dann **View in CMS** bei einem Knotenziel, um die System-URL des
   Knotens zu öffnen und das Ergebnis zu prüfen.

Um direkt zu den Zuordnungen eines Knotens zu springen, laden Sie die Liste mit den Abfrageparametern `TargetType`,
`TargetValue` und `RuleID` vor (das tun die Links **Map layout** und **Edit mapping**), zum Beispiel:

    /explayouts_ui/rule_list?TargetType=node&TargetValue=2

Einzelheiten:

- **Ziele**: ein Knoten, ein Teilbaum und andere Zieltypen. Der alte Zieltyp `content_node` wird als `node` gezeigt.
- **Bedingungen**: Klasse, Siteaccess, Abfrageparameter, Routenparameter, Zeit und andere. Klasse und Siteaccess sind
  Auswahllisten, kein freier Text. Die alten Namen von Bedingungen `ibexa_*` werden nicht mehr angeboten; eine bestehende
  Zuordnung, die einen verwendet, funktioniert weiter und wird unter ihrem kanonischen Namen gezeigt. Die
  Klassenbedingung heißt **class**.
- Jede Zeile einer Zuordnung hat Schnellaktionen: einschalten, ausschalten, Layout lösen, kopieren, löschen, einen
  Detailbereich (zugeordnetes Layout, Priorität, Schalter für eingeschaltet, Tabellen für Ziele und Bedingungen) und ein
  **Leeren des Caches** pro Zuordnung.

Änderungen an Zuordnungen entscheiden, welches Layout ausgegeben wird. Nach einer Massenänderung kann der Cache des
Resolvers unter `var/site/cache/explayouts/resolver/` ein Leeren brauchen; das Leeren des Caches pro Zuordnung tut das für
eine Zuordnung.

## Ein Layout in der Vorschau ansehen

`layout_preview` gibt ein Layout gegen einen repräsentativen Inhaltsknoten aus, ermittelt aus dem eigenen Ziel der
Zuordnung, sodass inhaltsgetriebene Blöcke echte Inhalte statt nichts zeigen.

- Die Vorschau setzt den Ansichtsmodus `content_info` auf `layout_preview`, sodass das öffentliche Seitenlayout Kopf- und
  Fußbereich weglassen kann, wenn das Layout selbst Zonen für Kopf- und Fußbereich enthält.
- Ein eigenes Template für die Vollansicht bricht die zirkuläre Referenz, die das Ergebnis des Moduls sonst erzeugen
  würde.
- **Load more** funktioniert in der Vorschau (1.3.1).

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `admininterface.ini` | `PaginationSettings` | `ItemsPerPage[explayouts_ui/<list>]` | `25` | Seitengröße der vier Listen |
| `explayouts.ini` (Extension `explayouts`) | `TemplateEditorSettings` | `AllowedTemplateRoots[]` | `design`, `extension` | Ordner, die der Template-Editor bearbeiten darf |
| `menu.ini` | `TopAdminMenu`, `Topmenu_explayouts_ui_dashboard`, `Leftmenu_explayouts_ui_dashboard` | `Tabs[]=explayouts_ui_dashboard` und die Menüblöcke | mitgeliefert | Reiter (Richtlinie `explayouts/read`) und Links des linken Menüs |
| `module.ini` | Modul `explayouts_ui` | Funktionen `read`, `edit` | mitgeliefert | Richtlinien |

Richtlinien (aus `module.php`):

- `read`: `layout_list`, `shared_layouts_list`, `rule_list`, `components`, `dashboard`, `layout_preview`;
- `edit`: `layout_create`, `layout_edit`, `rule_edit`, `block_edit`, `setup`, `transfer_import`;
- `template_editor` führt beide auf.

### Seitengröße der Listen

Die Listen der Layouts, gemeinsamen Layouts, Komponenten und Zuordnungen werden seitenweise angezeigt (1.2.0),
standardmäßig 25 pro Seite. Die Extension bringt diese Standards selbst mit; überschreiben Sie sie in
`admininterface.ini`:

```ini
[PaginationSettings]
ItemsPerPage[explayouts_ui/layout_list]=25
ItemsPerPage[explayouts_ui/shared_layouts_list]=25
ItemsPerPage[explayouts_ui/components]=25
ItemsPerPage[explayouts_ui/rule_list]=25
```

Siehe [Blättern in Admin-Listen](../seitenweise-listen-im-admin.md) und [Einstellungen zum Blättern](../../../bc/6.0/einstellungen-fuer-seitengroessen.md).

## Telefone und schmale Bildschirme

Der Kopf der Layout-Liste bricht unter 900 px um, setzt die Sortiersteuerungen in eine eigene Zeile und füllt unter
480 px die Breite, sodass **New layout** nie von einem Telefonbildschirm geschoben wird (1.2.3). Das Raster der Layouts
verwendet den kleineren Wert von 260 px und der verfügbaren Breite.

## Skripte, Stile und Schriften

Seit 1.3.7 (1. Oktober 2026) laufen die Seiten auf dem eigenen jQuery 4 der Administrationsoberfläche aus `ezjscore`, und
ihre Dateien tragen Namen von Exponential:

| Datei | Früherer Name |
|---|---|
| `design/admin/javascript/netgen/layouts-exponential.js` | `layouts-ibexa.js` |
| `design/admin/stylesheets/netgen/layouts-exponential.css` | `layouts-ibexa.css` |
| `design/admin/stylesheets/explayouts-ui.css` | `nglayouts-ui.css` |

`layouts-admin.js` wird nicht mehr geladen. 1.3.7 liefert außerdem die Schriften Roboto mit (keine 404 mehr) und
korrigiert die Aktionsbedingung der Regelliste; 1.3.8 verhindert, dass die seitenweiten Regeln des Stylesheets
Knotenansichten zurücksetzen. Die Dateien prüfen:

```bash
ls extension/explayouts_ui/design/admin/stylesheets extension/explayouts_ui/design/admin/stylesheets/netgen
```

## Sprachen

Jeder sichtbare Text ist ein Übersetzungstext (Englisch und Deutsch werden mitgeliefert). Der Reiter, sein Tooltip, der
Name des Navigationsteils und die Namen im linken Menü werden in den Kontexten `design/admin/pagelayout`,
`kernel/navigationpart` und `design/admin/parts/<menu>/menu` übersetzt.

## MongoDB

Die Übersichten der Komponentennutzung und der gemeinsamen Layouts lesen auch auf MongoDB (1.2.2). Der Join und das
Aggregat, die der Treiber nicht übersetzen kann, wurden durch Lesezugriffe ersetzt, die in PHP zugeordnet werden, sodass
Bildschirme, die früher nichts zeigten, jetzt die 49 Komponenten und die echten Anzahlen der Verweise zeigen.

## Verwandte Seiten

- [explayouts_ui_api: der Editor](explayouts_ui_api.md)
- [Exponential Layouts (Hinweis zur Verhaltensänderung)](../../../bc/6.0/layouts.md)
- [Spezifikation](../../../spezifikationen/6.0/explayouts-ui-api.md)
- [Template-Editor: Überschreibungen anlegen, ordnen und bearbeiten](../template-editor-ueberschreibungen.md)
- [Chronik](../../../geschichte/erweiterungen/explayouts_ui.md) und [Versionshinweise](../../../../changelogs/extensions/explayouts_ui.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/explayouts_ui.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2026-07](../../../geschichte/erweiterungen/monate/2026-07.md), [2026-08](../../../geschichte/erweiterungen/monate/2026-08.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
