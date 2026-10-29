---
title: Layouts, expui and expai operators
audience: developer, designer, app-creator
category: development
order: 460
summary: The template operators of the Exponential layout extensions (expl_parse, expl_strip, expl_has, expl_first, explayouts_ui_has_template), of expui (exp_config) and of expai (expai_money, expai_tokens, expai_number, expai_retention, expai_json), with parameters, return values, escaping, and examples rendered on a development installation, the expai ones on the admin siteaccess.
keywords: explayouts, expl_parse, expl_strip, expl_has, expl_first, explblock, explayouts_ui_has_template, expui, exp_config, expai, expai_money, expai_tokens, expai_number, expai_retention, expai_json
type: help
---
# Layouts, expui and expai operators

Three Exponential extension families add their own operators. Each exists only where its extension is active:
explayouts, explayouts_ui and expui on the sites and the administration, expai only on the administration siteaccesses
of a development installation (its operators are not registered on `site`).

| Extension | Class and file | Operators |
|---|---|---|
| explayouts | `ExplBlockOperator`, `extension/explayouts/classes/explblockoperator.php` | `expl_parse`, `expl_strip`, `expl_has`, `expl_first` |
| explayouts_ui | `expLayoutsUiTemplateOperators`, `extension/explayouts_ui/classes/explayoutsuitemplateoperators.php` | `explayouts_ui_has_template` |
| expui | `expUITemplateOperators`, `extension/expui/classes/expuitemplateoperators.php` | `exp_config` |
| expai | `expAiTemplateOperators`, `extension/expai/autoloads/expaitemplateoperators.php` | `expai_money`, `expai_tokens`, `expai_number`, `expai_retention`, `expai_json` |

## Contents

