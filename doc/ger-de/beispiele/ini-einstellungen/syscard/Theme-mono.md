---
title: syscard.ini [Theme-mono]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Theme-mono] in syscard.ini: Logo, LogoShade, Accent, Label, Value, Dim, Ok, Info ..."
keywords: Einstellung, syscard.ini, Theme-mono, INI, setting, Logo, LogoShade, Accent, Label, Value, Dim, Ok, Info, Warn, Error, Gradient, Bold
type: generated
generated: exp:help:examples
---
# syscard.ini [Theme-mono]

Der Block `[Theme-mono]` von `settings/syscard.ini`, mit 12 Einstellung(en).

Bearbeiten Sie `settings/syscard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/syscard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/syscard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/syscard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Logo

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-mono]
Logo=
*/ ?>
```

## LogoShade

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-mono]
LogoShade=
*/ ?>
```

## Accent

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-mono]
Accent=
*/ ?>
```

## Label

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-mono]
Label=
*/ ?>
```

## Value

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-mono]
Value=
*/ ?>
```

## Dim

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-mono]
Dim=
*/ ?>
```

## Ok

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-mono]
Ok=
*/ ?>
```

## Info

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-mono]
Info=
*/ ?>
```

## Warn

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-mono]
Warn=
*/ ?>
```

## Error

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-mono]
Error=
*/ ?>
```

## Gradient

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-mono]
# ein leeres "Gradient[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Gradient[]
*/ ?>
```

## Bold

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-mono]
Bold=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von syscard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
