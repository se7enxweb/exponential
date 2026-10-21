---
title: "Lektion 8.2: Abschlussprojekt: eine Kampagnenseite bauen"
audience: trainer, editor, website-creator, designer
category: training
order: 82
summary: Die Teilnehmenden bauen die geplante Kampagnenseite selbstständig, mit Layout, Blöcken, Collections, verknüpften Zonen, Veröffentlichung und einer zeitlich begrenzten Zuordnung, und der Trainer prüft sie anhand einer Liste. 90 Minuten.
keywords: exponential layouts, abschlussprojekt, kampagnenseite, landingpage, hero, list, manuell, dynamisch, anheften, verknüpfte zonen, zuordnung, zeitbedingung, veröffentlichen, checkliste, capstone, campaign page, landing page, manual, dynamic, pinning, linked zones, mapping, time condition, publish, checklist
type: help
---
# Lektion 8.2: Abschlussprojekt: eine Kampagnenseite bauen

Die letzte Lektion des Kurses baut den Plan aus Lektion 8.1 ohne Schritt-für-Schritt-Anleitung. Sie nutzt jedes Modul:
ein neues Layout, Blöcke, Collections, verknüpfte Zonen, Veröffentlichen und eine Zuordnung. Die Checkliste am Ende
ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung; die Zertifizierungen des Partnerprogramms sind
davon getrennt und ihre Einzelheiten werden nicht veröffentlicht ([Zertifizierungen](../../../partner/zertifizierungen.md)).

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Layouts](../README.md) |
| Modul | [Modul 8: Abschlussprojekt](README.md) |
| Dauer | 90 Minuten (5 Präsentation, 70 Übung, 15 Prüfung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Teilnehmende, die die Module 1 bis 7 und Lektion 8.1 abgeschlossen haben |
| Voraussetzungen | [Lektion 8.1](eine-landingpage-planen.md): ein geprüfter Plan; eine veröffentlichte Kampagnenseite pro Person |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- ein Layout ohne Anleitung nach einem Plan bauen;
- gemeinsame Zonen, einen Hero, eine manuelle und eine dynamische Liste, Offset und ein angeheftetes Element
  kombinieren;
- es veröffentlichen und für einen Zeitraum einer Seite zuordnen;
- das Ergebnis abgemeldet prüfen und es ohne offenen Entwurf übergeben.

## Hinweise für Trainer

### So präsentieren Sie es

1. **Noch einmal die Aufgabe** (5 Minuten). Die Teilnehmenden bauen ihren eigenen Plan. Alle haben eine
   veröffentlichte Inhaltsseite `Autumn campaign - <Vorname>` in ihrem Kursordner (legen Sie sie vor dem Kurs an oder
   lassen Sie die Teilnehmenden sie als Schritt 1 anlegen).
2. **Während der Übung** (70 Minuten). Gehen Sie herum; antworten Sie mit einer Frage („Welches Modul hat das
   behandelt?“). Das häufigste Hindernis ist eine Zuordnung, die gegen eine höhere Priorität verliert: Schicken Sie die
   Teilnehmenden zu Lektion 7.3.
3. **Prüfung** (15 Minuten). Öffnen Sie drei Seiten abgemeldet am Beamer und gehen Sie gemeinsam die Checkliste durch.

### Häufige Fragen

**Darf ich die Lektionen verwenden?** Ja.

**Was, wenn die Zeitbedingung meine Seite heute ausblendet?** Wählen Sie einen Zeitraum, der heute einschließt.

**Was, wenn mir die Zeit ausgeht?** Veröffentlichen Sie, was Sie haben, und ordnen Sie es zu; die Prüfung deckt den
Rest ab.

### Stolperfallen

- **Die Seite einer anderen Person zuordnen.** Alle ordnen nur ihre eigene Kampagnenseite zu.
- **Die gemeinsame Kopfzeile ändern.** Das gehört nicht zu dieser Aufgabe.
- **Verwirrung durch Caches.** Wenn sich die Seite nicht ändert, prüfen Sie zuerst die Zuordnung und leeren Sie dann
  die Caches.

## Praktische Übung

Bauen Sie Ihren Plan aus Lektion 8.1. Arbeiten Sie in beliebiger Reihenfolge; die Liste ist eine Erinnerung, kein
Rezept.

1. Legen Sie das Layout an (Typ laut Plan), mit dem Namen `Campaign - <Vorname>` und dem Bezeichner
   `campaign_<Vorname>`.
2. Verknüpfen Sie die Kopf- und Fußzeilenzonen mit dem gemeinsamen Kopf-/Fußzeilen-Layout.
3. Fügen Sie das Banner hinzu (einen **Hero**-Block oder eine Hero-Komponente) mit Titel, einem Satz und einer
   Schaltfläche.
4. Fügen Sie eine **List** mit dem Ansichtstyp `grid_featured` und einer manuellen Collection aus drei Elementen hinzu.
5. Fügen Sie eine **List** mit einer dynamischen Collection hinzu: die neuesten Artikel des Ordners des Trainers,
   Inhaltstyp *Article*, neueste zuerst, Limit 6, und einen Offset, wenn sie eine Abfrage mit einer anderen Liste
   teilt.
6. Heften Sie ein Element an der ersten Stelle der dynamischen Liste an und vermerken Sie den Grund im **Namen** des
   Blocks.
7. Fügen Sie einen **Full-view**-Block hinzu, wenn Ihre Kampagnenseite eigenen Text hat.
8. Benennen Sie jeden Block klar; wenden Sie eine CSS-Klasse des Themes an.
9. Veröffentlichen Sie das Layout (**Layout veröffentlichen**, bei Nachfrage **Clear caches**).
10. Legen Sie eine Zuordnung an: Knotenziel = Ihre Kampagnenseite, eine Bedingung **time**, die heute einschließt,
    eine Priorität über der normalen Zuordnung der Seite; aktivieren Sie sie.
11. Öffnen Sie Ihre Kampagnenseite abgemeldet und in Smartphone-Breite; beheben Sie, was nicht stimmt.
12. Stellen Sie sicher, dass kein Entwurf Ihres Layouts offen ist; schreiben Sie eine dreizeilige Übergabenotiz.

## Erwartetes Ergebnis

Der Trainer prüft jede Kampagnenseite anhand dieser Liste:

| Prüfpunkt | Wo nachsehen |
|---|---|
| Layout des geplanten Typs, benannt und mit Bezeichner wie vereinbart | Liste **Layouts** |
| Kopf- und Fußzeile mit dem gemeinsamen Layout verknüpft | Der Editor (verknüpfte Zonen) oder die Seite |
| Banner mit Titel, Satz und Schaltfläche | Die Seite |
| Manuelle Liste mit drei Elementen | Die Seite und die Collection des Blocks |
| Dynamische Liste: Artikel, neueste zuerst, Limit 6, keine Doppelungen mit der manuellen Liste | Die Seite |
| Ein angeheftetes Element mit dem Grund im Namen des Blocks | Der Editor |
| Layout veröffentlicht, kein Entwurf offen | Das Layout öffnen |
| Zuordnung: Knotenziel, Zeitbedingung, aktiviert, gewinnt gegen die normale Zuordnung | **Layout-Zuordnungen** und die abgemeldete Seite |

Wer sechs der acht Prüfpunkte erfüllt, hat die Fähigkeiten dieses Kurses; der Trainer empfiehlt zu jedem fehlenden
Prüfpunkt die zugehörigen Lektionen zur Wiederholung.

## Quiz

1. In welcher Reihenfolge veröffentlichen und ordnen Sie zu, und warum?
2. Ihre Seite zeigt abgemeldet das alte Layout. Welche drei Dinge prüfen Sie, in welcher Reihenfolge?
3. Wie verhindern Sie, dass die dynamische Liste die manuellen Highlights wiederholt?
4. Warum vermerken Sie den Grund für ein angeheftetes Element im Namen des Blocks?
5. Was bewirkt die Zeitbedingung nach dem Kampagnenmonat?

<details>
<summary>Antworten</summary>

1. Erst das Layout veröffentlichen, dann zuordnen (oder die Zuordnung aktivieren): Eine Zuordnung zu einem
   unveröffentlichten Layout zeigt nichts Neues.
2. Die Zuordnungen (Priorität, aktiviert), den Veröffentlichungsstatus des Layouts, dann die Caches
   ([Layout-Regeln](../../../../../eng-us/help/creators/website-creators/layout-rules.md#troubleshooting)).
3. Verwenden Sie einen Offset, wenn beide dieselbe Abfrage verwenden, oder wählen Sie eine Abfrage, die die
   hervorgehobenen Elemente ausschließt.
4. Damit der nächste Redakteur weiß, warum sich diese Position nie ändert.
5. Die Zuordnung passt nicht mehr, sodass die normale Zuordnung der Seite übernimmt.

</details>

## Weiterführende Informationen

- [Exponential Layouts: Durchgang B](../../../../../eng-us/bc/6.0/LAYOUTS.md#walkthrough-b-building-a-new-layout-from-scratch):
  dieselbe Aufgabe in der Referenz.
- [Der Layout-Editor](../../../layout-editor.md): der Editor in Kürze, zum Aufbewahren für die Teilnehmenden.

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
