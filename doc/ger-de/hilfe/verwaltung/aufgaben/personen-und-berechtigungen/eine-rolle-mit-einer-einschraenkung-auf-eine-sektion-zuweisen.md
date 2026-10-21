---
title: Eine Rolle mit einer Einschränkung auf eine Sektion zuweisen
audience: government, admin
category: public-sector
order: 100
summary: Schritt für Schritt - einer Benutzergruppe eine bestehende Rolle nur für die Inhalte einer Sektion geben (zum Beispiel einer internen oder eingeschränkten Sektion), mit Mit Einschränkungen zuordnen, und das Ergebnis prüfen.
keywords: Rolle zuweisen, Einschränkung auf eine Sektion, Mit Einschränkungen zuordnen, Sektion, eingeschränkte Sektion, Benutzergruppe, User_Section, Rollenzuweisung, assign role, section limitation, Assign with limitation, section, restricted section, user group, role assignment
type: help
---
# Eine Rolle mit einer Einschränkung auf eine Sektion zuweisen

Diese Aufgabe ist für Administratoren. Am Ende hat eine Benutzergruppe eine Rolle, deren Richtlinien nur für Objekte in
einer Sektion gelten. Der Mechanismus ist in
[Sektionen, Abschnitt 9.2](../../../../anleitungen/sektionen.md#92-eine-mit-der-einschränkung-auf-eine-sektion-zugewiesene-rolle) beschrieben.

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Wann eine Sektion, wann ein Teilbaum](#wann-eine-sektion-wann-ein-teilbaum)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Rolle existiert, und ihre Richtlinien ergeben ohne eigene Einschränkung Sinn (zum Beispiel „Department editor“).
- Die Sektion existiert (**Setup > Sektionen**) und enthält die Inhalte
  ([Eine Sektion für eingeschränkte Inhalte anlegen](eine-sektion-fuer-eingeschraenkte-inhalte-anlegen.md)).
- Die Benutzergruppe existiert.
- Ein Konto mit dem Modul `role`.

## Schritte

1. Öffnen Sie **Benutzer > Rollen und Richtlinien** und klicken Sie auf die Rolle.
2. Wählen Sie unter der Liste der Benutzer und Gruppen in der Auswahl der Einschränkung **Sektion**.
3. Klicken Sie auf **Mit Einschränkungen zuordnen**.
4. Wählen Sie die Sektion und fahren Sie fort.
5. Wählen Sie im Inhaltsbrowser die Benutzergruppe (oder Benutzer) und klicken Sie auf **Auswählen**.
6. Die Seite der Rolle listet die Gruppe mit *Nur in der Sektion ...*.

Jede Richtlinie der Rolle gilt jetzt nur für Objekte in dieser Sektion (intern wird eine Einschränkung `User_Section`
hinzugefügt).

## Prüfen, ob es funktioniert hat

- Die Seite der Sektion (**Setup > Sektionen**, auf die Sektion klicken) listet die Zuweisung unter „Benutzer und Gruppen
  mit Rollenlimitierungen verknüpft innerhalb dieser Sektion“.
- Melden Sie sich als Mitglied an: Inhalte in der Sektion lassen sich bearbeiten, Inhalte anderswo nicht (sofern keine
  andere Rolle es erlaubt).
- Die Sektion zählt jetzt als von Rollen verwendet und lässt sich nicht entfernen, solange die Zuweisung besteht.

## Wann eine Sektion, wann ein Teilbaum

| Einschränkung auf eine Sektion verwenden | Einschränkung auf einen Teilbaum verwenden |
|---|---|
| die Inhalte sind im Baum verstreut, gehören aber zusammen (interne Dokumente, ein Mitgliederbereich) | die Inhalte liegen unter einem Ordner (eine Abteilung) |
| Objekte mit mehreren Orten müssen überall abgedeckt sein | nur eine Stelle zählt |

Siehe [Sektionen und Einschränkungen auf Teilbäume](../../../../../eng-us/help/government/operations/people-and-permissions/sections-and-subtree-limits.md).

## Verwandte Seiten
- [Eine Rolle mit einer Einschränkung auf einen Teilbaum zuweisen](eine-rolle-mit-einer-einschraenkung-auf-einen-teilbaum-zuweisen.md)
- [Eine Rollenzuweisung entfernen](eine-rollenzuweisung-entfernen.md)
- [Rollen, Richtlinien und Einschränkungen](../../../../../eng-us/help/government/operations/people-and-permissions/roles-policies-and-limitations.md)
- [Anleitung zu den Sektionen](../../../../anleitungen/sektionen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
