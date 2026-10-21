---
title: Erweiterungspunkte in mailpreferences.ini
audience: developer, admin
category: examples
summary: Einstellungen von mailpreferences.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (11, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, mailpreferences.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in mailpreferences.ini

Einstellungen von `settings/mailpreferences.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/mailpreferences.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## Category_content HandlerClass

Ausgeliefert: `expNotificationMailCategoryHandler`. [Die Einstellung](../ini-einstellungen/mailpreferences/Category_content.md#handlerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/mailpreferences.ini.append.php
[Category_content]
HandlerClass=expNotificationMailCategoryHandler    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Category_collaboration HandlerClass

Ausgeliefert: `expNotificationMailCategoryHandler`. [Die Einstellung](../ini-einstellungen/mailpreferences/Category_collaboration.md#handlerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/mailpreferences.ini.append.php
[Category_collaboration]
HandlerClass=expNotificationMailCategoryHandler    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Category_newsletter HandlerClass

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/mailpreferences/Category_newsletter.md#handlerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/mailpreferences.ini.append.php
[Category_newsletter]
HandlerClass=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Category_marketing HandlerClass

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/mailpreferences/Category_marketing.md#handlerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/mailpreferences.ini.append.php
[Category_marketing]
HandlerClass=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Category_system HandlerClass

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/mailpreferences/Category_system.md#handlerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/mailpreferences.ini.append.php
[Category_system]
HandlerClass=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Category_security HandlerClass

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/mailpreferences/Category_security.md#handlerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/mailpreferences.ini.append.php
[Category_security]
HandlerClass=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Category_orders HandlerClass

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/mailpreferences/Category_orders.md#handlerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/mailpreferences.ini.append.php
[Category_orders]
HandlerClass=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Category_legal HandlerClass

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/mailpreferences/Category_legal.md#handlerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/mailpreferences.ini.append.php
[Category_legal]
HandlerClass=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Category_admin HandlerClass

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/mailpreferences/Category_admin.md#handlerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/mailpreferences.ini.append.php
[Category_admin]
HandlerClass=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## SuppressionSettings Listeners

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/mailpreferences/SuppressionSettings.md#listeners).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/mailpreferences.ini.append.php
[SuppressionSettings]
Listeners[]=expExampleListener    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## BounceSettings MessageListeners

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/mailpreferences/BounceSettings.md#messagelisteners).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/mailpreferences.ini.append.php
[BounceSettings]
MessageListeners[]=expExampleMessageListener    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von mailpreferences.ini](../ini-einstellungen/mailpreferences/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
