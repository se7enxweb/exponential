---
title: Template-Operator indent
audience: developer, admin
category: examples
summary: "Der Template-Operator indent: 3 Parameter, registriert von eZTemplateTextOperator."
keywords: Template-Operator, Operator, indent, template operator, operator, tpl, eZTemplateTextOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator indent

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateTextOperator` (`lib/eztemplate/classes/eztemplatetextoperator.php`).
Dieselbe Klasse registriert auch: [concat](concat.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `indent_count` | integer | ja | `false` |
| `indent_type` | identifier | nein | `'space'` |
| `indent_filler` | string | nein | `false` |

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|indent( 1, 'space', 'indent_filler' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/standard/templates/class/datatype/view/ezidentifier.tpl` Zeile 41:

```tpl
<p>{$class_attribute.data_text1|wash}{$class_attribute.data_int3|indent( $class_attribute.data_int2|sub( $class_attribute.data_int3|count ), 'custom', '0' )}{$class_attribute.data_text2|wash}</p>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatetextoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
