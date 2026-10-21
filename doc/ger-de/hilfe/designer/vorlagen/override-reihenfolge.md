---
title: Override-Reihenfolge
audience: designer, developer
category: templates
order: 590
summary: Welcher Override gewinnt, wenn mehrere Regeln zutreffen - Regeln werden nach Priority versucht (niedrigste zuerst; Regeln ohne Priority zuletzt), der erste Treffer gewinnt, spezifische Regeln vor allgemeinen, die Anordnung per Drag-and-drop im Template-Editor und wie Sie einen falschen Gewinner debuggen.
keywords: Override-Reihenfolge, Priority, erster Treffer gewinnt, Vorrang von Overrides, spezifisch vor allgemein, Umordnen im Template-Editor, falscher Override, Override-Konflikt, override order, first match wins, override precedence, specific before general, template editor reorder, wrong override, override conflict
type: help
---
# Override-Reihenfolge

Diese Seite ist für Designer, deren Override nicht gewinnt. Mehrere Regeln können auf dieselbe Seite zutreffen; nur die erste
in der Reihenfolge wird verwendet.

## Inhalt

1. [Die Reihenfolge](#die-reihenfolge)
2. [Spezifisch vor allgemein](#spezifisch-vor-allgemein)
3. [Regeln aus mehreren Dateien](#regeln-aus-mehreren-dateien)
4. [Umordnen in der Administration](#umordnen-in-der-administration)
5. [Debuggen](#debuggen)

## Die Reihenfolge

`eZTemplateDesignResource::overrideArray()` (`kernel/common/eztemplatedesignresource.php`) sortiert die Regeln nach ihrer
`Priority`: Niedrigere Werte werden zuerst ausgewertet, und eine Regel ohne `Priority` wird zuletzt einsortiert. Für jedes
Template gewinnt die erste Regel, deren Bedingungen alle zutreffen; die übrigen werden nicht betrachtet.

## Spezifisch vor allgemein

```ini
# one node
[mytheme_full_frontpage]
Source=node/view/full.tpl
MatchFile=full/frontpage.tpl
Subdir=templates
Match[node_remote_id]=mytheme-front
Priority=0

# a class in a place
[mytheme_full_article_in_blog]
Source=node/view/full.tpl
MatchFile=full/blog_article.tpl
Subdir=templates
Match[class_identifier]=article
Match[parent_class_identifier]=blog
Priority=10

# a class
[mytheme_full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
Priority=20
```

Lassen Sie Lücken (0, 10, 20), damit sich später eine Regel einfügen lässt, ohne neu zu nummerieren.

## Regeln aus mehreren Dateien

Regeln aus dem Siteaccess, aus Erweiterungen und aus Siteaccess-Dateien von Erweiterungen werden vor dem Sortieren zu einer
Liste zusammengeführt. Ohne `Priority` hängt ihre Reihenfolge untereinander davon ab, in welcher Reihenfolge die Dateien
gelesen werden, was schwer vorherzusagen ist: Geben Sie jeder Regel eines Themes eine `Priority`.

## Umordnen in der Administration

**Design > Template-Editor** zeigt die Overrides eines Templates in ihrer Auswertungsreihenfolge und lässt Sie sie per
Drag-and-drop umordnen; er schreibt `Priority`-Werte (10, 20, 30 ...) für die Regeln dieses Templates, und neue Regeln erhalten
`Priority=0` ([Overrides im Template-Editor](../../../../eng-us/features/6.0/template-editor-overrides.md)).

## Debuggen

- Schalten Sie Template-Pfadkommentare ein: Der Quelltext der Seite nennt das Template, das gewonnen hat ([Template-Pfadkommentare](template-pfad-kommentare.md)).
- Listen Sie im Template-Editor alle Regeln für die `Source` in ihrer Reihenfolge auf und lesen Sie ihre Bedingungen von oben
  nach unten.
- Denken Sie nach jeder Änderung an die Caches für INI und Template-Overrides.

## Verwandte Seiten

- [Override-Grundlagen](override-grundlagen.md)
- [Override-Match-Schlüssel](override-match-schluessel.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
