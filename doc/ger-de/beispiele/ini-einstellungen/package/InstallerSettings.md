---
title: package.ini [InstallerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [InstallerSettings] in package.ini: HandlerList, HandlerAlias, InstallBatchMaxItems, InstallBatchTimeBudgetSeconds"
keywords: Einstellung, package.ini, InstallerSettings, INI, setting, HandlerList, HandlerAlias, InstallBatchMaxItems, InstallBatchTimeBudgetSeconds
type: generated
generated: exp:help:examples
---
# package.ini [InstallerSettings]

Der Block `[InstallerSettings]` von `settings/package.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## HandlerList

> A list of extensions which have package installation handlers support  
> The extension must place package creation handlers in the  
> subdirectory packageinstallers.  

Standard: eine Liste mit 2: `ezcontentobject`, `ezinstallscript`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[InstallerSettings]
# ein leeres "HandlerList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HandlerList[]
HandlerList[]=ezcontentobject
HandlerList[]=ezinstallscript
*/ ?>
```

## HandlerAlias

Standard: ein Hash mit 2: `ezcontentobject=eZContentObjectPackageInstaller`, `ezinstallscript=eZInstallScriptPackageInstaller`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[InstallerSettings]
# ein leeres "HandlerAlias[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HandlerAlias[]
HandlerAlias[ezcontentobject]=eZContentObjectPackageInstaller
HandlerAlias[ezinstallscript]=eZInstallScriptPackageInstaller
*/ ?>
```

## InstallBatchMaxItems

> Maximum number of non-interactive install items processed per request.  

Standard: `12`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[InstallerSettings]
InstallBatchMaxItems=12
*/ ?>
```

## InstallBatchTimeBudgetSeconds

> Maximum wall-clock seconds spent processing install items per request.  

Standard: `6`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[InstallerSettings]
InstallBatchTimeBudgetSeconds=6
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
