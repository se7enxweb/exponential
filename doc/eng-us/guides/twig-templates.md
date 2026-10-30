# Writing and running .twig templates

This guide is for template developers who write Twig 3 templates for an Exponential design. It explains how a `.twig`
is found and rendered, what it sees, how it calls the template system, and what it cannot do. The feature page
[Twig templates](../features/6.0/twig-templates.md) has the overview and the settings; the first steps are in
[A first .twig, step by step](twig-templates-tutorial.md).

## How a .twig is rendered

The template system finds templates through the design resource: override rules, the design fallback and `design:`
URIs work for every file ending. When the file it finds ends in `.twig` or `.html.twig`
(`settings/site.ini [TemplateBridgeSettings] EngineSuffixList[]`), the template system does not parse it. It parses a
one-line stub in its place:

```
{exp_twig_render( 'extension/<ext>/design/<design>/templates/node/view/full.twig' )}
```

The stub is compiled and cached like any `.tpl`. When it runs, `exp_twig_render` hands the file to
`expTwigRenderer::render()`, which renders it with Twig and returns the text. So everything around the template stays
the template system's: the view cache, the `{cache-block}`s of the calling template, the debug bar (the Rendering
tab names both the `.tpl` URI and the `.twig` file that answered it), `{include}` from a `.tpl`, override rules.

One Twig environment is kept per process and per design chain. Twig compiles a `.twig` to a PHP class once; the class
is kept in `<cache directory>/twig/compile-secure` (or `compile` with the sandbox off) and cleared with the template
caches (`./console exp:ezcache --clear-tag=template`). The class name carries the file's modification time, the
siteaccess and the locale, so a changed file is compiled again even in a persistent worker, and texts translated when
the template was compiled (below) never leak into another language.

## Setup

