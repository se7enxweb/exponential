---
title: ezformtoken and CSRF protection
audience: developer
category: extension-development
order: 240
summary: How ezformtoken protects every POST of a signed-in user against request forgery - the token in forms and the page head, the X-CSRF-Token header, the 403 refusal - and what your extension does.
keywords: ezformtoken, ezxFormToken, CSRF, cross-site request forgery, ezxform_token, X-CSRF-Token, csrf-token meta, form token, POST, 403
type: help
---
# ezformtoken and CSRF protection

Part 8, reference 20. Cross-site request forgery (CSRF) is another site making a signed-in user's browser send a
POST to your site. The `ezformtoken` extension shipped with the kernel stops it with a secret token per session. Most
of the time it works without your extension doing anything; this page says when it does not, and what to do.

## How it works

The extension registers three listeners (its `settings/site.ini.append.php`):

| Event | Method | Does |
|---|---|---|
| `response/output` | `ezxFormToken::output()` | adds a hidden field `ezxform_token` to every `<form>` of an HTML page, and meta tags `csrf-param` and `csrf-token` (with the ID `ezxform_token_js`) to `<head>` |
| `request/input` | `ezxFormToken::input()` | refuses a POST without the right token |
| `session/regenerate` | `ezxFormToken::reset()` | a new token with a new session |

Only signed-in users with a session are protected (anonymous visitors have nothing to steal), and only POST
requests are checked.

## Where the token is looked for

On a POST, `input()` takes the token from, in this order:

1. the POST field (`ezxform_token`, or the field name configured);
2. the HTTP header `X-CSRF-Token`, for Ajax requests that post JSON or XML.

A missing or wrong token ends the request with a 403 refusal (a JSON body for Ajax and JSON requests).

## What your extension does

| Situation | What to do |
|---|---|
| A `<form method="post">` in a template | nothing: the output filter adds the field |
| A form built by JavaScript | add a hidden `ezxform_token` field with the value of the `csrf-token` meta tag |
| An Ajax POST with form data | send `ezxform_token`; `Exp.io.call()` and ezjscore's `$.ez()` do it for you |
| An Ajax POST with a JSON body | send the header `X-CSRF-Token` with the token |
| A view that changes data on GET | change it to POST: GET is never checked |
| A JSON page answered by your view | the token is only added to HTML pages; read it from the page that makes the call |

In PHP, `ezxFormToken::getToken()` returns the current session's token, for templates you render outside the normal
output (a dialog fetched by Ajax, for example):

```php
<?php
$tpl = eZTemplate::factory();
$tpl->setVariable( 'ezxform_token', ezxFormToken::getToken() );
```

## Reading the token in JavaScript

```javascript
var meta = document.querySelector( 'meta[name="csrf-token"]' );
var token = meta ? meta.getAttribute( 'content' ) : '';
fetch( '/myext/save', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token },
    body: JSON.stringify( { name: 'Ada' } ),
    credentials: 'same-origin'
} );
```

## Never switch it off

`ezxFormToken::setIsEnabled( false )` exists for special cases inside the kernel. An extension that disables the
protection to make its own form work opens every POST of the site to forgery. Fix the form instead.

## Related pages

- Previous: [ezjscore server functions reference](ezjscore-server-functions-reference.md)
- Next: [Sending mail with eZMail](ezmail.md)
- [Security in extensions](../part-3-extension-developer/security-in-extensions.md)
- [Cross-Site Request Forgery](https://owasp.org/www-community/attacks/csrf) at OWASP
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
