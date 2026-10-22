---
title: Wie schreibe ich Rich Text ohne den Online-Editor?
audience: editor
category: manual
order: 495
summary: Klicken Sie unter einem Rich-Text-Feld auf Editor deaktivieren, um seinen Text als einfache Tags zu bearbeiten (Absätze, Überschriften, Fett, Links, Einbettungen); Editor aktivieren schaltet zurück; die Einstellung gilt für Ihre Sitzung, und der Text wird beim Speichern oder Veröffentlichen geprüft.
keywords: Editor deaktivieren, Editor aktivieren, vereinfachtes XML, Rich-Text-Tags, Auszeichnung, Überschrift, fett, kursiv, Link, Einbettung, Prüfung, disable editor, enable editor, simplified XML, rich text tags, markup, header, strong, emphasize, link, embed, eznode, ezobject, validation, required data is either missing or is invalid
type: help
---
# Wie schreibe ich Rich Text ohne den Online-Editor?

Der Online-Editor ist eine Erleichterung: Was er speichert, ist strukturierter Text aus Tags. Sie können ihn
ausschalten und diese Tags selbst eingeben, zum Beispiel um eine Auszeichnung zu korrigieren, an die der Editor nicht
herankommt, um vorbereiteten Text einzufügen oder wenn der Editor in Ihrem Browser nicht lädt.

## Wer das tun kann

Redakteure, die das Element bearbeiten dürfen. Die Schaltfläche **Editor deaktivieren** wird Benutzern angezeigt, deren
Rolle `ezoe/disable_editor` hat.

## Schritte

1. Bearbeiten Sie das Element und klicken Sie unter dem Rich-Text-Feld auf **Editor deaktivieren**.
   Das Feld wird zu einem einfachen Textfeld mit dem Text als Tags.
2. Bearbeiten Sie den Text. Häufige Tags:

   | Sie möchten | Eingabe |
   |---|---|
   | einen neuen Absatz | zweimal Enter drücken (einmal Enter ergibt eine neue Zeile im selben Absatz) |
   | eine Überschrift | `<header level="2">Title</header>` |
   | fett, kursiv | `<strong>...</strong>`, `<emphasize>...</emphasize>` (oder `<b>`, `<i>`) |
   | eine Liste | `<ul><li>...</li></ul>` oder `<ol><li>...</li></ol>` |
   | einen Link auf eine andere Website | `<link href="https://example.org">text</link>` |
   | einen Link auf eine Seite dieser Website | `<link href="eznode://123">text</link>`, mit der Knotennummer |
   | ein eingebettetes Bild oder Element | `<embed href="ezobject://456" size="medium" />`, mit der Objektnummer |
   | Text, der genau wie eingegeben gezeigt wird | `<literal>...</literal>` |

3. Klicken Sie auf **Entwurf speichern**, um den Text zu prüfen.
   Ist ein Tag falsch, zeigt das Formular am Feld eine Meldung. Korrigieren Sie es und speichern Sie erneut.
4. Klicken Sie auf **Editor aktivieren**, um zurückzuschalten, oder auf **Zur Veröffentlichung schicken**.

Die Knotennummer steht als **Knoten** auf der Seite des Elements; die Objektnummer steht in seinem Tab **Details**.
Links, die mit `eznode://` oder `ezobject://` erstellt wurden, funktionieren weiter, wenn das Element verschoben wird.

## Was schiefgehen kann

- **„Entweder wurden benötigte Felder nicht ausgefüllt oder die Eingaben waren nicht gültig“.** Ein Tag ist nicht
  geschlossen, falsch geschrieben oder steht an einer Stelle, an der es nicht erlaubt ist (zum Beispiel eine
  Überschrift in einer Liste). Die Meldung am Feld nennt das Problem.
- **Der Editor bleibt auch bei anderen Feldern aus.** Die Wahl bleibt für Ihre Sitzung erhalten. Klicken Sie einmal auf
  **Editor aktivieren**, oder melden Sie sich ab und wieder an.
- **Es gibt keine Schaltfläche Editor deaktivieren.** Ihre Rolle umfasst sie nicht, oder die Website hat sie aus der
  Werkzeugleiste entfernt.

## Verwandte Seiten

- [Tags für formatierten Text](../buch-lernen/rich-text-tags.md), die vollständige Liste mit jedem Attribut
- [Inhalte erstellen und bearbeiten](../inhalte-bearbeiten.md#rich-text)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
