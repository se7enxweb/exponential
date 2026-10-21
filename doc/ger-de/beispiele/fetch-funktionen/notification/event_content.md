---
title: fetch( 'notification', 'event_content' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion event_content des Moduls notification: 1 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, notification, event_content, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'notification', 'event_content' )

Definiert in `kernel/notification/function_definition.php`; sie ruft `eZNotificationFunctionCollection::eventContent()` auf (`kernel/notification/eznotificationfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `event_id` | integer | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'notification', 'event_content', hash( 'event_id', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'notification', 'event_content', array( 'event_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/standard/templates/notification/handler/ezcollaboration/view/digest_element_plain.tpl` Zeile 3:

```tpl
{def $collaboration_item = fetch( 'notification', 'event_content', hash( 'event_id', $collection_item.event_id ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von notification](README.md)
- [Die Ansichten des Moduls notification](../../modulansichten/notification/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/notification/function_definition.php`, `kernel/notification/eznotificationfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
