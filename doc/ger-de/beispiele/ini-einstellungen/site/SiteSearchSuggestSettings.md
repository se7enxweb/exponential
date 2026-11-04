---
title: site.ini [SiteSearchSuggestSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SiteSearchSuggestSettings] in site.ini: Enabled, MinLength, Limit, ExcludeClasses, ThumbnailAlias, Wildcard, PublicCacheTTL, RateLimit ..."
keywords: Einstellung, site.ini, SiteSearchSuggestSettings, INI, setting, Enabled, MinLength, Limit, ExcludeClasses, ThumbnailAlias, Wildcard, PublicCacheTTL, RateLimit, RateWindow
type: generated
generated: exp:help:examples
---
# site.ini [SiteSearchSuggestSettings]

Der Block `[SiteSearchSuggestSettings]` von `settings/site.ini`, mit 9 Einstellung(en).

> Suggestions under the header search field of a site's design while the visitor types (the include  
> design:parts/front\_search\_suggest.tpl in the design's search form; the script javascript/page/search\_suggest.js asks  
> content/sitesuggest/(text)/<text>, expSearchSuggest::siteSuggest()). Per siteaccess: switch it off in the  
> siteaccess's site.ini.append.php. Guide: doc/eng-us/help/searching.md ("Suggestions on the site").  
>
> Only content objects the user may read are suggested (the anonymous user: what is public), with their name, address,  
> class name and a small image; never user accounts, content classes, administration pages or settings. The view  
> needs the policy content/read, as content/search.  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> true or false. false: the field is a plain search field, content/sitesuggest answers 404.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
Enabled=true
*/ ?>
```

## MinLength

> The fewest characters typed before suggestions are asked for.  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
MinLength=3
*/ ?>
```

## Limit

> The most suggestions (0 to 20).  

Standard: `6`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
Limit=6
*/ ?>
```

## ExcludeClasses

> Classes never suggested (besides every class with a user account attribute, which never is).  

Standard: eine Liste mit 2: `user`, `user_group`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
# ein leeres "ExcludeClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExcludeClasses[]
ExcludeClasses[]=user
ExcludeClasses[]=user_group
*/ ?>
```

## ThumbnailAlias

> The image alias of the small picture of a suggestion (image.ini), from the object's first image attribute; empty:  
> the class icon only.  

Standard: `small`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
ThumbnailAlias=small
*/ ?>
```

## Wildcard

> auto, enabled or disabled, as admininterface.ini \[SearchSuggestSettings\] Wildcard.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
Wildcard=auto
*/ ?>
```

## PublicCacheTTL

> Seconds an anonymous answer is kept (on the server and as Cache-Control: public, max-age), 0 to 3600. 0: not kept.  
> A signed-in user's answer is never kept (private, no-store).  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
PublicCacheTTL=60
*/ ?>
```

## RateLimit

> Requests of one address within RateWindow seconds; more are answered 429 with Retry-After. 0: no limit.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
RateLimit=60
*/ ?>
```

## RateWindow

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
RateWindow=60
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
