---
title: Wie Exponential-Releases entstehen
audience: community, admin, developer, hosting
category: community
order: 52
summary: Wie eine Änderung von einem Pull Request zu einem getaggten Exponential-Release gelangt, warum veröffentlichte Tags sich nie ändern, wie Versionsnummern zählen, welche Linien unterstützt werden und wo Community-Mitglieder unterwegs helfen können.
keywords: Release, Tag, Version, main, dev-main, 6.0.x, unveränderliche Tags, Changelog, Packagist, unterstützte Versionen, Upgrade, Versionsnummern, release, immutable tags, supported versions, version numbers
type: help
---
# Wie Exponential-Releases entstehen

## Inhalt

1. [Der Weg einer Änderung](#der-weg-einer-änderung)
2. [Veröffentlichte Versionen ändern sich nie](#veröffentlichte-versionen-ändern-sich-nie)
3. [Versionsnummern](#versionsnummern)
4. [Welche Linien unterstützt werden](#welche-linien-unterstützt-werden)
5. [Wo Sie helfen können](#wo-sie-helfen-können)

## Der Weg einer Änderung

```text
pull request  ->  review rounds  ->  squash and rebase  ->  merged on main (dev-main)
      ->  tested by maintainers and community  ->  tagged release (v6.0.x)  ->  Packagist  ->  your site
```

1. Eine Änderung kommt als Pull Request an und durchläuft das Review ([Reviews geben und erhalten](../mitwirken/reviews-geben-und-erhalten.md)).
2. Sie wird auf `main` gemergt, der Entwicklungslinie („der Linie 6.0.15, `dev-main`“, `SECURITY.md`).
3. Ein Release wird von `main` getaggt und veröffentlicht; jedes Paket ist ein GitHub-Repository und ein
   Composer-Paket ([Releases und Versionsnummern](../../wer-ist-die-exponential-foundation.md#releases-und-versionsnummern)).
4. Websites aktualisieren darauf ([Mit Git aktualisieren](../../mit-git-aktualisieren.md), [Upgrade](../../../installation/11-aktualisieren.md)).

Die Änderungen jedes Release stehen in `doc/changelogs/6.0/`, zum Beispiel im
[Changelog 6.0.14](../../../../changelogs/6.0/6.0.14.md).

## Veröffentlichte Versionen ändern sich nie

„Ein veröffentlichter Tag wird nie geändert: Eine Korrektur für ein Problem in `v6.0.14` oder einem früheren Tag
erreicht Sie als nächstes Release, nicht als korrigierte Kopie des alten“ (`SECURITY.md`). Für Sie bedeutet das: Eine
Versionsnummer bedeutet immer denselben Code, und der Weg zu einer Korrektur ist das Upgrade.

## Versionsnummern

- „Nach `0.1.9` ist das nächste Release `0.1.10`; nur die letzte Stelle zählt hoch, sofern Sie nichts anderes
  entscheiden“ ([Releases und Versionsnummern](../../wer-ist-die-exponential-foundation.md#releases-und-versionsnummern)).
- Sortieren Sie Versionen mit `git tag --sort=version:refname` oder `sort -V`; eine einfache Sortierung stellt
  `0.0.4.10` vor `0.0.4.8`.
- Jede Erweiterung gibt ihre Version in `ezinfo.php` und `extension.xml` an
  ([Regeln zur Versionierung](../../erweiterungsentwicklung/teil-7-veroeffentlichen/regeln-fuer-versionsnummern.md)).

## Welche Linien unterstützt werden

| Version | Unterstützt |
|---|---|
| 6.0.x, aktuelle Linie (`main`, `dev-main` und der nächste davon abgeleitete Tag) | Ja |
| Veröffentlichte 6.0.x-Tags | Korrektur durch Upgrade auf das neueste 6.0.x |
| 5.x, 4.x, 3.x und älter | Nein |

Quelle: `SECURITY.md`, „Supported Versions“.

## Wo Sie helfen können

| Phase | Hilfe |
|---|---|
| Pull Request | Prüfen und testen ([Die Arbeit anderer prüfen](../mitwirken/reviews-geben-und-erhalten.md#die-arbeit-anderer-prüfen)) |
| Auf `main` | Die Entwicklungslinie auf Ihrer Einrichtung testen ([Releases testen](../mitwirken/releases-testen.md)) |
| Nach dem Release | Korrekturen in ihren Issues bestätigen, aktualisieren, darüber schreiben ([Über ein Release schreiben](../../fuersprecher/ueber-ein-release-schreiben.md)) |

## Verwandte Seiten

- [So funktioniert die Community](README.md)
- [Auf dem Laufenden bleiben](../beitreten/auf-dem-laufenden-bleiben.md)
- [Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md)
- [Wie Updates funktionieren](../../kunden/gesund-halten/wie-updates-funktionieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
