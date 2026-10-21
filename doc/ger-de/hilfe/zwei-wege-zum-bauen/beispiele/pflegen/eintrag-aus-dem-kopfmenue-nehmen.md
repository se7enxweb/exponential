---
title: "Einen Eintrag aus dem Kopfmenü nehmen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1307
summary: Seine Zeile aus MainMenuID[] löschen (Administration) oder exp:ini rem (Konsole), dann Template & Inhalt.
keywords: Menüeintrag entfernen, MainMenuID, exp:ini rem, Kopfmenü, Pflege, remove menu item, header menu, maintenance
type: help
---
# Einen Eintrag aus dem Kopfmenü nehmen

Der Eintrag Tour verlässt das Menü; die Seite bleibt.

*Lebenszyklus: Pflegen, Beispiel 8 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Einstellungen](#mit-einstellungen)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

Nicht im Layout-Editor: **Setup > INI-Einstellungen**, `menu.ini`, `site`, `MainMenuID[]` **Bearbeiten**, die Zeile `=26210` löschen, **Speichern**, **Template & Inhalt**.

## Mit Einstellungen

1. `./console exp:ini rem menu.ini/SiteInfo/MainMenuID[] 26210 siteaccess:site`, dann `./console exp:cache clear --tag=template,content`.

## Welchen Weg Sie wählen

Beides geht; die Konsole zeigt mit `--dry-run` zuerst den Unterschied.

## Prüfen

- Die Seite ist weiterhin über ihre Adresse erreichbar.

## Verwandte Seiten

- [Einträge des Kopfmenüs umordnen oder entfernen](../../kopfmenue-eintraege-umordnen-oder-entfernen.md)
- [Beispiele zur Pflege, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
