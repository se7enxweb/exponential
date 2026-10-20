---
title: Wie Versionsnummern funktionieren
audience: press
category: press-media
order: 103
summary: Was die Zahlen in den Versionen von Exponential bedeuten, warum 6.0.10 nach 6.0.9 kommt, warum die letzte Stelle weiterzählt (0.0.4.9, dann 0.0.4.10), wie Tags, Branches und Composer-Bedingungen zusammenhängen und wie Sie Versionen richtig sortieren.
keywords: Version, Versionsnummer, Tag, Branch, 6.0.x, Sortieren, sort -V, version:refname, Composer, Tilde, dev-main, Velocity, Versionen von Erweiterungen, version, version number, sorting, tilde, extension versions
type: help
---
# Wie Versionsnummern funktionieren

Diese Seite ist für Reporter, Redaktionen und Creator, die Versionsnummern richtig schreiben müssen. Exponential und die
Pakete darum herum verwenden Nummern, deren letzte Stelle immer weiter hochzählt, was Menschen überrascht, die andere
Schemata gewohnt sind. Die Reihenfolge falsch anzugeben, ist einer der leichtesten Fehler in der Berichterstattung über
Releases.

## Inhalt

1. [Die Teile einer Version](#die-teile-einer-version)
2. [Die letzte Stelle zählt weiter](#die-letzte-stelle-zählt-weiter)
3. [Tags, Branches und die Entwicklungslinie](#tags-branches-und-die-entwicklungslinie)
4. [Versionen richtig sortieren](#versionen-richtig-sortieren)
5. [Composer-Bedingungen in einer Tabelle](#composer-bedingungen-in-einer-tabelle)

## Die Teile einer Version

| Beispiel | Wo Sie es sehen | Wie Sie es im Text schreiben |
|---|---|---|
| `v6.0.14` | Der Git-Tag eines Release | „Exponential 6.0.14“ (kein `v` im Fließtext) |
| `6.0.14` | Changelogs, Packagist | „Exponential 6.0.14“ |
| `6.0.15stable` | `lib/version.php` auf dem Branch `main` | „die Entwicklungslinie 6.0.15“, nie „6.0.15 stable“ |
| `dev-main`, `6.0.x-dev` | Composer-Namen für den Branch `main` | „der Entwicklungs-Branch“ |
| `v0.0.4.50` | Ein Release-Tag von Velocity | „Exponential Velocity 0.0.4.50“ |

Die Regel „Versionen haben im Fließtext kein `v`; das `v` gehört zum Git-Tag“ stammt aus den
[Regeln für Marke und Namen](../geschaeft/marken-und-namensregeln.md#das-produkt-exponential). Die Angaben zu
`6.0.15stable` und `6.0.x-dev` stammen aus [Den Code besorgen](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches).

## Die letzte Stelle zählt weiter

Die Versionen von Exponential springen bei neun nicht um. Nach 6.0.9 kam 6.0.10 (28. August 2025), nicht 6.1.0
([Release-Übersicht](../../../eng-us/release-overview)). Dasselbe gilt für Erweiterungen und für Velocity:

- „Nach `0.1.9` ist das nächste Release `0.1.10`; nur die letzte Stelle zählt hoch, sofern Sie nichts anderes
  entscheiden“ ([Leitfaden für Erweiterungen, Veröffentlichen](../../anleitungen/extensions.md#7-die-extension-veröffentlichen)).
- „Die letzte Stelle einer Velocity-Version zählt über 9 hinaus weiter (`0.0.4.9`, `0.0.4.10`, ...)“
  ([Den Code besorgen, das Velocity-Paket](../../installation/03-den-code-beschaffen.md#34-das-optionale-velocity-paket)).
- „Versionen zählen in der letzten Stelle: Auf `0.0.4.9` folgt `0.0.4.10`, nie `0.0.5.0`“
  ([Release Notes von Velocity](../../../changelogs/extensions/exponential-velocity.md)).

Lesen Sie einen Sprung in der letzten Stelle also nicht als kleines oder großes Release. Die Nummer gibt die
Reihenfolge an, nicht den Umfang. Was ein Release enthält, steht in seinem Changelog ([Ein Changelog lesen](ein-changelog-lesen.md)).

## Tags, Branches und die Entwicklungslinie

| Name | Was es ist | Quelle |
|---|---|---|
| Release-Tags `v6.0.0` bis `v6.0.14` | Genaue, dauerhafte Releases der Linie 6.0 | [Den Code besorgen](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches) |
| Ältere Tags wie `4.4.0` | Linien aus den Jahren von eZ Publish, ohne `v` getaggt | Dieselbe Seite |
| Branch `main` | „die aktuelle Entwicklung der Linie 6.0, heute 6.0.15“ | Dieselbe Seite |
| Release-Branches `6.0.7` bis `6.0.14` | „der Stand jeder Release-Linie“ | Dieselbe Seite |

Nur ein Tag ist ein Release. Ein Branch bewegt sich jeden Tag; ein Tag nie
([Veröffentlichte Versionen ändern sich nie](veroeffentlichte-versionen-aendern-sich-nie.md)).

## Versionen richtig sortieren

Eine einfache alphabetische Sortierung stellt `v6.0.10` vor `v6.0.8`, sodass eine nach Namen sortierte Liste die
falsche neueste Version zeigt. Die Dokumentation warnt: „Sortieren Sie immer nach Version (`--sort=version:refname`
oder `sort -V`), nie nach Namen“ ([Den Code besorgen](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches)).

```bash
git tag -l 'v6.0.*'                              # by name: v6.0.10 appears before v6.0.8 - misleading
git tag -l 'v6.0.*' --sort=version:refname       # by version: v6.0.14 is last - correct
```

Dasselbe gilt für Ihre eigenen Tabellen: Ordnen Sie Zeilen nach Release-Datum oder nach Version, nie alphabetisch.

## Composer-Bedingungen in einer Tabelle

Diesen begegnen Sie in `composer.json` und in Changelogs, die benötigte Pakete aufführen.

| Bedingung | Bedeutet | Beispiel und Quelle |
|---|---|---|
| `~0.0.4.54` | `>=0.0.4.54 <0.0.5.0`, also jede spätere 0.0.4.x | Velocity, [Den Code besorgen](../../installation/03-den-code-beschaffen.md#34-das-optionale-velocity-paket) |
| `~2.4.11` | `>=2.4.11 <2.5.0` | eztags, [Den Code besorgen](../../installation/03-den-code-beschaffen.md#35-extensions-sind-composer-pakete) |
| `^8.0` | PHP 8.0 oder jede spätere 8.x | Die PHP-Anforderung der Linie 6.0.15, dieselbe Seite, Abschnitt 3.1 |

Wenn ein Changelog sagt, ein Paket „benötigt `~1.4.39`“, schreiben Sie „benötigt Version 1.4.39 oder eine spätere
1.4.x“, nicht „verwendet Version 1.4.39“.

## Verwandte Seiten

- [Kurzkarte der Namensregeln](../fuersprecher/kurzkarte-namensregeln.md)
- [Veröffentlichte Versionen ändern sich nie](veroeffentlichte-versionen-aendern-sich-nie.md)
- [Ein Release selbst überprüfen](../../../eng-us/help/press/verifying-a-release-yourself.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
