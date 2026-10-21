---
title: fetch( 'package', 'item' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion item des Moduls package: 2 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, package, item, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'package', 'item' )

Definiert in `kernel/package/function_definition.php`; sie ruft `eZPackageFunctionCollection::fetchPackage()` auf (`kernel/package/ezpackagefunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `package_name` | string | ja |  |
| `repository_id` | string | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'package', 'item', hash( 'package_name', 'package_name' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'package', 'item', hash( 'package_name', 'package_name',
                                       'repository_id', 'repository_id' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'package', 'item', array( 'package_name' => 'package_name' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von package](README.md)
- [Die Ansichten des Moduls package](../../modulansichten/package/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/function_definition.php`, `kernel/package/ezpackagefunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
