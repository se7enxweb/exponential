---
title: Erweiterungspunkte in migration.ini
audience: developer, admin
category: examples
summary: Einstellungen von migration.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (3, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, migration.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in migration.ini

Einstellungen von `settings/migration.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/migration.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## MigrationSettings ExtensionDirectory

Ausgeliefert: `migrations`. [Die Einstellung](../ini-einstellungen/migration/MigrationSettings.md#extensiondirectory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/migration.ini.append.php
[MigrationSettings]
ExtensionDirectory=migrations    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## RemoteSettings PhpCheck

Ausgeliefert: `major`. [Die Einstellung](../ini-einstellungen/migration/RemoteSettings.md#phpcheck).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/migration.ini.append.php
[RemoteSettings]
PhpCheck=major    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## RemoteSettings ExtensionCheck

Ausgeliefert: `migrations`. [Die Einstellung](../ini-einstellungen/migration/RemoteSettings.md#extensioncheck).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/migration.ini.append.php
[RemoteSettings]
ExtensionCheck=migrations    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von migration.ini](../ini-einstellungen/migration/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
