---
title: syscard.ini [Theme-orange]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Theme-orange] in syscard.ini: Logo, LogoShade, Logo16, Accent, Accent16, Label, Label16, Value ..."
keywords: Einstellung, syscard.ini, Theme-orange, INI, setting, Logo, LogoShade, Logo16, Accent, Accent16, Label, Label16, Value, Dim, Ok, Info, Warn, Error, Gradient, GradientDirection, Bold
type: generated
generated: exp:help:examples
---
# syscard.ini [Theme-orange]

Der Block `[Theme-orange]` von `settings/syscard.ini`, mit 16 Einstellung(en).

Bearbeiten Sie `settings/syscard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/syscard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/syscard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/syscard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Logo

Standard: `#F28C28`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
Logo=#F28C28
*/ ?>
```

## LogoShade

Standard: `#C2410C`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
LogoShade=#C2410C
*/ ?>
```

## Logo16

Standard: `bright-yellow`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
Logo16=bright-yellow
*/ ?>
```

## Accent

Standard: `#F28C28`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
Accent=#F28C28
*/ ?>
```

## Accent16

Standard: `yellow`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
Accent16=yellow
*/ ?>
```

## Label

Standard: `#FDBA74`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
Label=#FDBA74
*/ ?>
```

## Label16

Standard: `yellow`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
Label16=yellow
*/ ?>
```

## Value

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
Value=
*/ ?>
```

## Dim

Standard: `#8A8A8A`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
Dim=#8A8A8A
*/ ?>
```

## Ok

Standard: `#22C55E`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
Ok=#22C55E
*/ ?>
```

## Info

Standard: `#FDBA74`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
Info=#FDBA74
*/ ?>
```

## Warn

Standard: `#EAB308`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
Warn=#EAB308
*/ ?>
```

## Error

Standard: `#EF4444`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
Error=#EF4444
*/ ?>
```

## Gradient

Standard: eine Liste mit 3: `#FDBA74`, `#F28C28`, `#C2410C`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
# ein leeres "Gradient[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Gradient[]
Gradient[]=#FDBA74
Gradient[]=#F28C28
Gradient[]=#C2410C
*/ ?>
```

## GradientDirection

Standard: `columns`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
GradientDirection=columns
*/ ?>
```

## Bold

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-orange]
Bold=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von syscard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
