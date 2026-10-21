---
title: "Ein neues Aussehen für Abschnittstitel"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1401
summary: Der Title-Block verteilt nach Ansichtstyp auf explayouts/block/title/<view type>.tpl; überschreiben Sie section_centered für den Stil des Prefooters.
keywords: Titel, section_centered, Override, Block-Template, Neugestaltung, title, override, block template, redesign
type: help
---
# Ein neues Aussehen für Abschnittstitel

Zentrierte Abschnittstitel bekommen links und rechts eine Linie.

*Lebenszyklus: Neugestaltung, Beispiel 2 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Titel mit der Ansicht `section_centered` (die des Prefooters) ändern sich überall.

## Mit Templates

1. Kopieren Sie `extension/sevenx_themes_media/design/media/templates/explayouts/block/title/section_centered.tpl` nach `extension/mysite/design/media/templates/explayouts/block/title/section_centered.tpl`.
2. Ändern Sie das Markup der Überschrift (behalten Sie das Tag aus `$block.parameters.tag`).
3. Leeren Sie **Template & Inhalt** und prüfen Sie eine Seite, die es verwendet.

## Welchen Weg Sie wählen

Überschreiben Sie pro Ansichtstyp, damit andere Titel ihr Aussehen behalten.

## Prüfen

- Der Titel des Prefooters zeigt das neue Aussehen, andere Titel nicht.

## Verwandte Seiten

- [Wie ein Block seinen Templates zugeordnet wird](../../zuordnung-block-zu-template.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
