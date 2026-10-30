---
title: Erweiterungspunkte in smarty.ini
audience: developer, admin
category: examples
summary: Einstellungen von smarty.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (2, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, smarty.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in smarty.ini

Einstellungen von `settings/smarty.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/smarty.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## SmartySettings CompileCheck

Ausgeliefert: `enabled`. [Die Einstellung](../ini-einstellungen/smarty/SmartySettings.md#compilecheck).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/smarty.ini.append.php
[SmartySettings]
CompileCheck=enabled    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## SmartySettings SecurityStaticClassList

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/smarty/SmartySettings.md#securitystaticclasslist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/smarty.ini.append.php
[SmartySettings]
SecurityStaticClassList[]=expExampleSecurityStaticClass    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von smarty.ini](../ini-einstellungen/smarty/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/smarty.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
