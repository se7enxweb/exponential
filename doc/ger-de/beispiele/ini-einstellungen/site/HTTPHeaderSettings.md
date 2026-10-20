---
title: site.ini [HTTPHeaderSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HTTPHeaderSettings] in site.ini: CustomHeader, OnlyForAnonymous, OnlyForContent, HeaderList, Cache-Control, Pragma, Expires, ClientIpByCustomHTTPHeader ..."
keywords: Einstellung, site.ini, HTTPHeaderSettings, INI, setting, CustomHeader, OnlyForAnonymous, OnlyForContent, HeaderList, Cache-Control, Pragma, Expires, ClientIpByCustomHTTPHeader, TrustedProxies, SecurityHeaders
type: generated
generated: exp:help:examples
---
# site.ini [HTTPHeaderSettings]

Der Block `[HTTPHeaderSettings]` von `settings/site.ini`, mit 10 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## CustomHeader

> Enable/disable custom HTTP header data.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
CustomHeader=disabled
*/ ?>
```

## OnlyForAnonymous

> Only apply custom headers for anonymous users  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
OnlyForAnonymous=disabled
*/ ?>
```

## OnlyForContent

> Only apply the fallback root \[/\] custom headers for the content/view module  
> If disabled, custom headers for the root path will be applied to all modules and their views  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
OnlyForContent=enabled
*/ ?>
```

## HeaderList

> Header list. Contains all HTTP which should override standard ones.  

Standard: eine Liste mit 3: `Cache-Control`, `Pragma`, `Expires`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# ein leeres "HeaderList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HeaderList[]
HeaderList[]=Cache-Control
HeaderList[]=Pragma
HeaderList[]=Expires
*/ ?>
```

## Cache-Control

> Default Cache-Control header  
> HTTP Headers are specified using the following format :  
> <HTTP header>\[<Exponential path|module{/view}>\]=<value>{;<depth>{;<level>}}  
> Note: to use a semicolon in the value, you need to escape it with a backslash.  
>
> Example :  
> # Set Pragma HTTP header to no-cache for whole site, except /news, and 2 levels below news.  
> Pragma\[\]  
> Pragma\[/\]=no-cache;2  
> Pragma\[/news\]=;2;0  
> Cache-Control values are set directly  

Standard: ein Hash mit 1: `/=no-cache, must-revalidate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# ein leeres "Cache-Control[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Cache-Control[]
Cache-Control[/]=no-cache, must-revalidate
*/ ?>
```

## Pragma

> Pragma values are set directly  

Standard: ein Hash mit 1: `/=no-cache`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# ein leeres "Pragma[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Pragma[]
Pragma[/]=no-cache
*/ ?>
```

## Expires

> Expires specifies time offset compared to current time  
> Default expired 2 hours ago ( no caching )  

Standard: ein Hash mit 1: `/=-7200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# ein leeres "Expires[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Expires[]
Expires[/]=-7200
*/ ?>
```

## ClientIpByCustomHTTPHeader

> Use a custom HTTP header to get the client IP  
> false is the default value  
> The header is read only for a request from one of the TrustedProxies below,  
> and from the right: trusted proxies are skipped and the first other address  
> is the client. Entries further left are what the client sent and are ignored.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
ClientIpByCustomHTTPHeader=false
*/ ?>
```

## TrustedProxies

> Useful example when client get connected via (reverse-)proxy  
> ClientIpByCustomHTTPHeader=X-Forwarded-For  
> The reverse proxies and load balancers in front of this site whose forwarded  
> headers can be believed: X-Forwarded-For (with ClientIpByCustomHTTPHeader),  
> X-Forwarded-Proto, X-Forwarded-Port, X-Forwarded-Server and X-Forwarded-Host.  
> They are request headers any visitor can send, so they count only when the  
> connection comes from one of these addresses (REMOTE\_ADDR). Addresses and  
> address/prefix ranges, IPv4 and IPv6, e.g.  
>  TrustedProxies\[\]=10.0.0.0/8  
>  TrustedProxies\[\]=192.0.2.10  
>  TrustedProxies\[\]=2001:db8::/32  
> The default trusts a proxy on this machine only. A trusted proxy must set  
> these headers itself, not pass on what the visitor sent.  
> An override that starts with "TrustedProxies\[\]" alone replaces the list;  
> leave it empty to trust no proxy at all.  
> See doc/eng-us/bc/6.0/trusted-proxies.md.  

Standard: eine Liste mit 2: `127.0.0.1`, `::1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# ein leeres "TrustedProxies[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TrustedProxies[]
TrustedProxies[]=127.0.0.1
TrustedProxies[]=::1
*/ ?>
```

## SecurityHeaders

> Security headers sent with every page, SecurityHeaders\[<header>\]=<value>.  
> Set a value empty in an override to drop that header. HeaderList above  
> still overrides these per path.  
>  - frame-ancestors / X-Frame-Options: pages may only be framed by the  
>    same site (clickjacking); allow a trusted editor host explicitly, e.g.  
>    SecurityHeaders\[Content-Security-Policy\]=frame-ancestors 'self' https://edit.example.com  
>  - Strict-Transport-Security is only sent over HTTPS and is off by  
>    default: enable it once the whole site is HTTPS-only, e.g.  
>    SecurityHeaders\[Strict-Transport-Security\]=max-age=31536000  

Standard: ein Hash mit 6: `X-Content-Type-Options=nosniff`, `X-Frame-Options=SAMEORIGIN`, `Content-Security-Policy=frame-ancestors 'self'`, `Referrer-Policy=strict-origin-when-cross-origin`, `Permissions-Policy=camera=(), microphone=(), payment=(), usb=()`, `Strict-Transport-Security=`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# ein leeres "SecurityHeaders[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecurityHeaders[]
SecurityHeaders[X-Content-Type-Options]=nosniff
SecurityHeaders[X-Frame-Options]=SAMEORIGIN
SecurityHeaders[Content-Security-Policy]=frame-ancestors 'self'
SecurityHeaders[Referrer-Policy]=strict-origin-when-cross-origin
SecurityHeaders[Permissions-Policy]=camera=(), microphone=(), payment=(), usb=()
SecurityHeaders[Strict-Transport-Security]=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
