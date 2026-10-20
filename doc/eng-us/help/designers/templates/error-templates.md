---
title: Error templates
audience: designer, developer
category: templates
order: 730
summary: Designing the error pages (access denied, not found, module not found, form token refused ...): the error/kernel/<number>.tpl templates and what each number means, the HTTP status sent, the sign-in form embedded on access denied, readable titles, and a helpful not-found page.
keywords: error templates, error pages, 404 page, access denied page, error/kernel, error.ini, HTTPError, EmbedURL, not found, kernel errors, error numbers
type: help
---
# Error templates

This page is for designers who style the pages visitors see when something is not available. Error pages are frequent
(every mistyped link) and deserve the same care as content pages.

## Contents

1. [The templates and their numbers](#the-templates-and-their-numbers)
2. [Status codes and handlers](#status-codes-and-handlers)
3. [A helpful not-found page](#a-helpful-not-found-page)
4. [Titles of error pages](#titles-of-error-pages)

## The templates and their numbers

`design/standard/templates/error/kernel/<number>.tpl`, where the number is a kernel error code (`eZError` constants in
`lib/ezutils/classes/ezerror.php`):

| Number | Meaning | Template |
|---|---|---|
| 1 | access denied | `error/kernel/1.tpl` |
| 2 | not found | `error/kernel/2.tpl` |
| 3 | not available | `error/kernel/3.tpl` |
| 4 | moved | `error/kernel/4.tpl` |
| 5 | language not found | `error/kernel/5.tpl` |
| 6 | form token refused | `error/kernel/6.tpl` |
| 20, 21, 22 | module not found, view not found, module disabled | `error/kernel/20.tpl` ... |

Copy the ones you restyle into your design at the same path. The media design has its own `templates/error/` with
`kernel/`, `shop/` and shared `parts/`.

## Status codes and handlers

`settings/error.ini [ErrorSettings-kernel]`:

- `HTTPError[<number>]` sets the HTTP status (shipped: 1 is 401, 2, 3 and 20 to 22 are 404, 4 is 301, 6 is 403);
- `ErrorHandler[1]=embed` with `EmbedURL[1]=/user/login`: an anonymous visitor who is denied access sees the error
  template with the sign-in form embedded, so they can sign in on the spot.

The pagelayout frames error pages like any other page.

## A helpful not-found page

- Say what happened in plain words and keep the site's header and navigation.
- Offer a search form and a link to the front page.
- Do not print the requested URL unwashed.

## Titles of error pages

An error page's path ends in the error code ("kernel (20)"), which means nothing to visitors. `$module_result` carries
`errorType` and `errorNumber`; the media design's `pagelayout/head/title.tpl` uses a switch on them to give each error
page a readable `<title>` ([Module result and content info](module-result-and-content-info.md)).

## Related pages

- [User and login templates](user-and-login-templates.md)
- [Search results template](search-results-template.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
