---
title: "Den Weg zurück planen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1532
summary: Exporte der Layouts, Sicherungen der INI-Dateien (exp:ini legt sie unter var/backup/ini/ ab) und das vorherige Release der Erweiterung.
keywords: Rückfallplan, Sicherungen, var/backup/ini, Exporte, Migration, rollback plan, backups, exports, migration
type: help
---
# Den Weg zurück planen

Jeder Schritt der Migration lässt sich rückgängig machen.

*Lebenszyklus: Migrieren, Beispiel 33 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Sicherungen](#sicherungen)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Ein `exp:layouts export --all` von unmittelbar vor der Migration.

## Sicherungen

1. `exp:ini` schreibt vor jeder Änderung eine Sicherung nach `var/backup/ini/<timestamp>/`; bewahren Sie das vorherige Release-Tag Ihrer Erweiterung auf.

## Welchen Weg Sie wählen

Drei Sicherungen, eine pro Art von Änderung.

## Prüfen

- Jede Sicherung ist auffindbar und wurde einmal gelesen.

## Verwandte Seiten

- [Ein Layout zurücksetzen](../../../../../eng-us/help/build-two-ways/roll-back-a-layout.md)
- [exp:ini für Site-Entwickler](../../../../../eng-us/help/build-two-ways/exp-ini-for-site-builders.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
