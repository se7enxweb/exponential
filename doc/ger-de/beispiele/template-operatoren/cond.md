---
title: Template-Operator cond
audience: developer, admin
category: examples
summary: "Der Template-Operator cond: keine benannten Parameter, registriert von eZTemplateControlOperator."
keywords: Template-Operator, Operator, cond, template operator, operator, tpl, eZTemplateControlOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator cond

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateControlOperator` (`lib/eztemplate/classes/eztemplatecontroloperator.php`).
Dieselbe Klasse registriert auch: [first_set](first_set.md).

## Parameter

Keine.

Der Operator nimmt keine Eingabe: Er wird für sich aufgerufen, nicht nach `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{cond}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/override/templates/embed-inline_image.tpl` Zeile 9:

```tpl
{cond( $attribute_parameters.align, concat( ' class="embed-inline-', $attribute_parameters.align, '"' ), '' )} />
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatecontroloperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
