---
title: setup.ini [directory_permissions]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [directory_permissions] in setup.ini: CheckList"
keywords: Einstellung, setup.ini, directory_permissions, INI, setting, CheckList
type: generated
generated: exp:help:examples
---
# setup.ini [directory_permissions]

Der Block `[directory_permissions]` von `settings/setup.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/setup.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/setup.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/setup.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/setup.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## CheckList

Standard: `design;extension;settings;settings/override;settings/siteaccess;settings/siteaccess/admin;var;var/cache;var/cache/codepages;var/cache/content;var/cache/ini;var/cache/override;var/cache/template;var/cache/template/process;var/cache/template/tree;var/cache/texttoimage;var/cache/translation;var/log;var/storage;var/storage/original;var/storage/reference;var/storage/variations;var/autoload`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[directory_permissions]
CheckList=design;extension;settings;settings/override;settings/siteaccess;settings/siteaccess/admin;var;var/cache;var/cache/codepages;var/cache/content;var/cache/ini;var/cache/override;var/cache/template;var/cache/template/process;var/cache/template/tree;var/cache/texttoimage;var/cache/translation;var/log;var/storage;var/storage/original;var/storage/reference;var/storage/variations;var/autoload
*/ ?>
```

## Siehe auch

- [Alle Blöcke von setup.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/setup.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
