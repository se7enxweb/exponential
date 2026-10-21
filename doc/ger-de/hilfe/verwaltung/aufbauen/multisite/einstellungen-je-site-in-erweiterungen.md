---
title: Einstellungen je Site in Erweiterungen
audience: government, admin, developer
category: public-sector
order: 50
summary: Wie jede Abteilungs-Site ihre Einstellungen, ihr Design und ihre Templates in einer Erweiterung zusammenhält, die nur gilt, während ihre Siteaccesses laufen (settings/override in der Erweiterung), damit eine Multisite-Installation überschaubar bleibt und jede Site in ihrem eigenen Git-Repository leben kann.
keywords: Einstellungen je Site, Erweiterung je Site, settings/override in der Erweiterung, Einstellungen des Siteaccess, INI-Cache, Git-Repository je Site, multi-site settings, extension per site, settings/override in extension, siteaccess settings, ExtensionSettingsSiteAccess, INI cache, Git repository per site
type: help
---
# Einstellungen je Site in Erweiterungen

Diese Seite richtet sich an Administratoren und Entwickler einer Installation mit vielen Sites. Seit Januar 2026 kann
eine Erweiterung, die eine Site enthält, Override-Einstellungen mitbringen, die nur aktiv sind, während einer ihrer
Siteaccesses läuft, und das Leeren des INI-Caches leert ihn für jedes Projekt
([Einstellungen je Site in Erweiterungen](../../../../../eng-us/features/6.0/multi-site-ini-overrides.md)).

## Inhalt

1. [Aufbau einer Site-Erweiterung](#aufbau-einer-site-erweiterung)
2. [Einrichtung](#einrichtung)
3. [Warum das zu Abteilungen passt](#warum-das-zu-abteilungen-passt)
4. [Siteaccesses für Übersetzungen](#siteaccesses-für-übersetzungen)

## Aufbau einer Site-Erweiterung

```
extension/site_dept_example/
  settings/override/site.ini.append.php        settings for all siteaccesses of this site
  settings/siteaccess/dept_example/...         settings of one siteaccess
  design/dept_example/templates/...            the design layer
  translations/...                             interface texts (extension named in TranslationExtensions[])
```

## Einrichtung

1. Geben Sie der Erweiterung für ihren Siteaccess ein Verzeichnis `settings/siteaccess/<siteaccess>/`; die neuen
   Verzeichnisse werden nur dann durchsucht, wenn es das gibt, sodass unbeteiligte Erweiterungen nichts kosten.
2. Legen Sie Einstellungen für jeden Siteaccess der Erweiterung in ihr `settings/override/`.
3. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini`.

## Warum das zu Abteilungen passt

- Ein Git-Repository je Abteilungs-Site, mit eigener Historie und eigenen Prüfern.
- Die zentralen Einstellungen bleiben klein; die Änderung einer Abteilung kann die Einstellungen einer anderen nicht
  ändern.
- Eine Site in eine andere Installation zu verlegen, bedeutet, ihre Erweiterung und ihre Inhalte zu verlegen.

## Siteaccesses für Übersetzungen

Ein Sprach-Siteaccess, der wie die Hauptsite der Abteilung aussehen soll, erhält
`[SiteAccessSettings] ExtensionSettingsSiteAccess=<main siteaccess>`
([Reihenfolge beim Laden von Erweiterungen](../../../../../eng-us/features/6.0/extension-loading-order.md)).

## Verwandte Seiten

- [Einstellungen je Site in Erweiterungen](../../../../../eng-us/features/6.0/multi-site-ini-overrides.md)
- [Eine Abteilungs-Site hinzufügen](eine-abteilungs-site-hinzufuegen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
