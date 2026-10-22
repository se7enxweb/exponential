---
title: Wie verschiebe ich einen Benutzer in eine andere Gruppe oder nehme ihn in zwei Gruppen auf?
audience: admin
category: manual
order: 615
summary: Verschieben Sie das Benutzerkonto wie jedes andere Element, um seine Gruppe zu ändern; fügen Sie in einer zweiten Gruppe einen Ort hinzu, um dem Benutzer die Rollen beider Gruppen zugleich zu geben.
keywords: Benutzer verschieben, Benutzergruppe, Gruppe wechseln, zweite Gruppe, zwei Gruppen, Ort, Orte hinzufügen, Rollen, Rechte, Tab Benutzer, move user, user group, change group, second group, two groups, location, add locations, roles, permissions, Users tab
context: content/view
type: help
---
# Wie verschiebe ich einen Benutzer in eine andere Gruppe oder nehme ihn in zwei Gruppen auf?

Ein Benutzerkonto liegt im Benutzerbaum wie jedes andere Element. Seine Gruppen sind die Gruppen über jedem seiner
Orte, und der Benutzer hat jede Rolle jeder dieser Gruppen. Das Verschieben des Kontos ändert seine Gruppe; ein
zusätzlicher Ort fügt eine Gruppe hinzu, ohne eine wegzunehmen.

## Wer das tun kann

- Verschieben erfordert `content/edit` und `content/remove` für das Konto sowie `content/create` der Klasse User in
  der Zielgruppe.
- Einen Ort hinzufügen erfordert `content/edit` oder `content/manage_locations` für das Konto sowie `content/create`
  der Klasse User in der neuen Gruppe.
- Administratoren dürfen beides.

## Schritte

So verschieben Sie das Konto in eine andere Gruppe:

1. Klicken Sie im oberen Menü auf **Benutzer** und öffnen Sie das Konto.
2. Klicken Sie auf **Verschieben**.
   Ein Browser mit dem Benutzerbaum öffnet sich.
3. Wählen Sie die neue Gruppe mit ihrem Optionsfeld und klicken Sie auf **Auswählen**.
   Das Konto wird jetzt in der neuen Gruppe aufgeführt.
4. Öffnen Sie den Tab **Rollen** des Kontos und prüfen Sie die Rollen, die es jetzt hat.

So nehmen Sie das Konto zusätzlich in eine zweite Gruppe auf:

5. Öffnen Sie das Konto und klicken Sie auf den Tab **Orte**.
6. Klicken Sie auf **Orte hinzufügen**, wählen Sie die zweite Gruppe und klicken Sie auf **Auswählen**.
   Das Konto wird in beiden Gruppen aufgeführt und hat die Rollen beider.

## Was schiefgehen kann

- **Der Benutzer hat immer noch die alten Rechte.** Rechte werden pro Benutzer zwischengespeichert. Sie gelten ab der
  nächsten Anmeldung oder nachdem der Benutzer-Cache geleert wurde.
- **Der Benutzer hat mehr Rechte als erwartet.** Rechte summieren sich: Jede Rolle jeder Gruppe über jedem Ort zählt,
  und die großzügigste Richtlinie gewinnt. Sehen Sie sich den Tab **Richtlinien** des Kontos an, um alle zusammen zu
  sehen.
- **Das Entfernen des Kontos aus einer Gruppe hat es überall entfernt.** Nehmen Sie eine Gruppe weg, indem Sie im Tab
  **Orte** diesen einen Ort entfernen, nicht das Konto. Wenn Sie das Konto selbst entfernen, entfernen Sie den
  Benutzer.
- **Die Sektion des Kontos hat sich geändert.** Beim Verschieben von Inhalten kann die Sektion des neuen übergeordneten
  Elements mitgenommen werden. Benutzerkonten bleiben normalerweise in der Sektion Users, und das ist so gewollt.

## Verwandte Seiten

- [Wie sehe ich, was ein Benutzer tun darf?](wie-sehe-ich-was-ein-benutzer-tun-darf.md)
- [Wie lege ich einen Benutzer oder eine Benutzergruppe an?](wie-lege-ich-einen-benutzer-oder-eine-benutzergruppe-an.md)
- [Wie zeige ich ein Element an mehr als einer Stelle?](wie-zeige-ich-ein-element-an-mehr-als-einer-stelle.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
