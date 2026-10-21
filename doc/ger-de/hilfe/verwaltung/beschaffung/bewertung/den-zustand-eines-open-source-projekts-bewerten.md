---
title: Den Zustand des Open-Source-Projekts bewerten
audience: government, project-manager, admin
category: public-sector
order: 130
summary: Wie eine öffentliche Stelle beurteilt, ob das Open-Source-Projekt Exponential hinter einem Angebot gepflegt wird und voraussichtlich Bestand hat - Releases und Tags, Changelogs, die Sicherheitsrichtlinie, kontinuierliche Integration, Dokumentation, Community-Kanäle und Steuerung - mit den Befehlen und Seiten, mit denen Sie jedes Signal selbst prüfen.
keywords: Zustand eines Open-Source-Projekts, Projektgesundheit, Pflege, Nachhaltigkeit, Release-Rhythmus, Changelog, kontinuierliche Integration, Community, Steuerung, Sorgfaltsprüfung, Langlebigkeit, open source health, project health, maintenance, sustainability, release cadence, changelog, SECURITY.md, continuous integration, community, governance, due diligence, longevity
type: help
---
# Den Zustand des Open-Source-Projekts bewerten

Diese Seite richtet sich an die technische Leitung oder Architektin bzw. den Architekten in einem Bewertungsgremium.
Wenn Sie Leistungen rund um freie Software einkaufen, hängen Sie auch von dem Projekt ab, das die Software pflegt. Diese
Seite listet die Signale auf, die Sie selbst im öffentlichen Repository und in der Dokumentation prüfen können, ohne
sich auf das Wort des Bieters zu verlassen. Jedes Signal verlinkt die Seite, die es beschreibt.

