---
title: Policies of the user module
audience: developer, admin
category: examples
summary: The policy functions of the user module (11) and their limitations.
keywords: user, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Policies of the user module

The policy functions of `user` and their limitations, from `$FunctionList` in `kernel/user/module.php`. In the administration they are chosen under Roles and policies; a policy on `user/*` grants every function.

## activation

Checked by the views: [unactivated](../module-views/user/unactivated.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'activation' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'activation' ) )}
    ...
{/if}
```

## audit

Checked by the views: [audit](../module-views/user/audit.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'audit' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'audit' ) )}
    ...
{/if}
```

## deactivate

Checked by the views: [deactivate](../module-views/user/deactivate.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'deactivate' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'deactivate' ) )}
    ...
{/if}
```

## hygiene

Checked by the views: [hygiene](../module-views/user/hygiene.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'hygiene' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'hygiene' ) )}
    ...
{/if}
```

## login

Checked by the views: [activate](../module-views/user/activate.md), [edit](../module-views/user/edit.md), [login](../module-views/user/login.md), [logout](../module-views/user/logout.md), [megamenu](../module-views/user/megamenu.md), [passkeys](../module-views/user/passkeys.md), [preferences](../module-views/user/preferences.md), [registration](../module-views/user/registration.md), [security](../module-views/user/security.md), [sessions](../module-views/user/sessions.md).

| Limitation | Values come from |
|---|---|
| SiteAccess | `eZSiteAccess::siteAccessList()` |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'login', array( 'SiteAccess' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'login' ) )}
    ...
{/if}
```

## password

Checked by the views: [forgotpassword](../module-views/user/forgotpassword.md), [password](../module-views/user/password.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'password' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'password' ) )}
    ...
{/if}
```

## preferences

Checked by the views: [setting](../module-views/user/setting.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'preferences' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'preferences' ) )}
    ...
{/if}
```

## register

Checked by the views: [register](../module-views/user/register.md), [success](../module-views/user/success.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'register' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'register' ) )}
    ...
{/if}
```

## selfedit

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'user', 'selfedit' )` or the `user/has_access_to` fetch).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'selfedit' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'selfedit' ) )}
    ...
{/if}
```

## sessions

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'user', 'sessions' )` or the `user/has_access_to` fetch).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'sessions' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'sessions' ) )}
    ...
{/if}
```

## sessions_manage

Checked by the views: [sessionreport](../module-views/user/sessionreport.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'user', 'sessions_manage' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'user', 'function', 'sessions_manage' ) )}
    ...
{/if}
```

## Related

- [All modules' policies](README.md)
- [The views of user](../module-views/user/README.md)
- [Roles and policies](../../guides/roles-and-policies.md)

---

Generated by `./console exp:help:examples` from `kernel/user/module.php`, `kernel/classes/ezrole.php`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
