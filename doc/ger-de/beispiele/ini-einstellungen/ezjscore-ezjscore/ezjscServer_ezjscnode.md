---
title: ezjscore.ini [ezjscServer_ezjscnode]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ezjscServer_ezjscnode] in ezjscore.ini (ezjscore): Class, Functions, RequirePolicy, HardLimit"
keywords: Einstellung, ezjscore.ini, ezjscServer_ezjscnode, INI, setting, Class, Functions, RequirePolicy, HardLimit
type: generated
generated: exp:help:examples
---
# ezjscore.ini [ezjscServer_ezjscnode]

Der Block `[ezjscServer_ezjscnode]` von `extension/ezjscore/settings/ezjscore.ini` (Erweiterung ezjscore), mit 4 Einstellung(en).

> File=extension/ezjscore/classes/ezjscserverfunctionsjs.php  

Bearbeiten Sie `extension/ezjscore/settings/ezjscore.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezjscore.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezjscore.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezjscore.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> Url to test this server function(return node list):  
> <root>/ezjscore/call/ezjscnode::subtree::2  

Standard: `ezjscServerFunctionsNode`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjscnode]
Class=ezjscServerFunctionsNode
*/ ?>
```

## Functions

> File=extension/ezjscore/classes/ezjscserverfunctionsnode.php  
> The node data (name, ids, remote id, owner, section, dates, class, translations, paths) is  
> readable by every user who may read the node, anonymous visitors included, unless  
> RequirePolicy=enabled: then a user also needs ezjscore/call with the function list ezjscnode  
> (FunctionList limitation). New installations write RequirePolicy=enabled in  
> settings/override/ezjscore.ini.append.php and grant the Editor role that policy; an upgraded  
> site keeps disabled until it switches. Guide: doc/eng-us/bc/6.0/backend\_ezjscore\_services.md  

Standard: eine Liste mit 1: `ezjscnode`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjscnode]
# ein leeres "Functions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Functions[]
Functions[]=ezjscnode
*/ ?>
```

## RequirePolicy

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjscnode]
RequirePolicy=disabled
*/ ?>
```

## HardLimit

> Allows setting a Hard Limit to the subtree method to prevent the load of very large subtrees  
>  reducing server load and risk of DOS attacks.  
>  false disables the feature, any integer will set the hard limit.  
>  for example: a hard limit of 100 will allow limiting to 30 or 40, but will replace a  
>               limit of 150.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjscnode]
HardLimit=false
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezjscore.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/settings/ezjscore.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
