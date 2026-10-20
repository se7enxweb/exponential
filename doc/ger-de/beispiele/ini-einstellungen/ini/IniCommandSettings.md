---
title: ini.ini [IniCommandSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [IniCommandSettings] in ini.ini: Actions, ActionAliases, ScopeProviders"
keywords: Einstellung, ini.ini, IniCommandSettings, INI, setting, Actions, ActionAliases, ScopeProviders
type: generated
generated: exp:help:examples
---
# ini.ini [IniCommandSettings]

Der Block `[IniCommandSettings]` von `settings/ini.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/ini.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ini.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ini.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ini.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Actions

> The actions of the command: Actions\[<name>\]=<class>. The class implements  
> expIniAction (kernel/classes/ini/actions/expiniaction.php): name(),  
> description(), usage() and run( expIniCommandContext $c ), which returns the  
> exit code. "exp:ini <name> ..." makes the class and runs it.  
>
> An extension adds an action by an INI append of its own, e.g.  
> extension/myext/settings/ini.ini.append.php:  
>
>   \[IniCommandSettings\]  
>   Actions\[dump\]=myExtIniActionDump  
>
> then regenerates the extension autoloads (php bin/php/ezpgenerateautoloads.php -e)  
> so the class is found. The same name as a built-in replaces the built-in;  
> an empty value (Actions\[copy\]=) switches an action off.  

Standard: ein Hash mit 13: `get=expIniActionGet`, `set=expIniActionSet`, `add=expIniActionAdd`, `rem=expIniActionRem`, `clear=expIniActionClear`, `toggle=expIniActionToggle`, `copy=expIniActionCopy`, `move=expIniActionMove`, `move-all=expIniActionMoveAll`, `where=expIniActionWhere`, `list=expIniActionList`, `scopes=expIniActionScopes` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ini.ini.append.php
[IniCommandSettings]
# ein leeres "Actions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Actions[]
Actions[get]=expIniActionGet
Actions[set]=expIniActionSet
Actions[add]=expIniActionAdd
Actions[rem]=expIniActionRem
Actions[clear]=expIniActionClear
Actions[toggle]=expIniActionToggle
Actions[copy]=expIniActionCopy
Actions[move]=expIniActionMove
Actions[move-all]=expIniActionMoveAll
Actions[where]=expIniActionWhere
Actions[list]=expIniActionList
Actions[scopes]=expIniActionScopes
Actions[actions]=expIniActionActions
*/ ?>
```

## ActionAliases

> Other names of actions: ActionAliases\[<alias>\]=<action name>.  

Standard: ein Hash mit 1: `remove=rem`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ini.ini.append.php
[IniCommandSettings]
# ein leeres "ActionAliases[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ActionAliases[]
ActionAliases[remove]=rem
*/ ?>
```

## ScopeProviders

> The scope providers: classes implementing expIniScopeProvider  
> (kernel/classes/ini/expiniscopeprovider.php), whose scopes( $root ) returns  
> expIniScope objects. The two built-ins give global, default, every siteaccess,  
> every extension and every extension siteaccess directory. An extension adds a  
> scope (a cluster-wide directory, one per environment, ...) by an INI append:  
>
>   \[IniCommandSettings\]  
>   ScopeProviders\[\]=myExtIniScopeProvider  
>
> The first scope of a name wins, so a provider cannot take over a built-in one.  

Standard: eine Liste mit 2: `expIniCoreScopeProvider`, `expIniExtensionScopeProvider`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ini.ini.append.php
[IniCommandSettings]
# ein leeres "ScopeProviders[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ScopeProviders[]
ScopeProviders[]=expIniCoreScopeProvider
ScopeProviders[]=expIniExtensionScopeProvider
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ini.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/ini.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
