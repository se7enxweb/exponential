---
title: "Lektion 4.2: Links und Anker"
audience: trainer, editor
category: training
order: 42
summary: Die Teilnehmenden verlinken Text mit einem anderen Element der Website, mit einer externen Adresse und mit einer E-Mail-Adresse, setzen einen Anker und verstehen die Linkprüfungen beim Speichern des Texts. 45 Minuten.
keywords: redakteure 101, online-editor, link, interner link, externer link, anker, mailto, durchsuchen, suchen, lesezeichen, link-dialog, toter link, TinyMCE 8, ezoe, editors 101, internal link, external link, anchor, link dialog, dead link
type: help
---
# Lektion 4.2: Links und Anker

Links verbinden einen Text mit dem Rest der Website und mit dem Web. Exponential verlinkt Elemente der Website über
ihren Platz im Baum, sodass ein Link weiter funktioniert, wenn ein Element umbenannt wird. Diese Lektion vermittelt den
Link-Dialog, Anker und das, was der Editor meldet, wenn ein Link defekt ist.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Redakteure 101](../README.md) |
| Modul | [Modul 4: Der Online-Editor](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Neue Redakteure und Autoren |
| Voraussetzungen | [Lektion 4.1](grundlagen-des-rich-text.md); einige Elemente auf der Schulungs-Website, auf die verlinkt werden kann |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Text mit den Reitern **Durchsuchen**, **Suchen** oder **Lesezeichen** des Link-Dialogs mit einem anderen Element der
  Website verlinken;
- Text mit einer externen Webadresse und mit einer E-Mail-Adresse verlinken;
- einen Anker setzen und darauf verlinken;
- einen Link entfernen;
- die Meldungen über dem Bearbeitungsformular lesen, wenn ein Link repariert oder entfernt werden musste.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Der Online-Editor auf Basis von TinyMCE 8](../../../../../eng-us/features/6.0/online-editor-tinymce8.md#what-the-tinymce-8-engine-does)
und [Linkprüfungen beim Speichern des Texts](../../../../../eng-us/features/6.0/online-editor-tinymce8.md#link-checks-when-the-text-is-saved).

1. **Interne Links** (5 Minuten). Markieren Sie ein Wort und klicken Sie auf die Link-Schaltfläche. Der Link-Dialog von
   ezoe hat die Reiter **Durchsuchen**, **Suchen** und **Lesezeichen** (Ihre eigenen Lesezeichen), um ein Element der
   Website zu wählen. Wählen Sie ein Element und bestätigen Sie. Erklären Sie, dass ein solcher Link auf das Element
   zeigt, nicht auf eine eingetippte Adresse, sodass ein Umbenennen des Elements ihn nicht zerstört.
2. **Externe und E-Mail-Links** (3 Minuten). Geben Sie eine Adresse ein, die mit `https://` beginnt, oder `mailto:`
   gefolgt von einer E-Mail-Adresse. Verwenden Sie im Kurs Adressen unter `example.invalid`.
3. **Anker** (3 Minuten). Setzen Sie den Cursor an eine Stelle in einem langen Text und klicken Sie auf die
   Anker-Schaltfläche; geben Sie ihm einen kurzen Namen. Verlinken Sie dann vom Anfang des Texts darauf.
4. **Einen Link entfernen** (1 Minute): die Schaltfläche zum Entfernen des Links neben der Link-Schaltfläche.
5. **Linkprüfungen** (3 Minuten). Beim Speichern des Texts wird jeder Link geprüft. Ein Link auf ein Element, das nicht
   mehr existiert, bleibt erhalten, und eine Meldung über dem Formular sagt, wie er zu reparieren ist (sie nennt den
   Link mit seinem Text und sagt es, wenn das Ziel im Papierkorb liegt). Ein Skript-Link (etwa `javascript:`) wird
   entfernt, ebenso ein `mailto:`-Link mit ungültiger Adresse; beides wird gemeldet. Die Meldung erscheint über dem
   Bearbeitungsformular, zum Beispiel *Der Entwurf wurde nur teilweise gespeichert.*

### Häufige Fragen

**Soll ich auf die Adresse verlinken, die ich von der Website kopiert habe, oder Durchsuchen verwenden?** Verwenden
Sie **Durchsuchen** oder **Suchen**: Ein interner Link folgt dem Element, wenn es verschoben oder umbenannt wird; eine
kopierte Adresse kann ins Leere führen.

**Was passiert mit meinem Link, wenn jemand die verlinkte Seite entfernt?** Der Link bleibt in Ihrem Text, und beim
nächsten Speichern meldet der Editor ihn mit seinem Text, damit Sie ihn reparieren können.

**Kann ein Link in einem neuen Fenster öffnen?** Das hängt von den Optionen des Link-Dialogs auf Ihrer Installation
ab; prüfen Sie es auf Ihrer eigenen Schulungs-Website, bevor Sie es versprechen.

### Stolperfallen

- **Ganze Sätze verlinken.** Der Linktext sollte sagen, wohin er führt. „Lesen Sie die Öffnungszeiten“ ist besser als
  „hier klicken“.
- **Testadressen.** Tragen Sie niemals die E-Mail-Adressen echter Kunden oder Kollegen in Schulungsinhalte ein.
  Verwenden Sie Adressen unter `example.invalid`.
- **Das Wiederherstellen aus dem Papierkorb repariert keinen Link über die Knotennummer.** Die Funktionsseite weist
  darauf hin, dass ein wiederhergestelltes Element einen neuen Ort bekommt, sodass ein Link auf seinen alten Knoten
  defekt bleibt. Verlinken Sie es mit **Durchsuchen** neu.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihren Artikel *My first article*.

1. Öffnen Sie Ihren Artikel und klicken Sie auf **Bearbeiten**. Gehen Sie zu **Body**.
2. Geben Sie den Satz `Read my news about the course.` ein. Markieren Sie die Wörter `my news`.
3. Klicken Sie auf die Link-Schaltfläche. Öffnen Sie im Dialog den Reiter **Durchsuchen** und suchen Sie Ihren Artikel
   *News one (updated)* in Ihrem Kursordner (Lektion 3.4). Wählen Sie ihn und bestätigen Sie.
4. Geben Sie den Satz `The documentation of Exponential is online.` ein. Markieren Sie `documentation` und verlinken
   Sie es mit der Adresse, die Ihnen der Trainer gibt (eine `https://`-Adresse).
5. Geben Sie `Write to the course team.` ein. Markieren Sie `course team` und verlinken Sie es mit
   `mailto:course@example.invalid`.
6. Scrollen Sie zur Überschrift *What I want to learn* (Lektion 4.1). Setzen Sie den Cursor an ihren Anfang und
   klicken Sie auf die Anker-Schaltfläche. Nennen Sie den Anker `learn`.
7. Geben Sie ganz oben in **Body** `Jump to my goals` ein und verlinken Sie es mit dem Anker `learn`.
8. Markieren Sie erneut die Wörter `course team` und klicken Sie auf die Schaltfläche zum Entfernen des Links. Machen
   Sie es dann mit `Ctrl+Z` rückgängig.
9. Klicken Sie auf **Entwurf speichern und beenden**. Prüfen Sie oben auf der Seite, ob es eine Meldung zu Links gibt.
10. Öffnen Sie den Entwurf erneut aus **Meine Entwürfe** und klicken Sie auf **Zur Veröffentlichung schicken**.
11. Klicken Sie auf **Auf der Website ansehen** und probieren Sie jeden Link aus.

## Erwartetes Ergebnis

- Auf der Website öffnet *my news* den Nachrichtenartikel der Teilnehmenden, *documentation* die externe Adresse,
  *course team* eine neue E-Mail an `course@example.invalid`, und *Jump to my goals* springt zur Überschrift.
- Es erschien keine Meldung zu Links, weil alle Links gültig waren.
- Der Trainer prüft das, indem er die Links einer Teilnehmerin oder eines Teilnehmers auf dem Projektor ausprobiert.

![Ein Artikel auf der öffentlichen Website, wo Links, Überschriften und Listen nach dem Veröffentlichen geprüft werden](../../../../../eng-us/help/images/trainers/e101-site-article.webp)

## Quiz

1. Nennen Sie die drei Reiter des Link-Dialogs, mit denen man ein Element der Website wählt.
2. Warum ist ein mit **Durchsuchen** gewählter Link besser als eine eingefügte Adresse derselben Seite?
3. Was passiert mit einem `javascript:`-Link, wenn der Text gespeichert wird?
   A. Er bleibt erhalten B. Er wird entfernt und gemeldet C. Er wird zu einem Anker D. Er wird zu einem E-Mail-Link
4. Eine verlinkte Seite wurde entfernt. Was tut der Editor beim nächsten Speichern des Texts mit Ihrem Link?
5. Welche E-Mail-Domain verwenden Sie für Beispieladressen in Schulungsinhalten?

<details>
<summary>Antworten</summary>

1. **Durchsuchen**, **Suchen** und **Lesezeichen**
   ([Der Online-Editor auf Basis von TinyMCE 8](../../../../../eng-us/features/6.0/online-editor-tinymce8.md#what-the-tinymce-8-engine-does)).
2. Er zeigt auf das Element und funktioniert deshalb weiter, wenn das Element umbenannt oder verschoben wird.
3. **B.** Skript-Links werden entfernt und gemeldet.
4. Er behält den Link und zeigt über dem Formular eine Meldung, die ihn nennt und sagt, wie er zu reparieren ist.
5. `example.invalid`.

</details>

## Weiterführende Literatur

- [Linkprüfungen beim Speichern des Texts](../../../../../eng-us/features/6.0/online-editor-tinymce8.md#link-checks-when-the-text-is-saved):
  jede Meldung im Detail.
- [Lesezeichen](../../../../anleitungen/lesezeichen.md): Ihre Lesezeichen erscheinen auch im Link-Dialog.
- [Inhalte erstellen und bearbeiten](../../../inhalte-bearbeiten.md#rich-text): Rich-Text-Felder.

## Verwandte Seiten

- [Modul 4: Der Online-Editor](README.md)
- [Redakteure 101: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
