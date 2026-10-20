---
title: Erweiterungspunkte in rest.ini
audience: developer, admin
category: examples
summary: Einstellungen von rest.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (8, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, rest.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in rest.ini

Einstellungen von `settings/rest.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/rest.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## System PrefixFilterClass

Ausgeliefert: `ezpRestDefaultRegexpPrefixFilter`. [Die Einstellung](../ini-einstellungen/rest/System.md#prefixfilterclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/rest.ini.append.php
[System]
PrefixFilterClass=ezpRestDefaultRegexpPrefixFilter    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ApiProvider ProviderClass

Ausgeliefert: ein Hash mit 1: `auth=ezpRestAuthProvider`. [Die Einstellung](../ini-einstellungen/rest/ApiProvider.md#providerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/rest.ini.append.php
[ApiProvider]
ProviderClass[expexample]=expExampleProviderClas    # Schlüssel und Wert wählen Sie
*/ ?>
```

## OutputSettings RendererClass

Ausgeliefert: ein Hash mit 1: `xhtml=ezpContentXHTMLRenderer`. [Die Einstellung](../ini-einstellungen/rest/OutputSettings.md#rendererclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/rest.ini.append.php
[OutputSettings]
RendererClass[expexample]=expExampleRendererClas    # Schlüssel und Wert wählen Sie
*/ ?>
```

## RouteSettings SkipFilter

Ausgeliefert: eine Liste mit 4: `ezpRestErrorController_show`, `ezpRestAuthController_basicAuth`, `ezpRestAuthController_oauthRequired`, `ezpRestOauthTokenController_handleRequest`. [Die Einstellung](../ini-einstellungen/rest/RouteSettings.md#skipfilter).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/rest.ini.append.php
[RouteSettings]
SkipFilter[]=expExampleSkipFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## PreRoutingFilters Filters

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/rest/PreRoutingFilters.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/rest.ini.append.php
[PreRoutingFilters]
Filters[]=expExampleFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## RequestFilters Filters

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/rest/RequestFilters.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/rest.ini.append.php
[RequestFilters]
Filters[]=expExampleFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## ResultFilters Filters

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/rest/ResultFilters.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/rest.ini.append.php
[ResultFilters]
Filters[]=expExampleFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## ResponseFilters Filters

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/rest/ResponseFilters.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/rest.ini.append.php
[ResponseFilters]
Filters[]=expExampleFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von rest.ini](../ini-einstellungen/rest/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rest.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
