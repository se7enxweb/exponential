---
title: image.ini [AliasSettings]
audience: developer, admin
category: examples
summary: "The settings of [AliasSettings] in image.ini: AliasList, AliasGeneration, OnDemandURL, Delivery, MaxWait, DraftTokenLifetime, SigningKeyGrace, PreferredConverter ..."
keywords: image.ini, AliasSettings, INI, setting, AliasList, AliasGeneration, OnDemandURL, Delivery, MaxWait, DraftTokenLifetime, SigningKeyGrace, PreferredConverter, Negotiate, Placeholder, SwapScript, PreGenerate, PreGenerateAliasList, PreGenerateFormats, MaxParallel, BulkNice, CacheMaxAge
type: generated
generated: exp:help:examples
---
# image.ini [AliasSettings]

The block `[AliasSettings]` of `settings/image.ini`, with 17 setting(s).

> -------- Image alias settings --------  

Never edit `settings/image.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/image.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in an extension (read when the extension is active).

## AliasList

> Defines a list of aliases that are available to  
> the template engine and other clients.  
> The alias must be defined as a separate INI block.  

Default: a list of 6: `reference`, `small`, `tiny`, `medium`, `large`, `rss`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
# an empty "AliasList[]" first replaces the shipped list; without it the items are added
AliasList[]
AliasList[]=reference
AliasList[]=small
AliasList[]=tiny
AliasList[]=medium
AliasList[]=large
AliasList[]=rss
*/ ?>
```

## AliasGeneration

> AliasList\[\]=rssitem  
> AliasList\[\]=gray  
> AliasList\[\]=zoom  
> How image aliases are made.  
>   on-demand  (the default) a template that shows an alias gets its URL, file name, MIME type, width  
>              and height computed, without converting anything while the page renders. The alias is  
>              made when the browser asks for it: by the view content/image, or by index\_image.php for a  
>              storage path the web server does not find (see .htaccess\_root). Command line scripts  
>              (cronjobs, mailers, exports) keep making aliases at once, as in render mode.  
>   render     the original behavior: the template call converts the image while the page renders.  
> The setting can be switched both ways at any time; no data is migrated. Aliases made in one mode are  
> used by the other.  

Default: `on-demand`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
AliasGeneration=on-demand
*/ ?>
```

## OnDemandURL

> The URL a template gets for an alias that is not made yet (on-demand mode):  
>   token      /content/image/<token>/<file name>; the signed token carries everything the view needs,  
>              so a request needs no database until the alias is made  
>   ids-token  /content/image/<attribute>/<version>/<language>/<alias>/<token>/<file name>  
>   ids        /content/image/<attribute>/<version>/<language>/<alias>/<file name> (unsigned: serves an  
>              alias that exists, never makes one)  
>   node       /content/image/<node>/<attribute identifier>/<alias> (unsigned, as ids)  
>   storage    the plain storage path, as render mode gives it; a miss reaches index\_image.php, which  
>              makes the alias when the path names a configured alias of a stored image  

Default: `token`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
OnDemandURL=token
*/ ?>
```

## Delivery

> How content/image answers with an alias that exists:  
>   auto      a redirect (302) to the storage URL when the web server serves the storage (a single server  
>             or shared file system), the file itself otherwise (a database cluster, drafts)  
>   redirect  always the redirect  
>   stream    always the file itself  

Default: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
Delivery=auto
*/ ?>
```

## MaxWait

> Seconds a request may spend making (or fetching) an alias, waiting for another process and for a  
> conversion slot (MaxParallel) included. When it takes longer the request answers the placeholder,  
> queues the alias for the job queue (when expqueue is there), and the browser asks again later. No new  
> conversion starts after MaxWait; one that has started is finished (stopping it would lose the work).  

Default: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
MaxWait=2
*/ ?>
```

## DraftTokenLifetime

> Seconds a signed URL of a draft or preview image (images-versioned) stays valid; published images  
> get URLs that do not expire.  

Default: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
DraftTokenLifetime=3600
*/ ?>
```

## SigningKeyGrace

> Seconds the previous signing key (image\_alias\_signing\_previous in Setup > Secrets) is still accepted  
> after the key was changed, so pages cached with the old URLs keep working.  

Default: `604800`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
SigningKeyGrace=604800
*/ ?>
```

## PreferredConverter

