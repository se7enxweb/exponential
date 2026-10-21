---
title: Erweiterungspunkte in requestrules.ini
audience: developer, admin
category: examples
summary: Einstellungen von requestrules.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (4, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, requestrules.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in requestrules.ini

Einstellungen von `settings/requestrules.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/requestrules.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## RequestRuleSettings RuleProviders

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/requestrules/RequestRuleSettings.md#ruleproviders).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/requestrules.ini.append.php
[RequestRuleSettings]
RuleProviders[]=expExampleRuleProvider    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## RequestRuleSettings FactProviders

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/requestrules/RequestRuleSettings.md#factproviders).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/requestrules.ini.append.php
[RequestRuleSettings]
FactProviders[]=expExampleFactProvider    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## RequestRuleSettings ConditionHandlers

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/requestrules/RequestRuleSettings.md#conditionhandlers).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/requestrules.ini.append.php
[RequestRuleSettings]
ConditionHandlers[]=expExampleConditionHandler    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## RequestRuleSettings ActionHandlers

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/requestrules/RequestRuleSettings.md#actionhandlers).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/requestrules.ini.append.php
[RequestRuleSettings]
ActionHandlers[]=expExampleActionHandler    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von requestrules.ini](../ini-einstellungen/requestrules/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/requestrules.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
