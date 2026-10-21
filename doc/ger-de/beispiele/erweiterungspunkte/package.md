---
title: Erweiterungspunkte in package.ini
audience: developer, admin
category: examples
summary: Einstellungen von package.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (3, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, package.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in package.ini

Einstellungen von `settings/package.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/package.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## PackageSettings ExtensionDirectories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/package/PackageSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/package.ini.append.php
[PackageSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## CreationSettings HandlerList

Ausgeliefert: eine Liste mit 4: `ezcontentobject`, `ezcontentclass`, `ezextension`, `ezstyle`. [Die Einstellung](../ini-einstellungen/package/CreationSettings.md#handlerlist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/package.ini.append.php
[CreationSettings]
HandlerList[]=expExampleHandler    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## InstallerSettings HandlerList

Ausgeliefert: eine Liste mit 2: `ezcontentobject`, `ezinstallscript`. [Die Einstellung](../ini-einstellungen/package/InstallerSettings.md#handlerlist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/package.ini.append.php
[InstallerSettings]
HandlerList[]=expExampleHandler    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von package.ini](../ini-einstellungen/package/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
