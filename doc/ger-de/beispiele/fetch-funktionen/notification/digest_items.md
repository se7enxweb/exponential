---
title: fetch( 'notification', 'digest_items' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion digest_items des Moduls notification: 3 Parameter, 3 erforderlich."
keywords: Fetch-Funktion, fetch, notification, digest_items, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'notification', 'digest_items' )

Definiert in `kernel/notification/function_definition.php`; sie ruft `eZNotificationFunctionCollection::digestItems()` auf (`kernel/notification/eznotificationfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `date` | integer | ja |  |
| `address` | string | ja |  |
| `handler` | string | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'notification', 'digest_items', hash( 'date', 1,
                                                    'address', 'address',
                                                    'handler', 'handler' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'notification', 'digest_items', array( 'date' => 1, 'address' => 'address', 'handler' => 'handler' ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/standard/templates/notification/handler/ezcollaboration/view/digest_plain.tpl` Zeile 4:

```tpl
{def $handler_items = fetch( 'notification', 'digest_items', hash( 'date', $date.timestamp, 'address', $address, 'handler', $handler.id_string ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von notification](README.md)
- [Die Ansichten des Moduls notification](../../modulansichten/notification/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/notification/function_definition.php`, `kernel/notification/eznotificationfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
