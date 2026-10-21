---
title: Der Sprachumschalter
audience: government, website-creator, developer
category: public-sector
order: 80
summary: Wie Sie mit Exponential einen Sprachumschalter auf jede Seite einer mehrsprachigen öffentlichen Website setzen (TranslationSA, der Operator language_switcher, switchlanguage/to), ihn barrierefrei machen und hreflang-Links hinzufügen, die die mitgelieferten Designs nicht ausgeben.
keywords: Sprachumschalter, language_switcher, TranslationSA, LanguageSA, switchlanguage, hreflang, lang-Attribut, aria-current, Barrierefreiheit, mehrsprachige Navigation, language switcher, lang attribute, accessibility, multilingual navigation
type: help
---
# Der Sprachumschalter

Diese Seite richtet sich an Websiteersteller. Eine öffentliche Website in mehreren Sprachen braucht auf jeder Seite
eine sichtbare Möglichkeit, die Sprache zu wechseln, die zur selben Seite in der anderen Sprache führt. Exponential
baut sie aus `[RegionalSettings] TranslationSA[]` und dem Kernel-Operator `language_switcher`
([Sprachumschalter und Adressen pro Sprache](../../../../eng-us/help/creators/website-creators/language-switcher-and-language-urls.md#the-language-switcher)).

## Inhalt

1. [Die Siteaccesses einrichten](#die-siteaccesses-einrichten)
2. [Das Template](#das-template)
3. [Barrierefrei machen](#barrierefrei-machen)
4. [hreflang-Links für Suchmaschinen](#hreflang-links-für-suchmaschinen)
5. [Wenn eine Seite nicht übersetzt ist](#wenn-eine-seite-nicht-übersetzt-ist)

## Die Siteaccesses einrichten

```ini
# in each public siteaccess, or once in settings/override/site.ini.append.php
[RegionalSettings]
TranslationSA[]
TranslationSA[site_en]=English
TranslationSA[site_fr]=Français
```

Jeder aufgeführte Siteaccess muss auch in `[SiteAccessSettings] RelatedSiteAccessList[]` stehen. Schreiben Sie den
Namen jeder Sprache in dieser Sprache. `LanguageSA[]` ordnet einer Sprache einen Siteaccess zu, wenn die Namen nicht
der Konvention folgen.

## Das Template

```text
{def $avail_translation = language_switcher( $site.uri.original_uri )}
<nav aria-label="{'Language'|i18n( 'design/mysite' )}">
<ul class="languages">
{foreach $avail_translation as $siteaccess => $lang}
  <li><a href={$lang.url|ezurl}{if $siteaccess|eq( $access_type.name )} aria-current="true"{/if}
         lang="{$siteaccess|wash}">{$lang.text|wash}</a></li>
{/foreach}
</ul>
</nav>
{undef $avail_translation}
```

Jeder Link läuft über das Modul `switchlanguage/to`, das dieselbe Seite im anderen Siteaccess findet. Setzen Sie das
Attribut `lang` auf den Sprachcode (zum Beispiel `fr`), nicht auf den Namen des Siteaccess. Leeren Sie danach den
INI- und den Template-Cache.

## Barrierefrei machen

- Jeder Linktext ist der eigene Name der Sprache („Français“, nicht „Französisch“), mit einem `lang`-Attribut, damit
  Screenreader ihn richtig aussprechen ([WCAG 2.2, Sprache von Teilen](https://www.w3.org/TR/WCAG22/)).
- Markieren Sie die aktuelle Sprache (`aria-current`).
- Verwenden Sie keine Flaggen allein: Flaggen stehen für Länder, nicht für Sprachen.
- Platzieren Sie ihn auf jeder Seite an derselben Stelle, per Tastatur erreichbar.

## hreflang-Links für Suchmaschinen

Exponential gibt `hreflang`-Alternativen nicht von selbst aus; bauen Sie sie im Head des Pagelayouts aus demselben
Ergebnis von `language_switcher`, mit vollständigen Adressen:
`<link rel="alternate" hreflang="fr" href="https://www.example.gov/fr/...">`.

## Wenn eine Seite nicht übersetzt ist

Der Umschalter führt zum selben Knoten im anderen Siteaccess. Was dieser Siteaccess zeigt, hängt von seiner
`SiteLanguageList[]` ab: die Seite in einer Ersatzsprache oder nichts, wenn das Objekt in keiner seiner Sprachen
vorliegt und nicht immer verfügbar ist (siehe „Ersatzregeln für unübersetzte Inhalte“). Testen Sie den Umschalter auf
einer Seite, die es nur in einer Sprache gibt.

## Verwandte Seiten

- [Sprachumschalter und Adressen pro Sprache](../../../../eng-us/help/creators/website-creators/language-switcher-and-language-urls.md)
- [Barrierefreiheit für Websiteersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)
- [SEO-Grundlagen](../../../../eng-us/help/creators/website-creators/seo-basics.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
