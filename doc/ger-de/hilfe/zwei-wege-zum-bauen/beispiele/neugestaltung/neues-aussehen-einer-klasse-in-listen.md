---
title: "Ein neues Aussehen für eine Klasse in Listen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1416
summary: content/views/<view>/<class>.tpl in Ihrer Erweiterung hinzufügen; Listen mit diesem Ansichtstyp der Elemente verwenden es für diese Klasse.
keywords: Ansicht der Einträge, Inhaltsansichten, Karte, Klassen-Template, Neugestaltung, item view, content views, card, class template, redesign
type: help
---
# Ein neues Aussehen für eine Klasse in Listen

Rezepte in der Kartenansicht zeigen die Kochzeit.

*Lebenszyklus: Neugestaltung, Beispiel 17 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Listen mit **Ansichtstyp der Elemente** Card zeigen Rezepte mit dem neuen Template.

## Mit Templates

1. `itemViewTemplate()` des Themes bevorzugt `content/views/<view>/<class>.tpl` und fällt auf `content/views/<view>.tpl` zurück; fügen Sie `extension/mysite/design/media/templates/content/views/card/ng_recipe.tpl` hinzu.

## Welchen Weg Sie wählen

Pro Klasse, damit andere Klassen die allgemeine Karte behalten.

## Prüfen

- Eine Rezeptkarte zeigt die Kochzeit; eine Artikelkarte ist unverändert.

## Verwandte Seiten

- [Wie ein Block seinen Templates zugeordnet wird](../../zuordnung-block-zu-template.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