1. [explayouts block markers](#explayouts-block-markers): [expl_parse](#expl_parse), [expl_strip](#expl_strip),
   [expl_has](#expl_has), [expl_first](#expl_first)
2. [explayouts_ui](#explayouts_ui): [explayouts_ui_has_template](#explayouts_ui_has_template)
3. [expui](#expui): [exp_config](#exp_config)
4. [expai](#expai): [expai_money](#expai_money), [expai_tokens](#expai_tokens), [expai_number](#expai_number),
   [expai_retention](#expai_retention), [expai_json](#expai_json)

## explayouts block markers

The `{explblock}` template function of explayouts wraps parts of rendered output in comment markers
`<!--expl:s:<name>-->` and `<!--expl:e:<name>-->` (names: a lowercase letter, then lowercase letters, digits or
`_`). The four operators read such output through `ExplBlockParser` (`extension/explayouts/classes/explblockparser.php`),
a parser without kernel dependencies, so they also work on cached HTML. All four take up to two positional parameters
(both `mixed`, optional); the first is used only when there is no input.

### expl_parse

Returns a hash: `blocks` (name => list of the contents of every block of that name, in order) and `stripped` (the
input without any markers). Crossed nesting drops the inner frame; an end marker without a start is ignored; a block
still open at the end runs to the end of the text.

### expl_strip

Returns only the text without markers (`stripped` of `expl_parse`).

### expl_has

Input: the HTML, the result of `expl_parse`, or its `blocks`. Parameter: the block name. `true` when that block exists
and is not empty.

### expl_first

Input: a list (such as `$parsed.blocks.title`): its first element; a string is returned as it is; anything else gives
an empty string.

## explayouts_ui

### explayouts_ui_has_template

| Parameter | Type | Required | Default |
|---|---|---|---|
| `path` | string | yes | empty |

`true` when a template with that path exists in one of the design bases of the siteaccess
(`eZTemplateDesignResource::fileMatch()` on `templates/`). A path containing `..` is always `false`. The layouts UI uses
it to include optional parts only when a design provides them.

```tpl
{def $html = '<div><!--expl:s:title--><h1>Recipes</h1><!--expl:e:title--><!--expl:s:lead-->Fresh food<!--expl:e:lead--></div>'
     $parsed = $html|expl_parse}
1 expl_parse blocks: {foreach $parsed.blocks as $name => $parts}{$name} ({$parts|count}){delimiter}, {/delimiter}{/foreach}

2 expl_parse stripped: {$parsed.stripped}
3 expl_strip: {$html|expl_strip}
4 expl_first: {$parsed.blocks.title|expl_first}
5 expl_has title: {expl_has( $html, 'title' )|choose( 'no', 'yes' )}, footer: {$parsed|expl_has( 'footer' )|choose( 'no', 'yes' )}
6 explayouts_ui_has_template: {explayouts_ui_has_template( 'pagelayout.tpl' )|choose( 'no', 'yes' )} / {explayouts_ui_has_template( 'no/such/template.tpl' )|choose( 'no', 'yes' )} / {explayouts_ui_has_template( '../settings/site.ini' )|choose( 'no', 'yes' )}
```

Output:

```text
1 expl_parse blocks: title (1), lead (1)
2 expl_parse stripped: <div><h1>Recipes</h1>Fresh food</div>
3 expl_strip: <div><h1>Recipes</h1>Fresh food</div>
4 expl_first: <h1>Recipes</h1>
5 expl_has title: yes, footer: no
6 explayouts_ui_has_template: yes / no / no
```

## expui

### exp_config

| Parameter | Type | Required | Default |
|---|---|---|---|
| `options` | array (hash) | no | empty |

Returns the configuration block that the expui JavaScript core reads: `<script type="application/json"
data-exp-config>` with `config` (version, root and www paths, siteaccess, ezjscore call address, preferences address,
form token element id, separator, locale code, HTTP locale and first weekday), `strings` and `prefs`. `options.strings`
adds texts to translate (in the context `extension/expui`) to those of `expui.ini` `[ExpUI] Strings[]`;
`options.prefs` names user preferences to include (names matching `[A-Za-z0-9_.-]{1,64}`). The JSON is encoded with
`<`, `>`, `&` and quotes escaped, so no value can end the script element. A `type="application/json"` block is data, so
it needs no CSP nonce. Print it unwashed; the example washes it only to show the tag:

```tpl
{def $script = exp_config( hash( 'strings', array( 'Close' ) ) )}
starts: {$script|extract_left( 48 )|wash}
ends:   {$script|extract_right( 9 )|wash}
```

Output:

```text
starts: &lt;script type=&quot;application/json&quot; data-exp-config&gt;
ends:   &lt;/script&gt;
```

## expai

The expai operators format the figures of the AI usage pages. Rendered on the `admin` siteaccess:

### expai_money

Parameter `approx` (boolean, default `false`). Formats an amount in dollars (`expAiCost::money()`): small amounts as
`< $0.01`, with `≈` in front when `approx` is true.

### expai_tokens

No parameters. Token counts shortened: `950`, `22k`, `1.2M` (`expAiCost::tokens()`).

### expai_number

Parameter `decimals` (integer, default 0). A number with the locale's separators (`expAiCost::number()`).

### expai_retention

No parameters. A data retention value from the provider settings (`none`, `zdr`, `30d`, `unknown`): `unknown` is
translated, every other value is shown as written.

### expai_json

No parameters. JSON of the input for a `<script type="application/json">` block: `<`, `>`, `&`, `'` and `"` are
escaped as `\u` sequences, and invalid UTF-8 becomes U+FFFD instead of making the whole result empty. A value that
cannot be encoded gives `null` and a warning in the debug log.

```tpl
1 expai_money: {0.0625|expai_money} / {0.004|expai_money} / {0.0625|expai_money( true() )}
2 expai_tokens: {950|expai_tokens} / {21600|expai_tokens} / {1200000|expai_tokens}
3 expai_number: {1234567.891|expai_number( 2 )}
4 expai_retention: {'unknown'|expai_retention} / {'30d'|expai_retention}
5 expai_json: {hash( 'text', '</script><b>"x"</b>', 'n', 3 )|expai_json}
```

Output:

```text
1 expai_money: $0.06 / < $0.01 / ≈$0.06
2 expai_tokens: 950 / 22k / 1.2M
3 expai_number: 1,234,567.89
4 expai_retention: unknown / 30d
5 expai_json: {"text":"\u003C\/script\u003E\u003Cb\u003E\u0022x\u0022\u003C\/b\u003E","n":3}
```

## Related pages

- [ezjscore operators in depth](ezjscore-operators.md) (`json_encode` for the same purpose without the extra escaping)
- [The layouts editor](../layouts-editor.md)
- [Content security policy for applications](../creators/app-creators/content-security-policy.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
