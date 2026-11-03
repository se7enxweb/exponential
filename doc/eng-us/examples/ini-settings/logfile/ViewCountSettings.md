---
title: logfile.ini [ViewCountSettings]
audience: developer, admin
category: examples
summary: "The settings of [ViewCountSettings] in logfile.ini: SiteAccessList, AllSiteAccesses, MatchSiteAccess, StateDir, LockTimeout, RotatedSuffixes, MaxLineLength, LineRegexp ..."
keywords: logfile.ini, ViewCountSettings, INI, setting, SiteAccessList, AllSiteAccesses, MatchSiteAccess, StateDir, LockTimeout, RotatedSuffixes, MaxLineLength, LineRegexp, ViewModes, Methods, Statuses, SkipExtensions, SkipPathPrefixes, BotUserAgents, SkipEmptyUserAgent
type: generated
generated: exp:help:examples
---
# logfile.ini [ViewCountSettings]

The block `[ViewCountSettings]` of `settings/logfile.ini`, with 15 setting(s).

Never edit `settings/logfile.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/logfile.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/logfile.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/logfile.ini.append.php` in an extension (read when the extension is active).

## SiteAccessList

> Which siteaccesses one run counts. Each reads the access log its own logfile.ini  
> \[AccessLogFileSettings\] names. Empty: only the siteaccess the run is for  
> (runcronjobs.php -s <siteaccess>), as the cronjob always did.  
> Example: SiteAccessList\[\]=site  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# an empty "SiteAccessList[]" first replaces the shipped list; without it the items are added
SiteAccessList[]
*/ ?>
```

## AllSiteAccesses

> enabled: count every siteaccess of AvailableSiteAccessList\[\] that has an access log,  
> except the administrations. SiteAccessList\[\] is not read then.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
AllSiteAccesses=disabled
*/ ?>
```

## MatchSiteAccess

> How the lines of a log are given to siteaccesses. auto: by the kernel's siteaccess  
> matching (site.ini \[SiteAccessSettings\] MatchOrder) when more than one counted siteaccess  
> reads the log, else all lines to the one; enabled: always matched, so a log of several sites  
> counts only the lines of the counted siteaccess; disabled: never matched (every siteaccess of  
> a shared log counts every line).  

Default: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
MatchSiteAccess=auto
*/ ?>
```

## StateDir

> Where the resume state is kept: one file per log, with each siteaccess's place in it.  
> Empty: viewcount below the VarDir of the run's siteaccess. A relative path is below the  
> installation.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
StateDir=
*/ ?>
```

## LockTimeout

> How long a run waits, in seconds, for another run that reads the same log.  

Default: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
LockTimeout=60
*/ ?>
```

## RotatedSuffixes

> The names of a log's rotated copy, appended to its name. After a rotation the rest of the  
> copy is read first, then the new log from its start. A copy is recognized by its inode or its  
> first line, so a compressed copy (.1.gz) is found too.  

Default: a list of 3: `.1`, `.1.gz`, `.0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# an empty "RotatedSuffixes[]" first replaces the shipped list; without it the items are added
RotatedSuffixes[]
RotatedSuffixes[]=.1
RotatedSuffixes[]=.1.gz
RotatedSuffixes[]=.0
*/ ?>
```

## MaxLineLength

> The longest line read, in bytes. Longer lines are skipped.  

Default: `65536`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
MaxLineLength=65536
*/ ?>
```

## LineRegexp

> A log format of its own: a regular expression with named groups time, request, status and,  
> optional, host, ip, agent, referer. Empty: the common, combined and vhost\_combined formats  
> (Apache, nginx, Plesk, Velocity), recognized by each line.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
LineRegexp=
*/ ?>
```

## ViewModes

> The view modes counted for /content/view/<view mode>/<node>. Empty: every view mode.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# an empty "ViewModes[]" first replaces the shipped list; without it the items are added
ViewModes[]
*/ ?>
```

## Methods

> The request methods counted. Empty: every method.  

