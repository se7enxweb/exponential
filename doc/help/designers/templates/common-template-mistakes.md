---
title: Common template mistakes
audience: designer, developer
category: templates
order: 350
summary: The template mistakes that cause most bugs, security holes and slow pages, each with its symptom and fix: unwashed output, fetch in loops, missing limits, cache keys, user data in cached views, quotes around ezurl, variable clashes, missing has_content, editing shipped templates, new templates not showing.
keywords: common mistakes, template bugs, pitfalls, unwashed output, fetch in loop, cache key, ezurl quotes, variable clash, has_content, template override cache, troubleshooting templates
type: help
---
# Common template mistakes

This page is for designers who want to avoid the usual traps. Each mistake has a symptom, so you can also use the page
to find the cause of a problem.

## Contents

1. [Output and security](#output-and-security)
2. [Data and speed](#data-and-speed)
3. [Caching](#caching)
4. [Syntax](#syntax)
5. [Files and caches](#files-and-caches)

## Output and security

| Mistake | Symptom | Fix |
|---|---|---|
| printing a value without `\|wash` | HTML from content breaks the page; script injection possible | wash at output ([Wash and escaping](wash-and-escaping.md)) |
| washing rich text output | tags shown as text | do not wash `attribute_view_gui` output |
| washing twice | `&amp;amp;` on the page | wash once, at the end |
| inline `<script>` or `onclick` | blocked under a Content Security Policy | script files ([No inline JavaScript](../theme-design/no-inline-javascript.md)) |

## Data and speed

| Mistake | Symptom | Fix |
|---|---|---|
| a fetch inside a `foreach` | slow pages, many queries in the debug output | one fetch before the loop ([Fetch performance](fetch-performance.md)) |
| a list fetch without `limit` | very slow pages on big folders | always a `limit`, paginate |
| reading `$node.children` for a menu | all children loaded with all data | a fetch with `limit` and `class_filter_array` |
| drawing an empty field | empty wrappers, stray labels | test `has_content` first |

## Caching

| Mistake | Symptom | Fix |
|---|---|---|
| user data in a view-cached full view | one visitor sees another's name | move it to the pagelayout or vary the cache ([Caching for designers](../theme-design/caching-for-designers.md)) |
| a cache-block without the current page in its keys | the menu marks the wrong page | add `$module_result.uri` or the node id to keys |
| `csp_nonce()` in cached output | scripts blocked on later visits | script files; nonce only outside caches |
| `currentdate()` or `rand()` in cached output | date or random value frozen | compute it outside cached parts |

## Syntax

| Mistake | Symptom | Fix |
|---|---|---|
| quotes around `ezurl`: `href="{$x\|ezurl}"` | `href=""/path""` broken attribute | write `href={$x\|ezurl}`, or `ezurl( 'no' )` inside your quotes ([URL operators](url-operators.md)) |
| `$a == $b` | parse error | `eq( $a, $b )` |
| `{def}` of an existing variable | warning, includer's value overwritten | prefixes and `{undef}` ([Variables and def](variables-and-def.md)) |
| comparing strings with `gt` | unexpected results | `gt` compares lengths of non-numeric strings ([Logic and comparison operators](logic-and-comparison-operators.md)) |
| an operator from an inactive extension | "unknown operator" in the log, nothing printed | activate the extension or use another operator |

## Files and caches

| Mistake | Symptom | Fix |
|---|---|---|
| editing a template in `design/standard` or a shipped theme | change lost on upgrade | copy it into your design ([The design cascade](../theme-design/the-design-cascade.md)) |
| a new template or override not showing | old template still used, or empty content area | clear `template-override` ([Debugging a design](../theme-design/debugging-a-design.md)) |
| an override with a typo in `MatchFile` | rule ignored, error log entry | check the path under `override/templates/` |
| two overrides for the same source | the wrong one wins | `Priority` ([Override ordering](override-ordering.md)) |

## Related pages

- [Template style guide](template-style-guide.md)
- [Template debugging](template-debugging.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
