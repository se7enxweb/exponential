---
title: Design-Glossar
audience: designer
category: theme-design
order: 190
summary: Kurze Definitionen der Begriffe, denen Designer in Exponential begegnen: Design, Siteaccess, Kaskade, Pagelayout, Override, View-Modus, Knoten, Objekt, Klasse, Datentyp, Cache-Block, Packer, Token, Layout, Zone, Block und mehr, jeweils mit einem Link auf ihre Seite.
keywords: Glossar, Begriffe, Definitionen, Wortschatz, Designbegriffe, Siteaccess, Override, View-Modus, Knoten, Objekt, Klasse, Datentyp, Packer, Token, Zone, Block, glossary, terms, definitions, vocabulary, design words, view mode, node, object, class, datatype
type: help
---
# Design-Glossar

Diese Seite ist für Designer, die in dieser Hilfe auf ein Wort stoßen und seine Bedeutung in ein oder zwei Sätzen suchen. Die Begriffe
stehen in alphabetischer Reihenfolge der englischen Fachwörter; jeder verweist auf die Seite, die ihn erklärt.

## Inhalt

1. [A bis F](#a-bis-f)
2. [G bis O](#g-bis-o)
3. [P bis Z](#p-bis-z)

## A bis F

**Attribut (Attribute).** Ein Feld eines Inhaltsobjekts (der Titel, der Haupttext, das Bild). In Templates
`$node.data_map.<identifier>`; gezeichnet mit `{attribute_view_gui attribute=...}`. Siehe
[attribute_view_gui](../templates/attribute-view-gui.md).

**Block.** Ein Element in einer Zone eines Layouts (Text, Bild, Liste, Hero ...), gezeichnet von einem Block-Template. Siehe
[Blockdefinitionen](blockdefinitionen.md).

**Cache-Block.** Ein Bereich `{cache-block}` ... `{/cache-block}` eines Templates, dessen Ausgabe gespeichert und wiederverwendet wird.
Siehe [Cache-block](../templates/cache-block.md).

**Kaskade (Design-Kaskade, cascade).** Die geordnete Liste der Design-Ordner, die der Kernel nach einer Datei durchsucht. Siehe
[Die Design-Kaskade](die-design-kaskade.md).

**Klasse (Inhaltsklasse, class).** Der Typ eines Inhaltsobjekts, mit seinen Feldern: Artikel, Ordner, Bild. Templates unterscheiden
sich oft pro Klasse (`class_identifier`).

**Komponente (admin4, component).** Eine gemeinsame CSS-Klasse `.a4-*` der Administration (`.a4-card`, `.a4-btn`). Siehe
[admin4-Komponenten](admin4-komponenten.md).

**Content Security Policy (CSP).** Ein Antwort-Header, der dem Browser sagt, welche Skripte und Stile er ausführen darf; eine strenge
Richtlinie blockiert Inline-Code. Siehe [CSP-sicheres Design](csp-sicheres-design.md).

**Datentyp (datatype).** Die Art eines Feldes (Textzeile, Rich Text, Bild, Datum). Jeder hat Ansichts- und Bearbeitungstemplates unter
`content/datatype/`. Siehe [Ansichtstemplates der Datentypen](../templates/datentyp-ansichtstemplates.md).

**Design.** Ein benannter Ordner mit Templates, Stylesheets, Skripten und Bildern. Siehe [Was ein Design ist](was-ein-design-ist.md).

**Design-Erweiterung (design extension).** Eine Erweiterung, die Designs trägt und sich in `DesignExtensions[]` einträgt. Siehe
[Design-Erweiterungen](design-erweiterungen.md).

**fetch-Funktion (fetch function).** Ein Template-Aufruf, der Daten liest: `fetch( 'content', 'list', hash( ... ) )`. Siehe
[Grundlagen von fetch](../templates/fetch-grundlagen.md).

## G bis O

**i18n.** Der Template-Operator, der einen Text übersetzt: `'Read more'|i18n( 'design/mytheme' )`. Siehe
[Der Operator i18n](../templates/der-i18n-operator.md).

**Layout (Layout-Editor).** Eine benannte Anordnung von Zonen mit Blöcken, die einer Seite durch eine Regel zugewiesen wird. Siehe
[Layouts für Designer](layouts-fuer-designer.md).

**Modulergebnis (module result).** Was eine Modul-View erzeugt hat, in `$module_result`; das Pagelayout gibt dessen `content` aus.
Siehe [Variablen des Pagelayouts](pagelayout-variablen.md).

**Knoten (node).** Ein Ort eines Inhaltsobjekts im Inhaltsbaum. Ein Objekt kann mehrere Knoten haben. Templates von Ansichten erhalten
`$node`. Siehe [Knoten, Objekte und Attribute](../templates/knoten-objekte-und-attribute.md).

**Objekt (Inhaltsobjekt, object).** Ein Stück Inhalt mit seinen Feldern und Versionen.

**Operator.** Eine Template-Funktion, verwendet mit Pipe- oder Aufrufsyntax: `$text|wash`, `concat( 'a', 'b' )`. Siehe
[Operatoren im Überblick](../templates/operatoren-ueberblick.md).

**Override.** Eine Regel in `override.ini`, die für eine Seite, die Bedingungen erfüllt, ein anderes Template verwendet. Siehe
[Grundlagen der Overrides](../templates/override-grundlagen.md).

## P bis Z

**Packer.** Der Teil von ezjscore, der Stylesheets und Skripte zu einer zwischengespeicherten Datei zusammenführt. Siehe
[Der Packer](der-packer.md).

**Pagelayout.** Das äußerste Template, `pagelayout.tpl`, das jede Seite einrahmt. Siehe [Das Pagelayout](das-pagelayout.md).

**Persistente Variable (persistent variable).** Ein Hash, den ein View-Template für das Pagelayout füllt. Siehe
[Die persistent variable](../templates/die-persistent-variable.md).

**Regel (Layouts, rule).** Die Zuordnung von Anfragen zu einem Layout, mit Zielen, Bedingungen und einer Priorität. Siehe
[Layout-Regeln](layout-regeln.md).

**Siteaccess.** Ein Zugang zu einer Installation, mit eigenen Einstellungen und Designs. Siehe
[Designs und Siteaccesses](designs-und-siteaccesses.md).

**StandardDesign.** Das letzte Design jeder Kaskade, standardmäßig `standard`.

**Template.** Eine `.tpl`-Datei in der Template-Sprache. Siehe [Templates im Überblick](../templates/templates-ueberblick.md).

**Token (Design-Token).** Eine benannte CSS-Custom-Property für eine Farbe, eine Größe oder eine Schrift, mit einem Wert pro Theme.
Siehe [Design-Token](design-tokens.md).

**View-Cache.** Das gespeicherte HTML von Vollansichten, geleert, wenn Inhalte veröffentlicht werden. Siehe
[Caching für Designer](caching-fuer-designer.md).

**View-Modus (view mode).** Die Art, wie ein Knoten gezeigt wird: `full`, `line`, `embed`, `embed-inline` ... Siehe
[View-Modi](../templates/ansichtsmodi.md).

**Wash.** Der Operator, der Text für die HTML-Ausgabe maskiert. Siehe [Wash und Maskierung](../templates/wash-und-maskierung.md).

**Zone.** Ein benannter Bereich eines Layouts (`main`, `sidebar`, `footer`), der Blöcke enthält. Siehe
[Layouttypen und Zonen](layouttypen-und-zonen.md).

## Verwandte Seiten

- [Designer: hier beginnen](../designer-hier-beginnen.md)
- [Was ein Design ist](was-ein-design-ist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
