---
title: logviewer.ini [RateLimitSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RateLimitSettings] in logviewer.ini: Requests, Window"
keywords: Einstellung, logviewer.ini, RateLimitSettings, INI, setting, Requests, Window
type: generated
generated: exp:help:examples
---
# logviewer.ini [RateLimitSettings]

Der Block `[RateLimitSettings]` von `settings/logviewer.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/logviewer.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logviewer.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logviewer.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logviewer.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Requests

> Die meisten Feed-Anfragen (Abfragen, Suchen, zugehörige Zeilen), die eine Sitzung in Window Sekunden stellen darf. Eine Anfrage über  
> der Grenze wird mit 429 und Retry-After beantwortet, und die Seite wartet länger.  

Standard: `120`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[RateLimitSettings]
Requests=120
*/ ?>
```

## Window

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[RateLimitSettings]
Window=60
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logviewer.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logviewer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
