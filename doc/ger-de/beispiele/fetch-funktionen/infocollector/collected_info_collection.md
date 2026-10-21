---
title: fetch( 'infocollector', 'collected_info_collection' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion collected_info_collection des Moduls infocollector: 2 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, infocollector, collected_info_collection, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'infocollector', 'collected_info_collection' )

Definiert in `kernel/infocollector/function_definition.php`; sie ruft `eZInfocollectorFunctionCollection::fetchCollectedInfoCollection()` auf (`kernel/infocollector/ezinfocollectorfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `collection_id` | integer | nein | `false` |
| `contentobject_id` | integer | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'infocollector', 'collected_info_collection', hash( 'collection_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'infocollector', 'collected_info_collection', hash( 'collection_id', 1,
                                                                  'contentobject_id', 1 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'infocollector', 'collected_info_collection', array( 'collection_id' => 1 ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von infocollector](README.md)
- [Die Ansichten des Moduls infocollector](../../modulansichten/infocollector/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/infocollector/function_definition.php`, `kernel/infocollector/ezinfocollectorfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
