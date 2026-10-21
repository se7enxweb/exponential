---
title: fetch( 'user', 'deactivation_offers' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion deactivation_offers des Moduls user: 1 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, user, deactivation_offers, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'user', 'deactivation_offers' )

Definiert in `kernel/user/function_definition.php`; sie ruft `eZUserFunctionCollection::fetchDeactivationOffers()` auf (`kernel/user/ezuserfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `object_id` | integer | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'user', 'deactivation_offers', hash( 'object_id', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'user', 'deactivation_offers', array( 'object_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/node/view/full.tpl` Zeile 150:

```tpl
{def $deactivation_offers = fetch( 'user', 'deactivation_offers', hash( 'object_id', $node.contentobject_id ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von user](README.md)
- [Die Ansichten des Moduls user](../../modulansichten/user/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/function_definition.php`, `kernel/user/ezuserfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
