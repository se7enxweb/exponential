---
title: site.ini [ContentSecurityPolicy]
audience: developer, admin
category: examples
summary: "The settings of [ContentSecurityPolicy] in site.ini: Mode, Directives, AdditionalSources, ReportOnlyDirectives, ReportOnlyDesigns, ReportOnlyViews, Nonce, NonceDirectives ..."
keywords: site.ini, ContentSecurityPolicy, INI, setting, Mode, Directives, AdditionalSources, ReportOnlyDirectives, ReportOnlyDesigns, ReportOnlyViews, Nonce, NonceDirectives, ReportURI, ReportEndpoint, ReportsPerMinute, ReportsPerMinuteTotal, MaxReportBytes, MaxViolationsPerReport, LogFile
type: generated
generated: exp:help:examples
---
# site.ini [ContentSecurityPolicy]

The block `[ContentSecurityPolicy]` of `settings/site.ini`, with 15 setting(s).

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## Mode

> The Content Security Policy of the pages (doc/eng-us/features/6.0/content-security-policy.md):  
> which scripts, stylesheets, images and connections a page may use. It is sent  
> beside the security headers of \[HTTPHeaderSettings\] SecurityHeaders\[\].  
>
> disabled: no policy  
> report-only: Content-Security-Policy-Report-Only; the browser reports what the  
>   policy would block (developer console, ReportURI) and blocks nothing  
> enforce: Content-Security-Policy; the browser blocks it. Sent as a second  
>   policy after SecurityHeaders\[Content-Security-Policy\], both apply  
> auto (the default): report-only on administration siteaccesses (those whose  
>   \[DesignSettings\] SiteDesign begins with "admin"), disabled on the others  

Default: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
Mode=auto
*/ ?>
```

## Directives

> The policy, Directives\[<directive>\]=<sources>. Set a value empty in an override  
> to leave that directive out; an override that starts with "Directives\[\]" alone  
> replaces the whole list. Values with ";" or "," are ignored. frame-ancestors  
> and sandbox are sent only with Mode=enforce (browsers ignore them in a  
> report-only policy). object-src 'none' blocks <object>/<embed> (the legacy  
> Flash, Windows Media, RealVideo and QuickTime previews, a PDF shown inline).  
> script-src 'self' allows script files of this site only: no inline <script>,  
> no on...= attributes, no javascript: links, no eval(). style-src 'self' allows  
> stylesheet files only: no <style> elements and no style="" attributes (setting  
> element.style from a script file stays allowed).  
> Workers (new Worker, a service worker) of this site only: no blob: or data: worker,  
> which would run code built from a string. Named here (it would otherwise fall back  
> to script-src) so AdditionalSources\[\] can add a host or blob: where a feature needs it.  

Default: a hash of 13: `default-src='self'`, `script-src='self'`, `style-src='self'`, `img-src='self' data: blob:`, `font-src='self' data:`, `media-src='self' blob:`, `connect-src='self'`, `worker-src='self'`, `frame-src='self'`, `object-src='none'`, `base-uri='self'`, `form-action='self'` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
# an empty "Directives[]" first replaces the shipped list; without it the items are added
Directives[]
Directives[default-src]='self'
Directives[script-src]='self'
Directives[style-src]='self'
Directives[img-src]='self' data: blob:
Directives[font-src]='self' data:
Directives[media-src]='self' blob:
Directives[connect-src]='self'
Directives[worker-src]='self'
Directives[frame-src]='self'
Directives[object-src]='none'
Directives[base-uri]='self'
Directives[form-action]='self'
Directives[frame-ancestors]='self'
*/ ?>
```

## AdditionalSources

