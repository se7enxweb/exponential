---
title: image.ini [AliasStaging]
audience: developer, admin
category: examples
summary: "The settings of [AliasStaging] in image.ini: Environment, SyncFrom, PathMap, Timeout, MaxFileSize, FailuresBeforePause, PauseSeconds, AlertEmail ..."
keywords: image.ini, AliasStaging, INI, setting, Environment, SyncFrom, PathMap, Timeout, MaxFileSize, FailuresBeforePause, PauseSeconds, AlertEmail, AlertRecipients
type: generated
generated: exp:help:examples
---
# image.ini [AliasStaging]

The block `[AliasStaging]` of `settings/image.ini`, with 9 setting(s).

> An alias block may list extra formats for the browsers that accept them (see Negotiate):  
> Formats\[\]=webp  
> Formats\[\]=avif  
> The order is the preference when a browser accepts both equally. A variant is made on demand from the  
> alias file and stored next to it as "<alias file>.<format>" (foo\_large.jpg.webp); neither the  
> attribute's XML nor ezimagefile name it, so they stay exactly as render mode writes them. It is made  
> again when the alias is newer, and removed with its alias. Its storage path also works on its own  
> (index\_image.php makes it), for web servers that negotiate themselves. Quality: the alias block's  
> Quality\[\]=image/webp;80, else \[MIMETypeSettings\] Quality\[\]=image/webp;80, else 80 (WebP) and 60 (AVIF).  
> GIF and SVG aliases get no variants.  

Never edit `settings/image.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/image.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in an extension (read when the extension is active).

## Environment

> Staging: a site that is not the production site fetches the images it is missing (originals and  
> aliases) from another installation, so a development or staging site needs no copy of var/storage.  
> A missing alias is fetched from the source first; when the source does not have it either, its  
> original is fetched and the alias is made here. A missing original asked for by its storage path is  
> fetched. All within \[AliasSettings\] MaxWait; after that the placeholder is answered and the browser asks  
> again. Only published images (not drafts) are staged. A resync (Setup > Image aliases, or the command  
> line) fetches the files of many images at once: missing ones, or also those that differ.  
>
> The environments (one per installation of the same site):  
>   alpha  early test installation of new code, far from release  
>   dev    a developer's installation  
>   beta   a preview for testers and editors  
>   stage  the pre-production copy: the last check before a release goes live  
>   pro    production, the live site: the source of the others; it never fetches anything  
>   post   post-production: a copy of production after a release, for checks and hotfixes  
>   arc    an archive: a frozen copy of an earlier production  
> Most sites only need a dev or stage installation that syncs from pro; syncing from pro (production) is  
> the common start.  
>
> This installation's environment; pro (the default) never fetches. An unknown value counts as pro.  

Default: `pro`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
Environment=pro
*/ ?>
```

## SyncFrom

> The environment to fetch missing images from (for example pro); its \[AliasSource\_<environment>\] block  
> names where it is. Empty: no staging.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
SyncFrom=
*/ ?>
```

## PathMap

> Path map for a source whose storage paths differ from this site's: <local prefix>;<source prefix>, the  
> source prefix a path below BaseURL or a whole URL. The longest matching local prefix wins; without a  
> match a file is fetched from BaseURL plus its own storage path.  
> PathMap\[\]=var/site/storage;var/production/storage  
> PathMap\[\]=var/site/storage;https://media.example.org/var/production/storage  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
# an empty "PathMap[]" first replaces the shipped list; without it the items are added
PathMap[]
*/ ?>
```

## Timeout

> Seconds one download may take (never more than what is left of MaxWait on a page request).  

Default: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
Timeout=5
*/ ?>
```

## MaxFileSize

> The largest file fetched, in bytes; a larger one is not stored.  

Default: `67108864`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
MaxFileSize=67108864
*/ ?>
```

## FailuresBeforePause

> After this many failures in a row (no answer, a timeout, a refused or invalid answer such as an HTML  
> error page, a file the source has neither as alias nor as original) the source is paused (circuit  
> breaker)...  

Default: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
FailuresBeforePause=5
*/ ?>
```

## PauseSeconds

> ...for this many seconds: missing images get the placeholder at once, nobody waits for the source.  
> The pause, its reason and the last error are shown in Setup > Image aliases; one file that comes  
> through again ends the count.  

Default: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
PauseSeconds=300
*/ ?>
```

## AlertEmail

> When a source is paused, an e-mail goes to AlertRecipients\[\] (at most once an hour per source).  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
AlertEmail=enabled
*/ ?>
```

## AlertRecipients

> The addresses of that e-mail; none: site.ini \[MailSettings\] AdminEmail.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasStaging]
# an empty "AlertRecipients[]" first replaces the shipped list; without it the items are added
AlertRecipients[]
*/ ?>
```

## Related

- [All blocks of image.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/image.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
