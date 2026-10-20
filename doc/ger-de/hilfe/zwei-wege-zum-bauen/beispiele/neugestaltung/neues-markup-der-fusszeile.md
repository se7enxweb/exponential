---
title: "Das Markup der Fußzeile neu bauen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1403
summary: pagelayout/footer.tpl überschreiben; seine Datenquellen (FooterMenuID, Site-Info, Footer links) behalten oder ändern.
keywords: Fußzeile, pagelayout/footer.tpl, Override, Neugestaltung, footer, override, redesign
type: help
---
# Das Markup der Fußzeile neu bauen

Eine Fußzeile in drei Spalten: Menü, Social-Media-Links, Adresse.

*Lebenszyklus: Neugestaltung, Beispiel 4 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Der Template-Block der Fußzeile in Header / Footer bindet Ihr Template ein.

## Mit Templates

1. Kopieren Sie `extension/sevenx_themes_media/design/media/templates/pagelayout/footer.tpl` nach `extension/mysite/design/media/templates/pagelayout/footer.tpl`.
2. Behalten Sie den Code der drei Teile (die Schleife über `ezini( 'SiteInfo', 'FooterMenuID', 'menu.ini' )`, die Felder der Site-Info, das Fetch der Footer links) und verteilen Sie ihn auf Spalten.
3. Leeren Sie **Template & Inhalt** und prüfen Sie eine Seite, die es verwendet.

## Welchen Weg Sie wählen

Templates.

## Prüfen

- Beide Linkzeilen und die Social-Media-Symbole sind noch da.

## Verwandte Seiten

- [Wie die Fußzeile aufgebaut ist](../../wie-die-fusszeile-aufgebaut-ist.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