> Sources added to a directive of Directives\[\], one entry per directive and  
> sources: AdditionalSources\[\]=<directive> <sources>. A list, so an extension and  
> the siteaccess can each add a host without restating the directive. An entry  
> for a directive that Directives\[\] does not have is ignored.  
> Example: AdditionalSources\[\]=script-src https://maps.googleapis.com  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
# an empty "AdditionalSources[]" first replaces the shipped list; without it the items are added
AdditionalSources[]
*/ ?>
```

## ReportOnlyDirectives

> With Mode=enforce: a second, stricter policy sent as  
> Content-Security-Policy-Report-Only (Directives\[\] with these entries in their  
> place), to measure what it would still block while the enforced policy  
> allows it. Empty: none.  
> Example: ReportOnlyDirectives\[style-src\]='self'  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
# an empty "ReportOnlyDirectives[]" first replaces the shipped list; without it the items are added
ReportOnlyDirectives[]
*/ ?>
```

## ReportOnlyDesigns

> Designs whose requests stay report-only under Mode=enforce: the classic  
> administration, with inline code, which a user can pick with the UI switch.  
> The design of the request (after the UI switch) decides.  

Default: a list of 3: `admin`, `admin2`, `admin_classic`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
# an empty "ReportOnlyDesigns[]" first replaces the shipped list; without it the items are added
ReportOnlyDesigns[]
ReportOnlyDesigns[]=admin
ReportOnlyDesigns[]=admin2
ReportOnlyDesigns[]=admin_classic
*/ ?>
```

## ReportOnlyViews

> Views (module/view) that send a policy of their own for their own inline  
> scripts, such as a bundled database tool: report-only under Mode=enforce, so  
> the kernel's policy cannot block them where a server keeps both headers.  
> Example: ReportOnlyViews\[\]=dse/adminneo  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
# an empty "ReportOnlyViews[]" first replaces the shipped list; without it the items are added
ReportOnlyViews[]
*/ ?>
```

## Nonce

> enabled: a nonce of the request, 'nonce-...', is added to the directives of  
> NonceDirectives\[\], for an inline script a template cannot move into a file:  
> <script nonce="{csp\_nonce()}">. Not added where the directive allows  
> 'unsafe-inline' (browsers would ignore 'unsafe-inline' next to it). Never print  
> csp\_nonce() inside a cache-block or a view-cached template.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
Nonce=enabled
*/ ?>
```

## NonceDirectives

Default: a list of 1: `script-src`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
# an empty "NonceDirectives[]" first replaces the shipped list; without it the items are added
NonceDirectives[]
NonceDirectives[]=script-src
*/ ?>
```

## ReportURI

> Where the browser sends its reports (report-uri), e.g. a collector of your  
> own; empty: the developer console only, or with ReportEndpoint=enabled the  
> csp/report view of the siteaccess. An absolute address is also announced as  
> the Reporting-Endpoints endpoint csp-endpoint (report-to).  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
ReportURI=
*/ ?>
```

## ReportEndpoint

> enabled: with ReportURI empty, the reports go to csp/report of the  
> siteaccess (the kernel module csp), which logs them to var/log/csp.log  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
ReportEndpoint=disabled
*/ ?>
```

## ReportsPerMinute

> The reports the view csp/report takes (one line per violation in var/log/csp.log,  
> Setup > Log files, key csp). Per client (the salted address hash of the  
> request classifier), per minute; over it the view answers 429 and the  
> request classifier writes one abuse/csp-report-flood line to security.log.  

Default: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
ReportsPerMinute=60
*/ ?>
```

## ReportsPerMinuteTotal

> All clients together, per minute.  

Default: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
ReportsPerMinuteTotal=600
*/ ?>
```

## MaxReportBytes

> The biggest report body taken, in bytes (413 above it).  

Default: `65536`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
MaxReportBytes=65536
*/ ?>
```

## MaxViolationsPerReport

> The most violations written from one report (the Reporting API sends several).  

Default: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
MaxViolationsPerReport=20
*/ ?>
```

## LogFile

> The log file, in the log directory (var/log).  

Default: `csp.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
LogFile=csp.log
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
