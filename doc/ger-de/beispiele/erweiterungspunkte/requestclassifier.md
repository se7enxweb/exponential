---
title: Erweiterungspunkte in requestclassifier.ini
audience: developer, admin
category: examples
summary: Einstellungen von requestclassifier.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (6, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, requestclassifier.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in requestclassifier.ini

Einstellungen von `settings/requestclassifier.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/requestclassifier.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## ClassifierSettings Listeners

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/requestclassifier/ClassifierSettings.md#listeners).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/requestclassifier.ini.append.php
[ClassifierSettings]
Listeners[]=expExampleListener    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## RuleSet-probes Class

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/requestclassifier/RuleSet-probes.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/requestclassifier.ini.append.php
[RuleSet-probes]
Class=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## RuleSet-traversal Class

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/requestclassifier/RuleSet-traversal.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/requestclassifier.ini.append.php
[RuleSet-traversal]
Class=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## RuleSet-injection Class

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/requestclassifier/RuleSet-injection.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/requestclassifier.ini.append.php
[RuleSet-injection]
Class=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## RuleSet-scanner Class

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/requestclassifier/RuleSet-scanner.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/requestclassifier.ini.append.php
[RuleSet-scanner]
Class=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## RuleSet-threshold Class

Ausgeliefert: leer. [Die Einstellung](../ini-einstellungen/requestclassifier/RuleSet-threshold.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/requestclassifier.ini.append.php
[RuleSet-threshold]
Class=    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von requestclassifier.ini](../ini-einstellungen/requestclassifier/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/requestclassifier.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
