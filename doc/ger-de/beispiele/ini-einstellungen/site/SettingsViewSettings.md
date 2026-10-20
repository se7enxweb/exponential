---
title: site.ini [SettingsViewSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SettingsViewSettings] in site.ini: CommonINIFileList, MaskedNameList, UnmaskedNameList, MaskRevealLastCharacters, RestartSettingList"
keywords: Einstellung, site.ini, SettingsViewSettings, INI, setting, CommonINIFileList, MaskedNameList, UnmaskedNameList, MaskRevealLastCharacters, RestartSettingList
type: generated
generated: exp:help:examples
---
# site.ini [SettingsViewSettings]

Der Block `[SettingsViewSettings]` von `settings/site.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

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

Standard: eine Liste mit 15: `site.ini`, `content.ini`, `design.ini`, `override.ini`, `menu.ini`, `contentstructuremenu.ini`, `module.ini`, `template.ini`, `image.ini`, `admininterface.ini`, `browse.ini`, `workflow.ini` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SettingsViewSettings]
# ein leeres "CommonINIFileList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: eine Liste mit 17: `password`, `passwd`, `passphrase`, `secret`, `token`, `salt`, `credential`, `privatekey`, `apikey`, `Key`, `*Key`, `*_key` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SettingsViewSettings]
# ein leeres "MaskedNameList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: eine Liste mit 11: `SortKey`, `CacheKey`, `PrimaryKey`, `ForeignKey`, `IndexKey`, `ShortcutKey`, `HotKey`, `GroupKey`, `SectionKey`, `LanguageKey`, `IdKey`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SettingsViewSettings]
# ein leeres "UnmaskedNameList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: `0`.

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

Standard: eine Liste mit 5: `velocity.ini`, `site.ini/ExtensionSettings`, `site.ini/DatabaseSettings`, `site.ini/FileSettings/VarDir`, `file.ini/ClusteringSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SettingsViewSettings]
# ein leeres "RestartSettingList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RestartSettingList[]
RestartSettingList[]=velocity.ini
RestartSettingList[]=site.ini/ExtensionSettings
RestartSettingList[]=site.ini/DatabaseSettings
RestartSettingList[]=site.ini/FileSettings/VarDir
RestartSettingList[]=file.ini/ClusteringSettings
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
