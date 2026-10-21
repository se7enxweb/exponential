---
title: "Jedem Artikel das Artikel-Layout geben"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1201
summary: Eine Teilbaum-Zuordnung mit einer Klassen-Bedingung: Full / Article auf Teilbaum 89 für ng_article, ng_blog_post, ng_news mit Priorität 130.
keywords: Klassen-Bedingung, Teilbaum-Ziel, Artikel-Layout, Zuordnung, Full / Article, class condition, subtree target, article layout, mapping
type: help
---
# Jedem Artikel das Artikel-Layout geben

Alle Artikel, Blog-Beiträge und Nachrichten unterhalb der Wurzel der Site teilen sich ein Layout.

*Lebenszyklus: Livegang, Beispiel 2 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. **Layouts > Layout-Zuordnungen > Neue Zuordnung**; **Layout verknüpfen**: *Full / Article*; **Priorität**: 130.
2. Ziel: **Teilbaum** mit der Wurzel der Site (Knoten 89), **Ziel hinzufügen**.
3. Bedingung: **Klasse**: NG Article, NG Blog post, News, **Bedingung hinzufügen**.
4. **Zuordnung hinzufügen**.

## Mit Templates

1. Mit Templates ist es `override.ini` `Match[class_identifier]=ng_article` für `node/view/full.tpl`, wie es das Theme in `extension/sevenx_themes_media/settings/siteaccess/site/override.ini.append.php` macht.

## Welchen Weg Sie wählen

Die Klassen-Zuordnung ist das Rückgrat einer Layouts-Site; Full-View-Overrides stellen weiterhin dar, was die Blöcke des Layouts einbinden.

## Prüfen

- Ein neu veröffentlichter Artikel bekommt das Layout (sein `pre_footer` zeigt den Prefooter).

## Verwandte Seiten

- [Welches Layout bekommt eine Seite?](../../layout-zuordnungen.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
