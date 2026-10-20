---
title: velocity.ini [CacheSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CacheSettings] in velocity.ini: Enabled, DefaultTtl, Dir, APCu, APCuMaxSize, SweepEvery, SweepMaxAge, FileMode ..."
keywords: Einstellung, velocity.ini, CacheSettings, INI, setting, Enabled, DefaultTtl, Dir, APCu, APCuMaxSize, SweepEvery, SweepMaxAge, FileMode, DirMode, SkipCookies, StaleWhileRevalidate, NotFoundSeconds, HonorRequestNoCache, MinifyHtml, MiddleOutCompression
type: generated
generated: exp:help:examples
---
# velocity.ini [CacheSettings]

Der Block `[CacheSettings]` von `settings/velocity.ini`, mit 15 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> The server's response cache, in front of the workers. A hit is answered by  
> the parent process -- no fork, no PHP, no database. Measured on this  
> installation, anonymous front page: about 3,500 req/s from the cache  
> against 27 rendered.  
>
> The settings further down that used to live in \[ServerSettings\] as  
> CacheSkipCookies, CacheStaleWhileRevalidate, CacheNotFoundSeconds,  
> MinifyCachedHtml and MiddleOutCompression still work there; a value here  
> wins.  
>
> Needs qbix-webserver v0.0.4.26 or later: older versions matched the session  
> cookie by its exact name, never recognised eZSESSID<digest>, and served a  
> signed-in visitor's pages to everybody.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
Enabled=enabled
*/ ?>
```

## DefaultTtl

> Seconds an anonymous page is kept. Exponential sends  
> Cache-Control: no-cache, must-revalidate, which the server does not treat as  
> a refusal, so a page is stored only when it has a lifetime -- its own  
> max-age, or this. 0 caches only responses that bring their own.  
>
> The trade: a visitor may see a page up to this old, and a page that differs  
> between anonymous visitors without a cookie telling them apart would be  
> shared between them. Signed-in visitors are never served from the cache.  

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
DefaultTtl=30
*/ ?>
```

## Dir

> Where entries too large for shared memory are kept. Relative paths resolve  
> against the installation root. Left to itself the server would put them in  
> files/cache/reverse of the directory above the document root.  

Standard: `var/cache/qbix-reverse`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
Dir=var/cache/qbix-reverse
*/ ?>
```

## APCu

> Small responses in APCu, shared by every worker; larger ones on disk.  
> APCuMaxSize is the size limit in bytes, 0 leaves the server's 65536.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
APCu=enabled
*/ ?>
```

## APCuMaxSize

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
APCuMaxSize=0
*/ ?>
```

## SweepEvery

> Clearing expired entries off the disk, every SweepEvery seconds (0 switches  
> it off). SweepMaxAge removes anything older than that regardless of its own  
> lifetime -- without it, a response that says max-age=31536000 stays a year.  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
SweepEvery=300
*/ ?>
```

## SweepMaxAge

Standard: `86400`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
SweepMaxAge=86400
*/ ?>
```

## FileMode

> Permissions, octal, as in \[LogSettings\]. A cached page is what an anonymous  
> visitor sees, but nothing else on the machine needs to read it.  

Standard: `0640`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
FileMode=0640
*/ ?>
```

## DirMode

Standard: `0750`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
DirMode=0750
*/ ?>
```

## SkipCookies

> Which cookies mean "this response is personal, do not cache it", matched as  
> a prefix. Empty means the session cookie plus is\_logged\_in, which the kernel  
> sets for signed-in visitors for exactly this purpose. The session cookie's  
> name follows \[Session\] SessionNameHandler in site.ini: with "default" PHP  
> names it (PHPSESSID); with "custom" it is SessionNamePrefix, which as a  
> prefix also covers every siteaccess's <prefix><digest>.  
>
> To name them yourself, list every one -- a list replaces the default:  
>   SkipCookies\[\]  
>   SkipCookies\[\]=PHPSESSID  
>   SkipCookies\[\]=is\_logged\_in  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
SkipCookies=
*/ ?>
```

## StaleWhileRevalidate

> Seconds an expired page may still be served while one request renders the  
> replacement, so that an expiry does not send every visitor to PHP at once.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
StaleWhileRevalidate=60
*/ ?>
```

## NotFoundSeconds

> Seconds a 404 is remembered, so a crawler walking dead links does not run the  
> full rendering path for each. 0 (the default) remembers none: every 404  
> reaches PHP, as under Apache, so 404.log and the request classifier  
> (security.log) see each one -- a 404 answered from the cache was seen once a  
> minute at most. A 404 the kernel marks private, no-store is never kept anyway.  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
NotFoundSeconds=0
*/ ?>
```

## HonorRequestNoCache

> A request carrying Cache-Control: no-cache (or Pragma: no-cache) is sent to  
> PHP instead of being answered from the cache, and the answer is stored again.  
> A browser's ordinary reload (max-age=0) is still answered from the cache.  
> Needs exponential-velocity with Q.web.cache.honorRequestNoCache; older  
> versions ignore it.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
HonorRequestNoCache=enabled
*/ ?>
```

## MinifyHtml

> Collapse the templates' indentation in stored pages: 27 % smaller documents.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
MinifyHtml=enabled
*/ ?>
```

## MiddleOutCompression

> Compress stored entries against a shared dictionary. Measured and left off:  
> entries are stored already gzipped, and a dictionary cannot shrink that.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[CacheSettings]
MiddleOutCompression=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
