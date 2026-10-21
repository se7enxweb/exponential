---
title: datetime.ini [ClassSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ClassSettings] in datetime.ini: Formats"
keywords: Einstellung, datetime.ini, ClassSettings, INI, setting, Formats
type: generated
generated: exp:help:examples
---
# datetime.ini [ClassSettings]

Der Block `[ClassSettings]` von `settings/datetime.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/datetime.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/datetime.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/datetime.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/datetime.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Formats

Standard: ein Hash mit 6: `all=%a, %A, %d, %D, %F, %g, %G, %h, %H, %i, %j, %l, %m, %M, %n, %O, %s, %T, %U, %w, %W, %Y, %y, %z, %Z, %r, %c`, `short_date=%j.%n.%Y`, `long_date=%j %F %Y`, `short_date_usa=%n/%j/%Y`, `time_12=%h:%i:%s %a`, `time_24=%H:%i:%s`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datetime.ini.append.php
[ClassSettings]
# ein leeres "Formats[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Formats[]
Formats[all]=%a, %A, %d, %D, %F, %g, %G, %h, %H, %i, %j, %l, %m, %M, %n, %O, %s, %T, %U, %w, %W, %Y, %y, %z, %Z, %r, %c
Formats[short_date]=%j.%n.%Y
Formats[long_date]=%j %F %Y
Formats[short_date_usa]=%n/%j/%Y
Formats[time_12]=%h:%i:%s %a
Formats[time_24]=%H:%i:%s
*/ ?>
```

## Siehe auch

- [Alle Blöcke von datetime.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/datetime.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
