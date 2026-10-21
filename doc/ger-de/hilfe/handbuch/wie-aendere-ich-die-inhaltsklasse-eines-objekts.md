---
title: Wie ändere ich die Inhaltsklasse eines Objekts?
audience: admin
category: manual
order: 340
summary: Wählen Sie im Kontextmenü des Objekts Change content class, wählen Sie die Zielklasse, ordnen Sie jedes alte Feld einem neuen zu und bestätigen Sie; das Objekt behält seinen Platz, seine ID und seine Versionen.
keywords: Klasse ändern, umwandeln, Inhaltsklasse, expchangeclass, changeclass/convert, Zuordnung der Attribute, Artikel zu Seite, change class, convert, content class, attributes mapping, article to page
type: help
---
# Wie ändere ich die Inhaltsklasse eines Objekts?

Wenn ein Objekt mit der falschen Klasse angelegt wurde (ein Artikel, der eine Seite sein sollte), können Sie es
umwandeln, statt den Text von Hand zu kopieren. Jedes Feld der alten Klasse wird einem Feld der neuen zugeordnet.

## Wer das tun kann

Benutzer, deren Rolle `changeclass/convert` hat (in einer neuen Installation die Erweiterung expchangeclass). In der
Regel nur Administratoren, weil eine falsche Zuordnung Feldinhalte verliert.

## Schritte

1. Öffnen Sie in der **Inhaltsstruktur** das Kontextmenü des Objekts und wählen Sie **Change content class**.
2. Wählen Sie unter **Destination content class** die neue Klasse und klicken Sie auf **Select**.
   Die Seite **Attributes mapping** öffnet sich, mit der **Source class** links und der **Destination class** rechts.
3. Wählen Sie für jedes Feld der Zielklasse, welches Feld der Quellklasse es füllt.
4. Lesen Sie die **Warnings**: Ein Feld ohne Entsprechung wird geleert.
5. Klicken Sie auf **Modify Object**.
6. Klicken Sie auf **Continue to new object** und prüfen Sie das Ergebnis.

## Was schiefgehen kann

- **Achtung: Ein Quellfeld, das nicht zugeordnet ist, geht verloren.** Prüfen Sie die Warnungen und testen Sie zuerst an
  einer Kopie, wenn das Objekt wichtig ist.
- **Der Menüeintrag fehlt.** Ihrer Rolle fehlt `changeclass/convert`.
- **Templates der Website sehen falsch aus.** Die neue Klasse hat andere Templates; das ist zu erwarten und Sache des
  Designers.

## Verwandte Seiten

- [Inhaltsklassen für Websites](../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md)
- [Inhaltsmodell und Bearbeitung](../../anleitungen/inhaltsmodell-und-bearbeitung.md)
- [Benutzerhandbuch](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
