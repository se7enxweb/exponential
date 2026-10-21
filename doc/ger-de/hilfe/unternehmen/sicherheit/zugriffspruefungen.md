---
title: Zugriffsprüfungen
audience: businesses, admin, project-manager
category: business-solutions
order: 60
summary: Eine jährliche Prüfung Schritt für Schritt, wer auf einer Exponential-Website was tun darf - Rollen mit Vollzugriff, sensible Richtlinien, Konten, die niemand nutzt, Menschen, die gegangen sind - mit den Rollenseiten, Benutzerkonten prüfen und dem Audit-Trail, und mit einem Protokoll, das Sie als Nachweis aufbewahren.
keywords: Zugriffsprüfung, Berechtigungen, Rollen, Richtlinien, minimale Rechte, Vollzugriff, Administrator, Benutzerkonten, ausgeschiedene Mitarbeiter, Kontenpflege, Audit-Trail, Rollenzuweisung, Nachweis, Rezertifizierung, access review, permissions, roles, policies, least privilege, full access, user accounts, leavers, user hygiene, audit trail, role assignment, evidence, recertification
type: help
---
# Zugriffsprüfungen

Diese Seite richtet sich an Inhaber und Führungskräfte, die mindestens einmal im Jahr bestätigen müssen, dass nur die
richtigen Menschen die richtigen Teile der Website erreichen, und an den Administrator, der die Prüfung vorbereitet. Eine
Zugriffsprüfung findet den Redakteur, der im letzten Frühjahr gegangen ist, den vorübergehenden Administrator, der nie
zurückgestuft wurde, und die Rolle, die still gewachsen ist.

> **Hinweis**
> Grundlagen, keine Rechtsberatung. Manche Vorschriften und Verträge verlangen Zugriffsprüfungen in festen Abständen;
> befolgen Sie sie, wo sie gelten.

## Inhalt

