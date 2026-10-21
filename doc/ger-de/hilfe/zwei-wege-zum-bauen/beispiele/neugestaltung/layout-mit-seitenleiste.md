---
title: "Einen Seitentyp auf ein Layout mit Seitenleiste umstellen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1408
summary: "Der Typ eines Layouts lässt sich nicht an Ort und Stelle wechseln: ein Layout des neuen Typs anlegen, neu bauen, neu zuordnen."
keywords: Layouttyp, Seitenleiste, layout_4, neu bauen, neu zuordnen, Neugestaltung, layout type, sidebar, rebuild, remap, redesign
type: help
---
# Einen Seitentyp auf ein Layout mit Seitenleiste umstellen

Artikel bekommen eine Seitenleiste wie Rezepte.

*Lebenszyklus: Neugestaltung, Beispiel 9 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. **Neues Layout** vom Typ **Header / Left / Right / Footer**.
2. Verknüpfen Sie `header`, `footer` (und bei Bedarf `pre_footer`) mit den gemeinsamen Layouts; bauen Sie `left` und `right`.
3. Veröffentlichen Sie und richten Sie dann die Artikel-Zuordnung darauf aus (**Anderes Layout verknüpfen** an der Zuordnung) oder legen Sie eine neue Zuordnung darüber an.

## Mit Templates

1. Im Media-Design ordnet `explayouts/layout.tpl` für `layout_4` `left` und `right` bereits in einer Bootstrap-Zeile an.

## Welchen Weg Sie wählen

Behalten Sie das alte Layout, bis das neue geprüft ist; das Umstellen der Zuordnung ist ein Schritt zurück.

## Prüfen

- Artikel zeigen die Seitenleiste.

## Verwandte Seiten

- [Welches Layout bekommt eine Seite?](../../layout-zuordnungen.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
