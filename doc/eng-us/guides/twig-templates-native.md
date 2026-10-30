# Writing Twig-native templates for Exponential

This guide is for template developers who write `.twig` templates by hand, the way Twig is written in Symfony, Drupal
or Craft projects, rather than converting `.tpl` files. A converted template looks like a `.tpl` written in Twig
syntax; a hand-written one can use what Twig does best: template inheritance, `embed`, macros, tests, the `?:` and
`??` operators, whitespace control. This page shows how each of those meets Exponential's own machinery: the override
system, the design fallback, the template namespaces, the sandbox and `wash`.

Every example was rendered by the template system on a development installation with the media demo content, in the
siteaccess of the Twig design (node 98 is the article "Why Track Your Workouts?"; `list` = `['a', 'b', 'c']`,
`user_name` = `'Ada'`, `price` = `12.5`, `html` = `'<b>bold</b>'`). The output is shown as it was printed.
The layout, component and macro files the examples use (`layout/article_base.twig`, `components/card.twig`,
`macros/ui.twig`) were scratch copies below the installation root when the examples ran; in a design they live in its
`templates/` directory.

## Two ways of finding a template

A `.twig` meets two different lookups, and knowing which one applies is the key to everything else on this page.

| Lookup | Used by | Override rules | Design fallback | Engines it finds |
|---|---|---|---|---|
| **The design resource** | the kernel (`content/view` and every module), `{% expinclude %}`, `node_view_gui`, `attribute_view_gui` and the other `*_view_gui` functions, `{include}` in a `.tpl` | yes | yes | `.tpl`, `.stpl`, `.twig` |
| **Twig's own loader** | `{% extends %}`, `{% include %}`, `{% embed %}`, `{% import %}`, `{% from %}`, `{% use %}` | no | yes, by directory order | `.twig` only |

Twig's loader looks for a name in the template directories of the siteaccess's designs, in the order the design
fallback uses (each design's `override/templates` and `templates`, the first design first), and finally below the
installation root. So `{% include 'parts/cookie_settings_link.twig' %}` finds the file of the first design that has
`templates/parts/cookie_settings_link.twig`, and `'extension/<ext>/design/<design>/templates/x.twig'` names one file
exactly:

```twig
{% include 'parts/cookie_settings_link.twig' %}
```

```
<a href="/cookie-policy#cookie-settings" class="js-open-ng-cc d-block my-2">Cookie settings</a>
```

**Rule of thumb:** use the design resource (`expinclude`, `*_view_gui`) for anything an override rule should be able
to replace, such as views of nodes and attributes and the parts a site customizes. Use Twig's loader for the building
blocks of your own design: layouts you extend, components you embed, macro files.

## Template inheritance with extends and blocks

A base template defines blocks; a child extends it and fills the blocks it wants to change. `parent()` prints the
base's content of a block.

`templates/layout/article_base.twig` of the design:

```twig
<article class="{% block css_class %}article{% endblock %}">
  <h1>{% block title %}{{ node.name|wash }}{% endblock %}</h1>
  {% block body %}<p>No body.</p>{% endblock %}
</article>
```

The full view of an article, found by an override rule as any `.twig`:

```twig
{% extends 'layout/article_base.twig' %}
{% block css_class %}{{ parent() }} article--featured{% endblock %}
{% block body %}<p>{{ node.data_map|length }} fields</p>{% endblock %}
```

```
<article class="article article--featured">
  <h1>Why Track Your Workouts?</h1>
  <p>18 fields</p></article>
```

Note the missing line break before `</article>`: Twig drops the first newline after a block tag, here after
`{% endblock %}` in the base. Write the newline twice, or put the closing tag on the same line, where it matters.

### Inheritance together with override rules

The override system decides **which file** renders a request; inheritance decides **how that file is built**. They
work together like this:

1. An override rule points the kernel's URI at a `.twig`, for example `node/view/full.tpl` for articles at
   `full/article.twig`.
2. `full/article.twig` extends a base layout by name: `{% extends 'layout/article_base.twig' %}`.
3. Twig finds `layout/article_base.twig` in the first design of the siteaccess that has it. A design higher in the
   design list can therefore replace the base layout for every article view at once, without an override rule, just
   by having a file of the same name.

What does not work: a child cannot extend a `.tpl` or `.stpl`, and an `{% extends %}` name is never run through
override rules. If the base itself should be chosen by an override rule, render it with `{% expinclude %}` instead
and pass the parts as variables, or use `embed` (below) for a component.

The pagelayout is a template like any other: `pagelayout.twig` may extend a `layout/base.twig` of your design, and
module views rendered into `module_result.content` stay what they are.

## include, embed and expinclude

| | Finds the file through | Override rules | Variables | Writes variables back |
|---|---|---|---|---|
| `{% expinclude uri='design:x.tpl' a=b %}` | the design resource | yes | the namespace and the parameters | yes, what the included `.twig` sets |
| `{% include 'x.twig' with {a: b} %}` | Twig's loader | no | a copy of the context, plus `with` (`only` for none) | no |
| `{% embed 'x.twig' %}...{% endembed %}` | Twig's loader | no | as `include` | no |

`embed` includes a template and lets you override its blocks at the place of inclusion: a component with slots.

`templates/components/card.twig`:

```twig
<div class="card">
  <h2>{% block heading %}Card{% endblock %}</h2>
  <div class="card-body">{% block content %}{% endblock %}</div>
</div>
```

```twig
{% embed 'components/card.twig' %}
  {% block heading %}{{ user_name|wash }}'s list{% endblock %}
  {% block content %}{{ list|join(', ') }}{% endblock %}
{% endembed %}
```

```
<div class="card">
  <h2>Ada's list</h2>
  <div class="card-body">a, b, c</div>
</div>
```

A missing optional part: `ignore missing` prints nothing instead of failing.

```twig
[{% include 'parts/no_such_file.twig' ignore missing %}]
```

```
[]
```

`with {...} only` gives the included template only the variables you list:

```twig
{% with {greeting: 'Hello'} only %}{{ greeting }} {{ user_name|default('nobody') }}{% endwith %}
```

```
Hello nobody
```

## Macros

Macros are Twig's reusable snippets with parameters; a `.tpl` has no equivalent (the nearest is an `{include}` with
parameters). Put them in a file and import it:

