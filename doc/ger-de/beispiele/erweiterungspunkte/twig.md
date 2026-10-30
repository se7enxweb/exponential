---
title: Erweiterungspunkte in twig.ini
audience: developer, admin
category: examples
summary: Einstellungen von twig.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (1, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, twig.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in twig.ini

Einstellungen von `settings/twig.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/twig.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## TwigSettings SecurityAllowedFilterList

Ausgeliefert: eine Liste mit 31: `abs`, `batch`, `capitalize`, `column`, `date`, `default`, `e`, `escape`, `first`, `format`, `join`, `json_encode` .... [Die Einstellung](../ini-einstellungen/twig/TwigSettings.md#securityallowedfilterlist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/twig.ini.append.php
[TwigSettings]
SecurityAllowedFilterList[]=expExampleSecurityAllowedFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von twig.ini](../ini-einstellungen/twig/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/twig.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
