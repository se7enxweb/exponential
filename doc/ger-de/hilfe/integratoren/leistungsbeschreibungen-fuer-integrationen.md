---
title: Leistungsbeschreibungen für Integrationen
audience: integrator, project-manager, partner
category: integration
order: 450
summary: Was eine Leistungsbeschreibung für eine Exponential-Integration enthalten sollte - Umfang je Datenfluss, Lieferumfang, Mitwirkungspflichten des Kunden, Annahmen, Abnahme, Änderungsanträge, Eigentum und Lizenzierung des Codes, Datenschutz, Betrieb und Wartung - mit einer Gliederung als Vorlage.
keywords: Leistungsbeschreibung, Vertrag, Umfang, Lieferumfang, Annahmen, Abnahmekriterien, Änderungsantrag, Mitwirkungspflichten, Eigentum, geistiges Eigentum, Datenverarbeitung, Gewährleistung, Wartung, Vorlage, statement of work, SOW, contract, scope, deliverables, assumptions, acceptance criteria, change request, customer obligations, ownership, intellectual property, GPL, data processing, warranty, maintenance, template
type: help
---
# Leistungsbeschreibungen für Integrationen

Diese Seite richtet sich an Integratoren und ihre Kunden, die die Leistungsbeschreibung (Statement of Work, SOW) für
ein Integrationsprojekt schreiben. Integrationen hängen von Systemen und Menschen ab, die der Integrator nicht steuert,
daher ist eine gute Leistungsbeschreibung genau beim Umfang, bei dem, was der Kunde liefert, und bei dem, was
geschieht, wenn sich das andere System ändert. Diese Seite ist eine praktische Gliederung, **keine Rechtsberatung**;
lassen Sie Verträge von Ihrem eigenen Rechtsbeistand prüfen.

## Inhalt

