---
title: "Die Neugestaltung auf einer Kopie durchführen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1418
summary: Templates in einem Branch Ihrer Erweiterung, Layouts in einer Sandbox-Kopie; dann die Templates einspielen und die Layouts importieren.
keywords: Neugestaltung, Sandbox, Branch, Staging, Export Import, redesign, sandbox, branch, staging, export import
type: help
---
# Die Neugestaltung auf einer Kopie durchführen

Die Live-Site läuft weiter, während das neue Design gebaut wird.

*Lebenszyklus: Neugestaltung, Beispiel 19 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [In einem Branch](#in-einem-branch)
3. [Beim Livegang](#beim-livegang)
4. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
5. [Prüfen](#prüfen)

## Im Layout-Editor

1. Bauen Sie die neuen Layouts auf der Kopie; exportieren Sie sie (`exp:layouts export --only=...`).

## In einem Branch

1. Entwickeln Sie die Templates in einem Branch; testen Sie auf der Kopie.

## Beim Livegang

1. Spielen Sie die Erweiterung ein, importieren Sie die Layouts (`--strategy=overwrite --publish` oder `copy`), stellen Sie die Zuordnungen um, leeren Sie Template & Inhalt.

## Welchen Weg Sie wählen

Beide Hälften gehen gemeinsam live.

## Prüfen

- Der Probe-Import auf dem Live-System zeigt die erwarteten Einträge.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../layouts-exportieren-und-importieren.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
