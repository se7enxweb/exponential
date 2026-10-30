---
title: Erweiterungspunkte in graphql.ini
audience: developer, admin
category: examples
summary: Einstellungen von graphql.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (2, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, graphql.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in graphql.ini

Einstellungen von `settings/graphql.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/graphql.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## GraphQLSettings ClassList

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/graphql/GraphQLSettings.md#classlist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/graphql.ini.append.php
[GraphQLSettings]
ClassList[]=expExampleClass    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## GraphQLSettings ExcludedClassList

Ausgeliefert: eine Liste mit 4: `user`, `user_group`, `common_ini_settings`, `template_look`. [Die Einstellung](../ini-einstellungen/graphql/GraphQLSettings.md#excludedclasslist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/graphql.ini.append.php
[GraphQLSettings]
ExcludedClassList[]=expExampleExcludedClass    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von graphql.ini](../ini-einstellungen/graphql/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/graphql.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
