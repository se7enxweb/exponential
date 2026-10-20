---
title: Die Entwicklungslinie testen
audience: advocate
category: advocacy
order: 105
summary: Wie Fürsprecher die aktuelle Entwicklungslinie von Exponential (den Branch main, dev-main) auf einer separaten Testinstallation testen und melden, was sie finden, damit Releases besser werden - nie im Produktivbetrieb, mit einer Testroutine und einer Vorlage für Meldungen.
keywords: Testen, Entwicklungslinie, Branch main, dev-main, Vorabversion, Git-Klon, Testinstallation, Rückmeldung, Regressionen, Release-Qualität, testing, development line, main branch, pre-release, Git clone, test installation, feedback, regressions, release quality
type: help
---
# Die Entwicklungslinie testen

Diese Seite ist für Fürsprecher, die helfen möchten, dass das nächste Release gut wird. Sicherheits- und andere
Korrekturen werden auf dem Branch `main` gemacht, „der Linie 6.0.15, `dev-main`“, und mit dem nächsten Release 6.0.x
veröffentlicht ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)). Wer `main` auf einer
Testinstallation ausprobiert, findet Probleme, bevor sie ein Release erreichen.

> **Warnung**
> Nur auf einer separaten Testinstallation mit erfundenen Daten. Nie auf einer Produktiv-Website, und richten Sie einen
> Installer nie auf eine Datenbank, die Sie behalten möchten ([Upgrade](../../installation/11-aktualisieren.md)).

## Inhalt

1. [Die Entwicklungslinie holen](#die-entwicklungslinie-holen)
2. [Was Sie testen](#was-sie-testen)
3. [Melden, was Sie finden](#melden-was-sie-finden)

## Die Entwicklungslinie holen

Am einfachsten ist ein Git-Klon, mit dem Sie auch aktualisieren und beitragen können
([Mit Git entwickeln](../mit-git-entwickeln.md)):

```bash
git clone https://github.com/se7enxweb/exponential.git exponential-main
cd exponential-main
git log -1 --format='%h %ad %s'      # note the commit you test
```

Installieren Sie sie dann auf SQLite, wie [Installation mit einem Befehl](../../../en/us/features/6.0/install-in-one-command.md)
es beschreibt. Um sie aktuell zu halten, siehe [Mit Git entwickeln, Auf dem neuesten Stand bleiben](../mit-git-entwickeln.md#auf-dem-neuesten-stand-bleiben);
um eine Korrektur vor ihrem Release auszuprobieren, [Eine Korrektur vor dem Release nutzen](../mit-git-entwickeln.md#eine-korrektur-vor-ihrem-release-nutzen).

## Was Sie testen

| Bereich | Wie |
|---|---|
| Ihre alltäglichen Aufgaben | Bearbeiten, veröffentlichen, hochladen, übersetzen, Layouts |
| Neue Funktionen im Änderungsprotokoll | Folgen Sie ihren Funktionsseiten ([Änderungsprotokoll 6.0.15](../../../changelogs/6.0/6.0.15.md)) |
| Ihre Sprache | Die Administration in Ihrer Sprache ([Eine maschinell unterstützte Übersetzung gegenlesen](maschinell-unterstuetzte-uebersetzung-gegenlesen.md)) |
| Ihre Umgebung | Ihre Datenbank, Engine, Ihr Betriebssystem |
| Barrierefreiheit | Ihre assistive Technologie ([Für Barrierefreiheit eintreten](fuer-barrierefreiheit-eintreten.md)) |

## Melden, was Sie finden

Nutzen Sie [Fehler gut melden](fehler-gut-melden.md) und nennen Sie den Commit:

```text
Environment: Exponential main at <commit>, PHP <version>, <engine>, <database>, <design>
Worked in <last release>: <yes/no/unknown>
```

„Funktionierte im letzten Release, auf main kaputt“ ist die wertvollste Art von Meldung: Sie verweist auf eine kürzliche
Änderung.

Kündigen Sie ungetestete Funktionen nicht als veröffentlicht an ([Über ein Release schreiben](ueber-ein-release-schreiben.md)).

## Verwandte Seiten

- [Issues nachstellen und bestätigen](issues-nachstellen-und-bestaetigen.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Release- und Upgrade-Planung](../fuehrung/release-und-upgrade-planung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
