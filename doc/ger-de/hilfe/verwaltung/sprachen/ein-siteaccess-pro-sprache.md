---
title: Ein Siteaccess pro Sprache
audience: government, admin, website-creator
category: public-sector
order: 50
summary: Wie jede Amtssprache einer öffentlichen Website in Exponential einen eigenen Siteaccess erhält - die RegionalSettings, die über Inhalt, Oberfläche und Ersatzsprache entscheiden, das Registrieren und Zuordnen des Siteaccess, ein gemeinsames Design und das Leeren der Caches.
keywords: Siteaccess, Sprach-Siteaccess, SiteLanguageList, Locale, ContentObjectLocale, ShowUntranslatedObjects, TextTranslation, RelatedSiteAccessList, AvailableSiteAccessList, ExtensionSettingsSiteAccess, siteaccess, language siteaccess
type: help
---
# Ein Siteaccess pro Sprache

Diese Seite richtet sich an Administratoren und Websiteersteller. Eine mehrsprachige Website hat in der Regel einen
öffentlichen Siteaccess pro Sprache, die sich alle eine Datenbank teilen. Jeder entscheidet, welche Inhaltssprachen er
in welcher Reihenfolge zeigt, welche Oberflächensprache er hat und unter welcher Adresse er erreichbar ist
([Inhaltssprachen, Abschnitt 5](../../../anleitungen/inhaltssprachen.md#5-eine-sprache-anzeigen-sitelanguagelist-und-siteaccesses)).

## Inhalt

1. [Die Einstellungen eines Sprach-Siteaccess](#die-einstellungen-eines-sprach-siteaccess)
2. [Schritt für Schritt](#schritt-für-schritt)
3. [Das Aussehen der Hauptwebsite übernehmen](#das-aussehen-der-hauptwebsite-übernehmen)
4. [Prüfen](#prüfen)

## Die Einstellungen eines Sprach-Siteaccess

```ini
# settings/siteaccess/site_fr/site.ini.append.php
[RegionalSettings]
Locale=fre-FR
ContentObjectLocale=fre-FR
SiteLanguageList[]
SiteLanguageList[]=fre-FR
SiteLanguageList[]=eng-US
ShowUntranslatedObjects=disabled
TextTranslation=enabled
```

| Einstellung | Was sie bewirkt |
|---|---|
| `SiteLanguageList[]` | die angezeigten Inhaltssprachen, die bevorzugte zuerst; die leere erste Zeile löscht geerbte Werte |
| `ShowUntranslatedObjects` | `disabled`: Objekte, die in keiner der Sprachen vorliegen, werden ausgeblendet, sofern sie nicht immer verfügbar sind |
| `ContentObjectLocale` | Standardsprache für neue Inhalte, die in diesem Siteaccess angelegt werden |
| `Locale` | Oberflächensprache sowie Datums- und Zahlenformate |
| `TextTranslation` | `enabled`, um Oberflächentexte in die `Locale` zu übersetzen |

Ändern Sie `ContentObjectLocale` auf einer Website mit Inhalten nicht, ohne die Warnung in `settings/site.ini` gelesen
zu haben.

## Schritt für Schritt

1. Fügen Sie die Inhaltssprache hinzu (siehe „Eine Inhaltssprache hinzufügen“).
2. Kopieren Sie `settings/siteaccess/site` nach `settings/siteaccess/site_fr` und setzen Sie den Block oben.
3. Fügen Sie in `settings/override/site.ini.append.php` `site_fr` zu `[SiteAccessSettings] AvailableSiteAccessList[]`
   und `RelatedSiteAccessList[]` hinzu und richten Sie die Zuordnung nach Pfad oder Host ein (siehe „Sprachadressen:
   Pfad oder Host“).
4. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-tag=ini,content`, und `./console exp:velocity cache clear`,
   wenn Velocity die Website ausliefert.
5. Übersetzen Sie, beginnend mit der Startseite und dem Menü.

## Das Aussehen der Hauptwebsite übernehmen

Ein Übersetzungs-Siteaccess, der genau wie die Hauptwebsite aussehen soll, erhält
`[SiteAccessSettings] ExtensionSettingsSiteAccess=<main siteaccess>`, damit die Einstellungen der Design-Erweiterung
für den Haupt-Siteaccess gelten
([Übersetzungen und Sprachen](../../../../eng-us/features/6.0/translations-and-languages.md#a-translation-siteaccess-that-looks-like-the-main-site)).
Geben Sie jedem Siteaccess einen eigenen `SiteName` in der jeweiligen Sprache.

## Prüfen

**Setup > Sprachen** zeigt unterhalb der Sprachkarten jeden Siteaccess aus `AvailableSiteAccessList` mit seiner
`SiteLanguageList` in Reihenfolge; eine Locale, die keine Inhaltssprache ist, wird rot markiert. Öffnen Sie die neue
Adresse abgemeldet und prüfen Sie eine übersetzte und eine unübersetzte Seite.

## Verwandte Seiten

- [Eine mehrsprachige Website bauen](../../../../eng-us/help/creators/website-creators/multilingual-sites.md)
- [10.14 Mehrsprachige Websites](../../../installation/10-nach-der-installation.md#1014-mehrsprachige-sites)
- [Ladereihenfolge der Erweiterungen](../../../../eng-us/features/6.0/extension-loading-order.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
