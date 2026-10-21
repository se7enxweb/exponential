---
title: Template-Operator attribute
audience: developer, admin
category: examples
summary: "Der Template-Operator attribute: 3 Parameter, registriert von eZTemplateAttributeOperator."
keywords: Attribut, Template-Operator, Operator, attribute, template operator, operator, tpl, eZTemplateAttributeOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator attribute

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateAttributeOperator` (`lib/eztemplate/classes/eztemplateattributeoperator.php`).
Dieselbe Klasse registriert auch: [dump](dump.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `show_values` | string | nein | `''` |
| `max_val` | numerical | nein | `2` |
| `format` | boolean | nein | `'html'` |

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|attribute( 'show_values', 2, 'html' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/shop/dashboard.tpl` Zeile 240:

```tpl
<p class="a4-shop-note">{'A class is a product class when it has a price attribute (%types). Only its published objects can be added to the basket.'|i18n( $ctx, , hash( '%types', 'ezprice, ezmultiprice' ) )}</p>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateattributeoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
