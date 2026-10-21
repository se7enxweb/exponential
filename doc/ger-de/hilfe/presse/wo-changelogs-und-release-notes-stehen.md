---
title: Wo Changelogs und Release Notes stehen
audience: press
category: press-media
order: 101
summary: Eine Karte aller Orte, die festhalten, was eine Version von Exponential enthält - die Changelog-Seiten, die Release-Übersicht, GitHub Releases, die Release Notes von Erweiterungen, Velocity und den Plattformpaketen, die Geschichte und das Ledger - und welche Sie wofür zitieren.
keywords: Changelog, Release Notes, Release-Übersicht, GitHub Releases, Geschichte, Ledger, Erweiterungen, Velocity, Plattform, Quellen, wo nachsehen, changelog, release notes, release overview, history, ledger, extensions, platform, sources, where to look
type: help
---
# Wo Changelogs und Release Notes stehen

Diese Seite ist für Reporter und Creator, die schnell den offiziellen Nachweis eines Releases finden müssen.
Exponential hält seine Release-Informationen im Repository selbst, neben dem Code, sodass jede Version von jedem geprüft
werden kann. Hier steht, wo jede Art von Nachweis liegt und wofür sie taugt.

## Inhalt

1. [Der Kernel: Exponential 6](#der-kernel-exponential-6)
2. [Erweiterungen, Velocity und die Plattformpakete](#erweiterungen-velocity-und-die-plattformpakete)
3. [Die Geschichte und das Ledger](#die-geschichte-und-das-ledger)
4. [Welche Quelle Sie wofür zitieren](#welche-quelle-sie-wofür-zitieren)
5. [Wenn sich zwei Quellen widersprechen](#wenn-sich-zwei-quellen-widersprechen)

## Der Kernel: Exponential 6

| Nachweis | Wo | Was er Ihnen liefert |
|---|---|---|
| Changelog einer Version | `doc/changelogs/6.0/<version>.md`, zum Beispiel [6.0.14](../../../changelogs/6.0/6.0.14.md) | Das Veröffentlichungsdatum, den Tag, „In short“, „Upgrading“ und die Änderungen, gruppiert als Added, Updated, Removed, Renamed, jede mit ihrem Commit |
| Die Entwicklungslinie | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md) | „Not released yet“; was die Linie an dem Datum enthält, das die Seite nennt |
| Release-Übersicht | [doc/eng-us/release-overview](../../../eng-us/release-overview) | Ein Eintrag pro Version seit 2002, mit Tag, Datum, Release-URL und Changelog-URL; für 6.0.x nennt sie außerdem das „Release date“ von GitHub, wo es abweicht |
| GitHub Releases | [github.com/se7enxweb/exponential/releases](https://github.com/se7enxweb/exponential/releases) | Das veröffentlichte Release mit seinen Notes, eines pro Tag |
| Ältere Linien | Ordner wie `doc/changelogs/4.7/` und `doc/changelogs/5.2/` im Repository | Changelogs in Textform aus den eZ-Publish-Jahren, als Geschichte aufbewahrt |

Die Changelog-Seiten der Linie 6.0 verlinken einander („Releases before and after“) und die Monatsseiten der
Geschichte, die denselben Zeitraum als Erzählung schildern.

## Erweiterungen, Velocity und die Plattformpakete

Jedes Repository, das neben Exponential ausgeliefert wird, hat eigene Release Notes, gesammelt in einer Übersicht:
[Release Notes der Erweiterungen, Themes, des Webservers und der Plattformpakete](../../../changelogs/extensions/README.md).

| Gruppe | Beispiel | Hinweis |
|---|---|---|
| Erweiterungen und Themes | [eztags](../../../changelogs/extensions/eztags.md), [cjw_newsletter](../../../changelogs/extensions/cjw_newsletter.md) | Jede listet ihre getaggten Releases auf, das neueste zuerst |
| Zwei Erweiterungen führen ihre Notes im eigenen Repository | expai (Exponential Intelligence) und expqueue | Die Übersicht verlinkt ihre Ordner `doc/changelog` |
| Der Webserver | [exponential-velocity](../../../changelogs/extensions/exponential-velocity.md) | Ab v0.0.4.43 stehen die vollständigen Notes auf [GitHub Releases](https://github.com/se7enxweb/exponential-velocity/releases) |
| Plattformpakete | [exponential-platform-nexus](../../../changelogs/extensions/exponential-platform-nexus.md), [exponential-platform-dxp](../../../changelogs/extensions/exponential-platform-dxp.md) | Nur die Änderungen des Teams von se7enxweb; die Upstream-Geschichte wird anderswo erzählt |

Mehr dazu: [Über ein Release einer Erweiterung berichten](ueber-ein-erweiterungs-release-berichten.md),
[Über Releases von Velocity berichten](ueber-velocity-releases-berichten.md),
[Über Releases von Exponential Platform berichten](ueber-platform-releases-berichten.md).

## Die Geschichte und das Ledger

| Nachweis | Wo | Was er Ihnen liefert |
|---|---|---|
| Die Chronik | [Die Geschichte](../../../eng-us/history/README.md) | Monatsseiten ab Dezember 2023: was sich geändert hat, warum es wichtig ist, mit Commits |
| Das Ledger | [Ledger aller Repositorys](../../../eng-us/history/ledger/README.md) | Jeder Commit jedes Repositorys, mit Datum, Art, Umfang und dem Release-Tag, der ihn zuerst enthielt |
| Die Seite zur Abdeckung | [Abdeckung](../../../eng-us/history/coverage.md) | Der Beleg, pro Repository und Monat, dass jede Änderung erklärt oder aufgeführt ist |
| Die eigene Chronik von Velocity | [Chronik von Velocity](../../../eng-us/history/velocity/README.md) | Der Webserver ab seinem ersten Commit im Juli 2026 |

Wie Sie sie zitieren, steht unter [Die Geschichtsseiten als Quelle](die-geschichtsseiten-als-quelle.md).

## Welche Quelle Sie wofür zitieren

| Sie wollen drucken | Zitieren Sie |
|---|---|
| „Version X wurde am Datum Y veröffentlicht“ | Die Changelog-Seite von X und die Release-Übersicht |
| „Version X bringt Funktion Z“ | Den Changelog von X, dazu die Funktionsseite, die er verlinkt |
| „Funktion Z kam im Monat M“ | Die Monatsseite der Geschichte |
| „Das Projekt hat N Releases veröffentlicht“ | Die Liste der Tags (`git tag -l 'v6.0.*' --sort=version:refname`) oder [Wer ist 7x?, Die 6.0.x-Releases](../wer-ist-7x.md#die-releases-60x) |
| „Erweiterung E in Version V macht ...“ | Die Seite mit den Release Notes von E |

## Wenn sich zwei Quellen widersprechen

Das kommt vor. Zwei echte Beispiele aus dem Repository:

- Die Release-Übersicht nennt für manche Versionen zwei Daten: 6.0.13 hat das Datum des Tags 18. April 2026 und das
  „Release date“ von GitHub 20. April 2026 ([Release-Übersicht](../../../eng-us/release-overview)). Sagen Sie, welches Sie meinen
  ([Daten prüfen](daten-pruefen.md)).
- Am 18. Oktober 2026 nannte die Übersicht der Release Notes der Erweiterungen 4.1.15 als neuestes Release von
  cjw_newsletter, während die [Seite von cjw_newsletter](../../../changelogs/extensions/cjw_newsletter.md) selbst schon
  4.1.16 aufführte. Die Seite des Pakets und seine Tags sind die genauere Quelle.

Im Zweifel entscheiden die Git-Tags, denn sie sind das Release ([Ein Release selbst prüfen](ein-release-selbst-pruefen.md)).

## Verwandte Seiten

- [Einen Changelog lesen](ein-changelog-lesen.md)
- [Über ein Release berichten](ueber-ein-release-berichten.md)
- [Wo die Fakten stehen](../fuersprecher/wo-die-fakten-stehen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
