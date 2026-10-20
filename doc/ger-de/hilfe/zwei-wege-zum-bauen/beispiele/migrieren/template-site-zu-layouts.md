---
title: "Eine mit Templates gebaute Site zu Layouts migrieren"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1513
summary: Das Design behalten; die Teile des Seitenlayouts zu Template-Blöcken eines gemeinsamen Header / Footer machen; Seitenlayouts pro Klasse hinzufügen; sie zuordnen.
keywords: Migration, Template-Site, Layouts, Template-Blöcke, gemeinsames Layout, migration, template site, layouts, template blocks, shared layout
type: help
---
# Eine mit Templates gebaute Site zu Layouts migrieren

Eine bestehende Site bekommt den Layout-Editor ohne Neugestaltung.

*Lebenszyklus: Migrieren, Beispiel 14 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Legen Sie einen gemeinsamen Header / Footer mit diesen Template-Blöcken an; legen Sie pro Seitentyp ein Layout an, das `header` und `footer` verknüpft; ordnen Sie jedes nach Klasse zu; fügen Sie Blöcke hinzu, wo Redakteure sie brauchen.

## Mit Templates

1. Speichern Sie die Teile Kopfzeile, Brotkrumenpfad und Fußzeile als Templates, die der Template-Block nennen kann (`explayouts/tpl_block/<name>.tpl`), oder ordnen Sie sie nach ihrem Platz zu, wie es das Media-Theme macht.

## Welchen Weg Sie wählen

So ist das Theme von Fit & Healthy gebaut: Template-Blöcke tragen den alten Rahmen.

## Prüfen

- Jede Seite sieht aus wie vorher; Redakteure können in `main` Blöcke hinzufügen.

## Verwandte Seiten

- [Eigene Template-Blöcke](../../eigene-template-bloecke.md)
- [Gemeinsame Layouts und Zonenverknüpfung](../../gemeinsame-layouts-und-zonenverknuepfung.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