`templates/macros/ui.twig`:

```twig
{% macro badge(text, kind = 'info') %}<span class="badge badge-{{ kind }}">{{ text|wash }}</span>{% endmacro %}
{% macro price(amount) %}{{ amount|l10n('currency') }}{% endmacro %}
```

```twig
{% import 'macros/ui.twig' as ui %}
{{ ui.badge('New') }} {{ ui.badge('Sale', 'warning') }} {{ ui.price(price) }}
```

```
<span class="badge badge-info">New</span> <span class="badge badge-warning">Sale</span> $12.50
```

A macro of the same file is called through `_self`:

```twig
{% macro star(n) %}{% for i in 1..n %}*{% endfor %}{% endmacro %}
{{ _self.star(3) }}
```

```
***
```

Macros run in their own scope: they see their parameters, not the variables of the template that calls them, and
what they `set` stays inside. Exponential's operators work in them as anywhere else (`l10n` above).

## Filters, functions and tests

Twig has three kinds of named things, and the template system's operators take part in two of them:

| Kind | Syntax | Exponential's part |
|---|---|---|
| filter | `value\|name(args)` | every operator, with the value as its input (`node.name\|wash`) |
| function | `name(args)` | every operator without an input (`cond(...)`, `fetch(...)`), every template function without a body (`attribute_view_gui({...})`) |
| test | `value is name` | Twig's own only: `defined`, `empty`, `null`, `odd`, `even`, `iterable`, `same as`, `divisible by` |

Tests are not checked by the sandbox, so all of Twig's tests work:

```twig
[{% if missing is defined %}defined{% else %}not defined{% endif %}] [{{ list is empty ? 'empty' : 'not empty' }}] [{% for i in 1..4 %}{{ i is odd ? 'o' : 'e' }}{% endfor %}] [{{ price is same as(12.5) ? 'same' : 'different' }}]
```

```
[not defined] [not empty] [oeoe] [same]
```

Twig's own operators work next to Exponential's, so you can write conditions and arithmetic the Twig way:

```twig
{% set total = 0 %}{% for n in [2, 3, 4] %}{% set total = total + n %}{% endfor %}{{ total }} {{ 7 // 2 }} {{ 7 % 3 }} {{ 2 ** 10 }} {{ 'a' ~ 'b' }} {{ price > 10 and price < 20 ? 'mid' : 'other' }}
```

```
9 3 1 1024 ab mid
```

