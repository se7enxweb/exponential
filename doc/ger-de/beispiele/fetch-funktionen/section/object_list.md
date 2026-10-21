---
title: fetch( 'section', 'object_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion object_list des Moduls section: 5 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, section, object_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'section', 'object_list' )

Definiert in `kernel/section/function_definition.php`; sie ruft `eZSectionFunctionCollection::fetchObjectList()` auf (`kernel/section/ezsectionfunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `section_id` | integer | ja |  |
| `offset` | integer | nein | `false` |
| `limit` | integer | nein | `false` |
| `sort_order` | variant | nein | `false` |
| `status` | string | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'section', 'object_list', hash( 'section_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'section', 'object_list', hash( 'section_id', 1,
                                              'offset', 1,
                                              'limit', 1,
                                              'sort_order', $sort_order,
                                              'status', 'status' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'section', 'object_list', array( 'section_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/section/view.tpl` Zeile 179:

```tpl
{foreach fetch( 'section', 'object_list', hash( 'section_id', $section.id, 'limit', $number_of_items, 'offset', $view_parameters.offset ) ) as $object}
```

## Siehe auch

- [Alle Fetch-Funktionen von section](README.md)
- [Die Ansichten des Moduls section](../../modulansichten/section/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/section/function_definition.php`, `kernel/section/ezsectionfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
