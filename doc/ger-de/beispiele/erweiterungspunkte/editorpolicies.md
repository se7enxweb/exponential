---
title: Erweiterungspunkte in editorpolicies.ini
audience: developer, admin
category: examples
summary: Einstellungen von editorpolicies.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (1, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, editorpolicies.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in editorpolicies.ini

Einstellungen von `settings/editorpolicies.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/editorpolicies.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## ClassSet_media_site Classes

Ausgeliefert: eine Liste mit 1: `ng_*`. [Die Einstellung](../ini-einstellungen/editorpolicies/ClassSet_media_site.md#classes).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/editorpolicies.ini.append.php
[ClassSet_media_site]
Classes[]=expExampleClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von editorpolicies.ini](../ini-einstellungen/editorpolicies/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/editorpolicies.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
