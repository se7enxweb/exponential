---
title: Policies of the setup module
audience: developer, admin
category: examples
summary: The policy functions of the setup module (10) and their limitations.
keywords: setup, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Policies of the setup module

The policy functions of `setup` and their limitations, from `$FunctionList` in `kernel/setup/module.php`. In the administration they are chosen under Roles and policies; a policy on `setup/*` grants every function.

## administrate

Checked by the views: [maintenance](../module-views/setup/maintenance.md), [menu](../module-views/setup/menu.md), [session](../module-views/setup/session.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'administrate' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'administrate' ) )}
    ...
{/if}
```

## install

Checked by the views: [init](../module-views/setup/init.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'install' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'install' ) )}
    ...
{/if}
```

## logs

Checked by the views: [logs](../module-views/setup/logs.md), [logsaction](../module-views/setup/logsaction.md), [logsdigest](../module-views/setup/logsdigest.md), [logsfeed](../module-views/setup/logsfeed.md), [logshealth](../module-views/setup/logshealth.md), [menu](../module-views/setup/menu.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'logs' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'logs' ) )}
    ...
{/if}
```

## managecache

Checked by the views: [cache](../module-views/setup/cache.md), [cachetoolbar](../module-views/setup/cachetoolbar.md), [menu](../module-views/setup/menu.md), [staticcachestream](../module-views/setup/staticcachestream.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'managecache' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'managecache' ) )}
    ...
{/if}
```

## managecronjobs

Checked by the views: [cronjobs](../module-views/setup/cronjobs.md), [cronjobsstream](../module-views/setup/cronjobsstream.md), [menu](../module-views/setup/menu.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'managecronjobs' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'managecronjobs' ) )}
    ...
{/if}
```

## managelogs

Checked by the views: [logsmanage](../module-views/setup/logsmanage.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'managelogs' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'managelogs' ) )}
    ...
{/if}
```

## preload

Checked by the views: [menu](../module-views/setup/menu.md), [preload](../module-views/setup/preload.md), [preloadjob](../module-views/setup/preloadjob.md), [preloadstream](../module-views/setup/preloadstream.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'preload' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'preload' ) )}
    ...
{/if}
```

## setup

Checked by the views: [contentextension](../module-views/setup/contentextension.md), [datatype](../module-views/setup/datatype.md), [designextension](../module-views/setup/designextension.md), [extensions](../module-views/setup/extensions.md), [handlerextension](../module-views/setup/handlerextension.md), [kerneloverride](../module-views/setup/kerneloverride.md), [menu](../module-views/setup/menu.md), [menuconfig](../module-views/setup/menuconfig.md), [moduleextension](../module-views/setup/moduleextension.md), [moduletable](../module-views/setup/moduletable.md), [modulewizard](../module-views/setup/modulewizard.md), [rad](../module-views/setup/rad.md), [radsurvey](../module-views/setup/radsurvey.md), [reauth](../module-views/setup/reauth.md), [settingsextension](../module-views/setup/settingsextension.md), [settingstoolbar](../module-views/setup/settingstoolbar.md), [systemupgrade](../module-views/setup/systemupgrade.md), [templatecreate](../module-views/setup/templatecreate.md), [templateedit](../module-views/setup/templateedit.md), [templatelist](../module-views/setup/templatelist.md), [templateoperator](../module-views/setup/templateoperator.md), [templateview](../module-views/setup/templateview.md), [toolbar](../module-views/setup/toolbar.md), [toolbarlist](../module-views/setup/toolbarlist.md), [workflowevent](../module-views/setup/workflowevent.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'setup' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'setup' ) )}
    ...
{/if}
```

## system_info

Checked by the views: [info](../module-views/setup/info.md), [languages](../module-views/setup/languages.md), [menu](../module-views/setup/menu.md), [sitedata](../module-views/setup/sitedata.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'system_info' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'system_info' ) )}
    ...
{/if}
```

## unmasklogs

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'setup', 'unmasklogs' )` or the `user/has_access_to` fetch).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'setup', 'unmasklogs' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'unmasklogs' ) )}
    ...
{/if}
```

## Related

- [All modules' policies](README.md)
- [The views of setup](../module-views/setup/README.md)
- [Roles and policies](../../guides/roles-and-policies.md)

---

Generated by `./console exp:help:examples` from `kernel/setup/module.php`, `kernel/classes/ezrole.php`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
