---
title: "Eine Versionsnummer wählen"
audience: designer, developer
category: theme-distribution
order: 70
summary: Wie Sie die nächste Versionsnummer eines Themes wählen - ausgehend von dem, was schon veröffentlicht ist, bei normalen Releases nur die letzte Stelle erhöhen und eine höhere Stelle nur als bewusste Entscheidung darüber verschieben, was das Release bedeutet.
keywords: Versionsnummer, Versionierung, nächste Version, Release-Nummer, semantische Versionierung, Major, Minor, Patch, letzte Stelle, version number, versioning, next version, semantic versioning, last position
type: help
---
# Eine Versionsnummer wählen

Diese Seite ist für Designer, die gleich veröffentlichen. Eine Versionsnummer ist ein Versprechen an Ihre Nutzer
darüber, was sich geändert hat. Wählen Sie sie mit Sorgfalt: Ist ein Tag gepusht, ist die Nummer für immer verbraucht.

## Das Vorgehen

1. **Sehen Sie nach, was veröffentlicht ist**, nicht in Ihr Gedächtnis oder Ihre Arbeitskopie
   ([Prüfen, was schon veröffentlicht ist](pruefen-was-schon-veroeffentlicht-ist.md)):

   ```bash
   git fetch --tags
   git tag -l 'v*' --sort=version:refname | tail -5
   gh release list --limit 10
   ```

2. **Nehmen Sie die höchste veröffentlichte Nummer** aus beiden Listen. Ein Tag kann ohne Release existieren, und ein
   Release kann zu einem Tag existieren, den Ihr Klon noch nicht geholt hat.
3. **Erhöhen Sie die letzte Stelle um eins**: Aus `1.0.23` wird `1.0.24`; aus `0.1.9` wird `0.1.10`
   ([Nach .9 kommt .10](nur-die-letzte-stelle-zaehlt.md)).
4. **Verschieben Sie eine höhere Stelle nur mit Absicht**, wenn das Release etwas anderes bedeutet:

   | Verschieben | Wann | Beispiel |
   |---|---|---|
   | Die letzte Stelle | Korrekturen, Verbesserungen, neue Templates, alles, was für bestehende Nutzer weiter funktioniert | `1.0.23` zu `1.0.24` |
   | Eine mittlere Stelle | Ein bewusster Schritt, zum Beispiel eine neue Mindestversion von Exponential oder PHP oder eine geänderte API für Template-Autoren | `1.4.x` zu `1.5.0` |
   | Die erste Stelle | Ein Redesign oder ein Bruch, bei dem Nutzer handeln müssen | `1.x` zu `2.0.0` |

5. Schreiben Sie die Nummer in jedes Versionsfeld ([Alle Versionsfelder synchron halten](../../../../eng-us/help/designers/extensions/keeping-version-fields-in-sync.md)).

## Was die veröffentlichten Themes tun

Lesen Sie `gh release list` für ein echtes Muster: `sevenx_themes_simple` zählt `1.0.20`, `1.0.21` ... `1.0.24`;
`sevenx_themes_media` hat `1.5.70` erreicht; `cjw_themes_jumper` zählt `0.1.2` bis `0.1.6`. Bei allen bewegt sich von
einem Release zum nächsten nur die letzte Stelle.

## Was Sie vermeiden sollten

- Eine Nummer erneut verwenden, auch eine, deren Release gelöscht wurde: Packagist und Nutzer können sie schon haben.
- Nummern überspringen, um „größer auszusehen“.
- Ein Release-Skript „den neuesten Tag“ nach einfacher Namenssortierung wählen lassen ([Tags richtig sortieren](tags-richtig-sortieren.md)).

## Verwandte Seiten

- [Die Version vor dem Tag erhöhen](version-vor-dem-tag-erhoehen.md)
- [Inkompatible Änderungen und die nächste Major-Version](../../../../eng-us/help/designers/lifecycle/breaking-changes.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
