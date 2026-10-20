---
title: "Eine Änderung am Kopfmenü in letzter Minute am Tag des Livegangs"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1222
summary: Der schnellste sichere Weg: zuerst exp:ini add mit --dry-run, dann das Leeren des Cache; etwa zwei Befehle.
keywords: Tag des Livegangs, Kopfmenü, schnelle Änderung, exp:ini, Probelauf, launch day, header menu, quick change, dry run
type: help
---
# Eine Änderung am Kopfmenü in letzter Minute am Tag des Livegangs

Den vergessenen Bereich in einer Minute ins Menü aufnehmen.

*Lebenszyklus: Livegang, Beispiel 23 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Einstellungen](#mit-einstellungen)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Oder unter **Setup > INI-Einstellungen** (zehn Aktionen auf der Testkopie).

## Mit Einstellungen

1. `./console exp:ini add menu.ini/SiteInfo/MainMenuID[] <id> siteaccess:site --dry-run`, den Unterschied prüfen, ohne `--dry-run` ausführen.
2. `./console exp:cache clear --tag=template,content`.

## Welchen Weg Sie wählen

Die Konsole: zwei Befehle, etwa 4 Sekunden auf der Testkopie.

## Prüfen

- Der Eintrag erscheint; `exp:ini` hat eine Sicherung unter `var/backup/ini/` angelegt.

## Verwandte Seiten

- [Das Kopfmenü über die Kommandozeile](../../../../../eng-us/help/build-two-ways/header-menu-by-cli.md)
- [Gemessene Schritte und Zeiten](../../gemessene-schritte-und-zeiten.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