Nothing has to be activated. Twig 3 has to be installed (`./console exp:template:engines` shows it); see
[Installing Twig](../features/6.0/twig-templates.md#installing-twig). Put `.twig` files where `.tpl` files go:

```
extension/<ext>/design/<design>/templates/pagelayout.twig
extension/<ext>/design/<design>/override/templates/full/article.twig
```

and point override rules at them as at any template:

```ini
[full_article]
Source=node/view/full.tpl
MatchFile=full/article.twig
Subdir=templates
Match[class_identifier]=article
```

The `Source` stays the `.tpl` URI the kernel asks for; only `MatchFile` names the `.twig`. A `.twig` can also be found
by its own URI (`design:parts/menu.twig`). When the converter writes a design, it writes these override rules for
every template it converts (see [The converter](twig-templates-converter.md)).

## Variables

A `.twig` sees the variables of the namespace it runs in, as a `.tpl` does: `node`, `view_parameters`,
`module_result`, `site`, `current_user`, the parameters of the `{include}` that included it. Values are the template
system's own: a node is an `eZContentObjectTreeNode` behind a proxy, so `node.name`, `node.data_map.title.content`,
`node.object.published` read attributes exactly as `$node.name` does.

```twig
{{ node.name|wash }} - {{ node.url_alias|ezurl }}
```

```
Why Track Your Workouts? - "/fitness/why-track-your-workouts"
```

(`ezurl` adds the quotes, as in a `.tpl`; `ezurl('no')` leaves them out.)

What a `.twig` sets with `{% set %}` is written back to the namespace when it ends, as `{def}` and `{set}` do in a
`.tpl`: a pagelayout, a module or a mail template that reads `$title` after the include sees it. A variable set to
`null` is removed, as `{undef}` does. Twig's own variables (`_context`, `loop`, `_self`) are not written back.

## Operators

Every template operator of the installation and of its extensions is a Twig filter and a Twig function. A filter is
the operator with an input, a function the operator without one, exactly as the `.tpl` writes it:

| `.tpl` | `.twig` |
|---|---|
| `{$user_name\|wash}` | `{{ user_name\|wash }}` |
| `{$list\|implode( ', ' )}` | `{{ list\|implode(', ') }}` |
| `{cond( $price\|gt( 10 ), 'expensive', 'cheap' )}` | `{{ cond(price\|gt(10), 'expensive', 'cheap') }}` |
| `{concat( 'a', 'b', $user_name )}` | `{{ concat('a', 'b', user_name) }}` |
| `{first_set( $missing, 'a default' )}` | `{{ first_set(missing, 'a default') }}` |

Where Twig has a filter or function of the same name (`count`, `merge`, `date`, `round` ...), the operator is
`exp_<name>`: `{{ list|exp_count }}`. Twig's own keeps its name and its meaning. The rules, the input modes and
`fetch()` are in [Operators and fetch functions](twig-templates-operators.md); every operator with its forms is in the
[Operator reference](twig-templates-operator-reference.md).

The logic and arithmetic operators (`eq`, `gt`, `and`, `sum`, `mod` ...), `cond`, `first_set`, `wash`, `implode`,
`contains` and `concat` run directly in PHP with exactly the results of the template system; every other operator
runs through the template system's own code, so it behaves as in a `.tpl` without exception.

## fetch()

```twig
{% set children = fetch('content', 'list', {parent_node_id: node.parent_node_id, limit: 3}) %}
{{ children|length }} children: {% for child in children %}{{ child.name|wash }}; {% endfor %}
```

```
3 children: 7 Keystone Habits to Maximize Your Mornings; The BiPolar Learning Curve; Split Options Weight Training Program;
```

The parameters are a Twig hash (`{key: value}`), the `.tpl`'s `hash( 'key', value )`.

## Loops

Twig's `{% for %}` loops over arrays and over every list the template system returns. `loop.index`, `loop.first`,
`loop.last` replace the `.tpl`'s `$item@index`-style counters and `{delimiter}`:

```twig
{% for item in list %}{{ loop.index }}:{{ item }} {% endfor %}
```

```
1:a 2:b 3:c
```

A converted `{section}` loops over `expsectionloop(...)`, a converted `{while}` and `{do}` over the `expwhile` tag (at
most `[TwigSettings] WhileLoopLimit` rounds). Twig has no `break` and `continue`; the converter writes them as flags,
hand-written templates use `{% for x in list|filter(...) %}` or `{% if %}`.

## Template functions and includes

The `*_view_gui` functions and every other template function without a body are Twig functions that take one hash:

```twig
{{ attribute_view_gui({attribute: node.data_map.body}) }}
{{ node_view_gui({content_node: child, view: 'line'}) }}
```

A function with a body is a tag: `{% explblock name='content' %}...{% endexplblock %}`. Includes go through the design
resource with `{% expinclude uri='design:parts/menu.tpl' %}`, which finds a `.tpl`, `.stpl` or `.twig` with its
override rules. Twig's `{% include %}` works for `.twig` files by path. Details:
[Template functions and tags](twig-templates-functions.md).

## Translations

`{{ 'Close'|i18n('design/standard/content') }}` translates as in a `.tpl`. With a constant text and context the
translation is done when the template is compiled, not on every request: the compiled class is kept per siteaccess
and locale. With arguments (`'%count items'|i18n('ctx', '', {'%count': n})`) the translated text is constant and only
the arguments are put in when it runs.

## Caching

`{% expcacheblock keys=[node.node_id] %}...{% endexpcacheblock %}` is the template system's `{cache-block}`: same keys,
`expiry`, `subtree_expiry`, `ignore_content_expiry`, same cache files, cleared by the same events. See
[Caching](twig-templates-caching.md).

## Errors and debugging

A `.twig` that fails to compile or to render prints nothing in its place and writes the error, with the file and the
line, to the debug output and `var/log/error.log`. With the debug output on, an HTML comment with the message is left
where the output would have been:

```
<!-- twig: rendering <root>/extension/<ext>/design/<design>/templates/parts/menu.twig failed: Unknown &quot;fooo&quot; filter in &quot;extension/<ext>/design/<design>/templates/parts/menu.twig&quot; at line 1. (menu.twig:1) -->
```

`./console exp:template:check --design-dir=<dir>` finds such errors before a page does. With `[TwigSettings]
Profile=enabled` and the debug output on, every page writes a "twig profile" notice: time in operators, functions,
includes and cache blocks. [Troubleshooting](twig-templates-troubleshooting.md) has the messages and their fixes.

## Velocity

Velocity's persistent workers keep the Twig environment and the compiled classes in memory. A changed `.twig` gets a
new class name (the modification time is part of it), so a worker renders the new version on the next request
without a restart. The operators and functions are registered once per worker when it starts; PHP-FPM, which builds
the environment on every request, resolves only the names a template uses.

## Limits

- No method calls and no properties of objects in the sandbox (the default): read values through their attributes.
- No `break` and `continue` in Twig; macros and template inheritance have no `.tpl` form (the reverse converter keeps
  them as a note).
- `{% set %}` in an included `.twig` writes back to the shared namespace, as a Smarty subtemplate does; a `{def}` of an
  included `.tpl` is local to its file.
- An undefined variable prints nothing (`StrictVariables=disabled`), as in a `.tpl`.

## Related

- [Twig templates](../features/6.0/twig-templates.md) (feature page)
- [Writing Twig-native templates](twig-templates-native.md), [recipes](twig-templates-recipes.md),
  [debugging](twig-templates-debugging.md), [your editor](twig-templates-editors.md),
  [versions and compatibility](twig-templates-compatibility.md)
- [The three template engines compared](template-engines.md) and their [speed](template-engines-speed.md)
- [Moving from .tpl to .twig](../help/template-language/moving-from-tpl-to-twig.md)
