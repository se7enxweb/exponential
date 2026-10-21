---
title: Erweiterungspunkte in template.ini
audience: developer, admin
category: examples
summary: Einstellungen von template.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (2, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, template.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in template.ini

Einstellungen von `settings/template.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/template.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## AttributeOperator DefaultFormatter

Ausgeliefert: `html`. [Die Einstellung](../ini-einstellungen/template/AttributeOperator.md#defaultformatter).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/template.ini.append.php
[AttributeOperator]
DefaultFormatter=html    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## AttributeOperator OutputFormatter

Ausgeliefert: ein Hash mit 2: `html=ezpAttributeOperatorHTMLFormatter`, `text=ezpAttributeOperatorTextFormatter`. [Die Einstellung](../ini-einstellungen/template/AttributeOperator.md#outputformatter).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/template.ini.append.php
[AttributeOperator]
OutputFormatter[expexample]=expExampleOutputFormatter    # Schlüssel und Wert wählen Sie
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von template.ini](../ini-einstellungen/template/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/template.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
