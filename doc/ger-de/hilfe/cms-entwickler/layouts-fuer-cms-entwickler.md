---
title: Layouts für CMS-Entwickler
audience: cms-developer
category: cms-development
order: 31
summary: Für CMS-Entwickler Exponential Layouts aus Sicht der Entwickler. Die Seite behandelt Layouts, Zonen, Blöcke und Regeln, wie eine Seite ihr Layout auflöst, wie Layouts im Pagelayout auf Templates treffen, Layout-Typen, Blockdefinitionen und View-Typen, Template-Blöcke, gemeinsame Layouts, das Übertragen von Layouts zwischen Installationen und wann Layouts statt Templates passen.
keywords: Exponential Layouts, explayouts, Layout-Editor, Layout-Typ, Zone, Block, Blockdefinition, View-Typ, Regel, Zuordnung, resolve_layout, Template-Block, gemeinsames Layout, Zonen verknüpfen, Sammlungen, exp:layouts, Landingpage, Seitenbaukasten, layouts editor, layout type, block definition, view type, rule, mapping, template block, shared layout, zone linking, collections, landing page, page builder
type: help
---
# Layouts für CMS-Entwickler

Diese Seite richtet sich an Entwickler, die entscheiden, welche Teile einer Website Redakteure selbst zusammenstellen.
**Exponential Layouts** (die Erweiterungen `explayouts`, eine Portierung der Upstream-Layouts-Pakete auf diesen Kernel) lässt
Redakteure Seiten ohne Code aus Blöcken in Zonen bauen, während Ihre Templates und Ihr Inhaltsmodell bestimmen, wie
jeder Block aussieht.

## Inhalt

