---
title: fetch( 'switchlanguage', 'url_alias' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion url_alias des Moduls switchlanguage: 3 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, switchlanguage, url_alias, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'switchlanguage', 'url_alias' )

Definiert in `kernel/private/modules/switchlanguage/function_definition.php`; sie ruft `ezpLanguageSwitcherFunctionCollection::fetchUrlAlias()` auf (`kernel/private/modules/switchlanguage/ezpLanguageSwitcherFunctionCollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `node_id` | integer | nein | `false` |
| `path` | string | nein | `false` |
| `locale` | string | ja | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'switchlanguage', 'url_alias', hash( 'locale', 'locale' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'switchlanguage', 'url_alias', hash( 'node_id', 1,
                                                   'path', 'path',
                                                   'locale', 'locale' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'switchlanguage', 'url_alias', array( 'locale' => 'locale' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von switchlanguage](README.md)
- [Die Ansichten des Moduls switchlanguage](../../modulansichten/switchlanguage/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/private/modules/switchlanguage/function_definition.php`, `kernel/private/modules/switchlanguage/ezpLanguageSwitcherFunctionCollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
