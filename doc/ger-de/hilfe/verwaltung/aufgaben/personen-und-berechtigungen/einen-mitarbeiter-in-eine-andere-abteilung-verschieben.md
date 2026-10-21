---
title: Einen Mitarbeiter in eine andere Abteilung verschieben
audience: government, admin
category: public-sector
order: 70
summary: Schritt für Schritt - das Konto einer Kollegin oder eines Kollegen von der Benutzergruppe einer Abteilung in eine andere verschieben, sodass die Rechte der alten Abteilung enden und die der neuen beginnen, einschließlich Freigabeaufgaben und Übergabe der Inhalte.
keywords: Wechsel, Abteilungswechsel, Versetzung, Benutzer verschieben, Benutzergruppe, Orte, Orte hinzufügen, Als Haupt setzen, Rollen, Änderung des Zugangs, mover, department change, transfer, move user, user group, locations, Add locations, Set main, roles, access change
type: help
---
# Einen Mitarbeiter in eine andere Abteilung verschieben

Diese Aufgabe ist für Administratoren und Mitarbeiter des Service Desks. Am Ende hat eine Kollegin oder ein Kollege, die
die Abteilung gewechselt haben, die Rechte der neuen Abteilung und keine der alten. Der Zugang folgt den Benutzergruppen,
also ist der Wechsel eine Änderung des Orts des Kontos. Hintergrund:
[Eintritte, Wechsel und Austritte](../../../../../eng-us/help/government/operations/people-and-permissions/joiners-movers-leavers.md#movers).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Das Konto verschieben](#das-konto-verschieben)
3. [Aufgaben, die nicht den Gruppen folgen](#aufgaben-die-nicht-den-gruppen-folgen)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Der Antrag mit alter und neuer Abteilung und dem Datum.
- Rechte, Benutzerkonten zu bearbeiten und zu verschieben (Administratoren haben sie).
- Optional: Schalten Sie die Audit-Ereignisse für Änderungen an Orten ein, damit der Wechsel vollständig aufgezeichnet
  wird ([Sicherheit und Audit, Schritt 3](../../../../anleitungen/sicherheit-und-audit.md#3-das-audit-protokoll-lesen-5-minuten)):

  ```ini
  # settings/override/audit.ini.append.php
  [AuditEventSettings]
  Enabled[]=content.node.add
  Enabled[]=content.node.main
  ```

## Das Konto verschieben

Verschieben Sie entweder den Knoten des Kontos direkt (Kontextmenü, Verschieben, die neue Gruppe wählen) oder arbeiten
Sie mit Orten:

1. Öffnen Sie das Konto im Baum **Benutzer** und seinen Reiter **Orte**.
2. **Orte hinzufügen**: Wählen Sie die Gruppe der neuen Abteilung.
3. Markieren Sie den neuen Ort und klicken Sie auf **Als Haupt setzen**.
4. Markieren Sie den alten Ort und klicken Sie auf **Ausgewählte entfernen**.

Das Konto hat jetzt nur die Rollen der neuen Gruppe (dazu die Rollen der Gruppen darüber und direkt zugewiesene Rollen).

## Aufgaben, die nicht den Gruppen folgen

| Aufgabe | Wo sie zu ändern ist |
|---|---|
| Freigebender der alten Abteilung | aus deren Gruppe der Freigebenden entfernen; **Benutzer, die den Inhalt bewilligen** in den Ereignissen Approve prüfen (**Setup > Workflows**) |
| Dem Konto direkt zugewiesene Rollen | jede Seite einer Rolle; markieren und **Ausgewählte entfernen** |
| Inhalte im Besitz des Kontos, wenn Rollen `Owner = Self` verwenden | **Change owner** für die Inhalte der alten Abteilung ([Eigentümerschaft von Inhalten](../../../../../eng-us/help/government/operations/editorial-workflows/content-ownership.md)) |
| Entwürfe in der alten Abteilung | die Person veröffentlicht oder entfernt sie oder übergibt sie |

## Prüfen, ob es funktioniert hat

- Die Kontoseite (`/user/edit/<id>`) listet die neue Gruppe und ihre Rollen, nicht die alten.
- Als die Person angemeldet (oder auf ihre Prüfung hin) bieten die Seiten der alten Abteilung kein **Bearbeiten** mehr
  an.
- **Benutzer > Benutzerkonten prüfen** zeigt die neue Gruppe in der Zeile des Kontos.

Änderungsnachweis: alte und neue Gruppe, Datum, Referenz des Antrags.

## Verwandte Seiten
- [Einen neuen Mitarbeiter hinzufügen](einen-neuen-mitarbeiter-hinzufuegen.md)
- [Eine Rollenzuweisung entfernen](eine-rollenzuweisung-entfernen.md)
- [Einem Freigebenden Rechte geben](einem-freigebenden-rechte-geben.md)
- [Rollengestaltung: Redakteure der Abteilungen](../../../../../eng-us/help/government/operations/people-and-permissions/role-design-department-editors.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
