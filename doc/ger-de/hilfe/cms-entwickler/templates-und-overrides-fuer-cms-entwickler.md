---
title: Templates und Overrides für CMS-Entwickler
audience: cms-developer
category: cms-development
order: 23
summary: Für CMS-Entwickler, wie eine Seite gezeichnet wird. Die Seite behandelt das Pagelayout und das Modulergebnis, die Design-Kette, Override-Regeln, die ein Template je Klasse, Knoten, Sektion oder Ansichtsmodus wählen, die Template-Sprache in Kürze, wo Ihre Templates liegen und wie Sie das Template hinter jedem Teil einer Seite finden.
keywords: Templates, TPL, Template-Sprache, Override, override.ini, Match, class_identifier, Pagelayout, module_result, Design-Kette, SiteDesign, AdditionalSiteDesignList, Design-Erweiterung, ShowUsedTemplates, Templates debuggen, node/view/full.tpl, template language, design chain, design extension, template debugging
type: help
---
# Templates und Overrides für CMS-Entwickler

Diese Seite richtet sich an Entwickler, die die Darstellung einer Website bauen. Sie zeigt den ganzen Mechanismus an
einer Stelle und verweist für Details auf die Seiten der Designer und des Buchs. Wenn Sie sich nur eines merken: Sie
ändern nie ein mitgeliefertes Template; Sie fügen ein eigenes Template hinzu und eine Regel, die sagt, wann es gilt.

## Inhalt

