---
title: "Den Layouttyp für einen Seitentyp wählen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1001
summary: "Der Layouttyp legt die Zonen fest: Header / Main / Footer (layout_2) für die meisten Seiten von Fit & Healthy, Header / Left / Right / Footer (layout_4) für eine Seitenleiste."
keywords: Layouttyp, Zonen, layout_2, layout_4, Header / Main / Footer, Seitenleiste, Planung, layout type, zones, sidebar, planning
type: help
---
# Den Layouttyp für einen Seitentyp wählen

Die Zonen auswählen, die ein neuer Seitentyp braucht, bevor sein Layout angelegt wird.

*Lebenszyklus: Planen, Beispiel 2 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. **Layouts > Layouts > Neues Layout** zeigt die Typen: 1 column, 2 columns, 3 columns, 4 columns, Hero + 3 columns, Sidebar left, Sidebar right, Featured, Mosaic, Single zone, Header / Main / Footer, Header / Left / Right / Footer.
2. Wählen Sie im Media-Theme **Header / Main / Footer** oder **Header / Left / Right / Footer**: Das `explayouts/layout.tpl` des Themes setzt die Zonen `header` und `footer` um `<main>` und ordnet für `layout_4` `left` und `right` an.

## Mit Templates

1. Die Zonen jedes Typs stehen in `extension/explayouts/settings/explayouts.ini.append.php`, `[LayoutType_<id>] Zones[]`; ein neuer Typ ist dort ein neuer Block plus sein Template.

## Welchen Weg Sie wählen

Verwenden Sie die beiden Typen, die das Theme zeichnet; ein neuer Typ braucht auch Template-Arbeit im Layout-Template des Themes.

## Prüfen

- Das neue Layout zeigt im Editor die Zonen, die Sie erwarten.

## Verwandte Seiten

- [Eine Kampagnenseite bauen, von Anfang bis Ende](../../../../../eng-us/help/build-two-ways/build-a-campaign-page.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
