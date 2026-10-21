---
title: "Eine Abschnittsüberschrift mit der richtigen Ebene"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1106
summary: Der Title-Block (Title, Heading level 1-6, HTML tag, Use link) für Überschriften zwischen Blöcken.
keywords: Titel, Überschrift, h2, Überschriftenebene, Title-Block, Abschnittstitel, Barrierefreiheit, title, heading, heading level, Title block, section title, accessibility
type: help
---
# Eine Abschnittsüberschrift mit der richtigen Ebene

„Most popular articles“ über einer Liste, als `h2`, damit die Seite ein einziges `h1` behält.

*Lebenszyklus: Bauen, Beispiel 7 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Prefooter*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Title** (Gruppe *Basic*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Title**, **Heading level (1-6)** (Standard 2) und bei Bedarf **HTML tag** und **Use link** aus.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Der eigene Titel des Prefooters verwendet die Ansicht `section_centered` und `tag = h2`; das Media-Design verteilt Titel nach Ansichtstyp auf `explayouts/block/title/<view type>.tpl`.

## Mit Templates

1. Schreiben Sie die Überschrift in das Template, das den Abschnitt darstellt: `<h2 class="title">{'Most popular articles'|i18n( 'mysite' )}</h2>`.

## Welchen Weg Sie wählen

Der Block, wenn Redakteure Abschnitte anordnen; im Template, wenn die Überschrift zu einer festen Komponente gehört.

## Prüfen

- Die Seite hat ein einziges `h1`, und die Abschnittstitel sind `h2` (ein Prüfwerkzeug für Barrierefreiheit zeigt die Gliederung).

## Verwandte Seiten

- [Wie ein Block seinen Templates zugeordnet wird](../../zuordnung-block-zu-template.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
