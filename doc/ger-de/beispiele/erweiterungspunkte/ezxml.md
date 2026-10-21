---
title: Erweiterungspunkte in ezxml.ini
audience: developer, admin
category: examples
summary: Einstellungen von ezxml.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (4, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, ezxml.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in ezxml.ini

Einstellungen von `settings/ezxml.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/ezxml.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## InputSettings HandlerClass

Ausgeliefert: `eZSimplifiedXMLInput`. [Die Einstellung](../ini-einstellungen/ezxml/InputSettings.md#handlerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ezxml.ini.append.php
[InputSettings]
HandlerClass=eZSimplifiedXMLInput    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## InputSettings AliasClasses

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/ezxml/InputSettings.md#aliasclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ezxml.ini.append.php
[InputSettings]
AliasClasses[]=expExampleAliasClasse    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## OutputSettings HandlerClass

Ausgeliefert: `eZXHTMLXMLOutput`. [Die Einstellung](../ini-einstellungen/ezxml/OutputSettings.md#handlerclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ezxml.ini.append.php
[OutputSettings]
HandlerClass=eZXHTMLXMLOutput    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## OutputSettings AliasClasses

Ausgeliefert: ein Hash mit 1: `ezpdf=eZPDFXMLOutput`. [Die Einstellung](../ini-einstellungen/ezxml/OutputSettings.md#aliasclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ezxml.ini.append.php
[OutputSettings]
AliasClasses[expexample]=expExampleAliasClasse    # Schlüssel und Wert wählen Sie
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von ezxml.ini](../ini-einstellungen/ezxml/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/ezxml.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
