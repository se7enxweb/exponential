---
title: "Die Menü-Einstellungen in einen anderen Siteaccess kopieren"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1508
summary: exp:ini copy kopiert eine Einstellung (ein ganzes Array mit seiner Zurücksetz-Zeile) von einem Geltungsbereich in einen anderen.
keywords: exp:ini copy, Menüs, Siteaccess, Migration, menus, siteaccess, migration
type: help
---
# Die Menü-Einstellungen in einen anderen Siteaccess kopieren

Die deutsche Site beginnt mit der englischen Menüliste.

*Lebenszyklus: Migrieren, Beispiel 9 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Einstellungen](#mit-einstellungen)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

Nicht im Layout-Editor.

## Mit Einstellungen

1. `./console exp:ini copy menu.ini/SiteInfo/MainMenuID siteaccess:site siteaccess:bold_ger --dry-run`, dann ohne `--dry-run`; dann **Template & Inhalt**.

## Welchen Weg Sie wählen

Kopieren, dann die Kopie ändern.

## Prüfen

- `exp:ini where menu.ini/SiteInfo/MainMenuID bold_ger` zeigt die Liste.

## Verwandte Seiten

- [Das Kopfmenü über die Kommandozeile](../../kopfmenue-per-kommandozeile.md)
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
