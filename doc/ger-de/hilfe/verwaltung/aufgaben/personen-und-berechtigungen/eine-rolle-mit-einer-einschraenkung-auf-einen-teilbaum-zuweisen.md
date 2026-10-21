---
title: Eine Rolle mit einer Einschränkung auf einen Teilbaum zuweisen
audience: government, admin
category: public-sector
order: 110
summary: Schritt für Schritt - einer Benutzergruppe eine bestehende Rolle nur unterhalb eines Ordners des Inhaltsbaums geben, mit Mit Einschränkungen zuordnen und Teilbaum, und prüfen, dass die Grenze hält.
keywords: Rolle zuweisen, Einschränkung auf einen Teilbaum, Mit Einschränkungen zuordnen, Teilbaum, Ordner, Abteilung, Benutzergruppe, Rollenzuweisung, Grenze, assign role, subtree limitation, Assign with limitation, subtree, folder, department, user group, role assignment, boundary
type: help
---
# Eine Rolle mit einer Einschränkung auf einen Teilbaum zuweisen

Diese Aufgabe ist für Administratoren. Am Ende hat eine Benutzergruppe eine Rolle, die nur an und unterhalb eines Orts
des Inhaltsbaums gilt, der übliche Weg, einer Abteilung einen eigenen Bereich zu geben
([Anleitung zu Rollen, Abschnitt 5](../../../../anleitungen/rollen-und-richtlinien.md#5-die-rolle-zuweisen)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Fallstricke](#fallstricke)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Rolle existiert ([Eine Redakteursrolle einer Abteilung für einen Teilbaum anlegen](eine-redakteursrolle-einer-abteilung-fuer-einen-teilbaum-anlegen.md)).
- Der Ordner und die Benutzergruppe existieren.
- Ein Konto mit dem Modul `role`; mit eingeschaltetem Schutz vor Rechteausweitung muss die Rolle innerhalb Ihres eigenen
  Zugangs liegen.

## Schritte

1. **Benutzer > Rollen und Richtlinien**, klicken Sie auf die Rolle.
2. Wählen Sie unter den Zuweisungen in der Auswahl der Einschränkung **Teilbaum**.
3. Klicken Sie auf **Mit Einschränkungen zuordnen**.
4. Gehen Sie im Inhaltsbrowser zum Ordner, wählen Sie ihn mit seinem Optionsfeld und klicken Sie auf **Auswählen**.
5. Wählen Sie die Benutzergruppe und klicken Sie auf **Auswählen**.
6. Die Seite der Rolle listet die Gruppe mit *Nur im Teilbaum ...*, und die Zeile unter den Zahlen zählt
   eingeschränkte Zuweisungen.

## Prüfen, ob es funktioniert hat

1. Melden Sie sich als Mitglied in einem privaten Browserfenster an.
2. Im Ordner und darunter: **Bearbeiten** wird angeboten (wenn die Rolle das Bearbeiten erlaubt).
3. Außerhalb: nicht angeboten; eine eingetippte Adresse zum Bearbeiten wird abgelehnt und als
   `access.permission.refused` aufgezeichnet.

## Fallstricke

- **Zuweisen** ohne Einschränkung gibt die Rolle für die ganze Site; wenn Sie es versehentlich gedrückt haben, markieren
  Sie die neue Zuweisung sofort und wählen Sie **Ausgewählte entfernen**
  ([Eine Rollenzuweisung entfernen](eine-rollenzuweisung-entfernen.md)).
- Eine Einschränkung auf einen Teilbaum folgt dem Baum: Herausverschobene Inhalte verlassen sofort die Reichweite der
  Gruppe.
- Richtlinien mit eigener Einschränkung auf einen Teilbaum kombinieren sich mit der der Zuweisung; beide müssen erfüllt
  sein.
- Die Richtlinie `user/login` sollte nicht von einer Zuweisung auf einen Teilbaum abhängen; halten Sie sie in einer
  Anmelderolle für Mitarbeiter, die ohne Einschränkung zugewiesen ist.

## Verwandte Seiten
- [Eine Rolle mit einer Einschränkung auf eine Sektion zuweisen](eine-rolle-mit-einer-einschraenkung-auf-eine-sektion-zuweisen.md)
- [Einen Auftragnehmer nur einen Ordner bearbeiten lassen](einen-auftragnehmer-nur-einen-ordner-bearbeiten-lassen.md)
- [Sektionen und Einschränkungen auf Teilbäume](../../../../../eng-us/help/government/operations/people-and-permissions/sections-and-subtree-limits.md)
- [Seitenweise Anzeige der Rollenzuweisungen](../../../../../eng-us/features/6.0/role-assignment-paging.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
