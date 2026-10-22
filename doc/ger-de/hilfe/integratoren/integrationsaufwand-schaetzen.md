---
title: Integrationsaufwand schätzen
audience: integrator, project-manager, partner
category: integration
order: 440
summary: Wie Sie eine Integration mit Exponential schätzen - die Aufteilung in Flüsse und Arbeitspakete, die Faktoren, die den Aufwand treiben (Schnittstelle, Volumen, Datenqualität, die API des anderen Systems, Identität, Tests, Betrieb), ein Arbeitsblatt mit Annahmen und die am häufigsten vergessenen Punkte.
keywords: Schätzung, schätzen, Aufwand, Umfang, Arbeitsaufteilung, Flüsse, Komplexität, Annahmen, Reserve, Datenqualität, API-Dokumentation, Hypercare, Arbeitsblatt, Festpreis, nach Aufwand, estimate, effort, scoping, work breakdown, flows, assumptions, contingency, fixed price, time and material
type: help
---
# Integrationsaufwand schätzen

Diese Seite ist für Integratoren und Account Manager, die Integrationsprojekte anbieten. Sie zeigt einen Weg, die
Arbeit aufzuteilen, und die Faktoren, die die Zahl bewegen. Sie enthält keine Tagessätze oder Referenzaufwände: Die
hängen von Ihrem Team und Ihrem Markt ab. Die allgemeine Anleitung zum Schätzen von Exponential-Projekten ist
[Schätzen und Umfang festlegen mit Erweiterungen](../fuehrung/schaetzen-und-abgrenzen-mit-erweiterungen.md).

## Inhalt

