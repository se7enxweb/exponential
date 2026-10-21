---
title: fetch( 'collaboration', 'inbox' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion inbox des Moduls collaboration: 6 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, collaboration, inbox, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'collaboration', 'inbox' )

Definiert in `kernel/collaboration/function_definition.php`; sie ruft `eZCollaborationFunctionCollection::fetchInbox()` auf (`kernel/collaboration/ezcollaborationfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `status` |  | nein | `'all'` |
| `role` |  | nein | `'all'` |
| `type` |  | nein | `'all'` |
| `group_id` |  | nein | `0` |
| `offset` |  | nein | `0` |
| `limit` |  | nein | `10` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'collaboration', 'inbox', hash( 'status', 'all' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'collaboration', 'inbox', hash( 'status', 'all',
                                              'role', 'all',
                                              'type', 'all',
                                              'group_id', 0,
                                              'offset', 0,
                                              'limit', 10 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'collaboration', 'inbox', array( 'status' => $status ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin4/templates/parts/my/menu.tpl` Zeile 21:

```tpl
{def $cb_counts = fetch( 'collaboration', 'inbox', hash( 'limit', 1 ) ).counts}
```

## Siehe auch

- [Alle Fetch-Funktionen von collaboration](README.md)
- [Die Ansichten des Moduls collaboration](../../modulansichten/collaboration/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/collaboration/function_definition.php`, `kernel/collaboration/ezcollaborationfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
