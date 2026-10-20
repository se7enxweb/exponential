---
title: Template-Ressourcen und Overrides
audience: developer, designer, app-creator
category: development
order: 420
summary: Wie aus einem Template-Namen eine Datei wird - die Ressourcen design: und file:, die geordneten Design-Basen eines Siteaccess (Website-Design, zusätzliche Designs, Standard-Design, jedes zuerst mit seinen Designs von Erweiterungen), Regeln in override.ini mit ihren Match-Schlüsseln und ihrer Reihenfolge und wie include und die Ansichtsfunktionen sie nutzen - mit der echten Auflösung auf dem Siteaccess site von alpha.
keywords: Template-Ressource, design:, file:, eZTemplateDesignResource, Design-Basen, SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignExtensions, override.ini, Match-Schlüssel, Reihenfolge der Overrides, include, node_view_gui, template resource, design bases, match keys, override order
type: help
---
# Template-Ressourcen und Overrides

Ein Template wird immer über eine **Ressource** benannt: `design:node/view/full.tpl`, `file:var/...`. Die Ressource macht aus dem Namen eine
Datei. Bei `design:` gehören dazu die Designs des Siteaccess, die Designs jeder Erweiterung und die Override-Regeln von `override.ini`. Diese
Seite zeigt, wie, mit der Auflösung, die der Siteaccess `site` von alpha vornimmt.

## Inhalt

