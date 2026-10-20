---
title: Erweiterungspunkte in contentstructuremenu.ini
audience: developer, admin
category: examples
summary: Einstellungen von contentstructuremenu.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (2, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, contentstructuremenu.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in contentstructuremenu.ini

Einstellungen von `settings/contentstructuremenu.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/contentstructuremenu.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## TreeMenu ShowClasses

Ausgeliefert: eine Liste mit 4: `folder`, `forum`, `gallery`, `user_group`. [Die Einstellung](../ini-einstellungen/contentstructuremenu/TreeMenu.md#showclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/contentstructuremenu.ini.append.php
[TreeMenu]
ShowClasses[]=expExampleShowClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## TreeMenu ChapterClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/contentstructuremenu/TreeMenu.md#chapterclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/contentstructuremenu.ini.append.php
[TreeMenu]
ChapterClasses[]=expExampleChapterClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von contentstructuremenu.ini](../ini-einstellungen/contentstructuremenu/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/contentstructuremenu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
