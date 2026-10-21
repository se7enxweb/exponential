---
title: "Die Einstellungsdateien einer anderen Wurzel ändern"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1509
summary: exp:ini --root=<dir> arbeitet auf dem Einstellungsbaum einer anderen Wurzel (einer Kopie der Installation); der INI-Cache dort wird nicht geleert, und der Geltungsbereich muss dort existieren.
keywords: exp:ini --root, andere Installation, Checkout, Einstellungen, another installation, checkout, settings
type: help
---
# Die Einstellungsdateien einer anderen Wurzel ändern

Die Menüliste in einer Kopie der Installation vorbereiten, bevor die Live-Installation geändert wird.

*Lebenszyklus: Migrieren, Beispiel 10 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Einstellungen](#mit-einstellungen)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

Nicht im Layout-Editor.

## Mit Einstellungen

1. `./console exp:ini add menu.ini/SiteInfo/MainMenuID[] 192 siteaccess:site --root=/path/to/copy --dry-run`.
2. Der Geltungsbereich muss in dieser Wurzel existieren: In einem frischen Checkout des Repositorys, das kein `settings/siteaccess/site/` der Installation hat, antwortete der Test *Refused: Unknown siteaccess 'site' (not in settings/siteaccess/ nor in AvailableSiteAccessList)*. Geltungsbereiche von Erweiterungen funktionieren in einem Checkout der Erweiterung.

## Welchen Weg Sie wählen

Nützlich, um Releases vorzubereiten.

## Prüfen

- Die Datei der Kopie enthält die Änderung; die laufende Site ist unverändert.

## Verwandte Seiten

- [exp:ini für Site-Entwickler](../../exp-ini-fuer-site-entwickler.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
