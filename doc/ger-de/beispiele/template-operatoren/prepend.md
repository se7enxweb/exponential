---
title: Template-Operator prepend
audience: developer, admin
category: examples
summary: "Der Template-Operator prepend: 1 Parameter, registriert von eZTemplateArrayOperator."
keywords: Template-Operator, Operator, prepend, template operator, operator, tpl, eZTemplateArrayOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator prepend

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateArrayOperator` (`lib/eztemplate/classes/eztemplatearrayoperator.php`).
Dieselbe Klasse registriert auch: [array](array.md), [hash](hash.md), [array_prepend](array_prepend.md), [array_append](array_append.md), [append](append.md), [array_merge](array_merge.md), [merge](merge.md), [contains](contains.md), [compare](compare.md), [extract](extract.md), [extract_left](extract_left.md), [extract_right](extract_right.md), [begins_with](begins_with.md), [ends_with](ends_with.md), [implode](implode.md), [explode](explode.md), [repeat](repeat.md), [reverse](reverse.md), [insert](insert.md), [remove](remove.md), [replace](replace.md), [unique](unique.md), [array_sum](array_sum.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `prepend_string` | string | nein | `false` |

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|prepend( 'prepend_string' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin3/templates/page_head_script.tpl` Zeile 15:

```tpl
{ezscript_load( ezini( 'JavaScriptSettings', 'BackendJavaScriptList', 'design.ini' )|merge( ezini( 'JavaScriptSettings', 'ClassicBackendJavaScriptList', 'design.ini' ) )|prepend( 'ezjsc::jquery', 'ezjsc::jqueryio', 'a4_url.js' ) )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatearrayoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
