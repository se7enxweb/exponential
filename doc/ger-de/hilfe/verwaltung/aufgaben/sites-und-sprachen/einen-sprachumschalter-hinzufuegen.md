---
title: Einen Sprachumschalter hinzufügen
audience: government, website-creator
category: public-sector
order: 40
summary: Aufgabe für Site-Ersteller - einen barrierefreien Sprachumschalter auf jede Seite einer mehrsprachigen öffentlichen Site setzen, mit TranslationSA und dem Operator language_switcher.
keywords: Sprachumschalter, Pagelayout, Barrierefreiheit, lang-Attribut, mehrsprachige Site, language switcher, TranslationSA, language_switcher, switchlanguage, pagelayout, accessibility, lang attribute
type: help
---
# Einen Sprachumschalter hinzufügen

Diese Aufgabe richtet sich an Site-Ersteller. Hintergrund: [Der Sprachumschalter](../../sprachen/der-sprachumschalter.md).

## Bevor Sie beginnen

- Ein Siteaccess je Sprache, alle in `[SiteAccessSettings] RelatedSiteAccessList[]` aufgeführt.
- Zugang zum Pagelayout Ihrer Design-Erweiterung.

## Schritte

1. Benennen Sie die Siteaccesses des Umschalters, jede Sprache in ihrem eigenen Namen:

   ```ini
   # settings/override/site.ini.append.php
   [RegionalSettings]
   TranslationSA[]
   TranslationSA[site_en]=English
   TranslationSA[site_fr]=Français
   ```

2. Fügen Sie im Pagelayout, wo der Kopfbereich ist, Folgendes ein:

   ```text
   {def $avail_translation = language_switcher( $site.uri.original_uri )}
   <nav aria-label="{'Language'|i18n( 'design/mysite' )}"><ul class="languages">
   {foreach $avail_translation as $siteaccess => $lang}
     <li><a href={$lang.url|ezurl}{if $siteaccess|eq( $access_type.name )} aria-current="true"{/if} lang="{$siteaccess|wash}">{$lang.text|wash}</a></li>
   {/foreach}
   </ul></nav>
   {undef $avail_translation}
   ```

   Ersetzen Sie `lang="{$siteaccess|wash}"` durch den Sprachcode, den Ihr Design jedem Siteaccess zuordnet (zum Beispiel
   `fr`).
3. Leeren Sie den INI- und den Template-Cache: `php bin/php/ezcache.php --clear-tag=ini,template`.

## Prüfen

- Auf einer übersetzten Seite führt jeder Link zur selben Seite in der anderen Sprache.
- Die aktuelle Sprache ist markiert; die Links sind per Tastatur erreichbar.
- Auf einer Seite, die es nur in einer Sprache gibt, zeigt der andere Link das, was die Ersatzregeln jenes Siteaccess
  vorsehen.

## Probleme

| Problem | Lösung |
|---|---|
| Links führen zur Startseite | der Siteaccess fehlt in `RelatedSiteAccessList[]` |
| Der Umschalter ist leer | `TranslationSA[]` für den Siteaccess nicht gesetzt, oder INI-Cache |

## Verwandte Seiten

- [Sprachumschalter und Adressen je Sprache](../../../ersteller/website-ersteller/sprachumschalter-und-sprach-urls.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
