---
title: Wie Pakete im Ökosystem versioniert werden
audience: ecosystem, developer, admin, partner
category: ecosystem
order: 23
summary: Die Regeln zur Versionierung, die im ganzen Ökosystem von Exponential gelten - veröffentlichte Tags ändern sich nie, die letzte Stelle zählt über neun hinaus weiter, Sortierung nach Version, die Tilde als Einschränkung und Versionsfelder in einer Erweiterung - und was sie für jemanden bedeuten, der sich auf ein Paket stützt.
keywords: Versionierung, Versionsnummern, Tags, unveränderliche Tags, semantisch, Tilde, sort -V, version:refname, ezinfo.php, extension.xml, Packagist, versioning, version numbers, tags, immutable tags, semantic, tilde constraint
type: help
---
# Wie Pakete im Ökosystem versioniert werden

## Inhalt

1. [Veröffentlichte Versionen ändern sich nie](#veröffentlichte-versionen-ändern-sich-nie)
2. [Die letzte Stelle zählt weiter](#die-letzte-stelle-zählt-weiter)
3. [Einschränkungen in composer.json](#einschränkungen-in-composerjson)
4. [Versionen in einer Erweiterung](#versionen-in-einer-erweiterung)
5. [Was das für Sie bedeutet](#was-das-für-sie-bedeutet)

## Veröffentlichte Versionen ändern sich nie

"A published tag is never changed: a fix for a problem in `v6.0.14` or an earlier tag reaches you as the next release,
not as a corrected copy of the old one", ein veröffentlichtes Tag wird also nie geändert; eine Korrektur für `v6.0.14`
oder ein früheres Tag erreicht Sie als nächstes Release, nicht als korrigierte Kopie des alten (`SECURITY.md`).
Dasselbe gilt für Erweiterungen ([Tags sind dauerhaft](../../erweiterungsentwicklung/teil-7-veroeffentlichen/tags-sind-dauerhaft.md)).

## Die letzte Stelle zählt weiter

"After `0.1.9` the next release is `0.1.10`; only the last position counts up unless you decide otherwise", nach
`0.1.9` kommt also `0.1.10`; nur die letzte Stelle zählt hoch, sofern Sie nicht anders entscheiden
([Releases und Versionsnummern](../../wer-ist-die-exponential-foundation.md#releases-und-versionsnummern)). Eine einfache
alphabetische Sortierung setzt `0.0.4.10` vor `0.0.4.8`; sortieren Sie mit `git tag --sort=version:refname` oder
`sort -V`. Composer und Packagist vergleichen jede Stelle als Zahl.

## Einschränkungen in composer.json

Die Einschränkungen des Kernels verwenden `~`, das "lets the last position float: `~6.0.8` accepts `6.0.9` and `6.0.10`
but not `6.1.0`", die letzte Stelle also beweglich lässt ([Die Standardverteilung der Erweiterungen](../../../funktionen/6.0/standard-extension-auslieferung.md)).
Velocity zum Beispiel wird mit `~0.0.4.57` verlangt (`README.md`).

## Versionen in einer Erweiterung

Eine Erweiterung nennt ihre Release-Nummer in `ezinfo.php` und `extension.xml`
([ezinfo.php und extension.xml hochzählen](../../erweiterungsentwicklung/teil-7-veroeffentlichen/ezinfo-und-extension-xml-anheben.md)),
damit die About-Seite zeigt, welches Release installiert ist ([About- und Paketseiten](../../../funktionen/6.0/info-und-paketseiten.md)).

## Was das für Sie bedeutet

- Eine Versionsnummer bedeutet immer denselben Code.
- Um eine Korrektur zu bekommen, aktualisieren Sie auf die nächste Version.
- Wenn Sie eine Version wählen, sehen Sie sich die veröffentlichten, nach Version sortierten Tags an, nicht Ihr
  Gedächtnis.
- Ihre eigenen Einschränkungen mit `~` übernehmen Korrekturen in der letzten Stelle bei `composer update` automatisch.

## Verwandte Seiten

- [Pakete](README.md)
- [Regeln zur Versionierung](../../erweiterungsentwicklung/teil-7-veroeffentlichen/regeln-fuer-versionsnummern.md)
- [Wie Releases entstehen](../../../../eng-us/help/community/how-it-works/how-releases-happen.md)
- [Die nächste Version wählen](../../erweiterungsentwicklung/teil-7-veroeffentlichen/die-naechste-version-waehlen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
