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

> Die Breite, wenn das Terminal sie nicht nennt (COLUMNS, stty); --width= gewinnt  

Standard: `80`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Display]
DefaultWidth=80
*/ ?>
```

## Wordmark

> Der Schriftzug "EXPONENTIAL" unter dem großen Logo, wenn das Terminal breit genug ist  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Display]
Wordmark=enabled
*/ ?>
```

## ColorBars

> Die Palettenzeilen und der Verlaufsbalken am Ende der Karte (nur mit Farben)  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Display]
ColorBars=enabled
*/ ?>
```

## Hints

> Der Block Hinweise am Ende der Karte  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Display]
Hints=enabled
*/ ?>
```

## Charset

> auto (UTF-8-Locale: Blockgrafik und Punkte; sonst ASCII), unicode oder ascii  

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
