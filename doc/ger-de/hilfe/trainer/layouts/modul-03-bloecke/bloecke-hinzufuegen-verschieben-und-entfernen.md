---
title: "Lektion 3.1: Blöcke hinzufügen, verschieben und entfernen"
audience: trainer, editor, website-creator
category: training
order: 31
summary: Die Teilnehmenden fügen Blöcke aus dem Bereich Block hinzufügen ein, wählen einen Block aus, ziehen ihn innerhalb einer Zone und zwischen Zonen, duplizieren ihn, setzen ihn zurück und löschen ihn. 45 Minuten.
keywords: exponential layouts, block hinzufügen, blocktypen, basic, components, listing, gallery, containers, placeholders, ziehen, block verschieben, block duplizieren, block löschen, block zurücksetzen, add a block, block types, drag, move block, duplicate block, delete block, revert block
type: help
---
# Lektion 3.1: Blöcke hinzufügen, verschieben und entfernen

Blöcke sind die Bausteine eines Layouts. Diese Lektion vermittelt die Handgriffe im Editor: einen Block hinzufügen,
auswählen, ziehen, duplizieren und löschen. Die nächsten Lektionen behandeln, was in die einzelnen Blöcke gehört.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Layouts](../README.md) |
| Modul | [Modul 3: Blöcke](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Redakteure und Website-Ersteller |
| Voraussetzungen | [Modul 2](../modul-02-layouts-und-layout-typen/README.md): Alle Teilnehmenden haben ein Kurs-Layout |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- **Block hinzufügen** öffnen und einen Blocktyp in seiner Gruppe finden;
- einen Blocktyp an die richtige Stelle in einer Zone ziehen;
- einen Block auswählen und seine Eigenschaften in den Tabs **Inhalt** und **Design** sehen;
- einen Block innerhalb einer Zone und in eine andere Zone verschieben;
- einen Block über sein Menü duplizieren, zurücksetzen und löschen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Der Layout-Editor](../../../layout-editor.md#einen-block-hinzufügen) und
[Rundgang durch die Administration: der Layout-Editor](../../../rundgang-durch-die-administration.md#in-der-praxis-der-layout-editor).

1. **Block hinzufügen** (4 Minuten). Die Plus-Schaltfläche oben links, **Block hinzufügen**, öffnet die **Blocktypen**
   in Gruppen: **Basic** (Title, Text, Button / Link, Rich text, Image, Markdown ...), **Components**, **Listing**,
   **Gallery**, **Containers** und **Placeholders**. Ziehen Sie einen Typ in eine Zone, an die Stelle, an die er
   gehört.
2. **Auswählen** (2 Minuten). Klicken Sie auf einen Block: Er wird blau umrandet, und seine Eigenschaften erscheinen
   rechts in zwei Tabs, **Inhalt** (was er zeigt) und **Design** (wie er aussieht). Änderungen erscheinen sofort im
   Block.
3. **Verschieben** (3 Minuten). Ziehen Sie einen Block an seiner Kopfleiste an eine andere Stelle, in derselben oder
   einer anderen Zone.
4. **Das Blockmenü** (4 Minuten). Das Menü mit drei Punkten auf einem Block enthält **Duplicate block** (die Kopie
   wird nach dem Original eingefügt, mit seinen Einstellungen: *Block successfully duplicated.*) und **Block löschen**
   (*Are you sure you want to delete the block? This cannot be undone.*). Manche Blöcke bieten auch **Revert block**
   an, das einen Block auf seine veröffentlichte Version zurücksetzt.
5. **Entwürfe** (2 Minuten). All das geschieht im Entwurf; die Website bleibt unverändert, bis das Layout
   veröffentlicht wird.

### Häufige Fragen

**Kann ich einen gelöschten Block wiederherstellen?** Nicht innerhalb des Entwurfs. Wenn das Layout schon einmal
veröffentlicht war, wirft **Verwerfen** den ganzen Entwurf weg, oder **Revert block** setzt einen geänderten Block auf
seine veröffentlichte Version zurück, wo es angeboten wird.

**Warum kann ich keinen Block in eine Zone ablegen?** Die Zone ist möglicherweise mit einem gemeinsamen Layout
verknüpft (rot gestrichelter Rahmen, schraffierte Blöcke): Ihre Blöcke kommen von anderswo (Modul 5).

**Gibt es eine Höchstzahl von Blöcken pro Zone?** Keine dokumentierte; halten Sie die Seiten lesbar.

### Stolperfallen

- **An der falschen Stelle ablegen.** Achten Sie auf die Ablagemarkierung, bevor Sie die Maustaste loslassen.
- **Löschen statt duplizieren.** Die beiden Einträge stehen nebeneinander; die Bestätigung hilft.
- **Versehentlich in einem gemeinsamen Layout arbeiten.** Vergewissern Sie sich, dass oben der Name Ihres eigenen
  Layouts steht.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihr Kurs-Layout `Training - <Ihr Vorname>`.

1. Öffnen Sie **Layouts** > **Layouts** und klicken Sie bei Ihrem Kurs-Layout auf **Öffnen**.
2. Klicken Sie auf **Block hinzufügen**. Lesen Sie die Gruppen der **Blocktypen**.
3. Ziehen Sie einen **Title**-Block in die Zone *MAIN*.
4. Ziehen Sie einen **Text**-Block in *MAIN*, unter den Titel.
5. Klicken Sie auf den **Title**-Block und lesen Sie seine Tabs **Inhalt** und **Design**. Geben Sie im Inhaltsfeld
   einen Titel ein.
6. Ziehen Sie den **Text**-Block über den **Title**-Block und dann wieder darunter.
7. Ziehen Sie den **Title**-Block in die Zone *POST_HEADER*.
8. Öffnen Sie das Menü mit drei Punkten auf dem **Text**-Block und wählen Sie **Duplicate block**. Lesen Sie die
   Meldung.
9. Löschen Sie das Duplikat mit **Block löschen**; lesen Sie die Frage und bestätigen Sie.
10. Öffnen Sie oben das Menü mit drei Punkten und wählen Sie **Entwurf speichern und schließen**.

## Erwartetes Ergebnis

- Der Entwurf des eigenen Layouts hat einen **Title**-Block in *POST_HEADER* und einen **Text**-Block in *MAIN*.
- Die Teilnehmenden haben *Block successfully duplicated.* und die Löschfrage gesehen.
- Die Website ist unverändert (nichts wurde veröffentlicht).
- Der Trainer prüft das, indem er das Layout einer Person am Beamer öffnet.

## Quiz

1. Nennen Sie vier Gruppen von **Block hinzufügen**.
2. Was enthalten die Tabs **Inhalt** und **Design** eines ausgewählten Blocks?
3. Wie verschieben Sie einen Block in eine andere Zone?
4. Richtig oder falsch: Ein gelöschter Block kann innerhalb des Entwurfs mit Rückgängig zurückgeholt werden.
5. Was bewirkt **Revert block**?

<details>
<summary>Antworten</summary>

1. Vier beliebige aus **Basic**, **Components**, **Listing**, **Gallery**, **Containers**, **Placeholders**
   ([Der Layout-Editor](../../../layout-editor.md#einen-block-hinzufügen)).
2. **Inhalt**: was der Block zeigt; **Design**: wie er aussieht.
3. Ziehen Sie ihn an seiner Kopfleiste in die andere Zone.
4. **Falsch.** Die Löschfrage sagt, dass es nicht rückgängig gemacht werden kann.
5. Er setzt den Block auf seine veröffentlichte Version zurück.

</details>

## Weiterführende Informationen

- [Der Layout-Editor](../../../layout-editor.md): Blöcke hinzufügen und ändern.
- [Rundgang durch die Administration](../../../rundgang-durch-die-administration.md#in-der-praxis-der-layout-editor): dieselben Schritte mit
  Bildern.

## Verwandte Seiten

- [Modul 3: Blöcke](README.md)
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