1. [Die Abschnitte einer Leistungsbeschreibung](#die-abschnitte-einer-leistungsbeschreibung)
2. [Umfang je Datenfluss](#umfang-je-datenfluss)
3. [Mitwirkungspflichten des Kunden und Annahmen](#mitwirkungspflichten-des-kunden-und-annahmen)
4. [Eigentum und Lizenzierung](#eigentum-und-lizenzierung)
5. [Eine Gliederung als Vorlage](#eine-gliederung-als-vorlage)

## Die Abschnitte einer Leistungsbeschreibung

| Abschnitt | Zweck |
|---|---|
| Hintergrund und Ziele | Warum es die Integration gibt, in den Worten des Kunden |
| Umfang | Die Datenflüsse, jeder mit seiner Schnittstellenspezifikation als Anlage |
| Nicht im Umfang | Was ausdrücklich nicht enthalten ist (zum Beispiel: Änderungen in den anderen Systemen, Datenbereinigung an der Quelle) |
| Lieferumfang | Die Integrationserweiterung, Dokumentation, Runbook, Tests, Schulung |
| Mitwirkungspflichten des Kunden | Zugänge, Testinstanzen, Zugangsdaten, Ansprechpartner, Antworten, Entscheidungen, mit Terminen |
| Annahmen | Jede Annahme der Schätzung, damit eine verletzte Annahme zum Änderungsantrag wird statt zum Streit |
| Zeitplan und Meilensteine | Phasen aus dem [Leitfaden](playbook-fuer-integrationsprojekte.md), mit Abhängigkeiten vom Kunden |
| Abnahme | Abnahmefälle je Datenfluss, der Abnahmezeitraum, was als Mangel gilt |
| Änderungsanträge | Wie Änderungen beantragt, geschätzt und genehmigt werden |
| Kaufmännisches | Preismodell, Zahlungsmeilensteine, Auslagen |
| Eigentum und Lizenzierung | Wem welcher Code gehört; Lizenzen dessen, was verwendet wird |
| Datenschutz | Rollen nach Datenschutzrecht, Auftragsverarbeiter, Datenflüsse (keine Rechtsberatung) |
| Hypercare, Gewährleistung, Wartung | Was nach dem Go-live geschieht und wie lange |

## Umfang je Datenfluss

Beschreiben Sie jeden Datenfluss auf dieselbe Weise und fügen Sie seine Schnittstellenspezifikation bei:

| Punkt | Beispiel |
|---|---|
| Name und Richtung | ERP-Produkte nach Exponential |
| Auslöser und Zeitpunkt | Nächtlich um 02:15; ein Delta stündlich von 07:00 bis 19:00 |
| Volumen | Bis zu 25 000 Produkte; bis zu 2 000 Änderungen am Tag |
| Zuordnung | Anlage A, Zuordnungstabelle der Klasse `product` |
| Schlüssel | `erp-product-<ARTNR>` |
| Entfernungen | Produkte, die in einem Vollexport fehlen, werden verborgen |
| Fehler | Ungültige Datensätze werden übersprungen und gemeldet; ein Lauf bricht ab, wenn die Datei weniger als 80 % der Produkte enthält |
| Überwachung | Alarme wie in Anlage B |
| Abnahmefälle | Anlage C |

## Mitwirkungspflichten des Kunden und Annahmen

Schreiben Sie sie als datierte Pflichten, nicht als Hoffnungen:

- [ ] Eine Testinstanz jedes anderen Systems ist bis `<date>` verfügbar, mit Testdaten, die der Produktion ähneln.
- [ ] Zugangsdaten für Test und Produktion werden bis `<date>` über einen vereinbarten sicheren Kanal bereitgestellt.
- [ ] Ein technischer Ansprechpartner je anderem System, der innerhalb eines Werktags antwortet.
- [ ] Entscheidungen der Datenverantwortlichen (Zuordnung, Entfernungen, Stammdaten) innerhalb von fünf Werktagen
      nach einer Frage.
- [ ] Änderungen an den APIs der anderen Systeme werden mindestens `<n>` Wochen vorher angekündigt.
- [ ] Die Exponential-Installation läuft mit einer unterstützten Version (zum Beispiel 6.0.15) und den vereinbarten
      Erweiterungen.

## Eigentum und Lizenzierung

Vereinbaren Sie ausdrücklich:

- **Kundenspezifischer Code** (die Zuordnungserweiterung): gehört meist dem Kunden oder ist ihm exklusiv lizenziert.
- **Allgemeine Konnektoren**, die der Integrator mitbringt oder baut: bleiben oft beim Integrator und werden dem Kunden
  lizenziert; oder werden mit Zustimmung des Kunden als Open Source veröffentlicht
  ([Integrationen zurückgeben](integrationen-zurueckgeben.md)).
- **Exponential und seine Erweiterungen** stehen unter der GPL v2 (oder jeder späteren Version); der Kunde erhält sie
  unter dieser Lizenz, und Code, der zusammen mit ihnen weitergegeben wird, muss sie beachten
  ([Lizenzierung für Partner](../partner/lizenzierung-fuer-partner.md)).
- **Zugangsdaten und Daten** gehören dem Kunden und werden am Ende übergeben
  ([Übergabe](uebergabe-einer-integration.md)).

## Eine Gliederung als Vorlage

```text
STATEMENT OF WORK  <project name>                       between <customer> and <integrator>     version <n>, <date>

1  Background and goals
2  Scope
   2.1 Flow 1: <name> (interface specification: Annex A1)
   2.2 Flow 2: <name> (Annex A2)
3  Out of scope
4  Deliverables
   - Extension <name> in repository <location>, release <version>
   - Documentation and runbook; tests; operator training (<hours>)
5  Customer obligations (dated)
6  Assumptions
7  Timeline and milestones
8  Acceptance (cases: Annex C; period: <days>; defect classes)
9  Change requests
10 Commercials
11 Ownership and licensing
12 Data protection (data flows: Annex D)
13 Hypercare (<weeks>), warranty (<period>), maintenance (separate agreement)
Annexes: A interface specifications, B monitoring plan, C acceptance cases, D data flows
Signatures
```

Für deutschsprachige Verträge übernehmen Sie dieselbe Gliederung mit deutschen Überschriften (Leistungsbeschreibung,
Hintergrund und Ziele, Umfang, Nicht im Umfang, Lieferumfang, Mitwirkungspflichten, Annahmen, Zeitplan, Abnahme,
Änderungsanträge, Kaufmännisches, Eigentum und Lizenzierung, Datenschutz, Hypercare, Gewährleistung und Wartung).

## Verwandte Seiten

- [Integrationsaufwand schätzen](integrationsaufwand-schaetzen.md)
- [Vorlage für eine Schnittstellenspezifikation](vorlage-fuer-eine-schnittstellenspezifikation.md)
- [Projektvorlagen](../fuehrung/projektvorlagen.md)
- [Die Partnerschaftsvereinbarung](../partner/die-partnerschaftsvereinbarung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
