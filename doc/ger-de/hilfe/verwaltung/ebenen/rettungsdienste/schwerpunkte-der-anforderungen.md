---
title: Schwerpunkte der Anforderungen für Rettungsdienste
audience: government, project-manager, admin
category: public-sector
order: 50
summary: Die Anforderungen, die für die Website eines Rettungsdienstes am meisten wiegen - Verfügbarkeit unter Last und bei Ausfällen, Geschwindigkeit beim Veröffentlichen, Richtigkeit, Barrierefreiheit unter Stress, Sprachen, Sicherheit - und was Exponential bietet.
keywords: Anforderungen, Rettungsdienste, Verfügbarkeit, Last, Ausfallsicherheit, statischer Cache, Geschwindigkeit, Richtigkeit, Barrierefreiheit, einfache Sprache, Sprachen, Sicherheit, NIS2, requirements, emergency services, availability, load, failover, static cache, speed, accuracy, accessibility, plain language, languages, security
type: help
---
# Schwerpunkte der Anforderungen für Rettungsdienste

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren der Website eines
Rettungsdienstes.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Verfügbarkeit](#verfügbarkeit)
2. [Geschwindigkeit und Richtigkeit](#geschwindigkeit-und-richtigkeit)
3. [Barrierefreiheit unter Stress](#barrierefreiheit-unter-stress)
4. [Sprachen](#sprachen)
5. [Sicherheit](#sicherheit)

## Verfügbarkeit

| Anforderung | Exponential | Ihre Arbeit |
|---|---|---|
| Viele Lesende gleichzeitig bedienen | Ansichts-Cache, statischer Cache, Antwort-Cache von Velocity ([Warnungen, Verfügbarkeit, Wartungsmodus und statischer Cache](warnungen-verfuegbarkeit-wartung-und-statischer-cache.md)) | ein Lasttest bei der erwarteten Spitze |
| Weiter ausliefern, wenn PHP oder die Datenbank ausfällt | Dateien des statischen Cache liefert der Webserver aus, ohne Exponential zu starten | statischer Cache konfiguriert und erzeugt |
| Geplante Arbeiten ohne Funkstille | Wartungsmodus mit einer klaren Seite, Status 503 und `Retry-After` ([Wartungsmodus](../../../../anleitungen/wartungsmodus.md)) | Wartung außerhalb von Risikozeiten |
| Einen Ausfall des Hostingstandorts überstehen | nichts im Produkt | ein zweiter Standort, DNS-Failover oder ein CDN, das Kopien weiter ausliefert; Sicherungen durch Wiederherstellen getestet ([Sicherungen und Wiederherstellungen](../../../ersteller/website-ersteller/backups-und-wiederherstellungen.md)) |

## Geschwindigkeit und Richtigkeit

| Anforderung | Exponential |
|---|---|
| In einer Minute veröffentlichen | eine kurze Klasse, eine Bereitschaftsrolle außerhalb der Freigabe ([Struktur](struktur-sektionen-und-rollen.md)) |
| Zeigen, wann Informationen zuletzt aktualisiert wurden | ein Datumsattribut `updated`, oben angezeigt |
| Schnell berichtigen, die Spur erhalten | bearbeiten und veröffentlichen; Versionen und das Audit-Protokoll bewahren den früheren Text ([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md)) |
| Veraltete Informationen automatisch entfernen | `hide.php` mit einer Endzeit ([Cronjobs](../../../../anleitungen/cronjobs.md)) |

## Barrierefreiheit unter Stress

Menschen, die während eines Einsatzes lesen, können Angst haben, einen kleinen Bildschirm nutzen, eine schwache
Verbindung haben oder assistive Technologie verwenden. In der EU erfasst die
[Richtlinie (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj) die Websites öffentlicher Stellen, und
[WCAG 2.2](https://www.w3.org/TR/WCAG22/) ist die Referenz. Status von Exponential: Administration admin4 erfüllt
WCAG 2.2 AA **teilweise**; EN 301 549 und Section 508 **Nicht geprüft**; öffentliche Designs nicht geprüft
([Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md)).

| Praxis | Warum |
|---|---|
| Anweisungen als Text, zuerst | Bilder, Karten und Video versagen bei schwachen Verbindungen und für Nutzerinnen und Nutzer von Screenreadern |
| Leichte Seiten | statische, zwischengespeicherte Seiten laden am schnellsten |
| Einfache Sprache ([plainlanguage.gov](https://www.plainlanguage.gov/)) | Menschen unter Stress lesen weniger |
| Keine Informationen nur in Farbe | Warnstufen als Wörter und als Farben |

## Sprachen

Einsatzanweisungen in den Sprachen, die in Ihrem Gebiet gesprochen werden, retten Leben. Übersetzungen je Objekt und
ein Siteaccess je Sprache ([Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md)); bereiten Sie übersetzte Vorlagen
häufiger Anweisungen im Voraus vor, freigegeben vor jedem Einsatz.

## Sicherheit

| Maßnahme | Lesen |
|---|---|
| Zweistufige Anmeldung für Veröffentlichende | [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) |
| Kontosperre nach fehlgeschlagenen Anmeldungen (standardmäßig aus) und ein Anfrageschutz | [Sicherheitshärtung](../../../sicherheitshaertung.md) |
| Audit-Alarme bei verdächtigen Anmeldeaktivitäten | [Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md) |
| Ein von einem Angreifer veröffentlichter falscher Einsatz ist ein ernstes Risiko | minimale Rechte, Überwachung, ein Verfahren, ihn sofort herunterzunehmen |

Rettungsdienste können je nach nationalem Recht wesentliche Einrichtungen im Sinne von NIS2,
[Richtlinie (EU) 2022/2555](https://eur-lex.europa.eu/eli/dir/2022/2555/oj), sein; anderswo ist
[NIST CSF 2.0](https://www.nist.gov/cyberframework) ein verbreiteter Rahmen. Der eigene Status von Exponential gegenüber
OWASP: **Teilweise erfüllt** ([Sicherheitsstandards](../../../../standards/sicherheit.md)).

## Verwandte Seiten

- [Rettungsdienste](README.md)
- [Die ersten 90 Tage für Rettungsdienste](die-ersten-90-tage.md)
- [HTTP-Caching und CDNs](../../../ersteller/website-ersteller/http-caching-und-cdns.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
