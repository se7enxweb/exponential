---
title: fetch( 'content', 'keyword' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion keyword des Moduls content: 10 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, keyword, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'keyword' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchKeyword()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `alphabet` | string | ja |  |
| `classid` | mixed | nein | `false` |
| `offset` | integer | nein | `false` |
| `limit` | integer | nein | `false` |
| `owner` | integer | nein | `false` |
| `sort_by` | array | nein | `array()` |
| `parent_node_id` | integer | nein | `false` |
| `include_duplicates` | bool | nein | `true` |
| `strict_matching` | bool | nein | `false` |
| `depth` | integer | nein | `1` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'keyword', hash( 'alphabet', 'alphabet' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'keyword', hash( 'alphabet', 'alphabet',
                                          'classid', $classid,
                                          'offset', 1,
                                          'limit', 1,
                                          'owner', 1,
                                          'sort_by', array(),
                                          'parent_node_id', 1,
                                          'include_duplicates', true(),
                                          'strict_matching', true(),
                                          'depth', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'keyword', array( 'alphabet' => 'alphabet' ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/standard/templates/content/keyword.tpl` Zeile 9:

```tpl
{section name=KeywordList loop=fetch( 'content', 'keyword', hash( alphabet, $alphabet, limit, $page_limit, offset, $view_parameters.offset, classid, $view_parameters.classid ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
