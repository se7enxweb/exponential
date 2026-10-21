---
title: "Lektion 4.1: Listenblöcke und manuelle Collections"
audience: trainer, editor, website-creator
category: training
order: 41
summary: Die Teilnehmenden fügen einen List-Block hinzu, wählen seinen Ansichtstyp, füllen eine manuelle Collection mit + Add items aus dem Inhaltsbrowser, ordnen und entfernen Elemente und überschreiben den Ansichtstyp eines Elements. 45 Minuten.
keywords: exponential layouts, list-block, listing, ansichtstyp, grid, grid featured, collection, manuelle collection, add items, inhaltsbrowser, set position, remove all manual items, list block, view type, manual collection, content browser
type: help
---
# Lektion 4.1: Listenblöcke und manuelle Collections

Listenblöcke zeigen Inhalte der Website: eine Liste von Artikeln, ein Raster von Produkten, eine Galerie. Was sie
zeigen, ist ihre **Collection**. Diese Lektion beginnt mit der einfachsten Art: einer **manuellen** Collection aus von
Hand ausgewählten Elementen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Layouts](../README.md) |
| Modul | [Modul 4: Collections](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure und Website-Ersteller |
| Voraussetzungen | [Modul 3](../modul-03-bloecke/README.md); einige veröffentlichte Artikel auf der Schulungs-Website |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Listenblöcke und die sechs Ansichtstypen des Blocks **List** nennen;
- eine manuelle Collection mit **+ Add items** über den Inhaltsbrowser füllen;
- die Position eines Elements mit **Set position** ändern und Elemente entfernen;
- den Ansichtstyp eines einzelnen Elements überschreiben;
- zwischen einer manuellen und einer dynamischen Collection wählen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Collections: einem Block seinen Inhalt geben](../../../../../eng-us/bc/6.0/LAYOUTS.md#collections-giving-a-block-its-content)
und [Blockkatalog](../../../../../eng-us/bc/6.0/LAYOUTS.md#block-catalogue).

1. **Listenblöcke** (3 Minuten). In der Gruppe **Listing**: **List**, **Grid**, **Zig-Zag (List)**, **Accordion
   (List)**, **Sushi bar**, **Single content**; Galerien in **Gallery**. **List** ist das Arbeitspferd mit sechs
   Ansichtstypen: `list`, `grid`, `list_numbered`, `list_zigzag`, `list_accordion`, `grid_featured`. Ändern Sie den
   Ansichtstyp, bevor Sie zu einem anderen Block greifen.
2. **Manuelle Collections** (5 Minuten). Jedes Element wird von Hand über den Inhaltsbrowser ausgewählt: richtig für
   bestimmte Inhalte, die sich nicht ändern, etwa drei benannte Produkte. In der Collection des Blocks öffnet **+ Add
   items** den Browser; ausgewählte Elemente erscheinen der Reihe nach.
3. **Aktionen für Elemente** (4 Minuten). Jedes Element hat Aktionen wie **Set position** (verschieben; *New position
   must be larger than 0*), **Override item view type**, **Edit in CMS** (öffnet das Inhaltselement) und **Remove**;
   **Remove all manual items** leert die Liste. Ein im Inhaltsbaum verborgenes Element ist mit *Hidden in CMS*
   gekennzeichnet.
4. **Manuell oder dynamisch** (3 Minuten). Manuell für kuratierte Zusammenstellungen; dynamisch (Lektion 4.2) für
   Listen, die sich selbst aktuell halten sollen.

### Häufige Fragen

**Aktualisiert sich eine manuelle Collection, wenn ich den Artikel bearbeite?** Ja: Sie verweist auf das Element;
Änderungen am Element werden angezeigt. Neue Elemente fügt sie aber nicht von selbst hinzu.

**Warum ist ein Element mit Hidden in CMS gekennzeichnet?** Es ist im Inhaltsbaum verborgen; Besucher sehen es in der
Liste nicht.

**Wo ändere ich die Anzahl der angezeigten Elemente?** Mit **Limit** (Lektion 4.3).

### Stolperfallen

- **An der falschen Stelle auswählen.** Der Inhaltsbrowser beginnt an einem bestimmten Ort; navigieren Sie, bevor Sie
  auswählen.
- **Manuell für Nachrichten.** Eine manuelle Nachrichtenliste ist nächste Woche veraltet. Verwenden Sie dynamisch.
- **Versehentlich alles entfernen.** **Remove all manual items** fragt vorher nach; lesen Sie die Frage.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihr Kurs-Layout. Der Trainer hat drei veröffentlichte Artikel zum
Auswählen genannt.

1. Öffnen Sie Ihr Kurs-Layout mit **Öffnen** (setzen Sie Ihren Entwurf fort, wenn Sie gefragt werden).
2. Klicken Sie auf **Block hinzufügen**, öffnen Sie **Listing** und ziehen Sie einen **List**-Block in *MAIN* unter
   den Full-view-Block.
3. Wählen Sie ihn aus. Setzen Sie unter **Design** den Ansichtstyp auf `grid`.
4. Wählen Sie in seiner Collection eine manuelle Collection, wenn der Block danach fragt, und klicken Sie auf **+ Add
   items**.
5. Suchen Sie im Inhaltsbrowser die drei Artikel, wählen Sie sie aus und bestätigen Sie.
6. Sehen Sie sich die drei Elemente im Block an. Verschieben Sie das letzte mit **Set position** auf Position 1.
7. Ändern Sie den Ansichtstyp eines Elements mit **Override item view type**, wenn Ihre Installation eine Auswahl
   anbietet; setzen Sie ihn danach zurück.
8. Entfernen Sie ein Element und fügen Sie es wieder hinzu.
9. Setzen Sie den Ansichtstyp des Blocks auf `list` und vergleichen Sie.
10. Geben Sie dem Block den **Namen** `Hand-picked articles` und wählen Sie **Entwurf speichern und schließen**.

## Erwartetes Ergebnis

- Der Entwurf hat einen List-Block namens *Hand-picked articles* mit drei Elementen in der gewählten Reihenfolge.
- Die Teilnehmenden haben gesehen, wie sich der Block zwischen `grid` und `list` ändert.
- Der Trainer prüft das, indem er den Entwurf einer Person am Beamer öffnet.

## Quiz

1. Nennen Sie drei Ansichtstypen des Blocks **List**.
2. Was öffnet den Inhaltsbrowser für eine manuelle Collection?
3. Wann ist eine manuelle Collection die richtige Wahl?
   A. Für die neuesten Nachrichten B. Für drei benannte Produkte C. Für alle Unterelemente eines Ordners D. Für
   Suchergebnisse
4. Was bedeutet *Hidden in CMS* bei einem Element?
5. Richtig oder falsch: Eine manuelle Collection fügt neu veröffentlichte Artikel von selbst hinzu.

<details>
<summary>Antworten</summary>

1. Drei beliebige aus `list`, `grid`, `list_numbered`, `list_zigzag`, `list_accordion`, `grid_featured`
   ([Exponential Layouts](../../../../../eng-us/bc/6.0/LAYOUTS.md#block-catalogue)).
2. **+ Add items**.
3. **B.**
4. Das Element ist im Inhaltsbaum verborgen; Besucher sehen es nicht.
5. **Falsch.**

</details>

## Weiterführende Informationen

- [Exponential Layouts: Collections](../../../../../eng-us/bc/6.0/LAYOUTS.md#collections-giving-a-block-its-content)
- [Layout-Typen, Zonen und Blöcke: Collections](../../../../../eng-us/help/creators/website-creators/layout-types-zones-and-blocks.md#collections)

## Verwandte Seiten

- [Modul 4: Collections](README.md)
- [Exponential Layouts: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
