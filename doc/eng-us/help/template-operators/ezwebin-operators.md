---
title: ezwebin operators
audience: developer, designer, app-creator
category: development
order: 410
summary: The six operators of the ezwebin extension - ezpagedata, ezpagedata_set, ezpagedata_append, ezarchive, ezkeywordlist and eztagcloud - with their parameters, what ezpagedata computes for a pagelayout, the database engines ezarchive supports, and examples rendered on alpha.
keywords: ezwebin, ezpagedata, ezpagedata_set, ezpagedata_append, ezarchive, ezkeywordlist, eztagcloud, page data, persistent_variable, tag cloud, keywords, archive
type: help
---
# ezwebin operators

The extension `ezwebin` (the website interface design) registers six operators in
`extension/ezwebin/autoloads/eztemplateautoload.php`, one class per file in `extension/ezwebin/autoloads/`. They exist
only while ezwebin is an active extension. `ezpagedata_set` and `ezpagedata_append` store their values through
ezjscore (`ezjscPackerTemplateFunctions::setPersistentArray()`), so ezwebin needs ezjscore.

| Operator | Class |
|---|---|
| `ezpagedata`, `ezpagedata_set`, `ezpagedata_append` | `eZPageData` (`ezpagedata.php`) |
| `ezarchive` | `eZArchive` (`ezarchive.php`) |
| `ezkeywordlist` | `eZKeywordList` (`ezkeywordlist.php`) |
| `eztagcloud` | `eZTagCloud` (`eztagcloud.php`) |

## Contents

