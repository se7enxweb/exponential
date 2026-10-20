---
title: Template-Operator gt
audience: developer, admin
category: examples
summary: "Der Template-Operator gt: 1 Parameter, registriert von eZTemplateLogicOperator."
keywords: Template-Operator, Operator, gt, template operator, operator, tpl, eZTemplateLogicOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator gt

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateLogicOperator` (`lib/eztemplate/classes/eztemplatelogicoperator.php`).
Dieselbe Klasse registriert auch: [lt](lt.md), [le](le.md), [ge](ge.md), [eq](eq.md), [ne](ne.md), [null](null.md), [not](not.md), [true](true.md), [false](false.md), [or](or.md), [and](and.md), [choose](choose.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `threshold` | mixed | ja | `false` |

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|gt( $threshold )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/override/templates/admin_preview/flash.tpl` Zeile 15:

```tpl
{if $attribute.content.width|gt( 0 )}width="{$attribute.content.width}"{/if} {if $attribute.content.height|gt( 0 )}height="{$attribute.content.height}"{/if} id="objectid{$node.object.id}">
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatelogicoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
