---
title: Override match keys
audience: designer, developer
category: templates
order: 580
summary: Every Match key a content view override can use (class_identifier, class, node, object, parent_node, section, section_identifier, depth, url_alias, viewmode, remote ids, parent class, class group, state), what each compares, which survive moving to another installation, and the url_alias prefix match.
keywords: override match keys, Match, class_identifier, node, parent_node, section, depth, url_alias, viewmode, remote_id, node_remote_id, parent_class_identifier, class_group, state, match conditions
type: help
---
# Override match keys

This page is a reference for the conditions of override rules (`Match[<key>]=<value>`). The keys a full view sets are
built in `kernel/classes/eznodeviewfunctions.php`; `node_view_gui` and `content_view_gui` set similar lists (see
[node_view_gui](node-view-gui.md), [content_view_gui](content-view-gui.md)).

## Contents

1. [The keys](#the-keys)
2. [Portable keys](#portable-keys)
3. [How values compare](#how-values-compare)
4. [Combining keys](#combining-keys)

## The keys

| Key | Compares | Example |
|---|---|---|
| `class_identifier` | the content class identifier | `Match[class_identifier]=article` |
| `class` | the class id | `Match[class]=16` |
| `node` | the node id | `Match[node]=2` |
| `object` | the object id | `Match[object]=57` |
| `parent_node` | the parent node id | `Match[parent_node]=61` |
| `section` | the section id | `Match[section]=6` |
| `section_identifier` | the section identifier | `Match[section_identifier]=news` |
| `depth` | the node's depth | `Match[depth]=2` |
| `url_alias` | the start of the URL alias (prefix) | `Match[url_alias]=news` |
| `viewmode` | the view mode (full views) | `Match[viewmode]=full` |
| `remote_id` | the object's remote id | `Match[remote_id]=cjw-starter-o-root` |
| `node_remote_id` | the node's remote id | |
| `parent_node_remote_id`, `parent_object_remote_id` | the parent's remote ids | |
| `parent_class`, `parent_class_identifier` | the parent's class | `Match[parent_class_identifier]=blog` |
| `class_group` | a class group id the class belongs to | |
| `state` | an object state id the object has | |
| `state_identifier` | an object state identifier (`<group>/<state>`) the object has | |
| `navigation_part_identifier` | the navigation part (mostly for the administration) | |
| `view_offset` | the paging offset of the view | `Match[view_offset]=0` (first page only) |

## Portable keys

Ids (`node`, `object`, `class`, `section`, `parent_node`) differ between installations: a design that uses them works only
on the site it was made for. Prefer identifiers and remote ids: `class_identifier`, `section_identifier`,
`remote_id`, `node_remote_id`, `parent_class_identifier`. The starter theme matches its root page by
`Match[remote_id]=cjw-starter-o-root` for exactly this reason.

## How values compare

- A plain value must be equal.
- When the page's value is a list (`class_group`, `state`), the rule matches if the list contains the value.
- `url_alias` matches when the page's alias **starts with** the value, so `Match[url_alias]=news` covers the whole
  News subtree (`eZTemplateDesignResource` treats it as a subtree match).

## Combining keys

All conditions of a rule must match:

```ini
[mytheme_full_article_in_blog]
Source=node/view/full.tpl
MatchFile=full/blog_article.tpl
Subdir=templates
Match[class_identifier]=article
Match[parent_class_identifier]=blog
Priority=5
```

There is no "or" inside a rule: write two rules with the same `MatchFile` instead.

## Related pages

- [Override basics](override-ini-basics.md)
- [Override a single node](override-a-single-node.md)
- [Override by section](override-by-section.md)
- [Override by parent](override-by-parent.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
