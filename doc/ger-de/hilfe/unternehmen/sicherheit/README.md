---
title: Sicherheit und Datenschutz
audience: businesses, admin, project-manager
category: business-solutions
order: 1
summary: Die Einstiegsseite des Bereichs Sicherheit. Grundlagen für Unternehmen, die Kundendaten in Exponential aufbewahren - Sicherheit, DSGVO, wo personenbezogene Daten liegen, Einwilligung, Konten, Zugriffsprüfungen, Updates, Aufbewahrung und Nachweise aus dem Audit-Trail. Keine Rechtsberatung.
keywords: Sicherheit, Datenschutz, DSGVO, Privatsphäre, personenbezogene Daten, Einwilligung, Passwörter, zweistufige Anmeldung, Zugriffsprüfung, Sicherheitsupdates, Aufbewahrung, Löschung, Audit-Trail, Compliance, keine Rechtsberatung, security, data protection, GDPR, privacy, personal data, consent, passwords, two-step sign-in, access review, security updates, retention, deletion, audit trail, compliance, not legal advice
type: help
---
# Sicherheit und Datenschutz

Dieser Bereich richtet sich an Inhaber, Führungskräfte und die Person, die in einer kleinen oder mittleren Organisation
für Kundendaten geradesteht. Er erklärt in einfachen Worten, was Exponential bereits tut, um eine Website und ihre Daten
zu schützen, was Sie selbst entscheiden und tun müssen und wo die technischen Seiten für diejenigen stehen, die die
Arbeit erledigen.

> **Warnung**
> Diese Seiten sind Grundlagen, keine Rechtsberatung. Ob Ihre Website die DSGVO oder ein anderes Gesetz einhält, hängt
> davon ab, was Sie verarbeiten, warum und für wen. Lesen Sie den Text der Verordnung selbst und lassen Sie sich von
> einem Anwalt oder Ihrem Datenschutzbeauftragten beraten. Die eigenen Seiten des Projekts sagen dasselbe
> ([Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md)).

## Inhalt

