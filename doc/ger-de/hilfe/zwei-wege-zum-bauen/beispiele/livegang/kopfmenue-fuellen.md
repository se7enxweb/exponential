---
title: "Das Kopfmenü für den Livegang füllen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1206
summary: Die endgültige Liste MainMenuID[] in einer Bearbeitung schreiben (Administration) oder mit clear plus add (Konsole).
keywords: Kopfmenü, Livegang, MainMenuID, exp:ini clear, exp:ini add, header menu, launch
type: help
---
# Das Kopfmenü für den Livegang füllen

Das Kopfmenü hat die Bereiche des Livegangs in der richtigen Reihenfolge.

*Lebenszyklus: Livegang, Beispiel 7 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Einstellungen](#mit-einstellungen)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

Nicht im Layout-Editor: Das Kopfmenü ist `menu.ini`. Unter **Setup > INI-Einstellungen**, `menu.ini`, Siteaccess `site`, `MainMenuID[]` **Bearbeiten**: eine Zeile `=<node id>` pro Eintrag nach der leeren ersten Zeile, **Speichern**, dann **Template & Inhalt**.

## Mit Einstellungen

1. `./console exp:ini clear menu.ini/SiteInfo/MainMenuID[] siteaccess:site`, dann ein `exp:ini add ... <id> siteaccess:site` pro Eintrag in der Reihenfolge, dann `./console exp:cache clear --tag=template,content`.

## Welchen Weg Sie wählen

Die Administration für eine einmalige Bearbeitung; die Konsole, wenn die Liste für mehrere Sites per Skript gesetzt wird.

## Prüfen

- Die Kopfzeile zeigt die Einträge in der geplanten Reihenfolge.

## Verwandte Seiten

- [Das Kopfmenü in der Administration](../../kopfmenue-in-der-administration.md)
- [Das Kopfmenü über die Kommandozeile](../../../../../eng-us/help/build-two-ways/header-menu-by-cli.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
