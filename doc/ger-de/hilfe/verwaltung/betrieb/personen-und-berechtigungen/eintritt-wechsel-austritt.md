---
title: Eintritt, Wechsel und Austritt
audience: government, admin, project-manager
category: public-sector
order: 110
summary: Der Lebenszyklus der Konten von Beschäftigten im öffentlichen Sektor in Exponential - Konten in der richtigen Gruppe anlegen, den Fachbereich wechseln und Konten am letzten Arbeitstag stilllegen, ohne Inhalte oder den Audit-Nachweis zu verlieren - mit den Bildschirmen und Befehlen für jeden Schritt.
keywords: Eintritt Wechsel Austritt, Einarbeitung, Ausscheiden, neue Beschäftigte, Personalveränderungen, Fachbereichswechsel, Austritt, Konto deaktivieren, Deaktivieren und verschieben, Benutzergruppen, Lebenszyklus eines Kontos, Personalprozess, joiners movers leavers, JML, onboarding, offboarding, new staff, department change, leaver, disable account, Deactivate and move, user groups, account life cycle, HR process
type: help
---
# Eintritt, Wechsel und Austritt

Diese Seite richtet sich an Administratoren und an Personal- oder IT-Serviceteams, die die Konten der Beschäftigten für eine
öffentliche Website verwalten. Die meisten öffentlichen Stellen haben ein Verfahren für Eintritt, Wechsel und Austritt; diese
Seite ordnet jedem Schritt zu, was Exponential bietet. Die Fakten stammen aus
[Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md), [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md)
und [Ein Benutzerpasswort zurücksetzen](../../../../../eng-us/features/6.0/reset-user-password.md).

