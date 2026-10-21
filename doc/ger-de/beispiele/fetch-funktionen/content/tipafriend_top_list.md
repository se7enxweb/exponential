---
title: fetch( 'content', 'tipafriend_top_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion tipafriend_top_list des Moduls content: 7 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, tipafriend_top_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'tipafriend_top_list' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchTipafriendTopList()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `offset` | integer | nein | `false` |
| `limit` | integer | nein | `false` |
| `start_time` | integer | nein | `false` |
| `end_time` | integer | nein | `false` |
| `duration` | integer | nein | `false` |
| `ascending` | boolean | nein | `false` |
| `extended` | boolean | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'tipafriend_top_list', hash( 'offset', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'tipafriend_top_list', hash( 'offset', 1,
                                                      'limit', 1,
                                                      'start_time', 1,
                                                      'end_time', 1,
                                                      'duration', 1,
                                                      'ascending', true(),
                                                      'extended', true() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'tipafriend_top_list', array( 'offset' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
