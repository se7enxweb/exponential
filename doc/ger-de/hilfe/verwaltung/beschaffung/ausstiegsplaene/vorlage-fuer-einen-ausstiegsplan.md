---
title: Vorlage für einen Ausstiegsplan für eine Exponential-Website
audience: government, project-manager, admin, partner
category: public-sector
order: 30
summary: Ein Ausstiegsplan zum Kopieren und Ausfüllen für eine öffentliche Website oder ein Intranet auf Exponential - Bestandsaufnahme, Lieferungen mit Formaten, Rollen, Zeitplan, Prüfung, Regeln für personenbezogene Daten, Umleitungen und Abnahme - zum Beifügen zum Vertrag und zum jährlichen Testen.
keywords: Vorlage für einen Ausstiegsplan, Ausstiegsstrategie, Übergabeplan, Übergangsplan, Vertragsanlage, Lieferungen, Datenexport, Prüfung, Abnahme, exit plan template, exit strategy, handover plan, transition plan, contract annex, deliverables, data export, verification, sign-off
type: help
---
# Vorlage für einen Ausstiegsplan für eine Exponential-Website

Diese Seite richtet sich an die Projektleitung, die den Ausstiegsplan schreibt, meist als Anlage zum Vertrag mit dem
Lieferanten für Umsetzung, Hosting oder Support. Kopieren Sie die Blöcke in Ihr Dokument und füllen Sie die Lücken aus.
Die Lieferungen und ihre Formate stammen aus [Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md)
und [Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md).

> **Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung.

