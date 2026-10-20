---
title: Erweiterungspunkte in setup.ini
audience: developer, admin
category: examples
summary: Einstellungen von setup.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (1, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, setup.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in setup.ini

Einstellungen von `settings/setup.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/setup.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## ezcversion TestClass

Ausgeliefert: `ezcBaseFile`. [Die Einstellung](../ini-einstellungen/setup/ezcversion.md#testclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/setup.ini.append.php
[ezcversion]
TestClass=ezcBaseFile    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von setup.ini](../ini-einstellungen/setup/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/setup.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
