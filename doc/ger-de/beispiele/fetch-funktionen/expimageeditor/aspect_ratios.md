---
title: fetch( 'expimageeditor', 'aspect_ratios' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion aspect_ratios des Moduls expimageeditor: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, expimageeditor, aspect_ratios, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'expimageeditor', 'aspect_ratios' )

Definiert in `extension/expimageeditor/modules/expimageeditor/function_definition.php`; sie ruft `expImageEditorFunctionCollection::fetchAspectRatios()` auf (`extension/expimageeditor/classes/expimageeditorfunctioncollection.php`).
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'expimageeditor', 'aspect_ratios' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'expimageeditor', 'aspect_ratios', array() );
```

## Siehe auch

- [Alle Fetch-Funktionen von expimageeditor](README.md)
- [Die Ansichten des Moduls expimageeditor](../../modulansichten/expimageeditor/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/expimageeditor/modules/expimageeditor/function_definition.php`, `extension/expimageeditor/classes/expimageeditorfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
