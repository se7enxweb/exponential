---
title: upload.ini [file_ClassSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [file_ClassSettings] in upload.ini: FileAttribute, NameAttribute, NamePattern"
keywords: Einstellung, upload.ini, file_ClassSettings, INI, setting, FileAttribute, NameAttribute, NamePattern
type: generated
generated: exp:help:examples
---
# upload.ini [file_ClassSettings]

Der Block `[file_ClassSettings]` von `settings/upload.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/upload.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/upload.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/upload.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/upload.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## FileAttribute

Standard: `file`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[file_ClassSettings]
FileAttribute=file
*/ ?>
```

## NameAttribute

Standard: `name`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[file_ClassSettings]
NameAttribute=name
*/ ?>
```

## NamePattern

Standard: `<original_filename_base>`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[file_ClassSettings]
NamePattern=<original_filename_base>
*/ ?>
```

## Siehe auch

- [Alle Blöcke von upload.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/upload.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
