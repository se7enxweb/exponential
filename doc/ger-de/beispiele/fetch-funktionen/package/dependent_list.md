---
title: fetch( 'package', 'dependent_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion dependent_list des Moduls package: 3 Parameter, 1 erforderlich."
keywords: Fetch-Funktion, fetch, package, dependent_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'package', 'dependent_list' )

Definiert in `kernel/package/function_definition.php`; sie ruft `eZPackageFunctionCollection::fetchDependentPackageList()` auf (`kernel/package/ezpackagefunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `package_name` | string | ja |  |
| `parameters` | array | nein | `false` |
| `repository_id` | string | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'package', 'dependent_list', hash( 'package_name', 'package_name' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'package', 'dependent_list', hash( 'package_name', 'package_name',
                                                 'parameters', array(),
                                                 'repository_id', 'repository_id' ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'package', 'dependent_list', array( 'package_name' => 'package_name' ) );
```

## Siehe auch

- [Alle Fetch-Funktionen von package](README.md)
- [Die Ansichten des Moduls package](../../modulansichten/package/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/function_definition.php`, `kernel/package/ezpackagefunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
