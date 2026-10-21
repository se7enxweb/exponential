---
title: fetch( 'collaboration', 'participant_map' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion participant_map des Moduls collaboration: 5 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, collaboration, participant_map, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'collaboration', 'participant_map' )

Definiert in `kernel/collaboration/function_definition.php`; sie ruft `eZCollaborationFunctionCollection::fetchParticipantMap()` auf (`kernel/collaboration/ezcollaborationfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `item_id` |  | nein | `false` |
| `sort_by` |  | nein | `false` |
| `offset` |  | nein | `false` |
| `limit` |  | nein | `false` |
| `field` |  | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'collaboration', 'participant_map', hash( 'item_id', $item_id ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'collaboration', 'participant_map', hash( 'item_id', $item_id,
                                                        'sort_by', $sort_by,
                                                        'offset', $offset,
                                                        'limit', $limit,
                                                        'field', $field ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'collaboration', 'participant_map', array( 'item_id' => $item_id ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von collaboration](README.md)
- [Die Ansichten des Moduls collaboration](../../modulansichten/collaboration/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/collaboration/function_definition.php`, `kernel/collaboration/ezcollaborationfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
