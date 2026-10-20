---
title: site.ini [SettingsViewSettings]
audience: developer, admin
category: examples
summary: "The settings of [SettingsViewSettings] in site.ini: CommonINIFileList, MaskedNameList, UnmaskedNameList, MaskRevealLastCharacters, RestartSettingList"
keywords: site.ini, SettingsViewSettings, INI, setting, CommonINIFileList, MaskedNameList, UnmaskedNameList, MaskRevealLastCharacters, RestartSettingList
type: generated
generated: exp:help:examples
---
# site.ini [SettingsViewSettings]

The block `[SettingsViewSettings]` of `settings/site.ini`, with 5 setting(s).

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## CommonINIFileList

> The ini files offered at the top of the drop-down on setup > view settings,  
> above a separator, before the rest in alphabetical order. This installation  
> has just over a hundred ini files, and hunting down one of the dozen that are  
> actually read every day is the whole of the problem.  
>
> The order is by how much each file is really used here, counted three ways:  
> how many override files exist for it, how often the php reads it, and how  
> often the templates read it. Anything listed that this installation does not  
> have is passed over, so a shortened install shows a shorter list rather than  
> dead entries.  
>
> Add to it freely: this is a convenience, and nothing depends on it.  
> The master file: everything about the site and its siteaccesses.  
> Datatypes, versioning, the tree, view caching.  
> Designs, stylesheets, javascript and where they are looked for.  
> The template override rules - which template answers for which content.  
> The site menus.  
> The administration tree menu on the left.  
> Modules, their views and who may reach them.  
> The template engine: caching, compilation, debug.  
> Image aliases and the conversion rules behind them.  
> The tabs and controls of the administration interface.  
> The content browser: what each browse button starts on and may pick.  
> Workflows and the events they are built from.  
> What runs on which cron schedule.  
> File and cluster handling, where var files live.  
> Charsets and translations.  

Default: a list of 15: `site.ini`, `content.ini`, `design.ini`, `override.ini`, `menu.ini`, `contentstructuremenu.ini`, `module.ini`, `template.ini`, `image.ini`, `admininterface.ini`, `browse.ini`, `workflow.ini` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SettingsViewSettings]
# an empty "CommonINIFileList[]" first replaces the shipped list; without it the items are added
CommonINIFileList[]
CommonINIFileList[]=site.ini
CommonINIFileList[]=content.ini
CommonINIFileList[]=design.ini
CommonINIFileList[]=override.ini
CommonINIFileList[]=menu.ini
CommonINIFileList[]=contentstructuremenu.ini
CommonINIFileList[]=module.ini
CommonINIFileList[]=template.ini
CommonINIFileList[]=image.ini
CommonINIFileList[]=admininterface.ini
CommonINIFileList[]=browse.ini
CommonINIFileList[]=workflow.ini
CommonINIFileList[]=cronjob.ini
CommonINIFileList[]=file.ini
CommonINIFileList[]=i18n.ini
*/ ?>
```

## MaskedNameList

> Settings whose values the settings pages (settings/view, settings/edit) never  
> show: only whether they are set or empty. A setting is a secret when its name  
> matches an entry of MaskedNameList\[\] and none of UnmaskedNameList\[\], or when  
> it is one exp:ini and the audit log mask (Password, Secret, Token, Salt,  
> ...Key, Credential: these cannot be switched off here). An entry without '\*'  
> is found anywhere in the name, ignoring case ("password" matches  
> TransportPassword); a capitalised single word ("Key", "DSN") must be the  
> whole name; an entry with '\*' is a pattern for the whole name and keeps case  
> ("\*Key" matches LicenseKey, not Monkey). A password inside any value  
> (scheme://user:password@host, password=... in a connection string) is masked  
> as well, and a secret's value is never searched.  

Default: a list of 17: `password`, `passwd`, `passphrase`, `secret`, `token`, `salt`, `credential`, `privatekey`, `apikey`, `Key`, `*Key`, `*_key` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SettingsViewSettings]
# an empty "MaskedNameList[]" first replaces the shipped list; without it the items are added
MaskedNameList[]
MaskedNameList[]=password
MaskedNameList[]=passwd
MaskedNameList[]=passphrase
MaskedNameList[]=secret
MaskedNameList[]=token
MaskedNameList[]=salt
MaskedNameList[]=credential
MaskedNameList[]=privatekey
MaskedNameList[]=apikey
MaskedNameList[]=Key
MaskedNameList[]=*Key
MaskedNameList[]=*_key
MaskedNameList[]=*_KEY
MaskedNameList[]=*-key
MaskedNameList[]=DSN
MaskedNameList[]=*Dsn
MaskedNameList[]=*DSN
*/ ?>
```

## UnmaskedNameList

> Names the entries above would match that are not secrets (whole names).  

Default: a list of 11: `SortKey`, `CacheKey`, `PrimaryKey`, `ForeignKey`, `IndexKey`, `ShortcutKey`, `HotKey`, `GroupKey`, `SectionKey`, `LanguageKey`, `IdKey`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SettingsViewSettings]
# an empty "UnmaskedNameList[]" first replaces the shipped list; without it the items are added
UnmaskedNameList[]
UnmaskedNameList[]=SortKey
UnmaskedNameList[]=CacheKey
UnmaskedNameList[]=PrimaryKey
UnmaskedNameList[]=ForeignKey
UnmaskedNameList[]=IndexKey
UnmaskedNameList[]=ShortcutKey
UnmaskedNameList[]=HotKey
UnmaskedNameList[]=GroupKey
UnmaskedNameList[]=SectionKey
UnmaskedNameList[]=LanguageKey
UnmaskedNameList[]=IdKey
*/ ?>
```

## MaskRevealLastCharacters

> Show the last characters of a secret (at most 4) so two values can be told  
> apart, and only of a value at least four times that long. 0 shows none.  

Default: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SettingsViewSettings]
MaskRevealLastCharacters=0
*/ ?>
```

## RestartSettingList

> Settings a running Velocity server reads only when it starts: after a change  
> the settings page says to restart it (exp:velocity restart). A file, a  
> file/block or a file/block/setting. Every other setting reaches PHP-FPM and  
> Velocity on their next request, because a write clears the INI cache.  

Default: a list of 5: `velocity.ini`, `site.ini/ExtensionSettings`, `site.ini/DatabaseSettings`, `site.ini/FileSettings/VarDir`, `file.ini/ClusteringSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SettingsViewSettings]
# an empty "RestartSettingList[]" first replaces the shipped list; without it the items are added
RestartSettingList[]
RestartSettingList[]=velocity.ini
RestartSettingList[]=site.ini/ExtensionSettings
RestartSettingList[]=site.ini/DatabaseSettings
RestartSettingList[]=site.ini/FileSettings/VarDir
RestartSettingList[]=file.ini/ClusteringSettings
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
