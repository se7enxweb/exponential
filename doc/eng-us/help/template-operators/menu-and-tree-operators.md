---
title: Menu and tree operators
audience: developer, designer, app-creator
category: development
order: 330
summary: The kernel operators that build navigation - treemenu (a menu that unfolds along the current path), content_structure_tree (a nested tree of nodes) and topmenu (the admin tabs from menu.ini) - with every parameter, the shape of what they return, their costs, and examples rendered on alpha.
keywords: treemenu, content_structure_tree, topmenu, menu, navigation, tree, menu.ini, TopAdminMenu, eZTreeMenuOperator, eZContentStructureTreeOperator, eZTopMenuOperator
type: help
---
# Menu and tree operators

Three kernel classes registered in `kernel/common/eztemplateautoload.php` build navigation data. They return arrays;
the template turns them into markup.

| Operator | Class and file |
|---|---|
| `treemenu` | `eZTreeMenuOperator`, `kernel/common/eztreemenuoperator.php` |
| `content_structure_tree` | `eZContentStructureTreeOperator`, `kernel/common/ezcontentstructuretreeoperator.php` |
| `topmenu` | `eZTopMenuOperator`, `kernel/common/eztopmenuoperator.php` |

## Contents

1. [The operators](#the-operators): [treemenu](#treemenu), [content_structure_tree](#content_structure_tree), [topmenu](#topmenu)
2. [Costs](#costs)

## The operators

### treemenu

Generated page: [treemenu](../../examples/template-operators/treemenu.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `path` | array | yes | |
| `node_id` | int | no | `false` (deprecated: a value only writes the notice "Deprecated parameter") |
| `class_filter` | array | no | `false` (all classes); an empty array means class id 1 |
| `depth_skip` | int | no | `false` (0) |
| `max_level` | int | no | `false`, which becomes 2 |
| `is_selected_method` | string | no | `tree` (every node on the path can be marked); `node` marks only the last node of the path |
| `indentation_level` | int | no | 15 |
| `language` | string or array | no | `false` |
| `load_data_map` | boolean | no | `null`: load the data maps when a level has 15 children or fewer |

- **Input:** none (call it as a function).
- **Path:** a list of hashes with `url` (`/content/view/full/<node id>`) and `node_id`, the form of
  `$module_result.path` on content pages. The node id is read from the fifth part of `url`; when the last entry has
  no `url` but a `node_id`, the URL is built from it.
- **Returns:** a flat list of items. Each has `id`, `level`, `class_name`, `is_main_node`, `has_children`,
  `indent` (`(level - 1) * indentation_level`), `url_alias` (with a leading `/`), `url`, `text` (the name),
  `is_selected` and `node` (the node object). The children of each path node are inserted right after it, so the
  list is already in menu order.

Starting at `path[depth_skip]`, the operator fetches the children of each path node (sorted the way that node sorts
its children, filtered by class) for up to `max_level` levels. When the path is shorter than one level it lists the
children of node 2.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 144 ) )}
{def $path = $node.path|extract_left( 0 )}
{foreach $node.path as $p}{set $path = $path|append( hash( 'url', concat( '/content/view/full/', $p.node_id ), 'node_id', $p.node_id ) )}{/foreach}
{set $path = $path|append( hash( 'url', false(), 'node_id', 144 ) )}
{foreach treemenu( $path, 144, array( 'ng_frontpage', 'ng_category', 'ng_landing_page' ), 1, 3 ) as $item}
{$item.level} {$item.indent} {$item.id} {$item.text}{if $item.is_selected} (selected){/if} {$item.url_alias}

{/foreach}
path: {foreach $path as $p}{$p.url} {/foreach}
node_id is {$node.node_id|get_type}, the id taken from a URL is {'/content/view/full/144'|explode( '/' )|extract( 4, 1 )|implode( '' )|get_type}
```

Output on alpha:

```text
0 -15 92 Fitness /fitness
0 -15 130 Healthy eating /healthy-eating
0 -15 144 Recipes /recipes
0 -15 164 Video /video
0 -15 192 Showcase /showcase
0 -15 26210 Tour /exponential-live
path: /content/view/full/2 /content/view/full/89  node_id is integer, the id taken from a URL is string[3]
```

No item came out selected. `is_selected` is set when the node id taken from the **next** path entry's URL is
identical (`===`) to the child's `node_id`. The id taken from a URL is a string while the `node_id` attribute is an
integer, as the last line shows, so the strict comparison does not match here. Mark the current item in the template
instead, by comparing `$item.id` with the current node id.

### content_structure_tree

Generated page: [content_structure_tree](../../examples/template-operators/content_structure_tree.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `root_node_id` | int | yes | 0 |
| `class_filter` | array of class identifiers | no | `false` |
| `max_depth` | int | no | 0 (no limit) |
| `max_nodes` | int | no | 0 (no limit) |
| `sort_by` | array of `'<field>/ascending'` or `'<field>/descending'` | no | the sorting of each parent |
| `fetch_hidden` | bool | no | `false` |
| `unfold_node_id` | int | no | 0 (unfold all) |

- **Input:** none.
- **Returns:** `false` when the root cannot be read, is hidden (without `fetch_hidden`), or its class is not in
  `class_filter`; otherwise a nested hash: `parent_node` (with `node`: `node_id`, `path_identification_string`,
  `children_count`, `sort_array`, `path_string`, `depth`, `is_hidden`, `is_invisible`; `classes_js_array`; and
  `object`: `id`, `name`, `class_identifier`, `class_name`, `published`, `is_container`, `language_js_array`) and
  `children`, a list of the same structure.

The class filter applies to the root as well: leave the root's class out and you get `false`. `max_depth` counts the
root, so `2` means the root and its children. `unfold_node_id` expands only that child below the first level. The
children are read with SQL that applies the current user's read limitations, published and visible nodes only.

```tpl
{def $tree = content_structure_tree( 89, array( 'ng_frontpage', 'ng_category', 'ng_landing_page', 'ng_topic' ), 2, 8, array( 'name/ascending' ) )}
root: {$tree.parent_node.node.node_id} {$tree.parent_node.object.name} ({$tree.parent_node.object.class_identifier}), {$tree.parent_node.node.children_count} children

