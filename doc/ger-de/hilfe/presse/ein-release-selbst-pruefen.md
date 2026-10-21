---
title: Ein Release selbst prüfen
audience: press
category: press-media
order: 105
summary: Drei Wege, auf denen ein Reporter bestätigen kann, dass ein Release von Exponential existiert, wann es erstellt wurde und was es enthält - GitHub Releases im Browser, ein paar Git-Befehle und Packagist - mit der erwarteten Ausgabe und was Sie tun, wenn die Ergebnisse nicht übereinstimmen.
keywords: prüfen, Überprüfung, Tag, git, GitHub Releases, Packagist, Veröffentlichungsdatum, ls-remote, Version, Faktencheck, Release existiert, verify, verification, tag, release date, version, fact check, release exists
type: help
---
# Ein Release selbst prüfen

Diese Seite ist für Reporter, Redakteure und Faktenprüfer, die ein Release mit eigenen Augen bestätigen wollen, statt
einem Beitrag, einer Pressemitteilung oder dieser Hilfe zu vertrauen. Alles hier funktioniert ohne Konto und ohne
Exponential zu installieren. Sie brauchen einen Browser und für die zweite Methode einen Rechner mit Git.

## Inhalt

1. [Methode 1: im Browser](#methode-1-im-browser)
2. [Methode 2: mit Git](#methode-2-mit-git)
3. [Methode 3: auf Packagist](#methode-3-auf-packagist)
4. [Bestätigen, was ein Release enthält](#bestätigen-was-ein-release-enthält)
5. [Wenn die Ergebnisse nicht übereinstimmen](#wenn-die-ergebnisse-nicht-übereinstimmen)

## Methode 1: im Browser

1. Öffnen Sie [github.com/se7enxweb/exponential/releases](https://github.com/se7enxweb/exponential/releases).
2. Suchen Sie die Version. Jedes Release zeigt seinen Tag (zum Beispiel `v6.0.14`) und das Datum, an dem GitHub es
   veröffentlicht hat.
3. Öffnen Sie die Seite des Tags über die [Release-Übersicht](../../../eng-us/release-overview), die für jede Version 6.0.x eine
   „Release url“ nennt, zum Beispiel `https://github.com/se7enxweb/exponential/releases/tag/v6.0.14`.

Verlassen Sie sich nicht allein auf die Reihenfolge der Liste: Bestätigen Sie die neueste Version anhand ihrer Nummer
mit Methode 2.

## Methode 2: mit Git

Die eigene Dokumentation des Repositorys nennt diese Befehle
([Den Code beschaffen](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches)). Ohne zu klonen:

```bash
git ls-remote --tags https://github.com/se7enxweb/exponential.git 'v6*'
```

In einem Klon:

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git fetch --tags
git tag -l 'v*' --sort=version:refname | tail -5
```

Erwartet am 18. Oktober 2026, die fünf neuesten Tags, der neueste zuletzt:

```text
v6.0.10
v6.0.11
v6.0.12
v6.0.13
v6.0.14
```

Das Datum des Commits eines Tags:

```bash
git log -1 --format='%ad' v6.0.14
```

Sortieren Sie immer mit `--sort=version:refname`; eine Sortierung nach Name setzt `v6.0.10` vor `v6.0.8`
([Wie Versionsnummern funktionieren](wie-versionsnummern-funktionieren.md#versionen-richtig-sortieren)).

## Methode 3: auf Packagist

Der Kernel ist das Composer-Paket
[se7enxweb/exponential](https://packagist.org/packages/se7enxweb/exponential). Seine Seite listet jede Version auf, die
Composer installieren kann. Velocity ist [se7enxweb/exponential-velocity](https://packagist.org/packages/se7enxweb/exponential-velocity).
Eine Version erscheint dort, nachdem Packagist den Tag gelesen hat; die Anleitung für Erweiterungen weist darauf hin,
dass der Webhook des Repositorys zu Packagist fehlen kann, wenn eine neue Version nicht erscheint
([Anleitung für Erweiterungen](../../anleitungen/extensions.md#7-die-extension-veröffentlichen)).

## Bestätigen, was ein Release enthält

| Frage | Wie Sie es prüfen |
|---|---|
| Ist Funktion X in Version Y? | Durchsuchen Sie den Changelog von Y ([Einen Changelog lesen](ein-changelog-lesen.md)), dann das Ledger, dessen Spalte „Release“ „the release tag that first contained the change“ nennt ([Geschichte](../../geschichte/README.md#so-lesen-sie-das-änderungsverzeichnis)) |
| Ist ein Commit in einem Release? | `git tag --contains <hash>` in einem Klon listet jeden Tag auf, der ihn enthält |
| Was hat sich zwischen zwei Releases geändert? | `git log --oneline v6.0.13..v6.0.14` |
| Welche Version betreibt eine Website? | Sicher weiß das nur ihr Betreiber; raten Sie nicht anhand von Bildschirmfotos |

## Wenn die Ergebnisse nicht übereinstimmen

| Abweichung | Wahrscheinlicher Grund | Was Sie drucken |
|---|---|---|
| Datum des Tags und Datum des GitHub-Releases unterscheiden sich | Das Release wurde später als der Tag veröffentlicht; die Release-Übersicht nennt beide, zum Beispiel 6.0.13: Tag am 18. April 2026, Release am 20. April 2026 | Das Datum des Tags, und sagen Sie das ([Daten prüfen](daten-pruefen.md)) |
| Packagist zeigt einen Tag nicht, den GitHub zeigt | Packagist hat ihn noch nicht gelesen | Warten Sie, oder schreiben Sie „auf GitHub getaggt“ |
| Ihr Klon zeigt einen Tag nicht | Sie haben ihn nicht geholt | Führen Sie `git fetch --tags` aus |
| Ein Blog nennt eine Version, die Sie nicht finden | Es kann die Entwicklungslinie sein oder ein Fehler | Drucken Sie sie nicht ([Über 6.0.15 berichten, solange es in Entwicklung ist](ueber-6-0-15-in-entwicklung-berichten.md)) |

## Verwandte Seiten

- [Veröffentlichte Versionen ändern sich nie](veroeffentlichte-versionen-aendern-sich-nie.md)
- [Daten prüfen](daten-pruefen.md)
- [Leitfaden zum Faktencheck](leitfaden-faktenpruefung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
