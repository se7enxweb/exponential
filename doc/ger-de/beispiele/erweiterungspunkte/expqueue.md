---
title: Erweiterungspunkte in expqueue.ini
audience: developer, admin
category: examples
summary: Einstellungen von expqueue.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (6, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, expqueue.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in expqueue.ini

Einstellungen von `settings/expqueue.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/expqueue.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## JobType_image.alias.generate Handler

Ausgeliefert: `expImageAliasQueueHandler`. [Die Einstellung](../ini-einstellungen/expqueue/JobType_image.alias.generate.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/expqueue.ini.append.php
[JobType_image.alias.generate]
Handler=expImageAliasQueueHandler    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## JobType_image.alias.generate Class

Ausgeliefert: `editorial`. [Die Einstellung](../ini-einstellungen/expqueue/JobType_image.alias.generate.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/expqueue.ini.append.php
[JobType_image.alias.generate]
Class=editorial    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## JobType_image.alias.bulk Handler

Ausgeliefert: `expImageAliasQueueHandler`. [Die Einstellung](../ini-einstellungen/expqueue/JobType_image.alias.bulk.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/expqueue.ini.append.php
[JobType_image.alias.bulk]
Handler=expImageAliasQueueHandler    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## JobType_image.alias.bulk Class

Ausgeliefert: `bulk`. [Die Einstellung](../ini-einstellungen/expqueue/JobType_image.alias.bulk.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/expqueue.ini.append.php
[JobType_image.alias.bulk]
Class=bulk    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## JobType_image.alias.resync Handler

Ausgeliefert: `expImageAliasQueueHandler`. [Die Einstellung](../ini-einstellungen/expqueue/JobType_image.alias.resync.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/expqueue.ini.append.php
[JobType_image.alias.resync]
Handler=expImageAliasQueueHandler    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## JobType_image.alias.resync Class

Ausgeliefert: `bulk`. [Die Einstellung](../ini-einstellungen/expqueue/JobType_image.alias.resync.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/expqueue.ini.append.php
[JobType_image.alias.resync]
Class=bulk    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von expqueue.ini](../ini-einstellungen/expqueue/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/expqueue.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
