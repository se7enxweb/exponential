---
title: "Lektion 2.1: Verknüpfungen und Einbetten"
audience: trainer, editor
category: training
order: 21
summary: Die Teilnehmenden erklären die drei Wege, auf denen ein Element auf ein anderes verweist (Relationsfelder, Einbettungen und Links im Rich Text), lesen den Tab Verknüpfungen in beide Richtungen und verwenden ein Feld für eine Relationsliste. 45 Minuten.
keywords: redakteure 201, verknüpfungen, verknüpfte objekte, rückwärts verknüpfte objekte, einbetten, link, relationsfeld, relationsliste, vorhandene objekte hinzufügen, verknüpftes für baum umdrehen, editors 201, relations, related objects, reverse related objects, embed, relation field, relation list, add existing objects, reverse related for subtree
type: help
---
# Lektion 2.1: Verknüpfungen und Einbetten

Elemente einer Website verweisen aufeinander: Ein Artikel zeigt ein Bild, eine Seite verlinkt auf ein Dokument, eine
Liste nennt verwandte Artikel. Exponential zeichnet diese Verweise als **Verknüpfungen** auf, und der Tab
**Verknüpfungen** zeigt sie in beide Richtungen. Wer sie kennt, ändert oder entfernt gemeinsam genutzte Inhalte sicher.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Redakteure 201](../README.md) |
| Modul | [Modul 2: Verknüpfungen, Orte und Einbetten](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure, die jede Woche veröffentlichen |
| Voraussetzungen | Redakteure 101 (Module 4 und 5) oder gleichwertige Erfahrung |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die drei Wege nennen, auf denen ein Element auf ein anderes verweist: ein Relationsfeld, eine Einbettung im Rich
  Text, ein Link im Rich Text;
- **Verknüpfte Objekte** und **Rückwärts verknüpfte Objekte** im Tab **Verknüpfungen** lesen;
- ein Feld für eine Relationsliste mit **Vorhandene Objekte hinzufügen** füllen und seine Einträge ordnen oder
  entfernen;
- herausfinden, was ein Element verwendet, bevor sie es ändern.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Verknüpfungen und Bilder](../../../inhalte-bearbeiten.md#verknüpfungen-und-bilder) und
[Entfernen und Verbergen: die Prüfung umgekehrter Verknüpfungen](../../../../../eng-us/features/6.0/reverse-relation-check.md).

1. **Drei Arten von Verweis** (5 Minuten). Zeichnen Sie sie:

   ```text
   Article --(Image field: relation)----------> Image item
   Article --(embed in Body)------------------> Image item
   Article --(link in Body)-------------------> File item / another article
   ```

   Alle drei sind Verknüpfungen, die die Website aufzeichnet. Die Prüfung vor dem Entfernen (Lektion 2.3) listet sie
   alle auf.
2. **Der Tab Verknüpfungen** (4 Minuten). Öffnen Sie ein Bild, das von einem Artikel verwendet wird.
   **Verknüpfungen (n)** zeigt zwei Tabellen: **Verknüpfte Objekte (n)**, worauf dieses Element verweist, und
   **Rückwärts verknüpfte Objekte (n)**, was auf dieses Element verweist. Bei einem Bild ist die zweite Tabelle die
   interessante.
3. **Felder für Relationslisten** (4 Minuten). Manche Typen haben ein Feld, das mehrere verknüpfte Elemente enthält
   (zum Beispiel „verwandte Artikel“). **Vorhandene Objekte hinzufügen** öffnet einen Browser, um mehrere auszuwählen;
   die Liste lässt sich ordnen, und Einträge lassen sich entfernen. Zeigen Sie es an einem Typ Ihrer
   Schulungsinstallation, der ein solches Feld hat, oder lassen Sie es weg, wenn keiner es hat.
4. **Für einen ganzen Teilbaum** (2 Minuten). **Fortgeschritten** > **Verknüpftes für Baum umdrehen** im Menü des
   Elements listet auf, was in einen Ordner und alles darunter verweist.

### Häufige Fragen

**Ist ein als Adresse eingegebener Link eine Verknüpfung?** Nur Links auf ein Element der Website (über **Durchsuchen**,
**Suchen** oder **Lesezeichen** im Link-Dialog) werden als Verknüpfungen aufgezeichnet. Eine eingefügte Webadresse ist
ein externer Link.

**Wann erscheint die Verknüpfung?** Wenn die Version veröffentlicht wird, die die Einbettung, den Link oder das Feld
enthält.

**Warum zeigt ein Bild umgekehrte Verknüpfungen, die ich nicht kenne?** Inhalte anderer Redakteure verwenden es. Genau
dafür gibt es den Tab: Prüfen Sie, bevor Sie ein gemeinsam genutztes Bild ändern.

### Stolperfallen

- **Leerer Tab Verknüpfungen bei einem Entwurf.** Verknüpfungen werden aus veröffentlichten Versionen aufgezeichnet;
  veröffentlichen Sie zuerst.
- **Kein Feld für eine Relationsliste auf der Schulungs-Website.** Prüfen Sie die Klassen vor dem Kurs; führen Sie es
  andernfalls nur mit einem Relationsfeld vor.
- **Ein gemeinsam genutztes Bild bearbeiten.** Eine Änderung daran ändert jeden Artikel unter **Rückwärts verknüpfte
  Objekte**.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihren Kursordner.

1. Legen Sie in Ihrem Kursordner einen Artikel `Our team` an. Wählen Sie in **Image** mit **Ein vorhandenes Objekt
   hinzufügen** ein Bild aus der Medienbibliothek.
2. Betten Sie in **Body** mit der Schaltfläche zum Einbetten ein zweites Bild ein und verlinken Sie einen Ausdruck über
   den Reiter **Durchsuchen** des Link-Dialogs auf einen Ihrer anderen Artikel.
3. Veröffentlichen Sie den Artikel.
4. Öffnen Sie seinen Tab **Verknüpfungen**. Notieren Sie die Elemente unter **Verknüpfte Objekte** und wie viele es
   sind.
5. Öffnen Sie das Bild, das Sie in Schritt 2 eingebettet haben. Suchen Sie in seinem Tab **Verknüpfungen** *Our team*
   unter **Rückwärts verknüpfte Objekte**.
6. Öffnen Sie den Artikel, auf den Sie in Schritt 2 verlinkt haben, und prüfen Sie auch seine **Rückwärts verknüpfte
   Objekte**.
7. Öffnen Sie im Baum das Menü Ihres Kursordners und wählen Sie **Fortgeschritten** > **Verknüpftes für Baum umdrehen**.
   Lesen Sie die Liste.
8. Wenn Ihre Schulungsinstallation einen Typ mit einem Feld für eine Relationsliste hat (der Trainer nennt ihn), legen
   Sie ein Element davon an, klicken Sie auf **Vorhandene Objekte hinzufügen**, wählen Sie zwei Ihrer Artikel aus und
   veröffentlichen Sie.

## Erwartetes Ergebnis

- *Our team* listet unter **Verknüpfte Objekte** das Hauptbild, das eingebettete Bild und den verlinkten Artikel auf.
- Sowohl das eingebettete Bild als auch der verlinkte Artikel listen *Our team* unter **Rückwärts verknüpfte Objekte**
  auf.
- **Verknüpftes für Baum umdrehen** hat die Elemente außerhalb des Kursordners aufgelistet, die in ihn verweisen
  (möglicherweise keine).
- Der Trainer prüft das, indem er den Tab **Verknüpfungen** des eingebetteten Bildes am Beamer öffnet.

![Der Tab Verknüpfungen: verknüpfte Objekte und rückwärts verknüpfte Objekte](../../../../../eng-us/help/images/trainers/e201-relations-tab.webp)

## Quiz

1. Nennen Sie die drei Wege, auf denen ein Element auf ein anderes Element verweisen kann.
2. Welche Tabelle des Tabs **Verknüpfungen** zeigt, was ein Bild verwendet?
   A. **Verknüpfte Objekte** B. **Rückwärts verknüpfte Objekte** C. **Orte** D. **Details**
3. Wann wird eine Verknüpfung aufgezeichnet?
4. Richtig oder falsch: Eine eingefügte externe Webadresse wird als Verknüpfung aufgezeichnet.
5. Wo sehen Sie, was in einen ganzen Ordner verweist?

<details>
<summary>Antworten</summary>

1. Ein Relationsfeld, eine Einbettung im Rich Text, ein Link auf ein Element im Rich Text.
2. **B.**
3. Wenn die Version, die sie enthält, veröffentlicht wird.
4. **Falsch.** Nur Links auf Elemente der Website sind Verknüpfungen.
5. Im Menü des Elements unter **Fortgeschritten** > **Verknüpftes für Baum umdrehen**.

</details>

## Weiterführende Informationen

- [Entfernen und Verbergen: die Prüfung umgekehrter Verknüpfungen](../../../../../eng-us/features/6.0/reverse-relation-check.md):
  warum Verknüpfungen vor dem Entfernen wichtig sind.
- [Inhalte erstellen und bearbeiten](../../../inhalte-bearbeiten.md#verknüpfungen-und-bilder): Relationsfelder im Formular.
- [Der Online-Editor auf TinyMCE 8](../../../../../eng-us/features/6.0/online-editor-tinymce8.md): Einbettungen und Links.

## Verwandte Seiten

- [Modul 2: Verknüpfungen, Orte und Einbetten](README.md)
- [Redakteure 201: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
