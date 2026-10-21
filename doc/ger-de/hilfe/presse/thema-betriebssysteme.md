---
title: "Thema: Betriebssysteme"
audience: press
category: press-media
order: 71
summary: Ein Themenbeitrag darüber, wo Exponential läuft - die vier dokumentierten Stufen von „hier getestet“ bis „heute nicht möglich“, Linux-Distributionen, BSD, macOS, Windows, Container und Prozessorarchitekturen - mit der Ehrlichkeit, eine begründete Erwartung von einem Testergebnis zu unterscheiden.
keywords: Thema, Betriebssysteme, Linux, BSD, FreeBSD, macOS, Windows, Container, Docker, Stufen, Unterstützungsmatrix, AlmaLinux, Ubuntu, Debian, story, operating systems, containers, tiers, support matrix
type: help
---
# Thema: Betriebssysteme

Diese Seite ist für Journalisten, die über Plattformen und Hosting berichten. Die Dokumentation von Exponential ordnet
Betriebssysteme in vier Stufen ein und sagt offen, welche getestet sind und von welchen nur erwartet wird, dass sie
funktionieren.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
3. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
4. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)

## Das Thema in einem Absatz

Exponential läuft auf „almost any [operating system] that runs PHP 8.0 or later“, und seine Dokumentation ordnet diese
in vier Stufen ein: **hier getestet** (das Projekt betreibt es täglich oder seine CI testet darauf), **funktioniert
nachweislich**, **sollte funktionieren** („a reasoned expectation, not a test result“) und **heute nicht möglich**
([Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md)). Stufe 1 ist der eigene Entwicklungsserver des Projekts auf
AlmaLinux 9 und die CI auf Ubuntu; Stufe 2 umfasst die Red-Hat-Familie, Debian, Ubuntu, Alpine, FreeBSD, macOS, Windows
und Container.

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Vier Stufen, mit Definitionen | [Die vier Stufen](../../../eng-us/help/os-support.md#the-four-tiers) |
| Stufe 1: AlmaLinux 9 (der Entwicklungsserver) und Ubuntu auf GitHub Actions (CI auf PHP 8.1 bis 8.5) | [Stufe 1: hier getestet](../../../eng-us/help/os-support.md#tier-1-tested-here) |
| Stufe 2: RHEL 9 und 10 und Verwandte, Debian 12 und 13, Ubuntu 22.04 und 24.04, Alpine, Plesk-Hosts, FreeBSD 14, DragonFly BSD, macOS, Windows, Docker und Podman | [Stufe 2: funktioniert nachweislich](../../../eng-us/help/os-support.md#tier-2-known-to-work) |
| Velocity liefert `.deb`-Pakete für Debian und Ubuntu, ein Binary für macOS arm64 und Container-Images für amd64 und arm64 | ebenda |
| Wer es auf einem System der Stufe 3 betreibt, wird gebeten, das zu melden, damit die Zeile aufsteigen kann | [Die vier Stufen](../../../eng-us/help/os-support.md#the-four-tiers) |

## Was Sie nicht behaupten sollten

| Sagen Sie nicht | Sagen Sie stattdessen |
|---|---|
| „Läuft überall“ (wie „Works Everywhere, On Any OS“ im Pressekit) | „Läuft auf vielen Systemen, eingeordnet in dokumentierte Stufen“ |
| „Auf Windows getestet“ | Windows ist Stufe 2, funktioniert nachweislich; die Seite nennt, was fehlt |
| „Läuft mit Velocity auf Shared Hosting“ | Velocity braucht meist einen eigenen Server; klassisches Hosting nutzt Apache oder Nginx mit PHP |

## Blickwinkel und Ideen für Überschriften

- „Eine Unterstützungsmatrix, die zugibt, was sie nicht getestet hat.“
- „Von AlmaLinux bis Apple Silicon: wo ein PHP-CMS läuft.“

## Verwandte Seiten

- [Thema: Freiheit beim Hosting](thema-freiheit-beim-hosting.md)
- [Thema: aktuelles PHP](thema-aktuelles-php.md)
- [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
