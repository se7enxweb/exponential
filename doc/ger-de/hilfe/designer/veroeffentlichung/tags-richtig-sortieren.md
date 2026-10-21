---
title: "Tags richtig sortieren"
audience: designer, developer
category: theme-distribution
order: 90
summary: Die Tags eines Themes in Versionsreihenfolge statt in Namensreihenfolge auflisten - git tag --sort=version:refname, sort -V und git describe - mit dem Fehler, den eine einfache Sortierung macht, und wie Sie Release-Skripte korrigieren.
keywords: git tag, sortieren, version:refname, sort -V, git describe, neuester Tag, Release-Skript, lexikalische Sortierung, sort, newest tag, release script, lexical sort
type: help
---
# Tags richtig sortieren

Diese Seite ist für Designer und Entwickler, die Tags von Hand oder in Skripten auflisten.

## Das Problem

Als Text sortiert, kommt `v1.0.10` vor `v1.0.8`, weil das Zeichen `1` vor `8` einsortiert wird. Ein Skript, das die
letzte Zeile eines einfachen `git tag -l` nimmt, hält dann `v1.0.9` für das neueste Release, und das nächste Release
bekommt eine Nummer, die schon vergeben ist.

## Die richtigen Befehle

| Befehl | Liefert |
|---|---|
| `git tag -l 'v*' --sort=version:refname` | Jeden Tag in Versionsreihenfolge, den ältesten zuerst |
| `git tag -l 'v*' --sort=-version:refname \| head -1` | Die höchste Version |
| `git tag -l 'v*' \| sort -V \| tail -1` | Die höchste Version, mit GNU sort |
| `git describe --tags --abbrev=0` | Den neuesten vom aktuellen Commit aus erreichbaren Tag (folgt der Historie, nicht den Namen) |

Zeigen Sie fünf statt einem, damit eine Überraschung sichtbar wird: `git tag -l 'v*' --sort=version:refname | tail -5`.

## Repositories, die ohne „v“ taggen

Manche Repositories taggen `4.1.5` statt `v4.1.5`. Verwenden Sie ein Muster, das dem entspricht, was das Repository
wirklich verwendet, oder gar keins: `git tag -l --sort=version:refname | tail -5`.

## Schritt für Schritt: ein Release-Skript korrigieren

1. Suchen Sie im Skript nach `git tag` und `tail -1`.
2. Fügen Sie `--sort=version:refname` hinzu (oder leiten Sie durch `sort -V`).
3. Testen Sie es an einem Repository, das einen Tag `.9` und einen Tag `.10` hat.
4. Vergleichen Sie auch mit `gh release list`: Ein Release kann zu einem Tag existieren, den Ihr Klon noch nicht geholt
   hat; führen Sie zuerst `git fetch --tags` aus.

## Verwandte Seiten

- [Nach .9 kommt .10](nur-die-letzte-stelle-zaehlt.md)
- [Prüfen, was schon veröffentlicht ist](pruefen-was-schon-veroeffentlicht-ist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
