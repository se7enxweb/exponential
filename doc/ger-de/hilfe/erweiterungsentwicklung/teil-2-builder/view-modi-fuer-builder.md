---
title: View-Modi für Builder
audience: developer, designer, app-creator
category: extension-development
order: 120
summary: Ein View-Modus ist eine Art, Inhalte zu zeichnen - ganze Seite, Zeile in einer Liste, Einbettung in Rich Text. Die Modi, die der Kernel mitbringt, wie eine Seite und eine Liste einen auswählen, einen eigenen Modus hinzufügen und welche Modi zwischengespeichert werden.
keywords: View-Modus, Ansichtsmodus, eigener View-Modus, Einbettung, Liste, view mode, viewmode, full, line, embed, embed-inline, listitem, node_view_gui, content_view_gui, custom view mode, CachedViewModes, node/view
type: help
---
# View-Modi für Builder

Teil 2, Kapitel 12. Derselbe Artikel wird auf seiner eigenen Seite, in einer Nachrichtenliste und wenn ein Redakteur ihn
in einen anderen Artikel einbettet, unterschiedlich gezeichnet. Jede dieser Darstellungen ist ein **View-Modus**, und
jeder hat sein eigenes Template `node/view/<mode>.tpl`, das Overrides je Klasse ersetzen können. Dieses Kapitel zeigt
die Modi und wie Sie eigene hinzufügen.

## Die Modi, die der Kernel mitbringt

Das Standard-Design hat je Modus ein Template in `design/standard/templates/node/view/`:

| Modus | Verwendet für |
|---|---|
| `full` | die Seite eines Knotens: `/content/view/full/<node>` und jeden URL-Alias |
| `line` | einen Eintrag in einer Liste |
| `listitem` | einen kurzen Listeneintrag |
| `embed` | ein in Rich Text als Block eingebettetes Objekt |
| `embed-inline` | ein innerhalb einer Textzeile eingebettetes Objekt |
| `sitemap`, `search`, `thumb`, `text`, `text_linked`, `toolline`, `plain` | besondere Listen und Ausgaben |
| `print`, `pdf`, `execute_pdf` | Druck- und PDF-Ausgabe |

Ihr Design und Ihre Erweiterungen können weitere hinzufügen.

## Wie eine Seite ihren Modus wählt

Die Adresse trägt ihn: `/content/view/<mode>/<node>`. Ein URL-Alias ist die Vollansicht. In Templates wählen Listen den
Modus jedes Elements:

```text
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 10 ) )}
{foreach $children as $child}
    {node_view_gui content_node=$child view='line'}
{/foreach}
```

`node_view_gui` zeichnet einen Knoten in einem Modus über das Override-System, sodass ein `line`-Override für Artikel
hier automatisch greift. `content_view_gui content_object=$object view='embed'` tut dasselbe für ein Objekt ohne
Platzierung.

## Einen eigenen View-Modus hinzufügen

Angenommen, eine Startseite soll Teaser mit einem Bild zeigen: ein Modus namens `teaser`.

1. Legen Sie das Template für den Modus in Ihrem Design an:
   `extension/mysite_design/design/mysite/templates/node/view/teaser.tpl`

   ```text
   <div class="teaser">
       <h3><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></h3>
   </div>
   ```

2. Geben Sie Klassen, wo nötig, einen eigenen Teaser, mit Overrides auf `Source=node/view/teaser.tpl` (siehe
   [Override-Bedingungen](override-bedingungen.md)).
3. Verwenden Sie ihn: `{node_view_gui content_node=$child view='teaser'}`.
4. Leeren Sie den Template-Override-Cache (neue Dateien): `php bin/php/ezcache.php --clear-id=template-override,template`.

Für einen Modus, der nur aus Templates verwendet wird, ist keine Einstellung nötig. Zwei Listen nennen Modi für andere
Zwecke:

| Einstellung | Zweck |
|---|---|
| `content.ini [VersionView] ViewModes[]` | die Modi, die die Versionsvorschau der Administration anbietet (`content/versionview`) |
| `content.ini [embed] AvailableViewModes[]` und `[embed-inline]` | die Modi, die der Online-Editor für Einbettungen anbietet |

## Caching und View-Modi

Ganze Seiten werden im **View-Cache** gehalten. Welche Modi zwischengespeichert werden, legt `site.ini [ContentSettings]`
fest:

```ini
CachedViewModes=full;sitemap;pdf
```

Einen Modus, den Sie für Seiten hinzufügen, die über ihre Adresse geöffnet werden (zum Beispiel
`/content/view/print/<node>`), können Sie dort ergänzen. Listen, die innerhalb einer zwischengespeicherten Seite
gezeichnet werden, gehören zum Cache dieser Seite. Die Regeln für das Leeren des View-Caches stehen in
[Caching und Templates](caching-und-templates.md).

## Variablen in einem Template für einen View-Modus

| Variable | Was sie ist |
|---|---|
| `$node` | der gezeichnete Knoten |
| `$object` | `$node.object`, bei `content_view_gui` das Objekt |
| `$view_parameters` | die ungeordneten Parameter der Adresse (`/(offset)/20`) in einer Vollansicht |
| `$persistent_variable` | Werte, die ein Template der Vollansicht für das Pagelayout setzt |

## Verwandte Seiten

- Zurück: [Override-Bedingungen: Klasse, Knoten, Sektion, View-Modus und mehr](override-bedingungen.md)
- Weiter: [Inhalte in Templates abrufen](fetch-fuer-builder.md)
- [Versionsvorschau in anderen View-Modi](../../../../eng-us/features/6.0/version-preview-view-modes.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
