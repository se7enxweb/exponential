---
title: syscard.ini [Display]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Display] in syscard.ini: DefaultWidth, Wordmark, ColorBars, Hints, Charset"
keywords: Einstellung, syscard.ini, Display, INI, setting, DefaultWidth, Wordmark, ColorBars, Hints, Charset
type: generated
generated: exp:help:examples
---
# syscard.ini [Display]

Der Block `[Display]` von `settings/syscard.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/syscard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/syscard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/syscard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/syscard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultWidth

> The width when the terminal does not say (COLUMNS, stty); --width= wins  

Standard: `80`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Display]
DefaultWidth=80
*/ ?>
```

## Wordmark

> The wordmark "EXPONENTIAL" under the big logo, when the terminal is wide enough  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Display]
Wordmark=enabled
*/ ?>
```

## ColorBars

> The palette rows and the gradient bar at the end of the card (only with colors)  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Display]
ColorBars=enabled
*/ ?>
```

## Hints

> The Hints block at the end of the card  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Display]
Hints=enabled
*/ ?>
```

## Charset

> auto (UTF-8 locale: block art and dots; else ASCII), unicode or ascii  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Display]
Charset=auto
*/ ?>
```

## Siehe auch

- [Alle Blöcke von syscard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
