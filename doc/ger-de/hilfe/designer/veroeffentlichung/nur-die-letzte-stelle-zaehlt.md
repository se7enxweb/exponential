---
title: "Nach .9 kommt .10: Nur die letzte Stelle zählt"
audience: designer, developer
category: theme-distribution
order: 80
summary: Eine Version auf .9 geht zu .10, nicht zur nächsthöheren Stelle - warum das für Composer, Packagist und GitHub sicher ist und warum Stellen über der letzten sich nur durch eine Entscheidung bewegen.
keywords: Version, .9, .10, letzte Stelle, hochzählen, Reihenfolge bei Packagist, Composer, Tilde, version_compare, last position, increment, Packagist ordering, tilde
type: help
---
# Nach .9 kommt .10: Nur die letzte Stelle zählt

Diese Seite ist für Designer, die eine Version erreichen, die auf `.9` endet, und sich fragen, ob die nächste `.10` ist
oder ein Sprung zur nächsthöheren Stelle.

## Die Regel

**Nur die letzte Stelle wird hochgezählt. Auf `0.0.4.9` folgt `0.0.4.10`, dann `0.0.4.11`, nie `0.0.5.0`.**

Die letzte Stelle ist eine ganze Zahl und zählt weiter. Die Ziffern gehen nicht aus. Eine Stelle darüber zu
verschieben, ist eine Entscheidung darüber, was das Release bedeutet ([Eine Versionsnummer wählen](eine-versionsnummer-waehlen.md)),
nie eine Folge davon, dass die Neun erreicht ist.

## Warum es sicher ist

| Werkzeug | Wie es `x.10` nach `x.9` einordnet |
|---|---|
| Composer und Packagist | Sie normalisieren Versionen und vergleichen jede Stelle als Zahl |
| GitHub Releases | Sortiert nach dem Datum des Tags, nicht nach seinem Namen |
| `git tag --sort=version:refname`, `sort -V` | Vergleichen Stellen als Zahlen |
| Eine Bedingung `~1.0.9` | Bedeutet `>=1.0.9 <1.1.0`, also liegt `1.0.10` darin |

Das Projekt Exponential hat das an seinen eigenen Releases geprüft: Ein Release `.10` nach einem `.9` wurde sowohl bei
Packagist als auch bei GitHub danach eingeordnet.

## Die eine Falle

Eine **einfache Sortierung nach Namen** stellt `.10` vor `.8`:

```bash
git tag -l 'v1.0.*' | tail -1                   # can print v1.0.9    wrong
git tag -l 'v1.0.*' --sort=version:refname       # ends with v1.0.10  right
git tag -l 'v1.0.*' | sort -V | tail -1          # v1.0.10            right
```

Jedes Ihrer Skripte, das „den neuesten Tag“ auswählt, braucht `--sort=version:refname` oder `sort -V`
([Tags richtig sortieren](tags-richtig-sortieren.md)).

## Schritt für Schritt bei einer .9

1. Bestätigen Sie, dass die höchste veröffentlichte Version `x.y.9` ist (Tags und Releases).
2. Schreiben Sie `x.y.10` in die Versionsfelder.
3. Committen, `vx.y.10` taggen, pushen, veröffentlichen.
4. Prüfen Sie Packagist: Die neue Version steht an erster Stelle.

## Verwandte Seiten

- [Versionsbedingungen Ihrer Nutzer](versionsbedingungen-fuer-nutzer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
