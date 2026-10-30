# Smarty templates: performance (Speed)

This page is for template developers, site builders and operators who want `.stpl` pages to be fast, and for anyone
who wants to know why they are. It tells the whole story of making Smarty templates fast in Exponential: what was
slow at first and why, every change that was made with the reason for it, one change that was deliberately not made,
the numbers before and after, what each cache layer contributes, the page-cache pattern of the media port, how to
measure without fooling yourself, how Velocity and PHP-FPM differ, the settings that matter, a checklist and a list of
pitfalls.

All numbers on this page were measured on a development installation on 28 October 2026, with the media design (the
`.tpl` original in `extension/sevenx_themes_media/design/media/` and its Smarty port in
`.../design/smarty_media/`, 516 templates) served by two siteaccesses that show the same content: `site` with the
`.tpl` design and `smarty` with the `.stpl` design. Addresses below use `https://www.example.com`.

Related reading: [Caching and performance](smarty-templates-caching.md) (the cache layers as a reference) and the
feature page [Smarty templates (.stpl) next to .tpl](../features/6.0/smarty-templates.md#speed).

## In short

- **A `.stpl` page is now faster than the same `.tpl` page**: 18 to 25 % on Apache with PHP-FPM, 32 to 60 % on
  Velocity, for byte-for-byte identical HTML.
- **At first it was much slower.** Every `.stpl` included by a `.stpl` went the whole way through the template system
  and started a new Smarty render with every variable converted and written back; a front page did that 266 times.
- **Six changes** made the difference: includes as Smarty subtemplates, one Smarty instance per set of designs, values
  converted once per page, unchanged variables not written back, name lookups remembered per process, and the
  write-back moved to a post-filter. Operator calls were measured and left alone: they were cheap.
- **The biggest single gain for real visitors is caching**: the media port keeps its whole pagelayout in one
  `{expcacheblock}` and renders only the visitor's own user menu fresh, which brought most Velocity requests to
  0.03 to 0.07 s.
- **Measure carefully**: the debug toolbar's script time, the median of ten requests, the same caches and the same
  debug state for both engines, and a server that is not overloaded.

## The results

Measured with the debug toolbar's script time, the median of 10 requests of each page, with the content view cache
warm and the debug toolbar on for both engines; the `.tpl` and the `.stpl` siteaccess showed byte-for-byte the same
HTML.

| Page | `.tpl`, Apache + PHP-FPM | `.stpl`, Apache + PHP-FPM | `.tpl`, Velocity | `.stpl`, Velocity |
|---|---|---|---|---|
| A category page (`/fitness`) | 0.119 s | 0.097 s (18 % faster) | 0.102 s | 0.069 s (32 % faster) |
| A recipe list (`/recipes`) | 0.102 s | 0.077 s (25 % faster) | 0.093 s | 0.037 s (60 % faster) |
| The front page | — | 0.104 s | — | 0.036 s |

- The `.tpl` front page was answered from the full-page cache in these runs, so it had no script time of its own to
  compare (see [why](#the-full-page-cache-and-the-debug-report) below).
- In template processing alone (the toolbar's accumulator), the `.stpl` category page took 74 ms against 90 ms for
  the `.tpl` one, with 14 MB of memory against 18 MB.
- Many `.stpl` requests on Velocity were answered in 0.024 to 0.040 s.

The numbers depend on the server's load and on its hardware; what carries over to another installation is the ratio,
and only when both engines are measured under identical conditions.

## Where it started: why .stpl was slow at first

The first working version rendered every `.stpl` correctly, and slowly: the Smarty pages were much slower than the
`.tpl` pages they were converted from. Profiling one front page showed where the time went.

| Finding on one front page | Count or cost |
|---|---|
| Full renders through the bridge | 266 |
| Operator calls through the bridge | 5,483 |
| Looking up the Smarty instance (`expSmartyRenderer::smarty()`): the template directories checked on disk and the settings hashed, on every render | about 1.7 ms each |
| Converting every variable of the namespace for Smarty (`wrap()`) on every render | about 190 ms on that page under load |
| Writing every variable back after every render, comparing each deeply with its old value (`writeBack()`) | about 240 ms on that page under load |

The cause of all of them was the same: **every `.stpl` included by a `.stpl` was a full render**. The include went
from Smarty to the template system (`{expinclude}`), which loaded the file through the design resource, found a `.stpl`,
handed back its stub `{exp_smarty_render( '<file>' )}`, ran the stub, which called `expSmartyRenderer::render()`, which
looked up the Smarty instance, converted every variable of the namespace into Smarty values (objects into proxies,
arrays walked element by element), created a new Smarty template, fetched it, and then walked every variable again to
write back what had changed. A page of 37 Smarty templates turned into 266 renders, each paying all of that.

## What was changed, and why

### 1. A .stpl included by a .stpl is a Smarty subtemplate

When `{expinclude}` is used without `name=` (the included template shares the includer's namespace, as a `.tpl`
`{include}` does), the bridge first asks the design resource which file the URI stands for, without loading it, and
when that file is a `.stpl`, renders it as a Smarty subtemplate of the including template.

`lib/smarty/classes/expsmartyincludefunction.php`:

```php
// A .stpl in the shared namespace runs as a Smarty subtemplate of this one: the same file the template
// system would load (the design resource, its override rules and keys), the same variables, without the
// detour through the template system, its stub and a second render.
if ( $shared )
{
    $file = expSmartyRenderer::resolveURI( $tpl, $uri, $templateName );
    if ( $file !== null && expSmartyRenderer::isSmartyTemplate( $file ) )
        return expSmartyRenderer::renderSubtemplate( $template, $file, $params, $uri, $templateName );
}
```

`expSmartyRenderer::resolveURI()` asks the resource with `eZTemplate::RESOURCE_QUERY`, the mode in which the
resource resolves a name (override rules, design fallback, the design keys of the page) and returns the file without
loading it:

```php
$resource = $tpl->resourceFor( $uri, $resourceName, $templateName );
if ( !is_object( $resource ) )
    return null;
$resourceData = $tpl->resourceData( $resource, $uri, $resourceName, $templateName );
$extraParameters = array();
if ( !$resource->handleResource( $tpl, $resourceData, eZTemplate::RESOURCE_QUERY, $extraParameters ) )
    return null;
```

`renderSubtemplate()` creates the child template with the includer as its parent, so it sees the includer's
variables without any conversion, assigns the include parameters, fetches it, and hands what the child defined or
changed back to the parent. Nothing goes through the template system's namespaces.

**Why it is right**: the file is the one the template system would have loaded (same resource, same override rules,
same keys), the variables are the same, and what the include defines is visible afterwards, as before. **What it
gained**: the front page went from 266 renders through the bridge to 37. With debug output on, each subtemplate is
still an entry of the debug bar's template tree, below the template that included it.

An include with `name=`, or of a `.tpl`, still goes through the template system, because there the namespaces matter.

### 2. One Smarty instance per set of designs

`expSmartyRenderer::smarty()` is called for every `.stpl` a page renders. It used to check every template directory
of every design on disk and hash the settings on each call. Now the instance is remembered by the design bases and
the cache directory, and found with one array lookup:

```php
// Called for every .stpl a page renders: the instance of the same design bases and cache directory is
// found without checking the directories and the settings again (an INI change needs a deploy anyway).
$bases = eZTemplateDesignResource::allDesignBases();
$basesKey = eZSys::cacheDirectory() . "\n" . implode( "\n", $bases );
if ( isset( self::$instancesByBases[$basesKey] ) )
    return self::$instancesByBases[$basesKey];
```

**Why it is safe**: the template directories follow from the design bases, and the settings that shape the instance
(`CompileCheck`, `Debugging`, `Security` and its lists) only change with a deploy, which restarts the workers. A
siteaccess with other designs gets its own instance.

### 3. Values are converted once per page

Exponential objects reach Smarty as read-only proxies, arrays converted element by element (`wrap()`), and go back
unwrapped (`unwrap()`). The same `$node`, `$module_result` or `$view_parameters` was converted again for every
`.stpl` of the page. Now the bridge remembers, per variable name, the last pair of raw and converted value:

```php
public static function wrapNamed( $name, $value )
{
    if ( isset( self::$pairs[$name] ) && self::$pairs[$name][0] === $value )
        return self::$pairs[$name][1];
    if ( !is_array( $value ) && !is_object( $value ) )
        return $value;
    $wrapped = self::wrap( $value );
    self::$pairs[$name] = array( $value, $wrapped );
    return $wrapped;
}
```

**Why it is fast**: `===` on the same object is one pointer comparison, and on the same array PHP compares the array
it shares first, which for an unchanged variable is the same array: the answer is found without walking the value.
`unwrapNamed()` does the same in the other direction. The pairs are dropped when the outermost render of a request
ends, so nothing of one request reaches the next.

### 4. Unchanged variables are not written back

After a render, the bridge writes back what the template defined, changed or set to `null`, as `{def}`, `{set}` and
`{undef}` do. It used to write back everything. Now a variable whose value is the one it came in with is skipped:

```php
$value = self::unwrapNamed( $name, $value );
if ( array_key_exists( $name, $input ) && $input[$name] === $value )
    continue;
```

With the pairing of change 3, that comparison is again a pointer comparison for objects and shared arrays.

### 5. Name lookups are remembered per process

A compiled Smarty template asks the extension for the callback of a modifier every time it uses one, not once when it
is compiled. On a page with thousands of operator calls, the lookup (is it a helper, does an operator of that name
exist, does `exp_` apply) ran thousands of times. `expSmartyExtension` now remembers every answer, also "not found":

```php
private static $found = array( 'modifier' => array(), 'function' => array(), 'block' => array() );

public function getModifierCallback( string $modifierName )
{
    if ( !array_key_exists( $modifierName, self::$found['modifier'] ) )
        self::$found['modifier'][$modifierName] = $this->findModifierCallback( $modifierName );
    return self::$found['modifier'][$modifierName];
}
```

**Why it is safe**: which operators and functions exist does not change while a process runs; a newly activated
extension needs a restart of the workers anyway.

### 6. The write-back goes through a post-filter

Smarty 5 resets a template's variables when `fetch()` returns, so after the render there was nothing left to write
back but by walking the template while it ran. The bridge now adds one call to the end of every compiled template, a
Smarty post-filter, that keeps the template's variables at the moment it ends:

```php
public static function appendCapture( $code )
{
    return $code . "<?php \\expSmartyRenderer::captureVariables( \$_smarty_tpl ); ?>\n";
}
```

`captureVariables()` stores them by the template object's id; `render()` and `renderSubtemplate()` take them out
after `fetch()` and write back from there. It costs one array copy per template, compiled once into the template.

### 7. Not done: native fast paths for operators

An obvious idea was to give the most used operators (`eq`, `ne`, `and`, `or`, `concat` ...) a Smarty-native
implementation, bypassing the template system. The operator call through the bridge was measured first: `eq`, called
1,158 times on one page, took 4.8 ms in total, about 4 microseconds a call. That is not where the time went, and a
second implementation of each operator would have to match the template system's behavior exactly (`eq` compares
loosely and accepts several parameters; `and` returns the last true value, `or` the first). The parity test
(`tests/tests/lib/smarty/expSmartyOperatorParityTest.php`) guarantees that every operator case of the template engine's
own tests gives the same output in `.stpl`, because every operator runs the template system's own code. Fast paths
would have traded that guarantee for a few milliseconds. They were not made.

### The order of the work

| Step | What changed | Effect on the front page |
|---|---|---|
| Start | every `.stpl` include a full render | 266 renders, 5,483 operator calls, `wrap()` and `writeBack()` of every variable every time |
| 1 | includes of `.stpl` as subtemplates | 37 renders through the bridge |
| 2 | Smarty instance per design bases | no directory checks or settings hash per render (about 1.7 ms each before) |
| 3, 4 | pairing and unchanged variables skipped | the conversions and write-backs no longer walk unchanged values |
| 5 | lookups remembered | one lookup per name and process |
| 6 | post-filter capture | write-back without walking the template |
| Cache | the whole pagelayout in one `{expcacheblock}` | most Velocity requests 0.03 to 0.07 s |

## What is still paid on every request

| Cost | On PHP-FPM | On Velocity |
|---|---|---|
| Loading the Smarty library | about 3 ms per request | once per worker |
| Creating the Smarty instance | about 2.5 ms per request | once per worker |
| The first request after a cold opcode cache | about 75 ms | once per worker start |
| Each `.stpl` rendered through the template system (the outermost one, `.tpl` includes of `.stpl`, `name=` includes) | a stub run and a render | the same |
| Each operator call | about 4 microseconds for a simple one | the same |
| Each template function (`{attribute_view_gui}` ...) | a template-system call through a generated `.tpl` | the same |

## Velocity and PHP-FPM

PHP-FPM starts every request from nothing: the kernel boots, the settings are read, Smarty is loaded and its instance
created, and the bootstrap with the debug report costs 40 to 60 ms before the page itself. Velocity keeps its workers
running: the kernel, the Smarty instance, the compiled templates in the opcode cache and the remembered lookups stay
in memory between requests. That is why a `.stpl` page reaches 0.04 s or less on Velocity and stays near 0.08 to
0.10 s on PHP-FPM with the debug report on.

A worker keeps nothing of a request's variables for the next: the pairs of change 3 and the captured variables of
change 6 are dropped when the outermost render of a request ends. A test that reloaded the same page 25 times with one
cookie jar on Velocity showed no slowdown from one request to the next.

What a persistent worker keeps that you must know about:

- the Smarty instance and the settings it was built from: after a change of `smarty.ini`, restart the workers
  (`./console exp:velocity deploy --allow-root-user` does);
- the remembered names: an operator or function that becomes available later is known after a restart;
- the input mode of each operator and `ParameterOperatorList[]`: read once per process.

## The caches, and what each one did here

| Layer | Same for `.tpl` and `.stpl`? | What we learned |
|---|---|---|
| Full-page cache (role-aware HTTP cache, `httpcache.ini`) | yes, it keeps output | never stores a page that carries a debug report; a page another visitor got cached shows `HIT` |
| Velocity response cache | yes | answers before a worker is chosen |
| Content view cache | yes | 0 SQL queries on a repeated full view in either engine |
| `{expcacheblock}` | the same cache as `{cache-block}` | needs `[TemplateSettings] TemplateCache=enabled`; one block around the whole pagelayout did the most |
| Compiled stubs and Smarty's compiled templates | `.stpl` only | written once; `CompileCheck` decides whether a change is noticed |

### The full-page cache and the debug report

The full-page cache (the role-aware HTTP cache, switched on for the siteaccesses in `[HttpCacheSettings]
CachedSiteAccesses[]` of `httpcache.ini`) keeps whole pages for anonymous visitors. It never stores a response that
carries the debug report (the Exp Debug bar or the classic report), and says so:

```bash
curl -s -o /dev/null -D - https://www.example.com/fitness | grep -i x-exp-cache
```

```
x-exp-cache: BYPASS (debug report)
```

Two consequences for measuring:

- **With the debug toolbar on you always see a render**, never the cache. That is what you want when you measure
  templates.
- **Without it, a page another visitor already caused to be stored shows `x-exp-cache: HIT`**, and its time says
  nothing about templates. That is how the `.tpl` front page above had no script time to compare: it came from the
  cache.

### The content view cache

A full view is kept as output per node, view, view parameters, language and the visitor's roles and limitations. It
works the same for both engines: a repeated full view ran no SQL query at all in either. The pagelayout around it is
not part of the view cache; it is rendered on every request unless a cache block keeps it.

### {expcacheblock} needs TemplateCache

`{expcacheblock}` stores into the template-block cache only with `[TemplateSettings] TemplateCache=enabled`; with it
off, the block's body runs on every request and nothing is stored, without any message. Check it first when a block
does not seem to cache.

## The page in one cache block

The pagelayout is the part of every page that the view cache does not keep: the head, the menus, the layout zones of
Exponential Layouts, the footer. The media port keeps all of it in **one** `{expcacheblock}`. The first version had
three blocks (head, layout, page end); merging them into one brought the Velocity numbers down to 0.03 to 0.07 s,
because a cached request then runs almost nothing of the pagelayout.

The block's keys, from `templates/pagelayout.stpl`:

```smarty
{$el_page_user = fetch('user', 'current_user')}
{expsetblock variable='exp_dynamic_parts' scope='global'}
1{/expsetblock}
{capture assign=el_page_html}
{expcacheblock id=4 keys=(['pagelayout-page', $module_result.content_info.node_id|default:0, $module_result.uri|default:'', $module_result.content|md5, $el_page_user.role_id_list|exp_implode:',', $el_page_user.limited_assignment_value_list|exp_implode:',', $el_page_user.is_logged_in])}
... the whole page ...
{/expcacheblock}
{/capture}
```

| Key | Why |
|---|---|
| `'pagelayout-page'` | a name, so the key never meets another block's |
| `$module_result.content_info.node_id` | the node shown |
| `$module_result.uri` | pages without a node (search, user pages, error pages) |
| `$module_result.content\|md5` | everything the view produced: paging, search results, form errors give another page |
| `$el_page_user.role_id_list` | what the visitor may see depends on their roles |
| `$el_page_user.limited_assignment_value_list` | roles limited to a subtree or a section |
| `$el_page_user.is_logged_in` | anonymous and signed-in pages differ even with the same roles |
| the siteaccess | added by the block itself |

### The per-visitor part as a placeholder

The user menu, with the visitor's name and the basket count, is the visitor's own and must never be stored. While
the page block is being built, a global flag says so, and the header writes a marker instead of the menu.
`templates/pagelayout/header.stpl`:

```smarty
{if and(is_set(expvar('exp_dynamic_parts', '')), eq(expvar('exp_dynamic_parts', ''), '1'))}
<!--exp-dynamic:user_menu-->{else}
{expinclude uri='design:pagelayout/header/user_menu.tpl'}{/if}
```

After the block, cached or freshly rendered, the pagelayout clears the flag, renders the menu and puts it in the
marker's place:

```smarty
{expsetblock variable='exp_dynamic_parts' scope='global'}
0{/expsetblock}
{capture assign=el_user_menu}
{expinclude uri='design:pagelayout/header/user_menu.tpl'}{/capture}
{($el_page_html|exp_explode:'<!--exp-dynamic:user_menu-->')|exp_implode:$el_user_menu}{$el_page_html = null}
```

The flag is global (`{expsetblock ... scope='global'}`, read with `expvar(..., '')`) because the header is several
include levels down; the marker is an HTML comment, harmless if it is ever printed. The `.tpl` pagelayout of the
media design caches its layout the same way (`{set-block scope=global variable=exp_dynamic_parts}`,
`{cache-block keys=...}`, `{$el_layout_html|explode( '<!--exp-dynamic:user_menu-->' )|implode( $el_user_menu )}`),
so both engines cache like for like.

### Finding the per-visitor parts: a worked inventory

Before caching a whole page, every part that depends on the visitor must be found. The inventory of the media design:

| Part | Depends on | How it was handled |
|---|---|---|
| User menu with the basket count (`pagelayout/header/user_menu`) | the session, the basket | the placeholder above: rendered fresh on every request |
| The wishlist button (`content/parts/product_buy_form.stpl`) | `is_logged_in` | covered by the key `is_logged_in` |
| The title of an error page (`pagelayout/head/title.stpl`: "Please sign in to see this page" or "You don't have access to this page" for an access error) | `is_logged_in` | covered by the key `is_logged_in` |
| Menus and lists that hide what the visitor may not read | roles and limitations | covered by `role_id_list` and `limited_assignment_value_list` |
| Forms with a form token | the session | nothing to do: the token is added to the output after rendering |

How to find them in your design: search the templates for `current_user`, `is_logged_in`, `basket`, `session`,
`ezhttp`, `ezpreference`, `csp_nonce`, and for fetches of the current user's data; open every page signed in and
signed out and compare.

### What must stay outside a cached block

- **Per-visitor content**: the current user, the session, the basket, messages. Use a placeholder.
- **Scripts and styles required while rendering**: `ezscript_require()` and `ezcss_require()` register a file for the
  page's head or end when they run. Inside a cached block they do not run on a cached request, and the file is
  missing from the page. Require them outside the block, or keep the code that prints them inside the same block.
- **`csp_nonce()`**: it changes on every request; a stored nonce no longer matches the page's header, and the
  browser blocks the script.
- **Not a problem: forms.** The form token of signed-in visitors (`ezformtoken`) is added to every
  `<form method="post">` on the output, after all templates ran, so a form that comes out of a cache block gets the
  current token.

### What expires the block

| Event | Effect |
|---|---|
| A content publish | expires every template-block cache, so the next request renders the page again |
| A layout publish or a layout-rule change (Exponential Layouts) | expires every template-block cache too: `explayouts_core` calls `eZContentObject::expireTemplateBlockCache()`, because a pagelayout keeps the rendered layout in a block |
| `expiry` of the block | its age (the default is two hours) |
| `php bin/php/ezcache.php --clear-tag=template` | everything template-related, the blocks included |

## How to measure

### The debug toolbar

Turn the debug output on for your own address ([the Exp Debug bar](../features/6.0/exp-debug-bar.md)). The page is
then always rendered (the full-page cache bypasses it), and the bar shows:

| Where | What to read |
|---|---|
| The bar | the script time, the SQL count, the peak memory |
| Timing tab, time accumulators | `Template processing` (the work of the templates), `Template load`, `Load cache`, `Cache load`, `TS init` (the translation system) |
| Rendering tab, content view cache | hit or generated, and why |
| Rendering tab, cache blocks | every `{cache-block}` and `{expcacheblock}` (named by its `.stpl` file and `id`), hit or generated, with its time |
| Rendering tab, template tree | every template with its time, `.stpl` subtemplates below the template that included them |

Compare engines with the same debug state: the debug report itself costs time, so a page with the bar against a page
without it is not a comparison.

### curl

Without the debug output, from a shell, the median of ten requests:

```bash
for i in 1 2 3 4 5 6 7 8 9 10; do
    curl -s -o /dev/null -w '%{time_total}\n' https://www.example.com/smarty/fitness
done | sort -n | sed -n 5,6p
```

Check `x-exp-cache` at the same time: a `HIT` measures the full-page cache, not the templates.

### Rules that kept the numbers honest

- **Several requests, the median.** Single requests vary a lot; ten requests and the middle value are stable.
- **Identical conditions.** The same caches warm (the view cache, the block cache), the same debug state, the same
  server, back to back.
- **Watch the server's load.** Results swung three to five times with the load. During one run the load was 52 on 12
  cores, and every number of that run was meaningless; it was thrown away and measured again later.
- **Check for accumulation in persistent workers.** Reload the same page many times with one cookie jar
  (`curl -b var/tmp/jar -c var/tmp/jar ...` 25 times) and look for a trend. There was none.
- **Know what answered.** A full-page cache hit, a view cache hit and a render are three different measurements.

## Settings that matter

| Setting | Default | Effect on speed |
|---|---|---|
| `[SmartySettings] CompileCheck` (`smarty.ini`) | `enabled` | `enabled`: Smarty checks every `.stpl` for a change on every use. `disabled` saves those checks on production; then clear the template caches after every template change, and restart the workers, since the instance (with the setting) is remembered |
| `[SmartySettings] Debugging` | `disabled` | `enabled` adds Smarty's debug console to every page; never on production |
| `[SmartySettings] Security` | `disabled` | `enabled` compiles templates with the security policy into `<cache dir>/smarty/compile-secure`. The check happens when a template is compiled, not when it runs, so a compiled template costs the same; turning it on recompiles every template once |
| `[SmartySettings] ParameterOperatorList[]` | empty | operators without template hints that read only their parameters; read once per process. Not a speed setting, but a wrong mode means wrong output |
| `[TemplateSettings] TemplateCache` (`site.ini`) | `enabled` | without it, `{expcacheblock}` caches nothing |
| `[ContentSettings] ViewCaching` | `enabled` | the content view cache, for both engines |
| `[HttpCacheSettings] Enabled`, `CachedSiteAccesses[]` (`httpcache.ini`) | `disabled` | the full-page cache for anonymous visitors |

## Checklist for a fast .stpl site

1. Serve it with Velocity if you can; on PHP-FPM, make sure the opcode cache is on and warm.
2. Include `.stpl` from `.stpl` with `{expinclude uri=...}` without `name=`, so it runs as a subtemplate.
3. Keep the pagelayout in one `{expcacheblock}` keyed by the node, the URI, the content's md5 and the visitor's roles,
   limitations and signed-in state, with per-visitor parts as placeholders.
4. Make an inventory of the per-visitor parts before you cache, and test signed in and signed out.
5. Keep `ezscript_require()`, `ezcss_require()` and `csp_nonce()` out of cached blocks.
6. Put `{expcacheblock}` around expensive fetches in views that the view cache does not keep.
7. Check `[TemplateSettings] TemplateCache=enabled` and the view cache.
8. On production, consider `CompileCheck=disabled`, with a template cache clear and a worker restart in every deploy.
9. Measure with the debug toolbar and with curl, medians of ten, identical conditions, and look at `x-exp-cache`.

## Pitfalls

| Pitfall | What happens | Instead |
|---|---|---|
| `{expinclude ... name='x'}` for a `.stpl` that does not need a namespace | it goes through the template system: a stub and a full render | leave `name=` out |
| A cache block without the visitor's roles in its keys | a visitor sees a menu or list made for someone else | key by `role_id_list`, `limited_assignment_value_list`, `is_logged_in` |
| Per-visitor content inside a cache block | the first visitor's name or basket shown to everyone | a placeholder rendered after the block |
| `ezscript_require()` inside a cache block | the script is missing on cached requests | require it outside the block |
| `TemplateCache` disabled | blocks cache nothing, silently | enable it |
| Measuring with the debug toolbar on one engine and off on the other | the debug report's 40 to 60 ms decide the comparison | the same debug state for both |
| Measuring a full-page cache hit | the time says nothing about templates | check `x-exp-cache`; use the debug toolbar |
| Measuring on a loaded server | numbers swing three to five times | measure when the load is low, and repeat |
| Changing `smarty.ini` and expecting it to work at once | the instance and the lookups are remembered per worker | deploy (restart the workers) |
| `CompileCheck=disabled` without a cache clear in the deploy | old templates keep running | clear the template caches in every deploy |
| Smarty's own `count`, `implode`, `explode` in hand edits | wrong output, not slower | the `exp_` names |

## Related pages

- [Caching and performance](smarty-templates-caching.md): the cache layers, `{expcacheblock}`, keys, expiry
- [Smarty templates (.stpl) next to .tpl](../features/6.0/smarty-templates.md), the feature page
- [Writing and running .stpl templates](smarty-templates.md#12-velocity-and-persistent-workers): Velocity and persistent workers
- [Troubleshooting and FAQ](smarty-templates-troubleshooting.md)
- [Benchmarking](benchmarking.md), [Caches](caches.md), [The role-aware HTTP cache](../bc/6.0/httpcache.md)
- [Velocity and the opcode cache](../features/6.0/velocity-opcode-cache-and-profile.md)
- [Speed of the three template engines](template-engines-speed.md): Smarty, Twig and `.tpl` measured side by side,
  cached, uncached and with the debug output

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
