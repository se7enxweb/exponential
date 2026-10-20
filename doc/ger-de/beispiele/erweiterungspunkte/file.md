---
title: Erweiterungspunkte in file.ini
audience: developer, admin
category: examples
summary: Einstellungen von file.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (5, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, file.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in file.ini

Einstellungen von `settings/file.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/file.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## BinaryFileSettings Handler

Ausgeliefert: `eZFilePassthroughHandler`. [Die Einstellung](../ini-einstellungen/file/BinaryFileSettings.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/file.ini.append.php
[BinaryFileSettings]
Handler=eZFilePassthroughHandler    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## FileSettings SystemUserCheck

Ausgeliefert: `disabled`. [Die Einstellung](../ini-einstellungen/file/FileSettings.md#systemusercheck).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/file.ini.append.php
[FileSettings]
SystemUserCheck=disabled    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## FileSettings Handlers

Ausgeliefert: ein Hash mit 3: `gzip=ezgzipcompressionhandler`, `gzipzlib=ezgzipzlibcompressionhandler`, `gzipshell=ezgzipshellcompressionhandler`. [Die Einstellung](../ini-einstellungen/file/FileSettings.md#handlers).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/file.ini.append.php
[FileSettings]
Handlers[expexample]=expExampleHandler    # Schlüssel und Wert wählen Sie
*/ ?>
```

## ClusteringSettings FileHandler

Ausgeliefert: `eZFSFileHandler`. [Die Einstellung](../ini-einstellungen/file/ClusteringSettings.md#filehandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/file.ini.append.php
[ClusteringSettings]
FileHandler=eZFSFileHandler    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ClusterEventsSettings Listener

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/file/ClusterEventsSettings.md#listener).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/file.ini.append.php
[ClusterEventsSettings]
Listener=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von file.ini](../ini-einstellungen/file/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/file.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
