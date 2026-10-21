---
title: fetch( 'content', 'object_count_by_user_id' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion object_count_by_user_id des Moduls content: 3 Parameter, 2 erforderlich."
keywords: Fetch-Funktion, fetch, content, object_count_by_user_id, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'object_count_by_user_id' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchObjectCountByUserID()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `class_id` | integer | ja | `false` |
| `user_id` | integer | ja | `false` |
| `status` | integer | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'object_count_by_user_id', hash( 'class_id', 1,
                                                          'user_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'object_count_by_user_id', hash( 'class_id', 1,
                                                          'user_id', 1,
                                                          'status', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'object_count_by_user_id', array( 'class_id' => 1, 'user_id' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
