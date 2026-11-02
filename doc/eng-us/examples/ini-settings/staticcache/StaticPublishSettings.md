---
title: staticcache.ini [StaticPublishSettings]
audience: developer, admin
category: examples
summary: "The settings of [StaticPublishSettings] in staticcache.ini: Enabled, TargetList, WorkDir, MaxSiteBytes, MaxFileBytes, Push, CommitName, CommitEmail ..."
keywords: staticcache.ini, StaticPublishSettings, INI, setting, Enabled, TargetList, WorkDir, MaxSiteBytes, MaxFileBytes, Push, CommitName, CommitEmail, JobHistory, GitHubAPI, GitHubAPIURL, GitHubAppId, GitHubAppKey, GitHubOAuthClientId, FormEndpoint, FormTokenDays, FormRateLimit, FormRateWindow, FormFallbackText, FormFallbackLink, SearchMaxBytes, PublishDebounce, NotifyEmail, WebhookSecret
type: generated
generated: exp:help:examples
---
# staticcache.ini [StaticPublishSettings]

The block `[StaticPublishSettings]` of `settings/staticcache.ini`, with 24 setting(s).

Never edit `settings/staticcache.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/staticcache.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/staticcache.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/staticcache.ini.append.php` in an extension (read when the extension is active).

## Enabled

> Static publishing: a front siteaccess built as a folder of static files  
> that any static host serves (GitHub Pages, any web server), with its links  
> rewritten and its files copied. Each target is a \[Target\_<id>\] block below;  
> ./console exp:static:targets lists them and exp:static:build builds one.  
>
> Static publishing as a whole: enabled or disabled. Nothing is built while  
> it is disabled.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
Enabled=disabled
*/ ?>
```

## TargetList

> The targets, by id: the ids of the \[Target\_<id>\] blocks that are built.  
> An id is 1 to 50 lower case letters, digits, \_ and -.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
# an empty "TargetList[]" first replaces the shipped list; without it the items are added
TargetList[]
*/ ?>
```

## WorkDir

> Where the builds are kept, relative to the var directory of the site  
> (var/<site>/static-publish) unless absolute. Each target has its own  
> folder below it, never inside the site's own repository.  

Default: `static-publish`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
WorkDir=static-publish
*/ ?>
```

## MaxSiteBytes

> The most bytes a build may have before the build warns: the size the host  
> allows a site (GitHub Pages: 1 GB). The build warns above 90 %.  

Default: `1000000000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
MaxSiteBytes=1000000000
*/ ?>
```

## MaxFileBytes

> The most bytes one file may have before the build warns: the size the host  
> allows a file (GitHub: 100 MB).  

Default: `100000000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
MaxFileBytes=100000000
*/ ?>
```

## Push

> Whether exp:static:publish and exp:static:rollback push to the target's  
> Remote: enabled, or disabled to commit and tag in the target's folder only  
> (exp:static:push pushes later). A push is never forced.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
Push=enabled
*/ ?>
```

## CommitName

> The committer of a publish when the target's repository names none.  

Default: `Exponential static publishing`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
CommitName=Exponential static publishing
*/ ?>
```

## CommitEmail

> Its e-mail address; empty: static-publish@<host name>.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
CommitEmail=
*/ ?>
```

## JobHistory

> How many jobs (builds, publishes, rollbacks started in the administration) keep their files  
> in <WorkDir>/.jobs; older ones are removed. A job that is running is always kept.  

Default: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
JobHistory=50
*/ ?>
```

## GitHubAPI

> The GitHub integration: GitHub's API for targets with Kind=github-pages  
> (create the repository, switch GitHub Pages on from the branch or from  
> GitHub Actions, the custom domain and HTTPS, the Pages build status). It  
> signs in with the target's Credential (token: or app:). enabled or disabled;  
> publishing to the branch works without it.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
GitHubAPI=enabled
*/ ?>
```

## GitHubAPIURL

> The address of GitHub's REST API (GitHub Enterprise Server:  
> https://<host>/api/v3).  

Default: `https://api.github.com`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
GitHubAPIURL=https://api.github.com
*/ ?>
```

## GitHubAppId

> A GitHub App for Credential=app:<installation id>: the App's id, and the  
> name of the secret (Setup > Secrets) that holds the private key (PEM) GitHub  
> made for it. The App needs the repository permissions Contents, Pages and  
> Administration (to create repositories), read and write.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
GitHubAppId=
*/ ?>
```

## GitHubAppKey

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
GitHubAppKey=
*/ ?>
```

## GitHubOAuthClientId

> The client id of an OAuth App with the device flow switched on, for  
> ./console exp:static:github signin: a user signs in on github.com and the  
> token is kept in the secret store for Credential=token:<secret>.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
GitHubOAuthClientId=
*/ ?>
```

## FormEndpoint

> The form endpoint (staticpublish/form): the information collectors of the  
> targets with Forms=endpoint post to this installation, which hands them to  
> the normal information collection. Only posts from a target's PublicURL or  
> CustomDomain with a token signed by a build are taken. enabled or disabled  
> (the copies show the fallback text instead).  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
FormEndpoint=enabled
*/ ?>
```

## FormTokenDays

> How long the token a build writes into a form stays valid, in days. A new  
> publish writes new tokens.  

Default: `400`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
FormTokenDays=400
*/ ?>
```

## FormRateLimit

> How many posts one address may send to one target in FormRateWindow  
> seconds before it is asked to wait (429).  

Default: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
FormRateLimit=5
*/ ?>
```

## FormRateWindow

Default: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
FormRateWindow=600
*/ ?>
```

## FormFallbackText

> The text that replaces a form a static copy cannot send (comments, the  
> basket, a collector with Forms=hide), and the text of its link to the same  
> page on the live site.  

Default: `This form works on the live site only.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
FormFallbackText=This form works on the live site only.
*/ ?>
```

## FormFallbackLink

Default: `Open this page there`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
FormFallbackLink=Open this page there
*/ ?>
```

## SearchMaxBytes

> The most bytes of one language's search index (Search=enabled): over it,  
> the index keeps fewer words per page, then no teasers, then fewer pages.  

Default: `2000000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
SearchMaxBytes=2000000
*/ ?>
```

## PublishDebounce

> Publishing on content publish (Triggers\[\]=publish): the cronjob part  
> staticpublish builds the changed pages of a target this many seconds after  
> the last change, so a series of edits makes one publish.  

Default: `120`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
PublishDebounce=120
*/ ?>
```

## NotifyEmail

> Where the notices of the targets' Notify\[\] go; empty: site.ini  
> \[MailSettings\] AdminEmail.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
NotifyEmail=
*/ ?>
```

## WebhookSecret

> The name of a secret (Setup > Secrets) whose value signs every webhook  
> (header X-Exp-Static-Signature: sha256=<HMAC of the body>). Empty: not  
> signed. Only the name is here; never the value.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[StaticPublishSettings]
WebhookSecret=
*/ ?>
```

## Related

- [All blocks of staticcache.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/staticcache.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
