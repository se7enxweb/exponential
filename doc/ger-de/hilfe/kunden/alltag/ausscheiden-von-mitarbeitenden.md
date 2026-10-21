---
title: Ausscheiden von Mitarbeitenden
audience: customer, admin, project-manager
category: customer-success
order: 7
summary: Was am letzten Arbeitstag eines Redakteurs, Freiberuflers oder einer Agentur auf einer Exponential-Website zu tun ist - deaktivieren statt entfernen, Entwürfe und Freigaben übergeben, API-Schlüssel und Tokens widerrufen und das Ergebnis prüfen - mit einem Protokoll zum Aufbewahren.
keywords: Ausscheiden, Offboarding, Konto deaktivieren, deaktivieren und verschieben, Kontenpflege, API-Schlüssel, Tokens, Entwürfe, Freigaben, Zugriff entziehen, offboarding, leaver, disable account, deactivate and move, user hygiene, API keys, tokens, drafts, approvals, access removal
type: help
---
# Ausscheiden von Mitarbeitenden

Diese Seite ist für die Leitung und die Person, die Konten verwaltet, wenn jemand das Team verlässt, der Vertrag eines
Freiberuflers endet oder eine Agentur die Arbeit zurückgibt. Der Zugriff sollte am letzten Arbeitstag enden, nicht bei
der nächsten jährlichen Überprüfung ([Zugriffsüberprüfungen](../../unternehmen/sicherheit/zugriffspruefungen.md)). In
Exponential ist der sichere Weg, das Konto zu **deaktivieren**, statt es zu entfernen.

## Inhalt

1. [Deaktivieren, nicht entfernen](#deaktivieren-nicht-entfernen)
2. [Vor dem letzten Tag](#vor-dem-letzten-tag)
3. [Am letzten Tag](#am-letzten-tag)
4. [Nach dem letzten Tag](#nach-dem-letzten-tag)
5. [Protokoll](#protokoll)

## Deaktivieren, nicht entfernen

- Ein Benutzerobjekt kommt nie in den Papierkorb: Das Entfernen eines Benutzers ist endgültig
  ([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)).
- **Deaktivieren** unter **Benutzer > Benutzerkonten prüfen** verhindert, dass sich das Konto anmeldet, und beendet
  seine Sitzungen. Seine Inhalte, Gruppen und Rollen bleiben erhalten, sodass die Historie, wer was geschrieben hat,
  bewahrt wird.
- Wenn Ihr Administrator **Deaktivieren und verschieben** eingeschaltet hat (`site.ini [UserSettings]
  DeactivateAndMove=enabled`, standardmäßig aus), wird das Konto deaktiviert und in eine verborgene Gruppe ohne Rollen
  verschoben; **Reaktivieren und zurückverschieben** macht das rückgängig ([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)).

Jede Änderung wird im Prüfprotokoll festgehalten (`access.user.disable`, Kanal *access*), sodass Sie einen Nachweis
des Datums haben ([Das Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md)).

## Vor dem letzten Tag

- [ ] Bitten Sie die Person, ihre **Entwürfe** fertigzustellen oder zu übergeben. Entwürfe sind persönlich: Nur ihr
      Ersteller sieht sie unter *Meine Entwürfe* ([Meine Entwürfe und meine ausstehenden Elemente](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)).
      Jemand anderes kann auf der Versionsseite aus jeder Version einen neuen Entwurf machen ([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)).
- [ ] Ist sie eine **freigebende Person**, fügen Sie zuerst einen Ersatz zu den freigebenden Benutzern oder Gruppen
      des Freigabe-Workflows hinzu, damit nichts auf eine Person wartet, die nicht mehr da ist ([Workflows](../../../anleitungen/workflows.md)).
- [ ] Erhält sie Formular-E-Mails, Newsletter-Freigaben oder Benachrichtigungen im Namen des Teams, leiten Sie diese
      an eine Funktionsadresse oder einen Kollegen um.
- [ ] Listen Sie die Layouts, Newsletter und Kampagnen auf, für die sie zuständig ist, und benennen Sie eine neue
      zuständige Person.

## Am letzten Tag

1. **Deaktivieren Sie das Konto** unter **Benutzer > Benutzerkonten prüfen** (oder über **Edit user settings** des Kontos).
2. **Widerrufen Sie API-Schlüssel und Tokens.** Persönliche API-Schlüssel enden, wenn ihr Besitzer deaktiviert oder
   entfernt wird, und Administratoren können jeden Schlüssel unter **Setup > oAuth-Verwaltung > API-Schlüssel**
   widerrufen ([Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md)). Remote-Service-Tokens eines deaktivierten
   Benutzers antworten mit 401 ([Remote-Dienste](../../../funktionen/6.0/entfernte-dienste-expservices.md)). Prüfen Sie
   trotzdem beide Listen.
3. **Entfernen Sie Rollenzuweisungen, die der Person direkt** (nicht über eine Gruppe) gemacht wurden, falls vorhanden.
4. Hatte die Person Administratorzugriff, fragen Sie Ihren Hoster oder Partner, ob sie auch Zugriff auf Server,
   Datenbank oder Control Panel hatte, und lassen Sie auch diesen schließen.

> **Warnung**
> Löschen Sie das Konto nicht, um „aufzuräumen“. Die Inhalte bleiben zwar erhalten, aber das Konto lässt sich nicht
> wiederherstellen, und Sie verlieren die Verbindung zwischen der Person und dem, was sie getan hat.

## Nach dem letzten Tag

- Prüfen Sie, dass über ausstehende Versionen, die sie geschickt hat, entschieden ist: *Meine Warteliste* der
  freigebenden Personen oder **Setup > Workflow-Prozesse** ([Workflows](../../../anleitungen/workflows.md)).
- Filtern Sie bei der nächsten Überprüfung unter Benutzerkonten prüfen nach **Deaktiviert** und **Seit N Tagen nicht
  gesehen**, um zu bestätigen, dass nichts übersehen wurde.
- Aktualisieren Sie das [Arbeitsblatt für Teamrollen](arbeitsblatt-teamrollen.md).

## Protokoll

```text
OFFBOARDING RECORD
Person or company: ______________________   Last working day: __________
Drafts handed over to: ______________   Approver replaced by: ______________
Account disabled on: __________   by: ______________
API keys / tokens revoked? yes / none found
Direct role assignments removed? yes / none
Server or panel access closed (asked host/partner)? yes / not applicable
Checked by (manager): ______________
```

## Verwandte Seiten

- [Neue Mitarbeitende einarbeiten](neue-mitarbeitende-einarbeiten.md)
- [Konten, Passwörter und zweistufige Anmeldung](konten-passwoerter-und-zweistufige-anmeldung.md)
- [Zugriffsüberprüfungen](../../unternehmen/sicherheit/zugriffspruefungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
