---
title: Wie gebe ich einer Gruppe Bearbeitungsrechte nur für eine Sektion?
audience: admin
category: manual
order: 500
summary: Legen Sie eine Rolle mit den Richtlinien content/read, content/create und content/edit an und weisen Sie sie der Benutzergruppe mit der Einschränkung Sektion zu, sodass die Rechte nur für die Inhalte dieser Sektion gelten.
keywords: Rollen, Richtlinien, Berechtigungen, Sektion, mit Einschränkungen zuordnen, Benutzergruppe, Bearbeitungsrechte, content/edit, role/assign, roles, policies, permissions, section, assign with limitation, user group, edit rights
type: help
---
# Wie gebe ich einer Gruppe Bearbeitungsrechte nur für eine Sektion?

Rechte kommen aus Rollen: Eine Rolle ist eine Liste von Richtlinien (was getan werden darf), und die Zuordnung zu einer
Benutzergruppe mit einer Einschränkung entscheidet, wo. Die Zuordnung mit der Einschränkung **Sektion** hält die Rechte
innerhalb einer Sektion.

## Wer das tun kann

Benutzer, deren Rolle `role/*` hat (Rollen anlegen, bearbeiten und zuordnen), meist nur Administratoren.

## Schritte

1. Öffnen Sie im oberen Menü **Benutzer** und wählen Sie **Rollen und Richtlinien**.
2. Klicken Sie auf **Neue Rolle**, geben Sie ihr einen Namen (zum Beispiel „News-Redakteure“) und speichern Sie.
3. Klicken Sie auf **Neue Richtlinie** und fügen Sie nacheinander hinzu:
   - Modul **content**, Funktion **read**;
   - Modul **content**, Funktion **create**;
   - Modul **content**, Funktion **edit**;
   - Modul **content**, Funktion **versionread**, damit die Gruppe Entwürfe und Versionen sehen kann.

   Wählen Sie jeweils **Vollen Zugriff gewähren** (die Beschränkung auf die Sektion kommt aus der Zuordnung).
4. Klicken Sie auf **Speichern**.
5. Klicken Sie auf der Seite der Rolle auf **Mit Einschränkungen zuordnen**, wählen Sie **Sektion**, wählen Sie die
   Sektion (zum Beispiel „News“) und klicken Sie auf **Auswählen**.
6. Wählen Sie die Benutzergruppe und bestätigen Sie.
7. Melden Sie sich als Testmitglied der Gruppe an und prüfen Sie, dass es in der Sektion bearbeiten kann und nirgendwo
   sonst.

## Was schiefgehen kann

- **Die Gruppe kann immer noch nicht bearbeiten.** Sie muss auch den Ort lesen dürfen, an dem sie arbeitet, und für neue
  Inhalte die Klassen, die sie anlegt. Fügen Sie `content/read` für den Baum darüber hinzu, oder beschränken Sie
  `content/create` in der Richtlinie nach **Klasse**.
- **Die Gruppe kann zu viel bearbeiten.** Eine andere ihrer Rollen gibt weitergehende Rechte. Rechte addieren sich:
  Prüfen Sie jede Rolle, die dem Benutzer und den Gruppen über ihm zugeordnet ist.
- **Änderungen wirken nicht sofort.** Rechte werden pro Benutzer im Cache gehalten. Sie gelten bei der nächsten
  Anmeldung oder nachdem der Benutzer-Cache geleert wurde.

## Verwandte Seiten

- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)
- [Sektionen](../../anleitungen/sektionen.md)
- [Rollen für Ihr Team](../kunden/alltag/rollen-fuer-ihr-team.md)
- [Rollen für neue Mitarbeiter wählen](../kunden/alltag/rollen-fuer-neue-mitarbeitende-waehlen.md)
- [Benutzerhandbuch](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
