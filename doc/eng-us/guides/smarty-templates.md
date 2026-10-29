# Smarty templates: writing and running .stpl templates

This guide is for template developers who want to write templates in Smarty 5 syntax in an Exponential design. You
will check that Smarty is available, put a first `.stpl` on a page with an override rule, and learn what a `.stpl`
sees and can do: variables and how they flow back, Exponential objects, includes, loops, template functions,
blocks, caching, debugging and the differences from `.tpl`. Follow parts 1 to 3 top to bottom (about fifteen
minutes); the rest is reference to come back to. The feature page is
[Smarty templates (.stpl) next to .tpl](../features/6.0/smarty-templates.md).

Two more pages complete this one:

- [Smarty templates: operators and fetch functions](smarty-templates-operators.md)
- [Smarty templates: the converter and porting a design](smarty-templates-converter.md)

Every command runs from the installation root. When you are logged in as `root`, add `--allow-root-user` (the
examples write it out). You need to know how a `.tpl` is found and overridden: [Templates and design](templates-and-design.md)
explains it.

## 1. What happens to a .stpl

The template system finds templates through its resources (`design:` and the file resource). When a template name
resolves to a file ending in `.stpl`:

1. The resource does not hand the template system the file's text. It hands it a one-line stub,
   `{exp_smarty_render( '<file>' )}` (`expSmartyRenderer::stubFor()`, called from
   `eZTemplateFileResource::handleResourceData()`).
2. The template system parses, compiles and caches the stub like any template, under a compiled-template key of its
   own (`expSmartyRenderer::cacheKey()`, called from `eZTemplateFileResource::cacheKey()`), so a stub never shares a
   compiled file with a parse of the Smarty text.
3. On every request, from the parsed stub and from the compiled one, the operator `exp_smarty_render` renders the
   file with Smarty 5, passing the variables of the template namespace it was called in.

