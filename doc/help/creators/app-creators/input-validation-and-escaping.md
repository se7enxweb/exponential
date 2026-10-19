---
title: Input validation and output escaping
audience: app-creator, developer
category: app-creation
order: 580
summary: Treat every input as untrusted and every output as needing escape - casting and allow lists in PHP, validating view parameters and posts, wash in templates for HTML and attributes, URLs, JSON and SQL, and the common mistakes.
keywords: input validation, output escaping, XSS, wash, htmlspecialchars, casting, allow list, view parameters, JSON, URL encoding, SQL escaping, app creator
type: help
---
# Input validation and output escaping

This page is for application creators who handle data from requests, imports or APIs. Two rules prevent most security
bugs: validate input where it enters, escape output where it leaves, in the format of the place it goes.

## Contents

1. [Validating input](#validating-input)
2. [Escaping output](#escaping-output)
3. [Contexts and their escaping](#contexts-and-their-escaping)
4. [Common mistakes](#common-mistakes)

## Validating input

| Input | Validate |
|---|---|
| IDs and numbers | `(int)`, then a range check (`max( 1, min( 100, (int)$limit ) )`) |
| Choices | an allow list: `in_array( $sort, array( 'name', 'published' ), true )` |
| Text | length limits, trim, the characters allowed for the field |
| E-mail, URL | the datatype or `filter_var()` validation; for URLs also the scheme (`https` only) |
| Files | type by content and size, stored through the kernel |
| Redirect targets | only addresses of the site ([Safe redirects](../../../features/6.0/safe-redirects.md)) |

View parameters (`$Params`) and unordered parameters are strings from the address, as untrusted as POST data.

## Escaping output

In templates, every value from users or the database goes through `|wash`:

```text
<h1>{$item.title|wash}</h1>
<a href={$item.url|ezurl} title="{$item.title|wash}">...</a>
<div data-conf="{json_encode( hash( 'id', $item.id ) )|wash}"></div>
```

Exceptions are values that are already safe HTML by design: `$module_result.content`, the output of
`attribute_view_gui` and of rich text rendering.

## Contexts and their escaping

| Output goes into | Escape with |
|---|---|
| HTML text and attributes | `|wash` (templates), `htmlspecialchars( $v, ENT_QUOTES, 'UTF-8' )` (PHP) |
| URLs in templates | `|ezurl` for site paths; `rawurlencode()` for parts of a URL ([Templates and design, part 10](../../../guides/templates-and-design.md#10-encoding-a-value-for-a-url-rawurlencode-and-rawurldecode)) |
| JSON | `json_encode()`; inside a `<script type="application/json">` also replace `<` and `>` |
| SQL | casts and `$db->escapeString()`, or `eZPersistentObject` conditions |
| Shell commands | avoid; otherwise `escapeshellarg()` |
| CSV for spreadsheets | prefix values starting with `=`, `+`, `-`, `@` (the kernel's CSV export does) |
| Log lines | strip line breaks; never log secrets |

## Common mistakes

- `{$value}` without `|wash` because "it is only a number": a later change makes it text.
- Trusting a hidden form field.
- Building a redirect from `$_SERVER['HTTP_REFERER']`.
- Escaping on input instead of on output (double escaping, or none in a new output).
- A JSON value printed into an inline script: use a data block or attribute instead.

## Related pages

- [Forms, CSRF protection and HTTP input](forms-csrf-and-http-input.md)
- [Security for applications](security-for-applications.md)
- [Security standard](../../../standards/security.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
