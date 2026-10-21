---
title: Erweiterungspunkte in notification.ini
audience: developer, admin
category: examples
summary: Einstellungen von notification.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (5, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, notification.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in notification.ini

Einstellungen von `settings/notification.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/notification.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## NotificationSettings IncludeClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/notification/NotificationSettings.md#includeclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/notification.ini.append.php
[NotificationSettings]
IncludeClasses[]=expExampleIncludeClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## RuleSettings ExtensionDirectories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/notification/RuleSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/notification.ini.append.php
[RuleSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## NotificationEventTypeSettings ExtensionDirectories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/notification/NotificationEventTypeSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/notification.ini.append.php
[NotificationEventTypeSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## NotificationEventHandlerSettings ExtensionDirectories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/notification/NotificationEventHandlerSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/notification.ini.append.php
[NotificationEventHandlerSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## TransportSettings DefaultTransport

Ausgeliefert: `mail`. [Die Einstellung](../ini-einstellungen/notification/TransportSettings.md#defaulttransport).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/notification.ini.append.php
[TransportSettings]
DefaultTransport=mail    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von notification.ini](../ini-einstellungen/notification/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/notification.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
