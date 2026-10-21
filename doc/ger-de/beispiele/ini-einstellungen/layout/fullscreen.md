---
title: layout.ini [fullscreen]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [fullscreen] in layout.ini: PageLayout"
keywords: Einstellung, layout.ini, fullscreen, INI, setting, PageLayout
type: generated
generated: exp:help:examples
---
# layout.ini [fullscreen]

Der Block `[fullscreen]` von `settings/layout.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/layout.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/layout.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/layout.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/layout.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## PageLayout

Standard: `fullscreen_pagelayout.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/layout.ini.append.php
[fullscreen]
PageLayout=fullscreen_pagelayout.tpl
*/ ?>
```

## Siehe auch

- [Alle Blöcke von layout.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/layout.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
