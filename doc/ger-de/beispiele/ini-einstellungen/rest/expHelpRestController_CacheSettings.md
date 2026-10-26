---
title: rest.ini [expHelpRestController_CacheSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [expHelpRestController_CacheSettings] in rest.ini: ApplicationCache"
keywords: Einstellung, rest.ini, expHelpRestController_CacheSettings, INI, setting, ApplicationCache
type: generated
generated: exp:help:examples
---
# rest.ini [expHelpRestController_CacheSettings]

Der Block `[expHelpRestController_CacheSettings]` von `settings/rest.ini`, mit 1 Einstellung(en).

> The context help of the help provider is never cached: what a user may see depends on the user  

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ApplicationCache

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[expHelpRestController_CacheSettings]
ApplicationCache=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rest.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rest.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
