---
title: Standards und Werkzeuge im Ökosystem
audience: ecosystem, developer, partner
category: ecosystem
order: 36
summary: Die Standards und Werkzeuge, die im Ökosystem von Exponential geteilt werden - PSR und die Programmierstandards des Projekts, Präfixe für Namen, Präfixe für Commit-Nachrichten, das Datei-Manifest, Git-Hooks, CI-Prüfungen sowie Standards für Barrierefreiheit, Sicherheit und Dokumentation.
keywords: Standards, PSR, Programmierstandards, Präfixe für Namen, exp, sevenx, Commit-Nachrichten, filelist.md5, make hooks, CI, PHPStan, PHPUnit, Barrierefreiheit, Sicherheit, Dokumentation, standards, coding standards, naming prefixes, commit messages, accessibility, security, documentation
type: help
---
# Standards und Werkzeuge im Ökosystem

Pakete, die denselben Standards folgen, arbeiten leichter zusammen und lassen sich leichter prüfen.

## Inhalt

1. [Standards für Code](#standards-für-code)
2. [Konventionen im Repository](#konventionen-im-repository)
3. [Prüfungen](#prüfungen)
4. [Weitere Standards](#weitere-standards)

## Standards für Code

- "Follow the PSR standards and our coding style", folgen Sie also den PSR-Standards und dem Programmierstil des
  Projekts (`CONTRIBUTING.md`; [Programmierstandards](../../../standards/programmierstandards.md)).
- Benennen Sie neuen Code nach der Präfixregel: `sevenx` oder `exp` für Code von 7x, `exp` für neuen Kernel-Code;
  vorhandene Bezeichner wie `eZContentObject` werden nie umbenannt ([Präfixe für Namen](../../../standards/namenspraefixe-sevenx-und-exp.md)).
- Konventionen für PHP und Composer: [PHP und Composer](../../../standards/php-und-composer.md).

## Konventionen im Repository

| Konvention | Quelle |
|---|---|
| Commit-Nachrichten beginnen mit `Added:`, `Updated:`, `Fixed:`, `Removed:` oder `Renamed:` | `CONTRIBUTING.md` |
| Jede geänderte Datei aktualisiert ihre Zeile in `share/filelist.md5` (`make manifest-fix`) | `CONTRIBUTING.md` |
| `make hooks` installiert die Hooks pre-commit und commit-msg | `CONTRIBUTING.md` |
| Pull-Requests werden mit Squash und Rebase übernommen; `main` wird nie umgeschrieben | `CONTRIBUTING.md` |
| Veröffentlichte Tags ändern sich nie | `SECURITY.md` |

Versionsverwaltung und Releases: [Versionsverwaltung und Releases](../../../standards/versionsverwaltung-und-releases.md).

## Prüfungen

Die CI lässt die Testsuite auf PHP 8.1 bis 8.5 laufen, prüft, ob PHP 8.0 startet, und führt statische Analyse und
Stilprüfungen aus ([Qualitätsprüfungen](../../../funktionen/6.0/qualitaetspruefungen.md), [CI](../../../bc/6.0/ci.md),
[Tests und CI](../../../standards/tests-und-ci.md)).

## Weitere Standards

| Bereich | Seite |
|---|---|
| Barrierefreiheit | [Barrierefreiheit](../../../standards/barrierefreiheit.md) |
| Sicherheit | [Sicherheit](../../../standards/sicherheit.md) |
| Datenschutz | [Datenschutz](../../../standards/datenschutz.md) |
| Internationalisierung | [Internationalisierung](../../../standards/internationalisierung.md) |
| APIs und Protokolle | [APIs und Protokolle](../../../standards/apis-und-protokolle.md) |
| Dokumentation und Veröffentlichung | [Dokumentation und Veröffentlichung](../../../standards/dokumentation-und-veroeffentlichung.md) |
| Die Gestaltung der Administration | [Die Administration gestalten](../../../bc/6.0/admin4-gestaltung.md) |

## Verwandte Seiten

- [Der PHP-Stack](README.md)
- [Standards](../../../standards/README.md)
- [Code beitragen](../../gemeinschaft/mitwirken/code-beitragen.md)
- [Commit-Nachrichten](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/commit-nachrichten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
