---
title: Erweiterungspunkte in error.ini
audience: developer, admin
category: examples
summary: Einstellungen von error.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (3, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, error.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in error.ini

Einstellungen von `settings/error.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/error.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## ErrorSettings DefaultErrorHandler

Ausgeliefert: `displayerror`. [Die Einstellung](../ini-einstellungen/error/ErrorSettings.md#defaulterrorhandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/error.ini.append.php
[ErrorSettings]
DefaultErrorHandler=displayerror    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ErrorSettings-kernel ErrorHandler

Ausgeliefert: ein Hash mit 1: `1=embed`. [Die Einstellung](../ini-einstellungen/error/ErrorSettings-kernel.md#errorhandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/error.ini.append.php
[ErrorSettings-kernel]
ErrorHandler[expexample]=expExampleErrorHandler    # Schlüssel und Wert wählen Sie
*/ ?>
```

## ErrorSettings-shop ErrorHandler

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/error/ErrorSettings-shop.md#errorhandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/error.ini.append.php
[ErrorSettings-shop]
ErrorHandler[]=expExampleErrorHandler    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von error.ini](../ini-einstellungen/error/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/error.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
