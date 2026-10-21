---
title: content.ini [ClassGroupIDs]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ClassGroupIDs] in content.ini: Content, Users, Media, Setup"
keywords: Einstellung, content.ini, ClassGroupIDs, INI, setting, Content, Users, Media, Setup
type: generated
generated: exp:help:examples
---
# content.ini [ClassGroupIDs]

Der Block `[ClassGroupIDs]` von `settings/content.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Content

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ClassGroupIDs]
Content=1
*/ ?>
```

## Users

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ClassGroupIDs]
Users=2
*/ ?>
```

## Media

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ClassGroupIDs]
Media=3
*/ ?>
```

## Setup

Standard: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ClassGroupIDs]
Setup=4
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
