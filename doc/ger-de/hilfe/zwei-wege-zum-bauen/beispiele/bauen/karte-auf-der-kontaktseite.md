---
title: "Eine Karte auf der Kontaktseite"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1115
summary: Der Map-Block (Map embed URL, Width, Height) im Layout der Kontaktseite.
keywords: Karte, Map-Block, Einbettung, Kontaktseite, Standort, iframe, map, Map block, embed, contact page, location
type: help
---
# Eine Karte auf der Kontaktseite

Eine Karte mit der Adresse des Studios über dem Kontaktformular.

*Lebenszyklus: Bauen, Beispiel 16 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und *das Layout der Kontaktseite*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Map** (Gruppe *Basic*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Map embed URL** (die Einbettungsadresse, die der Kartendienst angibt), **Width** (Standard `100%`) und **Height (px)** (Standard 400) aus.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Contact (Knoten 185) ist eine Seite vom Typ *Contact form*; prüfen Sie unter **Layouts > Layout-Zuordnungen**, welches Layout sie bekommt, oder geben Sie ihr eines ([Welches Layout bekommt eine Seite?](../../../../../eng-us/help/build-two-ways/layout-mappings.md)).

## Mit Templates

1. Fügen Sie das `<iframe>` der Karte in das Full-View-Template des Kontaktformulars in Ihrer Design-Erweiterung ein; beachten Sie die Content Security Policy der Site für Frames.

## Welchen Weg Sie wählen

Der Block hält die Karte beim Seitenlayout und bearbeitbar.

## Prüfen

- Die Karte lädt, und die Seite funktioniert auch, wenn sie nicht lädt (Tastatur, keine Fehler in der Konsole).

## Verwandte Seiten

- [Welches Layout bekommt eine Seite?](../../../../../eng-us/help/build-two-ways/layout-mappings.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
