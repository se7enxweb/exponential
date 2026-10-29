---
title: "Ausschreibungsvorlage: Klausel zu Hosting und Datenstandort"
audience: government, project-manager, hosting, partner
category: public-sector
order: 90
summary: Klauseltext dazu, wo und wie eine Exponential-Website gehostet wird - Standorte von Daten und Sicherungen, Unterauftragsverarbeiter, Umgebungen, Sicherungen und Wiederherstellungstests, Zugang zur Administration, Zugang des Kunden zu seinen eigenen Daten - mit Lücken und den technischen Fakten, mit denen die Antwort eines Hosters übereinstimmen sollte.
keywords: Hosting-Klausel, Datenstandort, Datenresidenz, Souveränität, Sicherungen, Wiederherstellungstest, RPO, RTO, Staging, Umgebungen, Unterauftragsverarbeiter, Beschränkung des Admin-Zugangs, Ausschreibungsvorlage, hosting clause, data location, data residency, sovereignty, backups, restore test, environments, sub-processors, admin access restriction, tender template
type: help
---
# Ausschreibungsvorlage: Klausel zu Hosting und Datenstandort

Diese Seite richtet sich an Beschaffungsstellen und IT-Leitungen, die das Hosting einer Exponential-Website beauftragen,
allein oder zusammen mit Bau und Support. Exponential läuft auf jedem Host, der seine Anforderungen erfüllt, die Klausel
betrifft also die Zusagen des Hosters. Hintergrund: [Anforderungen an Hosting und Datenstandort](../anforderungen/anforderungen-an-hosting-und-datenstandort.md).

**Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Die Klausel](#die-klausel)
2. [Was eine Sicherung enthalten muss](#was-eine-sicherung-enthalten-muss)
3. [Technische Fakten zum Abgleich der Antworten](#technische-fakten-zum-abgleich-der-antworten)
4. [Hinweise für die bewertende Person](#hinweise-für-die-bewertende-person)

## Die Klausel

```text
H. HOSTING AND DATA LOCATION

H.1 Locations. Production systems, backups, logs and disaster recovery copies shall
    be located only in <country / region>. The Supplier lists each location in
    <annex> and shall not change it without the Customer's prior written consent.

H.2 Access from elsewhere. Remote administration by Supplier staff from outside
    <country / region> is [not permitted / permitted only for <roles> under <terms>].

H.3 Environments. The Supplier provides a production environment and a staging
    environment with the same software versions; [a training environment].

H.4 Platform. The Supplier keeps the operating system, PHP, database and web server
    on versions supported by their maintainers and states the versions in <annex>.

H.5 Backups. The database and files are backed up together every <interval>,
    encrypted, kept for <period>, and stored in a location separate from
    production. Backups include the configuration and the keys listed in <annex>.

H.6 Recovery. Recovery point objective: <time>. Recovery time objective: <time>.
    The Supplier performs a restore test every <interval> and reports date,
    duration and result.

H.7 Administration access. Access to the administration is restricted to
    <networks / VPN / named addresses>; all access is over HTTPS.

H.8 Customer access to data. The Customer may obtain at any time, within <time>
    and without additional charge, a complete copy of the database, the files and
    the configuration.

H.9 Capacity. The Supplier monitors capacity (disk, memory, database) and warns the
    Customer before a limit is reached.
```

## Was eine Sicherung enthalten muss

Nehmen Sie aus [Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md) diese Liste in
die Anlage zu H.5 auf:

| Element | Warum |
|---|---|
| die Datenbank | Inhalte, Benutzer, Versionen, Einstellungen |
| `var/storage/` und `var/<site>/storage/` | hochgeladene Dateien |
| `settings/override/` und `settings/siteaccess/` | Konfiguration und Zugangsdaten |
| `settings/override/audit.ini.append.php` | Signaturschlüssel des Audits; ohne sie lassen sich bestehende Einträge nicht verifizieren |
| `settings/override/mailpreferences.ini.append.php` | das Geheimnis der E-Mail-Einstellungen; ein neues macht jeden bereits versandten Abmeldelink ungültig |
| `var/<site>/log/audit/` | das Audit-Protokoll mit signierten Manifesten |
| `composer.json`, `composer.lock`, eigene Erweiterungen und Designs | um dieselben Versionen neu aufzubauen |

Der Wartungsmodus sorgt dafür, dass Datenbank und Dateien während der Sicherung übereinstimmen
([10.9 Sicherung und Wiederherstellung](../../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung)).

## Technische Fakten zum Abgleich der Antworten

| Thema | Quelle |
|---|---|
| Anforderungen (PHP 7.4+, Datenbanken, Webserver) | [Anforderungen auf einen Blick](../../../hosting/grundlagen/anforderungen-auf-einen-blick.md) |
| Befehle zur Datenbanksicherung je Engine | [Datenbanksicherungen je Engine](../../../hosting/betrieb/datenbank-backups-je-engine.md) |
| Wiederherstellungstests | [Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md) |
| Die Administration beschränken | [Die Administration beschränken](../../../hosting/sicherheit/die-administration-einschraenken.md) |
| Mehrere Webknoten | [Mehrere Webknoten](../../../hosting/betrieb/mehrere-web-knoten.md) |
| TLS | [TLS und HTTPS](../../../hosting/sicherheit/tls-und-https.md) |

## Hinweise für die bewertende Person

- Ein RTO ohne datierten Wiederherstellungstest ist eine Schätzung.
- H.8 dient auch der Ausstiegsklausel; setzen Sie den Preis hier auf null, damit er später nicht als Druckmittel dienen
  kann.

## Verwandte Seiten
- [Ausschreibungsvorlagen](README.md)
- [Klausel zu Datenschutz und Auftragsverarbeitung](klausel-datenschutz-und-auftragsverarbeitung.md)
- [Klausel zu Service-Levels](klausel-service-level.md)
- [Klausel zu Ausstieg und Datenrückgabe](klausel-ausstieg-und-datenrueckgabe.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
