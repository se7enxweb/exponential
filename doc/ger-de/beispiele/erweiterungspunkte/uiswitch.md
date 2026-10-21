---
title: Erweiterungspunkte in uiswitch.ini
audience: developer, admin
category: examples
summary: Einstellungen von uiswitch.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (2, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, uiswitch.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in uiswitch.ini

Einstellungen von `settings/uiswitch.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/uiswitch.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## UISwitch_language Class

Ausgeliefert: `expUISwitchLanguageProvider`. [Die Einstellung](../ini-einstellungen/uiswitch/UISwitch_language.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/uiswitch.ini.append.php
[UISwitch_language]
Class=expUISwitchLanguageProvider    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## UISwitch_design Class

Ausgeliefert: `expUISwitchDesignProvider`. [Die Einstellung](../ini-einstellungen/uiswitch/UISwitch_design.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/uiswitch.ini.append.php
[UISwitch_design]
Class=expUISwitchDesignProvider    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von uiswitch.ini](../ini-einstellungen/uiswitch/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/uiswitch.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
