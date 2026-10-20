---
title: module.ini [ModuleSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ModuleSettings] in module.ini: ModuleRepositories, ExtensionRepositories, ModuleList"
keywords: Einstellung, module.ini, ModuleSettings, INI, setting, ModuleRepositories, ExtensionRepositories, ModuleList
type: generated
generated: exp:help:examples
---
# module.ini [ModuleSettings]

Der Block `[ModuleSettings]` von `settings/module.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/module.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/module.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/module.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/module.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ModuleRepositories

> An array of directories to search for modules  

Standard: eine Liste mit 2: `kernel`, `kernel/private/modules`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/module.ini.append.php
[ModuleSettings]
# ein leeres "ModuleRepositories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ModuleRepositories[]
ModuleRepositories[]=kernel
ModuleRepositories[]=kernel/private/modules
*/ ?>
```

## ExtensionRepositories

> A list of extensions which have modules  
> It's common to create a settings/module.ini.append file  
> in your extension and add the extension name to automatically  
> get modules from the extension when it's turned on.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/module.ini.append.php
[ModuleSettings]
# ein leeres "ExtensionRepositories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionRepositories[]
*/ ?>
```

## ModuleList

> An array of known modules in the system, extensions should  
> append to this array.  
> This is currently used to avoid URL aliases overriding  
> existing module names  

Standard: eine Liste mit 34: `apikey`, `audit`, `class`, `collaboration`, `contentcheck`, `csp`, `contentfinder`, `content`, `error`, `exphelp`, `ezinfo`, `infocollector` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/module.ini.append.php
[ModuleSettings]
# ein leeres "ModuleList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ModuleList[]
ModuleList[]=apikey
ModuleList[]=audit
ModuleList[]=class
ModuleList[]=collaboration
ModuleList[]=contentcheck
ModuleList[]=csp
ModuleList[]=contentfinder
ModuleList[]=content
ModuleList[]=error
ModuleList[]=exphelp
ModuleList[]=ezinfo
ModuleList[]=infocollector
ModuleList[]=layout
ModuleList[]=mailpreferences
ModuleList[]=notification
ModuleList[]=oauth
ModuleList[]=oauthadmin
ModuleList[]=package
ModuleList[]=pdf
ModuleList[]=role
# ... 14 weitere in der ausgelieferten Datei
*/ ?>
```

## Siehe auch

- [Alle Blöcke von module.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/module.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