1. [Die Teile](#die-teile)
2. [Wie eine Seite ihr Layout erhält](#wie-eine-seite-ihr-layout-erhält)
3. [Wo Layouts auf Ihre Templates treffen](#wo-layouts-auf-ihre-templates-treffen)
4. [Was Sie als Entwickler festlegen](#was-sie-als-entwickler-festlegen)
5. [Template-Blöcke: Ihr Code in einem Layout](#template-blöcke-ihr-code-in-einem-layout)
6. [Gemeinsame Layouts](#gemeinsame-layouts)
7. [Layouts zwischen Installationen](#layouts-zwischen-installationen)
8. [Layouts oder Templates](#layouts-oder-templates)
9. [Nach einer Änderung](#nach-einer-änderung)

## Die Teile

| Teil | Was es ist |
|---|---|
| Layout | der Plan einer Seite, von einem Redakteur im Layout-Editor erstellt |
| Layout-Typ | die Zonen, die ein Layout hat (*1 column*, *2 columns*, *Hero + 3 columns* ...), in Einstellungen mit einem Template festgelegt |
| Zone | ein Bereich des Layouts; enthält Blöcke untereinander |
| Block | ein Titel, Rich Text, ein Bild, eine Liste von Inhalten, eine Galerie, ein Hero und mehr; jeder von einer Blockdefinition mit View-Typen |
| Sammlung | die Elemente eines Listenblocks: von Hand ausgewählt oder durch eine Abfrage gefüllt |
| Regel (Zuordnung) | welche Seiten ein Layout verwenden: Ziele (eine Platzierung, ein Teilbaum, eine Modulseite) und Bedingungen (eine Klasse, ein Siteaccess) |
| Gemeinsames Layout | ein Layout, mit dessen Zonen andere verknüpft sind, etwa Kopf- und Fußbereich |

Die Sicht der Redakteure: [Der Layout-Editor](../layout-editor.md); der Rundgang:
[Rundgang durch den Layout-Editor](../zwei-wege-zum-bauen/rundgang-durch-den-layout-editor.md).

## Wie eine Seite ihr Layout erhält

Bei jeder Anfrage prüft der Resolver die aktivierten Regeln nach Priorität; die erste, deren Ziele und Bedingungen zur
Seite passen, gewinnt. Regeln liegen in der Datenbank, werden in der Layout-Verwaltung unter **Rules**
(**Layout mappings**) verwaltet, und die Registerkarte **Layouts** eines Knotens zeigt, zu welchem Layout er sich
auflöst ([Layout-Regeln](../designer/theme-design/layout-regeln.md), [Layout-Zuordnungen](../zwei-wege-zum-bauen/layout-zuordnungen.md)).

## Wo Layouts auf Ihre Templates treffen

Das Pagelayout fragt nach dem Layout der Seite und zeichnet es oder fällt auf das normale Ergebnis der View zurück:

```smarty
{def $page_layout = fetch( 'explayouts', 'resolve_layout', hash() )}
{if $page_layout}
    {include uri='design:explayouts/layout.tpl' layout=$page_layout module_result=$module_result}
{else}
    {$module_result.content}
{/if}
{undef $page_layout}
```

Blöcke eines Layouts können den eigenen Inhalt der Seite zeigen (die Full View in einem Layout); ein Layout kann also
das Template, das Sie für eine Klasse geschrieben haben, einrahmen statt es zu ersetzen. Weitere Fetch-Funktionen
beantworten, welches Layout oder welche Regeln ein Knoten hat
([Ein Layout in Templates abrufen](../designer/vorlagen/ein-layout-in-templates-abrufen.md)).

## Was Sie als Entwickler festlegen

| Sie legen fest | Wo | Lesen |
|---|---|---|
| Layout-Typen und ihre Zonen | `[LayoutType_<name>]` in `explayouts.ini` (in der Append-Datei Ihrer Erweiterung) und ein Template für den Layout-Typ | [Layout-Typen und Zonen](../designer/theme-design/layouttypen-und-zonen.md) |
| Neue View-Typen vorhandener Blöcke | die View-Typen der Blockdefinition und ein Template; kein PHP | [Einen View-Typ hinzufügen](../zwei-wege-zum-bauen/ansichtstyp-hinzufuegen.md) |
| Neue Blocktypen | eine Blockdefinition und ihre Templates | [Einen Blocktyp hinzufügen](../zwei-wege-zum-bauen/blocktyp-hinzufuegen.md), [Blockdefinitionen](../designer/theme-design/blockdefinitionen.md) |
| Wie Blöcke aussehen | View-Templates für Blöcke und Stylesheets in Ihrem Design | [View-Templates für Blöcke](../designer/theme-design/block-ansichtstemplates.md), [Layout-Blöcke gestalten](../designer/theme-design/layout-bloecke-gestalten.md) |
| Wie Listenelemente aussehen | Ihre Ansichtsmodi je Klasse | [Ansichtsmodi](ansichtsmodi.md) |

Redakteure kombinieren dann, was Sie festgelegt haben. Halten Sie die Zahl der Layout-Typen und View-Typen klein: Jeder
ist eine Wahl, die ein Redakteur verstehen muss.

## Template-Blöcke: Ihr Code in einem Layout

Ein **Template-Block** setzt ein Template Ihres Designs in eine Zone. Verwenden Sie ihn für Teile, die Code brauchen
(ein Suchformular, ein von einem Fetch gespeister Kalender, ein Widget), während Redakteure weiterhin entscheiden, wo
sie erscheinen ([Template-Blöcke](../zwei-wege-zum-bauen/eigene-template-bloecke.md)). Hier treffen sich die beiden Wege zu bauen
([Zwei Wege zum Bauen](../zwei-wege-zum-bauen/zwei-wege-zum-bauen.md)).

## Gemeinsame Layouts

Ein gemeinsames Layout enthält Blöcke, die viele Seiten verwenden; eine Zone eines anderen Layouts wird mit einer Zone
des gemeinsamen Layouts **verknüpft**, statt dessen Blöcke zu kopieren. Eine Bearbeitung des gemeinsamen Layouts ändert
jede Seite, die damit verknüpft ist
([Gemeinsame Layouts und Zonenverknüpfung](../zwei-wege-zum-bauen/gemeinsame-layouts-und-zonenverknuepfung.md)).

## Layouts zwischen Installationen

Layouts und Regeln liegen in den Layout-Tabellen der Datenbank, nicht in Inhaltspaketen. Sie werden als eine ZIP-Datei
übertragen:

```bash
./console exp:layouts list
./console exp:layouts export --all --output=var/tmp/layouts.zip
./console exp:layouts import var/tmp/layouts.zip --dry-run
```

([Layouts exportieren und importieren](../zwei-wege-zum-bauen/layouts-exportieren-und-importieren.md)). Regeln, die auf Platzierungen
zielen, verweisen auf Knoten-IDs, die sich zwischen Installationen unterscheiden; prüfen Sie sie nach einem Import.

## Layouts oder Templates

| Situation | Bauen mit |
|---|---|
| Landingpages, Kampagnen, eine Startseite, die Redakteure oft umstellen | Layouts |
| Jede Artikel-, Veranstaltungs- oder Produktseite | einem Template je Klasse, eventuell von einem Layout eingerahmt |
| Eine Liste, deren Inhalt Regeln folgt (die neuesten fünf News) | einem Layout-Block mit einer Abfragesammlung oder einem Fetch in einem Template |
| Etwas, das Code braucht | einem Template, mit einem Template-Block platziert, wenn Redakteure den Ort wählen |

Die [Entscheidungstabelle](../zwei-wege-zum-bauen/entscheidungstabelle.md) geht weitere Fälle durch.

## Nach einer Änderung

Layout-Änderungen werden im Editor veröffentlicht; Einstellungen und Templates brauchen geleerte Caches. Der Resolver
hat einen eigenen Cache, und Seiten werden in den View-Caches und im Antwort-Cache von Velocity zwischengespeichert
([Caches nach einer Änderung](../zwei-wege-zum-bauen/caches-nach-einer-aenderung.md),
[Warum erscheint meine Änderung nicht?](../zwei-wege-zum-bauen/warum-erscheint-meine-aenderung-nicht.md)).

## Verwandte Seiten

- [Zwei Wege zum Bauen](../zwei-wege-zum-bauen/README.md)
- [Layouts für Designer](../designer/theme-design/layouts-fuer-designer.md)
- [Kurs für Trainer: Exponential Layouts](../trainer/layouts/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
