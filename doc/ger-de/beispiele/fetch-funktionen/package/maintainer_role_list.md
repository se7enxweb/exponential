---
title: fetch( 'package', 'maintainer_role_list' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion maintainer_role_list des Moduls package: 2 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, package, maintainer_role_list, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'package', 'maintainer_role_list' )

Definiert in `kernel/package/function_definition.php`; sie ruft `eZPackageFunctionCollection::fetchMaintainerRoleList()` auf (`kernel/package/ezpackagefunctioncollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `type` | string | nein | `false` |
| `check_roles` | boolean | nein | `false` |

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'package', 'maintainer_role_list', hash( 'type', 'type' ) )}
{* mit jedem Parameter *}
{def $all = fetch( 'package', 'maintainer_role_list', hash( 'type', 'type',
                                                       'check_roles', true() ) )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'package', 'maintainer_role_list', array( 'type' => 'type' ) );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/package/create/wizard_review.tpl` Zeile 40:

```tpl
<dd>{$rv.maintainer_person|wash}{if first_set( $rv.maintainer_email, false() )} &lt;{$rv.maintainer_email|wash}&gt;{/if}{if first_set( $rv.maintainer_role, false() )} <span class="a4-tag">{foreach fetch( 'package', 'maintainer_role_list' ) as $rv_role}{if $rv_role.id|eq( $rv.maintainer_role )}{$rv_role.name|wash}{/if}{/foreach}</span>{/if}</dd>
```

## Siehe auch

- [Alle Fetch-Funktionen von package](README.md)
- [Die Ansichten des Moduls package](../../modulansichten/package/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/package/function_definition.php`, `kernel/package/ezpackagefunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
