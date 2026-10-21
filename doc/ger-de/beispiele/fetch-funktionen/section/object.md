---
title: fetch( 'section', 'object' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion object des Moduls section: 2 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, section, object, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'section', 'object' )

Definiert in `kernel/section/function_definition.php`; sie ruft `eZSectionFunctionCollection::fetchSectionObject()` auf (`kernel/section/ezsectionfunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `section_id` | integer | nein | `false` |
| `identifier` | string | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'section', 'object', hash( 'section_id', 1 ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'section', 'object', hash( 'section_id', 1,
                                         'identifier', 'identifier' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'section', 'object', array( 'section_id' => 1 ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/section/browse_assign.tpl` Zeile 4:

```tpl
{def $section = fetch( 'section', 'object', hash( 'section_id', $browse.content.section_id ) )}
```

## Siehe auch

- [Alle Fetch-Funktionen von section](README.md)
- [Die Ansichten des Moduls section](../../modulansichten/section/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/section/function_definition.php`, `kernel/section/ezsectionfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
