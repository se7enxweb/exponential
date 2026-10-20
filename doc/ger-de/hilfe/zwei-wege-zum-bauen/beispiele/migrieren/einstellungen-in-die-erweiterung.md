---
title: "Die Einstellungen einer Site in ihre Erweiterung verschieben"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1507
summary: exp:ini move / move-all verschieben Blöcke oder ganze Siteaccess-Geltungsbereiche in eine Erweiterung (--create-extension --activate), mit Probelauf.
keywords: exp:ini move, move-all, Migration von Einstellungen, Erweiterung, Versionsverwaltung, settings migration, extension, version control
type: help
---
# Die Einstellungen einer Site in ihre Erweiterung verschieben

Die Menülisten und Theme-Einstellungen liegen im Repository der Design-Erweiterung.

*Lebenszyklus: Migrieren, Beispiel 8 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Einstellungen](#mit-einstellungen)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

Nicht im Layout-Editor.

## Mit Einstellungen

1. `./console exp:ini move menu.ini/SiteInfo siteaccess:site extension:mysite:siteaccess:site --dry-run` zeigt den Unterschied (auf der Testkopie: *would move 1 block, 7 variables, 1 file*).
2. Ohne `--dry-run` verschiebt es den Block; das Werkzeug behält die Verschiebung nur, wenn die wirksamen Werte gleich bleiben (sonst nimmt es sie zurück, außer mit `--force`). `--only=MainMenuID,FooterMenuID` beschränkt es auf einige Variablen.
3. Auf der Testkopie, mit `mysite` an erster Stelle in `ActiveExtensions[]`, wurde seine Siteaccess-Datei nach der des Themes gelesen, und die wirksame Liste des Kopfmenüs war nach der Verschiebung unverändert.

## Welchen Weg Sie wählen

Versionieren Sie die Einstellungen mit den Templates, die sie lesen.

## Prüfen

- `exp:ini where menu.ini/SiteInfo/MainMenuID site` zeigt die Datei der Erweiterung; der wirksame Wert ist unverändert.

## Verwandte Seiten

- [exp:ini für Site-Entwickler](../../../../../eng-us/help/build-two-ways/exp-ini-for-site-builders.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
