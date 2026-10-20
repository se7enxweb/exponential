---
title: fetch( 'content', 'class' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion class des Moduls content: 1 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, content, class, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'class' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchClass()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `class_id` | integer,string | ja |  |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'class', hash( 'class_id', $class_id ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'class', array( 'class_id' => $class_id ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
