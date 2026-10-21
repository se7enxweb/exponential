---
title: "Der Startseite ihr Layout geben"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1200
summary: Eine Zuordnung vom Typ Ort auf den Wurzelknoten der Site: auf Fit & Healthy Landing Page / Home auf Ort 89 mit Priorität 210.
keywords: Startseite, Titelseite, Ziel Ort, Zuordnung, Landing Page / Home, home page, front page, Location target, mapping
type: help
---
# Der Startseite ihr Layout geben

Die Startseite zeigt ihr eigenes Layout, nicht das Standard-Layout.

*Lebenszyklus: Livegang, Beispiel 1 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. **Layouts > Layout-Zuordnungen > Neue Zuordnung**; **Layout verknüpfen**: *Landing Page / Home*; **Priorität**: die höchste der Site (210 auf Fit & Healthy).
2. Ziel: **Ort** mit der Wurzel der Site (Fit & Healthy, Knoten 89), **Ziel hinzufügen**.
3. **Zuordnung hinzufügen**.

Der Resolver behandelt die Adresse `/` (und `home`) als Indexseite des Siteaccess (`[SiteSettings] IndexPage`, hier `/content/view/full/89`).

## Mit Templates

1. Ohne Layouts ist die Startseite die Full View ihrer Klasse (NG Frontpage) über `override.ini`.

## Welchen Weg Sie wählen

Layouts: Die Startseite ändert sich oft und besteht aus vielen Blöcken (28 auf Fit & Healthy).

## Prüfen

- Der Quelltext der Startseite enthält die Blöcke des Startseiten-Layouts; `exp:layouts list` zeigt Rules 2 für Landing Page / Home.

## Verwandte Seiten

- [Welches Layout bekommt eine Seite?](../../layout-zuordnungen.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
