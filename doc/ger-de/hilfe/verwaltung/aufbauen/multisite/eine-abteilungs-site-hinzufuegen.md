---
title: Eine Abteilungs-Site hinzufügen
audience: government, admin, website-creator
category: public-sector
order: 30
summary: Schritt für Schritt für Administratoren - einer Abteilung in einer bestehenden Exponential-Installation eine eigene Site geben, mit einem Inhaltsordner, einem Siteaccess mit Wurzelknoten und Design, Registrierung und Host-Zuordnung, DNS und Zertifikat, und die Prüfungen.
keywords: Abteilungs-Site, Unter-Site, neue Site, Siteaccess, Multisite, department site, subsite, new site, siteaccess, RootNode, IndexPage, SiteDesign, HostMatchMapItems, AvailableSiteAccessList, SiteList, multisite
type: help
---
# Eine Abteilungs-Site hinzufügen

Diese Seite richtet sich an Administratoren und Site-Ersteller. Sie folgt den Schritten aus
[Mehrere Sites in einer Installation einrichten](../../../ersteller/website-ersteller/multisite-einrichten.md#schritt-für-schritt-eine-zweite-website)
für eine Abteilung einer Behörde.

## Inhalt

1. [Inhalte](#inhalte)
2. [Der Siteaccess](#der-siteaccess)
3. [Registrieren und zuordnen](#registrieren-und-zuordnen)
4. [Host, DNS und Zertifikat](#host-dns-und-zertifikat)
5. [Die Administration](#die-administration)
6. [Prüfungen](#prüfungen)

## Inhalte

Legen Sie unter der Wurzel einen Ordner für die Abteilung an, zum Beispiel „Abteilung Beispiel“, und notieren Sie seine
Knoten-ID. Weisen Sie ihn einer Sektion der Abteilung zu, wenn Redakteure verschiedener Abteilungen getrennt bleiben
müssen (siehe „Redakteure und Rollen je Abteilung“).

## Der Siteaccess

Kopieren Sie `settings/siteaccess/site` nach `settings/siteaccess/dept_example` und setzen Sie:

```ini
# settings/siteaccess/dept_example/site.ini.append.php
[SiteSettings]
SiteName=Department of Example
SiteURL=dept.example.gov
IndexPage=/content/view/full/<node id>
DefaultPage=/content/view/full/<node id>

[DesignSettings]
SiteDesign=dept_example
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=simple
```

und in `dept_example/content.ini.append.php`:

```ini
[NodeSettings]
RootNode=<node id>
```

## Registrieren und zuordnen

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
AvailableSiteAccessList[]=dept_example
RelatedSiteAccessList[]=dept_example
MatchOrder=host;uri
HostMatchType=map
HostMatchMapItems[]=dept.example.gov;dept_example
```

Behalten Sie die Einträge, die der Installer geschrieben hat; fügen Sie Ihre hinzu.

## Host, DNS und Zertifikat

Fügen Sie den Hostnamen dem DNS, dem Webserver oder Velocity und dem Zertifikat hinzu
([Live gehen: Hosts, HTTPS und Siteaccesses](../../../ersteller/website-ersteller/livegang-hosts-und-https.md)).

## Die Administration

Fügen Sie `dept_example` der `[SiteSettings] SiteList[]` des Administrations-Siteaccess hinzu, damit Vorschauen und
Links ihn verwenden können.

## Prüfungen

```bash
php bin/php/ezcache.php --clear-tag=ini
curl -sI https://dept.example.gov/ | head -1
```

Öffnen Sie die Site abgemeldet; prüfen Sie die Startseite, eine Seite, die Suche, und dass Seiten anderer Abteilungen
unter diesem Host nicht erreichbar sind, sofern das nicht beabsichtigt ist.

## Verwandte Seiten

- [Designs je Site](designs-je-site.md)
- [Einstellungen je Site in Erweiterungen](einstellungen-je-site-in-erweiterungen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
