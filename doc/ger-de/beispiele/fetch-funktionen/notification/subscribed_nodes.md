---
title: fetch( 'notification', 'subscribed_nodes' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion subscribed_nodes des Moduls notification: 2 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, notification, subscribed_nodes, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'notification', 'subscribed_nodes' )

Definiert in `kernel/notification/function_definition.php`; sie ruft `eZNotificationFunctionCollection::subscribedNodes()` auf (`kernel/notification/eznotificationfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `offset` | integer | nein | `false` |
| `limit` | integer | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'notification', 'subscribed_nodes', hash( 'offset', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'notification', 'subscribed_nodes', hash( 'offset', 1,
                                                        'limit', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'notification', 'subscribed_nodes', array( 'offset' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/notification/handler/ezsubtree/settings/edit.tpl` Zeile 51:

```tpl
{section var=Rules loop=fetch( 'notification', 'subscribed_nodes', hash( 'limit', $number_of_items, 'offset', $view_parameters.offset ) ) sequence=array( bglight, bgdark )}
```

## Siehe auch

- [Alle Fetch-Funktionen von notification](README.md)
- [Die Ansichten des Moduls notification](../../modulansichten/notification/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/notification/function_definition.php`, `kernel/notification/eznotificationfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
