---
title: Ein Rundgang durch das Theme starter
audience: designer
category: theme-design
order: 120
summary: Das Design starter (Erweiterung cjw_themes_jumper) als Beispiel eines Themes, das Inhalte über Remote-ids findet, Verhaltenseinstellungen pro Siteaccess hält, seinen Rahmen mit Layouts zeichnet und eigene Bildaliase und eine eigene Einstellungsdatei mitliefert.
keywords: Theme starter, cjw_themes_jumper, Design starter, Remote-id, ExtensionSettingsSiteAccess, cjw_jumper.ini, Rahmen aus Layouts, Offcanvas-Menü, Bildaliase, globale und Siteaccess-Einstellungen, starter theme, starter design, remote id, layouts chrome, offcanvas menu, image aliases, global and siteaccess settings
type: help
---
# Ein Rundgang durch das Theme starter

Diese Seite ist für Designer, die sehen möchten, wie sich ein Theme zwischen Installationen portabel machen lässt. Die Erweiterung
`cjw_themes_jumper` (Composer-Paket `se7enxweb/cjw_themes_jumper`) trägt das Design `starter`, das die Siteaccesses `starter` und
`starter_ger` der Demonstrationsinstallation verwenden. Die Seite beantwortet: wie das Theme seine Einstellungen aufteilt, wie es Inhalte
ohne Knoten-ids findet und wie es die Seite mit der Layout-Engine zeichnet.

## Inhalt

