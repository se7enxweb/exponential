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

> Die Aktionen des Befehls: Actions\[<name>\]=<class>. Die Klasse implementiert  
> expIniAction (kernel/classes/ini/actions/expiniaction.php): name(),  
> description(), usage() und run( expIniCommandContext $c ), das den  
> Exit-Code zurückgibt. "exp:ini <name> ..." erzeugt die Klasse und führt sie aus.  
>
> Eine Erweiterung fügt eine Aktion mit einem eigenen INI-Append hinzu, z. B.  
> extension/myext/settings/ini.ini.append.php:  
>
>   \[IniCommandSettings\]  
>   Actions\[dump\]=myExtIniActionDump  
>
> und erzeugt dann die Autoloads der Erweiterungen neu (php bin/php/ezpgenerateautoloads.php -e),  
> damit die Klasse gefunden wird. Derselbe Name wie eine eingebaute ersetzt die eingebaute;  
> ein leerer Wert (Actions\[copy\]=) schaltet eine Aktion ab.  

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

> Andere Namen von Aktionen: ActionAliases\[<alias>\]=<action name>.  

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

> Die Bereichs-Provider: Klassen, die expIniScopeProvider implementieren  
> (kernel/classes/ini/expiniscopeprovider.php), deren scopes( $root ) Objekte  
> expIniScope zurückgibt. Die beiden eingebauten liefern global, default, jeden Siteaccess,  
> jede Erweiterung und jedes Siteaccess-Verzeichnis jeder Erweiterung. Eine Erweiterung fügt einen  
> Bereich (ein Verzeichnis für den ganzen Cluster, eines pro Umgebung, ...) mit einem INI-Append hinzu:  
>
>   \[IniCommandSettings\]  
>   ScopeProviders\[\]=myExtIniScopeProvider  
>
> Der erste Bereich eines Namens gewinnt, ein Provider kann also keinen eingebauten übernehmen.  

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
