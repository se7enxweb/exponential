---
title: fetch( 'role', 'policies' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion policies des Moduls role: 3 Parameter, 1 erforderlich."
keywords: Rolle, Richtlinien, Fetch-Funktion, fetch, role, policies, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'role', 'policies' )

Definiert in `kernel/role/function_definition.php`; sie ruft `eZRoleFunctionCollection::fetchRolePolicies()` auf (`kernel/role/ezrolefunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `role_id` | integer | ja |  |
| `offset` | integer | nein | `0` |
| `limit` | integer | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'role', 'policies', hash( 'role_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'role', 'policies', hash( 'role_id', 1,
                                        'offset', 0,
                                        'limit', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'role', 'policies', array( 'role_id' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von role](README.md)
- [Die Ansichten des Moduls role](../../modulansichten/role/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/role/function_definition.php`, `kernel/role/ezrolefunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
