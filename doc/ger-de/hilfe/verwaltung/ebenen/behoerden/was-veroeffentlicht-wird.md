---
title: Was eine Behörde veröffentlicht
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: Die Inhalte, die Regulierungsbehörden, Aufsichtsbehörden und nachgeordnete Behörden typischerweise veröffentlichen - Leitfäden, Entscheidungen und Durchsetzung, Register, Programme, Daten und Informationen über die Organisation - und was jede Art von Exponential braucht.
keywords: Website einer Behörde, Regulierungsbehörde, Leitfäden, Durchsetzungsentscheidungen, Register, Programme, Förderungen, Statistiken, offene Daten, Informationen über die Organisation, Umfang, agency website, regulator, guidance, enforcement decisions, registers, programs, grants, statistics, open data, corporate information, scope
type: help
---
# Was eine Behörde veröffentlicht

Diese Seite richtet sich an Projektverantwortliche und Website-Ersteller einer Regulierungsbehörde, Aufsichtsbehörde
oder nachgeordneten Behörde, die den Umfang einer Website festlegen. Sie listet auf, was Behörden typischerweise
veröffentlichen (allgemeine Praxis im öffentlichen Sektor), und was jede Art von Inhalt vom System braucht.

## Inhalt

1. [Die Inhalte](#die-inhalte)
2. [Was das CMS können muss](#was-das-cms-können-muss)
3. [Register und Daten](#register-und-daten)
4. [Zu klärende Fragen](#zu-klärende-fragen)

## Die Inhalte

| Inhalt | Zielgruppe | Form und Rhythmus |
|---|---|---|
| Leitfäden: erklärte Regeln, wie man sie einhält, Verhaltenskodizes | regulierte Akteure, Fachleute | lange Seiten und Dokumente; versioniert; Geltungsdaten |
| Entscheidungen und Durchsetzung: Bescheide, Sanktionen, Ergebnisse von Prüfungen | Öffentlichkeit, Presse, regulierte Akteure | je Fall; können Organisationen nennen |
| Register: zugelassene oder registrierte Akteure | Öffentlichkeit, Unternehmen | Listen, die sich täglich ändern, oft aus einer Datenbank |
| Programme: was die Behörde fördert oder erbringt, wie man sich bewirbt | Antragstellende | Aufrufe mit Fristen |
| Berichte und Statistiken | Fachleute, Forschende, Aufsicht | regelmäßig, oft als PDF plus Datendateien |
| Konsultationen zu neuen Leitfäden | regulierte Akteure | feste Zeiträume, Stellungnahmen |
| Nachrichten, Hinweise und Warnungen (zum Beispiel vor Betrug oder zu einem Produkt) | Öffentlichkeit | dringend, kurzlebig |
| Informationen über die Organisation: Leitung, Jahresbericht, Haushalt, Beschwerdeweg | alle | jährlich |

## Was das CMS können muss

| Bedarf | Exponential | Lesen |
|---|---|---|
| Leitfäden mit Versionen und „was sich geändert hat“ | Versionen, Vergleich, neuer Entwurf aus einer alten Version | [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md) |
| Ein Status je Dokument (in Kraft, ersetzt, zurückgezogen) | Objektzustände, in Templates angezeigt und in Rollen verwendbar | [Objektzustände](../../../../anleitungen/objektzustaende.md) |
| Rechtliche Prüfung vor dem Veröffentlichen | ein Ereignis *Freigeben* mit dem Rechtsteam als Freigebenden | [Workflows](../../../../anleitungen/workflows.md) |
| Abonnenten über Änderungen informieren | Benachrichtigungen für einen Teilbaum; RSS | [Benachrichtigungen](../../../../anleitungen/benachrichtigungen-administrator.md), [RSS-Feeds](../../../../anleitungen/rss-feeds.md) |
| Getrennte Einstiege für Öffentlichkeit und Fachleute | zwei Ordner oder zwei Siteaccesses über gemeinsamen Inhalten | [Multisite einrichten](../../../../../eng-us/help/creators/website-creators/multisite-setup.md) |
| Konsultationen | der Informationssammler | [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md) |
| Warnungen auf der Startseite | ein Ordner oder ein Block des Layout-Editors, den die Startseite zeigt | [Der Layout-Editor](../../../layout-editor.md) |

## Register und Daten

Register liegen meist im Fall- oder Zulassungssystem der Behörde. Exponential ist keine Registerdatenbank; die üblichen
Muster sind:

| Muster | Wann |
|---|---|
| Auf die eigene Suche des Registers verlinken | das Register ist groß oder ändert sich ständig |
| Einen regelmäßigen Export als Objekt der Klasse File veröffentlichen (CSV, mit Datum im Namen) | das Register wird als offene Daten veröffentlicht |
| Einträge über die REST-API oder ein Skript als Inhaltsobjekte importieren | das Register ist klein und ändert sich selten ([REST API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md)) |

Beschreiben Sie Datensätze für die Veröffentlichung als offene Daten mit einem Standardvokabular wie
[DCAT 3](https://www.w3.org/TR/vocab-dcat-3/); in der EU legt die Open-Data-Richtlinie,
[Richtlinie (EU) 2019/1024](https://eur-lex.europa.eu/eli/dir/2019/1024/oj), Regeln für die Weiterverwendung fest.
Exponential erzeugt keine DCAT-Kataloge; dafür braucht es ein Template oder einen eigenen Katalog.

## Zu klärende Fragen

- [ ] Welche Leitfäden sind rechtlich verbindlich, und wer gibt jede Änderung frei?
- [ ] Müssen frühere Fassungen von Leitfäden öffentlich verfügbar bleiben oder nur auf Anfrage?
- [ ] Welche Durchsetzungsentscheidungen werden veröffentlicht, wie lange und mit welchen entfernten Namen?
- [ ] Welche Register werden veröffentlicht, und aus welchem System?

## Verwandte Seiten

- [Behörden](README.md)
- [Leitfäden für regulierte Akteure](leitfaeden-fuer-regulierte-akteure.md)
- [Inhaltsmodell für Behörden](inhaltsmodell.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
