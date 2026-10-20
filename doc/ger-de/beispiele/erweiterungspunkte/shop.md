---
title: Erweiterungspunkte in shop.ini
audience: developer, admin
category: examples
summary: Einstellungen von shop.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (7, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, shop.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in shop.ini

Einstellungen von `settings/shop.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/shop.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## MathSettings MathHandler

Ausgeliefert: `eZPHPMath`. [Die Einstellung](../ini-einstellungen/shop/MathSettings.md#mathhandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/shop.ini.append.php
[MathSettings]
MathHandler=eZPHPMath    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ExchangeRatesSettings ExchangeRatesUpdateHandler

Ausgeliefert: `eZECB`. [Die Einstellung](../ini-einstellungen/shop/ExchangeRatesSettings.md#exchangeratesupdatehandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/shop.ini.append.php
[ExchangeRatesSettings]
ExchangeRatesUpdateHandler=eZECB    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ExchangeRatesSettings ExtensionDirectories

Ausgeliefert: eine Liste mit 1: ``. [Die Einstellung](../ini-einstellungen/shop/ExchangeRatesSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/shop.ini.append.php
[ExchangeRatesSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## ShippingSettings ExtensionDirectories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/shop/ShippingSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/shop.ini.append.php
[ShippingSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## BasketInfoSettings Handler

Ausgeliefert: `ezdefault`. [Die Einstellung](../ini-einstellungen/shop/BasketInfoSettings.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/shop.ini.append.php
[BasketInfoSettings]
Handler=ezdefault    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## BasketInfoSettings ExtensionDirectories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/shop/BasketInfoSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/shop.ini.append.php
[BasketInfoSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## VATSettings ExtensionDirectories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/shop/VATSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/shop.ini.append.php
[VATSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von shop.ini](../ini-einstellungen/shop/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shop.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
