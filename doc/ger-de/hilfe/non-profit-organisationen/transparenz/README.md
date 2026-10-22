---
title: Transparenz für Non-Profit-Organisationen
audience: non-profit, government, businesses, editor
category: non-profits
order: 10
summary: Wie eine Non-Profit-Organisation ihre Exponential-Site nutzt, um offen über Geld, Organisationsführung und Entscheidungen zu informieren - Jahresveröffentlichungen und Finanzdokumente als Downloads, Seiten für Vorstand und Protokolle, Versionsverlauf und das manipulationssichere Audit-Protokoll als Rechenschaft sowie offene Daten über RSS-Feeds, Exporte und die REST-API - mit dem, was die Software bereitstellt, und dem, was Ihr eigener Prozess bleibt.
keywords: Transparenz, Rechenschaft, Jahresveröffentlichungen, Jahresabschluss, Gemeinnützigkeit, Meldungen, Vorstand, Protokolle, Organisationsführung, Versionen, Audit-Protokoll, offene Daten, RSS, REST-API, Vertrauen, transparency, accountability, annual publications, financial statements, 501(c)(3), charity filings, board, minutes, governance, versions, audit trail, open data, REST API, trust
type: help
---
# Transparenz für Non-Profit-Organisationen

Dieser Ordner richtet sich an Geschäftsführungen, Schatzmeister, Vorstandssekretariate und Kommunikationsmitarbeiter.
Spender, Geldgeber, Aufsichtsbehörden und die Menschen, für die Sie arbeiten, erwarten zunehmend zu sehen, wie eine
Non-Profit-Organisation geführt wird: wohin das Geld geht, wer entscheidet und was entschieden wurde. In manchen
Ländern ist einiges davon gesetzlich vorgeschrieben, zum Beispiel die Offenlegungspflichten von US-Organisationen nach
501(c)(3) oder die Meldungen eingetragener gemeinnütziger Organisationen anderswo; diese Hilfe ist keine
Rechtsberatung, fragen Sie daher Ihren Steuerberater oder Anwalt, was für Sie gilt.

## Inhalt

