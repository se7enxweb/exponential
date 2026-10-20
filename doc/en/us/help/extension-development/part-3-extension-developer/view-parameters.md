---
title: View parameters, unordered parameters and POST
audience: developer, app-creator
category: extension-development
order: 100
summary: How an address reaches a view: ordered, unordered and user parameters as eZModule::run() parses them, and reading POST and session values safely with eZHTTPTool.
keywords: view parameters, params, unordered_params, user parameters, (offset), Params, UserParameters, eZHTTPTool, postVariable, hasPostVariable, getVariable, session, validation, cast
type: help
---
# View parameters, unordered parameters and POST

Part 3, chapter 10. A view gets its input from three places: the **address**, the **POST body** of a form, and the
**session**. This chapter shows how each arrives, as `eZModule::run()` in `lib/ezutils/classes/ezmodule.php` parses it,
and how to read it safely.

## Ordered parameters

```php
$ViewList['world'] = array( 'script' => 'world.php', 'params' => array( 'Name', 'Mood' ) );
```

`/hello/world/Ada/happy` gives `$Params['Name'] = 'Ada'` and `$Params['Mood'] = 'happy'`. A missing parameter is not
set; read it with `isset()`.

## Unordered parameters

```php
$ViewList['list'] = array( 'script' => 'list.php',
                           'params' => array(),
                           'unordered_params' => array( 'offset' => 'Offset', 'sort' => 'SortBy' ) );
```

The URL names them, in any order, after the ordered parameters. Both spellings work:

| Address | `$Params` |
|---|---|
| `/hello/list/offset/20` | `Offset = '20'`, `SortBy = false` |
| `/hello/list/(offset)/20/(sort)/name` | `Offset = '20'`, `SortBy = 'name'` |

The first form is matched against the path elements; the second, in parentheses, is a **user parameter** that the
kernel maps onto the unordered parameter of the same name. A name at the end of the address with no value gives
`false`, "as if it were not given".

## User parameters

Every `/(name)/value` pair of the address is also collected in `$Params['UserParameters']`, whether the view declares
it or not. Content views pass them to templates as `$view_parameters`, which is how `/(offset)/10` pages a folder.
eZpedia's ["How to add view parameter support to your custom module view"](https://ezpedia.exponential.earth/en/solution/how_to_add_view_parameter_support_to_your_custom_module_view)
describes the same mechanism for older versions.

```php
<?php
$userParameters = isset( $Params['UserParameters'] ) ? $Params['UserParameters'] : array();
$offset = isset( $userParameters['offset'] ) ? (int)$userParameters['offset'] : 0;
$tpl = eZTemplate::factory();
$tpl->setVariable( 'view_parameters', $userParameters );
```

## Everything from the address is untrusted

Treat every parameter as text typed by a stranger:

| Value | Do |
|---|---|
| an ID | `(int)$Params['ObjectID']`, then fetch and check it exists |
| an offset or limit | `(int)`, and clamp: `max( 0, min( $offset, 10000 ) )` |
| a word from a fixed list | compare against the list: `in_array( $sort, array( 'name', 'published' ), true )` |
| free text shown in a page | escape it in the template: `{$name\|wash}` |
| free text in SQL | never directly; use `eZPersistentObject` or `$db->escapeString()` |

## POST variables

Use `eZHTTPTool` (`lib/ezutils/classes/ezhttptool.php`):

```php
<?php
$http = eZHTTPTool::instance();

if ( $http->hasPostVariable( 'Comment' ) )
{
    $comment = trim( (string)$http->postVariable( 'Comment' ) );
}
$page = (int)$http->postVariable( 'Page', 1 );      // with a fallback value
$fromQuery = $http->getVariable( 'q', '' );           // ?q=... of the address
```

`postVariable( $var, $fallbackValue = null )` returns the fallback when the variable is missing.

Prefer the module's actions (`single_post_actions`, `isCurrentAction()`, see [Modules and views](modules-and-views.md))
over checking button names by hand: they keep the view readable.

## The session

```php
<?php
$http = eZHTTPTool::instance();
$http->setSessionVariable( 'MysiteHelloLast', $name );
$last = $http->hasSessionVariable( 'MysiteHelloLast' ) ? $http->sessionVariable( 'MysiteHelloLast' ) : null;
$http->removeSessionVariable( 'MysiteHelloLast' );
```

Prefix session keys with your extension's name. Do not store large data or objects in the session.

## After a POST, redirect

A view that changed something ends with a redirect (`$Module->redirectToView()` or `redirectTo()`), so that reloading
the page does not send the form again.

## Related pages

- Previous: [Modules and views](modules-and-views.md)
- Next: [Policy functions and access checks](policy-functions-and-access.md)
- [eZHTTPTool: requests, sessions and redirects](../part-8-reference/ezhttptool.md)
- [Security in extensions](security-in-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
