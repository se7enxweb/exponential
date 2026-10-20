---
title: Template-Operator is_string
audience: developer, admin
category: examples
summary: "Der Template-Operator is_string: keine benannten Parameter, registriert von eZTemplateTypeOperator."
keywords: Template-Operator, Operator, is_string, template operator, operator, tpl, eZTemplateTypeOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator is_string

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateTypeOperator` (`lib/eztemplate/classes/eztemplatetypeoperator.php`).
Dieselbe Klasse registriert auch: [is_array](is_array.md), [is_boolean](is_boolean.md), [is_integer](is_integer.md), [is_float](is_float.md), [is_numeric](is_numeric.md), [is_object](is_object.md), [is_class](is_class.md), [is_null](is_null.md), [is_set](is_set.md), [is_unset](is_unset.md), [get_type](get_type.md), [get_class](get_class.md).

## Parameter

Keine.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|is_string}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/override/templates/window_controls_user.tpl` Zeile 10:

```tpl
{if $admin_navigation_content_pref|is_string}{set $admin_navigation_content = $admin_navigation_content_pref}{/if}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatetypeoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
