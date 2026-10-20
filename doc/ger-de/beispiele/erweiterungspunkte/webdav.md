---
title: Erweiterungspunkte in webdav.ini
audience: developer, admin
category: examples
summary: Einstellungen von webdav.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (2, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, webdav.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in webdav.ini

Einstellungen von `settings/webdav.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/webdav.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## GeneralSettings FolderClasses

Ausgeliefert: eine Liste mit 1: `folder`. [Die Einstellung](../ini-einstellungen/webdav/GeneralSettings.md#folderclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/webdav.ini.append.php
[GeneralSettings]
FolderClasses[]=expExampleFolderClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## FolderSettings FolderClass

Ausgeliefert: `1`. [Die Einstellung](../ini-einstellungen/webdav/FolderSettings.md#folderclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/webdav.ini.append.php
[FolderSettings]
FolderClass=1    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von webdav.ini](../ini-einstellungen/webdav/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/webdav.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
