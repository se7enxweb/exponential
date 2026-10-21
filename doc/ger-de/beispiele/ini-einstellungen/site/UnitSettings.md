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

> Beispielzuordnung zwischen Übersetzungs-Siteaccesses und dem Namen, der für den  
> Link der Sprachumschaltung verwendet wird, also dem Namen, der beim Verlinken auf  
> diese Siteaccesses verwendet wird.  
> Beispiel: TranslationSA\[<name of siteaccess>\]=<name of language switcher link, pointing to this siteaccess>  
> TranslationSA\[\]  
> TranslationSA\[eng\]=English  
> TranslationSA\[nor\]=Norwegian  
> TranslationSA\[fre\]=French  
> Beispielzuordnung zwischen Sprachen und Siteaccess; ist sie nicht definiert, wird nach Konvention zugeordnet,  
> z. B. eng-GB => eng  
> In beiden Fällen muss der Name des Siteaccess in \[SiteAccessSettings\]\\RelatedSiteAccessList\[\] stehen  
> Beispiel: LanguageSA\[<language>\]=<name of siteaccess>  
> LanguageSA\[\]  
> LanguageSA\[eng-GB\]=site1\_eng  
> LanguageSA\[nor-NB\]=site1\_nor  
> LanguageSA\[fre-FR\]=site1\_fre  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## BinaryUnits

> Liste der Einheiten, die als binäre Maße gelten  

Standard: `byte;bit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UnitSettings]
BinaryUnits=byte;bit
*/ ?>
```

## UseSIUnits

> True bedeutet, für binäre Maße Ki, Mi usw. statt K, M usw. zu verwenden.  
> Das ist gut, wenn Sie den korrekten Benennungsstandards folgen wollen.  

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
