---
title: Die 6.0.x-Releases auf einen Blick
audience: press
category: press-media
order: 14
summary: Jedes Release von Exponential 6.0.x mit seinem Datum und seinem dokumentierten Schwerpunkt, für Reporter, die eine Release-Geschichte brauchen - dazu, wo der Changelog jedes Releases steht, wie Sie die Liste selbst prüfen und wie Sie über 6.0.15 schreiben, bevor es veröffentlicht ist.
keywords: Releases, Release-Geschichte, Versionen, 6.0.0, 6.0.10, 6.0.14, 6.0.15, Changelog, Tags, Daten, Veröffentlichungsdaten, releases, release history, versions, changelog, tags, dates, release dates
type: help
---
# Die 6.0.x-Releases auf einen Blick

Diese Seite ist für Reporter, die die Release-Geschichte von Exponential 6 brauchen. Sie listet jedes Release 6.0.x mit
seinem Datum auf, nach [doc/eng-us/release-overview](../../../eng-us/release-overview) und den Git-Tags, Stand
**18. Oktober 2026**.

## Inhalt

1. [Die Releases](#die-releases)
2. [Die Changelogs](#die-changelogs)
3. [Die Liste selbst prüfen](#die-liste-selbst-prüfen)
4. [Über Releases schreiben](#über-releases-schreiben)

## Die Releases

| Version | Datum | Dokumentierter Schwerpunkt |
|---|---|---|
| 6.0.0 | 1. Januar 2024 | Das erste stabile Release der Linie (SQLite folgte in 6.0.1) |
| 6.0.1 | 29. Januar 2024 | SQLite als Datenbank, die keinen Server braucht |
| 6.0.2 | 29. Februar 2024 | |
| 6.0.3 | 2. April 2024 | |
| 6.0.4 | 5. September 2024 | Das erste Release nach admin3, dem responsiven Design der Administration |
| 6.0.5 | 1. Oktober 2024 | |
| 6.0.6 | 17. Dezember 2024 | |
| 6.0.7 | 1. Februar 2025 | Das letzte Release für PHP 7.4 |
| 6.0.8 | 2. Mai 2025 | Das erste Release nach Beginn der Unterstützung für PHP 8.4 |
| 6.0.9 | 10. Juni 2025 | |
| 6.0.10 | 28. August 2025 | Das erste Release unter dem Namen Exponential |
| 6.0.11 | 30. Dezember 2025 | Veröffentlicht in dem Monat, in dem die Unterstützung für PHP 8.5 kam |
| 6.0.12 | 10. Februar 2026 | |
| 6.0.13 | 18. April 2026 | |
| 6.0.14 | 7. Juni 2026 | MongoDB 8 als Datenbank |
| 6.0.15 | nicht veröffentlicht | In Entwicklung: Velocity, admin4, Prüfprotokoll, Exponential Layouts, Exponential Intelligence, das Hilfesystem und mehr |

Quelle: [Die 6.0.x-Releases](../wer-ist-7x.md#die-releases-60x). Eine leere Zelle beim Schwerpunkt bedeutet, dass die
Geschichtsseiten für dieses Release keine einzelne Hauptnachricht nennen; lesen Sie seinen Changelog.

Die Releases 6.0.0 bis 6.0.9 wurden veröffentlicht, als das Produkt noch seinen früheren Namen trug; bezeichnen Sie sie
in der Vergangenheitsform als Releases von Exponential, zum Beispiel „6.0.0, veröffentlicht am 1. Januar 2024“
([Versionsnummern im Text](versionsnummern-im-text.md)).

## Die Changelogs

| Release | Changelog |
|---|---|
| 6.0.0 | [6.0.0](../../../changelogs/6.0/6.0.0.md) |
| 6.0.10 | [6.0.10](../../../changelogs/6.0/6.0.10.md) |
| 6.0.14 | [6.0.14](../../../changelogs/6.0/6.0.14.md) |
| 6.0.15 (in Entwicklung) | [6.0.15](../../../changelogs/6.0/6.0.15.md) |
| Jedes Release | [Changelogs](../../../changelogs/README.md) |

Wie Sie sie lesen: [Wo Changelogs und Release Notes stehen](wo-changelogs-und-release-notes-stehen.md) und
[Einen Changelog lesen](ein-changelog-lesen.md).

## Die Liste selbst prüfen

```bash
git clone https://github.com/se7enxweb/exponential.git && cd exponential
git tag -l 'v6.0.*' --sort=version:refname
git log -1 --format=%ad v6.0.14
```

Sortieren Sie nach Version, nie nach Name: Eine einfache Sortierung nach Name setzt `v6.0.10` vor `v6.0.2`
([Wie Versionsnummern funktionieren](wie-versionsnummern-funktionieren.md)).

## Über Releases schreiben

- Ein Release ist ein Git-Tag; solange der Tag nicht existiert, ist die Version „in Entwicklung“ ([Über 6.0.15 berichten, solange es in Entwicklung ist](ueber-6-0-15-in-entwicklung-berichten.md)).
- Ein veröffentlichtes Release wird nie geändert; eine Korrektur kommt als nächstes Release ([Veröffentlichte Versionen ändern sich nie](veroeffentlichte-versionen-aendern-sich-nie.md)).
- Schreiben Sie die Version im Fließtext ohne „v“: „Exponential 6.0.14“.

## Verwandte Seiten

- [Über ein Release berichten](ueber-ein-release-berichten.md)
- [Zeitleiste für Reporter](zeitleiste-fuer-reporter.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
