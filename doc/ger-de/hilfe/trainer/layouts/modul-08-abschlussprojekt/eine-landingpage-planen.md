---
title: "Lektion 8.1: Eine Landingpage planen"
audience: trainer, editor, website-creator, designer
category: training
order: 81
summary: Die Teilnehmenden planen eine Kampagnen-Landingpage auf einem Planungsblatt, mit Layout-Typ, Zonen, Blöcken mit Ansichtstypen, Collections und der Zuordnung, und prüfen den Plan einer anderen Person, bevor sie bauen. 45 Minuten.
keywords: exponential layouts, planung, planungsblatt, landingpage, kampagnenseite, layout-typ, zonen, blöcke, collections, zuordnung, prüfung, planning, planning sheet, landing page, campaign page, layout type, zones, blocks, mapping, review
type: help
---
# Lektion 8.1: Eine Landingpage planen

Gute Layouts werden geplant, bevor sie gebaut werden. In dieser Lektion planen die Teilnehmenden eine
Kampagnen-Landingpage auf Papier, mit dem Planungsblatt aus der Dokumentation für Website-Ersteller, und prüfen
gegenseitig ihre Pläne. Lektion 8.2 baut sie.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Layouts](../README.md) |
| Modul | [Modul 8: Abschlussprojekt](README.md) |
| Dauer | 45 Minuten (10 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure, Website-Ersteller und Designer, die die Module 1 bis 7 abgeschlossen haben |
| Voraussetzungen | [Modul 7](../modul-07-zuordnungsregeln/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- aus einer Aufgabenbeschreibung einen Plan machen: Layout-Typ, Zonen, Blöcke mit Ansichtstypen, Collections,
  Zuordnung;
- entscheiden, was mit gemeinsamen Layouts verknüpft, was manuell und was dynamisch ist;
- eine Zuordnung mit Ziel, Bedingungen und Priorität wählen;
- einen Plan anhand einer Checkliste prüfen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Layout-Typen, Zonen und Blöcke: Planungsblatt](../../../../../eng-us/help/creators/website-creators/layout-types-zones-and-blocks.md#planning-sheet)
und [Landingpages mit dem Layout-Editor: Eine Kampagnenseite von Grund auf](../../../../../eng-us/help/creators/website-creators/landing-pages-with-layouts.md#a-campaign-page-from-scratch).

1. **Die Aufgabe** (3 Minuten). Zeigen Sie sie am Beamer: *Eine Herbstkampagnen-Seite für die Schulungs-Website. Ein
   großes Banner mit Titel, einem Satz und einer Schaltfläche; drei von Hand ausgewählte Highlights; darunter die sechs
   neuesten Artikel eines Ordners; die Kopf- und Fußzeile der Website. Das Kampagnen-Layout soll nur auf der
   Kampagnenseite verwendet werden, während des Kampagnenmonats.*
2. **Das Planungsblatt** (4 Minuten). Spalten: Seite, Layout-Typ, Zone, Blöcke (Typ / Ansichtstyp), Collection. Zeigen
   Sie das Beispiel aus der Dokumentation: eine Startseite auf `layout_2`, Kopf- und Fußzeile verknüpft, ein Hero und
   eine `list` mit `grid_featured` in `main`, eine dynamische Collection der neuesten Artikel mit Limit 7.
3. **Prüf-Checkliste** (3 Minuten): ein Full-view-Block oder keiner (und warum); Kopf- und Fußzeile verknüpft; manuell
   nur für kuratierte Elemente; dynamische Listen mit Inhaltstyp-Filter und Sortierung; Offset, wo zwei Listen eine
   Abfrage teilen; ein Knotenziel; eine Zeitbedingung für den Kampagnenzeitraum; eine Priorität über der normalen
   Zuordnung der Seite.

### Häufige Fragen

**Welcher Block erzeugt das Banner?** Ein **Hero**-Block oder eine Hero-Komponente mit einem Stil, in `post_header`.

**Soll der eigene Text der Kampagnenseite erscheinen?** Wenn das Element der Kampagnenseite Text hat, zeigt ein
**Full-view**-Block ihn.

**Wie lange sollte eine Kampagnenzuordnung gelten?** `from` und `to` der Zeitbedingung legen es fest; danach übernimmt
wieder die normale Zuordnung der Seite.

### Stolperfallen

- **Pläne ohne Zuordnung.** Das Layout würde nie angezeigt. Die Zeile für die Zuordnung gehört zum Blatt.
- **Zu viele Blöcke.** Eine Landingpage mit fünfzehn Blöcken heißt langes Scrollen. Kürzen Sie.
- **Manuelle Listen für Nachrichten.** Verwenden Sie dynamische.

## Praktische Übung

Arbeiten Sie zuerst allein, dann zu zweit. Verwenden Sie Papier oder eine Kopie des Planungsblatts.

1. Lesen Sie die Aufgabe.
2. Wählen Sie den Layout-Typ und schreiben Sie ihn auf das Blatt.
3. Listen Sie für jede Zone die Blöcke mit Typ und Ansichtstyp auf.
4. Markieren Sie, welche Zonen mit dem gemeinsamen Kopf-/Fußzeilen-Layout verknüpft sind.
5. Schreiben Sie für jeden Listenblock die Collection auf: manuell (welche Elemente) oder dynamisch (welche Abfrage,
   welcher Filter, welche Sortierung, welcher Offset, welches Limit).
6. Schreiben Sie die Zuordnung auf: Ziel, Bedingungen (einschließlich der Zeit) und wie ihre Priorität im Vergleich zur
   normalen Zuordnung der Seite steht.
7. Tauschen Sie die Pläne mit Ihrem Partner und prüfen Sie dessen Plan anhand der Checkliste, die der Trainer zeigt.
   Schreiben Sie zwei Anmerkungen.
8. Verbessern Sie Ihren Plan mit den Anmerkungen Ihres Partners.

## Erwartetes Ergebnis

- Alle Teilnehmenden haben einen vollständigen Plan, zum Beispiel: `layout_2`; *HEADER* und *FOOTER* verknüpft;
  *POST_HEADER*: Hero; *MAIN*: List (`grid_featured`, manuell, drei Elemente), List (`list`, dynamisch, Article unter
  dem Kampagnenordner, neueste zuerst, Limit 6); Zuordnung: Knoten = die Kampagnenseite, Zeit vom ersten bis zum
  letzten Tag des Monats, Priorität über der normalen Zuordnung der Seite.
- Jeder Plan hat zwei Prüfanmerkungen.
- Der Trainer prüft zwei Pläne am Beamer.

## Quiz

1. Welche fünf Spalten hat das Planungsblatt?
2. Welche Bedingung beschränkt eine Zuordnung auf den Kampagnenmonat?
3. Warum werden die Kopf- und Fußzeilenzonen verknüpft, statt sie zu bauen?
4. Wann ist auf dieser Seite eine manuelle Collection richtig, und wann eine dynamische?
5. Ihr Plan hat ein Knotenziel und eine Zeitbedingung. Was muss die Zuordnung noch haben, um gegen die normale
   Zuordnung der Seite zu gewinnen?

<details>
<summary>Antworten</summary>

1. Seite, Layout-Typ, Zone, Blöcke (Typ / Ansichtstyp), Collection
   ([Layout-Typen, Zonen und Blöcke](../../../../../eng-us/help/creators/website-creators/layout-types-zones-and-blocks.md#planning-sheet)).
2. Eine Bedingung **time** mit `from` und `to`.
3. Sie werden einmal in einem gemeinsamen Layout definiert; Änderungen dort erreichen jede Seite.
4. Manuell für die drei kuratierten Highlights; dynamisch für die neuesten Artikel.
5. Eine höhere Priorität.

</details>

## Weiterführende Informationen

- [Landingpages mit dem Layout-Editor](../../../../../eng-us/help/creators/website-creators/landing-pages-with-layouts.md)
- [Layout-Regeln: Rezepte](../../../../../eng-us/help/creators/website-creators/layout-rules.md#recipes)

## Verwandte Seiten

- [Modul 8: Abschlussprojekt](README.md)
- [Exponential Layouts: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
