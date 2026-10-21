---
title: fetch( 'content', 'same_classattribute_node' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion same_classattribute_node des Moduls content: 3 Parameter, 3 erforderlich."
keywords: Fetch-Funktion, fetch, content, same_classattribute_node, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'same_classattribute_node' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchSameClassAttributeNodeList()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `classattribute_id` | integer | ja |  |
| `value` | mixed | ja |  |
| `datatype` | string | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'same_classattribute_node', hash( 'classattribute_id', 1,
                                                           'value', $value,
                                                           'datatype', 'datatype' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'same_classattribute_node', array( 'classattribute_id' => 1, 'value' => $value, 'datatype' => 'datatype' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
