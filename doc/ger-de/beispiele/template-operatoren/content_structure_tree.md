---
title: Template-Operator content_structure_tree
audience: developer, admin
category: examples
summary: "Der Template-Operator content_structure_tree: 7 Parameter, registriert von eZContentStructureTreeOperator."
keywords: Template-Operator, Operator, content_structure_tree, template operator, operator, tpl, eZContentStructureTreeOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator content_structure_tree

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZContentStructureTreeOperator` (`kernel/common/ezcontentstructuretreeoperator.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `root_node_id` | int | ja | `0` |
| `class_filter` | array | nein | `false` |
| `max_depth` | int | nein | `0` |
| `max_nodes` | int | nein | `0` |
| `sort_by` | array | nein | `'false'` |
| `fetch_hidden` | bool | nein | `'false'` |
| `unfold_node_id` | int | nein | `0` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|content_structure_tree( 0, array(), 0, 0, 'false', 'false', 0 )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezcontentstructuretreeoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
