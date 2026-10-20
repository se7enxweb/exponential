---
title: site.ini [ExtensionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ExtensionSettings] in site.ini: ExtensionDirectory, AdditionalExtensionDirectories, ExtensionOrdering, ActiveExtensions, ActiveAccessExtensions"
keywords: Einstellung, site.ini, ExtensionSettings, INI, setting, ExtensionDirectory, AdditionalExtensionDirectories, ExtensionOrdering, ActiveExtensions, ActiveAccessExtensions
type: generated
generated: exp:help:examples
---
# site.ini [ExtensionSettings]

Der Block `[ExtensionSettings]` von `settings/site.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ExtensionDirectory

Standard: `extension`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
ExtensionDirectory=extension
*/ ?>
```

## AdditionalExtensionDirectories

> Further places extensions may live, searched after ExtensionDirectory above.  
> A second root separates what this project wrote from what it took from  
> elsewhere, so who owns a package is readable off the directory layout rather  
> than off a list somebody has to maintain.  
>
> An extension of the same name in a later root shadows the one before it. That  
> is how a package is forked without being edited - copy it across and change  
> it there - and also how one gets shadowed by accident.  
>
> The directory has to exist, and the autoload generator scans every configured  
> root, so after adding one:  
>
>     php bin/php/ezpgenerateautoloads.php  
>
> Nothing else: moving a package between roots is a copy, with no manifest to  
> edit and no composer dump-autoload to run.  
>
> Code should never build a path out of "extension/" and a name. Use  
> eZExtension::extensionPath() and eZExtension::expandedPathList(), which  
> resolve across every root. eZExtension::filterExtensionRootDirectories() is  
> the last word, for a root that has to be worked out at runtime.  
>
> See doc/eng-us/bc/6.0/AdditionalExtensionDirectories.md  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
# ein leeres "AdditionalExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdditionalExtensionDirectories[]
*/ ?>
```

## ExtensionOrdering

> AdditionalExtensionDirectories\[\]=extension\_src  
> Use extension reordering to avoid issues because of ActiveExtensions and  
> ActiveAccessExtensions not being correctly defined  
> Known issues:  
>   - Breaks clustering settings in siteaccess and extensions support  
>     Only known workaround is to disable this setting and order manually  
>   - Still loads an extension even if dependant extension is not loaded  
>   - Does not fix order issues across the two extensions settings  
>     ( ActiveExtensions and ActiveAccessExtensions )  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
ExtensionOrdering=enabled
*/ ?>
```

## ActiveExtensions

> A list of active extensions, add new ones to activate them  
> The extension itself will then have it's settings directory read (if any)  
> and any extra configurability is done automatically.  

Standard: eine Liste mit 1: `xrowmetadata`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
# ein leeres "ActiveExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ActiveExtensions[]
ActiveExtensions[]=xrowmetadata
*/ ?>
```

## ActiveAccessExtensions

> Same as ActiveExtensions but will be loaded after the siteaccess is chosen.  
> This allows for activating different extensions on different siteaccesses  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
# ein leeres "ActiveAccessExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ActiveAccessExtensions[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
