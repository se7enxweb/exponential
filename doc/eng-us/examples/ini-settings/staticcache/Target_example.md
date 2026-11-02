---
title: staticcache.ini [Target_example]
audience: developer, admin
category: examples
summary: "The settings of [Target_example] in staticcache.ini: Name, SiteAccess, SiteAccessList, DefaultSiteAccess, Kind, Remote, Branch, Folder ..."
keywords: staticcache.ini, Target_example, INI, setting, Name, SiteAccess, SiteAccessList, DefaultSiteAccess, Kind, Remote, Branch, Folder, Delivery, Links, BasePath, PublicURL, CustomDomain, Credential, CrawlURL, LiveURL, OutsideLinks, AdminLinks, MaxPages, MaxDepth, Assets, Extras, PostProcess, Search, Forms, ExcludePaths, ExcludeSections, Triggers, Schedule, Notify
type: generated
generated: exp:help:examples
---
# staticcache.ini [Target_example]

The block `[Target_example]` of `settings/staticcache.ini`, with 32 setting(s).

Never edit `settings/staticcache.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/staticcache.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/staticcache.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/staticcache.ini.append.php` in an extension (read when the extension is active).

## Name

> A target, as a template: copy the block as \[Target\_<id>\] in  
> settings/override/staticcache.ini.append.php and add <id> to TargetList\[\].  
> This block is not built (TargetList\[\] does not name it). Settings left out  
> of a target take the values shown here.  
>
> The name shown in lists and the administration.  

Default: `Example target`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
Name=Example target
*/ ?>
```

## SiteAccess

> The front siteaccess to publish. Administration and editing siteaccesses  
> (those that require a login or use an administration design) are refused.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
SiteAccess=
*/ ?>
```

## SiteAccessList

> Several front siteaccesses in one copy, instead of SiteAccess: sites that  
> share one host by their URI prefix (site.ini \[SiteAccessSettings\] MatchOrder  
> with uri, or host\_uri with a URI), such as site, bold and bold\_ger. Each  
> one but the default is written to the folder of its prefix (/bold\_ger/...);  
> the links between them stay in the copy, and links to a siteaccess not  
> listed are links outside the copy (OutsideLinks). When it names any,  
> SiteAccess is not read.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
# an empty "SiteAccessList[]" first replaces the shipped list; without it the items are added
SiteAccessList[]
*/ ?>
```

## DefaultSiteAccess

> The siteaccess of SiteAccessList\[\] served at the root of the copy. Empty:  
> every siteaccess is in its folder, and the root is a page that links to  
> each. A target of one siteaccess always has it at the root.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
DefaultSiteAccess=
*/ ?>
```

## Kind

> Where the build goes: github-pages (GitHub Pages) or git (any git host).  

Default: `github-pages`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
Kind=github-pages
*/ ?>
```

## Remote

> The git address the build is pushed to (no password or token in it).  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
Remote=
*/ ?>
```

## Branch

> The branch the build is pushed to: gh-pages, or main with Folder=docs.  

Default: `gh-pages`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
Branch=gh-pages
*/ ?>
```

## Folder

> The folder of the branch the site is in: empty for the root, or docs.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
Folder=
*/ ?>
```

## Delivery

> How GitHub Pages receives the build: branch (pushed to Branch) or actions  
> (a GitHub Actions workflow publishes it).  

Default: `branch`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
Delivery=branch
*/ ?>
```

## Links

> How links are written: relative (../about-us/, works below any path) or  
> base-path (/my-site/about-us/, below BasePath).  

Default: `relative`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
Links=relative
*/ ?>
```

## BasePath

> The path the site is served below: /<repository>/ for a GitHub project  
> site, empty or / for a custom domain or a user site. 404.html links below  
> it whatever Links says, and exp:static:serve serves the build below it.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
BasePath=
*/ ?>
```

## PublicURL

> The public address of the site (https://owner.github.io/my-site/), for the  
> sitemap, robots.txt and the canonical and social card links. Empty: they  
> are written like any other link and no sitemap is written.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
PublicURL=
*/ ?>
```

## CustomDomain

> A custom domain (www.example.com): written to CNAME; with it the site is  
> served at the root of the domain.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
CustomDomain=
*/ ?>
```

## Credential

> The credential the push uses: token:<secret name> (or token:env:<VARIABLE>)  
> for an https Remote, sshkey:<secret name> for an SSH Remote (exp:static:keys  
> <target> --generate makes the key: sshkey:staticpublish.<target>.deploy\_key),  
> app:<installation> (a GitHub App, with the GitHub integration), or none (the  
> host's own SSH agent or credential helper). Only a name; the value stays in  
> Setup > Secrets and reaches git only through its environment.  

Default: `none`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
Credential=none
*/ ?>
```

## CrawlURL

