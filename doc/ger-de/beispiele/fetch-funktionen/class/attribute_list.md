---
title: fetch( 'class', 'attribute_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion attribute_list des Moduls class: 1 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, class, attribute_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'class', 'attribute_list' )

Definiert in `kernel/class/function_definition.php`; sie ruft `eZClassFunctionCollection::fetchClassAttributeList()` auf (`kernel/class/ezclassfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `class_id` | integer | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'class', 'attribute_list', hash( 'class_id', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'class', 'attribute_list', array( 'class_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/workflow/eventtype/edit/event_ezwaituntildate.tpl` Zeile 76:

```tpl
{foreach fetch( 'class', 'attribute_list', hash( 'class_id', $class.id ) ) as $attribute}
```

## Siehe auch

- [Alle Fetch-Funktionen von class](README.md)
- [Die Ansichten des Moduls class](../../modulansichten/class/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/class/function_definition.php`, `kernel/class/ezclassfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
