---
title: Module result and content info
audience: designer, developer
category: templates
order: 660
summary: What $module_result holds after a view has run (content, path, title_path, uri, content_info, view_parameters, errorType and errorNumber) and how the pagelayout uses content_info to know the class, node, section, view mode and language of the page without fetching.
keywords: module_result, content_info, path, title_path, view_parameters, node_id, class_identifier, viewmode, errorType, errorNumber, pagelayout data, page facts
type: help
---
# Module result and content info

This page is for designers who write pagelayouts and their parts. `$module_result` is everything the module view
reports about the page it produced. The full list of keys is on [Pagelayout variables](../theme-design/pagelayout-variables.md);
this page shows how to use them.

## Contents

1. [Content pages and other pages](#content-pages-and-other-pages)
2. [Facts without fetching](#facts-without-fetching)
3. [The path](#the-path)
4. [Error pages](#error-pages)

## Content pages and other pages

`content_info` exists only when the view is a content view. On search, user, error and other module pages it is not an
array. Always guard:

```tpl
{def $ci = cond( is_array( $module_result.content_info ), $module_result.content_info, false() )}
```

## Facts without fetching

| Need | Read |
|---|---|
| the class of the page | `$ci.class_identifier` |
| the node, its parent, its depth | `$ci.node_id`, `$ci.parent_node_id`, `$ci.node_depth` |
| the view mode | `$ci.viewmode` (the media design checks for `payload` and `layout_preview`) |
| the section | `$ci.section_id`, `$ci.section_identifier` |
| the URL alias and the canonical one | `$ci.url_alias`, `$ci.main_node_url_alias` |
| the language shown | `$ci.current_language` |
| the page offset | `$ci.offset` |
| what the view handed over | `$ci.persistent_variable` ([The persistent variable](persistent-variable.md)) |

Each is free: no database query.

## The path

`$module_result.path` is a list of hashes (`text`, `url`, `url_alias`, `node_id`), from the top of the tree to the
current page, whose last entry has `url` false. Content views fill it from the node's path; other modules set their own
(for example "Search"). `title_path` is the same for the `<title>`. Breadcrumbs and titles are built from it
([Breadcrumbs](breadcrumbs.md)).

## Error pages

When a view fails (no access, not found), the kernel renders an error template and `$module_result` carries
`errorType` (for example `kernel`) and `errorNumber`. The media design's `pagelayout/head/title.tpl` uses them to give
error pages a readable title ([Error templates](error-templates.md)).

## Related pages

- [Pagelayout variables](../theme-design/pagelayout-variables.md)
- [The pagelayout](../theme-design/the-pagelayout.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