1. [Was dieser Bereich abdeckt](#was-dieser-bereich-abdeckt)
2. [Die Seiten](#die-seiten)
3. [Eine empfohlene Reihenfolge](#eine-empfohlene-reihenfolge)
4. [Was Exponential standardmäßig tut](#was-exponential-standardmäßig-tut)
5. [Benachbarte Bereiche](#benachbarte-bereiche)

## Was dieser Bereich abdeckt

- Die Sicherheitsgrundlagen, die jede Unternehmenswebsite braucht, und wer sie umsetzt.
- Die DSGVO im Überblick und wie die Software Ihnen hilft, Ihre Pflichten zu erfüllen.
- Wo personenbezogene Daten innerhalb einer Exponential-Installation liegen.
- Einwilligung für Mails und Formulare, Konten und Passwörter und die Prüfung, wer Zugriff hat.
- Sicherheitsupdates und wie Sie eine Schwachstelle vertraulich melden.
- Wie lange Daten aufbewahrt werden, wie sie gelöscht werden und wie Sie mit dem Audit-Trail Nachweise vorlegen.

## Die Seiten

| Seite | Was sie beantwortet |
|---|---|
| [Sicherheitsgrundlagen für Unternehmen](sicherheitsgrundlagen-fuer-unternehmen.md) | Die zehn Dinge, die ein Unternehmen eingerichtet haben muss, und wer was tut |
| [DSGVO-Grundlagen](dsgvo-grundlagen.md) | Die Grundsätze im Überblick und die Funktionen der Software, die sie unterstützen |
| [Personenbezogene Daten in Exponential](personenbezogene-daten-in-exponential.md) | Wo Konten, Bestellungen, Formulardaten, Newsletter-Abonnenten, Logs und der Audit-Trail gespeichert sind |
| [Einwilligung und Formulare](einwilligung-und-formulare.md) | Einwilligung für Mails, Double-Opt-in, Abmeldung, Formulare, die Daten erheben |
| [Benutzerkonten und Passwörter](benutzerkonten-und-passwoerter.md) | Passwortregeln, zweistufige Anmeldung, Passwortänderungen, Kontenpflege |
| [Zugriffsprüfungen](zugriffspruefungen.md) | Eine jährliche Prüfung, wer was tun darf, Schritt für Schritt |
| [Sicherheitsupdates und Offenlegung](sicherheitsupdates-und-offenlegung.md) | Unterstützte Versionen, Korrekturen einspielen, eine Schwachstelle vertraulich melden |
| [Aufbewahrung und Löschung von Daten](aufbewahrung-und-loeschung-von-daten.md) | Wie lange Logs und Datensätze aufbewahrt werden und wie Sie auf Anfrage löschen |
| [Compliance-Nachweise aus dem Audit-Trail](compliance-nachweise-aus-dem-audit-trail.md) | Den manipulationssicheren Audit-Trail als Nachweis verwenden |

## Eine empfohlene Reihenfolge

1. [Sicherheitsgrundlagen für Unternehmen](sicherheitsgrundlagen-fuer-unternehmen.md): der Überblick.
2. [Personenbezogene Daten in Exponential](personenbezogene-daten-in-exponential.md): Wissen Sie, was Sie haben.
3. [DSGVO-Grundlagen](dsgvo-grundlagen.md) und [Einwilligung und Formulare](einwilligung-und-formulare.md): Ihre Pflichten gegenüber
   Menschen.
4. [Benutzerkonten und Passwörter](benutzerkonten-und-passwoerter.md) und [Zugriffsprüfungen](zugriffspruefungen.md): wer
   hineinkommt.
5. [Sicherheitsupdates und Offenlegung](sicherheitsupdates-und-offenlegung.md): auf Dauer sicher bleiben.
6. [Aufbewahrung und Löschung von Daten](aufbewahrung-und-loeschung-von-daten.md) und
   [Compliance-Nachweise aus dem Audit-Trail](compliance-nachweise-aus-dem-audit-trail.md): es nachweisen.

## Was Exponential standardmäßig tut

| Schutz | Quelle |
|---|---|
| Passwörter als bcrypt-Hashes gespeichert; gängige Passwörter abgelehnt | [Sicherheitshärtung](../../sicherheitshaertung.md#brute-force-und-credential-stuffing-bei-der-anmeldung) |
| Sicherheits-Header auf jeder Seite, einschließlich der Administration | [Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md) |
| Eine Content Security Policy, in der Administration im Nur-Bericht-Modus | [Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md) |
| Ein manipulationssicherer Audit-Trail, standardmäßig eingeschaltet | [Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md) |
| Client-Adressen in Anfrage-Logs maskiert (`MaskIP=enabled`) | [Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md#datenschutz-als-standard-in-den-logs) |
| Optionale Mails nur mit Einwilligung, Abmeldung mit einem Klick | [E-Mail-Einstellungen](../../../../eng-us/features/6.0/mail-preferences.md) |
| Kein Installer hinterlässt das alte Standard-Administratorpasswort | [Das Standard-Administratorpasswort](../../standard-administratorpasswort.md) |
| Zweistufige Anmeldung über `sevenx_authentication_2fa` verfügbar | [Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md) |

Die Standardeinstellungen sind ein starker Anfang. Die Entscheidungen über Ihre Daten, Ihre Mitarbeiter und Ihre Verträge
bleiben Ihre.

## Benachbarte Bereiche

- [Zuverlässigkeit](../zuverlaessigkeit/README.md): Backups, Wiederherstellungen und Reaktion auf Vorfälle.
- [Shops: Sicherheitscheckliste](../shops/sicherheitscheckliste-fuer-shops.md) und
  [Kartenzahlungen und PCI-Grundlagen](../shops/kartenzahlungen-und-pci-grundlagen.md).
- [Newsletter: Opt-in und Einwilligung](../newsletter/opt-in-und-einwilligung.md).
- [Kosten](../kosten/README.md) und [Hosting und Partner auswählen](../auswahl/README.md).
- Behörden haben einen eigenen Bereich: [Behörden: hier beginnen](../../../../eng-us/help/government/government-start-here.md).

## Verwandte Seiten

- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Leitfaden Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)
- [Checklisten zu Barrierefreiheit, Sicherheit und Compliance](../../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md)
- [Sicherheit für Hoster](../../hosting/sicherheit/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