> The converter that writes the format variants (WebP, AVIF, see Formats\[\] below):  
>   auto         the imagick extension, then the ImageMagick command of \[ImageMagick\] (while IsEnabled=true),  
>                then GD: the first of them that can write the format (Imagick::queryFormats(),  
>                "convert -list format", gd\_info())  
>   imagick | imagemagick | gd   tried first, then the others in the auto order  
> A format no converter can write is skipped: the browser gets the alias in its own format.  
> The aliases of AliasList\[\] themselves are made by \[ImageConverterSettings\] ImageConverters\[\] in both  
> modes, so an alias made on demand is byte for byte the one render mode makes.  

Default: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
PreferredConverter=auto
*/ ?>
```

## Negotiate

> content/image answers with a WebP or AVIF variant of an alias when the browser's Accept header names  
> the type (image/webp, image/avif; image/\* does not count) and the alias block lists the format, with  
> Vary: Accept on every answer for that alias. In on-demand mode such an alias is linked through  
> content/image also once it is made (OnDemandURL token or ids-token), so every visit is negotiated.  
> disabled: aliases are always answered in their own format.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
Negotiate=enabled
*/ ?>
```

## Placeholder

> A neutral image of the right size (an SVG that follows the dark mode of the browser, no text, no  
> logo) while an image is missing, instead of a broken image. Applies to any missing image.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
Placeholder=enabled
*/ ?>
```

## SwapScript

> The placeholders carry the marker (Server-Timing: exp-image-placeholder) that the small script  
> design/standard/javascript/exp\_image\_swap.js looks for; it asks again and swaps in the real image.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
SwapScript=enabled
*/ ?>
```

## PreGenerate

> After publishing, every configured alias of the new images is made in the background, so the first  
> visitor finds them ready: the union of AliasList\[\] of this siteaccess and its related siteaccesses  
> (site.ini \[SiteAccessSettings\] RelatedSiteAccessList\[\]), each made as the siteaccess that lists it  
> defines it, plus the WebP/AVIF variants of Formats\[\]. It is a job of the job queue (expqueue, job type  
> image.alias.generate in settings/expqueue.ini); without the queue nothing is converted while  
> publishing (the aliases are made on demand, imagealias.log says "no-queue"). On-demand mode only.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
PreGenerate=enabled
*/ ?>
```

## PreGenerateAliasList

> Limits pre-generation to these aliases, one per line (PreGenerateAliasList\[\]=small). Each is still made  
> as the siteaccess that lists it in AliasList\[\] defines it. A name that no siteaccess of the union  
> defines is skipped, with a warning and one imagealias.log line per publication (event=queue  
> result=unknown-alias). Every other alias is made on demand, when a page first asks for it.  
> Empty (the default): every alias of the union.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
# an empty "PreGenerateAliasList[]" first replaces the shipped list; without it the items are added
PreGenerateAliasList[]
*/ ?>
```

## PreGenerateFormats

> Whether pre-generation also makes the WebP/AVIF variants (Formats\[\] of the alias blocks) of the  
> aliases it makes. disabled: only the aliases themselves; the variants are made on demand.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
PreGenerateFormats=enabled
*/ ?>
```

## MaxParallel

> Conversions (aliases and variants) at once per server, counted over every process: PHP-FPM, Velocity  
> and the command line (queue workers, scripts). A slot is a lock file in  
> <cache dir>/imagealias-slots/<server name>/. A request that finds every slot taken waits within  
> MaxWait, then answers the placeholder and queues the alias at the visitors' priority (job type  
> image.alias.generate), so a page full of new images never runs more than this many conversions. A  
> conversion that has started is always finished. On-demand mode only.  

Default: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
MaxParallel=2
*/ ?>
```

## BulkNice

> The nice value (0..19) a bulk run (job type image.alias.bulk: generate, regenerate or purge over a  
> scope) lowers its worker process to, so it gives way to the web server; an ImageMagick command it  
> starts inherits it. 0 leaves the priority as it is. Bulk runs can be paused and resumed in  
> Setup > Image aliases or on the command line; a paused run goes on where it stopped.  

Default: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
BulkNice=10
*/ ?>
```

## CacheMaxAge

> Cache-Control max-age (seconds) of a real alias answered by content/image or index\_image.php;  
> placeholders are always sent with no-store.  

Default: `31536000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
CacheMaxAge=31536000
*/ ?>
```

## Related

- [All blocks of image.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/image.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
