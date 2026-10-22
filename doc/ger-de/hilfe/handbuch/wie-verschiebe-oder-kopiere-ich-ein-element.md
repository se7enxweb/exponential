---
title: Wie verschiebe oder kopiere ich ein Element?
audience: editor
category: manual
order: 415
summary: Verschieben bringt ein Element und alles darunter zu einem neuen übergeordneten Element; Kopieren erstellt ein unabhängiges Duplikat eines Elements, und Teil des Baums kopieren dupliziert das Element mit allem darunter; alle drei beginnen auf der Seite des Elements oder in seinem Baummenü.
keywords: verschieben, kopieren, Teilbaum kopieren, duplizieren, neues übergeordnetes Element, neuen Ort wählen, zu kopierende Versionen, Knoten-ID, Objekt-ID, Sektion, move, copy, copy subtree, duplicate, new parent, choose a new location, versions to copy, node ID, object ID, section
context: content/copy
type: help
---
# Wie verschiebe oder kopiere ich ein Element?

**Verschieben** ändert, wo ein Element liegt; seine Adresse folgt, und alles darunter wird mitverschoben. **Kopieren**
erstellt ein neues, eigenständiges Element mit demselben Inhalt. Verwenden Sie eine Kopie als Ausgangspunkt für etwas
Ähnliches; verwenden Sie einen zusätzlichen Ort, wenn dasselbe Element an zwei Stellen erscheinen muss (siehe
[Wie zeige ich ein Element an mehr als einer Stelle?](wie-zeige-ich-ein-element-an-mehr-als-einer-stelle.md)).

## Wer das tun kann

- Verschieben erfordert `content/edit` für das Element und das Recht, seine Klasse unter dem neuen übergeordneten
  Element anzulegen.
- Kopieren erfordert `content/read` für das Element und `content/create` unter dem neuen übergeordneten Element.
  **Teil des Baums kopieren** erfordert `content/create` für jede Klasse im Teilbaum.

Eine Schaltfläche, die Sie nicht verwenden dürfen, ist ausgegraut; zeigen Sie darauf, um den Grund zu lesen.

## Schritte

1. Öffnen Sie das Element.
2. Klicken Sie neben **Bearbeiten** auf **Verschieben** oder **Kopieren**. Für einen ganzen Zweig öffnen Sie im Baum
   das Menü des Elements und wählen Sie **Teil des Baums kopieren**.
   Ein Browser öffnet sich mit den Stellen, die Sie wählen dürfen.
3. Öffnen Sie das gewünschte übergeordnete Element, indem Sie auf seinen Namen klicken, und wählen Sie es dann mit
   seinem Optionsfeld aus.
4. Klicken Sie auf **Auswählen**.
5. Wenn die Website fragt, welche Versionen kopiert werden sollen, wählen Sie **Aktuelle Version kopieren** oder **Alle
   Versionen kopieren** und klicken Sie auf **Kopieren**. Bei **Teil des Baums kopieren** wählen Sie, ob die Kopien
   ihre Ersteller und Daten behalten oder Sie und die aktuelle Zeit erhalten, und klicken Sie dann auf **Kopieren**.
   Das Element öffnet sich an seinem neuen Ort.
6. Um mehrere Elemente zu verschieben oder zu kopieren, haken Sie sie in der Liste **Unterelemente** an und wählen Sie
   **Weitere Aktionen** > **Ausgewähltes verschieben** oder **Auswahl kopieren**.

## Was schiefgehen kann

- **Das gewünschte übergeordnete Element lässt sich nicht auswählen.** Es ist kein Container, oder Ihre Rolle darf
  diese Klasse dort nicht anlegen.
- **Das verschobene Element hat jetzt andere Leser.** Ein Element übernimmt beim Verschieben die Sektion seines neuen
  übergeordneten Elements. Inhalte, die in eine Sektion verschoben werden, die Besucher nicht lesen dürfen,
  verschwinden von der Website; Inhalte, die aus einer geschützten Sektion herausgeschoben werden, können öffentlich
  werden. Prüfen Sie nach dem Verschieben den Tab **Details**.
- **Für eine große Kopie oder Verschiebung wird der Hintergrund angeboten.** Bei einem großen Teilbaum lässt die Seite
  Sie wählen, ob es sofort oder als Inhaltsauftrag laufen soll, der in Portionen arbeitet und seinen Fortschritt zeigt.
  Lassen Sie ihn fertig laufen, bevor Sie die Elemente bearbeiten.
- **Teil des Baums kopieren wird abgelehnt.** Eine Website kann begrenzen, wie viele Elemente eine einzelne
  Teilbaumkopie umfassen darf. Kopieren Sie kleinere Zweige, oder fragen Sie einen Administrator.
- **Die Kopie ist nicht dasselbe Element.** Sie hat ihre eigene Objekt-ID und Knoten-ID, ihre eigenen Versionen, und
  Änderungen an der einen erreichen die andere nicht. Der Tab **Details** zeigt beide Nummern.

## Verwandte Seiten

- [Inhalte erstellen und bearbeiten](../inhalte-bearbeiten.md#verschieben-kopieren-und-entfernen)
- [Wie zeige ich ein Element an mehr als einer Stelle?](wie-zeige-ich-ein-element-an-mehr-als-einer-stelle.md)
- [Wie tausche ich zwei Elemente im Baum?](wie-tausche-ich-zwei-elemente-im-baum.md)
- [Objekte, Knoten und Orte](../../../eng-us/help/cms-developers/objects-nodes-and-locations.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
