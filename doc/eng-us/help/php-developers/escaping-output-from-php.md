---
title: Escaping output from PHP
audience: php-developer, developer
category: php-development
order: 340
summary: How to escape values that PHP code writes into HTML, attributes, URLs, JSON, CSV, shell commands and logs - htmlspecialchars() with explicit flags for PHP 8.0, the wash operator and why templates are the better place, data attributes instead of inline scripts, and the mistakes that cause cross-site scripting.
keywords: escaping, output escaping, XSS, cross-site scripting, htmlspecialchars, ENT_QUOTES, ENT_SUBSTITUTE, ENT_HTML401, wash, json_encode, JSON_HEX_TAG, rawurlencode, escapeshellarg, CSV injection, data attributes, Content Security Policy
type: help
---
# Escaping output from PHP

This page is for PHP developers whose code writes values into output: HTML from a view or operator, JSON from an
endpoint, CSV from an export, a shell command, a log line. The rule is simple: escape every value where it leaves your
code, in the format of the place it goes. The template-side view of the same rule is
[Input validation and output escaping](../creators/app-creators/input-validation-and-escaping.md).

## Contents

1. [Prefer templates](#prefer-templates)
2. [HTML from PHP](#html-from-php)
3. [Attributes, URLs and data for scripts](#attributes-urls-and-data-for-scripts)
4. [JSON](#json)
5. [CSV, shell and logs](#csv-shell-and-logs)
6. [The context table](#the-context-table)
7. [Mistakes that cause cross-site scripting](#mistakes-that-cause-cross-site-scripting)

## Prefer templates

HTML belongs in templates, where `|wash` escapes each value at the point it is printed:

```text
<h2>{$item.title|wash}</h2>
<a href={concat( 'acmereport/item/', $item.id )|ezurl} title="{$item.title|wash}">{'Open'|i18n( 'extension/acmereport' )}</a>
```

A view passes values to the template (`$tpl->setVariable()`), not HTML. Then the escaping is visible in one place and a
reviewer can check it.

## HTML from PHP

When PHP must build markup (a template operator that returns HTML, an error message, an e-mail body), escape each value
with explicit flags:

```php
function acmeEscape( $value )
{
    return htmlspecialchars( (string)$value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8' );
}

$html = '<span class="acme-status">' . acmeEscape( $item->attribute( 'status' ) ) . '</span>';
```

Pass the flags every time. PHP 8.1 changed the default to `ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401`; PHP 8.0's default
leaves `'` unescaped, so a value inside a single-quoted attribute would break out on PHP 8.0. The kernel passes the flags
explicitly in `wash` and 27 other files since 6.0.15 ([PHP 8.0 support](../../bc/6.0/php-8.0-support.md)).

## Attributes, URLs and data for scripts

- **Attributes**: quote every attribute and escape the value as above.
- **URLs**: build site URLs with `eZURI::transformURI()` (PHP) or `|ezurl` (templates); encode a part of a URL with
  `rawurlencode()`, and only allow `http` and `https` for addresses that came from input.
- **Data for JavaScript**: never print values into an inline `<script>`. Put them in a `data-*` attribute or a
  `<script type="application/json">` block that your external script reads. The admin's Content Security Policy refuses
  inline scripts and `style=""` ([Content Security Policy for your code](../creators/app-creators/content-security-policy.md)).

```php
$attr = acmeEscape( json_encode( array( 'id' => (int)$item->attribute( 'id' ) ) ) );
$html = '<div class="acme-chart" data-conf="' . $attr . '"></div>';
```

## JSON

```php
header( 'Content-Type: application/json; charset=utf-8' );
echo json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
```

The `JSON_HEX_*` flags make the output safe even if it ends up inside HTML. Set the `Content-Type`, so a browser never
renders the answer as HTML. Through ezjscore or REST, return arrays and let the layer encode them.

## CSV, shell and logs

| Output | Escape |
|---|---|
| CSV opened in spreadsheets | `fputcsv()` for quoting; prefix values starting with `=`, `+`, `-` or `@` so they are not run as formulas (the kernel's CSV export does) |
| shell commands | avoid them; otherwise `escapeshellarg()` for every argument |
| log lines | remove line breaks (`str_replace( array( "\r", "\n" ), ' ', $value )`), so one entry cannot fake another; never log secrets or personal data |
| e-mail headers | remove line breaks from every value |

## The context table

| Output goes into | In PHP | In templates |
|---|---|---|
| HTML text | `htmlspecialchars( $v, ENT_QUOTES \| ENT_SUBSTITUTE \| ENT_HTML401, 'UTF-8' )` | `\|wash` |
| HTML attribute | the same, attribute quoted | `\|wash`, attribute quoted |
| site URL | `eZURI::transformURI()` | `\|ezurl` |
| part of a URL | `rawurlencode()` | `\|rawurlencode` |
| JSON | `json_encode()` with `JSON_HEX_*` | `json_encode()\|wash` in an attribute |
| SQL | `$db->escapeString()`, casts | (no SQL in templates) |

## Mistakes that cause cross-site scripting

- A number printed without escaping "because it is a number": a later change makes it text.
- `htmlspecialchars( $v )` without flags in code that must run on PHP 8.0.
- Escaping on input and storing escaped text: double escaping in one place, none in the next output that forgets.
- Building HTML in a view and passing it to a template that prints it raw.
- A value from the request echoed in an error message.
- A JSON value printed inside an inline script.

## Related pages

- [Security in PHP code](security-in-php-code.md)
- [Writing code that runs on PHP 8.0](writing-code-that-runs-on-php-8-0.md#defaults-that-changed-html-escaping)
- [String template operators](../../features/6.0/string-template-operators.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
