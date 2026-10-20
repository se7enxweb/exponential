---
title: Erweiterungspunkte in upload.ini
audience: developer, admin
category: examples
summary: Einstellungen von upload.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (2, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, upload.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in upload.ini

Einstellungen von `settings/upload.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/upload.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## LocationSettings ClassList

Ausgeliefert: eine Liste mit 1: `folder`. [Die Einstellung](../ini-einstellungen/upload/LocationSettings.md#classlist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/upload.ini.append.php
[LocationSettings]
ClassList[]=expExampleClass    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## CreateSettings DefaultClass

Ausgeliefert: `file`. [Die Einstellung](../ini-einstellungen/upload/CreateSettings.md#defaultclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/upload.ini.append.php
[CreateSettings]
DefaultClass=file    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von upload.ini](../ini-einstellungen/upload/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/upload.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
