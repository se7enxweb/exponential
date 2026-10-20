---
title: "Häufig gestellte Fragen als Akkordeon"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1113
summary: Der Accordion-Block (Items „Title|Content“ pro Zeile, Open first item) oder der Block Accordion (List) über Inhaltsobjekte.
keywords: FAQ, Akkordeon, Fragen, Accordion-Block, Accordion (List), list_accordion, accordion, questions, Accordion block
type: help
---
# Häufig gestellte Fragen als Akkordeon

Fünf Fragen und Antworten, die sich per Klick öffnen.

*Lebenszyklus: Bauen, Beispiel 14 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Accordion** (Gruppe *Basic*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Items (Title|Content per line)** aus, eine Frage pro Zeile als `Question|Answer`, dazu **Open first item**.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Wenn die Fragen Inhaltsobjekte sind (zum Beispiel der Klasse *Accordion item*), verwenden Sie stattdessen **Accordion (List)** (Gruppe *Listing*) mit einer Sammlung.

## Mit Templates

1. Holen Sie die Fragen als Inhalte und geben Sie sie mit `<details>` aus:

```smarty
{def $faq = fetch( 'content', 'list', hash( 'parent_node_id', <folder node id>, 'class_filter_type', 'include', 'class_filter_array', array( 'ng_accordion_item' ) ) )}
{foreach $faq as $q}
<details><summary>{$q.name|wash}</summary>{attribute_view_gui attribute=$q.data_map.description}</details>
{/foreach}
{undef $faq}
```

Die Klasse *Accordion item* (`ng_accordion_item`) hat die Felder `title` und `description`.

## Welchen Weg Sie wählen

Wenige Fragen auf einer Seite: der Accordion-Block. Viele Fragen, die auf mehreren Seiten wiederverwendet werden: Inhaltsobjekte und eine Liste.

## Prüfen

- Jede Frage öffnet und schließt sich auch mit der Tastatur.

## Verwandte Seiten

- [Manuelle und dynamische Sammlungen](../../../../../eng-us/help/build-two-ways/manual-and-dynamic-collections.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
