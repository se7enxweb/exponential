---
title: Vorlage für eine Bewertungscheckliste für ein CMS im öffentlichen Sektor
audience: government, project-manager, sales-marketing
category: public-sector
order: 20
summary: Eine Checkliste zum Kopieren und Anpassen, mit der ein Bewertungsgremium Exponential (oder ein anderes CMS) gegen die Anforderungen einer Ausschreibung prüft, mit den Nachweisen, die für jede Zeile im Repository zu suchen sind.
keywords: Bewertungscheckliste, Wertung einer Ausschreibung, Angebotswertung, Bewertung eines CMS, Erfüllungsmatrix, Nachweise, Beschaffung, öffentlicher Sektor, Vorlage, evaluation checklist, tender evaluation, bid evaluation, CMS assessment, compliance matrix, evidence, procurement, public sector, template
type: help
---
# Vorlage für eine Bewertungscheckliste für ein CMS im öffentlichen Sektor

Diese Seite richtet sich an die Mitglieder eines Bewertungsgremiums und die Beschaffungsstelle, die ihre Arbeit
vorbereitet. Sie gibt eine Checkliste, die Sie in Ihre eigene Bewertungsakte kopieren können, und für jede Zeile die
Stelle, an der die Nachweise für Exponential zu finden sind. Die Quellen der Nachweise sind die Seiten zu den Standards
in `doc/eng-us/standards/` und die Funktionsdokumentation des Repositorys; nichts auf dieser Seite ersetzt Ihre eigenen
Tests.

> **Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung.

