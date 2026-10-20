---
title: Erweiterungspunkte in binaryfile.ini
audience: developer, admin
category: examples
summary: Einstellungen von binaryfile.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (2, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, binaryfile.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in binaryfile.ini

Einstellungen von `settings/binaryfile.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/binaryfile.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## HandlerSettings Repositories

Ausgeliefert: eine Liste mit 1: `kernel/classes/datatypes/ezbinaryfile`. [Die Einstellung](../ini-einstellungen/binaryfile/HandlerSettings.md#repositories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/binaryfile.ini.append.php
[HandlerSettings]
Repositories[]=expExampleRepositorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## HandlerSettings ExtensionRepositories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/binaryfile/HandlerSettings.md#extensionrepositories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/binaryfile.ini.append.php
[HandlerSettings]
ExtensionRepositories[]=expExampleExtensionRepositorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von binaryfile.ini](../ini-einstellungen/binaryfile/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/binaryfile.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