> Fetch the pages from this address instead of the siteaccess's SiteURL  
> (an internal address of the same site, http://127.0.0.1:8080).  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
CrawlURL=
*/ ?>
```

## LiveURL

> Where links to what the build does not hold go (the search, the login, a  
> form, another siteaccess): the live site, https://www.example.com. Empty:  
> https:// and the host of the siteaccess's SiteURL.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
LiveURL=
*/ ?>
```

## OutsideLinks

> Links to pages of the site's own host (or LiveURL) that the build does not  
> hold (a module view such as /user/login or /shop/basket, a page with a  
> query string, a page left out, another siteaccess): keep (they go to the  
> live site, or stay as they are without one) or unlink (the link stays an  
> <a> with its classes but without an address, marked aria-disabled and  
> greyed out by the build's exp-static.css, so the theme's look holds).  
> Images, style sheets, forms (see Forms) and links to other hosts are never  
> taken out.  

Default: `keep`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
OutsideLinks=keep
*/ ?>
```

## AdminLinks

> Links into an administration siteaccess (/admin/..., on the site's own host  
> or LiveURL): keep (as OutsideLinks=keep) or unlink. The administration  
> siteaccesses are the ones the target's checks already know: a login is  
> required, an administration design, or a name such as admin or editor.  

Default: `keep`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
AdminLinks=keep
*/ ?>
```

## MaxPages

> The most pages one build crawls (1 to 20000).  

Default: `10000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
MaxPages=10000
*/ ?>
```

## MaxDepth

> How many links away from the front page the crawl goes (0 to 30). Empty:  
> \[CacheSettings\] MaxCacheDepth.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
MaxDepth=
*/ ?>
```

## Assets

> The files copied: referenced (what the pages and style sheets name) or  
> referenced-and-storage (also every image of var/<site>/storage that belongs  
> to published content the anonymous user may see).  

Default: `referenced-and-storage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
Assets=referenced-and-storage
*/ ?>
```

## Extras

> The files written besides the pages: nojekyll (.nojekyll, so GitHub Pages  
> publishes the files as they are), 404 (404.html from the site's own 404  
> page), robots (robots.txt), sitemap (sitemap.xml, needs PublicURL),  
> manifest (.static-publish/manifest.json; always written), redirects (a  
> small page for every old address and custom alias of a page, and for URL  
> wildcards without \*), readme (README.md), license (LICENSE, from License  
> below), workflow (the GitHub Actions workflow). CNAME is written whenever  
> CustomDomain is set.  

Default: a list of 5: `nojekyll`, `404`, `robots`, `sitemap`, `manifest`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
# an empty "Extras[]" first replaces the shipped list; without it the items are added
Extras[]
Extras[]=nojekyll
Extras[]=404
Extras[]=robots
Extras[]=sitemap
Extras[]=manifest
*/ ?>
```

## PostProcess

> What is done to every page: strip (always) removes debug output, form  
> tokens, session ids and Content Security Policy nonces. Optional: minify  
> (less white space, no comments), precompress (.gz, and .br with PHP's  
> brotli extension, next to every text file), fingerprint (style sheets and  
> scripts named by the hash of their content, site.3f2a9c1b.css).  

Default: a list of 1: `strip`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
# an empty "PostProcess[]" first replaces the shipped list; without it the items are added
PostProcess[]
PostProcess[]=strip
*/ ?>
```

## Search

> A search page for the static copy (search.html, an index per language and  
> a small script; the site's search forms go there): enabled or disabled.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
Search=disabled
*/ ?>
```

## Forms

> What happens to information collectors (forms): hide (the fallback text,  
> FormFallbackText), or endpoint (they post to the form endpoint of the live  
> site; needs PublicURL or CustomDomain). Comment, basket and login forms  
> are never sent from a static copy.  

Default: `hide`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
Forms=hide
*/ ?>
```

## ExcludePaths

> Pages left out, by their path in the site (/members, /shop/\*); \* matches  
> any part. Links to them go to the live site.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
# an empty "ExcludePaths[]" first replaces the shipped list; without it the items are added
ExcludePaths[]
*/ ?>
```

## ExcludeSections

> Pages left out by the section of their content (section ids).  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
# an empty "ExcludeSections[]" first replaces the shipped list; without it the items are added
ExcludeSections[]
*/ ?>
```

## Triggers

> What may start a build: manual, publish (the cronjob part staticpublish  
> publishes the changed pages after a content publish), schedule (the same  
> cronjob part publishes in full at the times of Schedule), cli.  

Default: a list of 2: `manual`, `cli`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
# an empty "Triggers[]" first replaces the shipped list; without it the items are added
Triggers[]
Triggers[]=manual
Triggers[]=cli
*/ ?>
```

## Schedule

> When a scheduled build runs (a cron expression, the server's time zone:  
> 0 3 \* \* \*, or @daily), for Triggers\[\]=schedule.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
Schedule=
*/ ?>
```

## Notify

> When to send a notice by e-mail (\[StaticPublishSettings\] NotifyEmail):  
> failure, success.  

Default: a list of 1: `failure`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
# an empty "Notify[]" first replaces the shipped list; without it the items are added
Notify[]
Notify[]=failure
*/ ?>
```

## Webhook

> An https address that is sent a notice of every publish and rollback  
> (POST, JSON, schema exp-static-webhook/1).  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
Webhook=
*/ ?>
```

## License

> The license of the content for Extras\[\]=license: an SPDX id (CC-BY-4.0)  
> or the text itself.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[Target_example]
License=
*/ ?>
```

## Related

- [All blocks of staticcache.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/staticcache.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
