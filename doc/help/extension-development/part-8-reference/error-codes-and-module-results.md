---
title: Error codes and module results
audience: developer
category: extension-development
order: 330
summary: The kernel error codes a view returns with handleError() - access denied, not found, not available, moved, form token refused - their HTTP status in error.ini and the error templates.
keywords: eZError, handleError, KERNEL_ACCESS_DENIED, KERNEL_NOT_FOUND, KERNEL_NOT_AVAILABLE, KERNEL_MOVED, KERNEL_FORM_TOKEN_REFUSED, error.ini, HTTPError, error templates, 404, 403, 401
type: help
---
# Error codes and module results

Part 8, reference 29. A view that cannot do its job returns an error instead of a result:
`return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );`. The kernel then shows the error page for that
code, with the right HTTP status, and (for access denied) may ask the visitor to sign in. This page lists the codes
and how they are handled.

## The kernel codes (eZError, kernel/classes/ezerror.php)

| Constant | Code | Use it when | HTTP status (error.ini) |
|---|---|---|---|
| `KERNEL_ACCESS_DENIED` | 1 | the user may not do this | 401; the default settings show the sign-in view in its place (`ErrorHandler[1]=embed`, `EmbedURL[1]=/user/login`) |
| `KERNEL_NOT_FOUND` | 2 | the thing asked for does not exist | 404 |
| `KERNEL_NOT_AVAILABLE` | 3 | it exists but cannot be shown now (wrong ID, missing parameter, unpublished) | 404 |
| `KERNEL_MOVED` | 4 | it moved | 301 |
| `KERNEL_LANGUAGE_NOT_FOUND` | 5 | the requested translation does not exist | |
| `KERNEL_FORM_TOKEN_REFUSED` | 6 | a POST without a valid form token (the kernel uses it) | 403 |
| `KERNEL_MODULE_NOT_FOUND` | 20 | no such module | 404 |
| `KERNEL_MODULE_VIEW_NOT_FOUND` | 21 | no such view | 404 |
| `KERNEL_MODULE_DISABLED` | 22 | the module is switched off | 404 |
| `KERNEL_NO_DB_CONNECTION` | 50 | the database is unreachable | |

There are also shop codes (`SHOP_*`) for the shop module's own errors.

## In a view

```php
<?php
$Module = $Params['Module'];
$itemID = (int)$Params['ItemID'];
$item = myExtItem::fetch( $itemID );
if ( !$item instanceof myExtItem )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}
$access = eZUser::currentUser()->hasAccessTo( 'myext', 'manage' );
if ( $access['accessWord'] !== 'yes' )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}
```

Return the call: the view must end there.

## How the kernel handles each code

`settings/error.ini [ErrorSettings-kernel]` decides per code:

| Setting | Meaning |
|---|---|
| `ErrorHandler[<code>]` | `displayerror` (show the error template, the default), `redirect`, `rerun` or `embed` (show another view, like the sign-in) |
| `RedirectURL[<code>]`, `EmbedURL[<code>]` | where to |
| `HTTPError[<code>]` | the HTTP status sent |

The error pages are the templates `error/kernel/<code>.tpl` in the design chain; a theme can override them.

## Errors that are not views

- **JSON answers** (an Ajax view): set the HTTP status yourself and answer with a JSON body; do not return an HTML
  error page to a script.
- **Command line scripts**: write the error with `$cli->error()` and end with a non-zero exit code
  ([eZScript and eZCLI](ezscript-and-ezcli.md)).
- **Fatal PHP errors** end in the kernel's general error page; the details go to the log, never to the visitor.

## Related pages

- Previous: [Directories under var/](directories-under-var.md)
- Next: [The expui JavaScript API](expui-javascript-api.md)
- [eZModule and module results](ezmodule.md)
- [Error templates](../../designers/templates/error-templates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
