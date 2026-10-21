---
title: Template-Operator ezoe_ini_section
audience: developer, admin
category: examples
summary: "Der Template-Operator ezoe_ini_section: 2 Parameter, registriert von eZOETemplateUtils."
keywords: Template-Operator, Operator, ezoe_ini_section, template operator, operator, tpl, eZOETemplateUtils
type: generated
generated: exp:help:examples
---
# Der Template-Operator ezoe_ini_section

Registriert in `extension/ezoe/autoloads/eztemplateautoload.php` von der Klasse `eZOETemplateUtils` (`extension/ezoe/autoloads/ezoetemplateutils.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `section` | string | ja | `''` |
| `file` | string | nein | `'site.ini'` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|ezoe_ini_section( 'section', 'site.ini' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `extension/ezoe/design/standard/templates/ezoe/customattributes.tpl` Zeile 48:

```tpl
{if ezoe_ini_section( concat('CustomAttribute_', $:tag_name, '_', $custom_attribute), 'ezoe_attributes.ini' )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezoe/autoloads/eztemplateautoload.php`, `extension/ezoe/autoloads/ezoetemplateutils.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
