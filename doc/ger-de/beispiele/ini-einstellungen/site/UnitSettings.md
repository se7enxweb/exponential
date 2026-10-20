---
title: site.ini [UnitSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UnitSettings] in site.ini: BinaryUnits, UseSIUnits"
keywords: Einstellung, site.ini, UnitSettings, INI, setting, BinaryUnits, UseSIUnits
type: generated
generated: exp:help:examples
---
# site.ini [UnitSettings]

Der Block `[UnitSettings]` von `settings/site.ini`, mit 2 Einstellung(en).

> Example mapping between translation siteaccesses and the name to use for the  
> language switcher link, e.g. the name which will be used when making links to  
> these siteaccesses.  
> Example: TranslationSA\[<name of siteaccess>\]=<name of language switcher link, pointing to this siteaccess>  
> TranslationSA\[\]  
> TranslationSA\[eng\]=English  
> TranslationSA\[nor\]=Norwegian  
> TranslationSA\[fre\]=French  
> Example mapping between languages and siteaccess, mapping is done by convention if not defined  
> like eg: eng-GB => eng  
> In both cases sa name needs to be present in \[SiteAccessSettings\]\\RelatedSiteAccessList\[\]  
> Example: LanguageSA\[<language>\]=<name of siteaccess>  
> LanguageSA\[\]  
> LanguageSA\[eng-GB\]=site1\_eng  
> LanguageSA\[nor-NB\]=site1\_nor  
> LanguageSA\[fre-FR\]=site1\_fre  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## BinaryUnits

> List of units which acts as binary measures  

Standard: `byte;bit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UnitSettings]
BinaryUnits=byte;bit
*/ ?>
```

## UseSIUnits

> True means use Ki, Mi etc. for binary measures instead of K, M etc.  
> This is good if you want to follow proper naming standards.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UnitSettings]
UseSIUnits=false
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
