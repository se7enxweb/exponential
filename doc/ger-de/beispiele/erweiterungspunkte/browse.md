---
title: Erweiterungspunkte in browse.ini
audience: developer, admin
category: examples
summary: Einstellungen von browse.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (3, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, browse.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in browse.ini

Einstellungen von `settings/browse.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/browse.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## RSSExportImageBrowse Class

Ausgeliefert: eine Liste mit 2: ``, `image`. [Die Einstellung](../ini-einstellungen/browse/RSSExportImageBrowse.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/browse.ini.append.php
[RSSExportImageBrowse]
Class[]=expExampleClas    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## RSSUserBrowse Class

Ausgeliefert: eine Liste mit 1: `user`. [Die Einstellung](../ini-einstellungen/browse/RSSUserBrowse.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/browse.ini.append.php
[RSSUserBrowse]
Class[]=expExampleClas    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AssignRole Class

Ausgeliefert: eine Liste mit 2: `user`, `user_group`. [Die Einstellung](../ini-einstellungen/browse/AssignRole.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/browse.ini.append.php
[AssignRole]
Class[]=expExampleClas    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von browse.ini](../ini-einstellungen/browse/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/browse.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
