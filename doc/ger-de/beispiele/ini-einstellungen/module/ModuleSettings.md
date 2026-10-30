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

> Ein Array von Verzeichnissen, in denen nach Modulen gesucht wird  

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

> Eine Liste der Erweiterungen, die Module haben  
> Üblicherweise legt man in der Erweiterung eine Datei settings/module.ini.append  
> an und trägt den Namen der Erweiterung ein, um automatisch  
> die Module der Erweiterung zu erhalten, wenn sie eingeschaltet ist.  

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

> Ein Array der im System bekannten Module, Erweiterungen sollten an dieses Array  
> anhängen.  
> Das wird derzeit verwendet, damit URL-Aliase keine  
> bestehenden Modulnamen überschreiben  
> Die Datentabellen und die benannten Datenbankverbindungen (doc/eng-us/features/6.0/data-tables.md)  
> Der Secret-Store des Kernels (doc/eng-us/features/6.0/secret-store.md)  

Standard: eine Liste mit 36: `apikey`, `audit`, `class`, `collaboration`, `contentcheck`, `csp`, `contentfinder`, `content`, `datatable`, `error`, `exphelp`, `ezinfo` ....

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
ModuleList[]=datatable
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
# ... 16 weitere in der ausgelieferten Datei
*/ ?>
```

## Siehe auch

- [Alle Blöcke von module.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/module.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