1. [The operators](#the-operators): [ezpagedata_set](#ezpagedata_set), [ezpagedata_append](#ezpagedata_append),
   [ezpagedata](#ezpagedata), [ezarchive](#ezarchive), [ezkeywordlist](#ezkeywordlist), [eztagcloud](#eztagcloud)
2. [Security notes](#security-notes)

## The operators

### ezpagedata_set

| Parameter | Type | Required | Default |
|---|---|---|---|
| `key` | string | yes | `false` |
| `value` | mixed | yes | `false` |

- **Input:** none. **Returns:** nothing (no output).

Stores `value` under `key` in the persistent variable, replacing what was there. In a view template that is
`$persistent_variable`; in the pagelayout `$module_result.content_info.persistent_variable`. Because the persistent
variable is saved with the view cache, a value set in a cached view template still reaches the pagelayout on a cached
page.

### ezpagedata_append

The same parameters. Appends `value` to the list under `key` (a single value becomes a list).

### ezpagedata

| Parameter | Type | Required | Default |
|---|---|---|---|
| `params` | array (hash) | no | empty |

- **Input:** none.
- **Returns:** a hash of page settings for the pagelayout: `persistent_variable`, `main_node_id`, `show_path`,
  `website_toolbar`, `node_id`, `is_edit`, `page_root_depth`, `page_depth`, `root_node`, `canonical_url`,
  `canonical_language_url`, `template_look`, `class_identifier`, `top_menu`, `left_menu`, `current_menu`,
  `extra_menu`, `extra_menu_node_id`, `extra_menu_subitems`, `extra_menu_class_list`, `path_array`,
  `path_id_array`, `path_normalized` and `css_classes`.

It reads `$module_result` (path, content_info, node_id), `$ui_context`, `$uri_string` and `$current_user` from the
template, `menu.ini` (`[SelectedMenu]`, `[MenuSettings] HideLeftMenuClasses[]` and `AlwaysAvailable`,
`[MenuContentSettings] ExtraIdentifierList[]` and `ExtraMenuSubitemsCheck`), `content.ini` `[NodeSettings] RootNode`
and `site.ini` `[SiteSettings] RootNodeDepth`. The values of `params` **and** of the persistent variable override the
computed ones (`left_menu`, `extra_menu`, `top_menu`, `show_path`, `website_toolbar`, `current_node_id` with
`set_current_node_path`, `canonical_url`, `template_look`, `extra_menu_node_id`, `extra_menu_class_list`,
`extra_menu_subitems`). That is how a view template hides the left menu of its page: `ezpagedata_set( 'left_menu',
false() )`. Unless `template_look` is given, it fetches the first object of the class `template_look`, which is one
query per call.

```tpl
{def $persistent_variable = hash()}{ezpagedata_set( 'left_menu', false() )}{ezpagedata_append( 'body_classes', 'recipes' )}{ezpagedata_append( 'body_classes', 'dark' )}
set: {$persistent_variable.left_menu|choose( 'false', 'true' )}, appended: {$persistent_variable.body_classes|implode( ' ' )}

{def $page = ezpagedata()}
keys: {foreach $page as $k => $v}{$k}{delimiter} {/delimiter}{/foreach}

left_menu={$page.left_menu|choose( 'false', 'true' )} css_classes={$page.css_classes} top_menu={$page.top_menu} root_node={$page.root_node}
```

Output on alpha:

```text

set: false, appended: recipes dark
keys: persistent_variable main_node_id show_path website_toolbar node_id is_edit page_root_depth page_depth root_node canonical_url canonical_language_url template_look class_identifier top_menu left_menu current_menu extra_menu extra_menu_node_id extra_menu_subitems extra_menu_class_list path_array path_id_array path_normalized css_classes
left_menu=false css_classes=nosidemenu noextrainfo top_menu=flat_top root_node=89
```

### ezarchive

| Parameter | Type | Required | Default |
|---|---|---|---|
| `class_identifier` | string | yes | empty |
| `parent_node_id` | integer | yes | 0 |

- **Input:** none.
- **Returns:** a list of rows with `month`, `year` and `timestamp` (the first of the month), newest first: the months
  in which visible children of `parent_node_id` of that class have a `publication_date` attribute value.

The attribute identifier `publication_date` is fixed in the SQL. The operator has SQL for MySQL, PostgreSQL and
Oracle only; on any other engine the query is empty and the result is an empty list. Alpha runs SQLite, so:

```tpl
database: {ezini( 'DatabaseSettings', 'DatabaseImplementation' )}

{def $months = ezarchive( 'blog_post', 2 )}
ezarchive rows: {$months|count}
```

Output on alpha:

```text
database: sqlite3
ezarchive rows: 0
```

### ezkeywordlist

| Parameter | Type | Required | Default |
|---|---|---|---|
| `class_identifier` | mixed (identifier or list) | yes | empty |
| `parent_node_id` | integer | yes | 0 |
| `depth` | integer | no | 0 (any depth) |

- **Input:** none.
- **Returns:** a list of rows with `keyword`, the distinct keywords of `ezkeyword` attributes below the parent node
  (the parent itself excluded), sorted, filtered by class (an empty identifier means all classes), by depth when
  given, by the current language, by visibility and by the user's read permissions.

### eztagcloud

| Parameter | Type | Required | Default |
|---|---|---|---|
| `params` | array (hash) | no | empty |

- **Input:** none.
- **params:** `class_identifier`, `parent_node_id`, `limit`, `offset`, `sort_by` (`array( 'keyword' or 'count',
  ascending )` or a list of them; default keyword ascending), `post_sort_by` (`keyword`, `keyword_reverse`, `count`,
  `count_reverse`: sorts after the limit).
- **Returns:** HTML: the template `design:tagcloud/tagcloud.tpl` rendered with `$tag_cloud`, a list of `tag`,
  `count` and `font_size` (100 to 200, spread over the counts).

Alpha tags its content with eztags, not with `ezkeyword` attributes, so both keyword operators find nothing here:

```tpl
{def $words = ezkeywordlist( '', 2 )}
ezkeywordlist: {$words|count} keywords, first: {foreach $words as $w max 5}{$w.keyword|wash}{delimiter}, {/delimiter}{/foreach}


eztagcloud:
{eztagcloud( hash( 'parent_node_id', 2, 'sort_by', array( 'count', false() ), 'limit', 4 ) )}
```

Output on alpha:

```text
ezkeywordlist: 0 keywords, first: 

eztagcloud:
```

For the eztags equivalent see `eztagscloud` in [eztags and star rating operators](eztags-and-starrating-operators.md).

## Security notes

- `ezarchive` puts `class_identifier` into the MySQL and PostgreSQL queries without escaping (only the Oracle query
  escapes it). Pass a constant identifier, never request input.
- `ezarchive` applies no permission check: it can list months of content the user may not read.
- `eztagcloud` puts the class id and the parent's path string into its SQL; both come from the database, not from the
  caller.

## Related pages

- [The persistent variable](../designers/templates/persistent-variable.md)
- [ezjscore operators in depth](ezjscore-operators.md)
- [Module result and content info](../designers/templates/module-result-and-content-info.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
