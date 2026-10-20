---
title: Debugging templates
audience: developer, designer, app-creator
category: extension-development
order: 150
summary: Find why a template, an override or a variable does not do what you expect - used templates, path comments, template debug, the attribute operator, debug-log and timing points, and the error messages of the engine.
keywords: debug templates, ShowUsedTemplates, ShowTemplatePathComments, template debug, attribute show, debug-log, debug-timing-point, debug-accumulator, unknown template variable, operator not registered
type: help
---
# Debugging templates

Part 2, chapter 15. Template problems fall into three groups: the wrong template is used, a variable is not what you
think, or the template has an error. This chapter gives the tool for each. Turn on debug output first
([Debug output and logs for developers](../part-1-from-user-to-developer/debug-output-and-logs-for-developers.md)).

## Which template is used?

| Setting (`site.ini [TemplateSettings]`) | Effect |
|---|---|
| `ShowUsedTemplates=enabled` | the debug output lists every template of the page: requested, used, and the override rule that chose it |
| `ShowTemplatePathComments=enabled` | every rendered template is wrapped in comments like `<!-- START design/media/templates/pagelayout/footer.tpl -->` and `<!-- STOP ... -->` in the page source |
| `Debug=enabled` (with `ShowXHTMLCode=enabled`) | the older inline template debug: comments with the template names in the HTML |

Development only: the comments go into every output, including mails, JSON, XML and RSS. Never leave them on in
production.

When an override does not apply, the used-templates table tells you what the kernel requested (`Source`) and what it
chose; compare that with your `override.ini` block ([Template overrides: the basics](template-overrides-basics.md)).

## What is in this variable?

```text
{$node|attribute( 'show', 1 )}
```

prints a table of the variable's attributes, one level deep (`2` for two levels). On a node it lists `name`,
`node_id`, `object`, `data_map`, `url_alias` and the rest, which is how you find the name of the attribute you need.
Remove it before you commit: it is for your eyes only. Note that it reads every attribute, including expensive ones.

For values you do not want in the page, write them to the debug output:

```text
{debug-log var=$node.name msg='the node name'}
{debug-log msg='reached the teaser branch'}
```

The engine prints `Debug: (the node name)` followed by the value in the debug report.

## How long does it take?

```text
{debug-timing-point id='menu'}
    ... the expensive part ...
{/debug-timing-point}
```

adds a timing point around a part of the template; `debug-accumulator` sums repeated blocks. The timings appear in the
debug output's timing table. Combine with `[DatabaseSettings] SQLOutput=enabled` to see which fetch is slow.

## The engine's messages

| Message in the debug output | Meaning | Fix |
|---|---|---|
| `Unknown template variable 'x' in namespace ''` | the variable was never defined here | `{def}` it, pass it to the include, or use `first_set( $x, default )` |
| `Operator 'x' is not registered` | no operator of that name (typo, or its extension is not active) | check the name in [Built-in template operators](../part-8-reference/template-operators-reference.md); activate the extension |
| `Object problem with operator 'x'` | the operator's class could not be set up | a PHP problem in the extension; see [Old-style constructors and static calls](../part-4-upgrading/old-constructors-and-static-calls.md) |
| `No template could be loaded for "x.tpl" using resource "design"` | the path is not in any design base | check the path and the [design chain](the-design-chain.md) |
| `parser error` with a line number | a syntax error, for example `max=3` instead of `max 3` in `foreach` | read the line; compare with [Templates for builders](templates-for-builders.md) |

Each message names the template file and the line and column, for example `full.tpl:12[5]`.

## Stale output

If your change does not show up:

1. Is the template compiled from an old version? Clear the template cache: `php bin/php/ezcache.php --clear-tag=template`.
2. Is the page in the view cache? Clear it: `php bin/php/ezcache.php --clear-tag=content`.
3. Is it a **new** file? Clear the template override cache: `--clear-id=template-override`.
4. Is the part inside a `{cache-block}`? Clear `--clear-id=template-block`.
5. Velocity keeps a response cache: `./console exp:velocity cache clear`.

While you work, `TemplateCompile=disabled` avoids step 1 entirely.

## A routine for a broken page

1. Read the errors in the debug output; the first one is usually the cause.
2. Find the template with path comments.
3. Dump the variables you depend on with `attribute( 'show' )`.
4. Cut the template down until the error disappears; the last thing you removed is the problem.
5. Undo the debug lines, clear the caches, check again.

eZpedia's ["Learning: Template Debugging"](https://ezpedia.exponential.earth/en/learning/learning_template_debugging)
describes the same tools from the earlier years.

## Related pages

- Previous: [The template operators builders use most](template-operators-builders-use-most.md)
- Next: [Caching and templates](caching-and-templates.md)
- [Template path comments](../../../features/6.0/template-path-comments.md)
- [The Exp Debug bar](../../../bc/6.0/debug-bar.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