So the template caches hold only the stub. What a request rendered is never stored in them. Smarty keeps its own
compiled templates (PHP, never output: Smarty's output caching is off) in `<cache directory>/smarty/compile/`, for
example `var/site/cache/smarty/compile/`.

The suffix decides. A `.tpl` is never sent to Smarty, and a request for `node/view/full.tpl` never finds a `.stpl`
by itself: a `.stpl` is used when an override rule points at it, when an include names it, or when a template
system call names it (`$tpl->fetch( 'design:parts/x.stpl' )`).

The stub operator can also be called directly from a `.tpl`, with a path relative to the installation root:

```
{exp_smarty_render( 'design/standard/templates/smarty/demo_full.stpl' )}
```

## 2. Check that Smarty is available

The root `composer.json` requires `smarty/smarty ^5`, so an installation set up with Composer has it in `vendor/`:

```bash
ls vendor/smarty/smarty/src/Smarty.php
```

Expected: the path is printed. If the installation has no Composer `vendor/`, put a Smarty 5 checkout somewhere and
name it in `settings/override/smarty.ini.append.php`:

```ini
[SmartySettings]
# the directory that holds src/Smarty.php, relative to the installation root or absolute
LibraryPath=lib-external/smarty
```

The library is loaded from Composer's autoload first and from `LibraryPath` only when the class `\Smarty\Smarty` is
not there; Smarty's `src/functions.php` is loaded from the same directory. Smarty 3 and 4 are not supported.

The other settings of `settings/smarty.ini`:

| Key | Default | Meaning |
|---|---|---|
| `CompileCheck` | `enabled` | Smarty compares a `.stpl` with its compiled file and recompiles a changed one. `disabled` never checks: faster, for production, but then clear the template cache after every template change. |
| `Debugging` | `disabled` | `enabled` turns on Smarty's debug console. |
| `FunctionList[]` | `attribute_view_gui`, `attribute_edit_gui`, `attribute_result_gui`, `attribute_pdf_gui`, `node_view_gui`, `content_view_gui`, `content_version_view_gui`, `class_attribute_view_gui`, `class_attribute_edit_gui`, `related_view_gui`, `shop_account_view_gui` | Exponential template functions always callable as Smarty tags (part 7). |

Nothing has to be activated: `settings/site.ini` registers the operator through
`[TemplateSettings] AutoloadPathList[]=lib/smarty/classes/` and the Smarty cache through `[Cache_smarty]`.

## 3. A first .stpl on a page

Use the demo the kernel ships, `design/standard/templates/smarty/demo_full.stpl`, as the full view of folders. In
`settings/siteaccess/<siteaccess>/override.ini.append.php`:

```ini
[folder_smarty_demo]
Source=node/view/full.tpl
MatchFile=../../templates/smarty/demo_full.stpl
Subdir=templates
Match[class_identifier]=folder
```

`MatchFile` is looked up below `override/templates/` of every design; the demo is in `templates/`, hence the
`../../templates/` (for your own `.stpl` in `override/templates/` you write the plain path, `MatchFile=full/folder.stpl`).

Clear the caches that hold the override list and the rendered views:

```bash
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-id=content --allow-root-user
```

Open a folder on the site. Expected: the page shows the folder's name, the line "Rendered by Smarty 5.x for node
&lt;id&gt; (folder)", and a list of up to five children. The demo is short:

```smarty
<div class="content-view-full smarty-demo" data-smarty-node="{$node.node_id}">
    <h1>{$node.name|wash}</h1>
    <p class="smarty-demo-marker">Rendered by Smarty {$smarty.version} for node {$node.node_id} ({$node.object.class_identifier|escape})</p>
    {if isset($node.data_map.short_description) && $node.data_map.short_description.has_content}
        <div class="attribute-short">{attribute_view_gui attribute=$node.data_map.short_description}</div>
    {/if}
    {$children = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'limit' => 5, 'sort_by' => ['priority', true]])}
    {include file='smarty/demo_children.stpl' children=$children}
</div>
```

Load a second folder: it shows its own node id. The second load runs the compiled stub, which renders the `.stpl`
again with the new `$node`. Edit the `.stpl` and reload: with `CompileCheck=enabled` the change is there at once
(clear the content cache if the view cache serves the page). Remove the override rule when you are done.

## 4. Variables

### What a .stpl sees

A `.stpl` sees the variables of its root namespace (what a `.tpl` reads as `$name`) and of its current namespace,
exactly what a `.tpl` in the same place would see:

- a node view: `$node`, `$view_parameters` and the other variables of that view (a form view also has `$validation`);
- a pagelayout: `$module_result`, `$site` and the rest of the pagelayout variables;
- an included template: its include parameters and, without `name=`, the variables of the including template;
- `$smarty` is Smarty's own (`{$smarty.version}`), so a template variable named `smarty` is not passed in.

Only names Smarty can use are passed: letters, digits and underscores, not starting with a digit. A template variable
with another name (a hyphen, for example) is not visible in a `.stpl`.

### Defining, changing and removing

Smarty's assignment is all a `.stpl` needs. When the template is done, every variable it defined or changed is
written back to its root namespace, and a variable set to `null` is removed there:

| `.tpl` | `.stpl` |
|---|---|
| `{def $title = $node.name}` | `{$title = $node.name}` |
| `{set $title = 'Other'}` | `{$title = 'Other'}` |
| `{undef $title}` | `{$title = null}` |
| `{undef}` (every variable of the root namespace) | `{expundefall}` |
| `{set-block scope=root variable=subject}...{/set-block}` | `{capture assign=subject}...{/capture}` |

That is what makes a `.stpl` mail template work: the module reads `$subject` after the template ran. From the ported
media design, `templates/emails/info_collection/email.stpl`:

```smarty
{capture assign=subject}
{ezini('SiteSettings', 'SiteName')} – {$content.name|wash} [{$collected_fields.sender_email.value}]{/capture}
```

Rules worth knowing:

- Loop variables are variables too: after `{foreach $children as $child}` the last `$child` is written back as well.
- Names that start with two underscores (`$__loop1`, `$__sequence2`, the helpers of converted templates) stay in
  Smarty and are never written back.
- Variables are written back to the namespace the `.stpl` was rendered in, not further up.

### Scopes and namespaces: {expset}

For the cases a plain assignment cannot express, `{expset}` writes into the template system's namespaces:

```smarty
{expset name='persistent_variable' value=['left_menu' => false] scope='global'}
```

| Parameter | Meaning |
|---|---|
| `name` | the variable name (required) |
| `value` | its value |
| `scope` | `relative` (the default: the current namespace), `root` (the template's root namespace) or `global` (a global namespace) |
| `namespace` | a namespace below the scope; with `scope='global'`, the global namespace of that name |

This is the `.tpl` form `{set scope=global persistent_variable=...}`. When the target is the template's own root
namespace, the Smarty variable is set too.

To read a variable of a global namespace (the `.tpl` forms `$#name` and `$#Namespace:name`), use `expvar()`:

```smarty
{if and(is_set(expvar('extra_data', 'Header')), exp_is_array(expvar('extra_data', 'Header')))}
```

## 5. Exponential objects

Exponential objects (nodes, objects, attributes, anything with `attribute()` and `hasAttribute()`) reach Smarty
wrapped in a read-only proxy (`expSmartyObjectProxy`), so Smarty's dot syntax reads attributes as a `.tpl` does:

```smarty
{$node.name|wash}
{$node.object.class_identifier}
{$node.data_map.body.has_content}
{$node.path_array|exp_implode:'/'}
{if isset($node.data_map.image)}...{/if}
{$node->attribute('name')}
```

- `isset($node.data_map.image)` is true when the attribute exists and is not `null`.
- Attribute values are wrapped again on the way out, and arrays of objects element by element, so
  `$node.data_map.body.content` and `$node.children` keep working at any depth.
- Method calls (`$node->attribute('name')`) are passed to the object; proxies among the arguments are unwrapped.
- Whenever a value goes back to Exponential (an operator, a template function, a fetch, a write-back), the proxy is
  replaced by the real object again.
- The proxy is read-only: `{$node.name = 'x'}` changes nothing on the object.
- A proxy printed on its own prints the object's `__toString()`, or nothing.
- Iterate arrays (`$node.data_map`, `$node.children`, a fetch result), not objects: Smarty's `{foreach}` does not
  iterate a proxy.
- An attribute of a value that is not a variable needs `expattr`, since Smarty has no syntax for it:

  ```smarty
  {$__loop901 = (fetch('shop', 'basket'))|expattr:'items'}
  <html lang="{(((ezini('RegionalSettings', 'Locale'))|locale)|expattr:'http_locale_code')|wash}">
  ```

## 6. Includes

There are two kinds, and they behave differently.

**`{expinclude}` is the template system's `{include}`.** The URI goes through the design resource and the override
rules, the included template may be a `.tpl` or a `.stpl`, and the other parameters become its variables:

```smarty
{expinclude uri='design:pagelayout/head/title.tpl'}
{expinclude uri='design:content/views/payload/ng_contact_form.tpl' node=$node referer=$requested_uri_string form_class='embed-form js-form-embed'}
{expinclude uri=(concat('design:explayouts/block/list/', $list_view_type, '.tpl')) block=$block}
```

- Without `name=`, the included template shares the including template's namespace: it sees all of its variables,
  including those the `.stpl` assigned so far, and what it defines is visible after the include, as with `{include}`
  in a `.tpl`.
- With `name=`, it runs in a namespace of that name below the current one.
- The parameters replace variables of the same name for the included template only; afterwards the old values are back.
- A missing `uri` stops the render with "expinclude: missing parameter 'uri'".
- **A `.stpl` included without `name=` runs as a Smarty subtemplate.** The bridge first asks the design resource
  which file the URI stands for (`expSmartyRenderer::resolveURI()`: the same resource, override rules and keys the
  template system would use, without loading the file). When that file is a `.stpl`, it is rendered directly as a
  Smarty subtemplate of the including one (`expSmartyRenderer::renderSubtemplate()`), not through the template system,
  its stub and a second render. It sees the including template's variables, its parameters are its own, and what it
  defines, changes or sets to `null` is visible in the including template afterwards, exactly as through the template
  system. This is the main reason a `.stpl` page is fast: the media front page renders 37 templates through the bridge
  instead of 266. With debug output on, a subtemplate rendered this way is in the debug bar's template tree below the
  template that included it, as an `{include}` of a `.tpl` is; without it, nothing is noted.

**`{include file=...}` is Smarty's own include.** It searches `override/templates/` and `templates/` of every design
of the siteaccess, in the design fallback order, and includes another `.stpl` without the template system:

```smarty
{include file='smarty/demo_children.stpl' children=$children}
```

Use it only for `.stpl` files: it applies no override rule, and a `.tpl` included this way would be read as Smarty.
Variables the included file assigns stay in it (Smarty's include scope).

## 7. Loops and conditions

Smarty's own `{if}`, `{elseif}`, `{else}`, `{foreach}`, `{foreachelse}`, `{for}`, `{while}`, `{break}` and
`{continue}` are used as they are. Conditions can use Smarty's operators (`==`, `&&`, `!`) or Exponential's
(`and(...)`, `$a|eq:2`), see [the operators guide](smarty-templates-operators.md).

One rule catches everyone who ports a `.tpl`: **`{foreach}` iterates a variable, not an expression.** Assign a
computed source first:

```smarty
{* refused: "missing 'from' attribute" *}
{foreach ($validation.attributes) as $item}...{/foreach}

{* works *}
{$__loop1 = fetch('content', 'list', ['parent_node_id' => $node.node_id])}
{foreach $__loop1 as $child}...{/foreach}
```

Helpers for what a `.tpl` loop can do and a Smarty loop cannot:

| Helper | Use |
|---|---|
| `expslice` | `{foreach}` with offset, max and reverse: `{$__loop1 = $list|expslice:2:5:false}` |
| `expsectionloop` | the items of a `{section loop=...}`: an array, or a number n for 1..n, with offset, max and reverse: `{$__loop1 = ($path)|expsectionloop:0:null:false}` |
| `exprange` | a range that counts up or down, for `{for}`: `{foreach exprange(1, 5) as $r_star}` |
| `expsequence` | one value of a sequence by position: `{$row_class = $__sequence1|expsequence:$item@index}` |
| `expcolumn` | one attribute of every item of an array: `$items|expcolumn:'id'` |

The `.tpl` `{delimiter}` is written as `{if !$item@first}...{/if}` at the top of the loop body. `{section}`,
`{switch}`, `{let}`, `{default}` and `{do}` have no Smarty tag; the converter writes them as the constructs above
([the converter guide](smarty-templates-converter.md#2-what-it-writes) has the table).

## 8. Template functions as tags

Every function and block with its parameters: [Template functions and blocks](smarty-templates-functions.md).

Every template function of the template system that is not template structure can be called as a Smarty tag with
named parameters. The output is printed, or with `assign=` put into a variable:

```smarty
{attribute_view_gui attribute=$node.data_map.body}
{node_view_gui view='line' content_node=$child}
{content_view_gui view='embed' content_object=$object}
{attribute_view_gui attribute=$node.data_map.body assign=body_html}
```

- The functions in `[SmartySettings] FunctionList[]` are always bridged as tags. Any other function the template
  system has registered in the siteaccess is bridged too: as a tag when it has no body, as a block when it has one.
- The tags the template system uses for structure are never bridged (`if`, `foreach`, `section`, `def`, `set`,
  `let`, `default`, `switch`, `include`, `set-block`, `append-block`, `cache-block`, `run-once`, `literal`, `delimiter`
  and the like); use the Smarty equivalents in this guide.
- Parameter names must be letters, digits and underscores, starting with a letter, or the call prints nothing (an HTML comment with debug output on) and logs
  "invalid parameter name". A function whose name has a hyphen (`debug-log`) cannot be a Smarty tag.
- Smarty's own tags win over an Exponential function of the same name (`include`, `capture`, `assign`, `cycle`, ...).
- The templates the functions use (a datatype's view template, a node's line view) may be `.tpl` or `.stpl`.
- Each call is a small generated `.tpl` in `<cache directory>/smarty/functions/` that the template system compiles and
  caches like any template, so a function runs exactly as from a `.tpl`.

**Functions with a body are Smarty blocks.** The block's output is rendered by Smarty first and handed to the
function as its children, as one piece of text. The layout block function of Exponential Layouts is the common case,
from the ported full views:

```smarty
{explblock name='content'}
<div class="content-view-full">
    {explblock name='article_body'}
    <div class="full-page-body">...</div>
    {/explblock}
</div>
{/explblock}
```

A function that inspects the structure of its children (rather than their output) sees only that text.

**`{expfetch}`** is a tag form of `fetch()`, for those who prefer attributes:

```smarty
{expfetch assign=children module=content function=list parent_node_id=$node.node_id limit=10}
```

`assign`, `module` and `function` are required; every other parameter becomes a fetch parameter.

## 9. Blocks: set-block, append-block, cache-block, run-once

| `.tpl` | `.stpl` |
|---|---|
| `{set-block variable=x}...{/set-block}` (also `scope=root`) | `{capture assign=x}...{/capture}` |
| `{set-block scope=global variable=cache_ttl}0{/set-block}` | `{expsetblock variable='cache_ttl' scope='global'}0{/expsetblock}` |
| `{append-block variable=scripts}...{/append-block}` | `{expappendblock variable='scripts'}...{/expappendblock}` |
| `{cache-block keys=array($node.node_id) expiry=3600}...{/cache-block}` | `{expcacheblock id=1 keys=[$node.node_id] expiry=3600}...{/expcacheblock}` |
| `{run-once}...{/run-once}` | `{exprunonce id=1}...{/exprunonce}` |

- `{expsetblock}` and `{expappendblock}` take `variable` (required), `scope` (`relative`, `root`, `global`) and `name`
  (a namespace), as `{expset}` does. `{expappendblock}` appends the block's output to an array.
- `{expcacheblock}` is the template system's `{cache-block}`: stored in the template-block cache with the same keys plus
  the siteaccess, expired by `expiry` (seconds), by content changes unless `ignore_content_expiry=true`, and by
  `subtree_expiry` (which also ignores content expiry, as in a `.tpl`). When the block is cached, its body does not
  run. It caches only when `[TemplateSettings] TemplateCache=enabled`.
- `{exprunonce}` outputs its body the first time it runs in a request only.
- Give every `{expcacheblock}` and `{exprunonce}` in a file an `id` of its own: the id and the file name make the key.
  The converter numbers them for you.

## 10. Caching and clearing

The layers in depth, the page block with per-visitor placeholders, keys for signed-in visitors and how to measure:
[Caching and performance](smarty-templates-caching.md).

| What | Where | Cleared by |
|---|---|---|
| The stub, compiled by the template system | `<cache directory>/template/compiled/` | the template caches (`--clear-tag=template`) |
| Smarty's compiled `.stpl` files | `<cache directory>/smarty/compile/` | `[Cache_smarty]`: `--clear-id=smarty`, and with the template caches (`--clear-tag=template`) |
| The generated calls of template functions | `<cache directory>/smarty/functions/` | the same |
| Rendered pages | the content view cache, `{expcacheblock}`, Velocity's response cache | as for `.tpl` pages |

```bash
php bin/php/ezcache.php --clear-id=smarty --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
```

- With `CompileCheck=enabled`, a changed `.stpl` is recompiled on its next render; nothing has to be cleared, except
  the caches that hold rendered pages.
- With `CompileCheck=disabled`, clear the Smarty cache after a template change.
- A new `.stpl`, a new override rule or a renamed file needs the template caches cleared, as for a `.tpl`.

## 11. Debugging

Every message with its cause and fix, and the problems that give no message, are listed in
[Troubleshooting and FAQ](smarty-templates-troubleshooting.md); this part is the short version.

A `.stpl` that fails does not break the page. Its output is replaced, and the reason is logged:

- in the debug output, as an error with the label `smarty` (and in the error log as any debug error);
- on the page, as an HTML comment `<!-- smarty: ... -->` where the template's output would have been, only when debug
  output is on. With debug output off the template prints nothing.

The messages:

| Message | Cause |
|---|---|
| `rendering <file> failed: <reason>` | Smarty could not compile or run the template; the reason is Smarty's message, or one of the `ez*` tags (`expinclude: missing parameter 'uri'`, `expfetch: missing parameter 'module'`, `expset: missing parameter 'name'`, `expsetblock: missing parameter 'variable'`) |
| `template not found: <file>` | the file the stub names is gone (renamed or removed): clear the template caches |
| `the Smarty library is not available (...)` | Smarty 5 is neither in Composer's autoload nor in `LibraryPath` |
| `invalid parameter name for <function>: <name>` | a template function was called with a parameter name that is not a plain name |
| `<!-- smarty: unsupported characters in the template file name -->` | the file name holds a quote, a backslash, a brace or a line break; rename the file |

Smarty's compile errors read `Syntax error in template "<file>"  on line <n> "<the line>" <reason>`. The common ones:

| Reason | Fix |
|---|---|
| `missing 'from' attribute` | a `{foreach}` over an expression: assign it to a variable first (part 7) |
| `unexpected closing tag` | a closing tag without its opening tag: a `.tpl` tag left in a hand port (`{/section}`, `{/let}`, `{/cache-block}`), or a closing tag whose opening tag Smarty did not read as one |
| `unclosed {if} tag`, `unclosed '{if}' tag` | the opposite: a tag that is never closed, or tags nested the wrong way round |
| `unknown modifier 'x'` | no operator `x` in this siteaccess (an extension that registers it is not active there), or a typing error |
| `unknown tag 'x'` | no template function `x`, or a `.tpl` tag such as `{def}` or `{set-block}` left in the file |
| ` - Unexpected "..."` | Smarty syntax error; often a `.tpl` habit: modifier parameters in parentheses (`$x|i18n('ctx')` must be `$x|i18n:'ctx'`), or a brace in CSS or JavaScript (below) |

Two problems that are not compile errors:

- **Braces in CSS and JavaScript.** A `{` followed by anything but white space starts a Smarty tag. Write
  `{ldelim}` (as the converter does: `body {ldelim}margin:0;}`), put a space after the brace, or wrap the code in
  `{literal}...{/literal}`.
- **"Array to string conversion".** An array was printed or passed where text was expected. Most often a Smarty
  modifier ran instead of the Exponential operator of the same name: Smarty's `explode` takes its parameters the
  other way round, its `implode` is deprecated, its `count` counts a string as 1. Use `exp_explode`, `exp_implode`,
  `exp_count` ([the `exp_` prefix](smarty-templates-operators.md#the-exp_-prefix)).

`Debugging=enabled` in `smarty.ini` shows Smarty's debug console with the variables of each template. Turn it off
again; never leave it on for a public site.

## 12. Velocity and persistent workers

Why Velocity makes such a difference for `.stpl` pages, with measurements: [Velocity and PHP-FPM](smarty-templates-performance.md#velocity-and-php-fpm).

Under Velocity, or any server that keeps PHP processes between requests:

- The Smarty instance is kept per process and identified by its configuration: the template directories of the
  designs, the compile directory, `CompileCheck` and `Debugging`. Siteaccesses with other designs get instances of
  their own. The instance is found by the design bases and the cache directory alone, without reading
  `smarty.ini` again, so a change of `CompileCheck` or `Debugging` reaches a worker after a restart (a deploy does).
- How an operator takes its input ([the input rules](smarty-templates-operators.md#how-an-operator-gets-its-input)) is worked out once per operator and process. After
  a change to an operator class, restart the workers (a deploy does).
- Which modifier, function or block a name stands for is looked up once per process (`expSmartyExtension`
  remembers the answer, also a "not found"). An operator or template function that becomes available later (an
  extension activated) is seen after a restart.
- Nothing of one request's variables is kept for the next: each render gets the variables of its own request. The
  values converted for Smarty during a page (`expSmartyRenderer::wrapNamed()`) and the variables captured at the end
  of each template are dropped when the outermost `.stpl` of a render is done.
- Velocity runs as `root`, PHP-FPM as the site's user, and both write `<cache directory>/smarty/`. Smarty creates its
  directories `0777` and its files `0666`, both less the process's umask. If one of them created the directory and the
  other cannot write into it, rendering fails with a "rendering ... failed" error about a file that cannot be written:
  clear the Smarty cache as the site's user (or give the directory to that user) and it is recreated.

## 13. Limits and differences from .tpl

- Smarty 5 only, on PHP 8. The suffix is fixed: `.stpl`.
- Smarty syntax throughout: modifier parameters follow colons (`$x|op:a:b`), never parentheses; array literals are
  `[...]` and `['key' => value]`; `true`, `false` and `null` are literals.
- Smarty's own modifiers and tags win over Exponential operators and functions of the same name; the operators are
  reached with `exp_`.
- No automatic escaping, as in `.tpl`: escape with `|wash` (or Smarty's `|escape`).
- Objects are read-only proxies; iterate arrays, not objects.
- Variables flow back to the namespace the `.stpl` was rendered in; Smarty's `{include file=...}` keeps its variables.
- A template function with a body gets its children as text.
- Template variables whose names are not plain names, and a variable named `smarty`, are not visible.
- Smarty's own output caching is off: page caching stays with Exponential (view cache, `{expcacheblock}`, Velocity's
  response cache).

## Related pages

- New to `.stpl`: [a first .stpl, step by step](smarty-templates-tutorial.md)
- Reference: [every operator](smarty-templates-operator-reference.md), [template functions and blocks](smarty-templates-functions.md), [troubleshooting and FAQ](smarty-templates-troubleshooting.md)
- Topics: [caching and performance](smarty-templates-caching.md), [performance (Speed)](smarty-templates-performance.md), [migrating a design](smarty-templates-migrating.md), [writing operators and functions](smarty-templates-plugins.md), [security and escaping](smarty-templates-security.md)
- [Smarty templates (.stpl) next to .tpl](../features/6.0/smarty-templates.md), the feature page
- [Smarty templates: operators and fetch functions](smarty-templates-operators.md)
- [Smarty templates: the converter and porting a design](smarty-templates-converter.md)
- [Templates and design](templates-and-design.md) and [Caches](caches.md)
- [Velocity: running Exponential in a persistent-worker web server](../features/6.0/velocity-persistent-worker-server.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
