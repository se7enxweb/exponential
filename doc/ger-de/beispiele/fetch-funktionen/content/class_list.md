---
title: fetch( 'content', 'class_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion class_list des Moduls content: 4 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, class_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'class_list' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchClassList()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `as_object` | bool | nein | `true` |
| `group_list` | array | nein | `false` |
| `sorts` | array | nein | `null` |
| `limit` | array | nein | `null` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'class_list', hash( 'as_object', true() ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'class_list', hash( 'as_object', true(),
                                             'group_list', array(),
                                             'sorts', array(),
                                             'limit', array() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'class_list', array( 'as_object' => true ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/visual/templatecreate.tpl` Zeile 136:

```tpl
{section name=Class loop=fetch('content', 'class_list', hash('sorts', array()))}
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
