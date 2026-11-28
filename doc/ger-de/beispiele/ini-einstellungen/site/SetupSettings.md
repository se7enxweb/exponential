---
title: site.ini [SetupSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SetupSettings] in site.ini: PageLayout, CriticalTests, OptionalTests, OverrideSiteDesign, AllowWizardOnInstalledSite, SharedStateDir"
keywords: Einstellung, site.ini, SetupSettings, INI, setting, PageLayout, CriticalTests, OptionalTests, OverrideSiteDesign, AllowWizardOnInstalledSite, SharedStateDir
type: generated
generated: exp:help:examples
---
# site.ini [SetupSettings]

Der Block `[SetupSettings]` von `settings/site.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## PageLayout

> Das Pagelayout für die Ersteinrichtung  

Standard: `setup_pagelayout.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SetupSettings]
PageLayout=setup_pagelayout.tpl
*/ ?>
```

## CriticalTests

> Liste der auszuführenden Tests; kritische Tests müssen bestehen, damit das Setup weitergeht  

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

> Der Setup-Assistent auf einer bereits installierten Website. Er zeigt zuerst eine  
> Seite, die das sagt, und ändert nichts. auto (die Vorgabe): Hat der Administrator  
> [SiteAccessSettings] CheckValidity=true gesetzt, bietet diese Seite „Einrichtungsassistenten  
> trotzdem ausführen“ an, das zu einer Bestätigungsseite führt; enabled: immer angeboten;  
> disabled: nie. Erst der POST der Bestätigungsseite startet den Assistenten und versetzt  
> die Website in den Wartungsmodus. Neu installiert wird auch mit dem Kickstarter oder  
> einer frischen Installation.  

Standard: `auto`. Werte: `auto`, `enabled`, `disabled` (jeder andere Wert gilt als `auto`). Siehe
[Der Setup-Assistent auf einer installierten Website](../../../bc/6.0/setup-assistent-auf-installierter-website.md).

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php: der Assistent läuft auf dieser installierten Website nie, auch nicht mit CheckValidity=true
[SetupSettings]
AllowWizardOnInstalledSite=disabled
*/ ?>
```

## SharedStateDir

> Wo der Setup-Assistent ablegt, was jeder Knoten einer von mehreren Knoten  
> bedienten Website sehen muss: seine Frist (der Wartungsmodus, solange er läuft),  
> seinen Durchlauf und setup.log, die Installiert-Markierung und Kopien der Dateien,  
> die er schreibt (die Einstellungen der neuen Website, importierte Pakete), für den  
> Knoten, der den nächsten Schritt bedient. Ein Verzeichnis auf einem Speicher, den  
> alle Knoten teilen, absolut oder relativ zur Installation; die Umgebungsvariable  
> EXP\_SETUP\_SHARED\_STATE\_DIR hat Vorrang. Leer: <MountPointPath>/var/exp-setup, wenn  
> file.ini eZDFSFileHandler verwendet, sonst keines, und ein einzelner Knoten behält  
> alles in var/.  

Standard: leer. Siehe
[Mehrere Web-Knoten](../../../hilfe/hosting/betrieb/mehrere-web-knoten.md) und [Der Setup-Assistent](../../../installation/05-setup-assistent.md).

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php: jeder Knoten hat /mnt/shared eingehängt
[SetupSettings]
SharedStateDir=/mnt/shared/exp-setup
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
