---
title: Kostenkategorien einer Exponential-Website
audience: government, project-manager, hosting
category: public-sector
order: 30
summary: Ein Katalog der Kostenkategorien für den Besitz einer Website oder eines Intranets auf Exponential - Software, Umsetzung, Infrastruktur, Betrieb, Personal, Qualität, Compliance und Ausstieg - mit dem, was jede Kostenart treibt, und der Seite der Dokumentation, die die Arbeit dahinter beschreibt.
keywords: Kostenkategorien, Kostenaufschlüsselung, Budgetzeilen, Kostentreiber, TCO, Hostingkosten, Umsetzungskosten, Supportkosten, Schulungskosten, Compliance-Kosten, cost categories, cost breakdown, budget lines, cost drivers, hosting cost, implementation cost, support cost, training cost, compliance cost
type: help
---
# Kostenkategorien einer Exponential-Website

Diese Seite richtet sich an Projektleitungen und Finanzpersonal, die eine vollständige Liste der Kostenzeilen brauchen,
bevor sie Angebote einholen. Jede Kategorie nennt, was ihre Kosten treibt, und die Seite der Dokumentation, die die
Arbeit beschreibt, damit Sie Lieferanten die richtigen Dinge bepreisen lassen. Sie enthält keine Preise.

## Inhalt
1. [Software](#software)
2. [Umsetzung](#umsetzung)
3. [Infrastruktur und Hosting](#infrastruktur-und-hosting)
4. [Betrieb](#betrieb)
5. [Personal](#personal)
6. [Qualität und Compliance](#qualität-und-compliance)
7. [Ausstieg](#ausstieg)
8. [Eine Checkliste der Kategorien](#eine-checkliste-der-kategorien)

## Software

| Zeile | Treiber | Hinweise |
|---|---|---|
| Lizenz des Kernels | keiner | GNU GPL Version 2 oder neuer, keine Lizenzgebühr ([Lizenzkosten](lizenzkosten.md)) |
| Erweiterungen | welche Erweiterungen Sie nutzen | die meisten sind freie Software; eine kommerzielle Erweiterung oder ein Dienst kann eine eigene Gebühr haben |
| Dienste Dritter | welche Dienste die Website aufruft | zum Beispiel ein Suchdienst, Captcha, Analyse oder ein Kartenschlüssel; prüfen Sie die Bedingungen jedes Anbieters |
| Entwicklungswerkzeuge | die Wahl Ihrer Entwickler | Git und Composer sind kostenlos |

## Umsetzung

| Zeile | Treiber | Dokumentation |
|---|---|---|
| Inhaltsmodell | Zahl und Komplexität der Inhaltstypen | [Ihr Inhaltsmodell entwerfen](../../../../../eng-us/help/creators/website-creators/designing-your-content-model.md) |
| Design und Templates | eigenes Design gegenüber einem mitgelieferten Theme | [Ein Design oder Theme wählen](../../../../../eng-us/help/creators/website-creators/choosing-a-design-or-theme.md) |
| Integrationen | anzubindende andere Systeme | [REST API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md) |
| Migration der Inhalte | Menge und Struktur der alten Inhalte | [Übersicht zur Migration](../../../../installation/migration/README.md) |
| Multisite und Sprachen | Zahl der Websites und Sprachen | [Multisite einrichten](../../../../../eng-us/help/creators/website-creators/multisite-setup.md), [Mehrsprachige Websites](../../../../../eng-us/help/creators/website-creators/multilingual-sites.md) |

## Infrastruktur und Hosting

| Zeile | Treiber | Dokumentation |
|---|---|---|
| Server oder Hosting-Tarif | Verkehr, Zahl der Websites, Verfügbarkeitsziel | [Hostingkosten](hostingkosten.md), [Ein Hosting-Modell wählen](../../../hosting/modelle/ein-hosting-modell-waehlen.md) |
| Datenbank | Wahl der Engine; SQLite braucht keinen Server | [Anforderungen](../../../../installation/02-voraussetzungen.md) |
| Suche | eingebaute Suche oder Solr | [Such-Backends](../../../hosting/grundlagen/such-backends.md) |
| Sicherungen und Speicher | Größe von `var/storage`, Aufbewahrung | [Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md) |
| TLS-Zertifikate, Domains, CDN | Ihre Wahl | [TLS und HTTPS](../../../hosting/sicherheit/tls-und-https.md), [CDN](../../../hosting/leistung/cdn.md) |

## Betrieb

| Zeile | Treiber | Dokumentation |
|---|---|---|
| Überwachung und Behandlung von Störungen | Verfügbarkeitsziel | [Überblick über die Überwachung](../../../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md) |
| Sicherheitsreleases und Upgrades | Häufigkeit der Releases, eigener Code | [Kosten für Upgrades und Wartung](kosten-fuer-upgrades-und-wartung.md) |
| Supportvertrag | Schweregrade und Reaktionszeiten | [Supportverträge](supportvertraege.md) |

## Personal

| Zeile | Treiber | Dokumentation |
|---|---|---|
| Webteam und Redaktion | Menge der Inhalte, Zahl der Abteilungen | [Personal und Kompetenzen](personal-und-kompetenzen.md) |
| Administratoren | Websites, Rollen, Benutzer | [Kurs für die Administration der Website](../../../trainer/website-administratoren/README.md) |
| Schulung | Zahl der Personen und Rollen | [Schulungskosten](schulungskosten.md) |

## Qualität und Compliance

| Zeile | Treiber | Dokumentation |
|---|---|---|
| Audit der Barrierefreiheit und Erklärung | öffentliche Designs werden vom Projekt nicht geprüft | [Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md) |
| Sicherheitstests | Penetrationstest, Wiederholungstests | [Sicherheitsstandards](../../../../../eng-us/standards/security.md) |
| Datenschutzarbeit | verarbeitete personenbezogene Daten, Dritte | [Datenschutzstandards](../../../../../eng-us/standards/privacy.md) |
| Schriftgut und Archivierung | Aufbewahrungsregeln | [Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md) |

## Ausstieg

| Zeile | Treiber | Dokumentation |
|---|---|---|
| Export und Prüfung | Menge der Inhalte, Zahl der Klassen | [Exponential verlassen](../../../../installation/migration/exponential-verlassen.md) |
| Umleitungen und Übergang | Zahl der Adressen | dieselbe Seite, Abschnitt 4 |
| Parallelbetrieb | Zeit, in der die alte Website schreibgeschützt bleibt | dieselbe Seite, Checkliste |

## Eine Checkliste der Kategorien

```text
[ ] Software: kernel (no fee), extensions, third-party services
[ ] Implementation: content model, design, integrations, migration, sites, languages
[ ] Infrastructure: servers, database, search, backups, TLS, CDN
[ ] Operations: monitoring, releases, upgrades, support
[ ] People: web team, editors, administrators, training
[ ] Quality and compliance: accessibility, security, privacy, records
[ ] Exit: export, redirects, parallel running
[ ] Risk allowance
```

## Verwandte Seiten
- [Gesamtbetriebskosten: Übersicht](README.md)
- [Der Aufbau eines TCO-Modells](aufbau-des-tco-modells.md)
- [Vorlage für ein TCO-Arbeitsblatt](vorlage-fuer-ein-tco-arbeitsblatt.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