{foreach $tree.children as $child}
- {$child.parent_node.node.node_id} {$child.parent_node.object.name} [{$child.parent_node.object.class_name}] depth {$child.parent_node.node.depth}

{/foreach}
```

Output on alpha:

```text
root: 89 Fit & Healthy (ng_frontpage), 16 children
- 92 Fitness [Landing page] depth 3
- 191 HIIT [Topic] depth 3
- 190 Health [Topic] depth 3
- 130 Healthy eating [Landing page] depth 3
- 144 Recipes [Category] depth 3
- 163 Running [Topic] depth 3
- 192 Showcase [Landing page] depth 3
```

`max_nodes` 8 stopped the list after the root and seven children, although the root has 16.

### topmenu

Generated page: [topmenu](../../examples/template-operators/topmenu.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `context` | string | yes | `content` |
| `filter_on_access` | bool | no | `false` |

- **Input:** none.
- **Returns:** the admin tabs as a list of hashes with `access`, `url`, `enabled`, `name`, `tooltip`, `icon`,
  `navigationpart_identifier` and `position` (`first`, `middle`, `last`). An empty list (and an error in the log)
  when `menu.ini` has no `[TopAdminMenu] Tabs[]`.

For each id in `[TopAdminMenu] Tabs[]` (without `HiddenTabs[]`, and without `SetupTabs[]` when the user can open
`setup/menu`) it reads the group `[Topmenu_<id>]`: `Shown[<context>]=false` leaves the tab out, `URL[<context>]` or
`URL[default]` gives the address, `Enabled[...]` the enabled state, `Name` and `Tooltip` are translated in
`TranslationContext` (default `design/admin/pagelayout`), `Icon` and `NavigationPartIdentifier` are passed on.
`PolicyList[]` entries are either `module/function` (checked with `hasAccessTo()`) or a node id (the node must be
readable); with `[MenuAccessSettings] CheckViewAccess` not disabled, the view behind the URL must be open to the user
as well. With `filter_on_access` the tabs without access are dropped.

Rendered with the `admin` siteaccess. The command line runs as the anonymous user, so every tab has `access=no` and
the filtered list is empty:

```tpl
{foreach topmenu( 'content' ) as $tab}
{$tab.position}: {$tab.name} -> {$tab.url} enabled={$tab.enabled|choose( 'no', 'yes' )} access={$tab.access|choose( 'no', 'yes' )} part={$tab.navigationpart_identifier}

{/foreach}
shown to this user: {topmenu( 'content', true() )|count}
```

Output on alpha:

```text
first: Dashboard -> content/dashboard enabled=yes access=no part=ezmynavigationpart
middle: Content structure -> content/view/full/2 enabled=yes access=no part=ezcontentnavigationpart
middle: Media -> content/view/full/43 enabled=yes access=no part=ezmedianavigationpart
middle: Users -> content/view/full/5 enabled=yes access=no part=ezusernavigationpart
middle: Store -> shop/dashboard enabled=yes access=no part=ezshopnavigationpart
middle: Layouts -> explayouts_ui/dashboard enabled=yes access=no part=ezexplayoutsuinavigationpart
middle: Setup -> setup/menu enabled=yes access=no part=ezsetupnavigationpart
middle: Tags -> tags/dashboard enabled=yes access=no part=eztagsnavigationpart
middle: Design -> content/view/full/58 enabled=yes access=no part=ezvisualnavigationpart
middle: Audit -> audit/dashboard enabled=yes access=no part=expauditnavigationpart
middle: Git -> git_manager/dashboard enabled=yes access=no part=gitmanagernavigationpart
middle: Export -> xrowextract/csv enabled=yes access=no part=ezextractnavigationpart
middle: CIE -> bccie/overview enabled=yes access=no part=ezbccienavigationpart
middle: Newsletter -> newsletter/index enabled=yes access=no part=eznewsletternavigationpart
last: Syndication -> syndication/menu enabled=yes access=no part=ezsyndicationnavigationpart
shown to this user: 0
```

## Costs

- `treemenu` runs one children fetch per path level and, by default, loads the data maps of small levels. Wrap menus
  in a `cache-block` keyed on the path.
- `content_structure_tree` with `max_depth` and `max_nodes` 0 walks the whole subtree below the root with at least one query
  per node. Always give both limits on a public page.
- `topmenu` checks the policies of every tab on each call.

## Related pages

- [Menus and navigation](../designers/templates/menus-and-navigation.md)
- [Module result and content info](../designers/templates/module-result-and-content-info.md)
- [Cache blocks](../designers/templates/cache-block.md)
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
