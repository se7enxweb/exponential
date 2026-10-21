---
title: syscard.ini [Theme-rainbow]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Theme-rainbow] in syscard.ini: Logo, LogoShade, Logo16, Accent, Accent16, Label, Label16, Value ..."
keywords: Einstellung, syscard.ini, Theme-rainbow, INI, setting, Logo, LogoShade, Logo16, Accent, Accent16, Label, Label16, Value, Dim, Ok, Info, Warn, Error, Gradient, GradientDirection, Bold
type: generated
generated: exp:help:examples
---
# syscard.ini [Theme-rainbow]

Der Block `[Theme-rainbow]` von `settings/syscard.ini`, mit 16 Einstellung(en).

> Ein Theme: eine Farbe #RRGGBB pro Rolle (leer: die eigene Farbe des Terminals). Logo und LogoShade schattieren das „e“ von oben  
> nach unten; Accent ist der Titel und die Abschnittsüberschriften; Gradient\[\] der Schriftzug und der Verlaufsbalken, über  
> die Spalten oder die Zeilen (GradientDirection). <Role>16= nennt die Grundfarbe für ein Terminal mit 16 Farben (black, red,  
> green, yellow, blue, magenta, cyan, white, bright-<name>), wenn die nächstliegende falsch aussähe.  

Bearbeiten Sie `settings/syscard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/syscard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/syscard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/syscard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Logo

Standard: `#F28C28`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
Logo=#F28C28
*/ ?>
```

## LogoShade

Standard: `#C2410C`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
LogoShade=#C2410C
*/ ?>
```

## Logo16

Standard: `bright-yellow`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
Logo16=bright-yellow
*/ ?>
```

## Accent

Standard: `#F28C28`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
Accent=#F28C28
*/ ?>
```

## Accent16

Standard: `yellow`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
Accent16=yellow
*/ ?>
```

## Label

Standard: `#F28C28`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
Label=#F28C28
*/ ?>
```

## Label16

Standard: `yellow`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
Label16=yellow
*/ ?>
```

## Value

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
Value=
*/ ?>
```

## Dim

Standard: `#8A8A8A`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
Dim=#8A8A8A
*/ ?>
```

## Ok

Standard: `#22C55E`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
Ok=#22C55E
*/ ?>
```

## Info

Standard: `#38BDF8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
Info=#38BDF8
*/ ?>
```

## Warn

Standard: `#EAB308`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
Warn=#EAB308
*/ ?>
```

## Error

Standard: `#EF4444`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
Error=#EF4444
*/ ?>
```

## Gradient

Standard: eine Liste mit 7: `#FF3B30`, `#FF9500`, `#FFCC00`, `#34C759`, `#00C7BE`, `#007AFF`, `#AF52DE`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
# ein leeres "Gradient[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Gradient[]
Gradient[]=#FF3B30
Gradient[]=#FF9500
Gradient[]=#FFCC00
Gradient[]=#34C759
Gradient[]=#00C7BE
Gradient[]=#007AFF
Gradient[]=#AF52DE
*/ ?>
```

## GradientDirection

Standard: `columns`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
GradientDirection=columns
*/ ?>
```

## Bold

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Theme-rainbow]
Bold=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von syscard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