## Inhalt
1. [Wer Exponential pflegt](#wer-exponential-pflegt)
2. [Releases und Versionsrichtlinie](#releases-und-versionsrichtlinie)
3. [Changelogs und Geschichte](#changelogs-und-geschichte)
4. [Sicherheitsrichtlinie](#sicherheitsrichtlinie)
5. [Tests und kontinuierliche Integration](#tests-und-kontinuierliche-integration)
6. [Dokumentation](#dokumentation)
7. [Community und Roadmap](#community-und-roadmap)
8. [Festzuhaltende schwache Signale](#festzuhaltende-schwache-signale)
9. [Eine Checkliste zum Zustand](#eine-checkliste-zum-zustand)

## Wer Exponential pflegt

Exponential wird von 7x gepflegt, weiterentwickelt, unterstützt und gehostet, mit der Exponential Foundation als
gemeinnütziger Organisation hinter dem Open-Source-Projekt ([Wer ist 7x?](../../../wer-ist-7x.md),
[Wer ist die Exponential Foundation?](../../../wer-ist-die-exponential-foundation.md)). Exponential führt die Codebasis von
eZ Publish fort, einem Produkt, das sein Schöpfer nach fast achtzehn Jahren eingestellt hat. Lesen Sie beide Seiten für
die Geschichte und dafür, was die Quellen nicht sagen.

## Releases und Versionsrichtlinie

Prüfen Sie die Tags selbst in einem Klon:

```bash
git fetch --tags
git tag -l 'v*' --sort=version:refname | tail -5
```

- Veröffentlichte Tags `v6.0.0` bis `v6.0.14` existieren; 6.0.15 ist in Entwicklung
  ([Quellcodeverwaltung und Releases](../../../../standards/versionsverwaltung-und-releases.md#die-versionen-des-kernels)).
- Die Veröffentlichungsdaten der jüngsten Tags stehen in
  [Die Roadmap](../../../projekt/roadmap.md#was-bereits-geliefert-wurde).
- Veröffentlichte Tags werden nie geändert; eine Korrektur kommt als nächste Version
  ([Tags sind dauerhaft](../../../../standards/versionsverwaltung-und-releases.md#tags-sind-dauerhaft)). Für Sie bedeutet das:
  Eine Versionsnummer bezeichnet immer denselben Code.

## Changelogs und Geschichte

- Jede Kernel-Version hat ein Changelog in `doc/changelogs/6.0/`, zum Beispiel das
  [Changelog von 6.0.15](../../../../../changelogs/6.0/6.0.15.md).
- Erweiterungen haben eigene Changelogs in `doc/changelogs/extensions/`.
- Jede Änderung seit Dezember 2023 wird in [der Geschichte](../../../../geschichte/README.md) erzählt.

Ein langes, ausführliches Changelog mit Einträgen zu Sicherheit und Barrierefreiheit ist ein Zeichen aktiver Pflege;
lesen Sie eine Stichprobe von Einträgen, statt sie zu zählen.

## Sicherheitsrichtlinie

`SECURITY.md` im Wurzelverzeichnis des Repositorys nennt die unterstützte Linie (6.0.x), eine Adresse für vertrauliche
Meldungen und eine erste Antwort „within one business day“ (innerhalb eines Werktags). Ältere Linien (5.x und früher)
werden nicht unterstützt. Die Seite [Sicherheitsstandards](../../../../standards/sicherheit.md) dokumentiert drei
Härtungsdurchgänge und nennt ihre Lücken.

## Tests und kontinuierliche Integration

- Die Testsuiten laufen bei jedem Push und Pull Request auf PHP 8.1 bis 8.5, und für PHP 8.0 wird geprüft, dass es
  startet ([Wer ist die Exponential Foundation?](../../../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung),
  [Tests und CI](../../../../standards/tests-und-ci.md)).
- Designtests sichern Regeln zur Barrierefreiheit des Designs admin4 ab
  ([Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md)).

## Dokumentation

- Die Dokumentation liegt im Repository, unter der GNU Free Documentation License 1.3, und wird in der Administration als
  Hilfe angezeigt ([Die Lizenz dieser Dokumentation](../../../../README-LIZENZ.md)).
- Die Seiten zu den Standards geben die Konformität ehrlich an, mit „Teilweise erfüllt“ und „Nicht geprüft“, wo das die
  Wahrheit ist ([Standards](../../../../standards/README.md)).

## Community und Roadmap

- Kostenlose Kanäle: Foren, Telegram, Discord, Slack ([Community-Kanäle](../../../projekt/community-kanaele.md)).
- Issue-Tracker auf GitHub für die Software und für das Projekt.
- Eine öffentliche Roadmap und eine Roadmap der Community ([Die Roadmap](../../../projekt/roadmap.md)). Die Seite der
  Roadmap vermerkt selbst, dass einige Versionsnummern darauf veraltet sind; das Changelog ist die maßgebliche
  Aufzeichnung.
- Ein Partnerprogramm mit veröffentlichten Stufen ([Partner: hier beginnen](../../../partner/partner-hier-beginnen.md)).

## Festzuhaltende schwache Signale

Die Dokumentation nennt diese selbst; wägen Sie sie gegen Ihre Risikobereitschaft ab:

- `main` hat keinen Branch-Schutz; Regeln für Merge und Push sind nur Richtlinie.
- Commits und Tags sind nicht signiert.
- Kein Schwachstellenscan der Abhängigkeiten in der CI.
- Die Pflege ist bei einem Unternehmen konzentriert; fragen Sie den Bieter, wie es weiterginge, wenn sich das ändert.
  Die GPL erlaubt jedem, den Code fortzuführen; das ist Ihre Rückfallebene.

## Eine Checkliste zum Zustand

```text
[ ] Latest tag and its date (git tag --sort=version:refname): <...>
[ ] Commits on main in the last 90 days: git log --since=90.days --oneline | wc -l -> <...>
[ ] Changelog of the current line read: security and accessibility entries present
[ ] SECURITY.md: supported versions and reporting address present
[ ] CI workflows present in .github/workflows/ and passing on main
[ ] Documentation covers install, upgrade, security, accessibility, privacy
[ ] Community channels active (a recent forum thread or issue answered)
[ ] Weak signals recorded and judged
```

## Verwandte Seiten
- [Bewertung: Übersicht](README.md)
- [Beschaffung: Übersicht](../README.md)
- [Support erhalten](../../../projekt/unterstuetzung-erhalten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
