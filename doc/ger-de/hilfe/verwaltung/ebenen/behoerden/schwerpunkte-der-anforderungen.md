---
title: Schwerpunkte der Anforderungen für Behörden
audience: government, project-manager, admin
category: public-sector
order: 50
summary: Die Anforderungen, die für die Website einer Behörde am meisten wiegen - Richtigkeit und Nachvollziehbarkeit von Leitfäden, Barrierefreiheit, Sicherheit, personenbezogene Daten in Entscheidungen, offene Daten - und was Exponential bietet und der Behörde überlässt.
keywords: Anforderungen, Behörde, Regulierungsbehörde, Nachvollziehbarkeit, Verlauf von Leitfäden, Audit-Protokoll, Barrierefreiheit, Sicherheit, personenbezogene Daten, Durchsetzung, offene Daten, requirements, agency, regulator, traceability, guidance history, audit trail, accessibility, security, personal data, enforcement, open data, DCAT
type: help
---
# Schwerpunkte der Anforderungen für Behörden

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren, die die Anforderungen
an die Website einer Regulierungsbehörde, Aufsichtsbehörde oder nachgeordneten Behörde festlegen.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Richtigkeit und Nachvollziehbarkeit](#richtigkeit-und-nachvollziehbarkeit)
2. [Barrierefreiheit](#barrierefreiheit)
3. [Sicherheit](#sicherheit)
4. [Personenbezogene Daten in Entscheidungen](#personenbezogene-daten-in-entscheidungen)
5. [Offene Daten](#offene-daten)

## Richtigkeit und Nachvollziehbarkeit

Die Frage „Was stand an diesem Datum in Ihrem Leitfaden?“ muss sich beantworten lassen.

| Bedarf | Exponential | Lücke oder Ihre Arbeit |
|---|---|---|
| Jede veröffentlichte Version bleibt erhalten | archivierte Versionen je Objekt | die Grenze `[VersionManagement] DefaultVersionHistoryLimit` (45 in der mitgelieferten `settings/content.ini`) entfernt beim Erreichen die älteste archivierte Version; heben Sie sie für die Leitfadenklasse mit `VersionHistoryClass[<class id>]` an oder archivieren Sie Versionen anderswo ([Inhaltsmodell und Bearbeitung](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md)) |
| Wer veröffentlicht und freigegeben hat | das Audit-Protokoll, standardmäßig an, hash-verkettet; Elemente der Zusammenarbeit für Freigaben | die Aufbewahrung auf Ihren Aktenplan einstellen ([Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md)) |
| Was sich geändert hat | **Vergleichen** auf der Versionsseite; ein Attribut für den Änderungshinweis | Änderungshinweise schreiben |
| Eine datierte Kopie außerhalb des CMS | PDF-Export eines Teilbaums; Ihr Schriftgutverwaltungssystem | [PDF-Exporte](../../../../anleitungen/pdf-exporte.md) |

Exponential hat keine eingebaute öffentliche Ansicht eines „Versionsarchivs“ früherer Versionen; frühere Versionen sind
in der Administration sichtbar. Muss die Öffentlichkeit frühere Fassungen sehen, veröffentlichen Sie sie als eigene
Objekte (zum Beispiel ein ersetztes Leitfaden-Objekt mit dem Zustand *Ersetzt*).

## Barrierefreiheit

Websites von Behörden sind in vielen Rechtsordnungen Websites des öffentlichen Sektors im Sinne des Rechts der
Barrierefreiheit: der EU-Richtlinie über barrierefreie Websites,
[Richtlinie (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj), und in den Vereinigten Staaten
[Section 508](https://www.section508.gov/) für Bundesbehörden. Status von Exponential: WCAG 2.2 AA **Teilweise erfüllt**
für die Administration admin4, EN 301 549 und Section 508 **Nicht geprüft**, öffentliche Designs nicht geprüft, kein VPAT
([Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md)). Lange Leitfäden brauchen strukturierte
Überschriften in HTML, nicht nur ein PDF.

## Sicherheit

| Thema | Exponential | Lesen |
|---|---|---|
| Konten der Beschäftigten | zweistufige Anmeldung, verpflichtend einstellbar | [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) |
| Minimale Rechte | Rollen mit Einschränkungen nach Sektion und Zustand | [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md) |
| Überwachung | Alarmregeln des Audits wie `brute_force`; Anfrageprotokolle | [Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md), [Anfrageprotokolle](../../../../funktionen/6.0/anfrageprotokolle.md) |
| Status | OWASP Top 10 **Teilweise erfüllt**; ASVS **Nicht geprüft** | [Sicherheitsstandards](../../../../standards/sicherheit.md) |

Behörden gelten je nach nationalem Recht oft als wesentliche oder wichtige Einrichtungen im Sinne von NIS2,
[Richtlinie (EU) 2022/2555](https://eur-lex.europa.eu/eli/dir/2022/2555/oj).

## Personenbezogene Daten in Entscheidungen

Durchsetzungsentscheidungen können Organisationen und manchmal Einzelpersonen nennen. Vereinbaren Sie eine
Veröffentlichungsrichtlinie: was veröffentlicht wird, wie lange und was geschwärzt wird. Dann:

- schwärzen Sie Dokumente vor dem Hochladen; das CMS speichert und liefert aus, was Sie hochladen;
- setzen Sie ein Ende der Veröffentlichung (`hide.php` mit `[HideSettings]`), wo Ihre Richtlinie den Zeitraum begrenzt
  ([Cronjobs](../../../../anleitungen/cronjobs.md));
- denken Sie daran, dass verborgene Inhalte weiterhin in der Datenbank und in alten Versionen stehen; das Entfernen ist
  eine gesonderte Entscheidung;
- Suchmaschinen und Archive können Kopien behalten, nachdem eine Seite verborgen wurde; das Verbergen beendet die
  Auslieferung durch die Website an Besucher, nicht anderswo angefertigte Kopien.

In der EU gilt die [DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj)
([Datenschutzstandards](../../../../standards/datenschutz.md)).

## Offene Daten

Statistiken und Register werden in der EU nach der Open-Data-Richtlinie,
[Richtlinie (EU) 2019/1024](https://eur-lex.europa.eu/eli/dir/2019/1024/oj), oft als offene Daten erwartet. Exponential
liefert Datendateien als Objekte der Klasse File aus und bietet RSS und REST-APIs
([REST API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md)); einen [DCAT](https://www.w3.org/TR/vocab-dcat-3/)-Katalog
erzeugt es nicht von selbst. Verwenden Sie eine offene Lizenz ([Open Definition](https://opendefinition.org/od/2.1/en/))
und nennen Sie sie auf jeder Seite eines Datensatzes.

## Verwandte Seiten

- [Behörden](README.md)
- [Die ersten 90 Tage für Behörden](die-ersten-90-tage.md)
- [Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