1. [Was es mitbringt](#was-es-mitbringt)
2. [Globale Einstellungen und Siteaccess-Einstellungen](#globale-einstellungen-und-siteaccess-einstellungen)
3. [Inhalte über Remote-id, nie über Knoten-id](#inhalte-über-remote-id-nie-über-knoten-id)
4. [Eine eigene Einstellungsdatei](#eine-eigene-einstellungsdatei)
5. [Das Pagelayout zeichnet ein Layout](#das-pagelayout-zeichnet-ein-layout)
6. [Stylesheets](#stylesheets)
7. [Ideen, die sich zu übernehmen lohnen](#ideen-die-sich-zu-übernehmen-lohnen)

## Was es mitbringt

Laut seinem README: das Design `starter` (Pagelayout, Kopfbereich, Offcanvas-Menü, Brotkrumenpfad, Prefooter und Fußbereich; Ansichten
full, line, list, tile, teaser, slide, gallery und embed), Templates für die Layout-Blöcke und ihre View-Typen, eigene XML-Tags mit
schlichten Darstellungen für jedes andere Design (in `design/standard/`), Bildaliase `cjw_i30` bis `cjw_i1920`, ein Proxy-Modul für
Kartenkacheln (`osm_proxy`), damit Besucher den Kachelserver nie kontaktieren, und deutsche Texte für das Design.

## Globale Einstellungen und Siteaccess-Einstellungen

Die Erweiterung ist für jeden Siteaccess aktiv (`ActiveExtensions[]`), aber nur wenige Einstellungen sind global:

- **global** (`settings/`): `design.ini` (das Design), `content.ini` (die eigenen XML-Tags, damit jeder Siteaccess die Inhalte öffnen und
  speichern kann, ohne Tags zu verlieren), `image.ini` (Aliase), `cjw_jumper.ini` und `osm_proxy.ini`;
- **pro Siteaccess** (`settings/siteaccess/cjw_starter/`): `site.ini`, `override.ini`, `module.ini`, `collect.ini`, `explayouts.ini`,
  alles, was ändert, wie sich eine Website verhält.

Die Siteaccesses `starter` holen den Teil pro Siteaccess mit einer Zeile herein:

```ini
[SiteAccessSettings]
ExtensionSettingsSiteAccess=cjw_starter
```

Ergebnis: Die Administration und andere Websites sind von den Overrides des Themes nicht betroffen, und ein zweiter Sprach-Siteaccess
braucht keine Kopien.

## Inhalte über Remote-id, nie über Knoten-id

Knoten-ids unterscheiden sich zwischen Installationen. Das Theme benennt Inhalte daher über die **Remote-id**, die gleich bleibt, wenn das
Inhaltspaket anderswo installiert wird:

- in `override.ini`: `Match[remote_id]=cjw-starter-o-root` für die Vollansicht der Wurzelseite;
- in `cjw_jumper.ini`: `[SiteInfo] NodeRemoteID=cjw-starter-n-65`;
- in Templates: `fetch( 'content', 'node', hash( 'remote_id', ezini( 'SiteInfo', 'NodeRemoteID', 'cjw_jumper.ini' ) ) )`.

Seine Einstellungsdatei sagt: „Anything not found is left out of the page; the chrome then builds itself from the content tree alone.“ Das
ist eine gute Regel für jedes Theme.

## Eine eigene Einstellungsdatei

`cjw_jumper.ini` enthält die Entscheidungen des Designs: `[Header]` (die Klassen von Kopfbildern, Ersatzbilder, Klassen für Landingpages und
große Kopfbereiche, Logobilder), `[Menu]`, `[LayoutSettings]` (welche Layouttypen und Namenspräfixe zum Design gehören, welche Modul-Views
ohne Layout gezeichnet werden). Die Templates lesen sie mit `ezini( ..., 'cjw_jumper.ini' )`. Eine Theme-Einstellung in einer eigenen
INI-Datei lässt sich pro Siteaccess ändern, ohne Templates anzufassen. Siehe [Design-Erweiterungen](design-erweiterungen.md).

## Das Pagelayout zeichnet ein Layout

`design/starter/templates/pagelayout.tpl` dokumentiert oben seine Variablen (alle mit dem Präfix `pl_`) und dann:

1. holt den aktuellen Knoten aus `$module_result.content_info.node_id`;
2. löst bei einer Vollansicht das Layout des Knotens mit `fetch( 'explayouts', 'resolve_layout_for_node', hash( 'node_id', ... ) )` auf und
   behält es nur, wenn sein Typ und sein Name zu diesem Design gehören (`[LayoutSettings]` von `cjw_jumper.ini`);
3. baut sonst im Template selbst ein Layout für den Rahmen: Kopfzone, Hauptzone mit einem Block `full_view`, Fußzone mit einem
   zweispaltigen Prefooter;
4. zeichnet es mit `{include uri='design:explayouts/layout.tpl' layout=$pl_layout}` oder zeichnet bei den als schlicht aufgeführten
   Modul-Views Kopfbereich, `#main` mit `{$module_result.content}` und Fußbereich direkt.

So zeichnen dieselben Block-Templates von Redakteuren gebaute Landingpages und gewöhnliche Seiten. Siehe
[Layouts für Designer](layouts-fuer-designer.md).

## Stylesheets

`pagelayout/head/style.tpl` lädt seine Dateien in einem Aufruf von `ezcss_load` in Packstufe 1 (zusammengeführt, nicht minimiert), mit der
Reihenfolge wie in der ursprünglichen Website („the cascade order is the pixel contract: never reorder“). Es gibt auch einen kurzen
`<style>`-Block aus, der Inhaltsblöcke anderer Sprachen ausblendet. Auf einer Website mit durchgesetzter Content Security Policy müsste ein
solcher Block in ein Stylesheet wandern (zum Beispiel eine Regel pro Sprache in einer gepackten Datei, geschaltet über eine Klasse am
`<html>`); siehe [Kein Inline-CSS](kein-inline-css.md).

## Ideen, die sich zu übernehmen lohnen

- Remote-ids statt Knoten-ids, in Einstellungen, Overrides und Templates.
- Verhaltenseinstellungen pro Siteaccess über `ExtensionSettingsSiteAccess`, globale Einstellungen nur dort, wo jeder Siteaccess sie braucht.
- Eine eigene Einstellungsdatei des Themes für jede Design-Entscheidung.
- Seitenvariablen einmal oben im Pagelayout definiert, in einem Kommentar dokumentiert.
- Ein schlichter Rückfall für jedes fehlende Inhaltsstück.

## Verwandte Seiten

- [Die Themes, die mit Exponential geliefert werden](themes-die-mit-exponential-kommen.md)
- [Designs und Siteaccesses](designs-und-siteaccesses.md)
- [Einen einzelnen Knoten überschreiben](../vorlagen/einen-einzelnen-knoten-ueberschreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
