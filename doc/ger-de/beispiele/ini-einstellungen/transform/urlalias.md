---
title: transform.ini [urlalias]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [urlalias] in transform.ini: Files, Extensions, Commands"
keywords: Einstellung, transform.ini, urlalias, INI, setting, Files, Extensions, Commands
type: generated
generated: exp:help:examples
---
# transform.ini [urlalias]

Der Block `[urlalias]` von `settings/transform.ini`, mit 3 Einstellung(en).

> Rule for URLAlias without support for IRI.  
> Any unicode character not supported by the URI spec will be  
> transformed or removed to produce.  
>
> Note: This was the old behaviour in Exponential.  

Bearbeiten Sie `settings/transform.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/transform.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/transform.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/transform.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Files

> Extra transformation files for urlalias  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[urlalias]
# ein leeres "Files[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Files[]
*/ ?>
```

## Extensions

> Extensions that have transformation files when urlalias is used  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[urlalias]
# ein leeres "Extensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Extensions[]
*/ ?>
```

## Commands

> The commands to use for search  

Standard: eine Liste mit 7: `normalize`, `transform`, `decompose`, `transliterate`, `diacritical`, `lowercase`, `url_cleanup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[urlalias]
# ein leeres "Commands[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Commands[]
Commands[]=normalize
Commands[]=transform
Commands[]=decompose
Commands[]=transliterate
Commands[]=diacritical
Commands[]=lowercase
Commands[]=url_cleanup
*/ ?>
```

## Siehe auch

- [Alle Blöcke von transform.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/transform.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
