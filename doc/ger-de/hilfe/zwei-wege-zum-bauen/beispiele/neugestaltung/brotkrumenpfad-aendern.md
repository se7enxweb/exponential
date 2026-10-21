---
title: "Den Brotkrumenpfad ändern"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1420
summary: Der Brotkrumenpfad ist ein Template-Block in der Zone header, dargestellt von pagelayout/breadcrumbs.tpl; überschreiben Sie es.
keywords: Brotkrumenpfad, pagelayout/breadcrumbs.tpl, Template-Block, Override, Neugestaltung, breadcrumb, template block, override, redesign
type: help
---
# Den Brotkrumenpfad ändern

Ein Brotkrumenpfad mit der aktuellen Seite in Fettschrift und einer schema.org-Liste.

*Lebenszyklus: Neugestaltung, Beispiel 21 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Der Template-Block des Brotkrumenpfads in Header / Footer (innerhalb einer Column mit dem Hintergrund `primary`) bleibt.

## Mit Templates

1. Kopieren Sie `extension/sevenx_themes_media/design/media/templates/pagelayout/breadcrumbs.tpl` nach `extension/mysite/design/media/templates/pagelayout/breadcrumbs.tpl`.
2. Ändern Sie das Markup; es liest den Pfad aus `$path_array`, das `pagelayout.tpl` aus den Vorfahren der Seite aufbaut.
3. Leeren Sie **Template & Inhalt** und prüfen Sie eine Seite, die es verwendet.

## Welchen Weg Sie wählen

Templates.

## Prüfen

- Jede Seite zeigt den neuen Brotkrumenpfad.

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