## Inhalt
1. [Der Grundsatz: Gruppen tragen den Zugang](#der-grundsatz-gruppen-tragen-den-zugang)
2. [Eintritt](#eintritt)
3. [Wechsel](#wechsel)
4. [Austritt](#austritt)
5. [Aufzubewahrende Nachweise](#aufzubewahrende-nachweise)
6. [Verwandte Seiten](#verwandte-seiten)

## Der Grundsatz: Gruppen tragen den Zugang

Rollen werden Benutzergruppen zugewiesen, daher folgt der Zugang eines Kontos den Gruppen, in denen es platziert ist. Wer neu
eintritt, wird in Gruppen platziert, wer wechselt, wechselt die Gruppen, wer austritt, verlässt alle. Niemand bearbeitet Rollen
für eine einzelne Person. Exponential hat keine eigene Anbindung an ein Personalsystem; die Anfrage kommt meist als Ticket, und das
Konto wird von einem Administrator geändert (oder von einem LDAP- oder Single-Sign-on-Handler, falls Ihre Installation einen
verwendet; fragen Sie Ihre Entwickler).

## Eintritt

1. Nehmen Sie die Anfrage mit dem Fachbereich, der Tätigkeit und der Person entgegen, die die Anfrage freigibt.
2. Öffnen Sie im Baum **Benutzer** die Benutzergruppe des Fachbereichs und legen Sie dort einen neuen Benutzer an; die
   Hauptplatzierung des Benutzers liegt in dieser Gruppe, also gelten die Rollen der Gruppe sofort
   ([Neue Beschäftigte hinzufügen](../../../../../eng-us/help/government/tasks/people-and-permissions/add-a-new-staff-member.md)).
3. Übermitteln Sie ein erstes Passwort über einen sicheren Kanal, oder lassen Sie die Person **Passwort vergessen?** verwenden;
   die Mindestlänge ist `site.ini [UserSettings] MinPasswordLength` (standardmäßig 10, [Sicherheitsstandard](../../../../../eng-us/standards/security.md)).
4. Bitten Sie die Person, die zweistufige Anmeldung einzurichten, wenn Ihre Richtlinie das verlangt
   ([Zwei-Faktor für Beschäftigte](zwei-faktor-anmeldung-fuer-beschaeftigte.md)).
5. Prüfen Sie: Die Kontoseite des Benutzers (`/user/edit/<id>`) nennt die Gruppen und Rollen.

## Wechsel

Ein Fachbereichswechsel ist ein Verschieben des Kontos zwischen Gruppen:

1. Fügen Sie eine Platzierung in der Gruppe des neuen Fachbereichs hinzu, machen Sie sie zur Hauptplatzierung und entfernen Sie
   dann die alte Platzierung. Oder verschieben Sie den Knoten des Kontos direkt.
2. Prüfen Sie die Kontoseite: Die Rollen der alten Gruppe sind weg, die der neuen Gruppe sind da.
3. Freigabeaufgaben: Wenn die Person im alten Fachbereich Inhalte freigegeben hat, entfernen Sie sie aus der dortigen Gruppe der
   Freigebenden und aus jedem Feld **Benutzer, die den Inhalt bewilligen** eines Freigabe-Ereignisses.

Siehe [Beschäftigte in einen anderen Fachbereich verschieben](../../../../../eng-us/help/government/tasks/people-and-permissions/move-a-staff-member-to-another-department.md).
Änderungen an Platzierungen stehen im Audit-Trail: das Entfernen einer Platzierung standardmäßig, das Hinzufügen und das Ändern
der Hauptplatzierung, sobald `content.node.add` und `content.node.main` aktiviert sind
([Sicherheit und Audit, Schritt 3](../../../../anleitungen/sicherheit-und-audit.md#3-das-audit-protokoll-lesen-5-minuten)).

## Austritt

Am letzten Arbeitstag:

1. **Deaktivieren** Sie das Konto unter **Benutzer > Benutzerkonten prüfen** (oder **Kontoeinstellungen** auf seiner Kontoseite).
   Es kann sich nicht mehr anmelden, und seine Sitzungen enden; Inhalte, Gruppen und Rollen bleiben. Erfasst als `access.user.disable`.
2. Legen Sie es innerhalb der vereinbarten Frist still:
   - **Deaktivieren und verschieben** (einzuschalten mit `[UserSettings] DeactivateAndMove=enabled`): deaktiviert, alle
     Platzierungen in eine verborgene Gruppe ohne Rollen gebracht, wobei gespeichert wird, wo es war; umkehrbar
     ([Eine austretende Person deaktivieren und verschieben](../../../../../eng-us/help/government/tasks/people-and-permissions/deactivate-and-move-a-leaver.md)).
   - Oder lassen Sie es deaktiviert an seinem Platz.
3. Übergabe: Entwürfe der austretenden Person erscheinen unter **Meine Entwürfe** nur für sie selbst, bitten Sie sie also, Entwürfe
   vor dem Austritt zu veröffentlichen oder zu entfernen; ausstehende Versionen erscheinen unter **Setup > Workflow-Prozesse**.
4. Entfernen Sie direkte Rollenzuweisungen und Einträge als Freigebende, die die Person nennen.

Das Konto zu entfernen ist möglich, aber endgültig: „Ein Benutzerobjekt kommt in Exponential nie in den Papierkorb: Das Entfernen
ist endgültig“ ([Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md#in-short)). Die meisten öffentlichen Stellen behalten
das Konto deaktiviert, weil die Inhalte, die ihm gehören, und der Audit-Trail darauf verweisen.

## Aufzubewahrende Nachweise

| Ereignis | Was aufzubewahren ist | Woher es stammt |
|---|---|---|
| Eintritt | Anfrage, freigebende Person, Gruppen | Ihr Ticket; die Kontoseite |
| Wechsel | alte und neue Gruppe | Ihr Ticket; Audit-Kanal `content` (Platzierungen) |
| Austritt | Datum der Deaktivierung, stillgelegt | Audit-Ereignisse `access.user.disable`, `access.user.deactivate_move` |
| Regelmäßige Überprüfung | Liste der Konten | **Liste als CSV exportieren** unter Benutzerkonten prüfen |

## Verwandte Seiten
- [Eine austretende Person entfernen](../../../../../eng-us/help/government/tasks/people-and-permissions/remove-a-leaver.md)
- [Pflege der Benutzerkonten](pflege-von-benutzerkonten.md)
- [Überprüfung der Administrationszugänge](ueberpruefung-administrativer-zugaenge.md)
- [Rollenentwurf: externe Dienstleister](rollenentwurf-externe-dienstleister.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
