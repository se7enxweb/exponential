# Twig templates (.twig) next to .tpl

This page is for site builders and template developers who know Twig, or who want to port a design to it. A design
can now hold Twig 3 templates (`*.twig`, and `*.html.twig` for templates shared with Symfony-style projects) next to
the usual Exponential templates (`*.tpl`) and Smarty templates (`*.stpl`). A `.twig` is found, overridden, included
and cached like any template, sees the same variables, and can use every Exponential template operator, every fetch
function, the `*_view_gui` functions and every block function. A converter turns a whole design of `.tpl` (or `.stpl`)
files into `.twig` and back. The feature is part of the kernel library: the code is in `lib/twig/`, the settings are in
`settings/twig.ini`, and nothing has to be activated: `.twig` templates work in every siteaccess. Added in October
2026. Coming from `.tpl`: [Moving from .tpl to .twig](../../help/template-language/moving-from-tpl-to-twig.md).
Choosing between the three engines: [The three template engines compared](../../guides/template-engines.md).

The guides go further:

| Guide | For |
|---|---|
| [A first .twig, step by step](../../guides/twig-templates-tutorial.md) | your first Twig template: a siteaccess, an override rule, variables, operators, a fetch, a loop, an include |
| [Writing and running .twig templates](../../guides/twig-templates.md) | how it works: the stub, setup, variables and write-back, objects, includes, loops, functions, tags, caching, debugging, Velocity, limits |
| [Operators and fetch functions](../../guides/twig-templates-operators.md) | filters and functions, how an operator gets its input, the `exp_` prefix, `fetch()`, the helpers |
| [Operator reference](../../guides/twig-templates-operator-reference.md) | every operator of the installation and its extensions, with its filter and function form |
| [Template functions and tags](../../guides/twig-templates-functions.md) | the `*_view_gui` functions, block functions as tags, the `exp*` tags, Twig's own tags |
| [Caching](../../guides/twig-templates-caching.md) | the cache layers, `{% expcacheblock %}`, the page block with a per-visitor placeholder, keys and expiry |
| [Performance (Speed)](../../guides/twig-templates-performance.md) | what makes `.twig` fast, the measurements, how to measure your own pages |
| [The converter and porting a design](../../guides/twig-templates-converter.md) | `exp:template:convert:tpl-to-twig` and `:twig-to-tpl`, every construct they write, their notes |
| [Migrating a design](../../guides/twig-templates-migrating.md) | planning, converting, checking, page-by-page parity, keeping the engines in step |
| [Troubleshooting and FAQ](../../guides/twig-templates-troubleshooting.md) | error messages with their cause and fix, common questions |
| [Writing operators and functions](../../guides/twig-templates-plugins.md) | your own operators and functions that work in every engine |
| [Security and escaping](../../guides/twig-templates-security.md) | the sandbox, `wash`, autoescape, what a template can reach |
| [Writing Twig-native templates](../../guides/twig-templates-native.md) | inheritance with the override system, `include` and `embed`, macros, tests, escaping, whitespace, the sandbox, the variables a template sees |
| [Recipes](../../guides/twig-templates-recipes.md) | a page layout, a line view, a layouts block, a mail template, cache blocks per role and user, a form, texts with a count, image aliases |
| [Debugging Twig templates](../../guides/twig-templates-debugging.md) | the check, the message in the page, path comments, the debug bar, the Twig profile, a change that does not show |
| [Twig in your editor](../../guides/twig-templates-editors.md) | syntax highlighting, file endings, what an editor's checks cannot know, the checker in the editor, snippets |
| [Versions, upgrades and compatibility](../../guides/twig-templates-compatibility.md) | Twig per PHP version, Composer, Twig missing, upgrading, Velocity |
| [Speed of the three template engines](../../guides/template-engines-speed.md) | Twig, Smarty and `.tpl` measured: cached, uncached and the debug render, with conditions and how to repeat it |
| [Templates side by side](../../help/template-language/tpl-twig-smarty-side-by-side.md) | the same template in `.tpl`, `.twig` and `.stpl`, and every operator in Twig |
| [Converting templates between template engines](../../guides/template-conversion.md) | the `exp:template:convert` command space, every direction |
| [Checking template syntax](../../guides/template-syntax-check.md) | `exp:template:check` for `.tpl`, `.stpl` and `.twig` |

## What it does

- **A template engine of its own, in the same template system.** When the design resource (or an `{include}`) finds a
  `.twig`, the template system does not parse it: it parses a one-line stub, `{exp_twig_render( '<file>' )}`, which
  renders the file with Twig on every request. Everything around it stays as it is: override rules, design fallback,
  `design:` URIs, view caches, `{cache-block}`s of the calling templates, the debug bar.
- **The same variables.** A `.twig` sees the variables of its namespace (`node`, `module_result`, `view_parameters`
  ...). What it sets with `{% set %}`, changes or sets to `null` is written back, as `{def}`, `{set}` and `{undef}` do
  in a `.tpl`, so a pagelayout, a module or a mail template reads it.
- **Every operator.** `{{ node.name|wash }}`, `{{ 'Close'|i18n('design/standard/content') }}`,
  `{{ fetch('content', 'list', {parent_node_id: 2}) }}`. A filter is the operator with an input, a function the
  operator without one, exactly as the `.tpl` writes it.
