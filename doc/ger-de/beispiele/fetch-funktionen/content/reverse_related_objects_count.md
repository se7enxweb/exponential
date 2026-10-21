---
title: fetch( 'content', 'reverse_related_objects_count' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion reverse_related_objects_count des Moduls content: 4 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, reverse_related_objects_count, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'reverse_related_objects_count' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchReverseRelatedObjectsCount()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `object_id` | integer | ja |  |
| `attribute_identifier` | string | nein |  |
| `all_relations` | boolean | nein | `false` |
| `ignore_visibility` | boolean | nein | `null` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'reverse_related_objects_count', hash( 'object_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'reverse_related_objects_count', hash( 'object_id', 1,
                                                                'attribute_identifier', 'attribute_identifier',
                                                                'all_relations', true(),
                                                                'ignore_visibility', true() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'reverse_related_objects_count', array( 'object_id' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
