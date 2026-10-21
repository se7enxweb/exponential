---
title: "Die Suche in der Kopfzeile ändern"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1424
summary: pagelayout/header/search_box.tpl überschreiben; das Formular sendet an /content/search.
keywords: Suchfeld, Kopfzeile, Override, Neugestaltung, search box, header, override, redesign
type: help
---
# Die Suche in der Kopfzeile ändern

Ein größeres Suchfeld mit einem Platzhaltertext.

*Lebenszyklus: Neugestaltung, Beispiel 25 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

Keine Änderung am Layout.

## Mit Templates

1. Kopieren Sie `extension/sevenx_themes_media/design/media/templates/pagelayout/header/search_box.tpl` nach `extension/mysite/design/media/templates/pagelayout/header/search_box.tpl`.
2. Behalten Sie `name="SearchText"` und die Aktion `/content/search`.
3. Leeren Sie **Template & Inhalt** und prüfen Sie eine Seite, die es verwendet.

## Welchen Weg Sie wählen

Templates.

## Prüfen

- Die Suche aus der Kopfzeile landet weiterhin auf der Ergebnisseite.

## Verwandte Seiten

- [Wie die Kopfzeile aufgebaut ist](../../wie-die-kopfzeile-aufgebaut-ist.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
