---
title: Template-Operator treemenu
audience: developer, admin
category: examples
summary: "Der Template-Operator treemenu: 9 Parameter, registriert von eZTreeMenuOperator."
keywords: Template-Operator, Operator, treemenu, template operator, operator, tpl, eZTreeMenuOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator treemenu

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZTreeMenuOperator` (`kernel/common/eztreemenuoperator.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `path` | array | ja | `false` |
| `node_id` | int | nein | `false` |
| `class_filter` | array | nein | `false` |
| `depth_skip` | int | nein | `false` |
| `max_level` | int | nein | `false` |
| `is_selected_method` | string | nein | `'tree'` |
| `indentation_level` | int | nein | `15` |
| `language` | string\|array | nein | `false` |
| `load_data_map` | boolean | nein | `null` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|treemenu( array(), 1, array(), 1, 1, 'tree', 15, $language, true() )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/eztreemenuoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
