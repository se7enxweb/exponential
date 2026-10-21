---
title: Sprachumschalter und Sprach-URLs
audience: website-creator, developer
category: website-creation
order: 260
summary: Jeder Sprache ihre eigene Adresse geben (Pfad, Host oder Host-Präfix), die Startseite auf die Sprache des Besuchers weiterleiten und einen Sprachumschalter mit TranslationSA und dem Operator language_switcher bauen.
keywords: Sprachumschalter, Sprach-URL, Weiterleitung auf die Sprache, Website-Ersteller, language switcher, language_switcher, TranslationSA, LanguageSA, switchlanguage, language URL, hreflang, DefaultHostUriMatchMapItems, redirect to language, Accept-Language, website creator
type: help
---
# Sprachumschalter und Sprach-URLs

Diese Seite richtet sich an Website-Ersteller, die jeder Sprache einer Website ihre eigene Adresse und einen Umschalter
zwischen ihnen geben. Sie behandelt die Wahl der Adressen, die automatische Weiterleitung von der Startseite auf die
Sprache des Besuchers und das Template des Umschalters.

## Inhalt

1. [Adressen pro Sprache](#adressen-pro-sprache)
2. [Die Startseite auf die Sprache des Besuchers weiterleiten](#die-startseite-auf-die-sprache-des-besuchers-weiterleiten)
3. [Der Sprachumschalter](#der-sprachumschalter)
4. [Suchmaschinen informieren](#suchmaschinen-informieren)
5. [Checkliste](#checkliste)

## Adressen pro Sprache

| Form | Beispiel | Eingerichtet mit |
|---|---|---|
| Pfad pro Sprache | `example.com/de/` | `MatchOrder=uri`, `URIMatchType=element`, Siteaccess namens `de` (oder eine Zuordnung) |
| Host pro Sprache | `example.de`, `de.example.com` | `MatchOrder=host`, `HostMatchMapItems[]=example.de;site_de` |
| Host und Pfad | `example.com/de` auf einem Host, andere Hosts anderswo | `host_uri` mit `HostUriMatchMapItems[]` |

![Setup, Sprachen: die Sprachen, in denen Inhalte geschrieben werden können](../../../../eng-us/help/images/creators/languages-setup.webp)

Pfade sind am einfachsten (ein Zertifikat, ein Host); Hosts passen zu Länderdomains. Die Einstellungen stehen in
`site.ini [SiteAccessSettings]` ([10.2](../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).

## Die Startseite auf die Sprache des Besuchers weiterleiten

Mit `DefaultHostUriMatchMapItems[]` leitet eine Adresse ohne Sprachsegment (`example.com/`) einmalig auf die Sprache des
Besuchers weiter, gewählt nach Host und nach dem `Accept-Language` des Browsers, mit einer Adresse pro Sprache und ohne
Cookie ([Siteaccess nach Host-Präfix und die Weiterleitung auf die Sprache](../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md)):

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
DefaultHostUriMatchMapItems[]=www.example.com;ger;site_de;strict;de
DefaultHostUriMatchMapItems[]=www.example.com;eng;site_en
```

Das Format ist `host;uri;siteaccess[;method[;language]]`: Ein deutscher Browser geht zu `/ger`, jeder andere Browser zu
`/eng` (der Eintrag ohne Sprache). `DefaultHostUriRedirect=disabled` zeigt die Seite unter der Adresse selbst an, statt
weiterzuleiten.

## Der Sprachumschalter

1. Benennen Sie die Siteaccesses des Umschalters, in jedem öffentlichen Siteaccess (oder einmal in
   `settings/override/`):

   ```ini
   [RegionalSettings]
   TranslationSA[]
   TranslationSA[site_en]=English
   TranslationSA[site_de]=Deutsch
   ```

   Jeder Siteaccess darin muss auch in `[SiteAccessSettings] RelatedSiteAccessList[]` stehen. `LanguageSA[]` ordnet
   eine Sprache einem Siteaccess zu, wenn die Namen nicht der Konvention folgen (`settings/site.ini` erklärt beides).
2. Geben Sie den Umschalter im Pagelayout mit dem Kernel-Operator `language_switcher` aus
   (`kernel/private/eztemplate/ezplanguageswitcheroperator.php`). Die Datei `page_header_languages.tpl` des Designs
   ezwebin macht es so:

   ```text
   {def $avail_translation = language_switcher( $site.uri.original_uri )}
   <ul class="languages">
   {foreach $avail_translation as $siteaccess => $lang}
     <li><a href={$lang.url|ezurl}{if $siteaccess|eq( $access_type.name )} aria-current="true"{/if}
            lang="{$siteaccess|wash}">{$lang.text|wash}</a></li>
   {/foreach}
   </ul>
   {undef $avail_translation}
   ```

   Jeder Link geht über das Modul `switchlanguage/to`, das dieselbe Seite im anderen Siteaccess findet
   (`LanguageSwitcherClass=ezpLanguageSwitcher` in `site.ini`). Geben Sie dem Attribut `lang` den Sprachcode, den Ihr
   Design verwendet (zum Beispiel `de`), statt des Siteaccess-Namens.
3. Leeren Sie den INI- und den Template-Cache.

## Suchmaschinen informieren

Suchmaschinen verstehen `hreflang`-Alternativen zwischen den Sprachversionen einer Seite. Exponential gibt sie nicht von
selbst aus; bauen Sie sie im Pagelayout aus demselben Ergebnis von `language_switcher`
(`<link rel="alternate" hreflang="de" href="...">`), mit vollständigen Adressen ([SEO-Grundlagen](seo-grundlagen.md)).

## Checkliste

- [ ] Jede Sprache hat ein Adressschema, das überall verwendet wird.
- [ ] `TranslationSA[]` und `RelatedSiteAccessList[]` führen jeden Sprach-Siteaccess auf.
- [ ] Der Umschalter verlinkt auf dieselbe Seite in der anderen Sprache, nicht auf die Startseite.
- [ ] Die aktuelle Sprache ist für Screenreader gekennzeichnet.
- [ ] Gecachte Seiten unterscheiden sich pro Sprache (das tun sie: Jeder Siteaccess hat seinen eigenen Cache).

## Verwandte Seiten

- [Eine mehrsprachige Website bauen](../../../../eng-us/help/creators/website-creators/multilingual-sites.md)
- [Websites mit Schreibrichtung von rechts nach links](websites-von-rechts-nach-links.md)
- [Übersetzungen und Sprachen](../../../../eng-us/features/6.0/translations-and-languages.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
