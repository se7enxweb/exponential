---
title: "Der eigene Inhalt der Seite in einem Layout"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1132
summary: Der Full-view-Block setzt die Full View der Seite (den Inhalt selbst) zwischen andere Blöcke.
keywords: Full View, Full-view-Block, Seiteninhalt, Platzhalter, Artikeltext, full view, Full view block, page content, placeholder, article body
type: help
---
# Der eigene Inhalt der Seite in einem Layout

Eine Artikelseite, deren Layout Blöcke über und unter dem eigentlichen Artikel hinzufügt.

*Lebenszyklus: Bauen, Beispiel 33 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Full / Article*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Full view** (Gruppe *Placeholders*) in die Zone `main`. Setzen Sie ihn dorthin, wo der Artikel erscheinen soll.
3. Fügen Sie davor oder danach weitere Blöcke hinzu.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Die Artikel-Layouts von Fit & Healthy verwenden Template-Blöcke für die Teile des Artikels (Kopf, Bild, Text); der Full-view-Block ist der Weg mit einem einzigen Block.

## Mit Templates

1. Die Full View selbst ist ein Template: Das Theme ordnet jede Klasse mit `override.ini` zu (zum Beispiel `node/view/full#ng_article` auf `node/view/full/full_ng_article.tpl`).

## Welchen Weg Sie wählen

Layouts entscheiden, was den Inhalt umgibt; das Full-View-Template entscheidet, wie der Inhalt aussieht.

## Prüfen

- Der Artikel erscheint genau einmal, dort, wo der Block steht.

## Verwandte Seiten

- [Eine Kampagnenseite bauen, von Anfang bis Ende](../../kampagnenseite-bauen.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
