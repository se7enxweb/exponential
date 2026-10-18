---
title: Forms, CSRF protection and HTTP input
audience: app-creator, developer
category: app-creation
order: 140
summary: Handle forms safely in views - reading input with eZHTTPTool and module actions, form tokens from ezformtoken, POST-redirect-GET, validation messages, safe redirects, uploads with eZHTTPFile, and JSON posts from scripts.
keywords: forms, CSRF, form token, ezformtoken, eZHTTPTool, postVariable, hasPostVariable, POST redirect GET, validation, eZHTTPFile, upload, safe redirect, app creator
type: help
---
# Forms, CSRF protection and HTTP input

This page is for application creators whose views take input. Three rules: read input through the kernel's helpers,
let the form token protect every POST, and validate everything before you use it.

## Contents

1. [Reading input](#reading-input)
2. [Form tokens](#form-tokens)
3. [POST, redirect, GET](#post-redirect-get)
4. [Showing validation errors](#showing-validation-errors)
5. [Uploads](#uploads)
6. [Posts from scripts](#posts-from-scripts)

## Reading input

```php
$http = eZHTTPTool::instance();
if ( $http->hasPostVariable( 'Title' ) )
    $title = trim( (string)$http->postVariable( 'Title' ) );
$page = $http->hasGetVariable( 'page' ) ? max( 1, (int)$http->getVariable( 'page' ) ) : 1;
```

For buttons, declare actions in `module.php` and use `$Module->isCurrentAction()` and `actionParameter()`
([Custom modules and views](custom-modules-and-views.md#handling-forms-actions)). Never read `$_POST` or `$_GET`
directly; under Velocity the kernel prepares them per request.

## Form tokens

`ezformtoken` adds a hidden token to every form with `method="post"` in the output and refuses POST requests without a
valid one: a 403 and one line in `var/log/warning.log` (`site.ini [HTMLForms]`). Your forms are protected as long as
they are rendered by templates and post to the same site. Set `[HTMLForms] Secret` per installation. When a token
expired, visitors see the form-expired page ([Form expired page](../../../features/6.0/form-expired-page.md)).

JavaScript that posts with `fetch()` must send the token too; read it from the page (the ezjscore and admin scripts do
this) rather than switching the check off.

## POST, redirect, GET

After a successful POST, redirect to a GET page, so a reload does not repeat the action:

```php
if ( $Module->isCurrentAction( 'Store' ) && $errors === array() )
{
    $item->store();
    return $Module->redirectTo( '/expdemo/item/' . (int)$item->attribute( 'id' ) );
}
```

Redirects to an address taken from the request (a `RedirectURI`) must stay on the site; use the kernel's safe redirect
checks ([Safe redirects](../../../features/6.0/safe-redirects.md)).

## Showing validation errors

Collect errors per field, re-render the form with the values the user typed, and show the errors next to the fields and
in a summary at the top:

```php
$tpl->setVariable( 'errors', $errors );   // array( 'title' => 'Enter a title' )
$tpl->setVariable( 'values', array( 'title' => $title ) );
```

## Uploads

```php
if ( eZHTTPFile::canFetch( 'File' ) )
{
    $file = eZHTTPFile::fetch( 'File' );
    // check $file->attribute( 'mime_type' ) and size against an allow list before storing
}
```

For content, prefer creating an object of the `file` or `image` class, which handles storage, clustering and SVG
sanitizing ([Creating and updating content in code](creating-content-in-code.md)).

## Posts from scripts

Machine clients do not use HTML forms: give them the REST API with API keys or OAuth, or expservices with tokens,
which have their own authentication instead of form tokens (REST API v1 and v2,
OAuth and API keys).

## Related pages

- Input validation and output escaping
- Security for applications
- [Security standard](../../../standards/security.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
