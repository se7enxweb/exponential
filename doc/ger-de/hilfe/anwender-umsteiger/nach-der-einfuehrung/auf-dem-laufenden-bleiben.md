---
title: Exponential nach der Einführung aktuell halten
audience: adopter, customer, admin, hosting
category: adoption
order: 62
summary: Wie Anwender ihre Exponential-Installation aktuell halten - warum Aktualisierungen zählen, die Linie 6.0 und unveränderliche Releases, eine Routine zum Prüfen und Einspielen von Aktualisierungen, Erweiterungen, PHP-Versionen und wer was tut.
keywords: Aktualisierungen, Updates, Upgrades, auf dem Laufenden bleiben, 6.0.x, Sicherheitsversionen, Routine, Erweiterungen, PHP-Version, Git, Composer, Tag des Updates, updates, upgrades, keeping current, security releases, routine, extensions, PHP version, update day
type: help
---
# Exponential nach der Einführung aktuell halten

## Inhalt

1. [Warum es wichtig ist](#warum-es-wichtig-ist)
2. [Wie Releases Sie erreichen](#wie-releases-sie-erreichen)
3. [Eine Routine](#eine-routine)
4. [Erweiterungen und PHP](#erweiterungen-und-php)

## Warum es wichtig ist

Sicherheitskorrekturen entstehen auf `main` und werden "published with the next 6.0.x release", also mit dem nächsten
Release 6.0.x veröffentlicht; "a fix for a problem in `v6.0.14` or an earlier tag reaches you as the next release, not
as a corrected copy of the old one", eine Korrektur für `v6.0.14` oder ein früheres Tag erreicht Sie also als nächstes
Release, nicht als korrigierte Kopie des alten (`SECURITY.md`). Eine Website, die nicht aktualisiert wird, erhält keine
Korrekturen.

## Wie Releases Sie erreichen

- Über Composer oder Git, je nachdem, wie die Website installiert wurde ([Mit Git aktualisieren](../../mit-git-aktualisieren.md)).
- **Setup > Updates and packages** (Aktualisierungen und Pakete) in der Administration findet und installiert
  Composer-Pakete ([Roadmap](../../projekt/roadmap.md)).
- Was sich geändert hat: die Changelogs ([Auf dem Laufenden bleiben](../../gemeinschaft/beitreten/auf-dem-laufenden-bleiben.md)).

## Eine Routine

| Wann | Tun |
|---|---|
| Monatlich | Nach neuen Releases sehen und ihre Hinweise lesen |
| Innerhalb von Tagen nach einer Sicherheitsversion | Sie einspielen, zuerst auf Staging |
| Bei jedem Update | Der [Checkliste für den Tag des Upgrades](../../kunden/gesund-halten/checkliste-upgrade-tag.md) folgen |
| Jährlich | Die PHP-Version und die Erweiterungen prüfen |

Wie Updates für Betreiber funktionieren: [Wie Updates funktionieren](../../kunden/gesund-halten/wie-updates-funktionieren.md). Mit
einem Anbieter: [Upgrades mit Ihrem Anbieter](../../kunden/gesund-halten/upgrades-mit-ihrem-anbieter.md).

## Erweiterungen und PHP

- Jede Erweiterung hat eigene Releases ([Wie Pakete versioniert werden](../../oekosystem/pakete/wie-pakete-versioniert-werden.md)).
- Halten Sie PHP auf einer unterstützten Version: Exponential läuft auf 8.0 bis 8.5 (`README.md`).
- Individueller Code in Erweiterungen erleichtert Aktualisierungen ([Erweitern ohne Fork](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/erweitern-ohne-zu-forken.md)).

## Verwandte Seiten

- [Nach der Einführung](README.md)
- [Release-Linie und Unterstützung](../../kunden/gesund-halten/release-linie-und-support.md)
- [Upgrades ohne Überraschungen](../../unternehmen/zuverlaessigkeit/aktualisierungen-ohne-ueberraschungen.md)
- [Aktualisieren](../../../installation/11-aktualisieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
