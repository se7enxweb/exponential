---
title: site.ini [ConfigurationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ConfigurationSettings] in site.ini: ManagedByDeployment"
keywords: Einstellung, site.ini, ConfigurationSettings, INI, setting, ManagedByDeployment
type: generated
generated: exp:help:examples
---
# site.ini [ConfigurationSettings]

Der Block `[ConfigurationSettings]` von `settings/site.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ManagedByDeployment

> enabled: the configuration of this siteaccess is managed by the deployment  
> (version control) and the admin interface does not change it on the server.  
> In a web request the views that write settings, templates, generated code or  
> packages (settings/edit, toolbar and menu editors, template editor, setup  
> wizards and code generators, package upload/install/uninstall/create/export;  
> the list is expManagedConfiguration::LOCKED\_VIEWS) answer like a view switched  
> off in \[SiteAccessRules\] and are hidden from the menus, and neither  
> eZINI::save() nor expIniEditor::save() writes a settings file. This holds  
> for PHP-FPM, Apache and the application servers that run PHP on the command  
> line (Velocity, the built-in server). Read-only views stay available. Command  
> line scripts are not affected. Set it in a versioned settings file, never  
> from the admin. See doc/eng-us/features/6.0/managed-by-deployment.md.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ConfigurationSettings]
ManagedByDeployment=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
