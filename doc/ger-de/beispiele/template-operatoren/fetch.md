---
title: Template-Operator fetch
audience: developer, admin
category: examples
summary: "Der Template-Operator fetch: 3 Parameter, registriert von eZTemplateExecuteOperator."
keywords: Template-Operator, Operator, fetch, template operator, operator, tpl, eZTemplateExecuteOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator fetch

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateExecuteOperator` (`lib/eztemplate/classes/eztemplateexecuteoperator.php`).
Dieselbe Klasse registriert auch: [fetch_alias](fetch_alias.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `module_name` | string | ja | `false` |
| `function_name` | string | ja | `false` |
| `function_parameters` | array | nein | `array()` |

Der Operator nimmt keine Eingabe: Er wird für sich aufgerufen, nicht nach `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{fetch( 'module_name', 'function_name', array() )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/override/templates/admin_preview/company.tpl` Zeile 36:

```tpl
{node_view_gui view=line content_node=fetch( content,node,hash(node_id,$:item.node_id ) )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateexecuteoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
