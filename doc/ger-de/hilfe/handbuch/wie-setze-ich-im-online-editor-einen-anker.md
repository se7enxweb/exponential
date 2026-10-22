---
title: Wie setze ich im Online-Editor einen Anker und verlinke darauf?
audience: editor
category: manual
order: 485
summary: Setzen Sie den Cursor an die Stelle, an der der Sprung landen soll, klicken Sie auf Anchor und geben Sie einen Namen ein; markieren Sie dann den Linktext, klicken Sie auf Insert link und wählen Sie den Anker unter Anchor in this text, oder hängen Sie #name an einen Link auf eine andere Seite an.
keywords: Anker, Sprungmarke, Sprunglink, Inhaltsverzeichnis, Link auf einen Teil einer Seite, Fragment, #, Link einfügen, Online-Editor, anchor, jump link, table of contents, link to a part of a page, fragment, insert link, anchor in this text, online editor, TinyMCE 8
type: help
---
# Wie setze ich im Online-Editor einen Anker und verlinke darauf?

Ein Anker markiert eine Stelle in einem Text. Ein Link darauf springt direkt dorthin; das ist nützlich für ein
Inhaltsverzeichnis am Anfang einer langen Seite oder um auf einen Abschnitt einer anderen Seite zu verweisen. Die
Schaltflächennamen unten sind die des Editors TinyMCE 8; ein anderer Editor, den Ihre Website anbietet, kann sie anders
nennen.

## Wer das tun kann

Redakteure, die das Element bearbeiten dürfen.

## Schritte

1. Bearbeiten Sie das Element und setzen Sie den Cursor an den Anfang der Stelle, zu der gesprungen werden soll, zum
   Beispiel eine Überschrift.
2. Klicken Sie in der Werkzeugleiste auf **Anchor**, geben Sie einen kurzen Namen ohne Leerzeichen ein (zum Beispiel
   `opening-hours`) und bestätigen Sie.
   Im Text erscheint eine kleine Ankermarke.
3. Markieren Sie den Text, der zum Link werden soll.
4. Klicken Sie auf **Insert link**.
5. Für einen Sprung innerhalb desselben Textes wählen Sie den Anker unter **Anchor in this text**. Für eine Stelle auf
   einer anderen Seite wählen Sie die Seite mit **Durchsuchen** oder **Suchen** und hängen `#` und den Ankernamen an
   die Adresse an.
6. Klicken Sie auf **OK** und dann auf **Zur Veröffentlichung schicken**.

## Was schiefgehen kann

- **Der Link öffnet die Seite, springt aber nicht.** Der Ankername im Link weicht vom Namen des Ankers ab, oder der
  Anker wurde entfernt. Die Namen müssen genau übereinstimmen, auch in Groß- und Kleinschreibung.
- **Zwei Anker haben denselben Namen.** Nur der erste wird erreicht. Geben Sie jedem Anker in einem Text einen eigenen
  Namen.
- **Der Sprung landet auf der Website an der falschen Stelle.** Ein fest stehender Kopfbereich verdeckt den Anfang des
  Ziels. Fragen Sie die Person, die sich um das Design kümmert.

## Verwandte Seiten

- [Wie füge ich im Online-Editor einen Link ein?](wie-fuege-ich-im-online-editor-einen-link-ein.md)
- [Tags für formatierten Text](../buch-lernen/rich-text-tags.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
