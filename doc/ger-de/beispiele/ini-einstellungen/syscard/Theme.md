---
title: syscard.ini [Theme]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Theme] in syscard.ini: Default, Themes"
keywords: Einstellung, syscard.ini, Theme, INI, setting, Default, Themes
type: generated
generated: exp:help:examples
---
# syscard.ini [Theme]

Der Block `[Theme]` von `settings/syscard.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/syscard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/syscard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/syscard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/syscard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Default

> The theme used without --theme=  

Standard: `rainbow`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme]
Default=rainbow
*/ ?>
```

## Themes

Standard: eine Liste mit 5: `rainbow`, `orange`, `mono`, `high-contrast`, `light`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme]
# ein leeres "Themes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Themes[]
Themes[]=rainbow
Themes[]=orange
Themes[]=mono
Themes[]=high-contrast
Themes[]=light
*/ ?>
```

## Siehe auch

- [Alle Blöcke von syscard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
