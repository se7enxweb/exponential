---
title: fetch( 'content', 'class_attribute' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion class_attribute des Moduls content: 2 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, class_attribute, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'class_attribute' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchClassAttribute()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `attribute_id` | integer | ja |  |
| `version_id` | integer | nein | `0` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'class_attribute', hash( 'attribute_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'content', 'class_attribute', hash( 'attribute_id', 1,
                                                  'version_id', 0 ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'class_attribute', array( 'attribute_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/relations.tpl` Zeile 27:

```tpl
{set $attr = fetch( 'content', 'class_attribute', hash( 'attribute_id', $attribute_id ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
