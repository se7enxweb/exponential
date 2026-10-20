---
title: "Ein Hinweisbanner"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1112
summary: Der Alert-Block (Message, Type, Dismissible) für einen kurzen Hinweis oder ein Template-Block.
keywords: Hinweis, Warnung, Banner, Alert-Block, Info, schließbar, alert, notice, banner, Alert block, info, warning, dismissible
type: help
---
# Ein Hinweisbanner

Ein einzeiliger Hinweis: „Our gym is closed on Monday.“

*Lebenszyklus: Bauen, Beispiel 13 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Alert** (Gruppe *Basic*) in die Zone `post_header`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Message**, **Type** (Standard `info`) und **Dismissible** aus.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Das Standard-Template schreibt `<div class="slb slb-alert alert alert-<type>">`. Für denselben Hinweis auf jeder Seite setzen Sie ihn in eine Zone eines gemeinsamen Layouts und verknüpfen diese Zone aus den Layouts: Die Layouts von Fit & Healthy verknüpfen nur `header` und `footer` von Header / Footer, dessen `post_header` wird also noch nirgends gezeigt.

## Mit Templates

1. Schreiben Sie `extension/mysite/design/media/templates/explayouts/tpl_block/closure_notice.tpl` in Ihrer Design-Erweiterung, leeren Sie den Template-Cache und platzieren Sie es mit einem **Template block**, dessen **Template name** `closure_notice` ist ([Eigene Template-Blöcke](../../../../../eng-us/help/build-two-ways/template-blocks.md)).

## Welchen Weg Sie wählen

Ein Hinweis, den Redakteure ein- und ausschalten: der Block. Ein Hinweis, der von Daten abhängt (Öffnungszeiten aus einem Inhaltsobjekt): das Template.

## Prüfen

- Der Hinweis erscheint auf den Seiten, deren Layouts ihn enthalten.

## Verwandte Seiten

- [Gemeinsame Layouts und Zonenverknüpfung](../../../../../eng-us/help/build-two-ways/shared-layouts-and-zone-linking.md)
- [Der Reiter Design](../../../../../eng-us/help/build-two-ways/design-tab-options.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
