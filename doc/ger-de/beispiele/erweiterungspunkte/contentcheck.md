---
title: Erweiterungspunkte in contentcheck.ini
audience: developer, admin
category: examples
summary: Einstellungen von contentcheck.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (3, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, contentcheck.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in contentcheck.ini

Einstellungen von `settings/contentcheck.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/contentcheck.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## UnreferencedSettings AssetClassList

Ausgeliefert: eine Liste mit 8: `image`, `file`, `video`, `audio`, `flash`, `quicktime`, `windows_media`, `real_video`. [Die Einstellung](../ini-einstellungen/contentcheck/UnreferencedSettings.md#assetclasslist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/contentcheck.ini.append.php
[UnreferencedSettings]
AssetClassList[]=expExampleAssetClass    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## UnreferencedSettings StorageParentClassList

Ausgeliefert: eine Liste mit 1: `folder`. [Die Einstellung](../ini-einstellungen/contentcheck/UnreferencedSettings.md#storageparentclasslist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/contentcheck.ini.append.php
[UnreferencedSettings]
StorageParentClassList[]=expExampleStorageParentClass    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## MissingFileSettings Handlers

Ausgeliefert: ein Hash mit 3: `ezimage=expContentCheckFileImage`, `ezbinaryfile=expContentCheckFileTable`, `ezmedia=expContentCheckFileTable`. [Die Einstellung](../ini-einstellungen/contentcheck/MissingFileSettings.md#handlers).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/contentcheck.ini.append.php
[MissingFileSettings]
Handlers[expexample]=expExampleHandler    # Schlüssel und Wert wählen Sie
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von contentcheck.ini](../ini-einstellungen/contentcheck/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/contentcheck.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
