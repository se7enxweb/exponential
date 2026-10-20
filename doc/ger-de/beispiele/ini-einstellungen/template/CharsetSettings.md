---
title: template.ini [CharsetSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CharsetSettings] in template.ini: DefaultTemplateCharset, DefaultTemplateLocale, AutoConvertOnSave"
keywords: Einstellung, template.ini, CharsetSettings, INI, setting, DefaultTemplateCharset, DefaultTemplateLocale, AutoConvertOnSave
type: generated
generated: exp:help:examples
---
# template.ini [CharsetSettings]

Der Block `[CharsetSettings]` von `settings/template.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/template.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/template.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/template.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/template.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultTemplateCharset

> The charset to use if no charset is specified in the template  

Standard: `utf-8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[CharsetSettings]
DefaultTemplateCharset=utf-8
*/ ?>
```

## DefaultTemplateLocale

> The locale to use for character manipulations if no locale is specified in  
> the template. (C is a special locale meaning 7-bit ASCII). Examples for this  
> setting include "german", "de\_DE.ISO-8859-1"; depending on your operating  
> system.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[CharsetSettings]
DefaultTemplateLocale=
*/ ?>
```

## AutoConvertOnSave

> The following setting will autoconvert the template to the HTTPCharset as set  
> in i18n.ini:CharacterSettings:HTTPCharset when saving a template file from  
> the admin interface, and add or adjust the template header when required.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[CharsetSettings]
AutoConvertOnSave=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von template.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/template.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
