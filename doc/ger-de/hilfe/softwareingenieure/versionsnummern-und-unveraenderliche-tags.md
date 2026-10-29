---
title: Versionsnummern und unveränderliche Tags
audience: software-engineer, developer
category: software-engineering
order: 610
summary: Die zwei Release-Regeln, die jeder Ingenieur von Exponential kennen muss - die letzte Stelle einer Version zählt weiter (auf 0.0.4.9 folgt 0.0.4.10), und ein veröffentlichter Tag oder ein Release wird nie verschoben, gelöscht, neu erstellt oder ersetzt - mit dem Warum, wie Sie die nächste Nummer anhand des Veröffentlichten wählen, der Sortierfalle und wie sich Composer-Bedingungen verhalten.
keywords: Versionsnummern, Versionierung, letzte Stelle, 0.0.4.10, Tags, unveränderlich, dauerhaft, neu erstellen, sort -V, version:refname, Composer, Tilde-Bedingung, Packagist, semantische Versionierung, Softwareingenieur, version numbers, versioning, last position, immutable, permanent, re-cut, tilde constraint, semantic versioning
type: help
---
# Versionsnummern und unveränderliche Tags

Diese Seite richtet sich an Ingenieure, die Versionsnummern wählen oder Tags für Exponential oder eines seiner Pakete
pushen. Zwei Regeln gelten für jedes Paket des Projekts, und beide lassen sich aus Gewohnheit leicht brechen.

## Inhalt

1. [Regel 1: die letzte Stelle zählt weiter](#regel-1-die-letzte-stelle-zählt-weiter)
2. [Regel 2: ein veröffentlichter Tag ändert sich nie](#regel-2-ein-veröffentlichter-tag-ändert-sich-nie)
3. [Die nächste Nummer wählen](#die-nächste-nummer-wählen)
4. [Die Sortierfalle](#die-sortierfalle)
5. [Wie Composer es sieht](#wie-composer-es-sieht)

## Regel 1: die letzte Stelle zählt weiter

Nach `x.y.9` kommt `x.y.10`, nie `x.(y+1).0`. Die Geschichte von Velocity zeigt `0.0.4.9`, gefolgt von `0.0.4.10`. Es
gibt kein "Ausgehen" der Ziffern: Die letzte Stelle ist eine Ganzzahl. Eine höhere Stelle zu ändern ist eine eigene
Entscheidung darüber, was das Release bedeutet, nie eine Folge davon, dass die Neun erreicht ist
([Versionsverwaltung und Releases](../../standards/versionsverwaltung-und-releases.md#die-nächste-versionsnummer-wählen)).

## Regel 2: ein veröffentlichter Tag ändert sich nie

Ein Tag und das daraus erstellte GitHub-Release sind unveränderlich, sobald sie gepusht sind:

- löschen, verschieben, umhängen oder erstellen Sie einen gepushten Tag nie neu, nicht einmal, um einen Tippfehler zu
  korrigieren oder eine vergessene Datei hinzuzufügen;
- ersetzen Sie nie die Assets eines veröffentlichten Releases;
- auf ein fehlerhaftes Release folgt die nächste Version, deren Notes sagen, was falsch war.

**Warum.** Composer und Packagist lesen einen Tag einmal und speichern die gefundene Version zwischen. Ein verschobener
Tag ergibt zwei verschiedene Pakete unter einer Versionsnummer: Installationen, die ihn früher aufgelöst haben, behalten
den alten Inhalt, neue erhalten den neuen, und von außen kann niemand erkennen, welchen er hat.

## Die nächste Nummer wählen

Immer anhand dessen, was bereits veröffentlicht ist, nie aus dem Gedächtnis oder aus dem Arbeitsbaum:

```bash
git fetch --tags
git tag -l 'v*' --sort=version:refname | tail -5
gh release list --limit 10
```

Vergleichen Sie beide Listen: Ein Tag kann ohne Release existieren, und ein Release kann für einen Tag existieren, den der
lokale Klon noch nicht geholt hat.

## Die Sortierfalle

Nach Namen sortiert steht `v0.0.4.10` vor `v0.0.4.8`:

```bash
git tag -l 'v0.0.4.*' | tail -1                 # wrong: a name sort
git tag -l 'v0.0.4.*' --sort=version:refname    # right
git tag -l 'v0.0.4.*' | sort -V | tail -1       # right
```

Jedes Skript, das "den neuesten Tag" auswählt, muss nach Version sortieren.

## Wie Composer es sieht

Composer und Packagist vergleichen jede Stelle numerisch, sodass `0.0.4.10` neuer ist als `0.0.4.9`. Eine
Tilde-Bedingung lässt die letzte Stelle frei: `~0.0.4.9` bedeutet `>=0.0.4.9 <0.0.5.0` und nimmt `0.0.4.10` bereits an.
Die `composer.json` im Hauptverzeichnis verlangt zum Beispiel `se7enxweb/exponential-velocity` in `~0.0.4.57`. Ein neues
Festlegen auf ein höheres Minimum dient der Klarheit und muss warten, bis die Version auf Packagist steht
([Abhängigkeitsverwaltung mit Composer](abhaengigkeitsverwaltung-mit-composer.md)).

## Verwandte Seiten

- [Release Engineering](release-engineering.md)
- [Tags sind dauerhaft](../erweiterungsentwicklung/teil-7-veroeffentlichen/tags-sind-dauerhaft.md)
- [Die nächste Version wählen](../erweiterungsentwicklung/teil-7-veroeffentlichen/die-naechste-version-waehlen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
