---
title: Objects, nodes and locations
audience: cms-developer
category: cms-development
order: 9
summary: For CMS developers, the difference between a content object and its nodes (locations), the main node, the content tree, sort order, hiding and removing. It also covers which ID to use in templates, PHP and imports.
keywords: content object, node, location, main node, content tree, parent node, path string, depth, priority, sort order, hidden, invisible, trash, multiple locations, node id, object id, remote id
type: help
---
# Objects, nodes and locations

This page is for developers who come from systems where a page *is* its place in the menu. In Exponential the
content and its place are two different things: the **object** holds the content, its **nodes** (locations) put it
into the tree. Knowing which one you hold in a template or in PHP avoids most beginner mistakes.

## Contents

1. [Object and node side by side](#object-and-node-side-by-side)
2. [The main node and extra locations](#the-main-node-and-extra-locations)
3. [The tree: paths, depth and the fixed tops](#the-tree-paths-depth-and-the-fixed-tops)
4. [Sort order of children](#sort-order-of-children)
5. [Hidden and invisible](#hidden-and-invisible)
6. [Moving, copying and removing](#moving-copying-and-removing)
7. [Which ID to use where](#which-id-to-use-where)
8. [In templates and in PHP](#in-templates-and-in-php)

## Object and node side by side

| | Content object | Node (location) |
|---|---|---|
| Holds | versions, translations, attribute values, owner, section, states | a place in the tree: parent, path, depth, priority, visibility |
| Table | `ezcontentobject` (plus versions and attributes) | `ezcontentobject_tree` |
| PHP class | `eZContentObject` | `eZContentObjectTreeNode` |
| How many | one | one or more per published object |
| Has a URL | through its nodes | yes: each node has a URL alias |

A page view on the site is always the view of a **node**: `/content/view/full/<node id>`, or the node's URL alias.
The node gives you its object (`$node.object`), and the object gives you its nodes (`assigned_nodes`, `main_node`).

## The main node and extra locations

An object can be placed in several parts of the tree: a product under two categories, a person under the team page
and the contact page. Each place is a node; one of them is the **main node** (`main_node_id`). Editors add
locations on the object's **Locations** tab in the administration.

- All nodes show the same object, so an edit appears everywhere at once.
- The main node gives the canonical address. Link and set canonical URLs with the main node:
  `$node.object.main_node.url_alias` in a template
  ([Nodes, objects and attributes](../designers/templates/nodes-objects-attributes.md#several-locations)).
- Fetches return nodes; a list that covers two locations of the same object returns it twice unless you pass
  `main_node_only` ([Fetching content in templates](fetching-content-in-templates.md)).

Use extra locations where the content truly belongs in two places. For "related reading" boxes, use object relations
instead ([Relations and embeds](relations-and-embeds.md)).

## The tree: paths, depth and the fixed tops

Every node stores its `path_string` (the node IDs from the top, for example `/1/2/61/74/`) and its `depth`. These
make subtree fetches and subtree permissions fast. The shipped tree has fixed tops, set in
`settings/content.ini [NodeSettings]`:

| Setting | Node | Holds |
|---|---|---|
| `RootNode` | 2 | the site's content |
| `UserRootNode` | 5 | users and user groups |
| `MediaRootNode` | 43 | the media library |
| `SetupRootNode` | 48 | setup objects |
| `DesignRootNode` | 58 | design objects |

A siteaccess can start its site lower in the tree, which is how several sites share one tree
([Siteaccesses and multisite](siteaccesses-and-multisite.md)).

## Sort order of children

Each node stores how its children are sorted (`sort_field`, `sort_order`); new nodes take the default of their class.
The sort fields are constants of `eZContentObjectTreeNode` (`SORT_FIELD_PATH`, `PUBLISHED`, `MODIFIED`, `SECTION`,
`DEPTH`, `CLASS_IDENTIFIER`, `CLASS_NAME`, `PRIORITY`, `NAME`, `MODIFIED_SUBNODE`, `NODE_ID`, `CONTENTOBJECT_ID`,
`VISIBILITY`). With priority sorting, editors set the order by hand in the sub-items list. In a template,
`$node.sort_array` gives you the node's own order to pass to a fetch, so your menu follows what editors chose.

## Hidden and invisible

| Flag | Meaning |
|---|---|
| `is_hidden` | an editor hid this node |
| `is_invisible` | this node is hidden or below a hidden node |

Visitors do not see invisible nodes while `site.ini [SiteAccessSettings] ShowHiddenNodes=false`, the shipped value;
the administration siteaccess usually shows them. Hiding is not a permission: a hidden node is still readable by
anyone with `content/read` on a siteaccess that shows hidden nodes. For real access control use sections and roles
([Sections, roles and policies](sections-roles-and-policies.md)).

## Moving, copying and removing

- **Move** changes the parent of one node; the URL aliases of the subtree follow and the old addresses keep
  redirecting ([URL aliases and routing](url-aliases-and-routing.md)).
- **Copy** creates a new object (with a new object ID) and a node for it; a subtree can be copied with
  `bin/php/ezsubtreecopy.php`.
- **Remove** a node: if the object has other locations, only this location goes. If it was the last one, the object
  goes to the **trash** by default and can be restored from there
  ([Publishing, versions and the trash](../publishing-and-versions.md#remove-and-restore)).

From PHP, use the kernel operations so caches, search and URL aliases follow: see
[Content from PHP](content-from-php.md).

## Which ID to use where

| You need | Use | Why |
|---|---|---|
| A link in a template | `$node.url_alias` (main node for canonical links) | addresses follow moves and renames |
| A fixed starting point in settings | a node ID on one installation, or a setting of your own read with `ezini` | IDs differ between installations |
| A reference from code or an import | the object's **remote ID** | stable, and the same across exports and re-imports |
| A relation between objects | object IDs (the datatype stores them) | relations point to objects, not places |

[Find a node ID](../build-two-ways/find-a-node-id.md) shows where to read IDs in the administration.

## In templates and in PHP

In a node view template the current node is `$node`:

```smarty
<h1>{$node.name|wash()}</h1>
<p>Object {$node.contentobject_id}, node {$node.node_id}, depth {$node.depth}</p>
{if $node.object.assigned_nodes|count()|gt( 1 )}
    <link rel="canonical" href={$node.object.main_node.url_alias|ezurl( , 'full' )} />
{/if}
```

In PHP:

```php
<?php
$node = eZContentObjectTreeNode::fetch( 2 );        // a node by node ID
$object = $node->object();                          // its object
$main = $object->mainNode();                        // the main node
$all = $object->assignedNodes();                    // every location
$byRemote = eZContentObject::fetchByRemoteID( 'expsite-about-page' );
```

[eZContentObjectTreeNode](../extension-development/part-8-reference/ezcontentobjecttreenode.md) and
[eZContentObject](../extension-development/part-8-reference/ezcontentobject.md) in the book list the methods.

## Related pages

- [The content model](the-content-model.md)
- [Node attributes reference](../template-language/node-attributes-reference.md)
- [Mapping content: locations](../../install/migration/content-model-mapping.md#4-locations-the-content-tree)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
