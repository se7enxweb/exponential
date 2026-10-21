---
title: fetch( 'role', 'policy_count' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion policy_count des Moduls role: 1 Parameter, 1 erforderlich."
keywords: Rolle, Fetch-Funktion, fetch, role, policy_count, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'role', 'policy_count' )

Definiert in `kernel/role/function_definition.php`; sie ruft `eZRoleFunctionCollection::fetchRolePolicyCount()` auf (`kernel/role/ezrolefunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `role_id` | integer | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'role', 'policy_count', hash( 'role_id', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'role', 'policy_count', array( 'role_id' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von role](README.md)
- [Die Ansichten des Moduls role](../../modulansichten/role/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/role/function_definition.php`, `kernel/role/ezrolefunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
