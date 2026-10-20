---
title: syscard.ini [Theme-light]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Theme-light] in syscard.ini: Logo, LogoShade, Logo16, Accent, Accent16, Label, Label16, Value ..."
keywords: Einstellung, syscard.ini, Theme-light, INI, setting, Logo, LogoShade, Logo16, Accent, Accent16, Label, Label16, Value, Value16, Dim, Dim16, Ok, Info, Warn, Error, Gradient, GradientDirection, Bold
type: generated
generated: exp:help:examples
---
# syscard.ini [Theme-light]

Der Block `[Theme-light]` von `settings/syscard.ini`, mit 18 Einstellung(en).

> For a terminal with a light background: darker colors  

Bearbeiten Sie `settings/syscard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/syscard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/syscard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/syscard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Logo

Standard: `#C2410C`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Logo=#C2410C
*/ ?>
```

## LogoShade

Standard: `#9A3412`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
LogoShade=#9A3412
*/ ?>
```

## Logo16

Standard: `red`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Logo16=red
*/ ?>
```

## Accent

Standard: `#9A3412`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Accent=#9A3412
*/ ?>
```

## Accent16

Standard: `red`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Accent16=red
*/ ?>
```

## Label

Standard: `#B45309`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Label=#B45309
*/ ?>
```

## Label16

Standard: `red`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Label16=red
*/ ?>
```

## Value

Standard: `#1C1917`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Value=#1C1917
*/ ?>
```

## Value16

Standard: `black`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Value16=black
*/ ?>
```

## Dim

Standard: `#57534E`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Dim=#57534E
*/ ?>
```

## Dim16

Standard: `black`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Dim16=black
*/ ?>
```

## Ok

Standard: `#15803D`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Ok=#15803D
*/ ?>
```

## Info

Standard: `#1D4ED8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Info=#1D4ED8
*/ ?>
```

## Warn

Standard: `#A16207`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Warn=#A16207
*/ ?>
```

## Error

Standard: `#B91C1C`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Error=#B91C1C
*/ ?>
```

## Gradient

Standard: eine Liste mit 6: `#B91C1C`, `#C2410C`, `#A16207`, `#15803D`, `#1D4ED8`, `#7E22CE`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
# ein leeres "Gradient[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Gradient[]
Gradient[]=#B91C1C
Gradient[]=#C2410C
Gradient[]=#A16207
Gradient[]=#15803D
Gradient[]=#1D4ED8
Gradient[]=#7E22CE
*/ ?>
```

## GradientDirection

Standard: `columns`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
GradientDirection=columns
*/ ?>
```

## Bold

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-light]
Bold=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von syscard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
