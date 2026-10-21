---
title: Was ein Gericht veröffentlicht
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: Die Inhalte, die Gerichte und Spruchkörper typischerweise veröffentlichen - Hinweise für Rechtsuchende, Standorte, Informationen zu Verhandlungen, Entscheidungen, Vorschriften, Formulare und Gebühren, Nachrichten - und was jede Art von Exponential braucht.
keywords: Website eines Gerichts, Spruchkörper, Rechtsuchende, Hinweise, Gerichtsstandorte, Verhandlungen, Terminrollen, Entscheidungen, Urteile, Vorschriften, Formulare, Gebühren, Umfang, court website, tribunal, court users, guidance, court locations, hearings, court lists, decisions, judgments, rules, forms, fees, scope
type: help
---
# Was ein Gericht veröffentlicht

Diese Seite richtet sich an Projektverantwortliche und Website-Ersteller an einem Gericht oder Spruchkörper, die den
Umfang einer Website festlegen. Sie listet auf, was Gerichte typischerweise veröffentlichen (allgemeine Praxis im
öffentlichen Sektor; Rechtsordnungen unterscheiden sich stark), und was jede Art von Inhalt braucht.

## Inhalt

1. [Die Inhalte](#die-inhalte)
2. [Was das CMS können muss](#was-das-cms-können-muss)
3. [Was nicht auf die Website gehört](#was-nicht-auf-die-website-gehört)
4. [Zu klärende Fragen](#zu-klärende-fragen)

## Die Inhalte

| Inhalt | Zielgruppe | Rhythmus |
|---|---|---|
| Hinweise für Rechtsuchende: ein Verfahren einleiten, erwidern, eine Verhandlung vorbereiten, Kosten, verfügbare Hilfe | Parteien, oft ohne Anwalt | stabil, bei Änderungen der Vorschriften überprüft |
| Gerichtsstandorte: Anschriften, Öffnungszeiten, Barrierefreiheit, Einlasskontrolle, Dolmetscher | alle, die erscheinen | gelegentlich |
| Informationen zu Verhandlungen: Terminrollen, wo veröffentlicht, Änderungen, Schließungen | Parteien, Presse | täglich, kurzfristig |
| Entscheidungen und Urteile, wo veröffentlicht | Öffentlichkeit, Anwaltschaft, Forschende, Presse | laufend |
| Vorschriften, Verfahrensanweisungen, Formulare und Gebühren | Anwaltschaft, Parteien | Änderungen zu den in Vorschriften festgelegten Daten |
| Nachrichten und Presseinformationen | Presse, Öffentlichkeit | gelegentlich |
| Über das Gericht: Zuständigkeit, öffentliche Aufgaben der Richterinnen und Richter, Jahresbericht, Beschwerden | alle | jährlich |

## Was das CMS können muss

| Bedarf | Exponential | Lesen |
|---|---|---|
| Entscheidungen in einem eingeschränkten Veröffentlichungsablauf | eine eigene Sektion, eine kleine Rolle, ein Ereignis *Freigeben* | [Struktur, Sektionen und Rollen für Gerichte](struktur-sektionen-und-rollen.md) |
| Genauer Text, keine stillschweigende Änderung | Versionen und das Audit-Protokoll | [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md), [Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md) |
| Eine Entscheidung schnell zurückziehen | verbergen oder in den Papierkorb verschieben und Versionen entfernen | [Entscheidungen mit Sorgfalt veröffentlichen](entscheidungen-mit-sorgfalt-fuer-personenbezogene-daten-veroeffentlichen.md) |
| Vorschriften, die ab einem Datum gelten | ein Attribut für das Geltungsdatum; ein Workflow-Ereignis *Bis Datum warten*, wenn die Veröffentlichung warten muss | [Workflows](../../../../anleitungen/workflows.md) |
| Schnelle Änderungen und Schließungen von Verhandlungen | ein Ordner für Hinweise, den die Startseite zeigt; dringendes Veröffentlichen ohne Freigabe für das diensthabende Team | [Der Layout-Editor](../../../layout-editor.md) |
| Formulare zum Herunterladen | Objekte der Klasse File | [Dateien und Downloads](../../../../../eng-us/help/creators/website-creators/files-and-downloads.md) |
| Hinweise in einfacher Sprache | eine Klasse Ratgeber mit einer kurzen Zusammenfassung | [Inhaltsmodell für Gerichte](inhaltsmodell.md) |

## Was nicht auf die Website gehört

| Nicht | Stattdessen |
|---|---|
| Verfahrensdokumente laufender Verfahren hochladen | sie gehören in das Fachverfahren der Gerichtsverwaltung |
| Terminrollen mit mehr personenbezogenen Daten veröffentlichen, als die Vorschriften erlauben | nur veröffentlichen, was die Vorschriften verlangen; die Felder mit der Justiz abstimmen |
| Eine Entscheidung erscheinen lassen, bevor sie anonymisiert und freigegeben ist | ein Prüfschritt und eine eigene Sektion |
| Die Einreichung von Dokumenten über ein Webformular anbieten | Systeme für den elektronischen Rechtsverkehr haben eigene Regeln für Sicherheit und Schriftgut |

## Zu klärende Fragen

- [ ] Welche Entscheidungen werden veröffentlicht, von wem, nach welcher Anonymisierung und wie lange?
- [ ] Welche Informationen zu Verhandlungen werden veröffentlicht, und aus welchem System?
- [ ] Wer veröffentlicht eine kurzfristige Schließung?
- [ ] In welchen Sprachen müssen Hinweise verfügbar sein?

## Verwandte Seiten

- [Gerichte](README.md)
- [Inhaltsmodell für Gerichte](inhaltsmodell.md)
- [Ihre Website planen](../../../../../eng-us/help/creators/website-creators/planning-your-website.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
