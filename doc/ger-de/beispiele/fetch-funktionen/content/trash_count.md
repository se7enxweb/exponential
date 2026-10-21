---
title: fetch( 'content', 'trash_count' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion trash_count des Moduls content: 2 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, trash_count, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'trash_count' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchTrashObjectCount()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `objectname_filter` | string | nein | `null` |
| `attribute_filter` | mixed | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'trash_count', hash( 'objectname_filter', 'objectname_filter' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'trash_count', hash( 'objectname_filter', 'objectname_filter',
                                              'attribute_filter', $attribute_filter ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'trash_count', array( 'objectname_filter' => 'objectname_filter' ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/content/trash.tpl` Zeile 28:

```tpl
{set $list_count = fetch( 'content', 'trash_count', hash( 'objectname_filter', $view_parameters.namefilter ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
