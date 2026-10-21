---
title: fetch( 'content', 'view_top_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion view_top_list des Moduls content: 4 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, view_top_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'view_top_list' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchMostViewedTopList()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `class_id` | integer | nein | `false` |
| `section_id` | integer | nein | `false` |
| `offset` | integer | nein | `false` |
| `limit` | integer | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'view_top_list', hash( 'class_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'view_top_list', hash( 'class_id', 1,
                                                'section_id', 1,
                                                'offset', 1,
                                                'limit', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'view_top_list', array( 'class_id' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
