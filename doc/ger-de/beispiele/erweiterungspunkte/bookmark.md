---
title: Erweiterungspunkte in bookmark.ini
audience: developer, admin
category: examples
summary: Einstellungen von bookmark.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (1, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, bookmark.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in bookmark.ini

Einstellungen von `settings/bookmark.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/bookmark.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## BookmarkTypeSettings TypeClass

Ausgeliefert: ein Hash mit 1: `search=expBookmarkTypeSearch`. [Die Einstellung](../ini-einstellungen/bookmark/BookmarkTypeSettings.md#typeclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/bookmark.ini.append.php
[BookmarkTypeSettings]
TypeClass[expexample]=expExampleTypeClas    # Schlüssel und Wert wählen Sie
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von bookmark.ini](../ini-einstellungen/bookmark/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/bookmark.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
