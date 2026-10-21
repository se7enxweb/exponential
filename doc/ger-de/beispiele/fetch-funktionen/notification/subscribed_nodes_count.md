---
title: fetch( 'notification', 'subscribed_nodes_count' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion subscribed_nodes_count des Moduls notification: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, notification, subscribed_nodes_count, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'notification', 'subscribed_nodes_count' )

Definiert in `kernel/notification/function_definition.php`; sie ruft `eZNotificationFunctionCollection::subscribedNodesCount()` auf (`kernel/notification/eznotificationfunctioncollection.php`).
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'notification', 'subscribed_nodes_count' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'notification', 'subscribed_nodes_count', array() );
```

## Siehe auch

- [Alle Fetch-Funktionen von notification](README.md)
- [Die Ansichten des Moduls notification](../../modulansichten/notification/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/notification/function_definition.php`, `kernel/notification/eznotificationfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