1. [Wie Zugriff in Exponential funktioniert](#wie-zugriff-in-exponential-funktioniert)
2. [Vor der Prüfung](#vor-der-prüfung)
3. [Schritt für Schritt](#schritt-für-schritt)
4. [Sensible Berechtigungen, die Sie zuerst ansehen](#sensible-berechtigungen-die-sie-zuerst-ansehen)
5. [Zwischen den Prüfungen](#zwischen-den-prüfungen)
6. [Ein Prüfprotokoll zum Aufbewahren](#ein-prüfprotokoll-zum-aufbewahren)

## Wie Zugriff in Exponential funktioniert

- Eine **Rolle** ist ein Satz von **Richtlinien**; jede Richtlinie erlaubt ein Modul oder eine Funktion, eventuell
  beschränkt auf Sektionen, Klassen, Teilbäume, Sprachen oder Siteaccesses.
- Eine Rolle bewirkt nichts, bis sie Benutzern oder Benutzergruppen **zugewiesen** ist. Ein Benutzer hat jede Richtlinie
  jeder Rolle, die ihm oder seinen Gruppen zugewiesen ist.
- Eine Rolle mit einer Richtlinie über **jedes Modul** ist Vollzugriff und in der Rollenliste rot markiert; eine
  Richtlinie über das Modul **role** lässt ihre Benutzer sich selbst jeden Zugriff geben und ist gelb markiert.

Quelle: [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md#kurz-gesagt). `PreventPrivilegeEscalation` ist
standardmäßig eingeschaltet (`settings/site.ini`), sodass niemand mehr vergeben kann, als er selbst hat
([Checklisten zu Barrierefreiheit, Sicherheit und Compliance](../../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md#konten-und-zugriff)).

## Vor der Prüfung

- [ ] Besorgen Sie sich von der Personalabteilung oder dem Vertragsverantwortlichen eine aktuelle Liste der
      Mitarbeiter, Auftragnehmer und Partner.
- [ ] Vereinbaren Sie, wer die Prüfung unterschreibt (der Verantwortliche der Website) und wer sie vorbereitet (ein
      Administrator).
- [ ] Notieren Sie das Datum der letzten Prüfung; der Audit-Trail kann zeigen, was sich seitdem geändert hat.

## Schritt für Schritt

1. **Listen Sie die Rollen auf.** Öffnen Sie **Benutzer > Rollen**. Notieren Sie jede rot (Vollzugriff) oder gelb (kann
   Rollen ändern) markierte Rolle. Fragen Sie bei jeder: Braucht diese Rolle das noch?
2. **Lesen Sie jede Rolle.** Öffnen Sie sie und lesen Sie ihre Richtlinien. Entfernen Sie Richtlinien, die niemand
   braucht; bevorzugen Sie Beschränkungen auf Sektionen oder Teilbäume statt ganzer Module
   ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
3. **Prüfen Sie die Zuweisungen.** Listen Sie für jede Rolle die Benutzer und Gruppen auf, denen sie zugewiesen ist.
   Vergleichen Sie mit der Mitarbeiterliste. Achten Sie am meisten auf Administratoren.
4. **Finden Sie ungenutzte Konten.** Öffnen Sie **Benutzer > Benutzerkonten prüfen** und verwenden Sie die Filter
   **Nie angemeldet** und **Seit N Tagen nicht gesehen** (standardmäßig 180)
   ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).
5. **Finden Sie geteilte und defekte Konten.** Verwenden Sie die Filter **Doppelter Benutzername**, **Doppelte
   E-Mail-Adresse** und **Verwaist**.
6. **Handeln Sie.** Deaktivieren Sie Konten, die nicht gebraucht werden (die Seite fragt nach einer Bestätigung und
   zeigt die Anzahl). Ein Benutzerobjekt zu entfernen ist endgültig, deaktivieren Sie daher zuerst; die optionale
   Einstellung **Deaktivieren und verschieben** sammelt deaktivierte Konten in einer verborgenen Gruppe ohne Rollen.
7. **Prüfen Sie die zweistufige Anmeldung** für Administratoren und Redakteure, wenn Ihre Richtlinien sie verlangen
   ([Benutzerkonten und Passwörter](benutzerkonten-und-passwoerter.md)).
8. **Sehen Sie sich das Protokoll an.** Filtern Sie im Audit-Trail den Kanal `access` nach Rollenzuweisungen seit der
   letzten Prüfung und bestätigen Sie, dass jede beabsichtigt war
   ([Compliance-Nachweise aus dem Audit-Trail](compliance-nachweise-aus-dem-audit-trail.md)).
9. **Exportieren Sie die Nachweise.** Exportieren Sie die Kontenliste aus Benutzerkonten prüfen als CSV und den
   Audit-Filter als CSV.
10. **Unterschreiben Sie das Protokoll** unten und legen Sie das Datum der nächsten Prüfung fest.

## Sensible Berechtigungen, die Sie zuerst ansehen

| Berechtigung | Warum sie wichtig ist | Quelle |
|---|---|---|
| Jede Rolle mit Vollzugriff | kann alles | [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) |
| Modul `role` | kann sich selbst jeden Zugriff geben | dieselbe |
| `infocollector/read` | liest jede Formulareingabe | [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md) |
| `mailpreferences/export` | exportiert die Mail-Daten von Menschen | [Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md#anfragen-betroffener-personen) |
| `audit/read`, `audit/manage` | liest oder verwaltet den Audit-Trail; in einer neuen Installation hat sie nur Administrator | [Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md#who-may-look) |
| `user/register` | wer sich überhaupt registrieren darf | [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md#8-festlegen-wer-sich-registrieren-und-wer-knotendaten-lesen-darf) |
| Autorisierung von OAuth-Clients | wer Apps verbinden darf | [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md#9-festlegen-wer-oauth-clients-autorisieren-darf) |
| Shop-Administration | sieht Kunden und Bestellungen | [Shop-Dashboard](../../../../eng-us/features/6.0/store-dashboard.md) |

Um in Benutzerkonten prüfen selbst E-Mail-Adressen zu sehen, braucht man das Recht `role/assign`
([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).

## Zwischen den Prüfungen

- Deaktivieren Sie Konten am letzten Arbeitstag einer Person; warten Sie nicht auf die jährliche Prüfung.
- Lassen Sie die Alarmregeln des Audit-Trails darauf achten, ob eine Administratorrolle vergeben wird; prüfen Sie mit
  `./console exp:audit alerts recipients`, wer die Alarmmail bekommt
  ([Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md#try-it-in-two-minutes)).
- Versehen Sie vorübergehenden Zugriff in Ihren Notizen mit einem Enddatum und entfernen Sie ihn an diesem Datum.
- Für Extranets mit Partnerkonten siehe [Prüfung der Berechtigungen im Extranet](../extranets/berechtigungen-im-extranet-pruefen.md).

## Ein Prüfprotokoll zum Aufbewahren

```text
ACCESS REVIEW RECORD
Site: ____________________   Review date: __________   Previous review: __________
Prepared by: ______________   Approved by (site owner): ______________

Roles with full access: ______________________   Still needed? yes / no  Action: ________
Roles that can change roles: _________________   Still needed? yes / no  Action: ________
Administrators (named): ______________________   All current staff? yes / no
Accounts never signed in: ___   disabled: ___
Accounts not seen for 180 days: ___   disabled: ___
Shared logins or e-mail addresses: ___   resolved: ___
Two-step sign-in on for all administrators? yes / no
Role assignments since last review checked in the audit trail? yes / no
Evidence files (CSV exports) stored at: ______________________
Next review date: __________
Signature: ______________
```

## Verwandte Seiten

- [Sicherheitsgrundlagen für Unternehmen](sicherheitsgrundlagen-fuer-unternehmen.md)
- [Die Administration einschränken](../../hosting/sicherheit/die-administration-einschraenken.md)
- [Abteilungen, Rollen und Sektionen](../intranets/abteilungen-rollen-und-sektionen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
