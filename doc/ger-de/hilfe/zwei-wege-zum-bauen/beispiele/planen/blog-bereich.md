---
title: "Einen Blog-Bereich planen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1022
summary: Blog-Beiträge (Klasse NG Blog post) teilen sich auf Fit & Healthy bereits die Artikel-Zuordnung; planen Sie die Übersichtsseite und ihre Listen.
keywords: Blog, ng_blog_post, Artikel-Zuordnung, Übersicht, Bereich, Planung, blog, article mapping, listing, section, planning
type: help
---
# Einen Blog-Bereich planen

Ein Blog mit eigener Übersichtsseite und dem Artikel-Layout für die Beiträge.

*Lebenszyklus: Planen, Beispiel 23 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Die Zuordnung Full / Article (Priorität 130) hat die Klassen-Bedingung `ng_article`, `ng_blog_post`, `ng_news`: Beiträge bekommen das Artikel-Layout ohne neue Zuordnung.
2. Die Übersichtsseite des Blogs ist eine Landingpage mit einer dynamischen Liste der Beiträge.

## Mit Templates

1. Eine eigene Full View für den Blog wäre ein Block in der `override.ini` für `ng_blog_post` in Ihrer Erweiterung.

## Welchen Weg Sie wählen

Verwenden Sie das Artikel-Layout wieder; geben Sie der Übersichtsseite ein eigenes Layout.

## Prüfen

- Ein neuer Blog-Beitrag erscheint mit dem Artikel-Layout.

## Verwandte Seiten

- [Welches Layout bekommt eine Seite?](../../layout-zuordnungen.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
