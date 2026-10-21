---
title: "Lektion 4.1: Grundlagen des Rich Text"
audience: trainer, editor
category: training
order: 41
summary: Die Teilnehmenden formatieren Text im Online-Editor (TinyMCE 8) mit Absatzformaten, Überschriften, fett und kursiv, Listen, Rückgängig und Wiederholen, Sonderzeichen und Vollbild und fügen Text sauber ein. 45 Minuten.
keywords: redakteure 101, online-editor, rich text, TinyMCE 8, ezoe, werkzeugleiste, absatz, überschrift, fett, kursiv, liste, rückgängig, einfügen, vollbild, tastenkürzel, editors 101, online editor, toolbar, heading, bold, italic, paste, fullscreen
type: help
---
# Lektion 4.1: Grundlagen des Rich Text

Felder wie **Intro** und **Body** eines Artikels sind Rich Text: Sie enthalten formatierten Text, Links, Tabellen und
eingebettete Bilder. Sie werden mit dem **Online-Editor** bearbeitet, der auf aktuellen Installationen TinyMCE 8 ist.
Diese Lektion behandelt die alltägliche Formatierung; Links folgen in Lektion 4.2, Einbettungen und Tabellen in
Lektion 4.3.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Redakteure 101](../README.md) |
| Modul | [Modul 4: Der Online-Editor](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Neue Redakteure und Autoren |
| Voraussetzungen | [Modul 3](../modul-03-erstellen-und-bearbeiten/README.md): Alle Teilnehmenden haben einen Artikel in einem Kursordner |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Gruppen der Werkzeugleiste benennen und eine Schaltfläche über ihren Tooltip finden;
- einen Text mit Absätzen, Überschriften und Listen gliedern statt mit Leerzeilen und fettem Text;
- fett, kursiv, Rückgängig und Wiederholen mit Maus und Tastatur verwenden;
- ein Sonderzeichen einfügen und im Vollbild arbeiten;
- Text aus einem anderen Programm einfügen und das Ergebnis prüfen.

## Hinweise für Trainer

### Welchen Editor die Teilnehmenden sehen

Der Online-Editor von Exponential (die Erweiterung **ezoe**) hat zwei Engines: TinyMCE 3 und TinyMCE 8. Beide
schreiben denselben gespeicherten Text, sodass Redakteure jederzeit wechseln können. Welche verwendet wird, wird pro
Website, pro Siteaccess oder pro Benutzer festgelegt; eine Installation kann außerdem weitere Editoren anbieten (zum
Beispiel **Tiptap**). Prüfen Sie vor dem Kurs, welche auf Ihrer Schulungsinstallation voreingestellt ist, und zeigen
Sie nach Möglichkeit TinyMCE 8. Quelle:
[Der Online-Editor auf Basis von TinyMCE 8](../../../../../eng-us/features/6.0/online-editor-tinymce8.md).

### So präsentieren Sie es

1. **Die Werkzeugleiste** (5 Minuten). Öffnen Sie einen Artikel mit **Bearbeiten** und gehen Sie zu **Body**. Gehen
   Sie die Werkzeugleiste entlang: die Liste der Absatzformate, fett, kursiv und unterstrichen, tiefgestellt und
   hochgestellt, Ausrichtung, Listen und Einzüge, Rückgängig und Wiederholen, Links und Anker, eingebettete Objekte,
   benutzerdefinierte Tags, Sonderzeichen, Tabellen, **Vollbild**, **Hilfe** und die Schaltfläche für den Quellcode.
   Jede Schaltfläche erklärt sich selbst, wenn Sie darauf zeigen. Quelle:
   [Rich Text](../../../rundgang-durch-die-administration.md#3-formatierter-text).
2. **Struktur, nicht Aussehen** (4 Minuten). Verwenden Sie die Liste der Absatzformate für Überschriften, niemals eine
   fette Zeile in größerer Schrift. Überschriften und Listen sind das, was Screenreader, Suchmaschinen und das Design der
   Website nutzen. Zeigen Sie eine Überschrift und eine Aufzählung.
3. **Tastatur** (2 Minuten). `Ctrl+B` fett, `Ctrl+I` kursiv, `Ctrl+Z` rückgängig (`Cmd` auf einem Mac). Die
   Schaltfläche **Hilfe** von TinyMCE listet jedes Tastenkürzel auf.
4. **Einfügen** (2 Minuten). Fügen Sie einen Absatz aus einer Textverarbeitung ein und sehen Sie sich das Ergebnis an.
   Der gespeicherte Text behält nur, was der Editor erlaubt; prüfen Sie nach dem Einfügen die Überschriften und Listen.
5. **Statusleiste** (2 Minuten). In TinyMCE 8 zeigt die Statusleiste den Pfad des Elements, in dem der Cursor steht,
   zum Beispiel *Path: paragraph » embed*; ein Klick auf ein Element dort öffnet seinen Dialog.

### Häufige Fragen

**Kann ich die Schriftart oder die Farbe ändern?** Nein, und das ist Absicht: Das Design der Website entscheidet, wie
Text aussieht. Sie entscheiden, was der Text ist (eine Überschrift, eine Liste, eine Hervorhebung).

**Wofür ist die Schaltfläche für den Quellcode?** Sie zeigt den Text als Markup des Editors. Redakteure brauchen sie
selten; Änderungen dort können verloren gehen, wenn das Markup nicht erlaubt ist.

**Warum sieht der Editor meines Kollegen anders aus?** Eine andere Engine (TinyMCE 3, TinyMCE 8 oder Tiptap), von der
Website oder vom Benutzer gewählt. Lektion 4.3 zeigt, wo man sie wählt.

**Muss ich das Feld speichern?** Nein. Das Feld ist Teil des Entwurfs; **Entwurf speichern und beenden** oder
**Zur Veröffentlichung schicken** bewahrt es.

### Stolperfallen

- **Leere Absätze als Abstand.** Die Teilnehmenden drücken zweimal die Eingabetaste, um Abstand zu schaffen. Das Design
  der Website legt die Abstände fest; entfernen Sie leere Absätze.
- **Fett statt Überschriften.** Zeigen Sie den Unterschied im Pfad der Statusleiste: *heading* gegenüber
  *paragraph » strong*.
- **Formatierung aus anderen Programmen einfügen.** Sieht ein eingefügter Text falsch aus, machen Sie es rückgängig
  und fügen Sie ihn mit dem Befehl Ihres Browsers zum Einfügen als reiner Text erneut ein; formatieren Sie ihn dann im
  Editor.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihren Artikel *My first article* (Lektion 3.2).

1. Öffnen Sie Ihren Artikel und klicken Sie auf **Bearbeiten**. Gehen Sie zum Feld **Body**.
2. Zeigen Sie auf jede Schaltfläche der Werkzeugleiste und lesen Sie ihren Tooltip. Schreiben Sie drei Schaltflächen
   auf, die Sie nicht erwartet haben.
3. Setzen Sie den Cursor an den Anfang des Haupttexts und geben Sie `What I want to learn` ein. Wählen Sie mit dem
   Cursor in dieser Zeile eine Überschrift aus der Liste der Absatzformate.
4. Geben Sie unter der Überschrift drei Dinge ein, die Sie lernen möchten, jedes in einer eigenen Zeile. Markieren Sie
   die drei Zeilen und klicken Sie auf die Schaltfläche für die Aufzählung.
5. Machen Sie ein Wort mit `Ctrl+B` **fett** und ein Wort mit `Ctrl+I` *kursiv*.
6. Drücken Sie zweimal `Ctrl+Z` und dann zweimal die Schaltfläche Wiederholen. Beobachten Sie, was sich ändert.
7. Fügen Sie mit der Schaltfläche für Sonderzeichen ein Sonderzeichen ein (zum Beispiel `©` oder `€`).
8. Klicken Sie auf **Vollbild**, schreiben Sie einen weiteren Satz und verlassen Sie das Vollbild mit derselben
   Schaltfläche.
9. Kopieren Sie einen kurzen Absatz aus einem beliebigen Dokument oder einer Webseite und fügen Sie ihn am Ende ein.
   Prüfen Sie seine Formatierung und entfernen Sie alles, was nicht passt.
10. Klicken Sie in die Überschrift und lesen Sie den Pfad in der Statusleiste unten im Editor.
11. Klicken Sie auf **Zur Veröffentlichung schicken** und sehen Sie sich den Artikel mit **Auf der Website ansehen**
    an.

## Erwartetes Ergebnis

- Der veröffentlichte Artikel zeigt eine Überschrift, eine Aufzählung mit drei Einträgen, ein fettes und ein kursives
  Wort, ein Sonderzeichen und den eingefügten Absatz.
- Auf der Website verwenden die Überschrift und die Liste das Design der Website.
- Die Teilnehmenden haben in der Statusleiste einen Pfad wie *Path: heading* gelesen (TinyMCE 8).
- Der Trainer prüft das, indem er den Artikel einer Teilnehmerin oder eines Teilnehmers auf der Website auf dem
  Projektor öffnet.

![Ein Rich-Text-Feld mit seiner Werkzeugleiste und unten dem Pfad des Elements unter dem Cursor](../../../../../eng-us/help/images/trainers/e101-richtext-toolbar.webp)

## Quiz

1. Warum verwendet man für Überschriften die Liste der Absatzformate, statt eine Zeile fett zu machen?
2. Welches Tastenkürzel macht Text kursiv?
   A. `Ctrl+B` B. `Ctrl+I` C. `Ctrl+U` D. `Ctrl+H`
3. Wo finden Sie die vollständige Liste der Tastenkürzel des Editors?
4. Richtig oder falsch: TinyMCE 3 und TinyMCE 8 speichern den Text unterschiedlich, deshalb kann man nicht wechseln.
5. Ein eingefügter Absatz bringt seltsame Formatierungen mit. Was tun Sie?

<details>
<summary>Antworten</summary>

1. Überschriften geben dem Text eine Struktur, die Screenreader, Suchmaschinen und das Design der Website nutzen.
2. **B.** `Ctrl+I` (`Cmd+I` auf einem Mac) ([Tastenkürzel](../../../tastenkuerzel.md#das-bearbeitungsformular)).
3. Unter der Schaltfläche **Hilfe** von TinyMCE.
4. **Falsch.** Beide schreiben dasselbe gespeicherte XML, sodass Redakteure hin und her wechseln können
   ([Der Online-Editor auf Basis von TinyMCE 8](../../../../../eng-us/features/6.0/online-editor-tinymce8.md)).
5. Rückgängig machen, als reinen Text erneut einfügen und mit der Werkzeugleiste formatieren.

</details>

## Weiterführende Literatur

- [Der Online-Editor auf Basis von TinyMCE 8](../../../../../eng-us/features/6.0/online-editor-tinymce8.md): die Engines, der
  Wechsel und jeder Dialog.
- [Inhalte erstellen und bearbeiten](../../../inhalte-bearbeiten.md#rich-text): Rich Text im Bearbeitungsformular.
- [Tastenkürzel](../../../tastenkuerzel.md): die Tasten des Bearbeitungsformulars.

## Verwandte Seiten

- [Modul 4: Der Online-Editor](README.md)
- [Redakteure 101: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
