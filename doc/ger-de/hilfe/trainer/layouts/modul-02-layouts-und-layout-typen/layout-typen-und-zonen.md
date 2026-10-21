---
title: "Lektion 2.1: Layout-Typen und ihre Zonen"
audience: trainer, editor, website-creator, designer
category: training
order: 21
summary: Die Teilnehmenden nennen die zwölf Layout-Typen und ihre Zonen, wählen einen Typ für ein Seitengerüst oder einen Inhaltsbereich und erklären, warum der Typ später nicht geändert werden kann. 30 Minuten.
keywords: exponential layouts, layout-typ, zonen, 1 column, 2 columns, hero, sidebar, featured, mosaic, single zone, header main footer, layout_2, layout_4, seitengerüst, layout type, zones, page shell
type: help
---
# Lektion 2.1: Layout-Typen und ihre Zonen

Jedes Layout beginnt mit einem **Layout-Typ**, und der Typ legt die Zonen endgültig fest. Ihn gut zu wählen ist die
erste Gestaltungsentscheidung für eine Seite. Diese Lektion stellt die zwölf mitgelieferten Typen vor und zeigt, wie
man wählt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Layouts](../README.md) |
| Modul | [Modul 2: Layouts und Layout-Typen](README.md) |
| Dauer | 30 Minuten (15 Präsentation, 15 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Redakteure, Website-Ersteller und Designer |
| Voraussetzungen | [Modul 1](../modul-01-layouts-kennenlernen/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die zwölf Layout-Typen und die Zonen der meistgenutzten nennen;
- für eine bestimmte Seite einen Typ für ein Seitengerüst (mit Kopf- und Fußzeilenzonen) oder für einen
  Inhaltsbereich wählen;
- erklären, warum der Typ eines Layouts später nicht geändert werden kann;
- den Typ eines Layouts in der Liste **Layouts** ablesen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Layout-Typen, Zonen und Blöcke](../../../../../eng-us/help/creators/website-creators/layout-types-zones-and-blocks.md#layout-types)
und [Exponential Layouts: Die Layout-Typen](../../../../../eng-us/bc/6.0/LAYOUTS.md#the-layout-types).

1. **Die Tabelle** (6 Minuten). Zeigen Sie sie am Beamer:

   | Bezeichner | Name | Zonen |
   |---|---|---|
   | `1_column` | 1 column | `main` |
   | `2_column` | 2 columns | `left`, `right` |
   | `3_column` | 3 columns | `left`, `main`, `right` |
   | `4_column` | 4 columns | `col1`, `col2`, `col3`, `col4` |
   | `hero` | Hero + 3 columns | `top`, `left`, `main`, `right` |
   | `sidebar_left` | Sidebar left | `sidebar`, `main` |
   | `sidebar_right` | Sidebar right | `main`, `sidebar` |
   | `featured` | Featured | `hero`, `feature1`, `feature2`, `feature3`, `bottom` |
   | `mosaic` | Mosaic | `a`, `b`, `c`, `d`, `e` |
   | `layout_1` | Single zone | `main` |
   | `layout_2` | Header / Main / Footer | `header`, `post_header`, `main`, `pre_footer`, `footer` |
   | `layout_4` | Header / Left / Right / Footer | `header`, `post_header`, `left`, `right`, `pre_footer`, `footer` |

2. **Gerüst oder Bereich** (3 Minuten). `layout_1`, `layout_2` und `layout_4` bilden ein ganzes Seitengerüst (mit
   Kopf- und Fußzeilenzonen); die Spaltentypen eignen sich für einen Inhaltsbereich.
3. **Endgültig** (2 Minuten). Blöcke gehören zu Zonen, deshalb kann sich der Typ nicht ändern. Eine falsche Wahl
   bedeutet ein neues Layout (die Blöcke von Hand übertragen oder neu bauen).
4. **Container** (2 Minuten). Innerhalb einer Zone teilen Container-Blöcke den Bereich weiter auf (Modul 3), sodass
   wenige Typen die meisten Seiten abdecken.

### Häufige Fragen

**Kann eine Website eigene Layout-Typen haben?** Ja, Entwickler fügen sie in den Einstellungen hinzu
(`[LayoutType_*]`-Blöcke in den Einstellungen der Layouts-Erweiterung), mit einem Template, das die Zonen zeichnet.

**Welchen Typ verwenden die meisten Startseiten?** In den Beispieldaten des Multisite-Pakets verwenden viele
Seiten-Layouts `layout_2` (Header / Main / Footer); sehen Sie in der Liste **Layouts** Ihrer eigenen Website nach.

**Wofür ist `post_header`?** Ein Streifen direkt unter der Kopfzeile, zum Beispiel für ein Hero-Banner oder eine
Brotkrumennavigation.

### Stolperfallen

- **Spalten für eine ganze Seite wählen.** Die Seite hat dann keine Kopf- und Fußzeilenzonen, die mit der gemeinsamen
  Kopf- und Fußzeile verknüpft werden können (Modul 5).
- **Zu viel planen.** Die Teilnehmenden vergleichen für jede Seite alle zwölf Typen. Beginnen Sie mit `layout_2` und
  Containern.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Papier.

1. Öffnen Sie **Layouts** > **Layouts** und stellen Sie **Sortieren nach** auf **Layout-Typ**.
2. Notieren Sie, welche Layout-Typen die Schulungs-Website verwendet und wie viele Layouts jeweils.
3. Klicken Sie bei einem `layout_2`-Layout auf **Vorschau** und erkennen Sie seine fünf Zonen auf dem Bildschirm.
4. Wählen Sie für jede dieser Seiten einen Layout-Typ und notieren Sie, warum:
   - eine Startseite mit Kopfzeile, einem großen Banner, einer Nachrichtenliste und einer Fußzeile;
   - eine Kontaktseite mit einem Formular links und der Adresse rechts;
   - eine Seite für eine Produkteinführung mit einem großen Hero und drei Feature-Kästen.
5. Listen Sie für Ihre Wahl für die Startseite auf, in welche Zone jeder Teil kommt.
6. Vergleichen Sie mit Ihrem Nachbarn.

## Erwartetes Ergebnis

- Die Notizen listen die verwendeten Typen mit ihrer Anzahl auf.
- Sinnvolle Entscheidungen: `layout_2` für die Startseite (Banner in `post_header`, Nachrichten in `main`), `layout_4`
  oder `2_column` für die Kontaktseite, `featured` oder `hero` (oder `layout_2` mit Containern) für die
  Produkteinführung.
- Der Trainer prüft die Entscheidungen und Begründungen von zwei Teilnehmenden.

## Quiz

1. Welche Zonen hat `layout_2`?
2. Welche Layout-Typen enthalten Kopf- und Fußzeilenzonen?
3. Richtig oder falsch: Sie können den Typ eines Layouts nach dem Anlegen ändern.
4. Welcher Typ hat die Zonen `top`, `left`, `main`, `right`?
   A. `3_column` B. `hero` C. `featured` D. `mosaic`
5. Wie teilen Sie eine Zone in Spalten auf, ohne einen anderen Layout-Typ zu verwenden?

<details>
<summary>Antworten</summary>

1. `header`, `post_header`, `main`, `pre_footer`, `footer`.
2. `layout_2` und `layout_4` (und `layout_1` ist ein Gerüst mit einer einzigen Zone).
3. **Falsch.** Blöcke gehören zu Zonen, deshalb ist der Typ endgültig.
4. **B.**
5. Mit einem Container-Block (zwei, drei oder vier Spalten) innerhalb der Zone.

</details>

## Weiterführende Informationen

- [Layout-Typen, Zonen und Blöcke](../../../../../eng-us/help/creators/website-creators/layout-types-zones-and-blocks.md): Typen, Blöcke
  und ein Planungsblatt.
- [Exponential Layouts: Zonen](../../../../../eng-us/bc/6.0/LAYOUTS.md#zones): was eine Zone leistet.

## Verwandte Seiten

- [Modul 2: Layouts und Layout-Typen](README.md)
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
