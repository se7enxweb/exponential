---
title: fetch( 'notification', 'handler_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion handler_list des Moduls notification: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, notification, handler_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'notification', 'handler_list' )

Definiert in `kernel/notification/function_definition.php`; sie ruft `eZNotificationFunctionCollection::handlerList()` auf (`kernel/notification/eznotificationfunctioncollection.php`).
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'notification', 'handler_list' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'notification', 'handler_list', array() );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/base/templates/notification/settings.tpl` Zeile 9:

```tpl
{let handlers=fetch('notification','handler_list')}
```

## Siehe auch

- [Alle Fetch-Funktionen von notification](README.md)
- [Die Ansichten des Moduls notification](../../modulansichten/notification/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/notification/function_definition.php`, `kernel/notification/eznotificationfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
