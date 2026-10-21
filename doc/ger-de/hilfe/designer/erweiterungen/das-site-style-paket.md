---
title: "Das Seitenstil-Paket"
audience: designer
category: theme-extensions
order: 350
summary: Der Assistent Seitenstil packt zwei Stylesheets (Website und Klassen), ihre Bilder und ein Vorschaubild in ein Exponential-Paket; wofür er da ist, jeder Schritt und wann eine vollständige Theme-Erweiterung die bessere Wahl ist.
keywords: Seitenstil, Site Style, ezstyle, Paket-Assistent, site.css, classes.css, SiteCSS, ClassesCSS, Vorschaubild, Farbschema, Stilpaket, site style, package wizard, thumbnail, color scheme, style package
type: help
---
# Das Seitenstil-Paket

Diese Seite ist für Designer, die ein **Farbschema** für ein bestehendes Design teilen wollen statt eines ganzen Themes.
Der Assistent Seitenstil (`ezstyle`) ist der älteste Paketersteller genau dafür.

## Was er packt

Laut Code des Erstellers (`kernel/classes/packagecreators/ezstyle/`):

- ein **Website-Stylesheet** und ein **Klassen-Stylesheet** (gespeichert unter den Variablennamen `sitecssfile` und
  `classescssfile`);
- beliebig viele **Bilder**, im Paket unter `images` gespeichert;
- ein **Vorschaubild** des Aussehens.

Sie entsprechen den beiden Stylesheet-Einstellungen von `design.ini [StylesheetSettings]`: `SiteCSS` und `ClassesCSS`
(in den Standardwerten des Kernels `stylesheets/t02/site-colors.css` und `stylesheets/t02/classes-colors.css`).

## Schritt für Schritt

1. Bereiten Sie die beiden Stylesheets und ihre Bilder vor sowie ein Vorschaubild einer Seite in diesem Stil.
2. Öffnen Sie **Setup > Pakete > Neues Paket erstellen** und wählen Sie **Seitenstil**.
3. **Paket Miniaturbild**: Wählen Sie das Vorschaubild.
4. **CSS-Dateien**: Wählen Sie das Website-Stylesheet und das Klassen-Stylesheet.
5. **Bilddateien**: **Bild hinzufügen** fügt die gewählte Datei hinzu und bleibt im Schritt; fügen Sie jedes Bild hinzu,
   dann **Fortfahren**.
6. **Paket Informationen**: Name, Zusammenfassung, Version, Lizenz (aus der eingerichteten Liste), Format.
7. **Paket Betreuer** und **Paket-Changelog**, dann prüfen und **Paket erstellen**.
8. Auf der Seite des Pakets schreibt **Herunterladen** `<name>-<version>.ezpkg`.

## Wann stattdessen eine Theme-Erweiterung

| Ein Seitenstil-Paket passt, wenn | Eine Theme-Erweiterung passt, wenn |
|---|---|
| Sich nur Farben und einige Bilder ändern | Sich Templates, Layout oder Skripte ändern |
| Das Design darunter gleich bleibt | Sie Einstellungen, Übersetzungen, Operatoren brauchen |
| Nutzer unter mehreren Aussehen wählen | Nutzer ein Theme installieren, aktualisieren und konfigurieren |

## Verwandte Seiten

- [Der Paketerstellungs-Assistent Schritt für Schritt](paketerstellungs-assistent-schritt-fuer-schritt.md)
- [Stylesheets laden](../theme-design/stylesheets-laden.md)
- [Der Paket-Assistent](../../../funktionen/6.0/paketassistent.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
