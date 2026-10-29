---
title: Betriebssysteme im Ökosystem
audience: ecosystem, admin, hosting
category: ecosystem
order: 34
summary: Die Betriebssysteme, auf denen das Ökosystem von Exponential läuft, nach den vier Stufen der Seite zur Unterstützung von Betriebssystemen - hier getestet, bekanntermaßen funktionsfähig, voraussichtlich funktionsfähig, heute nicht möglich - und wie Anwender ein System eine Stufe höher bringen.
keywords: Betriebssysteme, Linux, AlmaLinux, RHEL, Debian, Ubuntu, Alpine, FreeBSD, macOS, Windows, Docker, Podman, Stufen, Unterstützung, operating systems, tiers, support
type: help
---
# Betriebssysteme im Ökosystem

Exponential läuft auf "almost any [operating system] that runs PHP 8.0 or later (PHP 7.4 usable without support)", also auf fast jedem Betriebssystem,
auf dem PHP 8.0 oder neuer (PHP 7.4 ohne Support nutzbar) läuft (`README.md`, "Requirements"). Die Hilfe ordnet sie in vier Stufen
([Unterstützte Betriebssysteme](../../betriebssystemunterstuetzung.md)).

## Inhalt

1. [Die vier Stufen](#die-vier-stufen)
2. [Beispiele nach Stufe](#beispiele-nach-stufe)
3. [Ein System eine Stufe höher bringen](#ein-system-eine-stufe-höher-bringen)

## Die vier Stufen

| Stufe | Bedeutung |
|---|---|
| 1. Hier getestet | "The project runs Exponential on it every day, or its CI runs the test suites on it on every push", das Projekt betreibt Exponential täglich darauf, oder seine CI lässt bei jedem Push die Testsuiten darauf laufen |
| 2. Bekanntermaßen funktionsfähig | Durch Dokumentation, Pakete oder die eigene CI von Velocity abgedeckt, oder dasselbe PHP auf demselben Kernel wie Stufe 1 |
| 3. Voraussichtlich funktionsfähig | Ein gepflegtes PHP 8.x existiert, und nichts steht im Weg; "a reasoned expectation, not a test result", eine begründete Erwartung, kein Testergebnis |
| 4. Heute nicht möglich | Es gibt kein PHP 8, oder die Hardware kann es nicht ausführen |

## Beispiele nach Stufe

| Stufe | Systeme (Auswahl) |
|---|---|
| 1 | AlmaLinux 9 (der Entwicklungsserver des Projekts); Ubuntu in GitHub Actions (die CI) |
| 2 | RHEL 9 und 10 und ihre Nachbauten, Debian 12 und 13, Ubuntu 22.04 und 24.04, Alpine, Plesk-Hosts, FreeBSD 14, DragonFly BSD, macOS, Windows, Docker- und Podman-Container |
| 3 und 4 | Siehe die vollständige Seite |

Velocity veröffentlicht `.deb`-Pakete, ein Binary für macOS und Container-Images unter
`ghcr.io/se7enxweb/exponential-velocity` ([Unterstützte Betriebssysteme](../../betriebssystemunterstuetzung.md)).

## Ein System eine Stufe höher bringen

"If you run Exponential on a tier 3 system, please tell the project on the community tracker or the software tracker,
and the row moves up", wenn Sie Exponential also auf einem System der Stufe 3 betreiben, sagen Sie es dem Projekt im
Tracker der Community oder der Software, und die Zeile rückt auf ([Unterstützte Betriebssysteme](../../betriebssystemunterstuetzung.md)).
Das ist ein Beitrag, den jeder Anwender leisten kann ([Einen Fehler melden](../../gemeinschaft/mitwirken/einen-fehler-melden.md)).

## Verwandte Seiten

- [Der PHP-Stack](README.md)
- [Unterstützte Betriebssysteme](../../betriebssystemunterstuetzung.md)
- [Betriebssysteme für Hoster](../../hosting/grundlagen/betriebssysteme-fuer-hoster.md)
- [Velocity-Pakete und -Binaries](../../../funktionen/6.0/velocity-pakete-und-binaerdateien.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
