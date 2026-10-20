---
title: package.ini [PackageSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PackageSettings] in package.ini: RepositoryDirectories, ExtensionDirectories, HandlerAlias, TypeList, StateList"
keywords: Einstellung, package.ini, PackageSettings, INI, setting, RepositoryDirectories, ExtensionDirectories, HandlerAlias, TypeList, StateList
type: generated
generated: exp:help:examples
---
# package.ini [PackageSettings]

Der Block `[PackageSettings]` von `settings/package.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RepositoryDirectories

Standard: eine Liste mit 1: `kernel/classes`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[PackageSettings]
# ein leeres "RepositoryDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RepositoryDirectories[]
RepositoryDirectories[]=kernel/classes
*/ ?>
```

## ExtensionDirectories

> Eine Liste der Erweiterungen, die Paket-Handler unterstützen  
> Die Erweiterung muss die Paket-Handler im  
> Unterverzeichnis packagehandlers ablegen.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[PackageSettings]
# ein leeres "ExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDirectories[]
*/ ?>
```

## HandlerAlias

> Aliase für Handler; damit können Handler  
> schönere Namen und auch Kürzel für bestimmte Einträge haben.  

Standard: ein Hash mit 11: `ezcontentclass=eZContentClassPackageHandler`, `ezcontentobject=eZContentObjectPackageHandler`, `eziniaddon=eZINIAddonPackageHandler`, `ezfile=eZFilePackageHandler`, `ezini=eZFilePackageHandler`, `ezdesign=eZFilePackageHandler`, `eztemplate=eZFilePackageHandler`, `ezthumbnail=eZFilePackageHandler`, `ezsql=eZDBPackageHandler`, `ezextension=eZExtensionPackageHandler`, `ezinstallscript=eZInstallScriptPackageHandler`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[PackageSettings]
# ein leeres "HandlerAlias[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HandlerAlias[]
HandlerAlias[ezcontentclass]=eZContentClassPackageHandler
HandlerAlias[ezcontentobject]=eZContentObjectPackageHandler
HandlerAlias[eziniaddon]=eZINIAddonPackageHandler
HandlerAlias[ezfile]=eZFilePackageHandler
HandlerAlias[ezini]=eZFilePackageHandler
HandlerAlias[ezdesign]=eZFilePackageHandler
HandlerAlias[eztemplate]=eZFilePackageHandler
HandlerAlias[ezthumbnail]=eZFilePackageHandler
HandlerAlias[ezsql]=eZDBPackageHandler
HandlerAlias[ezextension]=eZExtensionPackageHandler
HandlerAlias[ezinstallscript]=eZInstallScriptPackageHandler
*/ ?>
```

## TypeList

> Pakete von Erweiterungen: Der Assistent zum Exportieren von Erweiterungen erstellt sie, und der Paketserver hat  
> mehrere (ezwebin\_extension ...). Seit 6.0.15 aufgeführt, damit die Einschränkung Type der Paket-  
> richtlinien sie nennen kann.  

Standard: ein Hash mit 5: `contentobject=Content object`, `contentclass=Content class`, `sitestyle=Site style`, `site=Site`, `extension=Extension`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[PackageSettings]
# ein leeres "TypeList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TypeList[]
TypeList[contentobject]=Content object
TypeList[contentclass]=Content class
TypeList[sitestyle]=Site style
TypeList[site]=Site
TypeList[extension]=Extension
*/ ?>
```

## StateList

Standard: ein Hash mit 5: `mature=Mature`, `stable=Stable`, `beta=Beta`, `alpha=Alpha`, `prealpha=Pre-Alpha`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[PackageSettings]
# ein leeres "StateList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
StateList[]
StateList[mature]=Mature
StateList[stable]=Stable
StateList[beta]=Beta
StateList[alpha]=Alpha
StateList[prealpha]=Pre-Alpha
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
