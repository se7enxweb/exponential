---
title: fetch( 'content', 'can_instantiate_class_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion can_instantiate_class_list des Moduls content: 6 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, can_instantiate_class_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'can_instantiate_class_list' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::canInstantiateClassList()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `group_id` | array | nein | `0` |
| `parent_node` | object | nein | `0` |
| `filter_type` | string | nein | `'include'` |
| `fetch_id` | string | nein | `false` |
| `as_object` | bool | nein | `true` |
| `group_by_class_group` | bool | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'can_instantiate_class_list', hash( 'group_id', 0 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'can_instantiate_class_list', hash( 'group_id', 0,
                                                             'parent_node', 0,
                                                             'filter_type', 'include',
                                                             'fetch_id', 'fetch_id',
                                                             'as_object', true(),
                                                             'group_by_class_group', true() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'can_instantiate_class_list', array( 'group_id' => array() ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/contentstructuremenu/content_structure_menu_dynamic.tpl` Zeile 59:

```tpl
*}{foreach fetch( 'content', 'can_instantiate_class_list', hash( 'parent_node', $root_node, 'filter_type', $filter_type, 'group_id', $filter_groups ) ) as $class}{*
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