When a name exists both in Twig and as an operator, the plain name is Twig's and the operator is `exp_<name>`
([Operators and fetch functions](twig-templates-operators.md#the-prefix-exp)). Mind names that are no operator at
all: `exp_upper` is not an operator (the operator is `upcase`), so it passes its input through unchanged and writes
`Unknown operator upper` to the debug output:

```twig
{{ node.name|upcase }} / {{ node.name|exp_upper }}
```

```
WHY TRACK YOUR WORKOUTS? / Why Track Your Workouts?
```

`./console exp:template:check --level=strict` reports such a name as `TWIG-STRICT-UNKNOWN-OPERATOR` before a page does.

## Escaping: wash, autoescape and |raw

By default (`[TwigSettings] Autoescape=disabled`) a `.twig` escapes nothing on its own, exactly like a `.tpl`: escape
every value that comes from input or content with `wash` (or Twig's `escape`, `e`):

```twig
{{ html }}|{{ html|raw }}|{{ html|wash }}|{{ attribute_view_gui({attribute: node.data_map.title})|trim }}
```

```
<b>bold</b>|<b>bold</b>|&lt;b&gt;bold&lt;/b&gt;|Why Track Your Workouts?
```

With `Autoescape=enabled` in the siteaccess's `twig.ini.append.php`, Twig escapes every printed value, and the same
template prints:

```
&lt;b&gt;bold&lt;/b&gt;|<b>bold</b>|&amp;lt;b&amp;gt;bold&amp;lt;/b&amp;gt;|Why Track Your Workouts?
```

Three things to take from that:

- `|raw` is the way to print a value that is already HTML once autoescape is on.
- `|wash` under autoescape escapes **twice** (`&amp;lt;`): use one of the two, not both. A converted design uses
  `wash` everywhere, so leave autoescape off for it.
- The output of the `*_view_gui` functions is printed as it is: they render templates that do their own escaping.

For hand-written designs autoescape is a reasonable choice, but switch it on per siteaccess and never for a design
that also holds converted templates. [Security and escaping](twig-templates-security.md) has the details.

## Whitespace control

Twig removes the first newline after a tag and nothing else; `-` inside a tag delimiter trims all whitespace on that
side:

```twig
A {{- ' B ' -}} C|{{ '  x  '|trim }}|
{%- if true %}
  yes
{%- endif %}|
```

```
A B C|x|  yes|
```

```twig
<ul>
{%- for item in list %}
  <li>{{ item }}</li>
{%- endfor %}
</ul>
```

```
<ul>  <li>a</li>  <li>b</li>  <li>c</li></ul>
```

The `spaceless` filter is not in the sandbox's allowed list by default; trim with `-` instead, or allow the filter for
a siteaccess you trust (`SecurityAllowedFilterList[]=spaceless`).

## What the sandbox allows

The sandbox (`[TwigSettings] Security=enabled`, the default) decides what a hand-written template may use of Twig's own
vocabulary. Everything Exponential adds (operators, template functions, the `exp` tags) is allowed. Of Twig's own:

| Allowed by default | Refused by default (examples) |
|---|---|
| tags `if`, `for`, `set`, `apply`, `block`, `extends`, `include`, `embed`, `macro`, `import`, `from`, `use`, `with`, `verbatim`, `guard`, `types` | `do`, `autoescape`, `sandbox`, `flush`, `deprecated` |
| filters `abs`, `batch`, `capitalize`, `column`, `date`, `default`, `e`, `escape`, `first`, `format`, `join`, `json_encode`, `keys`, `last`, `length`, `lower`, `merge`, `nl2br`, `number_format`, `raw`, `replace`, `reverse`, `round`, `slice`, `sort`, `split`, `striptags`, `title`, `trim`, `upper`, `url_encode` | `filter`, `map`, `reduce`, `spaceless`, `format_*` of extensions |
| functions `attribute`, `block`, `cycle`, `max`, `min`, `parent`, `random`, `range` | `constant`, `include` (the function; the tag is allowed), `source`, `dump` |
| every test | — |
| reading attributes (`node.name`) | method calls (`node.name()`), object properties |

What a refused construct looks like when the page renders, with the debug output on:

```
<!-- twig: rendering <root>/var/tmp/doc-examples/example.twig failed: Filter &quot;filter&quot; is not allowed in &quot;var/tmp/doc-examples/example.twig&quot; at line 1. (example.twig:1) -->
```

The arrow-function filters (`filter`, `map`, `reduce`) are refused because their arrow functions run arbitrary
expressions; use a `for` loop with an `if`, or allow them for a siteaccess you trust. The lists are in
`settings/twig.ini`; [Security and escaping](twig-templates-security.md) explains each setting.

## Variables a template sees

A `.twig` sees the variables of the namespace it runs in, as a `.tpl` would. Which ones depends on who renders it:

| Template | Variables |
|---|---|
| `pagelayout.twig` | `module_result` (with `content`, `path`, `content_info`, `view_parameters`, `node_id` ...), `site` (title, URIs, the design), `current_user`, `anonymous_user_id`, `access_type` (the siteaccess: `access_type.name`), `navigation_part`, `uri_string`, `requested_uri_string`, `ui_context`, `ui_component`, `warning_list` |
| a node view (`content/view`) | `node`, `viewmode`, `language_code`, `view_parameters`, `collection_attributes`, `validation`, `persistent_variable`, and the design keys; a view rendered by `node_view_gui` gets `node` and the function's parameters |
| an attribute view (`attribute_view_gui`) | `attribute` and the function's other parameters |
| an included template | the parameters of the include, and the shared namespace for `expinclude` |
| a mail or notification template | the variables the module sets (for example `node`, `object`); it sets `subject` and the like for the module to read back |

The **design keys** a node view was chosen with live in the namespace `DesignKeys` (`$DesignKeys:used` in a `.tpl`);
read them with `expvar(name, namespace)`:

```twig
{{ expvar('used', 'DesignKeys').class_identifier }} / {{ expvar('used', 'DesignKeys').viewmode }} / {{ expvar('matched', 'DesignKeys')|keys|join(',') }}
```

```
article / line / class_identifier,viewmode
```

Values that every template can fetch for itself, whatever namespace it runs in:

| Value | Twig |
|---|---|
| the current user | `fetch('user', 'current_user')` (`.login`, `.is_logged_in`, `.contentobject.name`) |
| a setting | `ezini('SiteSettings', 'SiteName')`, `ezini('TwigSettings', 'Security', 'twig.ini')` |
| the current siteaccess | `access_type.name` in the pagelayout; elsewhere `ezini('SiteAccessSettings', ...)` |
| a node | `fetch('content', 'node', {node_id: 2})` |

```twig
{% set user = fetch('user', 'current_user') %}{{ user.login }} {{ user.is_logged_in ? 'signed in' : 'anonymous' }} {{ user.contentobject.name|wash }}
{{ ezini('SiteSettings', 'SiteName') }} / {{ ezini('DesignSettings', 'SiteDesign') }} / {{ ezini('TwigSettings', 'Security', 'twig.ini') }}
```

```
anonymous anonymous Anonymous User
Fit & Healthy / twig_media / enabled
```

Twig has no globals of Exponential's: everything comes from the namespace or from a fetch, as in a `.tpl`, so a
template behaves the same in every engine and under Velocity's persistent workers.

## Writing variables for the page

What a `.twig` sets with `{% set %}` is written back to its namespace when it ends, so a node view can hand a value
to the pagelayout as a `.tpl` does. For another scope, use `expset` and `expsetblock`:

```twig
{% expsetblock variable='subject' scope='root' %}New article: {{ node.name|wash }}{% endexpsetblock %}[{{ subject }}]
```

```
[New article: Why Track Your Workouts?]
```

`scope='global'` reaches the pagelayout from any depth (the persistent variable of a node view, `cache_ttl`), as in
[Template functions and tags](twig-templates-functions.md#the-exp-tags).

## A complete hand-written full view

Putting the pieces together, a full view that extends a base layout, imports macros, renders the body through the
attribute's own template (so its override rules apply) and lists siblings through their line views:

```twig
{% extends 'layout/article_base.twig' %}
{% import 'macros/ui.twig' as ui %}

{% block title %}{{ node.name|wash }} {{ ui.badge(node.object.class_identifier) }}{% endblock %}

{% block body %}
  <p class="meta">{{ node.object.published|l10n('shortdate') }}</p>
  {{ attribute_view_gui({attribute: node.data_map.body}) }}
  {% set siblings = fetch('content', 'list', {parent_node_id: node.parent_node_id, limit: 3}) %}
  {% if siblings is not empty %}
    <ul>
    {%- for child in siblings %}{% if child.node_id != node.node_id %}
      <li>{{ node_view_gui({content_node: child, view: 'line'}) }}</li>
    {%- endif %}{% endfor %}
    </ul>
  {% endif %}
{% endblock %}
```

Twig 3 has no `for ... if` loop filter (it was removed with Twig 3.0), and the `filter` filter is refused by the
sandbox, so the condition goes inside the loop. The same template with the scratch copies of the base layout and the
macros, rendered with the body and the line views left out to keep the output short, printed:

```
<article class="article">
  <h1>Why Track Your Workouts? <span class="badge badge-info">ng_article</span></h1>
    <p class="meta">07/23/2018</p>
        <ul>      <li>7 Keystone Habits to Maximize Your Mornings</li>      <li>The BiPolar Learning Curve</li>      <li>Split Options Weight Training Program</li>    </ul>
  </article>
```

## Related

- [Writing and running .twig templates](twig-templates.md)
- [Recipes](twig-templates-recipes.md)
- [Security and escaping](twig-templates-security.md)
- [Template functions and tags](twig-templates-functions.md)
- [Templates side by side: .tpl, .twig and .stpl](../help/template-language/tpl-twig-smarty-side-by-side.md)