## Inhalt
1. [Wie Sie die Checkliste verwenden](#wie-sie-die-checkliste-verwenden)
2. [Kopf der Bewertungsakte](#kopf-der-bewertungsakte)
3. [Funktionale Prüfungen](#funktionale-prüfungen)
4. [Prüfungen zur Barrierefreiheit](#prüfungen-zur-barrierefreiheit)
5. [Prüfungen zu Sicherheit und Datenschutz](#prüfungen-zu-sicherheit-und-datenschutz)
6. [Prüfungen zu Sprachen und Inhalten](#prüfungen-zu-sprachen-und-inhalten)
7. [Prüfungen zu Betrieb, Support und Ausstieg](#prüfungen-zu-betrieb-support-und-ausstieg)
8. [Das Ergebnis festhalten](#das-ergebnis-festhalten)

## Wie Sie die Checkliste verwenden

1. Kopieren Sie die Zeilen unten in Ihre Bewertungsakte und löschen Sie die, nach denen Ihre Ausschreibung nicht gefragt
   hat.
2. Halten Sie den Wortlaut jeder Zeile identisch mit der Anforderung in Ihrer Ausschreibung, damit eine prüfende Person
   ihn nachverfolgen kann.
3. Halten Sie für jede Zeile drei Dinge fest: die Antwort des Bieters, den geprüften Nachweis und Ihren Befund (erfüllt,
   teilweise erfüllt, nicht erfüllt, nicht belegt).
4. Behandeln Sie „Nicht geprüft“ in der eigenen Dokumentation eines Herstellers als „nicht belegt“, sofern das Angebot keine
   Nachweise ergänzt. Die Seiten zu den Standards von Exponential verwenden genau diese Statusangaben: „Erfüllt“,
   „Teilweise erfüllt“, „Nicht geprüft“ und „Nicht erfüllt“
   ([Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md)).
5. Vergeben Sie danach die Punkte, mit der Vorlage für einen Bewertungsbogen (eine eigene Seite dieses Ordners).

## Kopf der Bewertungsakte

```text
Tender:            <reference and title>
Bid:               <bidder name> / product: Exponential <version>
Evaluator:         <name, role>
Date(s):           <dates of review, demo and proof of concept>
Conflict of interest declared: <yes/no, date>
Sources checked:   repository at tag/commit <...>; demo on <date>; proof of concept on <date>
```

## Funktionale Prüfungen

| # | Prüfung | Wo die Nachweise für Exponential sind |
|---|---|---|
| F1 | Strukturierte Inhaltstypen, von der Administration festgelegt | [Inhaltsmodell und Bearbeitung](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| F2 | Entwürfe, Versionen, Vergleich und Wiederherstellung | [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md), [Entwürfe und ausstehende Objekte](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md) |
| F3 | Freigabe vor dem Veröffentlichen | [Workflows](../../../../anleitungen/workflows.md) |
| F4 | Rollen und Berechtigungen je Sektion und Inhaltstyp | [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md), [Sektionen](../../../../anleitungen/sektionen.md) |
| F5 | Mehrere Websites aus einer Installation | [Multisite einrichten](../../../ersteller/website-ersteller/multisite-einrichten.md) |
| F6 | Formulare, die Eingaben sammeln | [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md) |
| F7 | Suche auf der Website | [Suche auf der Website](../../../ersteller/website-ersteller/suche-auf-der-website.md), [Suche mit ezfind und Solr](../../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md) |
| F8 | RSS-Feeds und PDF-Export | [RSS-Feeds](../../../../anleitungen/rss-feeds.md), [PDF-Exporte](../../../../anleitungen/pdf-exporte.md) |
| F9 | Inhalts-API für andere Systeme | [REST API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md) |

## Prüfungen zur Barrierefreiheit

| # | Prüfung | Nachweis |
|---|---|---|
| A1 | Status gegenüber WCAG 2.2 AA je Bestandteil angegeben | [Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md): admin4/admin4l „Teilweise erfüllt“; öffentliche Designs nicht geprüft |
| A2 | Position zu EN 301 549 und Section 508 | dieselbe Seite: beide „Nicht geprüft“; es gibt kein VPAT und keinen Accessibility Conformance Report |
| A3 | Wer die öffentliche Website barrierefrei macht | das eigene Design und der Testplan des Bieters; siehe [Die Barrierefreiheit testen](../../../ersteller/website-ersteller/barrierefreiheit-testen.md) |
| A4 | Erklärung zur Barrierefreiheit für die Website | der Bieter muss sie schreiben; keine Vorlage mitgeliefert ([Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md#bekannte-lücken-2)) |

## Prüfungen zu Sicherheit und Datenschutz

| # | Prüfung | Nachweis |
|---|---|---|
| S1 | Zuordnung zu den OWASP Top 10 | [Sicherheitsstandards](../../../../standards/sicherheit.md): „Teilweise erfüllt“; ASVS „Nicht geprüft“ |
| S2 | Zweistufige Anmeldung | [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) (Erweiterung, auf Wunsch) |
| S3 | Audit-Protokoll, wer was getan hat | [Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md), standardmäßig an |
| S4 | Offenlegung von Schwachstellen und unterstützte Versionen | `SECURITY.md` im Wurzelverzeichnis des Repositorys, zusammengefasst in [Sicherheitsstandards](../../../../standards/sicherheit.md#offenlegung-von-schwachstellen) |
| S5 | Härtungsplan für den Produktivbetrieb | [Sicherheitshärtung](../../../sicherheitshaertung.md), [Kapitel 13](../../../../installation/13-sicherheitshaertung.md) |
| P1 | Einwilligungsnachweise, Löschung, Datenminimierung in Protokollen | [Datenschutzstandards](../../../../standards/datenschutz.md) |
| P2 | Cookie-Einwilligung | keine mitgeliefert: Der Bieter muss eine ergänzen, wenn die Website Analyse oder Einbettungen verwendet ([Datenschutzstandards](../../../../standards/datenschutz.md#eprivacy-richtlinie-cookies)) |

## Prüfungen zu Sprachen und Inhalten

| # | Prüfung | Nachweis |
|---|---|---|
| L1 | Inhalte in mehreren Sprachen mit Fallback | [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md) |
| L2 | Sprachen von rechts nach links | [Sprachen von rechts nach links](../../../../funktionen/6.0/rechts-nach-links.md) |
| L3 | Administration in der Sprache der Beschäftigten | [Sprachen](../../../sprachen.md) |

## Prüfungen zu Betrieb, Support und Ausstieg

| # | Prüfung | Nachweis |
|---|---|---|
| O1 | Unterstützte PHP-Versionen und Datenbanken | [Anforderungen](../../../../installation/02-voraussetzungen.md) |
| O2 | Sicherungen von Datenbank und Dateien | [Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md) |
| O3 | Upgrade-Pfad und -Prüfung | [Aktualisieren](../../../../anleitungen/aktualisieren.md), [Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md) |
| O4 | Release-Richtlinie | [Quellcodeverwaltung und Releases](../../../../standards/versionsverwaltung-und-releases.md) |
| O5 | Support-Angebot mit benannten Reaktionszeiten | das Angebot selbst; vergleichen Sie mit [Support-Stufen und Reaktionszeiten](../../../partner/support-stufen-und-reaktionszeiten.md) |
| X1 | Vollständiger Datenexport beim Ausstieg | [Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md) |
| X2 | Lizenz ohne Gebühr und mit verfügbarem Quellcode | [Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md) |

## Das Ergebnis festhalten

```text
Line | Bid answer (quote) | Evidence checked (link, test, demo minute) | Finding | Evaluator initials
F1   | ...                | ...                                         | met     | ...
```

Bewahren Sie die Links zu den Nachweisen in der Akte auf. Ein Befund ohne Quelle ist nicht vertretbar, wenn ein Bieter
um ein Debriefing bittet.

## Verwandte Seiten
- [Bewertung: Übersicht](README.md)
- [Beschaffung: Übersicht](../README.md)
- [Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md)
- [Sicherheitsstandards](../../../../standards/sicherheit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
