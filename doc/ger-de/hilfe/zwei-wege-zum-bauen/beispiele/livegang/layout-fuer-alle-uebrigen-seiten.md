---
title: "Ein Layout für alle übrigen Seiten"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1204
summary: Default Layout fängt den Rest auf: eine Zuordnung mit Pfadpräfix „/“ und Priorität 10 und [ResolverSettings] DefaultLayout als Rückfall.
keywords: Standard-Layout, Auffang-Layout, Pfadpräfix, Rückfall, default layout, catch-all, path prefix, fallback, DefaultLayout
type: help
---
# Ein Layout für alle übrigen Seiten

Keine Seite bleibt ohne Kopf- und Fußzeile.

*Lebenszyklus: Livegang, Beispiel 5 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. **Layouts > Layout-Zuordnungen > Neue Zuordnung**; **Layout verknüpfen**: *Default Layout*; **Priorität**: die niedrigste (10).
2. Ziel: **Pfadpräfix** `/`, **Ziel hinzufügen**.
3. **Zuordnung hinzufügen**.

Bei Default Layout sind nur `header` und `footer` mit Header / Footer verknüpft, dazu kommt ein eigener Block.

## Mit Templates

1. `explayouts.ini [ResolverSettings] DefaultLayout` nennt das Layout, das verwendet wird, wenn keine Zuordnung passt (auf dieser Installation der Bezeichner von Default Layout).

## Welchen Weg Sie wählen

Behalten Sie beides: Die Zuordnung ist für Redakteure sichtbar, die Einstellung ist das Sicherheitsnetz.

## Prüfen

- Eine Seite einer Klasse ohne eigene Zuordnung hat trotzdem die Kopfzeile der Site.

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
