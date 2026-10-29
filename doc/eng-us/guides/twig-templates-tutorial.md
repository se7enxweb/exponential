# A first .twig, step by step

This tutorial is for a template developer who has written `.tpl` templates (or Twig in another project) and wants a
first Exponential page in Twig. It builds the full view of an article as a `.twig`: an override rule, the node's
variables, operators, a translation, a fetch, a loop and an include. Every output below was printed by the template
system on a development installation with the media demo content (node 98 is the article "Why Track Your Workouts?").

## 1. Check that Twig is there

```bash
./console exp:template:engines
```

```
AVAILABLE    tpl (Exponential templates) Exponential 6.0.15stable
AVAILABLE    stpl (Smarty templates) Smarty 5.8.5
AVAILABLE    twig (Twig templates)  Twig 3.30.0
PASS 3 engines available
```

If `twig` is not available, see [Installing Twig](../features/6.0/twig-templates.md#installing-twig).

## 2. An override rule that points at a .twig

In the design of your siteaccess, `settings/siteaccess/<siteaccess>/override.ini.append.php`:

```ini
[full_article_twig]
Source=node/view/full.tpl
MatchFile=full/article.twig
Subdir=templates
Match[class_identifier]=article
```

and the file `extension/<ext>/design/<design>/override/templates/full/article.twig`. Clear the override cache once
(`./console exp:ezcache --clear-tag=template`); after that a changed `.twig` is picked up on the next request.

## 3. The node and its attributes

```twig
<h1>{{ node.name|wash }}</h1>
<p>{{ node.data_map|length }} attributes; title: {{ node.data_map.title.content|wash }}</p>
```

```
<h1>Why Track Your Workouts?</h1>
<p>18 attributes; title: Why Track Your Workouts?</p>
```

`node` is the same `eZContentObjectTreeNode` a `.tpl` gets as `$node`; `.` reads an attribute, as in a `.tpl`.

## 4. Operators

Every template operator works, as a filter (with an input) or a function (without one):

```twig
{{ node.url_alias|ezurl }}
{{ node.object.published|l10n('shortdate') }}
{{ 'Close'|i18n('design/standard/content') }}
```

```
"/fitness/why-track-your-workouts"
07/23/2018
Close
```

Mind `wash`: nothing is escaped unless you say so, as in a `.tpl`. With `html` set to `<b>bold</b>`:

```twig
{{ html }} / {{ html|wash }}
```

```
<b>bold</b> / &lt;b&gt;bold&lt;/b&gt;
```

## 5. A fetch and a loop

```twig
{% set children = fetch('content', 'list', {parent_node_id: node.parent_node_id, limit: 3}) %}
{{ children|length }} children: {% for child in children %}{{ child.name|wash }}; {% endfor %}
```

```
3 children: 7 Keystone Habits to Maximize Your Mornings; The BiPolar Learning Curve; Split Options Weight Training Program;
```

In a real list, render each child with its line view:

```twig
{% for child in children %}
    {{ node_view_gui({content_node: child, view: 'line'}) }}
{% endfor %}
```

## 6. A condition

```twig
{{ cond(price|gt(10), 'expensive', 'cheap') }}
{% if price|gt(10) %}expensive{% else %}cheap{% endif %}
```

With `price` 12.5 both print `expensive`. The operators of the `.tpl` (`gt`, `and`, `cond`) and Twig's own operators
(`>`, `and`, `? :`) both work; a converted template uses the operators, so its results match the `.tpl` exactly.

## 7. An include

```twig
{% expinclude uri='design:parts/related.tpl' node=node %}
```

`expinclude` is the template system's `{include}`: it finds the template through the design resource, override
rules and fallback included, whatever its engine. Parameters become variables of the included template.

## 8. Check it

```bash
./console exp:template:check extension/<ext>/design/<design>/override/templates/full/article.twig
```

A typing mistake such as `|wahs` is a compile error in Twig (`Unknown "wahs" filter`), which the check reports with the
file and line before any visitor sees the page.

## Next

- [Writing and running .twig templates](twig-templates.md): how it all works
- [Operators and fetch functions](twig-templates-operators.md)
- [The converter and porting a design](twig-templates-converter.md): convert a whole `.tpl` design instead of writing
  it by hand
