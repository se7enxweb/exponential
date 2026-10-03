# An extension can replace a kernel module

A module is a set of views behind a URL (`content/view`, `user/login`, `shop/basket`).
Until January 2024 the kernel's module won whenever an extension offered a module
of the same name, so an extension could add modules but not replace one. Now the
module repositories are searched so that **extensions come first**: put a module
with the same name in your extension and it is used instead of the kernel's.

## When to use it

Replace a whole module when a view needs more than a template override or an
event can give, for example a login flow with an extra step or a changed basket.
For small changes prefer the [RAD extension points](../../bc/6.0/rad-extension-points.md)
and view classes ([commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md)).

## Do it

1. Create the module in your extension, with the same layout as a kernel module:

   ```
   extension/myext/modules/user/module.php
   extension/myext/modules/user/login.php
   ```

2. Make sure the extension is active (`settings/override/site.ini.append.php`,
   `[ExtensionSettings] ActiveExtensions[]=myext`) and that it lists itself in
   `settings/module.ini`, block `[ModuleSettings]`, key `ExtensionRepositories[]`
   (an extension does this in its own `settings/module.ini.append.php` with
   `ExtensionRepositories[]=myext`).
3. Clear the caches: `php bin/php/ezcache.php --clear-all --allow-root-user`.
4. Open the URL; the extension's view runs.

A view you do not define in your copy of the module is not inherited: the whole
module is replaced, so copy every view you still need.

## How it works

`eZModule::globalPathList()` (`lib/ezutils/classes/ezmodule.php`) builds the list
of module directories: the kernel first and the extension repositories after it.
Since 24 January 2024 (`c6b90d723d`) it reverses the combined list, so the
extension repositories are searched first; the first directory that holds a
module with the requested name wins.

## A related robustness fix

A view script of an incomplete extension (the ownerchange extension was one) can
return something that is not the expected array. Since October 2024
(`884f21e963`) `eZModule` only fills in the default navigation part of the result when the
result is an array, so such a view no longer ends in a fatal error while the
page is assembled.

## Related

[Chronicle: January 2024, second half](../../history/2024/2024-01b.md),
[Chronicle: October 2024](../../history/2024/2024-10.md).
