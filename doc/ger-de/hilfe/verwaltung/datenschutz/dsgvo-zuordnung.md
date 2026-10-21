---
title: Die DSGVO und Exponential
audience: government, project-manager, admin, developer
category: public-sector
order: 20
summary: Für öffentliche Stellen in der EU und ihre Datenschutzbeauftragten - Themen der DSGVO (Einwilligung, Einwilligungsnachweise, Auskunft, Löschung, Einschränkung, Datenminimierung, Speicherbegrenzung, Sicherheit, Drittanbieter), zugeordnet zu den Funktionen, die Exponential bereitstellt, mit dem Status des Projekts „Teilweise erfüllt (Funktionen bereitgestellt)“ und den verbleibenden Pflichten des Betreibers.
keywords: DSGVO, Datenschutz-Grundverordnung, Verordnung 2016/679, Datenschutz, Einwilligung, Auskunftsrecht, Recht auf Löschung, Einschränkung, Datenminimierung, Speicherbegrenzung, Sicherheit der Verarbeitung, Zuordnung, Datenschutzbeauftragte, öffentlicher Sektor, Verantwortlicher, GDPR, consent, right of access, right to erasure, DPO
type: reference
---
# Die DSGVO und Exponential

Diese Seite richtet sich an Datenschutzbeauftragte, Projektleitungen und Administratoren öffentlicher Stellen in der EU
(und anderswo, wo die DSGVO gilt). Sie ordnet Themen der Datenschutz-Grundverordnung dem zu, was Exponential
bereitstellt, nach [Datenschutzstandards: DSGVO](../../../../eng-us/standards/privacy.md#gdpr). Der Status des Projekts ist
**„Teilweise erfüllt“**, in dem Sinne, dass die Software die aufgeführten Funktionen bereitstellt;
die Pflichten der Verordnung bleiben bei der Organisation, die die Website betreibt. Amtlicher Text: Verordnung (EU)
2016/679, <https://eur-lex.europa.eu/eli/reg/2016/679/oj>.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Zuordnung](#die-zuordnung)
2. [Was bei der Organisation bleibt](#was-bei-der-organisation-bleibt)
3. [Bekannte Lücken](#bekannte-lücken)
4. [Verwandte Seiten](#verwandte-seiten)

## Die Zuordnung

| Thema der DSGVO | Was Exponential bereitstellt | Seite in diesem Bereich |
|---|---|---|
| Einwilligung (Art. 4 Nr. 11, Art. 7) | E-Mail-Kategorien aus, bis die Person sie einschaltet; ein Schalter pro Zweck; Double-Opt-in für Newsletter und Marketing; `DefaultOn=true` wird abgelehnt, sofern nicht ausdrücklich erlaubt | E-Mail-Kategorien und Opt-in |
| Einwilligungsnachweise (Art. 7 Abs. 1) | Tabelle `expmail_consent_log` mit Zeit, Kategorie, altem und neuem Zustand, Quelle, dem genauen angezeigten Text, handelndem Benutzer, Siteaccess; Audit-Ereignis `access.user.consent.change` | Einwilligungsnachweise für E-Mail |
| Widerruf (Art. 7 Abs. 3) | derselbe Schalter, „Alle optionalen E-Mails ausschalten“, Abmelden mit einem Klick in jeder optionalen E-Mail, ohne Anmeldung | Abmelden mit einem Klick |
| Auskunft und Datenübertragbarkeit (Art. 15, 20) | E-Mail-Einstellungen und Einwilligungsverlauf als JSON oder CSV; Audit-Einträge von oder über einen Benutzer | Das Auskunftsrecht; Was nicht exportiert werden kann |
| Löschung (Art. 17) | das Entfernen eines Benutzerkontos löscht seine E-Mail-Einstellungen und anonymisiert sein Einwilligungsprotokoll; `exp:mail:preferences erase`; Audit-Einträge bleiben für ihre Aufbewahrungsfrist erhalten und werden pseudonymisiert | Das Recht auf Löschung |
| Einschränkung (Art. 18) | „Deaktivieren und verschieben“ (Opt-in); „Benutzerkonten prüfen“ | Einschränkung, Deaktivieren und verschieben |
| Datenminimierung (Art. 5 Abs. 1 Buchst. c, Art. 25) | Audit: Adressen gekürzt, Sitzungen gehasht, keine Passwörter oder POST-Inhalte, Einstellungen pro Feld; Anfrageprotokolle: maskierte Adressen, Abfragewerte `***`; Maskierung im Protokollbetrachter | Datenminimierung; Maskierung in Protokollen; Datenschutz im Prüfprotokoll |
| Speicherbegrenzung (Art. 5 Abs. 1 Buchst. e) | Audit 90 Tage live und 2 Jahre archiviert; 404-, Sicherheits- und CSP-Protokolle nach 30, 90 und 30 Tagen bereinigt; Aufbewahrung des Einwilligungsprotokolls | Aufbewahrung im Überblick |
| Sicherheit (Art. 32) | siehe den Sicherheitsbereich dieser Hilfe | (Sicherheitsbereich) |
| Keine Daten an Drittanbieter durch die Administration | Administrationsskripte lokal ausgeliefert; admin4 lädt keine Schriften oder Skripte von anderen Hosts | Erweiterungen von Drittanbietern |
| Fehlerberichte ohne personenbezogene Daten | der Fehlerbericht der Hilfe entfernt Adressen und Zugangsdaten | Bestandsaufnahme personenbezogener Daten |

## Was bei der Organisation bleibt

Nach der Aussage des Projekts: Zwecke und Rechtsgrundlagen festlegen, das Verzeichnis von Verarbeitungstätigkeiten
führen (Art. 30), die Datenschutzerklärung schreiben, Auftragsverarbeitungsverträge mit jedem hinzugefügten
Drittanbieter schließen und Anträge beantworten. Für eine öffentliche Stelle kommen hinzu: die DSFA, wo erforderlich,
der Aufbewahrungsplan und Anweisungen für die Beschäftigten.

## Bekannte Lücken

- Keine einheitliche Funktion „alle meine Daten exportieren“ oder „mich löschen“.
- Das Entfernen von Benutzern ist manuell, ein Konto nach dem anderen; ein Benutzerobjekt kommt nie in den Papierkorb.
- Kernel-Protokolle außer 404-, Sicherheits- und CSP-Protokoll haben standardmäßig keine Altersgrenze.
- Formulare zur Informationssammlung speichern, was Besucher eingeben; Einwilligungstext und Aufbewahrung sind Sache
  der Website.
- Suchbegriffe werden gespeichert, wo die Suchstatistiken eingeschaltet sind.

## Verwandte Seiten

- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Audit-Leitfaden, 4.12 Datenschutz- und DSGVO-Aufgaben](../../../../eng-us/bc/6.0/audit.md#412-privacy-and-gdpr-tasks)
- [Compliance und DSGVO für Hoster](../../hosting/sicherheit/compliance-und-dsgvo.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
