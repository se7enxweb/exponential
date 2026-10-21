---
title: "Lektion 3.4: Container und der Full-view-Block"
audience: trainer, editor, website-creator, designer
category: training
order: 34
summary: Die Teilnehmenden teilen eine Zone mit Container-Blöcken in Spalten, platzieren Blöcke in deren Platzhaltern und verwenden den Full-view-Block, um den eigenen Inhalt der Seite innerhalb eines Layouts zu zeigen. 45 Minuten.
keywords: exponential layouts, container, zwei spalten, drei spalten, vier spalten, spalte, platzhalter, verschachtelung, full-view-block, seitengerüst, components, containers, two columns, three columns, four columns, column, placeholders, nesting, full view block, page shell
type: help
---
# Lektion 3.4: Container und der Full-view-Block

Zwei Blöcke unterscheiden sich von allen anderen. **Container** enthalten andere Blöcke, sodass eine Zone ohne neuen
Layout-Typ in Spalten geteilt werden kann. Der Block **Full view** zeigt das eigene Inhaltselement der Seite innerhalb
eines Layouts, sodass ein Layout gewöhnliche Inhaltsseiten einrahmen kann. Diese Lektion vermittelt beides.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Layouts](../README.md) |
| Modul | [Modul 3: Blöcke](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure, Website-Ersteller und Designer |
| Voraussetzungen | [Lektion 3.3](schaltflaechen-bilder-und-der-design-tab.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- einen Container **Two columns**, **Three columns**, **Four columns** oder **Column** hinzufügen und seine Platzhalter
  füllen;
- die Aufteilung eines zweispaltigen Containers über seinen Ansichtstyp ändern;
- erklären, warum die Verschachtelung flach bleiben sollte;
- einen **Full-view**-Block hinzufügen und erklären, was er auf jeder Seite zeigt, für die das Layout verwendet wird;
- die Komponentenblöcke nennen und sagen, wo ihre Verwendung aufgelistet ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Exponential Layouts: Container und Verschachtelung](../../../../../eng-us/bc/6.0/LAYOUTS.md#containers-and-nesting) und
[Blockkatalog](../../../../../eng-us/bc/6.0/LAYOUTS.md#block-catalogue).

1. **Container** (4 Minuten). Vier Container-Blöcke, jeder mit Platzhaltern (benannten Steckplätzen wie kleine Zonen):

   | Container | Platzhalter |
   |---|---|
   | Two columns | `left`, `right` |
   | Three columns | `col_1`, `col_2`, `col_3` |
   | Four columns | `col_1` bis `col_4` |
   | Column | `main` |

   Two columns hat die Ansichtstypen `two_columns_66_33` und `two_columns_33_66` für die Breitenaufteilung; ein
   Wechsel des Ansichtstyps ordnet neu an, ohne Blöcke zu verschieben.
2. **Verschachtelung** (2 Minuten). Container lassen sich verschachteln; zwei Ebenen sind leicht zu bearbeiten, vier
   nicht.
3. **Full view** (4 Minuten). In der Gruppe **Placeholders**. Er stellt das aktuelle Inhaltselement in seiner normalen
   Vollansicht dar: Bei einem Artikel zeigt er den Artikel. Die meisten Layouts für Seitengerüste brauchen genau einen.
   Ein Layout mit einem Full-view-Block, das einem Teilbaum zugeordnet ist, rahmt jede Seite darin ein.
4. **Components** (3 Minuten). Die Gruppe **Components** enthält vorgestaltete Abschnitte (Hero, Features, About,
   Logos, Quote, Lead) mit jeweils mehreren Stilen. Der Bildschirm **Components** im Reiter Layouts listet auf, wo
   jede verwendet wird.

### Häufige Fragen

**Warum zeigt mein Full-view-Block im Editor nichts?** Der Editor hat kein aktuelles Inhaltselement; die Vorschau oder
die Website zeigen ihn mit echtem Inhalt.

**Container oder ein Layout-Typ mit mehr Zonen?** Container in einem einfachen Gerüst sind flexibler; ein neuer
Layout-Typ ist eine Aufgabe für Entwickler.

**Sind Container responsiv?** Die Spalten-Templates tragen die responsiven Klassen des Themes; prüfen Sie es in
Smartphone-Breite.

### Stolperfallen

- **In den Container statt in einen Platzhalter ablegen.** Zielen Sie auf den Ablagebereich innerhalb des Containers.
- **Zwei Full-view-Blöcke.** Der Inhalt erscheint doppelt.
- **Tiefe Verschachtelung.** Hören Sie bei zwei Ebenen auf.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihr Kurs-Layout.

1. Öffnen Sie Ihr Kurs-Layout mit **Öffnen** (setzen Sie Ihren Entwurf fort, wenn Sie gefragt werden).
2. Klicken Sie auf **Block hinzufügen**, öffnen Sie die Gruppe **Containers** und ziehen Sie **Two columns** in
   *MAIN* über den Rich-text-Block.
3. Ziehen Sie den **Rich-text**-Block in den linken Platzhalter des Containers und den **Button / Link**-Block in den
   rechten Platzhalter.
4. Wählen Sie den Container aus und wechseln Sie unter **Design** seinen Ansichtstyp zwischen `two_columns_66_33` und
   `two_columns_33_66`. Beobachten Sie, wie sich die Aufteilung ändert.
5. Öffnen Sie die Gruppe **Placeholders** und ziehen Sie einen **Full-view**-Block in *MAIN* unter den Container.
6. Öffnen Sie die Gruppe **Components** und lesen Sie, welche Blocktypen sie anbietet. Fügen Sie keinen hinzu.
7. Wählen Sie **Entwurf speichern und schließen**.
8. Öffnen Sie im linken Menü des Reiters Layouts **Components** und finden Sie heraus, wo eine Komponente auf der
   Schulungs-Website verwendet wird.

## Erwartetes Ergebnis

- Der Entwurf hat einen zweispaltigen Container mit dem Rich text links und der Schaltfläche rechts sowie einen
  Full-view-Block darunter.
- Die Teilnehmenden haben gesehen, wie sich die Aufteilung mit dem Ansichtstyp ändert.
- Der Trainer prüft das, indem er den Entwurf einer Person am Beamer öffnet und die Platzhalter benennt.

## Quiz

1. Was ist ein Platzhalter?
2. Welche Ansichtstypen hat der Container **Two columns**?
3. Was zeigt ein **Full-view**-Block?
   A. Eine Liste von Artikeln B. Das aktuelle Inhaltselement in seiner Vollansicht C. Die Kopfzeile der Website D. Ein
   Bild
4. Wie tief sollten Container verschachtelt werden?
5. Wo sehen Sie, welche Seiten eine Komponente verwenden?

<details>
<summary>Antworten</summary>

1. Ein benannter Steckplatz innerhalb eines Containers, der Blöcke enthält, wie eine kleine Zone
   ([Exponential Layouts](../../../../../eng-us/bc/6.0/LAYOUTS.md#containers-and-nesting)).
2. `two_columns_66_33` und `two_columns_33_66`.
3. **B.**
4. Flach: Zwei Ebenen sind leicht zu bearbeiten.
5. Auf dem Bildschirm **Components** im Reiter Layouts.

</details>

## Weiterführende Informationen

- [Exponential Layouts: Container und Verschachtelung](../../../../../eng-us/bc/6.0/LAYOUTS.md#containers-and-nesting)
- [Layout-Typen, Zonen und Blöcke](../../../../../eng-us/help/creators/website-creators/layout-types-zones-and-blocks.md#containers)

## Verwandte Seiten

- [Modul 3: Blöcke](README.md)
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
