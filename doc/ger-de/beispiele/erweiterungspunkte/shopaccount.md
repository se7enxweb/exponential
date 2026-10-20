---
title: Erweiterungspunkte in shopaccount.ini
audience: developer, admin
category: examples
summary: Einstellungen von shopaccount.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (4, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, shopaccount.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in shopaccount.ini

Einstellungen von `settings/shopaccount.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/shopaccount.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## HandlerSettings Repositories

Ausgeliefert: eine Liste mit 1: `kernel/classes`. [Die Einstellung](../ini-einstellungen/shopaccount/HandlerSettings.md#repositories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/shopaccount.ini.append.php
[HandlerSettings]
Repositories[]=expExampleRepositorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## HandlerSettings ExtensionRepositories

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/shopaccount/HandlerSettings.md#extensionrepositories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/shopaccount.ini.append.php
[HandlerSettings]
ExtensionRepositories[]=expExampleExtensionRepositorie    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AccountSettings Handler

Ausgeliefert: `ezuser`. [Die Einstellung](../ini-einstellungen/shopaccount/AccountSettings.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/shopaccount.ini.append.php
[AccountSettings]
Handler=ezuser    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ConfirmOrderSettings Handler

Ausgeliefert: `ezdefault`. [Die Einstellung](../ini-einstellungen/shopaccount/ConfirmOrderSettings.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/shopaccount.ini.append.php
[ConfirmOrderSettings]
Handler=ezdefault    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von shopaccount.ini](../ini-einstellungen/shopaccount/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shopaccount.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
