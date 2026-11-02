---
title: migration.ini [RemoteSettings]
audience: developer, admin
category: examples
summary: "The settings of [RemoteSettings] in migration.ini: RemoteList, Parallel, Timeout, Retries, StoreReports, StageOrder, Notify, NotifyEmail"
keywords: migration.ini, RemoteSettings, INI, setting, RemoteList, Parallel, Timeout, Retries, StoreReports, StageOrder, Notify, NotifyEmail
type: generated
generated: exp:help:examples
---
# migration.ini [RemoteSettings]

The block `[RemoteSettings]` of `settings/migration.ini`, with 8 setting(s).

Never edit `settings/migration.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/migration.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in an extension (read when the extension is active).

## RemoteList

> Remotes: other installations this one deploys its migrations to (./console exp:migration:deploy --remotes=...),  
> all at once and in sync. One \[Remote\_<id>\] section for each id of RemoteList\[\]; set them in settings/override.  
> A remote section never holds a secret: Credential names one of the secret store (Setup > Secrets) or an  
> environment variable (env:VARIABLE). Guide: doc/eng-us/guides/migrations-remote-deploy.md  
>
> \[Remote\_production1\]  
> Environment=production           alpha, dev, stage, beta, production, post, arch ... (the label reports show)  
> Transport=rsync                  ssh (scp), sftp, rsync (over ssh), ftp, ftps (files only), local (this host)  
> Host=node1.example.com  
> Port=22  
> User=deploy  
> Path=/var/www/site               the installation's root on the remote  
> Php=php                          the PHP CLI there  
> Credential=sshkey:deploy\_production1  
> Groups\[\]=production              --remotes=production selects every remote of the group  
> HostKeyFingerprint=SHA256:...    optional: the host key must have it (else it is learnt on first contact)  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
# an empty "RemoteList[]" first replaces the shipped list; without it the items are added
RemoteList[]
*/ ?>
```

## Parallel

> How many remotes are worked on at the same time. The switch (every remote applies) always runs on all at once.  

Default: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
Parallel=4
*/ ?>
```

## Timeout

> Seconds one remote may take for one phase (preflight, transfer, verify, switch, rollback).  

Default: `900`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
Timeout=900
*/ ?>
```

## Retries

> How often a connection failure (ssh exit 255, a timeout, a dropped FTP transfer) is tried again in the preflight,  
> the transfer and the verification. A run on a remote is never started twice.  

Default: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
Retries=2
*/ ?>
```

## StoreReports

> enabled: every deploy that sends or runs something keeps its report in var/<site>/migration/deploy/reports/.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
StoreReports=enabled
*/ ?>
```

## StageOrder

> A staged deploy (--staged, the administration's "Staged") puts the remotes in stages by their Environment, in  
> this order (an environment that is not listed comes last); each stage is switched all or nothing, and a gate  
> waits for someone to approve the next one (--continue=<deploy id>, or Approve on the deploy's page).  

Default: a list of 8: `alpha`, `dev`, `test`, `stage`, `beta`, `production`, `post`, `arch`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
# an empty "StageOrder[]" first replaces the shipped list; without it the items are added
StageOrder[]
StageOrder[]=alpha
StageOrder[]=dev
StageOrder[]=test
StageOrder[]=stage
StageOrder[]=beta
StageOrder[]=production
StageOrder[]=post
StageOrder[]=arch
*/ ?>
```

## Notify

> Notices by e-mail about a deploy: failure (refused, failed, taken back, out of sync), gate (a staged deploy waits  
> for approval), success (deployed or transferred). Plain text, mail category admin.  

Default: a list of 2: `failure`, `gate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
# an empty "Notify[]" first replaces the shipped list; without it the items are added
Notify[]
Notify[]=failure
Notify[]=gate
*/ ?>
```

## NotifyEmail

> Where the notices go; empty: site.ini \[MailSettings\] AdminEmail.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RemoteSettings]
NotifyEmail=
*/ ?>
```

## Related

- [All blocks of migration.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/migration.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
