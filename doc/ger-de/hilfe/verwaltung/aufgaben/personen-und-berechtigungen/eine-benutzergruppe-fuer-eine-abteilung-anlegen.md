---
title: Eine Benutzergruppe für eine Abteilung anlegen
audience: government, admin
category: public-sector
order: 170
summary: Schritt für Schritt - im Benutzerbaum eine Benutzergruppe für eine Abteilung oder ein Team anlegen, ihr ihre Rollen mit Einschränkungen geben und Mitglieder hinzufügen, sodass der Zugang je Gruppe statt je Person verwaltet wird.
keywords: Benutzergruppe, Abteilung, Team, Benutzerbaum, Gruppe anlegen, Rollen, Mitglieder, Zugang nach Gruppe, Organisationsstruktur, user group, department, team, Users tree, create group, roles, members, access by group, organization structure
type: help
---
# Eine Benutzergruppe für eine Abteilung anlegen

Diese Aufgabe ist für Administratoren. Am Ende hat eine Abteilung eine Benutzergruppe, die ihre Rollen trägt; um den
Zugang einer Person zu ändern, genügt es dann, sie hinzuzufügen oder zu entfernen. Hintergrund:
[Rollen, Richtlinien und Einschränkungen](../../../../../eng-us/help/government/operations/people-and-permissions/roles-policies-and-limitations.md#assigning-roles).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Struktur planen](#die-struktur-planen)
3. [Die Gruppe anlegen](#die-gruppe-anlegen)
4. [Ihr Rollen geben](#ihr-rollen-geben)
5. [Mitglieder hinzufügen](#mitglieder-hinzufügen)
6. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
7. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Administratorkonto (das Anlegen von Benutzergruppen braucht `content/create` der Klasse für Benutzergruppen unter
  Benutzer).
- Der Name der Abteilung, wie er den Mitarbeitern erscheinen soll.

## Die Struktur planen

Rollen, die einer Gruppe zugewiesen sind, erreichen auch die Mitglieder der Gruppen darunter. Eine einfache Struktur für
eine Behörde:

```
Users
  Staff                       (role: Staff sign-in)
    Web team                  (role: Web team)
    Departments
      Department of Example   (role: Department editor, subtree /services/example/)
      Office of Placeholder   (role: Department editor, subtree /services/placeholder/)
    Approvers - Example       (role: Approver, subtree /services/example/)
  Contractors
    Contract 2026-07          (role: Contractor - content, subtree of one folder)
```

Halten Sie die Konten von Besuchern und Mitgliedern in anderen Gruppen als die der Mitarbeiter.

## Die Gruppe anlegen

1. Öffnen Sie in der Administration **Benutzer** und die übergeordnete Gruppe (zum Beispiel „Departments“).
2. Legen Sie dort ein neues Element der Klasse „User group“ an.
3. Benennen Sie es nach der Abteilung; veröffentlichen Sie.

## Ihr Rollen geben

Auf jeder Seite einer Rolle: **Zuweisen** (oder **Mit Einschränkungen zuordnen** mit dem Teilbaum der Abteilung) und
wählen Sie die neue Gruppe ([Eine Rolle mit einer Einschränkung auf einen Teilbaum zuweisen](eine-rolle-mit-einer-einschraenkung-auf-einen-teilbaum-zuweisen.md)).

## Mitglieder hinzufügen

- Neue Mitarbeiter: Legen Sie ihr Konto innerhalb der Gruppe an ([Einen neuen Mitarbeiter hinzufügen](einen-neuen-mitarbeiter-hinzufuegen.md)).
- Bestehende Mitarbeiter: Fügen Sie einen Ort in der Gruppe hinzu (Reiter **Orte** des Kontos, **Orte hinzufügen**)
  oder verschieben Sie das Konto
  ([Einen Mitarbeiter in eine andere Abteilung verschieben](einen-mitarbeiter-in-eine-andere-abteilung-verschieben.md)).

## Prüfen, ob es funktioniert hat

- Auf jeder Seite einer Rolle ist die Gruppe mit ihrer Einschränkung gelistet.
- **Benutzer > Benutzerkonten prüfen**, **Benutzergruppe** = die neue Gruppe: Die Mitglieder werden mit den Rollen der
  Gruppe gelistet.

## Verwandte Seiten
- [Eine Redakteursrolle einer Abteilung für einen Teilbaum anlegen](eine-redakteursrolle-einer-abteilung-fuer-einen-teilbaum-anlegen.md)
- [Eintritte, Wechsel und Austritte](../../../../../eng-us/help/government/operations/people-and-permissions/joiners-movers-leavers.md)
- [Anleitung zu Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)
- [Kurs für Site-Administratoren](../../../trainer/website-administratoren/README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
