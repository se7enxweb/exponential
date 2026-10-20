---
title: Erweiterungspunkte in ini.ini
audience: developer, admin
category: examples
summary: Einstellungen von ini.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (1, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, ini.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in ini.ini

Einstellungen von `settings/ini.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/ini.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## IniCommandSettings ScopeProviders

Ausgeliefert: eine Liste mit 2: `expIniCoreScopeProvider`, `expIniExtensionScopeProvider`. [Die Einstellung](../ini-einstellungen/ini/IniCommandSettings.md#scopeproviders).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ini.ini.append.php
[IniCommandSettings]
ScopeProviders[]=expExampleScopeProvider    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von ini.ini](../ini-einstellungen/ini/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/ini.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