Default: a list of 1: `GET`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# an empty "Methods[]" first replaces the shipped list; without it the items are added
Methods[]
Methods[]=GET
*/ ?>
```

## Statuses

> The response statuses counted. 304 (not modified) is a view of a page the browser had.  
> Empty: every status.  

Default: a list of 2: `200`, `304`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# an empty "Statuses[]" first replaces the shipped list; without it the items are added
Statuses[]
Statuses[]=200
Statuses[]=304
*/ ?>
```

## SkipExtensions

> Paths ending in these extensions are files, not page views.  

Default: a list of 33: `css`, `js`, `mjs`, `map`, `png`, `jpg`, `jpeg`, `gif`, `webp`, `avif`, `svg`, `ico` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# an empty "SkipExtensions[]" first replaces the shipped list; without it the items are added
SkipExtensions[]
SkipExtensions[]=css
SkipExtensions[]=js
SkipExtensions[]=mjs
SkipExtensions[]=map
SkipExtensions[]=png
SkipExtensions[]=jpg
SkipExtensions[]=jpeg
SkipExtensions[]=gif
SkipExtensions[]=webp
SkipExtensions[]=avif
SkipExtensions[]=svg
SkipExtensions[]=ico
SkipExtensions[]=bmp
SkipExtensions[]=tif
SkipExtensions[]=tiff
SkipExtensions[]=woff
SkipExtensions[]=woff2
SkipExtensions[]=ttf
SkipExtensions[]=otf
SkipExtensions[]=eot
# ... 13 more in the shipped file
*/ ?>
```

## SkipPathPrefixes

> Paths starting with these are not page views: files, services and module views. They are  
> compared with the path as requested and with the path without the siteaccess's part.  

Default: a list of 18: `/var/`, `/design/`, `/extension/`, `/share/`, `/lib/`, `/.well-known/`, `/ezjscore/`, `/api/`, `/user/`, `/layout/`, `/content/search`, `/content/download/` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# an empty "SkipPathPrefixes[]" first replaces the shipped list; without it the items are added
SkipPathPrefixes[]
SkipPathPrefixes[]=/var/
SkipPathPrefixes[]=/design/
SkipPathPrefixes[]=/extension/
SkipPathPrefixes[]=/share/
SkipPathPrefixes[]=/lib/
SkipPathPrefixes[]=/.well-known/
SkipPathPrefixes[]=/ezjscore/
SkipPathPrefixes[]=/api/
SkipPathPrefixes[]=/user/
SkipPathPrefixes[]=/layout/
SkipPathPrefixes[]=/content/search
SkipPathPrefixes[]=/content/download/
SkipPathPrefixes[]=/content/edit/
SkipPathPrefixes[]=/content/action
SkipPathPrefixes[]=/notification/
SkipPathPrefixes[]=/shop/
SkipPathPrefixes[]=/rss/
SkipPathPrefixes[]=/Q/
*/ ?>
```

## BotUserAgents

> Requests whose user agent contains one of these (in any case) are robots, not views.  

Default: a list of 24: `bot`, `crawl`, `spider`, `slurp`, `scrap`, `warmer`, `preview`, `monitor`, `uptime`, `pingdom`, `curl/`, `wget/` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
# an empty "BotUserAgents[]" first replaces the shipped list; without it the items are added
BotUserAgents[]
BotUserAgents[]=bot
BotUserAgents[]=crawl
BotUserAgents[]=spider
BotUserAgents[]=slurp
BotUserAgents[]=scrap
BotUserAgents[]=warmer
BotUserAgents[]=preview
BotUserAgents[]=monitor
BotUserAgents[]=uptime
BotUserAgents[]=pingdom
BotUserAgents[]=curl/
BotUserAgents[]=wget/
BotUserAgents[]=python
BotUserAgents[]=java/
BotUserAgents[]=go-http-client
BotUserAgents[]=okhttp
BotUserAgents[]=libwww
BotUserAgents[]=httpclient
BotUserAgents[]=headless
BotUserAgents[]=lighthouse
# ... 4 more in the shipped file
*/ ?>
```

## SkipEmptyUserAgent

> enabled: a request without a user agent is a robot.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logfile.ini.append.php
[ViewCountSettings]
SkipEmptyUserAgent=enabled
*/ ?>
```

## Related

- [All blocks of logfile.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/logfile.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
