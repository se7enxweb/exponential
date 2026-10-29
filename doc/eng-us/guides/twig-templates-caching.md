# Twig templates: caching

This guide is for template developers and site builders who want `.twig` pages served from cache. A `.twig` takes
part in every cache layer a `.tpl` does; this page says which layers there are, how `{% expcacheblock %}` works, and
how one cache block around a whole page keeps the visitor's own menu fresh. The same pattern in Smarty is in
[Smarty templates: caching](smarty-templates-caching.md).

## The layers

From the outside in:

| Layer | Holds | Engine-specific? |
|---|---|---|
| Velocity's response cache | whole responses for anonymous visitors (Velocity only) | no |
| The role-aware HTTP cache (`exphttpcache`) | whole pages per role set | no |
| The content view cache | the rendered `content/view` result of a node | no |
| Cache blocks (`{cache-block}`, `{% expcacheblock %}`) | parts of a template's output | the tag |
| Compiled templates | `.tpl` as PHP, `.twig` as PHP classes | yes: `<cache directory>/twig/compile-secure` |

A cached page never reaches the template engine: whether its templates are `.tpl`, `.stpl` or `.twig` makes no
difference to a cached request. Measurements on a development installation (cached pages of the three engines within
a few percent of each other) are in [Performance](twig-templates-performance.md).

## {% expcacheblock %}

```twig
{% expcacheblock keys=[node.node_id] %}cached: {{ node.name|wash }}{% endexpcacheblock %}
```

```
cached: Why Track Your Workouts?
```

`expcacheblock` is the template system's `{cache-block}`: the same parameters, the same cache files
(`<cache directory>/template-block/`), the same expiry and clearing.

| Parameter | Meaning |
|---|---|
| `keys` | a value or a list: the block is kept per key; the siteaccess and the template's place are always part of the key |
| `expiry` | seconds a copy is kept (default 7200); `0` keeps it until it is cleared |
| `ignore_content_expiry` | a publish of content does not expire the block |
| `subtree_expiry` | a publish below this node (a path or node id) expires the block |
| `id` | the block's number within the template (the converter numbers them): blocks of one template without `id` and with the same keys share one cache entry, so give each block of a hand-written template its own |

Variables set inside a block are written back when the block runs; when the block comes from cache, they are not
set (as in a `.tpl`). Keep the `{% set %}` of a value you need after the block outside it.

## One block for the whole page, a fresh user menu

The converted media pagelayout keeps the whole page in one cache block, keyed by node, URI, page content and the
visitor's roles, so a cached page costs one cache read. The visitor's own part (the user menu with the basket) is
never kept: a flag tells the menu template to write a marker in its place while the block renders, and after the
block the fresh menu replaces the marker on every request:

```twig
{% expsetblock variable='exp_dynamic_parts' scope='global' %}1{% endexpsetblock %}
{% set el_page_html %}
{% expcacheblock id=4 keys=(['pagelayout-page', module_result.content_info.node_id|default(0), module_result.uri|default(''), module_result.content|md5, el_page_user.role_id_list|implode(','), el_page_user.limited_assignment_value_list|implode(','), el_page_user.is_logged_in]) %}
    ... the page; pagelayout/header writes <!--exp-dynamic:user_menu--> while exp_dynamic_parts is 1 ...
{% endexpcacheblock %}
{% endset %}
{% expsetblock variable='exp_dynamic_parts' scope='global' %}0{% endexpsetblock %}
{% set el_user_menu %}{% expinclude uri='design:pagelayout/header/user_menu.tpl' %}{% endset %}
{{ (el_page_html|explode('<!--exp-dynamic:user_menu-->'))|implode(el_user_menu) }}
```

The Rendering tab of the debug bar shows the block (`pagelayout.twig id=4`), whether it was read from cache or
generated, its keys and its file. The same pattern for another per-visitor part: give the part its own marker, write
the marker while the flag is set, render the part after the block and replace the marker.

## Compiled templates

Twig compiles each `.twig` once to a PHP class in `<cache directory>/twig/compile-secure` (`compile` with the sandbox
off). The class name holds the file's modification time, the siteaccess and the locale, so a changed file is compiled
again on the next request, also in Velocity's persistent workers, and translations made when the template was
compiled stay with their language. `./console exp:ezcache --clear-tag=template` clears the compiled templates of all
engines.

## Related

- [Writing and running .twig templates](twig-templates.md)
- [Caches](caches.md)
- [Performance](twig-templates-performance.md)