1. [Wie eine Seite gezeichnet wird](#wie-eine-seite-gezeichnet-wird)
2. [Die Design-Kette](#die-design-kette)
3. [Override-Regeln](#override-regeln)
4. [Die Template-Sprache in zehn Zeilen](#die-template-sprache-in-zehn-zeilen)
5. [Wo Ihre Templates liegen](#wo-ihre-templates-liegen)
6. [Das Template hinter einer Seite finden](#das-template-hinter-einer-seite-finden)
7. [Regeln für wartbare Templates](#regeln-für-wartbare-templates)
8. [Wo es in die Tiefe geht](#wo-es-in-die-tiefe-geht)

## Wie eine Seite gezeichnet wird

```text
Request -> siteaccess -> module/view (content/view/full/61)
   view template: node/view/full.tpl  -> override rule -> full/exp_event.tpl  -> $module_result.content
   pagelayout.tpl wraps it: <html>, header, menus, footer, {$module_result.content}
```

Jede Seite ist das Ergebnis einer View, gezeichnet von einem View-Template und eingesetzt in das **Pagelayout**
(`pagelayout.tpl`), das äußere Template des Designs. Das Pagelayout erhält das Ergebnis der View als `$module_result`
mit dem Inhalt, dem Pfad und Angaben zum Knoten ([Das Pagelayout](../designer/theme-design/das-pagelayout.md),
[Modulergebnis und Inhaltsinformationen](../designer/vorlagen/module-result-und-content-info.md)). Seiten, die mit
Exponential Layouts gebaut sind, ergänzen dieses Bild um Zonen und Blöcke
([Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md)).

## Die Design-Kette

Ein Template-Pfad wie `node/view/line.tpl` wird in einer Kette von Designs gesucht: im `SiteDesign` des Siteaccess,
dann in `AdditionalSiteDesignList[]`, dann im Standarddesign, jeweils auch innerhalb jeder aktiven Erweiterung, die
einen `design/`-Ordner dafür hat. Der erste Treffer gewinnt. So setzen sich die Templates Ihrer Design-Erweiterung gegen
die mitgelieferten durch, ohne diese anzufassen ([Die Design-Kette](../erweiterungsentwicklung/teil-2-builder/die-design-kette.md),
[Die Design-Kaskade](../designer/theme-design/die-design-kaskade.md)).

## Override-Regeln

Für Inhalte reicht die Kette allein nicht: Ein Artikel und eine Veranstaltung verwenden beide `node/view/full.tpl`.
Override-Regeln in `override.ini` wählen nach Bedingung eine andere Datei:

```ini
[exp_mysite_full_event]
Source=node/view/full.tpl
MatchFile=full/exp_event.tpl
Subdir=templates
Match[class_identifier]=exp_event
Priority=10
```

Die Match-Schlüssel sind `class_identifier`, `class`, `node`, `parent_node`, `parent_class_identifier`, `object`,
`remote_id`, `section`, `section_identifier`, `depth`, `url_alias`, `viewmode` und `view_offset`; alle Bedingungen
einer Regel müssen passen, und die erste passende Regel in der Reihenfolge gewinnt
([Override-Grundlagen](../designer/vorlagen/override-grundlagen.md), [Match-Schlüssel für Overrides](../designer/vorlagen/override-match-schluessel.md),
[Reihenfolge der Overrides](../designer/vorlagen/override-reihenfolge.md)). Passen Sie auf Bezeichner, nicht auf
numerische IDs, wenn eine Regel in jeder Installation funktionieren muss; `remote_id` ist der stabile Weg, ein
einzelnes Objekt anzusprechen.

## Die Template-Sprache in zehn Zeilen

```smarty
{* a comment *}
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 5 ) )}
{if $children|count()}
<ul>
    {foreach $children as $child}
        <li>{node_view_gui content_node=$child view='line'}</li>
    {/foreach}
</ul>
{/if}
{undef $children}
<p>{$node.data_map.intro.content|wash()}</p>
```

Funktionen stehen in geschweiften Klammern (`{if}`, `{foreach}`, `{def}`, `{include}`), Operatoren werden mit einem
senkrechten Strich angehängt (`|wash()`, `|ezurl`, `|count()`), und Templates haben keinen Zugriff auf PHP: Alles, was
sie brauchen, kommt aus Fetch-Funktionen, Operatoren und Variablen. `wash` maskiert Ausgaben; verwenden Sie es für alles,
was Sie ausgeben und was nicht bereits HTML aus einem Datentyp ist
([Was die Template-Sprache ist](../template-sprache/was-die-template-sprache-ist.md),
[Template-Operatoren](../template-operatoren/README.md)). Wenn Sie Smarty kennen, lesen Sie
[Smarty im Vergleich](../template-sprache/smarty-nebeneinander.md).

## Wo Ihre Templates liegen

In Ihrer Design-Erweiterung, nie in `design/standard` oder einem anderen mitgelieferten Design:

```text
extension/exp_mysite/
  design/exp_mysite/templates/pagelayout.tpl              your pagelayout
  design/exp_mysite/templates/node/view/line.tpl          a default for a view mode
  design/exp_mysite/override/templates/full/exp_event.tpl a template chosen by a rule
  design/exp_mysite/stylesheets/ ... javascript/ ... images/
  settings/design.ini.append.php                          registers the design in the extension
  settings/siteaccess/<public siteaccess>/override.ini.append.php   the rules
```

Templates enthalten keinen `<style>`-Block und kein `style=""`-Attribut; CSS gehört in Stylesheets, die über
`[StylesheetSettings]` geladen werden ([Kein Inline-CSS](../designer/theme-design/kein-inline-css.md)). Die Erweiterung
Schritt für Schritt bauen: [Ihre eigene Design-Erweiterung](../zwei-wege-zum-bauen/eigene-design-erweiterung.md).

## Das Template hinter einer Seite finden

In einer Entwicklungsinstallation:

```bash
./console exp:ini set site.ini/TemplateSettings/ShowUsedTemplates enabled global --allow-root-user
./console exp:ini set site.ini/TemplateSettings/ShowTemplatePathComments enabled global --allow-root-user
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Die Debug-Ausgabe listet dann jedes Template, das eine Seite verwendet hat, und die Override-Regel, die es gewählt hat,
und der Quelltext der Seite nennt das Template jedes Teils in HTML-Kommentaren
([Das Template hinter einer Seite finden](../trainer/entwickler/modul-03-designs-overrides-und-debugging/das-template-hinter-einer-seite-finden.md),
[Kommentare mit Template-Pfaden](../designer/vorlagen/template-pfad-kommentare.md)). Die Syntax eines Templates prüfen
Sie in der Shell mit `./console exp:eztemplatecheck --help`.

## Regeln für wartbare Templates

- Ein Template je Klasse und Ansichtsmodus; gemeinsame Teile in `{include}`-Dateien.
- Rufen Sie Daten im View-Template ab, nicht im Pagelayout, es sei denn, die Daten sind auf jeder Seite gleich
  (Menüs), und dann in einem Cache-Block ([Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md)).
- Geben Sie nur maskierte Werte oder Ausgaben von Datentypen aus; bauen Sie nie HTML durch Verketten aus
  Benutzereingaben.
- Verwenden Sie `i18n` für feste Texte ([Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md#template-texte)).
- Halten Sie Geschäftslogik in Operatoren oder Fetch-Funktionen Ihrer Erweiterung, nicht in langen Template-Bedingungen
  ([Stilrichtlinie für Templates](../designer/vorlagen/template-styleguide.md)).

## Wo es in die Tiefe geht

| Thema | Seite |
|---|---|
| Die Sprache, vollständig | [Die Template-Sprache](../template-sprache/README.md) |
| Jeder Operator | [Template-Operatoren](../template-operatoren/README.md) |
| Template-Referenz der Designer | [Templates im Überblick](../designer/vorlagen/templates-ueberblick.md) |
| Das Kapitel für Website-Bauer | [Templates für Website-Bauer](../erweiterungsentwicklung/teil-2-builder/templates-fuer-builder.md) |
| Der Kurs | [Modul 2: Die Template-Sprache](../trainer/entwickler/modul-02-die-template-sprache/README.md) |

## Verwandte Seiten

- [Ansichtsmodi](ansichtsmodi.md)
- [Inhalte in Templates abrufen](inhalte-in-templates-abrufen.md)
- [Templates und Design](../../anleitungen/templates-und-design.md): die Anleitung für Administratoren
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
