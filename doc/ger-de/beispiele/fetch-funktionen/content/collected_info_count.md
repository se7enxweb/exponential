---
title: fetch( 'content', 'collected_info_count' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion collected_info_count des Moduls content: 5 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, collected_info_count, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'collected_info_count' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchCollectedInfoCount()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `object_attribute_id` | integer | nein | `false` |
| `object_id` | integer | nein | `false` |
| `value` | integer | nein | `false` |
| `creator_id` | integer | nein | `false` |
| `user_identifier` | string | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'collected_info_count', hash( 'object_attribute_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'collected_info_count', hash( 'object_attribute_id', 1,
                                                       'object_id', 1,
                                                       'value', 1,
                                                       'creator_id', 1,
                                                       'user_identifier', 'user_identifier' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'collected_info_count', array( 'object_attribute_id' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
