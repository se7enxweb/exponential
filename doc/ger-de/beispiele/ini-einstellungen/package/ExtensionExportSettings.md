---
title: package.ini [ExtensionExportSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ExtensionExportSettings] in package.ini: AllowedExtensions, RequirePolicy"
keywords: Einstellung, package.ini, ExtensionExportSettings, INI, setting, AllowedExtensions, RequirePolicy
type: generated
generated: exp:help:examples
---
# package.ini [ExtensionExportSettings]

Der Block `[ExtensionExportSettings]` von `settings/package.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AllowedExtensions

> Exporting an extension as a package (Setup > Extensions, the package wizard, ezpm.php add) is switched off by  
> default. Every way also refuses an extension that holds secret values or key material  
> (eZExtensionPackageHandler::packRefusal()).  
>
> An extension's maintainer can name findings of the check that are known not to be secrets (a documentation  
> example the check cannot tell from a real value) in a file package\_check.ini at the root of the extension, one  
> per line: AllowedFindings\[\]=<path below the extension>:<setting name or (kind)>, for example  
> AllowedFindings\[\]=settings/recaptcha.ini.append.php:PrivateKey. It can only leave out what it names: key files, a  
> private key block, a copy of a settings file and a link are always refused. Guide:  
> doc/eng-us/bc/6.0/extension-package-secret-check.md  
>
> ACTION REQUIRED to export: list the extension here AND grant the policy package/export\_extension to the role.  
>
> The extensions that may be exported. Empty (the default): none. "\*": every extension the check lets through.  
> Applies to every way, the command line included.  
> Example:  
> AllowedExtensions\[\]=myextension  
> AllowedExtensions\[\]=\*  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[ExtensionExportSettings]
# ein leeres "AllowedExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedExtensions[]
*/ ?>
```

## RequirePolicy

> enabled (the default): exporting an extension in the administration also needs the policy  
> package/export\_extension (the Administrator role with all functions has it). disabled: the access to  
> Setup > Extensions or to the package wizard is enough. The command line runs as no user and is not asked.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[ExtensionExportSettings]
RequirePolicy=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