1. [Ressourcen](#ressourcen)
2. [Die Design-Basen](#die-design-basen)
3. [Eine Datei ohne Overrides finden](#eine-datei-ohne-overrides-finden)
4. [Overrides](#overrides)
5. [Wer die Schlüssel setzt](#wer-die-schlüssel-setzt)
6. [Include und die Ansichtsfunktionen](#include-und-die-ansichtsfunktionen)
7. [Verwandte Seiten](#verwandte-seiten)

## Ressourcen

| Ressource | Klasse | Findet |
|---|---|---|
| `design:` | `eZTemplateDesignResource` (`kernel/common/eztemplatedesignresource.php`) | eine Datei unter `templates/` (oder `override/templates/`) der Design-Basen, nach den Override-Regeln |
| `file:` | `eZTemplateFileResource` (`lib/eztemplate/classes/eztemplatefileresource.php`) | einen Pfad, absolut oder relativ zur Wurzel der Installation |

`eZTemplate::factory()` registriert die Design-Ressource; die Datei-Ressource ist immer da und der Standard: Ein Name ohne Ressource wird als
Dateipfad gelesen. Die Beispiele dieser Seiten nutzen `file:`, um Templates außerhalb der Design-Ordner zu rendern; Code der Website nutzt
`design:`, damit Designs und Overrides greifen.

## Die Design-Basen

`eZTemplateDesignResource::allDesignBases()` baut die geordnete Liste der zu durchsuchenden Ordner (`findDesignBase()`):

1. die Designs, in dieser Reihenfolge: `[DesignSettings] SiteDesign`, dann `AdditionalSiteDesignList[]`, dann `StandardDesign`;
2. für jedes Design zuerst `extension/<name>/design/<design>` jeder Erweiterung in `design.ini [ExtensionSettings] DesignExtensions[]` (in
   dieser Reihenfolge), dann das `design/<design>` des Kernels; nicht vorhandene Ordner werden übersprungen.

Mit `DesignLocationCache=enabled` wird die Liste in einer Cache-Datei gespeichert (geleert mit der Cache-ID `design_base`). Ein nur lesendes
CLI-Skript gab die Liste für den Siteaccess `site` von alpha aus (`eZTemplateDesignResource::allDesignBases()`):

```text
siteaccess:               site
SiteDesign:               media
AdditionalSiteDesignList: ezwebin, standard, base
StandardDesign:           standard
DesignExtensions:         50 extensions

design bases, in search order (49):
  1  extension/sevenx_alpha_settings/design/media
  2  extension/sevenx_themes_media/design/media
  3  extension/sevenx_authentication_2fa/design/media
  4  design/media
  5  extension/cjw_newsletter/design/ezwebin
  6  extension/ezautosave/design/ezwebin
  7  extension/ezwebin/design/ezwebin
  8  design/ezwebin
  9  extension/expui/design/standard
 10  extension/ezjscore/design/standard
 11  extension/xrowmetadata/design/standard
 12  extension/ezmultiupload/design/standard
 13  extension/cjw_newsletter/design/standard
 14  extension/ezodf/design/standard
 15  extension/swark/design/standard
 16  extension/bcgooglesitemaps/design/standard
 17  extension/bcwebsitestatistics/design/standard
 18  extension/bccie/design/standard
 19  extension/xrowextract/design/standard
 20  extension/enhancedezbinaryfile/design/standard
 21  extension/enhancedselection2/design/standard
 22  extension/ezupdate/design/standard
 23  extension/git_manager/design/standard
 24  extension/syndication/design/standard
 25  extension/ngclasslist/design/standard
 26  extension/explayouts/design/standard
 27  extension/explayouts_content_browser_ui/design/standard
 28  extension/explayouts_ui/design/standard
 29  extension/explayouts_ui_api/design/standard
 30  extension/exp_enhanced_link/design/standard
 31  extension/ezownerchange/design/standard
 32  extension/recaptcha/design/standard
 33  extension/powercontent/design/standard
 34  extension/sevenx_dse/design/standard
 35  extension/sevenx_authentication_2fa/design/standard
 36  extension/cjw_themes_jumper/design/standard
 37  extension/cjw_tmv/design/standard
 38  extension/birthday/design/standard
 39  extension/exp_oe_tiptap/design/standard
 40  extension/ezstarrating/design/standard
 41  extension/ezgmaplocation/design/standard
 42  extension/ezautosave/design/standard
 43  extension/ezie/design/standard
 44  extension/eztags/design/standard
 45  extension/ezoe/design/standard
 46  extension/ezwt/design/standard
 47  design/standard
 48  extension/enhancedezbinaryfile/design/base
 49  design/base

```

Die erste Basis, die die Datei hat, gewinnt; `extension/sevenx_alpha_settings/design/media` kann also jedes Template des Media-Themes ersetzen
und das Media-Theme jedes Template von ezwebin oder des Standard-Designs.

## Eine Datei ohne Overrides finden

`eZTemplateDesignResource::fileMatch()` probiert `<base>/templates/<path>` in der Reihenfolge der Basen:

```text
resolution of design:<path> without overrides (first existing file wins):
  design:pagelayout.tpl
    -> extension/sevenx_themes_media/design/media/templates/pagelayout.tpl  (2 places tried)
  design:node/view/full.tpl
    -> extension/ezwebin/design/ezwebin/templates/node/view/full.tpl  (7 places tried)
  design:node/view/line.tpl
    -> extension/ezwebin/design/ezwebin/templates/node/view/line.tpl  (7 places tried)
  design:content/datatype/view/ezstring.tpl
    -> design/standard/templates/content/datatype/view/ezstring.tpl  (47 places tried)
```

Das Einbinden eines Design-Templates aus einem Template nutzt denselben Durchlauf. Hier das Ansichts-Template des Standard-Designs für den
Datentyp `ezstring`, in der 47. Basis gefunden:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
[{include uri='design:content/datatype/view/ezstring.tpl' attribute=$node.data_map.title}]
```

Ausgabe auf alpha:

```text
[
Why Should You Choose Strength Training?]
```

## Overrides

`override.ini` (des Siteaccess und jeder Erweiterung) hat eine Gruppe pro Regel. Die Regel für Artikel auf alpha, aus
`extension/sevenx_themes_media/settings/siteaccess/site/override.ini.append.php`:

```ini
[node/view/full#ng_article]
Source=node/view/full.tpl
MatchFile=node/view/full/full_ng_article.tpl
Subdir=templates
Match[class_identifier]=ng_article
Priority=1
```

Die Design-Ressource sammelt die Regeln pro `Source` (`eZTemplateDesignResource::overrideArray()`); die `MatchFile` wird in
`<base>/override/templates/` der Design-Basen gesucht, in der Reihenfolge der Basen. Wenn ein Template geholt wird, werden die Regeln für seinen
Namen der Reihe nach probiert, und **die erste Regel, deren Bedingungen alle passen, gewinnt**; eine Regel ohne Bedingungen ist ein Standard,
den eine spätere passende Regel noch ersetzen kann. Eine Bedingung `url_alias` passt, wenn der URL-Alias der Seite mit dem angegebenen Wert
beginnt; jeder andere Schlüssel muss dem Wert des Schlüssels gleichen (oder einer seiner Werte sein, wenn der Schlüssel ein Array ist, etwa
`class_group`). Regeln werden in der Reihenfolge der zusammengeführten `override.ini` genommen, oder nach einer numerischen `Priority` in der
Gruppe, wenn eine angegeben ist (niedrigere zuerst).

Auf alpha hat `node/view/full.tpl` 57 Regeln; die ersten:

```text
override rules for design:node/view/full.tpl: 57 (default file extension/ezwebin/design/ezwebin/templates/node/view/full.tpl)
  [node/view/full#showcase_s1_tour] extension/sevenx_alpha_settings/design/media/override/templates/node/view/full/showcase_s1_tour.tpl  if {"remote_id":"showcase-s1-tour"}
  [node/view/full#frontpage] extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_frontpage.tpl  if {"class_identifier":"frontpage"}
  [node/view/full#ng_article] extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_article.tpl  if {"class_identifier":"ng_article"}
  [node/view/full#ng_audio] extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_audio.tpl  if {"class_identifier":"ng_audio"}
  [node/view/full#ng_banner] extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_banner.tpl  if {"class_identifier":"ng_banner"}
  [node/view/full#ng_blog_post] extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_blog_post.tpl  if {"class_identifier":"ng_blog_post"}
  [node/view/full#ng_category] extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_category.tpl  if {"class_identifier":"ng_category"}
  [node/view/full#ng_contact_form] extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_contact_form.tpl  if {"class_identifier":"ng_contact_form"}
```

Dasselbe Skript ließ die Design-Ressource selbst die Datei für gegebene Schlüssel wählen (`eZTemplate::loadURIRoot()`, das das Template lädt,
ohne es zu rendern):

```text
design:node/view/full.tpl with keys class_identifier=ng_article
  -> extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_article.tpl
design:node/view/full.tpl with keys class_identifier=folder
  -> extension/ezwebin/design/ezwebin/override/templates/full/folder.tpl
design:node/view/full.tpl with keys class_identifier=ng_article;remote_id=showcase-s1-tour
  -> extension/sevenx_alpha_settings/design/media/override/templates/node/view/full/showcase_s1_tour.tpl
```

Im dritten Fall passen zwei Regeln; `showcase_s1_tour` kommt zuerst und gewinnt daher gegen die Klassenregel. Die Match-Schlüssel und ihre
Bedeutung stehen auf [Match-Schlüssel der Overrides](../../../en/us/help/designers/templates/override-match-keys.md), die Regeln der Reihenfolge auf
[Reihenfolge der Overrides](../../../en/us/help/designers/templates/override-ordering.md).

## Wer die Schlüssel setzt

- `content/view` setzt die Schlüssel des Knotens (`object`, `node`, `parent_node`, `class`, `class_identifier`, `section`, `url_alias`, `depth`,
  `viewmode`, `state`, `persistent_variable` und mehr), bevor es das Ansichts-Template holt (`eZNodeviewfunctions::generateNodeViewData()`), und
  bei einem Treffer im View-Cache erneut aus `content_info`.
- `node_view_gui`, `content_view_gui` und `attribute_view_gui` setzen die Schlüssel des Elements, das sie rendern
  (`kernel/common/ezobjectforwarder.php`), sodass ein Override von `node/view/line.tpl` auf die Klasse jedes Kinds in einer Liste prüfen kann.
- `layout/set` fügt `layout` hinzu; Fehlerseiten fügen `error_type` und `error_number` hinzu.
- Eine Ansicht oder Erweiterung kann eigene mit `eZTemplateDesignResource::instance()->setKeys()` hinzufügen.

## Include und die Ansichtsfunktionen

- `{include uri='design:parts/menu.tpl'}` nimmt einen vollständigen Ressourcennamen und übergibt seine anderen Parameter als Variablen; es fügt
  keine Schlüssel hinzu, aber die bereits für die Seite gesetzten Schlüssel gelten auch für den eingebundenen Namen.
- Die Funktionen `*_view_gui` bauen den Namen selbst aus ihrer Regel in `kernel/common/eztemplateautoload.php`: `node_view_gui` nutzt
  `node/view/<view>.tpl`, `attribute_view_gui` `content/datatype/view/<datatype>.tpl`, und jede führt das Template in einem eigenen Namensraum
  aus (`NodeView`, `ContentAttribute` ...).
- Eine fehlende Datei protokolliert eine Warnung und gibt nichts aus (siehe [Fehler und Fehlersuche](fehler-und-fehlersuche.md)).

## Verwandte Seiten

- [Grundlagen von override.ini](../../../en/us/help/designers/templates/override-ini-basics.md)
- [Match-Schlüssel der Overrides](../../../en/us/help/designers/templates/override-match-keys.md)
- [Reihenfolge der Overrides](../../../en/us/help/designers/templates/override-ordering.md)
- [Include](../../../en/us/help/designers/templates/include.md)
- [Template-Caches](template-caches.md) (die Caches für Overrides und Design-Basen)
- [Templates von Modulansichten](templates-von-modulansichten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
