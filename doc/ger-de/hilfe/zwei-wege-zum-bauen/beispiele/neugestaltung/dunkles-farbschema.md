---
title: "Ein dunkles Farbschema für die Site"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1430
summary: CSS mit prefers-color-scheme für die Klassen des Designs; keine Änderung an Templates oder Layouts.
keywords: dunkler Modus, prefers-color-scheme, CSS, Neugestaltung, dark mode, redesign
type: help
---
# Ein dunkles Farbschema für die Site

Die Site folgt der dunklen Einstellung des Besuchers.

*Lebenszyklus: Neugestaltung, Beispiel 31 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

Keine Änderung am Layout; prüfen Sie jede Hintergrundfarbe, die Redakteure wählen können.

## Mit Templates

1. Fügen Sie Ihrem Stylesheet Regeln `@media (prefers-color-scheme: dark) { ... }` für den Seitenhintergrund, den Text und die Klassen `bg-color-*` hinzu.

## Welchen Weg Sie wählen

CSS.

## Prüfen

- Textkontrast von mindestens 4,5:1 in beiden Schemata.

## Verwandte Seiten

- [Ihre Blöcke gestalten](../../bloecke-gestalten.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
