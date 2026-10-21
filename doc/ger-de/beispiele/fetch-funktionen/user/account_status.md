---
title: fetch( 'user', 'account_status' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion account_status des Moduls user: 1 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, user, account_status, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'user', 'account_status' )

Definiert in `kernel/user/function_definition.php`; sie ruft `eZUserFunctionCollection::fetchAccountStatus()` auf (`kernel/user/ezuserfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `user_id` | integer | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'user', 'account_status', hash( 'user_id', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'user', 'account_status', array( 'user_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin4/templates/node/view/a4_full_body.tpl` Zeile 66:

```tpl
{def $a4_account_status = fetch( 'user', 'account_status', hash( 'user_id', $node.contentobject_id ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von user](README.md)
- [Die Ansichten des Moduls user](../../modulansichten/user/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/function_definition.php`, `kernel/user/ezuserfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
