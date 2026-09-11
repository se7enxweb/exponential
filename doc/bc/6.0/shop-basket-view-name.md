# Shop basket view name

## Added: a configurable name for the shop basket page

The shop basket was reachable only at `/shop/basket/`, with that name hardcoded
in twelve places across six kernel scripts and in every shipped template's form
action. A site that wanted to call it a cart had to fork `kernel/shop`.

The name is now a setting. Any simple token works, and the whole checkout
follows it — routing, redirects, form actions, the breadcrumb and the template
lookup.

### Setting

`settings/shop.ini`:

```ini
[BasketSettings]
# The view name the shop uses for the basket page, and for every redirect,
# form action and breadcrumb that targets it.
#
#   BasketViewName=basket   -> /shop/basket/   (default)
#   BasketViewName=cart     -> /shop/cart/
#   BasketViewName=buyer    -> /shop/buyer/
BasketViewName=basket
```

Allowed characters are letters, digits, dash and underscore. Anything else
falls back to `basket` rather than producing a URL that routes nowhere.

### PHP API

`kernel/classes/ezbasket.php` gains two static methods:

- `eZBasket::viewName()`  
  The configured view name, validated. Returns `basket` when the setting is
  absent, empty or not a usable token.

- `eZBasket::viewTemplate()`  
  The template `basket.php` should render, *without* the `.tpl` suffix. Asks
  the design system whether `shop/<name>.tpl` exists, via
  `eZTemplateDesignResource::fileMatch()` across `allDesignBases()`, and returns
  `basket` when it does not.

How a value resolves:

| `BasketViewName` | `viewName()` | template rendered  |
|------------------|--------------|--------------------|
| `basket`         | `basket`     | `shop/basket.tpl`  |
| `cart`           | `cart`       | `shop/cart.tpl`    |
| `buyer`          | `buyer`      | `shop/basket.tpl`  |
| `Trolley-2`      | `Trolley-2`  | `shop/basket.tpl`  |
| `my cart!`       | `basket`     | `shop/basket.tpl`  |
| *(empty)*        | `basket`     | `shop/basket.tpl`  |

`viewTemplate()` is what makes an unthemed name work with no extra files: the
name routes, and the ordinary basket template renders it.

### Routing

`kernel/shop/module.php` registers the configured name as a view at runtime,
alongside `basket`:

```php
$basketViewName = eZBasket::viewName();
if ( $basketViewName !== 'basket' )
{
    $ViewList[$basketViewName] = array(
        "functions" => array( 'buy' ),
        "script" => "basket.php",
        ... );
}
```

`basket` stays registered whatever the setting, so existing links and bookmarks
keep resolving. Only the configured name and `basket` are live at any one time:

```
BasketViewName=basket   /shop/basket/ 200   /shop/cart/ 404   /shop/buyer/ 404
BasketViewName=buyer    /shop/basket/ 200   /shop/cart/ 404   /shop/buyer/ 200
```

Both names run the same `basket.php`, so there is one code path and no
behaviour can drift between them.

### What follows the setting

Twelve redirect sites across six scripts, previously hardcoded:

| File                          | Sites |
|-------------------------------|-------|
| `kernel/shop/basket.php`      | 6 `functionURI()` calls, plus the breadcrumb label |
| `kernel/shop/add.php`         | 2, including the literal `"/shop/basket/"` |
| `kernel/shop/updatebasket.php`| 1 |
| `kernel/shop/confirmorder.php`| 1 |
| `kernel/shop/register.php`    | 1 |
| `kernel/shop/userregister.php`| 1 |

**Not** renamed, deliberately: `$FunctionList['basket']` in
`kernel/shop/function_definition.php` is the `shop/basket` **policy** function
name. Renaming it would break every role policy granting access to the basket.

### Template contract

`basket.php` sets `basket_view_name`. Templates build the action from it and
never read the INI themselves:

```tpl
<form method="post" action={concat('/shop/',cond(is_set($basket_view_name),$basket_view_name,'basket'),'/')|ezurl}>
```

The `cond()` fallback covers a design rendering the template outside the module.

Updated in this repository: `design/standard/templates/shop/basket.tpl` and
`design/base/templates/shop/basket.tpl`.

