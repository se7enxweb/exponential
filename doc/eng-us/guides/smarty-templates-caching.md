# Smarty templates: caching and performance

This page is for template developers and operators who want `.stpl` pages to be fast and right. It goes through every
cache a `.stpl` page meets, from the outside in, explains the pattern the media port uses to keep a whole page in one
cache block while the visitor's own parts stay fresh, shows how to key a cache for signed-in visitors, lists what
expires what, and how to measure. The measured numbers of the media port are on the feature page:
[Speed](../features/6.0/smarty-templates.md#speed).

## The layers, outside in

| Layer | What it keeps | Who gets it | Expired by | `.stpl` specific |
|---|---|---|---|---|
| Velocity response cache | whole responses, in the server process | requests without a session cookie, when the page allows it | its TTL, a touched file | nothing |
| Role-aware HTTP cache (`httpcache.ini`) | whole rendered pages, per permission context | anonymous visitors of the siteaccesses it lists | content changes, layout changes, `MaxAge` | nothing: it keeps the output |
| Content view cache (`ViewCaching`) | the output of a content view (`$module_result.content`) per node, view, view parameters, language and the visitor's roles and limitations | everybody | a publish of the node or of related content, a cache clear | nothing: it keeps the output |
| Template-block cache (`{expcacheblock}`) | the output of a block, by its keys | everybody whose keys match | `expiry`, a content publish, `subtree_expiry`, a layout publish, a cache clear | the same cache as `{cache-block}` |
| Compiled stubs | `{exp_smarty_render( '<file>' )}`, compiled to PHP | — | the template caches | the only thing of a `.stpl` the template system caches |
| Smarty's compiled templates | each `.stpl` compiled to PHP, in `<cache directory>/smarty/compile/` | — | a changed `.stpl` (with `CompileCheck=enabled`), `--clear-id=smarty`, the template caches | yes |
| Generated function calls | one small `.tpl` per template function and set of parameter names, in `<cache directory>/smarty/functions/` | — | the same | yes |

None of the caches stores the output of a `.stpl` by itself: Smarty's own output caching is off. A `.stpl` is rendered
on every request that is not answered by one of the first four layers.

### The full-page caches

The role-aware HTTP cache keeps whole pages for anonymous visitors and answers them before the kernel starts
([The role-aware HTTP cache](../bc/6.0/httpcache.md)); Velocity's response cache does the same in the server process
([Velocity response cache](../features/6.0/velocity-response-cache.md)). Both keep output, so they treat `.tpl` and
`.stpl` pages alike. Two consequences:

- **A page with the debug report is never stored.** When the page carries the Exp Debug bar or the classic debug
  report, the HTTP cache answers `X-Exp-Cache: BYPASS (debug report)` and renders it. So with the debug output on you
  always see the rendering time, never the cache; with it off, an anonymous visitor may get the page without any
  template running.
- **A signed-in visitor is not served from them.** For signed-in visitors the content view cache and the template-block
  cache do the work.

Check what the HTTP cache did:

```bash
curl -s -o /dev/null -D - https://<host>/<page> | grep -i x-exp-cache
```

### The content view cache

A full view (or any view mode in `[ContentSettings] CachedViewModes`) is kept as output, keyed by the node, the view
mode, the view parameters, the language and the visitor's roles and limitations. A `.stpl` full view is cached exactly
like a `.tpl` one; while you work on it, clear the content cache after a change, or turn the view cache off in your
work siteaccess (`[ContentSettings] ViewCaching=disabled`). The debug bar's **Rendering** tab says for each request
whether the view was read from the cache or generated, and why.

## {expcacheblock} in detail

```smarty
{expcacheblock id=2 keys=[$node.node_id, $view_parameters.offset] expiry=3600}
    {$children = fetch('content', 'list', ['parent_node_id' => $node.node_id, 'offset' => $view_parameters.offset, 'limit' => 10])}
    {foreach $children as $child}{node_view_gui view='line' content_node=$child}{/foreach}
{/expcacheblock}
```

- **The key** is the `keys` you give, the block's place (`stpl:<file>:<id>`) and the siteaccess (plus the
  administration design variant of the UI switch). Without `keys`, the block is the same for every page that renders
  that file: right for a footer, wrong for anything that depends on the node.
- **`id`** must be unique in the file. Two blocks with the same id in one file share one cache entry.
- **`expiry`** in seconds (default 7200, two hours); `0` keeps the block until something else expires it.
- **Content expiry.** A publish of any content expires every block, unless the block says `ignore_content_expiry=true`.
- **`subtree_expiry`** (a node id, or a path such as `'content/view/full/2'` or a URL alias): only a publish below that
  node expires the block, and the global content expiry is ignored.
