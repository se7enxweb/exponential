# Smarty templates: security and escaping

This page is for template developers and reviewers of `.stpl` templates. It explains what is escaped and what is not,
how `|wash` compares with Smarty's `|escape` and `|raw`, how to print values that come from visitors, what Smarty can
reach that a `.tpl` cannot and the security policy that closes it, how forms keep their token, and what never
belongs in a cache block. The short version: a `.stpl` escapes nothing by itself, exactly like a `.tpl`; wash every
value that is not markup you made.

## Nothing is escaped automatically

The template system prints values as they are, and the bridge does not turn on Smarty's automatic escaping either
(Smarty's `escape_html` stays off). So

```smarty
<h1>{$node.name}</h1>
```

prints the name as it is stored, including any `<`, `&` or `"`. Content names, attribute texts, search words and
everything from the request must be escaped where they are printed:

```smarty
<h1>{$node.name|wash}</h1>
<a href={$node.url_alias|ezurl} title="{$node.name|wash}">{$node.name|wash}</a>
```

What does not need `|wash`, because it is markup made to be printed:

- the output of a template function: `{attribute_view_gui attribute=$node.data_map.body}` renders rich text through
  its datatype's template, which escapes what it must. Washing it would show the tags as text;
- the output of an included template (`{expinclude}`) and `$module_result.content` in a pagelayout;
- `ezurl`, `ezroot`, `ezdesign` and `ezimage` output for addresses you control (see "Addresses" below).

## wash, and Smarty's escape and raw

`|wash` is the template operator; it is what the converter keeps and what behaves the same in both engines.

| Form | Does |
|---|---|
| `{$x\|wash}` (`wash:'xhtml'`) | `htmlspecialchars()` with `ENT_QUOTES`: `<` `>` `&` `"` `'` become entities. For text in elements and in quoted attributes |
| `{$x\|wash:'email'}` | the same, then `.` and `@` replaced by `[WashSettings] EmailDotText` and `EmailAtText` of `template.ini`, against address harvesting |
| `{$x\|wash:'javascript'}` | a value for a JavaScript string literal: quotes, backslashes, line breaks and `</script>` made safe. Put it inside quotes: `var t = '{$x\|wash:'javascript'}';` |
| `{$x\|wash:'pdf'}` | for the PDF export: entities decoded, spaces and line breaks as PDF directives. Never for HTML |

From the template engine's tests, the same in `.stpl`: `{'<a>'|wash:'xhtml'}` prints `&lt;a&gt;`;
`{'a</script>\b'|wash:'javascript'}` prints `a\x3C/script\x3E\\b`.

Smarty's own modifiers exist too:

- `|escape` (`escape:'html'`, `'htmlall'`, `'url'`, `'quotes'`, `'javascript'`, `'mail'` ...) is Smarty's. `escape:'html'`
  gives nearly what `wash` gives, but it is a second, different set of rules: use `wash` so `.tpl` and `.stpl` agree,
  and never both on one value (the result would be escaped twice: `&amp;lt;`).
- `|raw` marks a value as not to be escaped by Smarty's automatic escaping. Since that is off, `|raw` changes nothing;
  do not read it as a promise that a value is safe or unsafe.
- `{$x nofilter}` has no effect either, for the same reason.

## Values from visitors

Everything a visitor sends is untrusted: the address and its view parameters, the query string, posted fields,
cookies, the search text. Wash it where it is printed, in the context it is printed in:

```smarty
{$search_text_html = $search_text|wash}
<p>{'You searched for %text.'|i18n:'design/mysite':expvoid():(['%text' => $search_text_html])}</p>
<input type="text" name="SearchText" value="{$search_text|wash}" />
{if is_set($view_parameters.tag)}<p>Tag: {$view_parameters.tag|wash}</p>{/if}
```

For `ezhttp`, write the call of the `.tpl` and wash the result: `{ezhttp('SearchText', 'get')|wash}` (the operator
takes its name as a parameter, see the [operator reference](smarty-templates-operator-reference.md)).

Smarty can also read the request directly: `$smarty.get`, `$smarty.post`, `$smarty.cookies`, `$smarty.server`,
`$smarty.request`, `$smarty.session` and `$smarty.env` are PHP's `$_GET`, `$_POST` ... with no checks at all. Prefer
the view's variables and `ezhttp`; if you use them, wash every value. The security policy below refuses them.

### In attributes, URLs, scripts and styles

| Context | Write |
|---|---|
| Text of an element | `{$x\|wash}` |
| A quoted attribute | `title="{$x\|wash}"`; always quote the attribute |
| A link to content | `href={$node.url_alias\|ezurl}` (quotes included) |
| A link built from a visitor's value | build the path from washed or validated parts; a value inside a query string: `{$x\|rawurlencode}` |
| An address an editor typed (a link field, a block setting) | check the scheme: `explayouts_safe_url` (Exponential Layouts) returns only safe addresses |
| A JavaScript string | `'{$x\|wash:'javascript'}'` |
| JSON in a `<script>` block | encode it, and keep `<` out of it: the media port turns `<` and `>` into `<` and `>` after encoding (`templates/pagelayout/script_bottom.stpl`) |
| CSS | never from a visitor; colors and classes from editors through `explayouts_safe_color`, `explayouts_safe_css_class` |

## What Smarty can do that a .tpl cannot

Without the security policy (`[SmartySettings] Security=disabled`; the default is `enabled`) a `.stpl` can use every
Smarty feature,
including:

- `{fetch file='...'}`: Smarty's tag that reads a local file or a URL (not the module `fetch()`);
- `$smarty.server`, `$smarty.env`, `$smarty.cookies`, `$smarty.session` (see above), `$smarty.const` and PHP
  constants;
- `{math}`, `{eval}`, `{debug}`, `{config_load}`, and Smarty's `{include file=...}` of any `.stpl` in the designs'
  template directories;
- static classes (`{eZSys::rootDir()}`, `{eZUser::currentUser()}`), and methods of the objects it is given:
  `{$node->expSmartyObject()->remove()}` reaches the content object itself.

So a `.stpl` is code, as much as a PHP file. Only trusted developers should write them, they should be reviewed like
code, and the right to edit templates in the administration (the `visual` module's template editor, which can edit a
`.stpl` that an override rule already uses) must be given only to people you would let change PHP. The same is true of
`.tpl` files, which can read every setting with `ezini` and fetch any content the visitor may read; Smarty widens it
to the file system and to PHP.

## The Smarty security policy (proposal)

`[SmartySettings] Security=enabled` in `settings/smarty.ini` (or an override) turns on a Smarty security policy
(`expSmartySecurity`, a `Smarty\Security`), configured by the `Security*` settings next to it. It is **off by
default** for now; this section is the proposal to decide on.

### What it refuses

A template that uses any of these does not compile; the renderer logs Smarty's message ("tag 'eval' disabled by
security setting", "special variable '$smarty.server' not allowed by security setting" ...) and the page shows nothing
in its place (an HTML comment with debug output on).

| Feature | Setting | Default |
|---|---|---|
| `{eval}`, `{fetch file=...}`, `{math}`, `{debug}`, `{config_load}`, `{html_image}` | `SecurityDisabledTagList[]` | these six |
| Smarty tags not in the allowed list | `SecurityAllowedTagList[]` (empty: all but the disabled ones) | the control tags, `capture`, delimiters, `block`/`extends`, `include`, `function`/`call`, `assign`/`append`, `nocache`, `setfilter`, `counter`, `cycle`, `textformat`, `mailto`, the `html_*` form helpers |
| the modifier `debug_print_var` | `SecurityDisabledModifierList[]` | `debug_print_var` |
| `$smarty.get`, `.post`, `.cookies`, `.session`, `.server`, `.env`, `.request`, `.const`, `.template_object` | `SecurityDisabledSpecialVariableList[]` | these nine |
| PHP constants (`{if PHP_VERSION}`, `$smarty.const`) | `SecurityConstants` | `disabled` |
| static classes, methods and properties (`{eZSys::rootDir()}`) | `SecurityStaticClassList[]` (empty: none) | none |
| stream wrappers as templates (`{include file='php://...'}`) | `SecurityStreamList[]` (empty: none) | none |
| `->` in a tag: methods and properties of objects | `SecurityObjectAccess` | `disabled` |
| templates outside the template directories of the design bases | `SecurityTrustedDirList[]` adds directories | none added |

`$smarty.template_object` is refused because it reaches the Smarty instance, which could switch the policy off;
`->` because every value with attributes is an object (`$node->expSmartyObject()` is the content object, with
`remove()`, `store()` ...), and `ezhttp()` without parameters returns the HTTP tool. Templates read attributes with
dots (`$node.name`), which stays allowed: it calls `attribute()` only.

### What it allows

- Smarty's control structure and the tags listed above; `{$x = ...}` (assign) and `{call}` always.
- Every Smarty modifier except the disabled ones (`|upper`, `|count`, `|escape`, `isset()`, `|default` ...).
- **Every operator, function and block of the template system**, as a `.tpl` may use them: `{$x|wash}`,
  `{concat(...)}`, `{fetch('content', 'node', ...)}`, `{attribute_view_gui ...}`, `{expinclude}`, `{expcacheblock}`,
  `{expset}` ... A disabled list may name one of them too.
- The functions a template defines with `{function}`.

The policy is checked when a template is compiled. Templates compiled under it are kept apart
(`<cache dir>/smarty/compile-secure`), so turning it on recompiles every template once, and a template compiled
without it is never run with it. At run time it costs one check of the directory of each template file Smarty loads.

### Which templates would break

- **None of the media port.** All 516 templates of `extension/sevenx_themes_media/design/smarty_media` compile under
  the default lists (checked by compiling each one with and without the policy). A survey of the design: its Smarty
  tags are `if`/`elseif`/`else`, `foreach`, `capture`, `ldelim`/`rdelim`, `break`, `continue` and assignments; its
  Smarty modifiers `default` and `in_array`; everything else is a template operator or function; no `$smarty.*`, no
  `::`, no `->`. `ezhttp(null, ...)`, `ezmodule(null, ...)` and `language_switcher(null, ...)` are operator calls.
- **None of the converter's output.** Every template engine and operator case of the parity tests
  (`expSmartyOperatorParityTest`, 453 cases) gives the same output with the policy on.
- **Hand-written templates** that use what the table refuses: `$smarty.get`/`.server`/`.cookies` (use `ezhttp()` or
  the view's variables), `{fetch file=...}`, `{math}` (use `sum`, `sub`, `mul`, `div`), `{eval}`, static classes or
  `->` (write a template operator), PHP constants, and `{include file=...}` or
  `{exp_smarty_render( '<file>' )}` of a file outside the design bases' template directories (add the directory to
  `SecurityTrustedDirList[]`, or include it through the design with `{expinclude uri='design:...'}`).

### What it does not cover

The template system's own operators stay as powerful as in a `.tpl`: `fetch()` reads any content the visitor may
read, `ezini()` any setting, `ezhttp()` the request. The policy closes what Smarty adds on top of that (the file
system, PHP functions and classes, the request outside the template system), not what a `.tpl` can do anyway; only
trusted people should still write templates.

### Recommendation

The policy is on by default (`Security=enabled` in `settings/smarty.ini`). It was switched on after the media port
was checked under it: the 516 templates compile, the parity cases pass, and the pages of the `.tpl` and `.stpl`
siteaccesses stayed identical. A site that needs a refused feature turns that one feature back on in its override
(`SecurityDisabledTagList[]` restated without `math`, a class in `SecurityStaticClassList[]`,
`SecurityObjectAccess=enabled`) rather than switching the policy off. Only if a site cannot do without several of
them, turn it off for that siteaccess in `settings/siteaccess/<siteaccess>/smarty.ini.append.php`:

```ini
[SmartySettings]
Security=disabled
```

After a change to these settings, deploy (the PHP workers keep the Smarty instance of their process).

## Forms and the form token

The `ezformtoken` extension protects forms against cross-site request forgery. It works on the page's output, after
all templates ran: for a signed-in visitor it adds a hidden field `ezxform_token` to every `<form ... method="post">`,
puts the token in `<meta name="csrf-token">` in the `<head>`, replaces the text `@$ezxFormToken@` with the token, and
marks the page `Cache-Control: private`. Because this happens on the output, it works for `.stpl` and `.tpl` alike,
and also for a form that comes out of an `{expcacheblock}`: the token is added on every response, never kept in the
cache. What you need to do:

- write `method="post"` on POST forms (any case) so the field is added;
- for a request made from JavaScript, read the token from the meta tag (`csrf-token`) and send it as the
  `ezxform_token` field or the header your code uses;
- never print a token yourself into a template.

## Never in a cache block

An `{expcacheblock}` stores its output for every visitor whose keys match. Never put into one:

- `csp_nonce()`: the nonce changes on every request, and a stored one no longer matches the page's
  `Content-Security-Policy` header, so the browser blocks the script;
- the visitor's name, basket, messages or anything per user, unless the block is keyed by the user (and then think
  twice: a key per user fills the cache). Use the placeholder pattern of
  [Caching and performance](smarty-templates-caching.md#the-page-block-with-placeholders);
- anything permission-dependent without the visitor's roles and limitations in the keys
  ([Keys for signed-in visitors](smarty-templates-caching.md#keys-for-signed-in-visitors)).

## Review checklist for a .stpl

- Every printed value that is not markup is `|wash`ed (or `wash:'javascript'` in scripts), once.
- No `$smarty.get`, `$smarty.post` ... unwashed; prefer view variables and `ezhttp`.
- No `{fetch file=...}`, `{eval}`, `{math}` with values from visitors.
- It compiles with `Security=enabled` (no `$smarty.server`, `::`, `->`, `{eval}`, `{fetch file=...}`).
- Attributes are quoted.
- Addresses from editors go through a safe-URL check.
- Cache blocks hold nothing per visitor and are keyed by roles where content depends on permissions.
- No inline `<style>` or `style=""` (the design rules of the project); the CSP of the site may forbid them anyway.

## Related pages

- [Content Security Policy](../features/6.0/content-security-policy.md)
- [Operator reference](smarty-templates-operator-reference.md), [Caching and performance](smarty-templates-caching.md)
- [Security and audit](security-and-audit.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
