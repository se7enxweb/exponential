---
title: site.ini [HTTPHeaderSettings]
audience: developer, admin
category: examples
summary: "The settings of [HTTPHeaderSettings] in site.ini: CustomHeader, OnlyForAnonymous, OnlyForContent, HeaderList, Cache-Control, Pragma, Expires, ClientIpByCustomHTTPHeader ..."
keywords: site.ini, HTTPHeaderSettings, INI, setting, CustomHeader, OnlyForAnonymous, OnlyForContent, HeaderList, Cache-Control, Pragma, Expires, ClientIpByCustomHTTPHeader, TrustedProxies, SecurityHeaders
type: generated
generated: exp:help:examples
---
# site.ini [HTTPHeaderSettings]

The block `[HTTPHeaderSettings]` of `settings/site.ini`, with 10 setting(s).

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## CustomHeader

> Enable/disable custom HTTP header data.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
CustomHeader=disabled
*/ ?>
```

## OnlyForAnonymous

> Only apply custom headers for anonymous users  

Default: `disabled`.

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

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
OnlyForContent=enabled
*/ ?>
```

## HeaderList

> Header list. Contains all HTTP which should override standard ones.  

Default: a list of 3: `Cache-Control`, `Pragma`, `Expires`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# an empty "HeaderList[]" first replaces the shipped list; without it the items are added
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

Default: a hash of 1: `/=no-cache, must-revalidate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# an empty "Cache-Control[]" first replaces the shipped list; without it the items are added
Cache-Control[]
Cache-Control[/]=no-cache, must-revalidate
*/ ?>
```

## Pragma

> Pragma values are set directly  

Default: a hash of 1: `/=no-cache`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# an empty "Pragma[]" first replaces the shipped list; without it the items are added
Pragma[]
Pragma[/]=no-cache
*/ ?>
```

## Expires

> Expires specifies time offset compared to current time  
> Default expired 2 hours ago ( no caching )  

Default: a hash of 1: `/=-7200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# an empty "Expires[]" first replaces the shipped list; without it the items are added
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

Default: `false`.

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

Default: a list of 2: `127.0.0.1`, `::1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# an empty "TrustedProxies[]" first replaces the shipped list; without it the items are added
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

Default: a hash of 6: `X-Content-Type-Options=nosniff`, `X-Frame-Options=SAMEORIGIN`, `Content-Security-Policy=frame-ancestors 'self'`, `Referrer-Policy=strict-origin-when-cross-origin`, `Permissions-Policy=camera=(), microphone=(), payment=(), usb=()`, `Strict-Transport-Security=`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# an empty "SecurityHeaders[]" first replaces the shipped list; without it the items are added
SecurityHeaders[]
SecurityHeaders[X-Content-Type-Options]=nosniff
SecurityHeaders[X-Frame-Options]=SAMEORIGIN
SecurityHeaders[Content-Security-Policy]=frame-ancestors 'self'
SecurityHeaders[Referrer-Policy]=strict-origin-when-cross-origin
SecurityHeaders[Permissions-Policy]=camera=(), microphone=(), payment=(), usb=()
SecurityHeaders[Strict-Transport-Security]=
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
