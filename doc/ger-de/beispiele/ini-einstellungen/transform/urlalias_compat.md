---
title: transform.ini [urlalias_compat]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [urlalias_compat] in transform.ini: Files, Extensions, Commands"
keywords: Einstellung, transform.ini, urlalias_compat, INI, setting, Files, Extensions, Commands
type: generated
generated: exp:help:examples
---
# transform.ini [urlalias_compat]

Der Block `[urlalias_compat]` von `settings/transform.ini`, mit 3 Einstellung(en).

> Alte Regeln des URL-Alias-Systems (schöne URLs); die Regeln  
> sorgen dafür, dass der Text in gleichwertige ASCII-  
> Zeichen umgewandelt wird. Die wichtigsten Regeln sind Transliteration,  
> Kleinschreibung und das Entfernen diakritischer Zeichen.  

Bearbeiten Sie `settings/transform.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/transform.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/transform.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/transform.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Files

> Zusätzliche Umwandlungsdateien für urlalias  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[urlalias_compat]
# ein leeres "Files[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Files[]
*/ ?>
```

## Extensions

> Erweiterungen, die Umwandlungsdateien für urlalias haben  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[urlalias_compat]
# ein leeres "Extensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Extensions[]
*/ ?>
```

## Commands

> Die für die Suche verwendeten Befehle  
> Das ist die alte Form der URLs, nur in Kleinbuchstaben und mit Unterstrich als Trenner,  

Standard: eine Liste mit 7: `normalize`, `transform`, `decompose`, `transliterate`, `diacritical`, `lowercase`, `url_cleanup_compat`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[urlalias_compat]
# ein leeres "Commands[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Commands[]
Commands[]=normalize
Commands[]=transform
Commands[]=decompose
Commands[]=transliterate
Commands[]=diacritical
Commands[]=lowercase
Commands[]=url_cleanup_compat
*/ ?>
```

## Siehe auch

- [Alle Blöcke von transform.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/transform.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
