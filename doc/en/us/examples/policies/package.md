---
title: Policies of the package module
audience: developer, admin
category: examples
summary: The policy functions of the package module (9) and their limitations.
keywords: package, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Policies of the package module

The policy functions of `package` and their limitations, from `$FunctionList` in `kernel/package/module.php`. In the administration they are chosen under Roles and policies; a policy on `package/*` grants every function.

## create

Checked by the views: [create](../module-views/package/create.md).

| Limitation | Values come from |
|---|---|
| CreatorType | `eZPackageCreationHandler::creatorLimitationList()` |
| Role | `eZPackage::maintainerRoleListForRoles()` |
| Type | `eZPackage::typeList()` |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'create', array( 'CreatorType' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'create' ) )}
    ...
{/if}
```

## edit

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'package', 'edit' )` or the `user/has_access_to` fetch).

| Limitation | Values come from |
|---|---|
| Type | `eZPackage::typeList()` |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'edit', array( 'Type' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'edit' ) )}
    ...
{/if}
```

## export

Checked by the views: [export](../module-views/package/export.md).

| Limitation | Values come from |
|---|---|
| Type | `eZPackage::typeList()` |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'export', array( 'Type' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'export' ) )}
    ...
{/if}
```

## export_extension

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'package', 'export_extension' )` or the `user/has_access_to` fetch).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'export_extension' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'export_extension' ) )}
    ...
{/if}
```

## import

Checked by the views: [upload](../module-views/package/upload.md).

| Limitation | Values come from |
|---|---|
| Type | `eZPackage::typeList()` |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'import', array( 'Type' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'import' ) )}
    ...
{/if}
```

## install

Checked by the views: [install](../module-views/package/install.md), [uninstall](../module-views/package/uninstall.md).

| Limitation | Values come from |
|---|---|
| Type | `eZPackage::typeList()` |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'install', array( 'Type' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'install' ) )}
    ...
{/if}
```

## list

Checked by the views: [list](../module-views/package/list.md).

| Limitation | Values come from |
|---|---|
| Type | `eZPackage::typeList()` |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'list', array( 'Type' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'list' ) )}
    ...
{/if}
```

## read

Checked by the views: [compare](../module-views/package/compare.md), [view](../module-views/package/view.md), [viewfile](../module-views/package/viewfile.md).

| Limitation | Values come from |
|---|---|
| Type | `eZPackage::typeList()` |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'read', array( 'Type' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'read' ) )}
    ...
{/if}
```

## remove

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'package', 'remove' )` or the `user/has_access_to` fetch).

| Limitation | Values come from |
|---|---|
| Type | `eZPackage::typeList()` |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'package', 'remove', array( 'Type' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'package', 'function', 'remove' ) )}
    ...
{/if}
```

## Related

- [All modules' policies](README.md)
- [The views of package](../module-views/package/README.md)
- [Roles and policies](../../guides/roles-and-policies.md)

---

Generated by `./console exp:help:examples` from `kernel/package/module.php`, `kernel/classes/ezrole.php`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
