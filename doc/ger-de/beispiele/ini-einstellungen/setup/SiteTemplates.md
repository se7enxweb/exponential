---
title: setup.ini [SiteTemplates]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SiteTemplates] in setup.ini: ThumbnailBase, ThumbnailExtension, Name"
keywords: Einstellung, setup.ini, SiteTemplates, INI, setting, ThumbnailBase, ThumbnailExtension, Name
type: generated
generated: exp:help:examples
---
# setup.ini [SiteTemplates]

Der Block `[SiteTemplates]` von `settings/setup.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/setup.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/setup.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/setup.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/setup.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ThumbnailBase

Standard: `eZ_setup_template`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[SiteTemplates]
ThumbnailBase=eZ_setup_template
*/ ?>
```

## ThumbnailExtension

Standard: `png`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[SiteTemplates]
ThumbnailExtension=png
*/ ?>
```

## Name

Standard: eine Liste mit 3: `corporate`, `forum`, `magazine`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[SiteTemplates]
# ein leeres "Name[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Name[]
Name[]=corporate
Name[]=forum
Name[]=magazine
*/ ?>
```

## Siehe auch

- [Alle Blöcke von setup.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/setup.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
