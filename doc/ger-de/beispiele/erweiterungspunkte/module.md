---
title: Erweiterungspunkte in module.ini
audience: developer, admin
category: examples
summary: Einstellungen von module.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (2, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, module.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in module.ini

Einstellungen von `settings/module.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/module.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## ModuleSettings ModuleRepositories

Ausgeliefert: eine Liste mit 2: `kernel`, `kernel/private/modules`. [Die Einstellung](../ini-einstellungen/module/ModuleSettings.md#modulerepositories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/module.ini.append.php
[ModuleSettings]
ModuleRepositories[]=expExampleModuleRepositorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## ModuleSettings ExtensionRepositories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/module/ModuleSettings.md#extensionrepositories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/module.ini.append.php
[ModuleSettings]
ExtensionRepositories[]=expExampleExtensionRepositorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von module.ini](../ini-einstellungen/module/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/module.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
