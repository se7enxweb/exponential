---
title: Template-Operator array_append
audience: developer, admin
category: examples
summary: "Der Template-Operator array_append: keine benannten Parameter, registriert von eZTemplateArrayOperator."
keywords: Template-Operator, Operator, array_append, template operator, operator, tpl, eZTemplateArrayOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator array_append

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateArrayOperator` (`lib/eztemplate/classes/eztemplatearrayoperator.php`).
Dieselbe Klasse registriert auch: [array](array.md), [hash](hash.md), [array_prepend](array_prepend.md), [prepend](prepend.md), [append](append.md), [array_merge](array_merge.md), [merge](merge.md), [contains](contains.md), [compare](compare.md), [extract](extract.md), [extract_left](extract_left.md), [extract_right](extract_right.md), [begins_with](begins_with.md), [ends_with](ends_with.md), [implode](implode.md), [explode](explode.md), [repeat](repeat.md), [reverse](reverse.md), [insert](insert.md), [remove](remove.md), [replace](replace.md), [unique](unique.md), [array_sum](array_sum.md).

## Parameter

Keine.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|array_append}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatearrayoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
