---
title: Schwerpunkte der Anforderungen für Parlamente
audience: government, project-manager, admin
category: public-sector
order: 50
summary: Die Anforderungen, die für die Website eines Parlaments am meisten wiegen - Vollständigkeit und Dauerhaftigkeit der Aufzeichnung, offene Daten, Barrierefreiheit von Dokumenten und Video, Sprachen, Sicherheit und Last in Spitzenzeiten - und was Exponential bietet.
keywords: Anforderungen, Parlament, Aufzeichnung, Dauerhaftigkeit, offene Daten, Barrierefreiheit, Untertitel, Protokolle, Sprachen, Sicherheit, Last, Spitzenverkehr, requirements, parliament, record, permanence, open data, DCAT, accessibility, captions, transcripts, languages, security, load, peak traffic
type: help
---
# Schwerpunkte der Anforderungen für Parlamente

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren der Website eines
Parlaments oder einer gesetzgebenden Versammlung.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Vollständigkeit und Dauerhaftigkeit der Aufzeichnung](#vollständigkeit-und-dauerhaftigkeit-der-aufzeichnung)
2. [Offene Daten](#offene-daten)
3. [Barrierefreiheit](#barrierefreiheit)
4. [Sprachen](#sprachen)
5. [Sicherheit und Last](#sicherheit-und-last)

## Vollständigkeit und Dauerhaftigkeit der Aufzeichnung

| Anforderung | Exponential | Ihre Arbeit |
|---|---|---|
| Stabile Adressen für jeden Eintrag der Aufzeichnung, über Jahrzehnte | URL-Aliase; alte Aliase leiten nach einer Umbenennung oder Verschiebung um ([Umleitungen und verschobene Inhalte](../../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md)) | eine Benennungsregel (Wahlperiode, Nummer), die sich nicht ändert |
| Nichts wird stillschweigend entfernt | Versionen und das Audit-Protokoll | ein Verfahren für das Entfernen |
| Vollständiger Verlauf jeder Seite | die Grenze des Versionsverlaufs: `[VersionManagement] DefaultVersionHistoryLimit` (45 in der mitgelieferten `settings/content.ini`) und `VersionHistoryClass[<class id>]` je Klasse | sie für die Klassen der Aufzeichnung anheben oder die Aufzeichnung in den Quellsystemen halten |
| Langzeitbewahrung | nichts eingebaut | in Ihr Schriftgutverwaltungssystem archivieren; [ISO 14721 (OAIS)](https://www.iso.org/standard/57284.html) beschreibt die Langzeitbewahrung |

## Offene Daten

Parlamentarische Daten (Mitglieder, Abstimmungen, Gesetzentwürfe) werden vielfach weiterverwendet. In der EU legt die
Open-Data-Richtlinie, [Richtlinie (EU) 2019/1024](https://eur-lex.europa.eu/eli/dir/2019/1024/oj), Regeln für die
Weiterverwendung von Informationen des öffentlichen Sektors fest. Exponential bietet:

| Kanal | Lesen |
|---|---|
| RSS-Exporte von Tagesordnungen, Gesetzentwürfen und Abstimmungen | [RSS-Feeds](../../../../anleitungen/rss-feeds.md) |
| Die REST-API für Inhalte | [REST API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md) |
| Datendateien (CSV, XML) als Objekte der Klasse File | [Dateien und Downloads](../../../../../eng-us/help/creators/website-creators/files-and-downloads.md) |

Einen [DCAT](https://www.w3.org/TR/vocab-dcat-3/)-Katalog erzeugt es nicht; beschreiben Sie Datensätze auf einer Seite
und veröffentlichen Sie, wenn Sie einen Katalog brauchen, einen aus den Quellsystemen. Nennen Sie eine offene Lizenz
([Open Definition](https://opendefinition.org/od/2.1/en/)).

## Barrierefreiheit

Parlamente veröffentlichen lange Dokumente und Video. In der EU erfasst die
[Richtlinie (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj) die Websites öffentlicher Stellen; der
eigene Status von Parlamenten hängt von der nationalen Umsetzung ab. Status von Exponential: Administration admin4
**Partly complies** mit WCAG 2.2 AA; EN 301 549 und Section 508 **Unverified**; öffentliche Designs nicht geprüft
([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)).

| Inhalt | Praxis |
|---|---|
| Video der Sitzungen | Untertitel und das von der Videoseite verlinkte Protokoll |
| Gesetzentwürfe und Berichte | eine Zusammenfassung in HTML auf der Seite des Gesetzentwurfs; barrierefreie PDFs aus dem Gesetzgebungssystem |
| Abstimmungstabellen | echte Tabellen mit Kopfzeilen (der Datentyp Matrix gibt eine Tabelle aus) |

## Sprachen

Mehrere Amtssprachen sind häufig. Übersetzungen je Objekt und ein Siteaccess je Sprache
([Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md)); Dokumente der Aufzeichnung gibt es meist je Sprache als
eigene Dateien, die aus dem übersetzten Objekt verknüpft sind.

## Sicherheit und Last

| Thema | Exponential | Lesen |
|---|---|---|
| Konten der Beschäftigten | zweistufige Anmeldung, verpflichtend einstellbar | [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) |
| Status | OWASP Top 10 **Partly complies** | [Sicherheitsstandards](../../../../../eng-us/standards/security.md) |
| Spitzen während wichtiger Abstimmungen | Ansichts-Cache, statischer Cache, Antwort-Cache von Velocity | [Statischer Cache und Velocity](../../../../../eng-us/help/creators/website-creators/static-cache-and-velocity.md) |
| Defacement oder Manipulation erkannt | Audit-Protokoll, Dateikonsistenzprüfung | [Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md), [Dateikonsistenzprüfung](../../../../../eng-us/features/6.0/file-consistency-check.md) |

Führen Sie vor der ersten wichtigen Abstimmung nach dem Start einen Lasttest mit eingeschalteten Caches durch.

## Verwandte Seiten

- [Parlamente](README.md)
- [Die ersten 90 Tage für Parlamente](die-ersten-90-tage.md)
- [Leistung und Caching](../../../../../eng-us/help/creators/website-creators/performance-and-caching.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
