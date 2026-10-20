---
title: "Bestandsaufnahme von Layouts und Einstellungen auf der Konsole"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1015
summary: exp:layouts list und exp:ini where geben ein vollständiges, skriptfähiges Bild der Layouts und Menü-Einstellungen einer Installation.
keywords: exp:layouts list, exp:ini where, Bestandsaufnahme, Konsole, Prüfung, inventory, console, audit
type: help
---
# Bestandsaufnahme von Layouts und Einstellungen auf der Konsole

Eine Neugestaltung oder Prüfung mit Fakten beginnen, nicht mit Erinnerungen.

*Lebenszyklus: Planen, Beispiel 16 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Auf der Kommandozeile](#auf-der-kommandozeile)
2. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
3. [Prüfen](#prüfen)

## Auf der Kommandozeile

1. `./console exp:layouts list` (dazu `--drafts`, `--json`).
2. `./console exp:ini where menu.ini/SiteInfo/MainMenuID site` und dasselbe für `FooterMenuID`.
3. `./console exp:layouts export --all --output=inventory.zip` für alle Details (`layouts.json`).

## Welchen Weg Sie wählen

Die Ausgabe der Konsole lässt sich beim Projekt aufbewahren und später vergleichen.

## Prüfen

- Die Bestandsaufnahme nennt jedes Layout, seine Zuordnungen und seine Verknüpfungen.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../../../../eng-us/help/build-two-ways/export-and-import-layouts.md)
- [exp:ini für Site-Entwickler](../../exp-ini-fuer-site-entwickler.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
