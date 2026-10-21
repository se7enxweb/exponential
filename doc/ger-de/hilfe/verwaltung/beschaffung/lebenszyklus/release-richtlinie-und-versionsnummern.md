---
title: Release-Richtlinie und Versionsnummern
audience: government, project-manager, admin
category: public-sector
order: 20
summary: Was eine Versionsnummer von Exponential einer öffentlichen Stelle verspricht - dauerhafte Tags, das nächste Release als einzige Korrektur, die letzte Stelle zählt weiter, Changelogs je Version - und wie Sie das in einem Vertrag und einem Bestandsverzeichnis nutzen.
keywords: Release-Richtlinie, Versionsnummer, Tag, unveränderlich, dauerhaft, Changelog, 6.0.x, semantische Versionierung, Packagist, Release, Vertrag, Bestandsverzeichnis, öffentlicher Sektor, release policy, version number, immutable, permanent, semantic versioning, contract, asset register, public sector
type: help
---
# Release-Richtlinie und Versionsnummern

Diese Seite richtet sich an Projektleitungen und Vertragsmanagement einer öffentlichen Stelle, die angeben müssen,
welche Version der Software sie betreiben, und verstehen müssen, was eine neue Version bedeutet. Sie erklärt die
Release-Richtlinie von Exponential in einfachen Worten. Die Quellen sind [Quellcodeverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md) und
[`SECURITY.md`](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md).

## Inhalt

1. [Kurz gesagt](#kurz-gesagt)
2. [Eine veröffentlichte Version ändert sich nie](#eine-veröffentlichte-version-ändert-sich-nie)
3. [Wie die Nummern aufgebaut sind](#wie-die-nummern-aufgebaut-sind)
4. [Wo jede Änderung festgehalten wird](#wo-jede-änderung-festgehalten-wird)
5. [Das in einem Vertrag nutzen](#das-in-einem-vertrag-nutzen)
6. [Bekannte Lücken](#bekannte-lücken)

## Kurz gesagt

- Exponential 6.0 ist die gepflegte Linie; `main` ist ihr Entwicklungszweig, und neue Versionen sind Tags, die davon
  erstellt werden.
- Ein Tag und das daraus erstellte Release werden nach der Veröffentlichung nie geändert. Eine Korrektur kommt immer als
  nächste Version.
- Jede Version hat ein Changelog in `doc/changelogs/`.
- Erweiterungen tragen ihre eigenen Versionsnummern, die im Release in ihre eigenen Dateien geschrieben werden.

## Eine veröffentlichte Version ändert sich nie

„Ein veröffentlichter Tag wird nie geändert: Eine Korrektur für ein Problem in `v6.0.14` oder einem früheren Tag
erreicht Sie als nächstes Release, nicht als korrigierte Kopie des alten“ (`SECURITY.md`). Der Grund ist technisch und
eine Frage des Vertrauens: Composer und Packagist lesen einen Tag einmal und speichern, was sie gefunden haben, ein
verschobener Tag ergäbe also zwei verschiedene Pakete unter einer Versionsnummer ([Quellcodeverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md#tags-are-permanent)).

Für eine öffentliche Stelle heißt das:

- die in Ihrem Bestandsverzeichnis erfasste Version bezeichnet genau einen Satz von Dateien;
- eine prüfende Stelle kann diese Version später abrufen und denselben Code sehen;
- eine Schwachstelle in einer Version wird durch den Wechsel auf eine neuere behoben, nie durch einen stillen Austausch.

## Wie die Nummern aufgebaut sind

| Teil | Bedeutung | Beispiel |
|---|---|---|
| Haupt- und Nebenversion | die Produktlinie | 6.0 |
| Letzte Stelle | das Release innerhalb der Linie | 6.0.14, dann 6.0.15 |

Die letzte Stelle zählt weiter: Nach `.9` kommt `.10`, nie ein Sprung einer höheren Stelle; das Anheben einer höheren
Stelle ist eine eigene Entscheidung darüber, was das Release bedeutet ([Quellcodeverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md#choosing-the-next-version-number)).
Die Version des Kernels steht in `lib/version.php`. Veröffentlichte Kernel-Tags reichen von `v6.0.0` bis `v6.0.14`, und
6.0.15 ist in Entwicklung ([Quellcodeverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md#the-kernels-versions)).

Welche Version eine Website betreibt, sehen Sie unter **Setup > Systeminformationen** (`/setup/info`)
([Systeminformationen](../../../../anleitungen/systeminformationen.md)) oder auf der About-Seite, die auch die Version jeder
Erweiterung auflistet ([About- und Paketseiten](../../../../../eng-us/features/6.0/about-and-package-pages.md)).

## Wo jede Änderung festgehalten wird

| Nachweis | Wo |
|---|---|
| Änderungen am Kernel je Version | `doc/changelogs/<series>/<version>.md`, zum Beispiel das [Changelog 6.0.15](../../../../../changelogs/6.0/6.0.15.md) |
| Änderungen an Erweiterungen | `doc/changelogs/extensions/<name>.md` |
| Änderungen, die beim Upgrade ein Handeln erfordern | Seiten in `doc/eng-us/bc/6.0/`, aus dem Changelog verlinkt |
| Die Geschichte hinter den Änderungen seit Dezember 2023 | [die Geschichte](../../../../../eng-us/history/README.md) |

Einträge im Changelog sind nach Added, Updated, Removed und Renamed gruppiert und aus der Commit-Historie
zusammengestellt. Ein Lieferant kann Ihnen damit sagen, was ein Upgrade ändert ([Ein Changelog lesen](../../../../../eng-us/help/press/reading-a-changelog.md) erklärt,
wie man eines liest).

## Das in einem Vertrag nutzen

- Nennen Sie die Version, die der Lieferant liefert, und die Version bei der Abnahme, als Tags (`v6.0.x`) oder, bei
  Entwicklungsarbeit, als Commit.
- Verlangen Sie, dass der Lieferant bei jedem Upgrade sagt, welche Einträge im Changelog Ihre Website betreffen.
- Verlangen Sie, dass eigener Code in Ihrem eigenen Repository bei einem bekannten Commit aufbewahrt wird, damit sich der
  gelieferte Stand neu aufbauen lässt.
- Akzeptieren Sie von einem Lieferanten keine „gepatchten“ Kopien einer veröffentlichten Version; verlangen Sie das
  nächste Release plus Ihre eigenen Änderungen darauf.

## Bekannte Lücken

Die Seite zu den Standards nennt sie ([Quellcodeverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md#known-gaps)):

- Commits und Tags sind **nicht signiert**;
- die Regeln für Merges und gegen Force-Push sind nur Richtlinie; `main` hat keinen Branch-Schutz;
- ältere Kernel-Tags (`v6.0.10` bis `v6.0.12`) sind leichte Tags ohne Nachricht.

Verlangt Ihre Sicherheitsrichtlinie signierte Releases, halten Sie das in Ihrer Bewertung als Lücke fest und fragen Sie,
wie ein Lieferant sie ausgleichen würde (zum Beispiel durch Prüfung der Prüfsummen gegen `share/filelist.md5` mit
**Setup > Upgrade-Prüfung**, [Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md)).

## Verwandte Seiten

- [Der Lebenszyklus des Produkts](README.md)
- [Unterstützte Versionen](unterstuetzte-versionen.md)
- [Quellcodeverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