- **When the block is cached, its body does not run**: nothing inside it is fetched or rendered, and the variables it
  would assign are not assigned. Assign what you need after the block outside of it.
- **Only with `[TemplateSettings] TemplateCache=enabled`**: otherwise the body always runs and nothing is stored.
- **Never put per-visitor or per-request output in it**: the basket, "logged in as ...", a form token, a CSP nonce
  (`csp_nonce()`), the current time. Either key the block by what makes it differ, or leave that part out with the
  pattern of the next section.

Smarty's `{nocache}` and Smarty's own caching do not apply: output caching is off in the bridge.

## The page block with placeholders

The media port renders the whole page, layout, header and footer included, inside one `{expcacheblock}` in
`templates/pagelayout.stpl`, keyed by what makes the page differ. The one part that is the visitor's own, the user
menu with the basket count, is never kept: a marker stands in its place inside the block, and the menu is rendered
fresh after the block on every request and put into the marker's place. On a cached request the pagelayout runs only
the user menu.

The three pieces, from `extension/sevenx_themes_media/design/smarty_media/templates/`:

1. **A global flag** says "a page block is being built". `pagelayout.stpl` sets it before the block and clears it
   after:

   ```smarty
   {$el_page_user = fetch('user', 'current_user')}
   {expsetblock variable='exp_dynamic_parts' scope='global'}
   1{/expsetblock}
   {capture assign=el_page_html}
   {expcacheblock id=4 keys=(['pagelayout-page', $module_result.content_info.node_id|default:0, $module_result.uri|default:'', $module_result.content|md5, $el_page_user.role_id_list|exp_implode:',', $el_page_user.limited_assignment_value_list|exp_implode:',', $el_page_user.is_logged_in])}
   ... the whole page ...
   {/expcacheblock}
   {/capture}
   {expsetblock variable='exp_dynamic_parts' scope='global'}
   0{/expsetblock}
   ```

2. **The part that would render the visitor's menu writes a marker instead while the flag is set**
   (`pagelayout/header.stpl`):

   ```smarty
   {if and(is_set(expvar('exp_dynamic_parts', '')), eq(expvar('exp_dynamic_parts', ''), '1'))}
   <!--exp-dynamic:user_menu-->{else}
   {expinclude uri='design:pagelayout/header/user_menu.tpl'}{/if}
   ```

3. **After the block, the fresh menu replaces the marker** in the page, cached or not:

   ```smarty
   {capture assign=el_user_menu}
   {expinclude uri='design:pagelayout/header/user_menu.tpl'}{/capture}
   {($el_page_html|exp_explode:'<!--exp-dynamic:user_menu-->')|exp_implode:$el_user_menu}{$el_page_html = null}
   ```

Why it is built this way:

- The flag is global (`scope='global'`, read with `expvar(..., '')`) because the header is a template included several
  levels down; a global variable reaches it whatever the namespaces in between.
- The marker is an HTML comment, so a page rendered outside of the pattern (the flag not set) never shows it, and a
  page from an old cache without the marker simply has nothing to replace.
- `exp_explode` and `exp_implode` replace every marker, without a regular expression and without escaping the menu.
- The key holds `$module_result.content|md5`: the page's own content, which is itself the output of the view (cached
  by the view cache, keyed by the node, the view parameters and the visitor's roles), so paging, a search result or a
  form error give a different page block.
- The `.tpl` pagelayout of the media design does the same with `{set-block}`, `{cache-block}`, `$#exp_dynamic_parts`
  and `{$el_layout_html|explode( '<!--exp-dynamic:user_menu-->' )|implode( $el_user_menu )}`, so both engines cache
  the same page.

Use the same pattern for a cart in a header, a "Hello, <name>", or a notice with the session's messages: give each
part its own marker name (`<!--exp-dynamic:cart-->`), write the marker while the flag is set, render the part after the
block and replace its marker.

## Keys for signed-in visitors

What a visitor may see depends on their roles and limitations. A block that shows anything permission-dependent (a
menu that hides what the visitor may not read, edit links, a list of content) must be keyed by them:

| Key part | From | Why |
|---|---|---|
| `$user.role_id_list\|exp_implode:','` | `fetch('user', 'current_user')` | the roles the visitor has, through every group |
| `$user.limited_assignment_value_list\|exp_implode:','` | the same | roles assigned with a limitation (a subtree, a section) give different content |
| `$user.is_logged_in` | the same | anonymous and signed in differ even with the same roles (the menu, the forms) |
| `$module_result.content\|md5` | the pagelayout | everything the view produced |
| `$node.node_id`, `$view_parameters.offset` | a view | the node and the page of a list |
| the siteaccess | automatic | every block is per siteaccess |

