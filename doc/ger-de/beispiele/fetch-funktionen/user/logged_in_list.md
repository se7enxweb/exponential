---
title: fetch( 'user', 'logged_in_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion logged_in_list des Moduls user: 3 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, user, logged_in_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'user', 'logged_in_list' )

Definiert in `kernel/user/function_definition.php`; sie ruft `eZUserFunctionCollection::fetchLoggedInList()` auf (`kernel/user/ezuserfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `sort_by` | mixed | nein |  |
| `offset` | integer | nein |  |
| `limit` | integer | nein |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'user', 'logged_in_list', hash( 'sort_by', $sort_by ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'user', 'logged_in_list', hash( 'sort_by', $sort_by,
                                              'offset', 1,
                                              'limit', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'user', 'logged_in_list', array( 'sort_by' => $sort_by ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von user](README.md)
- [Die Ansichten des Moduls user](../../modulansichten/user/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/function_definition.php`, `kernel/user/ezuserfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
