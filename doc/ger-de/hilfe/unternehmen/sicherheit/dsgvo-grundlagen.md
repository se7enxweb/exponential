---
title: DSGVO-Grundlagen
audience: businesses, admin, project-manager
category: business-solutions
order: 20
summary: Die Datenschutz-Grundverordnung der EU im Überblick für ein Unternehmen, das Exponential betreibt - Rollen, Grundsätze und Rechte der Menschen - zugeordnet zu den Funktionen und Befehlen der Software, die helfen, und zu den Entscheidungen, die bei Ihnen bleiben. Keine Rechtsberatung.
keywords: DSGVO, Datenschutz-Grundverordnung, Datenschutz, Privatsphäre, Verantwortlicher, Auftragsverarbeiter, Grundsätze, Einwilligung, Auskunftsrecht, Recht auf Löschung, Datenübertragbarkeit, Speicherbegrenzung, Datenschutzhinweis, Datenschutzbeauftragter, keine Rechtsberatung, GDPR, General Data Protection Regulation, data protection, privacy, controller, processor, principles, consent, right of access, right to erasure, portability, storage limitation, privacy notice, DPO, not legal advice
type: help
---
# DSGVO-Grundlagen

Diese Seite richtet sich an Inhaber und Führungskräfte von Unternehmen, die Menschen in der Europäischen Union bedienen
oder aus anderen Gründen unter die Datenschutz-Grundverordnung (DSGVO) fallen. Sie gibt den Überblick über die
Verordnung in einfachen Worten und zeigt für jeden Teil, welche Funktionen von Exponential helfen und was Sie trotzdem
selbst tun müssen.

![Gesammelte Informationen: was Besucher über Formulare gesendet haben](../../../../eng-us/help/images/help/bu-forms.webp)

