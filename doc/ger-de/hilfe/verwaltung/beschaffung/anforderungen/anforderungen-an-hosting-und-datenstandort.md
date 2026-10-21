---
title: Anforderungen an Hosting und Datenstandort
audience: government, project-manager, hosting, admin
category: public-sector
order: 80
summary: Formulierungen dazu, wo und wie ein öffentliches CMS gehostet wird - im eigenen Haus oder bei einem Lieferanten, Datenstandort, Datenbanken, Betriebssysteme, ausgehende Verbindungen, Sicherungen und Wiederherstellung - mit dem, was die Dokumentation von Exponential über seine technischen Möglichkeiten sagt.
keywords: Hosting, Datenstandort, Datenresidenz, im eigenen Haus, Cloud, Souveränität, Datenbank, Betriebssystem, Sicherungen, RPO, RTO, ausgehende Verbindungen, Dritte, Ausschreibung, Anforderungen, hosting, data location, data residency, on premise, sovereignty, database, operating system, backups, outbound connections, third parties, tender, requirements
type: help
---
# Anforderungen an Hosting und Datenstandort

Diese Seite richtet sich an Beschaffungsteams und IT-Leitungen, die entscheiden, wo eine öffentliche Website läuft und
wo ihre Daten liegen. Exponential ist Open-Source-Software, die Sie auf Ihren eigenen Servern betreiben oder von jedem
Lieferanten betreiben lassen können, die Anforderungen an das Hosting betreffen also vor allem den Lieferanten, nicht
das Produkt. Diese Seite gibt Formulierungen für Anforderungen und die technischen Fakten aus dem Installationsbuch und
der Hilfe zum Hosting, mit denen die Antworten der Bieter übereinstimmen sollten.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Möglichkeiten des Hostings, die Exponential zulässt](#möglichkeiten-des-hostings-die-exponential-zulässt)
2. [Formulierung der Anforderungen](#formulierung-der-anforderungen)
3. [Technische Fakten zum Abgleich der Antworten](#technische-fakten-zum-abgleich-der-antworten)
4. [Wo personenbezogene Daten und Schlüssel liegen](#wo-personenbezogene-daten-und-schlüssel-liegen)
5. [Fallstricke](#fallstricke)

## Möglichkeiten des Hostings, die Exponential zulässt

- **Jeder Host, der die Anforderungen erfüllt**: PHP 8.0 oder neuer, eine unterstützte Datenbank und ein Webserver
  ([Anforderungen](../../../../installation/02-voraussetzungen.md), [Anforderungen auf einen Blick](../../../hosting/grundlagen/anforderungen-auf-einen-blick.md)).
- **Modelle**: Shared Hosting, VPS, dedizierte Server, Container und Kubernetes werden in
  [Ein Hosting-Modell wählen](../../../hosting/modelle/ein-hosting-modell-waehlen.md) verglichen.
- **Ihr eigenes Rechenzentrum**: Nichts in der Lizenz oder der Software bindet es an die Cloud eines Herstellers. Die
  Lizenz ist die GNU GPL Version 2 oder neuer ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md)).

## Formulierung der Anforderungen

| Id | Anforderung |
|---|---|
| HOS-01 | Alle Produktionsdaten, Sicherungen und Protokolle werden in <Land / Region> gespeichert. Führen Sie jeden Standort auf, einschließlich der Standorte für Sicherungen und Notfallwiederherstellung. |
| HOS-02 | Führen Sie jeden Unterauftragsverarbeiter mit Zugang zum System oder seinen Daten auf, und das Land, aus dem er tätig ist. |
| HOS-03 | Führen Sie jede ausgehende Verbindung des gelieferten Systems auf (Paketserver, Mail-Relays, Suchdienste, APIs Dritter) und ihren Zweck. |
| HOS-04 | Sicherungen von Datenbank und Dateien werden alle <Intervall> zusammen erstellt, <Zeitraum> aufbewahrt, verschlüsselt und getrennt von der Produktion gespeichert. |
| HOS-05 | Das Recovery Point Objective beträgt <Zeit> und das Recovery Time Objective <Zeit>; alle <Intervall> wird ein Wiederherstellungstest durchgeführt und berichtet. |
| HOS-06 | Betriebssystem, PHP-Version und Datenbankversion liegen während des Vertrags innerhalb der Sicherheitsunterstützung ihrer Hersteller. |
| HOS-07 | Die Administration lässt sich auf <Netze / VPN> beschränken. |
| HOS-08 | Der Kunde kann jederzeit ohne Zusatzkosten eine vollständige Kopie von Datenbank, Dateien und Konfiguration erhalten. |
| HOS-09 | Die Website wird nur über HTTPS ausgeliefert, mit automatisch erneuerten Zertifikaten. |

Füllen Sie die Lücken nach Ihrem eigenen Bedarf aus; diese Hilfe schlägt keine Werte vor.

## Technische Fakten zum Abgleich der Antworten

| Thema | Was die Dokumentation sagt |
|---|---|
| Datenbanken | In der offiziellen Reihenfolge der Präferenz: SQLite 3 (Standard), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL ([Datenbankunterstützung](../../../datenbankunterstuetzung.md)) |
| Webserver | Exponential Velocity, Apache oder nginx mit PHP-FPM, FrankenPHP ([Die Website ausliefern](../../../../installation/08-die-website-ausliefern.md), [Einen Webserver wählen](../../../hosting/server/einen-webserver-waehlen.md)) |
| Betriebssysteme | Stufen von „tested here“ bis „not possible today“ ([Unterstützung von Betriebssystemen](../../../betriebssystemunterstuetzung.md)) |
| PHP | 8.0 ist die älteste unterstützte Version; 8.5 die neueste getestete ([Anforderungen](../../../../installation/02-voraussetzungen.md)) |
| Mehrere Webknoten | Brauchen einen Datenbankserver und gemeinsamen Speicher ([Mehrere Webknoten](../../../hosting/betrieb/mehrere-web-knoten.md)) |
| HTTPS auf Velocity | Zertifikatsverwaltung einschließlich ACME ([HTTPS-Zertifikate von Velocity](../../../../../eng-us/features/6.0/velocity-https-certificates.md)) |
| Die Administration beschränken | [Die Administration beschränken](../../../hosting/sicherheit/die-administration-einschraenken.md) |
| Ausgehende Verbindungen im Produkt | **Setup > Updates and packages** holt Composer-Pakete von Paketservern; E-Mails gehen an den konfigurierten Transport; Erweiterungen wie Analyse oder CAPTCHA kontaktieren bei Aktivierung Dritte ([Datenschutzstandards](../../../../../eng-us/standards/privacy.md)) |

## Wo personenbezogene Daten und Schlüssel liegen

Die Antwort eines Bieters auf HOS-01 sollte all das abdecken, aus
[Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md):

- die Datenbank (Inhalte, Benutzerkonten, Formulareingaben, Einwilligungsnachweise);
- `var/storage/` und `var/<site>/storage/` (hochgeladene Dateien);
- `var/<site>/log/audit/` (das Audit-Protokoll) und `var/log/` (Protokolle);
- `settings/override/` (Zugangsdaten, die Signaturschlüssel des Audits, das Geheimnis der E-Mail-Einstellungen);
- jede Sicherungskopie davon.

## Fallstricke

- Ein Cloud-Produkt mit Namen vorgeben. Geben Sie das Ergebnis vor (Standort, Kontrolle, Wiederherstellung); lassen Sie
  die Bieter Vorschläge machen.
- Das Mail-Relay, den Suchdienst und den Sicherungsspeicher vergessen, wenn Sie nach dem Datenstandort fragen.
- Eine Wiederherstellungszeit akzeptieren, die nie getestet wurde. Fragen Sie nach Datum und Dauer des letzten
  Wiederherstellungstests ([Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)).

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Ausschreibungsvorlage: Klausel zu Hosting und Datenstandort](../ausschreibungsvorlagen/klausel-hosting-und-datenstandort.md)
- [Anforderungen an Betrieb und Überwachung](anforderungen-an-betrieb-und-ueberwachung.md)
- [Hosting-Unternehmen: hier beginnen](../../../hosting/hosting-anbieter-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
