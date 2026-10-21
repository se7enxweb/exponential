---
title: fetch( 'content', 'keyword_count' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion keyword_count des Moduls content: 7 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, keyword_count, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'keyword_count' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchKeywordCount()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `alphabet` | string | ja |  |
| `classid` | mixed | nein | `false` |
| `owner` | integer | nein | `false` |
| `parent_node_id` | integer | nein | `false` |
| `include_duplicates` | bool | nein | `true` |
| `strict_matching` | bool | nein | `false` |
| `depth` | integer | nein | `1` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'keyword_count', hash( 'alphabet', 'alphabet' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'keyword_count', hash( 'alphabet', 'alphabet',
                                                'classid', $classid,
                                                'owner', 1,
                                                'parent_node_id', 1,
                                                'include_duplicates', true(),
                                                'strict_matching', true(),
                                                'depth', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'keyword_count', array( 'alphabet' => 'alphabet' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
