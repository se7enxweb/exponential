---
title: Template-Operator concat
audience: developer, admin
category: examples
summary: "Der Template-Operator concat: keine benannten Parameter, registriert von eZTemplateTextOperator."
keywords: Template-Operator, Operator, concat, template operator, operator, tpl, eZTemplateTextOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator concat

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateTextOperator` (`lib/eztemplate/classes/eztemplatetextoperator.php`).
Dieselbe Klasse registriert auch: [indent](indent.md).

## Parameter

Keine.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|concat}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/override/templates/admin_preview/flash.tpl` Zeile 17:

```tpl
<param name="movie" value={concat("content/download/",$attribute.contentobject_id,"/",$attribute.content.contentobject_attribute_id,"/",$attribute.content.original_filename)|ezurl} />
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatetextoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
