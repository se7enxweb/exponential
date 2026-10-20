---
title: site.ini [SetupSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SetupSettings] in site.ini: PageLayout, CriticalTests, OptionalTests, OverrideSiteDesign, AllowWizardOnInstalledSite"
keywords: Einstellung, site.ini, SetupSettings, INI, setting, PageLayout, CriticalTests, OptionalTests, OverrideSiteDesign, AllowWizardOnInstalledSite
type: generated
generated: exp:help:examples
---
# site.ini [SetupSettings]

Der Block `[SetupSettings]` von `settings/site.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## PageLayout

> The pagelayout to use for first time setups  

Standard: `setup_pagelayout.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SetupSettings]
PageLayout=setup_pagelayout.tpl
*/ ?>
```

## CriticalTests

> List of tests to be run, critical tests must pass for the setup to continue  

Standard: `directory_permissions;phpversion;database_extensions;image_conversion;safe_mode;memory_limit;execution_time;magic_quotes_runtime;allow_url_fopen;php_session;file_upload;zlib_extension;dom_extension;iconv_extension;mbstring_extension;intl_extension;xsl_extension;timezone;ezcversion`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SetupSettings]
CriticalTests=directory_permissions;phpversion;database_extensions;image_conversion;safe_mode;memory_limit;execution_time;magic_quotes_runtime;allow_url_fopen;php_session;file_upload;zlib_extension;dom_extension;iconv_extension;mbstring_extension;intl_extension;xsl_extension;timezone;ezcversion
*/ ?>
```

## OptionalTests

Standard: `variables_order;php_magicquotes;curl_extension;imagegd_extension;imagemagick_program;database_all_extensions;php_register_globals;texttoimage_functions;open_basedir`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SetupSettings]
OptionalTests=variables_order;php_magicquotes;curl_extension;imagegd_extension;imagemagick_program;database_all_extensions;php_register_globals;texttoimage_functions;open_basedir
*/ ?>
```

## OverrideSiteDesign

Standard: `standard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SetupSettings]
OverrideSiteDesign=standard
*/ ?>
```

## AllowWizardOnInstalledSite

> The setup wizard (setup/init) on a site that is already installed: disabled  
> shows a page that says so and changes nothing. enabled is for development  
> only: the wizard then runs after a confirm page, and the site goes into  
> maintenance mode once that page is posted. Reinstalling a site is done with  
> the kickstarter or a fresh installation.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SetupSettings]
AllowWizardOnInstalledSite=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
