---
title: fetch( 'user', 'member_of' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion member_of des Moduls user: 1 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, user, member_of, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'user', 'member_of' )

Definiert in `kernel/user/function_definition.php`; sie ruft `eZUserFunctionCollection::fetchMemberOf()` auf (`kernel/user/ezuserfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `id` | integer | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'user', 'member_of', hash( 'id', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'user', 'member_of', array( 'id' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von user](README.md)
- [Die Ansichten des Moduls user](../../modulansichten/user/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/user/function_definition.php`, `kernel/user/ezuserfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