### Theming a name

A design may style a name by adding `shop/<name>.tpl`.
`design/standard/templates/shop/cart.tpl` ships as the worked example, and
includes the basket markup rather than duplicating it:

```tpl
{include uri='design:shop/basket.tpl' basket_view_name=$basket_view_name}
```

`basket_view_name` must be passed explicitly — an included template gets its own
scope and does not inherit variables set by the module.

Any design that overrides `shop/basket.tpl` therefore keeps working under either
name, with no per-name file required.

## Fixed: kernel bugs found while wiring this up

These are independent of the setting and apply at the default.

### Add to basket did nothing

`kernel/shop/operation_definition.php` declares `option_list` as a **required
parameter of type `array`**, but `add.php` passed the session variable
straight through — and it is simply unset for a product with no options. The
parameter check failed and `eZOperationHandler::execute()` returned
`STATUS_CONTINUE` *without running the operation body*, so the request
redirected to the basket having added nothing, logging no error.

`add.php` now always passes an array.

### `array_keys( false )` — fatal on PHP 8

Once the body did run, `eZShopOperationCollection::addToBasket()` called
`array_keys()` on that same non-array. `$optionList` is now normalised at the
top of the method, covering all nine uses within it.

### A checked-out basket served as the current one

`eZBasket::currentBasket()` matched on `session_id` alone. A basket whose
session had been blanked but which was already attached to an order was
therefore returned to any request arriving without a session id, so items were
added to a dead basket. The lookup now also requires `order_id = 0`.

### Address line 2 was required, line 1 was not

`kernel/shop/userregister.php` validated `Street2` and ignored `Street1` — the
stray indentation on that `if` suggests it was never intended. A form filled in
the obvious way could not be submitted and gave no clue why. Line 1 is now the
required one.

The same script also accepts `City` with a fallback to `Place`, stores the
address line as `<city>`, and collects an optional `<phone>` — required only
when the form actually offers the field, so shipped templates without it are
unaffected.

## Media design templates

The media design previously fell through to ezwebin's shop templates, which do
not match it and hardcode `/shop/basket/`. A full set of overrides now ships in
the `sevenx_themes_media` extension:

| Template | Purpose |
|---|---|
| `shop/basket.tpl` | basket page, action follows the view name |
| `shop/userregister.tpl` | account information, `City` + `Phone` |
| `shop/confirmorder.tpl` | confirm order |
| `shop/orderview.tpl` | placed order |
| `shop/country/edit.tpl` | country selector, styled and untruncated |
| `shop/accounthandlers/html/ez.tpl` | customer and address block |

Commit:
<https://github.com/se7enxweb/sevenx_themes_media/commit/6f5c454>

Two of those fix problems in the templates they replace:

- `shop/country/edit.tpl` — the standard template shortens every country name
  to twenty characters, which was written for a narrow fixed-width select and
  cuts real entries (`Saint Vincent and...`, `United States of...`). The
  override stops truncating by default and gives the select a form control
  class. Pass `max_len` to shorten again.

- `shop/accounthandlers/html/ez.tpl` — the standard template labels `street1`
  as **Company** and `street2` as **Street**, so an ordinary address rendered as
  a company name above an empty street. Labels now match the fields, the
  address line reads `city` with a fallback to `place`, and the phone number is
  shown.

### A trap worth knowing

Submit buttons are disabled a tick after submission, never inside the handler:

```js
form.addEventListener( 'submit', function()
{
    window.setTimeout( disableButtons, 0 );
} );
```

A submit button disabled while the submit event is still running is **left out
of the posted data**. That loses the button's own name, so the module sees no
action and silently re-renders an empty form — with no validation message,
because no validation ran. It only reproduces in a browser; `curl` has no
JavaScript and always succeeded.

## Compatibility

The default is `basket`, and every URL, redirect and template behaves exactly as
before when the setting is absent. `/shop/basket/` remains registered under any
configuration.

The one behaviour change at the default is the `Street1`/`Street2` correction
above: a form that previously required address line 2 now requires line 1. Any
custom `shop/userregister.tpl` marking line 2 with an asterisk should move it.
