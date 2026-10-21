---
title: Checkliste Intranet
audience: government, admin, project-manager
category: public-sector
order: 90
summary: Eine Checkliste für Start und Überprüfung eines mit Exponential gebauten Intranets für Mitarbeiter - Zugang, Sektionen und Rollen, Anmeldung, Sitzungen, Netz, Suchmaschinen, Audit, ausscheidende Mitarbeiter und Verantwortung für Inhalte.
keywords: Checkliste Intranet, Start, Überprüfung, Zugriffskontrolle, Anmeldung, Sitzungen, ausscheidende Mitarbeiter, Audit, interne Inhalte, Sicherheit, intranet checklist, launch, review, access control, sign-in, sessions, leavers, audit, internal content, security
type: help
---
# Checkliste Intranet

Verwenden Sie diese Checkliste, bevor ein Intranet online geht, und danach zweimal im Jahr. Jeder Punkt nennt die Seite
dieses Bereichs, die ihn erklärt.

## Inhalt

1. [Zugang](#zugang)
2. [Anmeldung](#anmeldung)
3. [Betrieb](#betrieb)
4. [Inhalte](#inhalte)

## Zugang

- [ ] Der Intranet-Siteaccess verlangt eine Anmeldung; `user/register` ist dort nicht offen (siehe „Ein Siteaccess nur
  für Mitarbeiter“).
- [ ] Der Intranet-Host ist nur aus dem internen Netz oder über ein VPN erreichbar, wenn Ihre Richtlinie es vorsieht.
- [ ] Interne Inhalte liegen in Sektionen, die Anonymous nicht lesen kann, abgemeldet getestet (siehe „Sektionen und
  Rollen für Intranet-Inhalte“).
- [ ] Eingeschränktes Material in einer eigenen Sektion und Gruppe.

## Anmeldung

- [ ] Die Login-Handler gewählt und getestet; LDAP gegen das echte Verzeichnis getestet (siehe „Anmeldung über LDAP“).
- [ ] Zweistufige Anmeldung für Mitarbeiter mit administrativen Rechten verlangt (siehe „Zweistufige Anmeldung für
  Mitarbeiter“).
- [ ] Das Zeitlimit der Sitzungen passt zu den Inhalten (siehe „Sitzungen, Abmeldung und gemeinsam genutzte
  Computer“).
- [ ] Eine Entscheidung über Sperre oder Alarmierung bei fehlgeschlagenen Anmeldungen.

## Betrieb

- [ ] Ausscheidende: Verzeichniskonto und Exponential-Konto am selben Tag deaktiviert; **Benutzer > Benutzerkonten
  prüfen** monatlich auf lange nicht gesehene Konten überprüft.
- [ ] Das Audit-Protokoll eingeschaltet und von jemandem gelesen ([Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md)).
- [ ] Sicherungen umfassen die Inhalte und Dateien des Intranets.

## Inhalte

- [ ] Jede Intranet-Sektion hat eine verantwortliche Person und ein Überprüfungsdatum.
- [ ] Keine personenbezogenen Daten über das hinaus, was Mitarbeiter brauchen; Personaldaten bleiben in den Systemen
  der Personalverwaltung.
- [ ] Die Suche funktioniert für Mitarbeiter, und interne Seiten erscheinen nicht in der Suche der öffentlichen Site.

## Verwandte Seiten

- [Intranets für Mitarbeiter mit Exponential](intranets-fuer-mitarbeiter-mit-exponential.md)
- [Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
