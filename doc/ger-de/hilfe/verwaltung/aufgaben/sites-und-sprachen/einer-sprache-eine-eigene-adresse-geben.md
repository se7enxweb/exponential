---
title: Einer Sprache eine eigene Adresse geben
audience: government, admin
category: public-sector
order: 50
summary: Aufgabe für Administratoren - einen Siteaccess für eine Sprache anlegen und ihn über einen Pfad (/fr/) oder einen Host (fr.example.gov) erreichen, mit den RegionalSettings, die diese Sprache zuerst zeigen.
keywords: Sprachadresse, Sprach-Siteaccess, Pfad, Host, language address, language siteaccess, path, host, URIMatchType, HostMatchMapItems, SiteLanguageList, Locale
type: help
---
# Einer Sprache eine eigene Adresse geben

Diese Aufgabe richtet sich an Administratoren. Hintergrund: [Ein Siteaccess pro Sprache](../../sprachen/ein-siteaccess-pro-sprache.md)
und [Sprachadressen: Pfad oder Host](../../sprachen/sprachadressen-pfad-oder-host.md).

## Bevor Sie beginnen

- Die Inhaltssprache existiert (siehe „Eine neue Inhaltssprache hinzufügen“).
- Sie haben sich zwischen einem Pfad und einem Host entschieden.

## Schritte

1. Kopieren Sie `settings/siteaccess/site` nach `settings/siteaccess/fr` (ein Pfad-Siteaccess wird über seinen Namen
   erreicht).
2. Setzen Sie in `settings/siteaccess/fr/site.ini.append.php`:

   ```ini
   [RegionalSettings]
   Locale=fre-FR
   ContentObjectLocale=fre-FR
   SiteLanguageList[]
   SiteLanguageList[]=fre-FR
   SiteLanguageList[]=eng-US
   TextTranslation=enabled

   [SiteSettings]
   SiteName=<name of the site in French>
   ```

3. Fügen Sie in `settings/override/site.ini.append.php` `AvailableSiteAccessList[]=fr` und
   `RelatedSiteAccessList[]=fr` hinzu.
4. Zuordnung: für einen Pfad `MatchOrder=uri` mit `URIMatchType=element` (mitgeliefert); für einen Host
   `HostMatchMapItems[]=fr.example.gov;fr` mit `MatchOrder=host` oder `host;uri`, dazu DNS, Webserver und Zertifikat.
5. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-tag=ini,content`; mit Velocity auch
   `./console exp:velocity cache clear`.

## Prüfen

- `https://www.example.gov/fr/` (oder der Host) antwortet abgemeldet mit `<html lang="fr-FR">`.
- **Setup > Sprachen** führt `fr` mit seiner `SiteLanguageList` unter den Karten der Sprachen auf.

## Probleme

| Problem | Lösung |
|---|---|
| Die Adresse zeigt die Standard-Site | der Siteaccess steht nicht in `AvailableSiteAccessList[]`, oder die Reihenfolge der Zuordnung |
| Seiten sind auf der neuen Site versteckt | sie haben keine Übersetzung in den aufgeführten Sprachen und sind nicht immer verfügbar |

## Verwandte Seiten

- [10.2 Siteaccesses und Site-Adressen](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
