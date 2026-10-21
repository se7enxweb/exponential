---
title: Bereitschaft für Informationsfreiheit
audience: government, admin, project-manager
category: public-sector
order: 210
summary: Wie eine öffentliche Stelle ihre Exponential-Website und ihre Daten auf Anträge nach dem Informationsfreiheitsrecht vorbereitet - wissen, wo Informationen liegen, sie finden, Versionen und Audit-Einträge, Exporte, Schwärzung personenbezogener Daten und proaktive Veröffentlichung.
keywords: Informationsfreiheit, Informationsfreiheitsgesetz, IFG, Informationszugang, Anträge, Schriftgut, Suche, Versionen, Audit-Trail, Export, Schwärzung, proaktive Veröffentlichung, öffentlicher Sektor, freedom of information, FOI, FOIA, access to information, requests, records, search, versions, audit trail, export, redaction, proactive publication, public sector
type: help
---
# Bereitschaft für Informationsfreiheit

Diese Checkliste richtet sich an die für Informationszugang Zuständigen und die Website-Administration einer
öffentlichen Stelle, die Anträge nach dem Informationsfreiheitsrecht erhält. Website-Inhalte, Formulareinsendungen und
Systemaufzeichnungen können alle unter einen Antrag fallen. Diese Seite hilft Ihnen, sie zu finden und bereitzustellen;
ob sie herausgegeben werden müssen, ist eine rechtliche Frage.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung. In den Vereinigten Staaten folgen Bundesbehörden dem FOIA
> ([foia.gov](https://www.foia.gov/)).

## Inhalt

1. [Wissen, wo Informationen liegen](#wissen-wo-informationen-liegen)
2. [Sie finden](#sie-finden)
3. [Sie bereitstellen](#sie-bereitstellen)
4. [Personenbezogene Daten in Auskünften](#personenbezogene-daten-in-auskünften)
5. [Proaktiv veröffentlichen](#proaktiv-veröffentlichen)

## Wissen, wo Informationen liegen

- [ ] Veröffentlichte und unveröffentlichte Inhalte: Objekte, ihre Versionen und Übersetzungen ([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)).
- [ ] Entfernte Inhalte im Papierkorb, bis er geleert wird ([Papierkorb](../../../../eng-us/features/6.0/trash-who-and-where.md)).
- [ ] Formulareinsendungen unter **Setup > Gesammelte Informationen** ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)).
- [ ] Wer was geändert hat: der Audit-Trail ([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)).
- [ ] Inhalte von Newslettern und Daten von Abonnenten, falls genutzt ([Newsletter mit cjw_newsletter](../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md)).
- [ ] Datensicherungen, die Inhalte enthalten können, die von der Website gelöscht wurden ([Sicherungspläne und Aufbewahrung](../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md)).
- [ ] Die Übersicht dokumentiert, mit einer verantwortlichen Person für jede Quelle.

## Sie finden

- [ ] Suche in der Administration und erweiterte Suche nach Inhalten ([Suchen](../../suchen.md)).
- [ ] Der Inhaltsfinder, um Objekte im ganzen Baum zu finden (**Setup > Inhaltsfinder**) ([Inhaltsfinder](../../../../eng-us/features/6.0/content-finder.md)).
- [ ] Audit-Suche nach Name, Benutzer und Zeitraum: `./console exp:audit search --query=<text> --limit=<n>` ([Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)).
- [ ] Entwürfe und ausstehende Versionen: Fragen Sie die Eigentümer; Entwürfe gehören ihren Autoren ([Meine Entwürfe und meine Warteliste](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)).
- [ ] Eine Person aus dem eigenen Haus ist dafür geschult, nicht nur der Dienstleister.

## Sie bereitstellen

- [ ] Eine Seite oder Version so, wie Besucher sie sahen: Vorschau einer Version ([Ansichtsmodi der Versionsvorschau](../../../../eng-us/features/6.0/version-preview-view-modes.md)).
- [ ] Viele Objekte einer Klasse: Export mit xrowextract als CSV, XML oder JSON ([Von Exponential weg migrieren](../../../installation/migration/exponential-verlassen.md#1-was-sie-exportieren-sollten)).
- [ ] Formulareinsendungen als CSV: `./console ext:bccie:export --object=<id> --format=csv` (dieselbe Seite).
- [ ] Audit-Einträge: der Audit-Export (`/audit/export`) oder `./console exp:audit export` ([Audit-Leitfaden](../../../../eng-us/bc/6.0/audit.md)).
- [ ] Eine Aufzeichnung, was wann von wem gesucht wurde.

## Personenbezogene Daten in Auskünften

- [ ] Auskünfte vor der Herausgabe auf personenbezogene Daten Dritter geprüft.
- [ ] Exporte von Formulareinsendungen und Benutzerdaten unter Zugriffskontrolle behandelt und gelöscht, wenn der Antrag abgeschlossen ist.
- [ ] Audit-Einträge enthalten nach der eingestellten Zeit pseudonymisierte Felder; wissen Sie, was sie zeigen können und was nicht ([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)).

## Proaktiv veröffentlichen

- [ ] Eine Seite für ein Offenlegungsprotokoll oder einen Veröffentlichungsplan, wenn Ihr Gesetz eine erwartet.
- [ ] Häufig nachgefragte Informationen als Seiten oder Datensätze veröffentlicht ([Bereitschaft für offene Daten](bereitschaft-fuer-offene-daten.md)).
- [ ] Regeln zu Schriftgut und Informationsfreiheit aufeinander abgestimmt ([Checkliste Schriftgut](checkliste-schriftgut.md)).

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Checkliste Schriftgut](checkliste-schriftgut.md)
- Der Bereich Transparenz und Informationsfreiheit dieser Hilfe für Verwaltungen (von einem anderen Autor geschrieben)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
