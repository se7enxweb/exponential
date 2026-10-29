# Twig templates: template functions and tags

This guide is for template developers who call Exponential template functions from `.twig`: the `*_view_gui`
functions, the functions with a body (block functions), and the `exp*` tags that stand for the `.tpl`'s own
constructs (`{include}`, `{set-block}`, `{cache-block}`, `{run-once}`). It also says which of Twig's own tags a
template may use. The examples were rendered by the template system on a development installation (node 98 is an
article, `user_name` is `'Ada'`).

## Functions without a body: Twig functions with one hash

Every template function without a body is a Twig function that takes one hash of named parameters, the `.tpl`'s
`name=value` pairs:

| `.tpl` | `.twig` |
|---|---|
| `{attribute_view_gui attribute=$node.data_map.title}` | `{{ attribute_view_gui({attribute: node.data_map.title}) }}` |
| `{node_view_gui content_node=$child view='line'}` | `{{ node_view_gui({content_node: child, view: 'line'}) }}` |
| `{content_view_gui content_object=$object view='embed'}` | `{{ content_view_gui({content_object: object, view: 'embed'}) }}` |

```twig
{{ attribute_view_gui({attribute: node.data_map.title}) }}
```

```
Why Track Your Workouts?
```

The function runs in the template system exactly as in a `.tpl`: it finds the datatype's or the view's template
through the design resource, so its override rules apply and it may render a `.tpl`, `.stpl` or `.twig`.
`[TwigSettings] FunctionList[]` lists the `*_view_gui` functions; every other function without a body is bridged
anyway.

## Functions with a body: tags

A template function with a body (a block function of the kernel or an extension) is a Twig tag of the same name with
`end<name>`:

```twig
{% explblock name='content' %}
    ...
{% endexplblock %}
```

The parameters are written as in the `.tpl`, `name=value`, values in Twig syntax.

## The exp tags

| `.tpl` | `.twig` | What it does |
|---|---|---|
| `{include uri='design:parts/x.tpl' a=$b}` | `{% expinclude uri='design:parts/x.tpl' a=b %}` | includes through the design resource, whatever the engine of the file it finds; parameters become variables of the included template |
| `{set-block variable=x}...{/set-block}` | `{% set x %}...{% endset %}` | the body as a variable (Twig's own capture) |
| `{set-block variable=x scope=global}...{/set-block}` | `{% expsetblock variable='x' scope='global' %}...{% endexpsetblock %}` | the body as a variable of another scope |
| `{append-block variable=x}...{/append-block}` | `{% expappendblock variable='x' %}...{% endexpappendblock %}` | appends the body to the list `x` |
| `{cache-block keys=...}...{/cache-block}` | `{% expcacheblock keys=[...] %}...{% endexpcacheblock %}` | the template system's cache block ([Caching](twig-templates-caching.md)) |
| `{run-once}...{/run-once}` | `{% exprunonce %}...{% endexprunonce %}` | the body once per page |
| `{set scope=global x=2}` | `{% expset name='x' value=2 scope='global' %}` | sets a variable in another scope |
| `{while ...}...{/while}`, `{do}...{/do}` | `{% expwhile ... %}...{% endexpwhile %}` | a loop with a condition, at most `[TwigSettings] WhileLoopLimit` rounds |
| `{undef}` (no names) | `{% expundefall %}` | removes every variable the template set |

```twig
{% expappendblock variable='more' %}a{% endexpappendblock %}{% expappendblock variable='more' %}b{% endexpappendblock %}{{ more|implode(',') }}
{% expsetblock variable='greeting' scope='global' %}Hello {{ user_name|wash }}{% endexpsetblock %}[{{ expvar('greeting') }}]
{% for i in [1, 2, 3] %}{% exprunonce %}once {% endexprunonce %}{{ i }} {% endfor %}
```

```
a,b
[Hello Ada]
once 1 2 3
```

## Includes between engines

- A `.twig` includes any template through the design resource: `{% expinclude uri='design:parts/menu.tpl' %}`. If an
  override rule or the design has a `.twig` or `.stpl` for that URI, that file renders.
- A `.tpl` includes a `.twig` with `{include uri='design:parts/menu.twig'}`, or by a `.tpl` URI that an override rule
  points at a `.twig`.
- An included `.twig` in the shared namespace writes back the variables it sets, as a Smarty subtemplate does.

Twig's own `{% include 'file.twig' %}` and `{% embed %}` work for `.twig` files by path below the design's template
directories; they do not apply override rules.

## Twig's own tags

The sandbox (on by default) allows these of Twig's tags: `if`, `for`, `set`, `apply`, `block`, `extends`,
`include`, `embed`, `macro`, `import`, `from`, `use`, `with`, `verbatim`, `guard`, `types`, and every `exp` tag.
`[TwigSettings] SecurityAllowedTagList[]` changes the list; a tag outside it is reported by
`exp:template:check --level=strict` (`TWIG-SECURITY-001`) and refused when the template is compiled.

## Related

- [Operators and fetch functions](twig-templates-operators.md)
- [Template function examples](../examples/template-functions/README.md)
- [Writing operators and functions](twig-templates-plugins.md)