- **Every template function.** `{{ attribute_view_gui({attribute: node.data_map.body}) }}`,
  `{{ node_view_gui({content_node: child, view: 'line'}) }}`; a function with a body is a tag:
  `{% explblock name='x' %}...{% endexplblock %}`.
- **Includes in every direction.** `{% expinclude uri='design:parts/x.tpl' %}` includes a `.tpl`, `.stpl` or `.twig`
  through the design resource, with its override rules; a `.tpl` includes a `.twig` with `{include}`.
- **Caching.** `{% expcacheblock keys=[...] %}...{% endexpcacheblock %}` is the template system's `{cache-block}`.
- **A sandbox on by default.** Bridged operators and functions run with the template system's own rules; Twig's
  own tags, filters and functions only as `twig.ini` lists them; no method call and no property of an object.
- **Converters in every direction** and a **syntax checker** for all three engines.

A first look:

```twig
<h1>{{ node.name|wash }}</h1>
{% for child in fetch('content', 'list', {parent_node_id: node.node_id, limit: 3}) %}
    {{ node_view_gui({content_node: child, view: 'line'}) }}
{% endfor %}
```

## Settings

`settings/twig.ini [TwigSettings]` (override it like any settings file):

| Setting | Default | Meaning |
|---|---|---|
| `LibraryPath` | empty | where Twig 3 is loaded from when the installation's Composer autoload has no `twig/twig`: a Composer directory (with `vendor/autoload.php`) or the package itself |
| `AutoReload` | `enabled` | a changed `.twig` is compiled again; `disabled` saves a file check per template (clear the template caches after a deploy) |
| `Debug` | `disabled` | Twig's debug mode (`dump()`) |
| `StrictVariables` | `disabled` | an undefined variable prints nothing, as in a `.tpl` |
| `Autoescape` | `disabled` | nothing is escaped unless the template says so (`\|wash`), as in a `.tpl`; see [Security and escaping](../../guides/twig-templates-security.md) |
| `TemplateSuffixList[]` | `.twig`, `.html.twig` | the endings Twig renders |
| `WhileLoopLimit` | `10000` | the most rounds of a converted `{while}` or `{do}` |
| `FunctionList[]` | the `*_view_gui` functions | template functions callable as Twig functions (every function without a body is bridged anyway) |
| `ParameterOperatorList[]` | empty | operators that read their parameters only (also `site.ini [TemplateBridgeSettings] ParameterOperatorList[]`) |
| `Security` | `enabled` | the sandbox |
| `SecurityAllowedTagList[]`, `SecurityAllowedFilterList[]`, `SecurityAllowedFunctionList[]` | Twig's safe ones | what the sandbox allows of Twig's own |
| `SecurityAllowedMethodList[]`, `SecurityAllowedPropertyList[]` | empty | `Class::method`, `Class::property` allowed |

The file endings of all engines and the class that renders each are in `settings/site.ini [TemplateBridgeSettings]`
(`EngineSuffixList[]`, `RendererList[]`); the converters in `ConverterList[]`, the syntax checkers in `CheckerList[]`.

## Installing Twig

The installation's `composer.json` requires `twig/twig` `^3`. After `composer update`, nothing else is needed.
Without it (or to try a newer Twig), install it in a directory of its own and point `LibraryPath` at it:

```bash
mkdir -p var/lib/twig-vendor && cd var/lib/twig-vendor
composer require twig/twig:^3
```

```ini
# settings/override/twig.ini.append.php
[TwigSettings]
LibraryPath=var/lib/twig-vendor
```

`./console exp:template:engines` shows which engines are available, their versions and settings:

```
AVAILABLE    tpl (Exponential templates) Exponential 6.0.15stable
AVAILABLE    stpl (Smarty templates) Smarty 5.8.5
AVAILABLE    twig (Twig templates)  Twig 3.30.0
PASS 3 engines available
```

## How fast it is

A `.twig` is compiled once to PHP (kept in `<cache directory>/twig/compile-secure`, cleared with the template caches),
and one Twig environment is kept per process, so Velocity's persistent workers compile and load each template once.
On a development installation the converted media design renders the same pages as `.tpl` and `.stpl` at about the
speed of `.stpl`; [Performance (Speed)](../../guides/twig-templates-performance.md) has the measurements and how
they were taken.

## Converting and checking

```bash
./console exp:template:convert:tpl-to-twig --from=extension/<ext>/design/<design> --to=extension/<ext>/design/<design>_twig
./console exp:template:check --design-dir=extension/<ext>/design/<design>_twig
```

Converting the media design (517 templates) writes 517 `.twig` that compile, and every page tested on a development
installation renders byte for byte what the `.tpl` design renders, on Apache with PHP-FPM and on Velocity. The 453
cases of the template engine's own tests give the same output in `.twig` as in `.tpl`, with and without the sandbox.

## Limits

- A `.twig` included from a `.twig` in the shared namespace writes back the variables it sets, as a Smarty subtemplate
  does; a `{def}` of an included `.tpl` is local to its file and gone when it ends.
- Twig 3 has no `break`/`continue`: the converter writes them as flags; hand-written templates use loop filters or
  `{% if %}`.
- Template inheritance (`{% extends %}`, `{% block %}`) and macros work in hand-written `.twig`, but have no `.tpl`
  form: the reverse converter keeps them as a note and a comment.
- Method calls on objects are refused by the sandbox; read values through their attributes (`node.data_map`).
