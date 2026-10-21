---
title: Multisite einrichten
audience: website-creator, admin
category: website-creation
order: 240
summary: Mehrere Websites aus einer Exponential-Installation betreiben - ein Siteaccess pro Website, ein Wurzelordner pro Website, Designs und Einstellungen pro Website in Erweiterungen, Host-Zuordnung, Links zwischen Websites und die Abwägungen im Betrieb.
keywords: Multisite, mehrere Websites, Siteaccess, Host-Zuordnung, Website-Erweiterung, Website-Ersteller, multisite, multi-site, several sites, siteaccess, host matching, HostMatchMapItems, RootNode, IndexPage, RelatedSiteAccessList, site extension, ezurlmultisite, website creator
type: help
---
# Multisite einrichten

Diese Seite richtet sich an Website-Ersteller, die mehr als eine Website auf einer Exponential-Installation bauen:
mehrere Marken, Regionen oder eine Hauptwebsite und Microsites. Jede Website ist ein oder mehrere **Siteaccesses**: ein
Name, seine Einstellungen in `settings/siteaccess/<name>/` und eine Regel, welche Anfragen er beantwortet
([10.2 Siteaccesses und Website-Adressen](../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).

## Inhalt

1. [Formen einer Multisite](#formen-einer-multisite)
2. [Schritt für Schritt: eine zweite Website](#schritt-für-schritt-eine-zweite-website)
3. [Einstellungen pro Website in einer Erweiterung](#einstellungen-pro-website-in-einer-erweiterung)
4. [Links zwischen den Websites](#links-zwischen-den-websites)
5. [Redaktion und Administration](#redaktion-und-administration)
6. [Abwägungen](#abwägungen)

## Formen einer Multisite

| Form | Wie |
|---|---|
| Getrennte Inhalte pro Website | ein Ordner pro Website unter der Wurzel; jeder Siteaccess verwendet seinen Ordner als Wurzel |
| Gemeinsame Inhalte, verschiedene Designs | dieselbe Wurzel, ein anderes `SiteDesign` pro Siteaccess |
| Eine Website in mehreren Sprachen | ein Siteaccess pro Sprache ([Eine mehrsprachige Website bauen](mehrsprachige-websites.md)) |
| Jede Website in ihrer eigenen Erweiterung | Einstellungen, die nur gelten, während die Siteaccesses der Website laufen ([Einstellungen pro Website](../../../../eng-us/features/6.0/multi-site-ini-overrides.md)) |

Das Standard-Seitenpaket `sevenx_multisite` ist selbst eine Multisite-Installation.

## Schritt für Schritt: eine zweite Website

1. **Inhalte.** Legen Sie unter der Wurzel einen Ordner `Site B` an und notieren Sie seine Knoten-ID.
2. **Siteaccess.** Kopieren Sie `settings/siteaccess/site` nach `settings/siteaccess/site_b` und setzen Sie in
   `site_b/site.ini.append.php`:

   ```ini
   [SiteSettings]
   SiteName=Site B
   SiteURL=www.site-b.example
   IndexPage=/content/view/full/<node id of Site B>
   DefaultPage=/content/view/full/<node id of Site B>

   [DesignSettings]
   SiteDesign=siteb
   AdditionalSiteDesignList[]
   AdditionalSiteDesignList[]=simple
   ```

   und in `site_b/content.ini.append.php`:

   ```ini
   [NodeSettings]
   RootNode=<node id of Site B>
   ```

3. **Registrieren und zuordnen** in `settings/override/site.ini.append.php`:

   ```ini
   [SiteAccessSettings]
   AvailableSiteAccessList[]=site_b
   RelatedSiteAccessList[]=site_b
   MatchOrder=host;uri
   HostMatchType=map
   HostMatchMapItems[]=www.site-b.example;site_b
   ```

   Behalten Sie die Einträge, die der Installer für die anderen Siteaccesses geschrieben hat; fügen Sie Ihre hinzu.
   `HostMatchMethod=start` lässt auch einen Testhost wie `www.site-b.example.test.local` denselben Siteaccess erreichen
   ([Siteaccess nach Host-Präfix](../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md)).
4. **Webserver und DNS.** Tragen Sie den Hostnamen im DNS, im Webserver oder in Velocity und im Zertifikat ein
   ([Livegang: Hosts und HTTPS](livegang-hosts-und-https.md)).
5. **Caches leeren und prüfen.**

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   curl -sI https://www.site-b.example/ | head -1
   ```

6. **Die Administration mit der Website bekannt machen**: Fügen Sie `site_b` zu `[SiteSettings] SiteList[]` des
   Administrations-Siteaccess hinzu, damit Vorschauen und Links sie verwenden können.

## Einstellungen pro Website in einer Erweiterung

Wenn jede Website eine Erweiterung ist (`extension/site_b/`), kann sie Dateien in `settings/override/` mitbringen, die nur
gelten, während einer ihrer eigenen Siteaccesses läuft, plus `settings/siteaccess/<name>/` pro Siteaccess. Das Leeren
des INI-Caches leert ihn für jede Website
([Einstellungen pro Website innerhalb von Erweiterungen](../../../../eng-us/features/6.0/multi-site-ini-overrides.md)). So bleiben
Design, Einstellungen und Templates jeder Website zusammen in einem Git-Repository.

## Links zwischen den Websites

Ein Link im Inhalt von Website A auf eine Seite, die im Ordner von Website B liegt, muss auf den Host von Website B
zeigen. Die Erweiterung `ezurlmultisite` löst solche Links auf
([Links auf Inhalte einer anderen Website](../../../../eng-us/features/6.0/multisite-cross-site-links.md)).

## Redaktion und Administration

- Eine Administration kann jede Website bedienen; nach Teilbaum eingeschränkte Rollen halten jedes Team im Ordner seiner
  Website ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
- Mehrere Administrations-Siteaccesses sind möglich, wenn Teams die Websites der anderen überhaupt nicht sehen sollen.

## Abwägungen

| Stärke | Einschränkung |
|---|---|
| ein Kernel, ein Satz Erweiterungen zum Aktualisieren und Absichern | jede Website zieht bei einem Upgrade mit den anderen mit |
| gemeinsame Inhalte und Medien, ein Suchindex | eine stark belastete Website verlangsamt die anderen auf demselben Rechner |
| ein Backup | eine Wiederherstellung stellt alle Websites wieder her |

Schreiben Sie die Abwägung in den Plan
([Multisite- und mehrsprachige Projekte planen](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md)).

## Verwandte Seiten

- [Eine mehrsprachige Website bauen](mehrsprachige-websites.md)
- [Den Inhaltsbaum strukturieren](den-inhaltsbaum-strukturieren.md)
- Kochbuch zu Multisite-Siteaccesses (`doc/eng-us/examples/cookbook/multisite-siteaccess.md`)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
