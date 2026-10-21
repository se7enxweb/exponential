---
title: fetch( 'content', 'draft_count' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion draft_count des Moduls content: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, content, draft_count, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'content', 'draft_count' )

Definiert in `kernel/content/function_definition.php`; sie ruft `eZContentFunctionCollection::fetchDraftVersionCount()` auf (`kernel/content/ezcontentfunctioncollection.php`).
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'content', 'draft_count' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'content', 'draft_count', array() );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/standard/templates/content/bookmark.tpl` Zeile 4:

```tpl
{*     list_count=fetch('content','draft_count')} *}
```

## Siehe auch

- [Alle Fetch-Funktionen von content](README.md)
- [Die Ansichten des Moduls content](../../modulansichten/content/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/function_definition.php`, `kernel/content/ezcontentfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