## Inhalt
1. [Teil 1 - Umfang und Rollen](#teil-1---umfang-und-rollen)
2. [Teil 2 - Bestandsaufnahme](#teil-2---bestandsaufnahme)
3. [Teil 3 - Lieferungen](#teil-3---lieferungen)
4. [Teil 4 - Zeitplan](#teil-4---zeitplan)
5. [Teil 5 - Prüfung](#teil-5---prüfung)
6. [Teil 6 - Personenbezogene Daten und Löschung](#teil-6---personenbezogene-daten-und-löschung)
7. [Teil 7 - Abnahme](#teil-7---abnahme)
8. [Den Plan testen](#den-plan-testen)

## Teil 1 - Umfang und Rollen

```text
System:            <site names and addresses>          Exponential version: <...>
Contract:          <reference>, ends <date>
Exit trigger:      end of contract | termination | change of system | supplier failure
Exit owner (us):   <role>        Supplier exit lead: <role>        Receiving party: <role or new supplier>
Data protection:   <role>        Security: <role>                  Records: <role>
```

## Teil 2 - Bestandsaufnahme

Halten Sie sie aktuell; überprüfen Sie sie jährlich.

```text
Sites and siteaccesses:          <list>                     (Setup > System information shows them)
Languages:                       <list>
Content classes:                 <count, list>             (Setup > Classes)
Active extensions:               <list, with versions>      (Setup > Extensions)
Custom extensions and designs:   <list, Git repositories>
Integrations:                    <systems, direction, protocol>
Database engine and version:     <...>
Engine:                          Velocity | Apache/nginx with PHP-FPM | FrankenPHP
Hosting:                         <provider, location of data and backups>
Scheduled jobs:                  <cron entries>             (Setup > Cronjobs)
Third-party services:            <analytics, captcha, maps, mail, ...>
```

## Teil 3 - Lieferungen

| # | Lieferung | Format | Quelle |
|---|---|---|---|
| D1 | Website-Archiv aller Inhalte | Archiv von xrowextract: CSV je Klasse, `manifest.json`, `README.txt` (ZIP oder TAR) | [Formate für den Datenexport](formate-fuer-den-datenexport.md) |
| D2 | Vollständiger Datenbank-Dump | Dump-Werkzeug der Engine oder `exp:ezsqldumpschema` | [Datenbanksicherungen je Engine](../../../hosting/betrieb/datenbank-backups-je-engine.md) |
| D3 | Dateien | `var/storage/` und `var/<site>/storage/` | [Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md) |
| D4 | Einstellungen | `settings/override/`, `settings/siteaccess/` (vertrauliche Übertragung) | [Übergabe von Schlüsseln und Geheimnissen](uebergabe-von-schluesseln-und-geheimnissen.md) |
| D5 | Code | Git-Repositorys der eigenen Erweiterungen und Designs, mit Historie | [Übergabe der Dokumentation](uebergabe-der-dokumentation.md) |
| D6 | Layouts (falls verwendet) | ZIP aus `exp:layouts export` | [Exponential verlassen](../../../../installation/migration/exponential-verlassen.md) |
| D7 | Formulareingaben | CSV aus `ext:bccie:export` | ebenda |
| D8 | Einwilligungsnachweise | CSV aus `exp:mailconsent` | ebenda |
| D9 | Audit-Protokoll | Audit-Verzeichnis mit signierten Manifesten; Export mit `exp:audit` | [Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md) |
| D10 | Adressliste für Umleitungen | aus dem Archiv plus `ezurlalias_ml`, `ezurlwildcard` | [URL-Umleitungen nach dem Ausstieg](url-umleitungen-nach-dem-ausstieg.md) |
| D11 | Dokumentation | Inhaltsmodell, Einstellungen, Integrationen, Betriebshandbücher | [Übergabe der Dokumentation](uebergabe-der-dokumentation.md) |
| D12 | Versionsvermerk | Exponential, PHP, Datenbank, Engine, Erweiterungen | [Kunden-Offboarding](../../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md) |

## Teil 4 - Zeitplan

```text
T-<n> weeks   exit plan reviewed; receiving party named
T-<n> weeks   test export delivered and verified (D1-D3)
T-<n> days    content freeze announced to editors
T             maintenance mode on; final export D1-D12; maintenance off (or site handed over)
T+<n> days    receiving party confirms completeness in writing
T+<n> days    supplier deletes data as agreed and confirms in writing
```

## Teil 5 - Prüfung

- [ ] `manifest.json` aus D1 geöffnet; die Zeilenzahlen je Klasse stimmen mit den Zahlen in der Administration überein.
- [ ] Eine Stichprobe von Dateien aus D3 lässt sich öffnen; in D1 referenzierte Bilder existieren in D3.
- [ ] D2 einmal auf einem Testrechner wiederhergestellt; die Website startet ([Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)).
- [ ] D9: Die Audit-Kette lässt sich verifizieren (`./console exp:audit verify`).
- [ ] D5 lässt sich bauen; im Vergleich zur Produktion fehlt keine Datei.

## Teil 6 - Personenbezogene Daten und Löschung

- Welche personenbezogenen Daten an die übernehmende Partei gehen, und auf welcher Rechtsgrundlage: <...>
- Passwort-Hashes: nicht im Website-Archiv; Benutzer setzen ihr Passwort auf dem Zielsystem neu, sofern nichts anderes
  vereinbart ist ([Personenbezogene Daten beim Ausstieg](personenbezogene-daten-beim-ausstieg.md)).
- Der Lieferant löscht: Produktion, Staging, Sicherungen, Logdateien - am <Datum>, schriftlich bestätigt.

## Teil 7 - Abnahme

```text
Exit completed: <date>     Our exit owner: <name, signature>     Supplier: <name, signature>
Open items: <list>
```

## Den Plan testen

Führen Sie Teil 3 und Teil 5 einmal im Jahr zusammen mit dem Lieferanten auf einer Kopie durch. Ein Export, der nie
getestet wurde, ist eine Hoffnung, kein Plan.

## Verwandte Seiten
- [Ausstiegspläne: Übersicht](README.md)
- [Warum den Ausstieg planen](warum-den-ausstieg-planen.md)
- [Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
