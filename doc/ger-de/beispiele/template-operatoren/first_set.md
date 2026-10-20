---
title: Template-Operator first_set
audience: developer, admin
category: examples
summary: "Der Template-Operator first_set: keine benannten Parameter, registriert von eZTemplateControlOperator."
keywords: Template-Operator, Operator, first_set, template operator, operator, tpl, eZTemplateControlOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator first_set

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateControlOperator` (`lib/eztemplate/classes/eztemplatecontroloperator.php`).
Dieselbe Klasse registriert auch: [cond](cond.md).

## Parameter

Keine.

Der Operator nimmt keine Eingabe: Er wird für sich aufgerufen, nicht nach `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{first_set}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/audit/charts.tpl` Zeile 25:

```tpl
<input id="au-name" type="text" name="Name" list="au-names" placeholder="access.*" value="{first_set( $filters.name, '' )|wash}" />
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatecontroloperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
