---
title: ISO/IEC 27001 und eine Exponential-Website
audience: government, project-manager, admin
category: public-sector
order: 300
summary: Für öffentliche Stellen mit einem Informationssicherheitsmanagementsystem nach ISO/IEC 27001 - wie eine mit Exponential betriebene Website in den Geltungsbereich des ISMS aufgenommen wird, welche Produktfunktionen Nachweise für typische Maßnahmen liefern und was bei der Organisation und ihren Lieferanten verbleibt.
keywords: ISO/IEC 27001, ISMS, Informationssicherheitsmanagementsystem, Maßnahmen, Erklärung zur Anwendbarkeit, Nachweise, Zugangssteuerung, Protokollierung, Änderungsmanagement, Lieferantenbeziehungen, Zertifizierung, öffentlicher Sektor, information security management system, controls, statement of applicability, evidence, access control, logging, change management, supplier relationships, certification, public sector
type: help
---
# ISO/IEC 27001 und eine Exponential-Website

Diese Seite richtet sich an Informationssicherheitsbeauftragte öffentlicher Stellen, die ein ISMS nach ISO/IEC 27001
(<https://www.iso.org/standard/27001>) betreiben. Eine öffentliche Website gehört als Informationssystem meist zum
Geltungsbereich des ISMS. Diese Seite hilft Ihnen, in einer Exponential-Installation Nachweise für typische
Maßnahmenthemen zu finden. Sie zitiert die Norm nicht und ist keine Erklärung zur Anwendbarkeit; das Projekt besitzt
keine Zertifizierung.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Geltungsbereich](#geltungsbereich)
2. [Nachweise nach Thema](#nachweise-nach-thema)
3. [Lieferanten](#lieferanten)
4. [Verwandte Seiten](#verwandte-seiten)

## Geltungsbereich

Beschreiben Sie die Website als Wert (Asset): die Version von Exponential und die Erweiterungen, das Hosting (eigenes
Rechenzentrum oder Lieferant), die Administration und die öffentlichen Siteaccesses, die Daten, die sie enthält
(Inhalte, Benutzerkonten, Formulareingaben, Einwilligungsnachweise, Protokolle). Der Datenschutzbereich dieser Hilfe
listet auf, wo personenbezogene Daten liegen.

## Nachweise nach Thema

| Thema | Nachweis in Exponential | Wie Sie ihn erzeugen |
|---|---|---|
| Zugangssteuerung | Rollen, Zuweisungen, Kontenliste | Rollenseiten; Benutzer > Benutzerkonten prüfen, CSV-Export |
| Authentifizierung | Einstellungen für Passwort und zweistufige Anmeldung | Ausgabe von `./console exp:ini get`; Registrierungsstatus |
| Protokollierung und Überwachung | Audit-Protokoll, Empfänger der Alarme, Prüfung | `./console exp:audit status`, `verify`, `alerts recipients` |
| Änderungsmanagement | `ManagedByDeployment`, aufgezeichnete Schreibvorgänge an Einstellungen (`system.setting.write`) | Einstellungen; Audit-Konsole gefiltert auf `system.setting.*` |
| Konfigurationsmanagement | Sicherheits-Header, CSP-Modus, Debug aus | Ausgabe von `curl -sI`; Einstellungen |
| Handhabung technischer Schwachstellen | Aktualisierungsnachweise, `composer audit` | Patch-Nachweise |
| Integrität der Software | Dateikonsistenzprüfung | Bericht der Upgrade-Prüfung, Ausgabe von `verifyfiles.sh` |
| Datensicherung | Nachweise über Sicherung und Wiederherstellung | Betriebsnachweise |
| Sichere Entwicklung | die Tests und Härtungshinweise des Projekts; Ihre eigenen Code-Reviews | [Sicherheitsstandards: Tests und CI](../../../../eng-us/standards/security.md#tests-and-ci) |
| Löschung von Informationen | Aufbewahrung von Protokollen, Audit, Einwilligungsprotokoll | Aufbewahrungseinstellungen (siehe den Datenschutzbereich) |

## Lieferanten

Führen Sie die Lieferanten in Ihrem Lieferantenverzeichnis: das Hosting-Unternehmen, den Implementierungspartner und
alle Dienste Dritter, die über Erweiterungen aktiviert sind (Analyse, CAPTCHAs, Karten, KI-Anbieter). Das
Exponential-Projekt selbst ist ein Open-Source-Projekt und kein vertraglich gebundener Lieferant, sofern Sie nicht
Support bei 7x oder einem Partner einkaufen ([Wer ist 7x](../../wer-ist-7x.md)).

## Verwandte Seiten

- [Sicherheitsstandards](../../../../eng-us/standards/security.md)
- [Die Sicherheitslage von Exponential auf einen Blick](sicherheitslage-auf-einen-blick.md)
- [Sicherheits-Checkliste vor dem Go-live öffentlicher Websites](sicherheits-checkliste-vor-dem-go-live.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
