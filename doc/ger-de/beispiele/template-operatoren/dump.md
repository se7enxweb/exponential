---
title: Template-Operator dump
audience: developer, admin
category: examples
summary: "Der Template-Operator dump: 3 Parameter, registriert von eZTemplateAttributeOperator."
keywords: Template-Operator, Operator, dump, template operator, operator, tpl, eZTemplateAttributeOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator dump

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateAttributeOperator` (`lib/eztemplate/classes/eztemplateattributeoperator.php`).
Dieselbe Klasse registriert auch: [attribute](attribute.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `show_values` | string | nein | `'show'` |
| `max_val` | numerical | nein | `1` |
| `format` | boolean | nein | `'html'` |

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|dump( 'show', 1, 'html' )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateattributeoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
