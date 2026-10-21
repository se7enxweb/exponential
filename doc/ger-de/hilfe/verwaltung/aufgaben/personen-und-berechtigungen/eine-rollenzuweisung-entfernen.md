---
title: Eine Rollenzuweisung entfernen
audience: government, admin
category: public-sector
order: 190
summary: Schritt für Schritt - einem Benutzer oder einer Gruppe auf der Seite der Rolle mit Ausgewählte entfernen eine Rolle wegnehmen, Zuweisungen aufräumen, deren Benutzer oder Gruppe nicht mehr existiert, und prüfen, dass der Zugang weg ist.
keywords: Rolle entfernen, Rollenzuweisung, Ausgewählte entfernen, Zugang entziehen, Rechte wegnehmen, entfernter Benutzer, verwaiste Zuweisung, Überprüfung des Zugangs, remove role, role assignment, Remove selected, revoke access, take away rights, removed user, orphaned assignment, access review
type: help
---
# Eine Rollenzuweisung entfernen

Diese Aufgabe ist für Administratoren. Am Ende hat ein Benutzer oder eine Gruppe eine Rolle nicht mehr. Die Benutzer und
Gruppen werden nicht entfernt; sie verlieren nur die Rolle
([Anleitung zu Rollen, Abschnitt 5](../../../../anleitungen/rollen-und-richtlinien.md#5-die-rolle-zuweisen)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Zuweisungen entfernter Benutzer oder Gruppen](#zuweisungen-entfernter-benutzer-oder-gruppen)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Konto mit dem Modul `role`.
- Wissen Sie, ob die Person die Rolle direkt oder über eine Gruppe erhalten hat: Die Zeile des Kontos unter
  **Benutzer > Benutzerkonten prüfen** listet seine eigenen und geerbten Rollen. Um eine Rolle wegzunehmen, die eine
  Gruppe gibt, nehmen Sie die Person aus der Gruppe heraus, statt die Zuweisung der Gruppe zu entfernen.

## Schritte

1. **Benutzer > Rollen und Richtlinien**, öffnen Sie die Rolle.
2. Finden Sie unter „Benutzer und Gruppen, die die Rolle <Rolle> benutzen“ die Zuweisung; bei vielen Zuweisungen
   verwenden Sie den Namensfilter.
3. Markieren Sie die Zuweisung (ein Benutzer oder eine Gruppe, mit ihrer Einschränkung).
4. Öffnen Sie die Bestätigung und klicken Sie auf **Angekreuzte Zuweisungen entfernen**.
5. Die Benutzer und Gruppen verlieren die Rolle sofort.

Jede Aktion auf den Seiten der Rollen braucht das Formulartoken der Seite; eine Anfrage ohne es wird mit 403 abgelehnt.

## Zuweisungen entfernter Benutzer oder Gruppen

Eine Zuweisung, deren Benutzer oder Gruppe entfernt wurde, wird auf der Seite der Rolle zuerst gelistet und lässt sich
auf dieselbe Weise entfernen. Entfernen Sie sie bei Überprüfungen des Zugangs; sie sind in jedem Audit Rauschen.

## Prüfen, ob es funktioniert hat

- Die Seite der Rolle listet die Zuweisung nicht mehr; **Betroffene Benutzer insgesamt** ist gesunken.
- Die Seite des Kontos (`/user/edit/<id>`) listet die Rolle nicht mehr.
- Die Änderung wird im Kanal `access` des Audit-Protokolls aufgezeichnet.

Änderungsnachweis: Rolle, Benutzer oder Gruppe, Einschränkung, Datum, Grund.

## Verwandte Seiten
- [Prüfen, wer Administratorrechte hat](pruefen-wer-administratorrechte-hat.md)
- [Einen Mitarbeiter in eine andere Abteilung verschieben](einen-mitarbeiter-in-eine-andere-abteilung-verschieben.md)
- [Überprüfungen des Administratorzugangs](../../../../../eng-us/help/government/operations/people-and-permissions/admin-access-reviews.md)
- [Seitenweise Anzeige der Rollenzuweisungen](../../../../../eng-us/features/6.0/role-assignment-paging.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
