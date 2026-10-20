---
title: syscard.ini [Theme-high-contrast]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Theme-high-contrast] in syscard.ini: Logo, LogoShade, Logo16, Accent, Accent16, Label, Label16, Value ..."
keywords: Einstellung, syscard.ini, Theme-high-contrast, INI, setting, Logo, LogoShade, Logo16, Accent, Accent16, Label, Label16, Value, Value16, Dim, Dim16, Ok, Info, Warn, Error, Gradient, GradientDirection, Bold
type: generated
generated: exp:help:examples
---
# syscard.ini [Theme-high-contrast]

Der Block `[Theme-high-contrast]` von `settings/syscard.ini`, mit 18 Einstellung(en).

Bearbeiten Sie `settings/syscard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/syscard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/syscard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/syscard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Logo

Standard: `#FF8700`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Logo=#FF8700
*/ ?>
```

## LogoShade

Standard: `#FF8700`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
LogoShade=#FF8700
*/ ?>
```

## Logo16

Standard: `bright-yellow`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Logo16=bright-yellow
*/ ?>
```

## Accent

Standard: `#FFFFFF`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Accent=#FFFFFF
*/ ?>
```

## Accent16

Standard: `bright-white`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Accent16=bright-white
*/ ?>
```

## Label

Standard: `#FFFF00`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Label=#FFFF00
*/ ?>
```

## Label16

Standard: `bright-yellow`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Label16=bright-yellow
*/ ?>
```

## Value

Standard: `#FFFFFF`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Value=#FFFFFF
*/ ?>
```

## Value16

Standard: `bright-white`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Value16=bright-white
*/ ?>
```

## Dim

Standard: `#FFFFFF`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Dim=#FFFFFF
*/ ?>
```

## Dim16

Standard: `bright-white`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Dim16=bright-white
*/ ?>
```

## Ok

Standard: `#00FF00`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Ok=#00FF00
*/ ?>
```

## Info

Standard: `#00FFFF`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Info=#00FFFF
*/ ?>
```

## Warn

Standard: `#FFFF00`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Warn=#FFFF00
*/ ?>
```

## Error

Standard: `#FF5555`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Error=#FF5555
*/ ?>
```

## Gradient

Standard: eine Liste mit 5: `#FF5555`, `#FFFF00`, `#00FF00`, `#00FFFF`, `#FF55FF`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
# ein leeres "Gradient[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Gradient[]
Gradient[]=#FF5555
Gradient[]=#FFFF00
Gradient[]=#00FF00
Gradient[]=#00FFFF
Gradient[]=#FF55FF
*/ ?>
```

## GradientDirection

Standard: `columns`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
GradientDirection=columns
*/ ?>
```

## Bold

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-high-contrast]
Bold=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von syscard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
