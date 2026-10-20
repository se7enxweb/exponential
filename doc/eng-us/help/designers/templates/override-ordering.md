---
title: Override ordering
audience: designer, developer
category: templates
order: 590
summary: Which override wins when several rules match: rules are tried by Priority (lowest first; rules without Priority last), the first match wins, specific rules before general ones, the template editor's drag and drop ordering, and how to debug a wrong winner.
keywords: override order, Priority, first match wins, override precedence, specific before general, template editor reorder, wrong override, override conflict
type: help
---
# Override ordering

This page is for designers whose override does not win. Several rules can match the same page; only the first in order
is used.

## Contents

1. [The order](#the-order)
2. [Specific before general](#specific-before-general)
3. [Rules from several files](#rules-from-several-files)
4. [Reordering in the administration](#reordering-in-the-administration)
5. [Debugging](#debugging)

## The order

`eZTemplateDesignResource::overrideArray()` (`kernel/common/eztemplatedesignresource.php`) sorts the rules by their
`Priority`: lower values are evaluated first, and a rule without `Priority` sorts last. For each template, the first rule
whose conditions all match wins; the rest are not looked at.

## Specific before general

```ini
# one node
[mytheme_full_frontpage]
Source=node/view/full.tpl
MatchFile=full/frontpage.tpl
Subdir=templates
Match[node_remote_id]=mytheme-front
Priority=0

# a class in a place
[mytheme_full_article_in_blog]
Source=node/view/full.tpl
MatchFile=full/blog_article.tpl
Subdir=templates
Match[class_identifier]=article
Match[parent_class_identifier]=blog
Priority=10

# a class
[mytheme_full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
Priority=20
```

Leave gaps (0, 10, 20) so a rule can be inserted later without renumbering.

## Rules from several files

Rules from the siteaccess, from extensions and from extension siteaccess files are merged into one list before
sorting. Without `Priority`, their relative order depends on the order in which the files are read, which is hard to
predict: give every rule of a theme a `Priority`.

## Reordering in the administration

**Design > Template Editor** shows the overrides of a template in their evaluation order and lets you reorder them by
drag and drop; it writes `Priority` values (10, 20, 30 ...) for that template's rules, and new rules get `Priority=0`
([Template editor overrides](../../../features/6.0/template-editor-overrides.md)).

## Debugging

- Turn on template path comments: the page source names the template that won ([Template path comments](template-path-comments.md)).
- List all rules for the `Source` in the Template Editor, in order, and read their conditions top to bottom.
- Remember the INI and template override caches after every change.

## Related pages

- [Override basics](override-ini-basics.md)
- [Override match keys](override-match-keys.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
