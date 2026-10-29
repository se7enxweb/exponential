---
title: Wie sehe ich, was ein Benutzer tun darf?
audience: admin
category: manual
order: 620
summary: Öffnen Sie das Konto im Tab Benutzer und lesen Sie seinen Tab Rollen (jede Rolle des Benutzers und seiner Gruppen, mit der Einschränkung jeder Zuweisung) und seinen Tab Richtlinien (was diese Rollen erlauben, Modul für Modul).
keywords: Rechte, Berechtigungen, was darf ein Benutzer, Tab Rollen, Tab Richtlinien, zugewiesene Rollen, Einschränkung, Zugriff verweigert, effektive Rechte, Benutzergruppe, permissions, what may a user do, roles tab, policies tab, assigned roles, limitation, access denied, effective rights, role/view, user group
context: content/view
type: help
---
# Wie sehe ich, was ein Benutzer tun darf?

Ein Benutzer darf tun, was die Richtlinien seiner Rollen erlauben, und sonst nichts. Die Rollen stammen vom Konto
selbst und von jeder Gruppe über jedem seiner Orte. Die Seite des Kontos in der Administration trägt sie für Sie
zusammen.

## Wer das tun kann

Jeder, der das Konto im Benutzerbaum lesen darf. Um einer Rolle auf ihre eigene Seite zu folgen, braucht man
`role/*`.

## Schritte

1. Klicken Sie im oberen Menü auf **Benutzer** und öffnen Sie das Konto (oder eine Benutzergruppe).
2. Klicken Sie auf den Tab **Rollen**.
   Der Tab listet jede Rolle auf, die das Konto hat, direkt oder über eine Gruppe. Die Spalte **Einschränkung** zeigt,
   wenn eine Rolle nur für einen Teilbaum oder eine Sektion zugewiesen wurde, mit einem Link dorthin.
3. Klicken Sie auf den Tab **Richtlinien**.
   Der Tab listet Rolle für Rolle das Modul, die Funktion und die Einschränkungen jeder Richtlinie auf. Bei einer
   Rolle mit vielen Richtlinien zeigt er die ersten und verlinkt für den Rest auf die Rolle.
4. Um eine ganze Rolle zu lesen, klicken Sie auf ihren Namen.
   Die Seite der Rolle zeigt alle ihre Richtlinien und jeden Benutzer und jede Gruppe, der sie zugewiesen ist.
5. Zum Vergleich öffnen Sie einen Benutzer, der die Aufgabe erledigen kann, und sehen Sie sich dieselben Tabs an.

## Was schiefgehen kann

- **Ein Tab fehlt.** Die Tabs lassen sich mit dem Schalter **Reiter** über ihnen ausschalten; schalten Sie sie wieder
  ein.
- **Der Benutzer wird abgewiesen, obwohl eine Richtlinie es zu erlauben scheint.** Prüfen Sie jede Einschränkung der
  Richtlinie: Alle müssen zugleich erfüllt sein (zum Beispiel Class *und* Section). Prüfen Sie auch die Einschränkung
  der Zuweisung im Tab **Rollen** und ob die Inhalte in dem Teilbaum oder der Sektion liegen, die sie nennt.
- **Eine Richtlinie ist als zugriffslos markiert.** Ihre Einschränkung wird von einer Erweiterung behandelt, die nicht
  aktiv ist, daher verweigert die Richtlinie. Aktivieren Sie die Erweiterung oder entfernen Sie die Richtlinie.
- **Die Rechte haben sich geändert, aber der Benutzer merkt nichts davon.** Rechte werden pro Benutzer
  zwischengespeichert. Sie gelten ab der nächsten Anmeldung oder nachdem der Benutzer-Cache geleert wurde.
- **Die Anmeldung wird auf einer Website abgewiesen.** Die Anmeldung erfordert `user/login` für diesen Siteaccess, eine
  Richtlinie wie jede andere, die im Tab **Richtlinien** mit einer Einschränkung SiteAccess erscheint.

## Verwandte Seiten

- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)
- [Was die Redakteursrolle erlaubt](../was-die-redakteursrolle-erlaubt.md)
- [Wie gebe ich einer Gruppe Bearbeitungsrechte nur für eine Sektion?](wie-gebe-ich-einer-gruppe-bearbeitungsrechte-fuer-eine-sektion.md)
- [Wie verschiebe ich einen Benutzer in eine andere Gruppe oder nehme ihn in zwei Gruppen auf?](wie-verschiebe-ich-einen-benutzer-in-eine-andere-gruppe.md)
- [Sektionen, Rollen und Richtlinien](../../../eng-us/help/cms-developers/sections-roles-and-policies.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
