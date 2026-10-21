---
title: fetch( 'notification', 'digest_handlers' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion digest_handlers des Moduls notification: 2 Parameter, 2 erforderlich."
keywords: Fetch-Funktion, fetch, notification, digest_handlers, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'notification', 'digest_handlers' )

Definiert in `kernel/notification/function_definition.php`; sie ruft `eZNotificationFunctionCollection::digestHandlerList()` auf (`kernel/notification/eznotificationfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `date` | integer | ja |  |
| `address` | string | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'notification', 'digest_handlers', hash( 'date', 1,
                                                       'address', 'address' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'notification', 'digest_handlers', array( 'date' => 1, 'address' => 'address' ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/base/templates/notification/handler/ezgeneraldigest/view/plain.tpl` Zeile 12:

```tpl
{foreach fetch( 'notification', 'digest_handlers', hash( 'date', $date.timestamp, 'address', $address ) ) as $digest_handler}
```

## Siehe auch

- [Alle Fetch-Funktionen von notification](README.md)
- [Die Ansichten des Moduls notification](../../modulansichten/notification/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/notification/function_definition.php`, `kernel/notification/eznotificationfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
