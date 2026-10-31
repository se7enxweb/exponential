---
title: upgrade.ini [UpgradeSettings]
audience: developer, admin
category: examples
summary: "The settings of [UpgradeSettings] in upgrade.ini: StepList, CaptionKeep, CaptionKeepDays, CaptionKeepUpgrades, SmokeURL, ExtensionStepList"
keywords: upgrade.ini, UpgradeSettings, INI, setting, StepList, CaptionKeep, CaptionKeepDays, CaptionKeepUpgrades, SmokeURL, ExtensionStepList
type: generated
generated: exp:help:examples
---
# upgrade.ini [UpgradeSettings]

The block `[UpgradeSettings]` of `settings/upgrade.ini`, with 6 setting(s).

Never edit `settings/upgrade.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/upgrade.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/upgrade.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/upgrade.ini.append.php` in an extension (read when the extension is active).

## StepList

> The kernel's upgrade steps, class names (expUpgradeStep). Each step belongs to a stage  
> (code, libraries, database, scripts, finish); they run by stage, then by version, then in  
> the order listed here, after the steps they name in after().  
> stage code: the git checkouts, fast-forward only, through git\_manager  
> stage libraries: Composer (an update only when asked with --update, then install from composer.lock)  
> stage database: schema and data, every engine (step 0 first: the record table)  
> stage scripts: the PHP upgrade steps, through the classes of the scripts and setup commands  
> stage finish: make the changes live and check the site (only when the run changed something)  

Default: a list of 37: `expUpgradeStepCodeUpdate`, `expUpgradeStepLibrariesUpdate`, `expUpgradeStepLibrariesInstall`, `expUpgradeStepRecordTable`, `expUpgradeStepPasswordHashWidth`, `expUpgradeStepTrashColumns`, `expUpgradeStepPdfExportFooter`, `expUpgradeStepRssOpmlPodcast`, `expUpgradeStepOrderStatuses`, `expUpgradeStepAuditIndexTables`, `expUpgradeStepBookmarkFolders`, `expUpgradeStepBookmarkViewTable` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
# an empty "StepList[]" first replaces the shipped list; without it the items are added
StepList[]
StepList[]=expUpgradeStepCodeUpdate
StepList[]=expUpgradeStepLibrariesUpdate
StepList[]=expUpgradeStepLibrariesInstall
StepList[]=expUpgradeStepRecordTable
StepList[]=expUpgradeStepPasswordHashWidth
StepList[]=expUpgradeStepTrashColumns
StepList[]=expUpgradeStepPdfExportFooter
StepList[]=expUpgradeStepRssOpmlPodcast
StepList[]=expUpgradeStepOrderStatuses
StepList[]=expUpgradeStepAuditIndexTables
StepList[]=expUpgradeStepBookmarkFolders
StepList[]=expUpgradeStepBookmarkViewTable
StepList[]=expUpgradeStepBookmarkViewColumns
StepList[]=expUpgradeStepMailPreferenceTables
StepList[]=expUpgradeStepApiKeyTable
StepList[]=expUpgradeStepTransferRunTable
StepList[]=expUpgradeStepDataTableTables
StepList[]=expUpgradeStepUserSessionTable
StepList[]=expUpgradeStepOAuthPkceColumns
StepList[]=expUpgradeStepPasskeyTable
# ... 17 more in the shipped file
*/ ?>
```

## CaptionKeep

> Captions (exp:upgrade:captions --prune): keep the newest CaptionKeep captions, those younger than  
> CaptionKeepDays, and the pre and post captions of the last CaptionKeepUpgrades upgrades. Only the  
> captions of exp:upgrade are pruned; git\_manager's own keep to its MaxBackups.  

Default: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
CaptionKeep=10
*/ ?>
```

## CaptionKeepDays

Default: `90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
CaptionKeepDays=90
*/ ?>
```

## CaptionKeepUpgrades

Default: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
CaptionKeepUpgrades=3
*/ ?>
```

## SmokeURL

> The addresses exp:upgrade:verify --after requests (200 to 399 passes). Empty: velocity.ini  
> \[DeploySettings\] WarmUpURL\[\] (the addresses of Apache and of Velocity), else the front page  
> and the sign-in page of \[SiteSettings\] SiteURL.  
> SmokeURL\[\]=https://www.example.com/  
> SmokeURL\[\]=https://www.example.com/admin/user/login  
> SmokeURL\[\]=https://www.example.com:8080/  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
# an empty "SmokeURL[]" first replaces the shipped list; without it the items are added
SmokeURL[]
*/ ?>
```

## ExtensionStepList

> The steps of extensions: each extension appends its classes in its own  
> settings/upgrade.ini.append.php, e.g. ExtensionStepList\[\]=myextUpgradeStepWidgetTable  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
# an empty "ExtensionStepList[]" first replaces the shipped list; without it the items are added
ExtensionStepList[]
*/ ?>
```

## Related

- [All blocks of upgrade.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/upgrade.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