1. [Pro Fluss schätzen](#pro-fluss-schätzen)
2. [Faktoren, die den Aufwand treiben](#faktoren-die-den-aufwand-treiben)
3. [Arbeit, die oft vergessen wird](#arbeit-die-oft-vergessen-wird)
4. [Ein Arbeitsblatt für die Schätzung](#ein-arbeitsblatt-für-die-schätzung)
5. [Festpreis oder nach Aufwand](#festpreis-oder-nach-aufwand)

## Pro Fluss schätzen

Ein **Fluss** ist eine Art von Daten, die sich in einer Richtung zwischen zwei Systemen bewegt („ERP-Produkte nach
Exponential", „Formular-Leads ins CRM"). Schätzen Sie jeden Fluss mit denselben Arbeitspaketen:

| Arbeitspaket | Enthält |
|---|---|
| Analyse und Spezifikation | Zuordnung, Schlüssel, Löschregel, Fehler, Überwachung; die Schnittstellenspezifikation |
| Inhaltsmodell | Klassen, Attribute, Sektionen, Rollen (wenn der Fluss neue Inhalte bringt) |
| Client des anderen Systems | Authentifizierung, Aufrufe, Seitenweise Abfrage, Fehlerbehandlung, ein Test-Double |
| Umwandlung | Zuordnungscode, Rich-Text-Umwandlung, Umgang mit Medien |
| Seite von Exponential | Import oder Sender, Hooks, Cronjob-Teile, Befehle, Tabellen |
| Tests | Unit- und Vertragstests, Probeläufe, der Idempotenztest, Ende-zu-Ende |
| Betrieb | Überwachungsprüfung, Protokollzeilen, Abschnitt im Runbook, Einrichtung der Alarme |
| Livegang | Erstbefüllung, Probe, Umstellung |

Fügen Sie die gemeinsamen Punkte einmal pro Projekt hinzu: das Gerüst der Erweiterung, Umgebungen, Dienstkonten, CI,
die Sicherheitsprüfung, Übergabe und Hypercare.

## Faktoren, die den Aufwand treiben

| Faktor | Geringerer Aufwand | Höherer Aufwand |
|---|---|---|
| Schnittstelle | Eine mitgelieferte Schnittstelle passt (RSS-Export, Zeitplan von `xrowextract`, REST-Lesezugriffe) | Eine neue Schnittstelle (ein Plugin für eine Suchmaschine, ein SSO-Handler hinter einem Proxy) |
| Richtung | In eine Richtung | In beide Richtungen, mit aufzulösenden Konflikten |
| Zeitverhalten | Nächtlicher Stapel | Nahezu Echtzeit mit Outbox und Wiederholungen |
| Volumen | Hunderte Datensätze | Hunderttausende, mit Performance-Arbeit |
| Datenqualität | Saubere, geprüfte Quelle | Freitext, Duplikate, fehlende Schlüssel |
| Die API des anderen Systems | Dokumentiert, mit Testinstanz und Ansprechpartner | Undokumentiert, nur Produktion, langsame Antworten |
| Inhalte | Einfache Felder | Rich Text mit eingebetteten Elementen, Beziehungen, Medien, mehrere Sprachen |
| Identität | Keine | LDAP mit Gruppenzuordnung, SSO, Anlegen und Entziehen von Konten |
| Compliance | Keine personenbezogenen Daten | Personenbezogene Daten, Zahlungen, Audit-Anforderungen |
| Umgebungen | Entwicklung, Staging und Produktion existieren | Sie müssen erst aufgebaut werden |

Bewerten Sie jeden Faktor pro Fluss (niedrig, mittel, hoch) und lassen Sie die Bewertungen, nicht das Bauchgefühl,
über die Reserve entscheiden.

## Arbeit, die oft vergessen wird

- [ ] Die Löschregel und ihre Umsetzung.
- [ ] Der Sicherheitsstopp bei abgeschnittenen Quellen.
- [ ] Wiederholte Läufe und der Idempotenztest.
- [ ] Rich-Text-Umwandlung und der Bericht über nicht umwandelbares Markup.
- [ ] Weiterleitungen für migrierte Inhalte.
- [ ] Überwachung und Alarme; das Runbook.
- [ ] Verfahren zur Schlüsselrotation und seine erste Rotation.
- [ ] Warten auf das Team des anderen Systems (Zugang, Antworten, Korrekturen).
- [ ] Hypercare nach dem Livegang.
- [ ] Übergabe und Schulung der Betreiber.
- [ ] Das erste Upgrade nach dem Livegang, wenn der Vertrag Wartung umfasst.

## Ein Arbeitsblatt für die Schätzung

```text
Project: <customer> integrations                    Version: 1     Date: <date>     Author: <name>

Flow                         Surface              Dir   Timing    Volume   Quality  API   Content  Rating
1 ERP products -> site       cronjob + PHP API    in    nightly   20 000   medium   good  plain    medium
2 Form leads -> CRM          cronjob + CRM API    out   5 min     50/day   good     good  plain    low
3 Staff sign-in              LDAP login handler   in    on login  300      -        -     -        low

Work item (per flow)         Flow 1   Flow 2   Flow 3
Analysis and specification
Content model
Client of the other system
Transformation
Exponential side
Tests
Operations
Go-live
Subtotal

Shared items: extension skeleton, environments, service accounts, CI, security review, handover, hypercare
Contingency (from ratings):
Total:

Assumptions (each one a line in the statement of work):
- The customer provides test instances of the ERP and the CRM by <date>.
- The ERP export file format is as in the sample of <date>; changes are change requests.
- ...
```

## Festpreis oder nach Aufwand

| Ein Festpreis funktioniert, wenn | Abrechnung nach Aufwand ist fairer, wenn |
|---|---|
| Die Flüsse spezifiziert und die anderen Systeme bekannt sind | Die API des anderen Systems unbekannt ist oder sich noch ändert |
| Die Datenqualität an echten Daten gemessen wurde | Die Datenqualität unbekannt ist |
| Der Kunde Zugang und Antworten rechtzeitig liefert (schriftlich festgehalten) | Mehrere Dritte beteiligt sind |

Eine übliche Aufteilung: Bestandsaufnahme und Spezifikation nach Aufwand, der Bau zum Festpreis auf Grundlage der
Spezifikation, Hypercare und Betrieb als Dienstleistung. Welches Modell auch immer: Schreiben Sie die Annahmen in die
Leistungsbeschreibung ([Leistungsbeschreibungen für Integrationen](leistungsbeschreibungen-fuer-integrationen.md)).

## Verwandte Seiten

- [Schätzen und Umfang festlegen mit Erweiterungen](../fuehrung/schaetzen-und-abgrenzen-mit-erweiterungen.md)
- [Arbeitsblatt zur Bestandsaufnahme einer Integration](arbeitsblatt-zur-bestandsaufnahme-einer-integration.md)
- [Playbook für Integrationsprojekte](playbook-fuer-integrationsprojekte.md)
- [Vertriebsunterstützung für Partner](../partner/pre-sales-unterstuetzung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