> **Warnung**
> Diese Seite enthält Grundlagen, keine Rechtsberatung. Sie sagt nicht, ob Ihre Website die Vorschriften einhält. Lesen
> Sie den Text der Verordnung, Verordnung (EU) 2016/679, im Amtsblatt (<https://eur-lex.europa.eu/eli/reg/2016/679/oj>),
> und lassen Sie sich von einem Anwalt oder Ihrem Datenschutzbeauftragten beraten. Andere Gesetze können ebenfalls
> gelten, zum Beispiel zum E-Mail-Marketing; das Projekt ordnet mehrere davon in
> [E-Mail-Einstellungen: die Checkliste zu den Gesetzen](../../../spezifikationen/6.0/mail-einstellungen-compliance.md) ein.

## Inhalt

1. [Wer entscheidet: Verantwortlicher und Auftragsverarbeiter](#wer-entscheidet-verantwortlicher-und-auftragsverarbeiter)
2. [Die Grundsätze und was hilft](#die-grundsätze-und-was-hilft)
3. [Die Rechte der Menschen und die Werkzeuge](#die-rechte-der-menschen-und-die-werkzeuge)
4. [Sicherheit der Verarbeitung](#sicherheit-der-verarbeitung)
5. [Was die Software Ihnen nicht abnehmen kann](#was-die-software-ihnen-nicht-abnehmen-kann)
6. [In fünf Schritten anfangen](#in-fünf-schritten-anfangen)

## Wer entscheidet: Verantwortlicher und Auftragsverarbeiter

In einer typischen Konstellation entscheidet Ihr Unternehmen, warum und wie personenbezogene Daten auf Ihrer Website
verwendet werden, und ein Hoster oder Partner betreibt die Infrastruktur für Sie. Welche rechtliche Rolle jede Partei hat
und welchen Vertrag Sie untereinander brauchen, klären Sie mit Ihrem Rechtsbeistand
([Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md#rollen-wer-was-entscheidet)). Fragen Sie Ihren Hoster
und Partner, wie sie Anfragen zum Datenschutz unterstützen; siehe
[Fragen an einen Partner](../auswahl/fragen-an-einen-partner.md).

## Die Grundsätze und was hilft

Die Verordnung legt Grundsätze für die Verarbeitung personenbezogener Daten fest. Im Überblick, mit den Funktionen, die
jeden unterstützen:

| Grundsatz (im Überblick) | Was in Exponential hilft | Quelle |
|---|---|---|
| Rechtmäßigkeit, Verarbeitung nach Treu und Glauben, Transparenz | Mails sagen, warum sie gesendet wurden; Fußzeilen und die Einstellungsseite können Ihren Datenschutzhinweis verlinken | [Die Checkliste zu den Gesetzen](../../../spezifikationen/6.0/mail-einstellungen-compliance.md) |
| Zweckbindung | Ein Einwilligungsschalter pro Mail-Kategorie | [E-Mail-Einstellungen](../../../../eng-us/features/6.0/mail-preferences.md) |
| Datenminimierung | Anfrage-Logs maskieren Client-Adressen standardmäßig; der Audit-Trail kürzt Adressen und hasht Sessions | [Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md#datenschutz-als-standard-in-den-logs) |
| Richtigkeit | Benutzer ändern ihre eigenen Kontodaten; Mitarbeiter bearbeiten Benutzerobjekte in der Administration | [Ihr Konto und die Anmeldung](../../konten-und-anmeldung.md) |
| Speicherbegrenzung | Aufbewahrungseinstellungen für Logs, den Audit-Trail, das Einwilligungsprotokoll, den Papierkorb und Sessions | [Aufbewahrung und Löschung von Daten](aufbewahrung-und-loeschung-von-daten.md) |
| Integrität und Vertraulichkeit | bcrypt-Passwörter, zweistufige Anmeldung, Sicherheits-Header, Rollen | [Benutzerkonten und Passwörter](benutzerkonten-und-passwoerter.md) |
| Rechenschaftspflicht | Ein Protokoll der Einwilligungsänderungen und ein manipulationssicherer Audit-Trail | [Compliance-Nachweise aus dem Audit-Trail](compliance-nachweise-aus-dem-audit-trail.md) |

## Die Rechte der Menschen und die Werkzeuge

| Anfrage | Was die Software bietet | Quelle |
|---|---|---|
| Ihre Daten sehen (Auskunft) | Für Mail-Daten der eigene Download der Person oder `exp:mail:preferences export`; Audit-Datensätze über einen Benutzer mit `exp:audit export --subject-user=<id>` | [Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md#anfragen-betroffener-personen) |
| Ihre Daten mitnehmen (Datenübertragbarkeit) | „Meine E-Mail-Daten herunterladen“ als JSON und CSV | [Die Checkliste zu den Gesetzen](../../../spezifikationen/6.0/mail-einstellungen-compliance.md) |
| Ihre Daten berichtigen | Bearbeitung des Kontos durch den Benutzer oder durch Mitarbeiter | [Ihr Konto und die Anmeldung](../../konten-und-anmeldung.md) |
| Ihre Daten löschen | `exp:mail:preferences erase`; das Entfernen eines Benutzerkontos löscht auch seine E-Mail-Einstellungen; Formularsammlungen lassen sich unter **Setup > Gesammelte Informationen** entfernen | [Aufbewahrung und Löschung von Daten](aufbewahrung-und-loeschung-von-daten.md) |
| Werbung widersprechen | „Alle optionalen E-Mails beenden“ per Link setzt die Adresse auf die Sperrliste | [Einwilligung und Formulare](einwilligung-und-formulare.md) |

Manche Daten deckt kein einzelner Befehl ab: Bestellungen, Datensätze von Newsletter-Abonnenten in `cjw_newsletter` und
Kopien in Backups. Planen Sie, wie Sie mit jedem davon umgehen;
[Personenbezogene Daten in Exponential](personenbezogene-daten-in-exponential.md) listet auf, wo sie liegen.

> **Hinweis**
> Der Audit-Trail ist so gebaut, dass einzelne Datensätze nicht gelöscht werden können, ohne seine Hash-Kette zu brechen.
> Der Audit-Leitfaden beschreibt, Datensätze für ihre Aufbewahrungsfrist zu behalten und personenbezogene Felder nach
> einer festgelegten Zahl von Tagen zu pseudonymisieren
> ([Audit: Aufgaben zu Datenschutz und DSGVO](../../../../eng-us/bc/6.0/audit.md#412-privacy-and-gdpr-tasks)). Ob das in Ihrem
> Fall eine Löschanfrage beantwortet, ist eine rechtliche Frage.

## Sicherheit der Verarbeitung

Was die Software beiträgt, steht in [Sicherheitsgrundlagen für Unternehmen](sicherheitsgrundlagen-fuer-unternehmen.md) und
[Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md#sicherheit-der-verarbeitung). Was der Hoster beiträgt
(Isolation, verschlüsselte Backups, Patches), gehört in Ihren Hosting-Vertrag.

## Was die Software Ihnen nicht abnehmen kann

- Ihre Rechtsgrundlage für jede Art der Verarbeitung festlegen.
- Ihren Datenschutzhinweis und Ihr Verzeichnis von Verarbeitungstätigkeiten schreiben.
- Den Absendernamen und die Postanschrift in der Fußzeile Ihrer Mails ausfüllen
  (`mailpreferences.ini [FooterSettings]`, nach einer Installation leer).
- Aufbewahrungsfristen festlegen und die Aufbewahrung der Backups passend einstellen.
- Dienste Dritter beurteilen, die Ihre Templates laden (Schriften, Karten, Webanalyse); die Content Security Policy macht
  sie sichtbar, sie beurteilt sie nicht.
- Verträge mit Ihrem Hoster, Partner, Zahlungsanbieter oder einem gehosteten KI-Anbieter unterschreiben.

## In fünf Schritten anfangen

1. Listen Sie die personenbezogenen Daten auf, die Sie haben, mit [Personenbezogene Daten in Exponential](personenbezogene-daten-in-exponential.md).
2. Beantworten Sie die Fragen in [Checklisten zu Barrierefreiheit, Sicherheit und Compliance](../../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md#datenschutz-und-privatsphäre-zu-beantwortende-fragen)
   mit Ihrem Datenschutzbeauftragten oder Anwalt.
3. Legen Sie Aufbewahrungsfristen und die passenden Einstellungen fest ([Aufbewahrung und Löschung von Daten](aufbewahrung-und-loeschung-von-daten.md)).
4. Schreiben Sie auf, wie Sie Anfragen beantworten, Daten zu sehen, zu exportieren oder zu löschen, und wer das tut.
5. Prüfen Sie es einmal im Jahr mit der [Checkliste: Jahresrückblick](../vorlagen/checkliste-jahresrueckblick.md).

## Verwandte Seiten

- [Compliance und DSGVO für Hoster](../../hosting/sicherheit/compliance-und-dsgvo.md)
- [Einwilligung und Formulare](einwilligung-und-formulare.md)
- [Kundendaten schützen (Partner)](../../partner/kundendaten-schuetzen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
