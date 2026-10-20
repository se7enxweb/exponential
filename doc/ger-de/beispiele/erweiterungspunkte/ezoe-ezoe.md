---
title: Erweiterungspunkte in ezoe.ini
audience: developer, admin
category: examples
summary: Einstellungen von ezoe.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (3, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, ezoe.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in ezoe.ini

Einstellungen von `extension/ezoe/settings/ezoe.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/ezoe.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## EditorSettings UploadExtensionCheck

Ausgeliefert: `engine`. [Die Einstellung](../ini-einstellungen/ezoe-ezoe/EditorSettings.md#uploadextensioncheck).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ezoe.ini.append.php
[EditorSettings]
UploadExtensionCheck=engine    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## EditorSettings Plugins

Ausgeliefert: eine Liste mit 7: `eztable`, `paste`, `fullscreen`, `inlinepopups`, `autolink`, `lists`, `breakoutspace`. [Die Einstellung](../ini-einstellungen/ezoe-ezoe/EditorSettings.md#plugins).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ezoe.ini.append.php
[EditorSettings]
Plugins[]=expExamplePlugin    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Engine_tinymce8 ExternalPlugins

Ausgeliefert: ein Hash mit 7: `ezembed=javascript/tinymce8_ez/plugins/ezembed/plugin.js`, `ezcustomtag=javascript/tinymce8_ez/plugins/ezcustomtag/plugin.js`, `ezlink=javascript/tinymce8_ez/plugins/ezlink/plugin.js`, `ezpath=javascript/tinymce8_ez/plugins/ezpath/plugin.js`, `ezliteral=javascript/tinymce8_ez/plugins/ezliteral/plugin.js`, `eztable=javascript/tinymce8_ez/plugins/eztable/plugin.js`, `ezgeneral=javascript/tinymce8_ez/plugins/ezgeneral/plugin.js`. [Die Einstellung](../ini-einstellungen/ezoe-ezoe/Engine_tinymce8.md#externalplugins).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ezoe.ini.append.php
[Engine_tinymce8]
ExternalPlugins[expexample]=expExampleExternalPlugin    # Schlüssel und Wert wählen Sie
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von ezoe.ini](../ini-einstellungen/ezoe-ezoe/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezoe/settings/ezoe.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
