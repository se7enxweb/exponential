---
title: "Template overrides: the basics"
audience: developer, designer, app-creator
category: extension-development
order: 100
summary: An override replaces one template for some content only - articles in full view, the front page, one section. The override.ini block, where the file goes, how to check it applies, and the mistakes that stop it.
keywords: override, override.ini, Source, MatchFile, Subdir, Match, class_identifier, full view, line view, template override, design extension
type: help
---
# Template overrides: the basics

Part 2, chapter 10. The design chain replaces a template for **all** content. An **override** replaces it for **some**
content: articles in full view, the front page, everything in one section. Overrides are how real sites give each kind
of content its own look, and they are the builder's most important tool.

## An override in four lines

In `override.ini.append.php` of your siteaccess or your site extension:

```ini
[full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
```

| Line | Meaning |
|---|---|
| `[full_article]` | A unique name for the rule. Anything readable; `<viewmode>_<class>` is the convention. |
| `Source` | The template being replaced, as the kernel requests it (`node/view/full.tpl` for the full view of a node). |
| `MatchFile` | Your template, relative to `<design>/override/templates/`. |
| `Subdir` | Always `templates`. |
| `Match[<key>]` | The condition. Several `Match` lines must all be true. |

This rule means: "when the full view of a node is drawn and the node is an `article`, use `full/article.tpl`
instead". It is a real rule; the media theme ships exactly this block in its siteaccess settings.

## Where the template file goes

`MatchFile` is looked up in the `override/templates/` folder of each design base, through the design chain:

```text
extension/mysite_design/design/mysite/override/templates/full/article.tpl
```

The override setting can live anywhere that is read for the siteaccess; the template file must be in a design of that
siteaccess's chain. Most sites keep both in one design extension.

## A first override, step by step

1. Find the template to replace: with `ShowUsedTemplates=enabled`, open an article and read the template table in
   the debug output. The **requested** template is your `Source`.
2. Copy the standard template as a starting point:

   ```bash
   mkdir -p extension/mysite_design/design/mysite/override/templates/full
   cp design/standard/templates/node/view/full.tpl extension/mysite_design/design/mysite/override/templates/full/article.tpl
   ```

3. Add the rule to `extension/mysite_design/settings/siteaccess/mysite/override.ini.append.php` (or to your
   siteaccess's `override.ini.append.php`).
4. Clear the caches that hold the rules and the compiled templates:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-id=template-override,template
   ```

5. Open an article. The template table now shows `full/article.tpl` as the **used** template for
   `node/view/full.tpl`, and names the rule `full_article`.
6. Change the copy. Start small: wrap the title in a class of your design.

## What a full view template receives

In `node/view/full.tpl` and its overrides, the variable `$node` is the node being shown. The usual start:

```text
<article class="article-full">
    <h1>{$node.name|wash}</h1>
    {attribute_view_gui attribute=$node.data_map.intro}
    {attribute_view_gui attribute=$node.data_map.body}
</article>
```

`$node.data_map` holds the fields (attributes) by identifier; `attribute_view_gui` draws one field with its datatype's
own view template, so rich text, images and dates render correctly. Look up your class's attribute identifiers in
**Setup > Classes**.

## Order of rules

When several rules match, the one tried **first** wins. A rule can carry a `Priority=<number>` line: the template
engine tries rules from the lowest `Priority` value up, and rules without a `Priority` come last
(`overrideArray()` in `kernel/common/eztemplatedesignresource.php`). Give specific rules (one node) a lower number than
general ones (a class):

```ini
[full_object_1234]
Source=node/view/full.tpl
MatchFile=full/object_1234.tpl
Subdir=templates
Priority=0
Match[object]=1234
```

**Design > Template editor** (`/visual/templatelist`) shows the rules in the order they are tried and lets you reorder
them by drag and drop; see [Template editor: create, order and edit overrides](../../../features/6.0/template-editor-overrides.md).

## Common mistakes

| Symptom | Cause |
|---|---|
| The override is never used | `Source` does not match the requested template exactly (check the debug table); the file is not in `override/templates/` of a design in the chain; the INI cache was not cleared |
| A blank content area | a new template file and an old template override cache: clear `template-override` |
| It applies to the wrong content | the `Match` key or value: `class_identifier` takes the identifier (`article`), `class` the numeric ID |
| Works on one siteaccess, not another | the rule is in one siteaccess's settings only, or the other siteaccess uses another design |
| Two rules fight | ordering: give the specific rule a lower `Priority` |

## Related pages

- Previous: [The design chain: where a template is found](the-design-chain.md)
- Next: [Override conditions](override-conditions.md)
- [Template editor: create, order and edit overrides](../../../features/6.0/template-editor-overrides.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
