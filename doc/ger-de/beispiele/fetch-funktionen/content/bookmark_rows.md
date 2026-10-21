---
title: fetch( 'content', 'bookmark_rows' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion bookmark_rows des Moduls content: 1 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, bookmark_rows, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'bookmark_rows' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchBookmarkRows()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `folder_id` | integer | nein | `0` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'bookmark_rows', hash( 'folder_id', 0 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'bookmark_rows', array( 'folder_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/standard/templates/content/bookmark_tree.tpl` Zeile 18:

```tpl
{set $tree_rows = fetch( 'content', 'bookmark_rows', hash() )}
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
