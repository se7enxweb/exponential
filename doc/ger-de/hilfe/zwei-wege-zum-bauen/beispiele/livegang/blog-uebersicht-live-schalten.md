---
title: "Die Blog-Übersichtsseite live schalten"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1218
summary: Eine Landingpage mit einer dynamischen Liste von NG Blog posts, über Ort zugeordnet; die Beiträge verwenden bereits das Artikel-Layout.
keywords: Blog, Livegang, dynamische Liste, ng_blog_post, Landingpage, blog, launch, dynamic list, landing page
type: help
---
# Die Blog-Übersichtsseite live schalten

Eine Blog-Übersicht, die die Beiträge mit dem neuesten zuerst listet.

*Lebenszyklus: Livegang, Beispiel 19 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Legen Sie die Landingpage „Blog“ im Baum an.
2. Legen Sie ihr Layout mit einem List-Block an: dynamische Sammlung, übergeordnet der Blog-Ordner, absteigend nach Published sortiert.
3. Ordnen Sie es über **Ort** zu.

## Mit Templates

1. `fetch( 'content', 'list', hash( 'parent_node_id', <blog node>, 'class_filter_type', 'include', 'class_filter_array', array( 'ng_blog_post' ), 'sort_by', array( 'published', false() ) ) )` in einem Template-Block.

## Welchen Weg Sie wählen

Die dynamische Liste hält die Übersicht ohne Code aktuell.

## Prüfen

- Ein neuer Beitrag erscheint als Erstes.

## Verwandte Seiten

- [Manuelle und dynamische Sammlungen](../../manuelle-und-dynamische-sammlungen.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