Do not key by the user id unless the block is really per user: every visitor gets an entry of their own, and the cache
grows with the number of visitors. Keys by roles keep one entry per permission context.

## What expires what

| Event | Expires |
|---|---|
| A change to a `.stpl` | its compiled file on the next render (with `CompileCheck=enabled`); with `disabled`, nothing until `--clear-id=smarty` |
| A new `.stpl`, a new or changed override rule | nothing by itself: clear the template caches (`--clear-tag=template`) and the content cache |
| A content publish | the view cache of the node and related content (smart view cache), every template-block (`{expcacheblock}`) without `ignore_content_expiry`, the blocks whose `subtree_expiry` covers the node, the HTTP cache (`ContentChangePurges`) |
| A layout or layout-rule publish (Exponential Layouts) | every template-block, so a pagelayout that keeps the rendered layout in a block shows the new one; the layout resolver's cache |
| `php bin/php/ezcache.php --clear-tag=template` | compiled stubs and `.tpl` templates, Smarty's compiled files and generated calls, template-blocks, the override cache |
| `php bin/php/ezcache.php --clear-id=content` | the content view cache |
| A changed INI setting | nothing in templates: clear the INI cache (`--clear-tag=ini`) and reload PHP-FPM / restart Velocity |

`./console exp:velocity deploy --allow-root-user` clears all of these in the right order and reloads the servers.

## How to measure

1. **Turn the debug output on for your address only** ([the Exp Debug bar](../features/6.0/exp-debug-bar.md)). The
   HTTP cache never stores or serves a page with the debug report, so every request is rendered.
2. **Read the bar.** The script time, the **Timing** tab's time accumulators (the template processing among them),
   and the **Rendering** tab:
   - the **Content view cache** row: hit or generated, and why;
   - the **Cache blocks** table: every `{cache-block}` and `{expcacheblock}`, hit or generated, its time and keys. A
     `{cache-block}` is named by its template and line, an `{expcacheblock}` by its `.stpl` file and its `id`
     (`full.stpl id=2`);
   - the **Template tree**: every template that ran, with its time. A `.stpl` included by a `.stpl` without `name=`
     runs as a Smarty subtemplate and is in the tree below the template that included it, as an `{include}` of a
     `.tpl` is.
3. **Compare engines** on the same URL: the `.tpl` siteaccess and the `.stpl` one, the median of ten requests each,
   with the same caches warm.
4. **Without the debug output**, from a shell:

   ```bash
   for i in 1 2 3 4 5 6 7 8 9 10; do
       curl -s -o /dev/null -w '%{time_total}\n' https://<host>/smarty/<page>
   done | sort -n | sed -n 5,6p
   ```

   prints the two middle times of ten requests. Look at `X-Exp-Cache` too: a `HIT` measures the cache, not the
   template.

## Why a .stpl page is fast

The whole story, with the numbers before and after, every change and its reason, and how to measure:
[Smarty templates: performance (Speed)](smarty-templates-performance.md).

- A `.stpl` included by a `.stpl` (`{expinclude}` without `name=`) runs as a Smarty subtemplate, not through the
  template system, its stub and a second render.
- The value of a variable is converted for Smarty once per page: the next `.stpl` that sees the same `$node` or
  `$module_result` gets the converted value back after one comparison (`expSmartyRenderer::wrapNamed()`).
- What a modifier, function or block name stands for is looked up once per process (`expSmartyExtension`).
- The Smarty instance is created once per process and set of designs.
- The variables a template assigned are collected by one call at the end of its compiled code (a post-filter of the
  bridge), since Smarty 5 drops them when `fetch()` returns; nothing walks the template's variables during rendering.
- Under Velocity, the kernel, Smarty and the compiled templates stay loaded in the workers.

What costs time in a `.stpl` is what costs time in a `.tpl`: fetches, template functions (each is a template system
call), and `{expinclude}` of `.tpl` templates. Cache blocks around the expensive parts, and the page block above, do
the most.

## Related pages

- [Smarty templates: performance (Speed)](smarty-templates-performance.md): what made `.stpl` fast, measured
- [Smarty templates (.stpl) next to .tpl](../features/6.0/smarty-templates.md#speed): the measured numbers
- [Template functions and blocks](smarty-templates-functions.md#expcacheblock)
- [Caches](caches.md), [The role-aware HTTP cache](../bc/6.0/httpcache.md), [The Exp Debug bar](../features/6.0/exp-debug-bar.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