1. [Was Exponential bereitstellt](#was-exponential-bereitstellt)
2. [Die Seiten dieses Ordners](#die-seiten-dieses-ordners)
3. [Ein Satz von Transparenzseiten](#ein-satz-von-transparenzseiten)
4. [Was Ihr eigener Prozess bleibt](#was-ihr-eigener-prozess-bleibt)

## Was Exponential bereitstellt

| Bedarf | Was es gibt | Quelle |
|---|---|---|
| Dokumente veröffentlichen | Dateiobjekte (Klasse `file`, `ezbinaryfile`), Downloads über `content/download` mit Berechtigungsprüfung | [Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md) |
| Frühere Versionen aufbewahren | Jede Veröffentlichung ist eine Version; standardmäßig 45 pro Objekt, pro Klasse konfigurierbar | [Begrenzung des Versionsverlaufs](../../verwaltung/schriftgut-und-archivierung/grenze-der-versionsgeschichte.md) |
| Zeigen, wer was geändert hat | Das manipulationssichere Audit-Protokoll, standardmäßig eingeschaltet | [Das Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md) |
| Zu einem Datum veröffentlichen | Zeitgesteuerte Veröffentlichung mit einem Workflow | [Zeitgesteuertes Veröffentlichen](../../kunden/alltag/zeitgesteuertes-veroeffentlichen.md) |
| Unterlagen nur für den Vorstand | Bereiche und Rollen | [Bereiche und Objektzustände](../../ersteller/website-ersteller/sektionen-und-objektzustaende.md) |
| Feeds neuer Veröffentlichungen | Exporte als RSS 2.0 und Atom | [RSS-Feeds für Ihre Site](../../ersteller/website-ersteller/rss-feeds-fuer-ihre-website.md) |
| Maschinenlesbarer Zugriff | Die REST-API (`ezprestapi`, v1 und v2) | [REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md) |

## Die Seiten dieses Ordners

| Seite | Was sie beantwortet |
|---|---|
| [Jahresveröffentlichungen als Downloads](jahresveroeffentlichungen-als-downloads.md) | Jahresberichte und Meldungen als Dateien, nach Jahr, barrierefrei, mit Korrekturen |
| [Finanzdokumente](finanzdokumente.md) | Abschlüsse, Budgets, Ausgaben aus Fördermitteln; was veröffentlicht wird und was vertraulich bleibt |
| [Seiten für Vorstand und Protokolle](seiten-fuer-vorstand-und-protokolle.md) | Vorstandsmitglieder, Sitzungen, Tagesordnungen und Protokolle; öffentlich und nur für den Vorstand |
| [Versionen und das Audit-Protokoll](versionen-und-das-audit-protokoll.md) | Zeigen, was auf einer Seite stand und wer sie geändert hat |
| [Offene Daten, RSS und REST](offene-daten-rss-und-rest.md) | Feeds, Exporte und die API für Geldgeber, Netzwerke und Forschende |

Gliederung: [Gliederung der Seite für Jahresveröffentlichungen](../vorlagen/gliederung-der-seite-fuer-jahresveroeffentlichungen.md).

## Ein Satz von Transparenzseiten

Ein typischer Bereich „Rechenschaft“ auf der Site einer Non-Profit-Organisation:

| Seite | Inhalt | Seite in diesem Ordner |
|---|---|---|
| Wer wir sind | Mission, offizieller Name, Registrierungsangaben | |
| Unsere Menschen | Vorstand, Leitung, wie Entscheidungen getroffen werden | [Seiten für Vorstand und Protokolle](seiten-fuer-vorstand-und-protokolle.md) |
| Unser Geld | Jahresveröffentlichungen, Jahresabschlüsse, wohin Spenden gehen | [Finanzdokumente](finanzdokumente.md) |
| Unsere Richtlinien | Datenschutz, Beschwerden, Schutzkonzepte, Interessenkonflikte | [Jahresveröffentlichungen als Downloads](jahresveroeffentlichungen-als-downloads.md) |
| Unsere Wirkung | Ergebnisse, Geschichten, Evaluationen | [Förderergebnisse für Geldgeber](../foerdermittel/foerderergebnisse-fuer-geldgeber.md) |
| Kontakt | Wer Fragen zu diesen Dokumenten beantwortet | |

Die Praxis öffentlicher Stellen ist ein gutes Vorbild; siehe [Transparenz in Exponential im Überblick](../../verwaltung/transparenz-und-informationsfreiheit/transparenz-in-exponential-auf-einen-blick.md)
und [Proaktive Veröffentlichung](../../verwaltung/transparenz-und-informationsfreiheit/proaktive-veroeffentlichung.md).

## Was Ihr eigener Prozess bleibt

- Entscheiden, was veröffentlicht werden muss und bis wann (Gesetz, Aufsichtsbehörde, Geldgeber).
- Personenbezogene oder vertrauliche Daten vor der Veröffentlichung schwärzen: „Schwärzen ist ein manueller Schritt“
  ([Schwärzen ist ein manueller Schritt](../../verwaltung/transparenz-und-informationsfreiheit/schwaerzen-ist-handarbeit.md)).
- Dokumente freigeben, bevor sie online gehen ([Freigabe vor der Veröffentlichung](../../verwaltung/transparenz-und-informationsfreiheit/freigabe-vor-dem-veroeffentlichen.md)).
- Die offiziellen Unterlagen aufbewahren; die Site ist ein Veröffentlichungskanal, nicht Ihr maßgebliches Archiv.

## Verwandte Seiten

- [Non-Profit-Organisationen: hier beginnen](../non-profit-organisationen-hier-beginnen.md)
- [Behörden: hier beginnen](../../verwaltung/verwaltungen-hier-beginnen.md)
- [Transparenz-Checkliste (Behörden)](../../verwaltung/transparenz-und-informationsfreiheit/checkliste-transparenz.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
