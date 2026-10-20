---
title: shop.ini [MathSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MathSettings] in shop.ini: MathHandler, MathScale, RoundingType, RoundingPrecision, RoundingTarget"
keywords: Einstellung, shop.ini, MathSettings, INI, setting, MathHandler, MathScale, RoundingType, RoundingPrecision, RoundingTarget
type: generated
generated: exp:help:examples
---
# shop.ini [MathSettings]

Der Block `[MathSettings]` von `settings/shop.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/shop.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/shop.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/shop.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/shop.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MathHandler

> eZPHPMath   - mit Gleitkommaberechnungen von PHP  
> eZBCMath    - mit der Erweiterung bcmath.  

Standard: `eZPHPMath`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[MathSettings]
MathHandler=eZPHPMath
*/ ?>
```

## MathScale

> MathScale legt die signifikanten Stellen nach dem Dezimalpunkt fest  
> Hinweis: gilt nur für den Handler eZBCMath.  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[MathSettings]
MathScale=10
*/ ?>
```

## RoundingType

> RoundingType legt die Rundungsmethode für automatische Umrechnungen fest.  
> Mögliche Werte: none, round, ceil, floor  

Standard: `round`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[MathSettings]
RoundingType=round
*/ ?>
```

## RoundingPrecision

> RoundingPrecision legt die signifikanten Stellen nach dem Dezimalpunkt fest,  
> die erhalten bleiben sollen.  
> Werte:  
>   numerisch, z. B. 0, 1, 2, ...  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[MathSettings]
RoundingPrecision=2
*/ ?>
```

## RoundingTarget

> RoundingTarget erzwingt das Runden auf ein angegebenes Ziel.  
> Beispiel:  
> input: number = 89.468543  
>        precision = 2  
>        target\_1 = 5  
>        target\_2 = 99  
> übliches Runden: round( number, precision ) = 89.47  
> mit target\_1: round( number, precision, target ) = 89.45  
> mit target\_2: round( number, precision, target ) = 89.99  
> Werte:  
>   numerisch, z. B. 1,2,...  
>   false  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[MathSettings]
RoundingTarget=false
*/ ?>
```

## Siehe auch

- [Alle Blöcke von shop.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shop.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
