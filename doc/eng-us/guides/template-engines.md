# The three template engines compared

This guide is for site builders and template developers who choose between the template engines of Exponential:
Exponential templates (`.tpl`), Smarty templates (`.stpl`) and Twig templates (`.twig`, `.html.twig`). All three run in
the same template system, see the same variables, can call every operator, fetch function and template function, and
can be mixed in one design and one page. What differs is the language, its tools and who already knows it.

## At a glance

| | `.tpl` | `.stpl` | `.twig` |
|---|---|---|---|
| Language | Exponential's own template language | Smarty 5 | Twig 3 |
| Library | the kernel | Smarty 5 (`smarty/smarty`, `[SmartySettings] LibraryPath`) | Twig 3 (`twig/twig`, `[TwigSettings] LibraryPath`) |
| Settings | `template.ini` | `smarty.ini` | `twig.ini` |
| Compiled to | PHP (`<cache>/template/compiled`) | PHP (`<cache>/smarty/compile-secure`) | PHP classes (`<cache>/twig/compile-secure`) |
| Operators | by name | as modifiers; `exp_<name>` where Smarty has the name | as filters and functions; `exp_<name>` where Twig has the name |
| Template functions | `{attribute_view_gui ...}` | `{attribute_view_gui ...}` (same parameters) | `{{ attribute_view_gui({...}) }}` |
| Include | `{include uri=...}` | `{expinclude uri=...}` | `{% expinclude uri=... %}` |
| Cache block | `{cache-block}` | `{expcacheblock}` | `{% expcacheblock %}` |
| Sandbox | the template system's rules | Smarty's security policy, on by default | Twig's sandbox, on by default |
| Escaping | `wash` | `wash` (no auto escaping) | `wash` (no auto escaping by default) |
| Inheritance, macros | no | `{extends}`, `{block}`, `{function}`, as `smarty.ini` allows them | `{% extends %}`, `{% block %}`, `{% macro %}` |
| Debug bar | yes | yes, with the `.stpl` file | yes, with the `.twig` file |
| Converters | to and from both others | to and from both others | to and from both others |

## Which to choose

- **`.tpl`** for an existing design and for developers who know Exponential: every example, every shipped design and
  every extension's templates are `.tpl`, and nothing has to be installed.
- **`.stpl`** for a team that knows Smarty, or a design that comes from a Smarty project.
- **`.twig`** for a team that knows Twig from Symfony, Drupal or Craft, or templates shared with such a project
  (`.html.twig`). The editors and tools that support Twig work with the files as they are.

Speed does not decide it: cached pages never reach a template engine, and uncached pages render within a small
margin of each other. Measured on a development installation, a converted design rendered uncached pages as `.twig`
at 0.83 to 0.98 times the time of the `.tpl` design and 0.90 to 1.19 times the `.stpl` design
([Twig performance](twig-templates-performance.md), [Smarty performance](smarty-templates-performance.md)).

## Mixing them

Each template of a design can be of any engine. The design resource finds `x.tpl`, `x.stpl` or `x.twig` as the
override rules and the design order say, and every engine includes the others through it:

```
{include uri='design:parts/menu.twig'}                 .tpl includes a .twig
{expinclude uri='design:parts/menu.tpl'}               .stpl includes a .tpl (or what an override rule names)
{% expinclude uri='design:parts/menu.stpl' %}          .twig includes a .stpl
```

Variables cross with the include (its parameters, the shared namespace). A design can therefore move one template at
a time: convert the pagelayout, keep the rest, and convert more when it suits.

## Converting between them

Six commands convert in every direction, and `exp:template:convert` takes `--from-engine` and `--to-engine`:

```bash
./console exp:template:engines
./console exp:template:convert:tpl-to-twig --from=<design> --to=<design>_twig
./console exp:template:convert:twig-to-stpl --from=<design>_twig --to=<design>_stpl
```

Every converted file is checked before it is written and carries a marker, so a second run replaces only what was
converted and not changed since; see [Converting templates between template engines](template-conversion.md).
`exp:template:check` checks the syntax of all three ([Checking template syntax](template-syntax-check.md)).

## Related

- [Twig templates](../features/6.0/twig-templates.md)
- [Smarty templates](../features/6.0/smarty-templates.md)
- [Converting between template engines](../help/template-language/converting-between-template-engines.md)
