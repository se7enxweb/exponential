---
title: "Einen Ansichtstyp nach einer Neugestaltung ausmustern"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1417
summary: Ihn erst aus ViewTypes[] entfernen, wenn kein Block ihn mehr verwendet; finden Sie diese in einem Export.
keywords: Ansichtstyp ausmustern, Aufräumen, ViewTypes, Export, Neugestaltung, retire view type, cleanup, export, redesign
type: help
---
# Einen Ansichtstyp nach einer Neugestaltung ausmustern

Ein Aussehen aufräumen, das das neue Design nicht hat.

*Lebenszyklus: Neugestaltung, Beispiel 18 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit einem Export und Einstellungen](#mit-einem-export-und-einstellungen)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Stellen Sie jeden gefundenen Block auf einen anderen Ansichtstyp um (beachten Sie bei explayouts_ui_api vor 1.3.29 die Eigenheit beim Speichern).

## Mit einem Export und Einstellungen

1. Exportieren Sie alle Layouts und suchen Sie in `layouts.json` nach `"view_type": "<name>"`; stellen Sie diese Blöcke zuerst um.
2. Entfernen Sie dann die Zeile `ViewTypes[]` (mit `exp:ini rem`) und das Template.

## Welchen Weg Sie wählen

Blöcke behalten ihren gespeicherten Ansichtstyp; ein entfernter würde auf ein fehlendes Template zeigen.

## Prüfen

- Ein neuer Export enthält keinen Block mit dem alten Ansichtstyp.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../layouts-exportieren-und-importieren.md)
- [Einen Ansichtstyp hinzufügen](../../ansichtstyp-hinzufuegen.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
