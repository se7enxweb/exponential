---
title: "Jeden Seitentyp an seinem Markup prüfen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1210
summary: Layouts und Templates hinterlassen Markierungen im HTML (Zonen-Abschnitte, ngl-block-Klassen, IDs der Menüeinträge), die ein Skript vor dem Livegang prüfen kann.
keywords: Prüfung vor dem Livegang, Markup, curl, ngl-block, Zone, Smoke-Test, launch check, markup, zone, smoke test
type: help
---
# Jeden Seitentyp an seinem Markup prüfen

Eine Prüfung von einer Minute, dass jeder Seitentyp sein Layout darstellt.

*Lebenszyklus: Livegang, Beispiel 11 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Vergleichen Sie im Editor die Blöcke des Layouts mit dem, was die Seite zeigt.

## Auf der Kommandozeile

1. Rufen Sie für jeden Seitentyp eine Seite ab und suchen Sie nach ihren Markierungen:

```bash
curl -s https://www.example.com/fitness | grep -o 'zone zone-[a-z_]*' | sort -u
curl -s https://www.example.com/fitness | grep -c 'ngl-block '
curl -s https://www.example.com/ | grep -o 'menu-item-main_menu-location-id-[0-9]*'
```

## Welchen Weg Sie wählen

Das Skript läuft nach jedem Deployment; der Vergleich im Editor, wenn etwas fehlt.

## Prüfen

- Jeder Seitentyp hat die Abschnitte `zone-header`, `zone-main` und `zone-footer` und eine plausible Anzahl von Blöcken.

## Verwandte Seiten

- [Warum erscheint meine Änderung nicht?](../../warum-erscheint-meine-aenderung-nicht.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
