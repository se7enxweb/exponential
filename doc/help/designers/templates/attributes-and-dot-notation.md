---
title: Attributes and dot notation
audience: designer, developer
category: templates
order: 40
summary: Reading data in templates with the dot: attributes of objects ($node.name), keys of hashes, items of arrays, chained lookups ($node.data_map.image.content), dynamic keys, and how to see what attributes an object has with attribute and dump.
keywords: dot notation, attributes, data_map, hash keys, array index, chained lookup, dynamic key, attribute operator, dump, show attributes, debug variable
type: help
---
# Attributes and dot notation

This page is for designers who read values from nodes, objects and hashes. The dot is the template language's one way
to look inside a value.

## Contents

1. [Objects, hashes and arrays](#objects-hashes-and-arrays)
2. [Chains](#chains)
3. [Dynamic keys](#dynamic-keys)
4. [What does this object have?](#what-does-this-object-have)
5. [Missing attributes](#missing-attributes)

## Objects, hashes and arrays

```tpl
{$node.name}                   {* attribute of an object *}
{$settings.color}              {* key of a hash *}
{$items.0.name}                {* first item of an array, then its attribute *}
```

Kernel objects (nodes, objects, attributes, users) expose a fixed list of attributes; a node, for example, has `name`,
`url_alias`, `data_map`, `children`, `parent`, `path`, `class_identifier`, `can_edit` and more
([Nodes, objects and attributes](nodes-objects-attributes.md)).

## Chains

```tpl
{$node.data_map.image.content['large'].url}
{$node.object.owner.name}
{$node.parent.name}
```

Each step is a lookup; some (like `children` or `parent`) run a database query. Store a value you use several times
in a variable.

## Dynamic keys

When the key is in a variable, use brackets:

```tpl
{def $field = 'intro'}
{$node.data_map[$field].has_content}
{$image_content[$image_class].url}
```

## What does this object have?

The `attribute` and `dump` operators print the attributes of a value (`lib/eztemplate/classes/eztemplateattributeoperator.php`):

```tpl
{$node|attribute( show, 1 )}
{$node.data_map|dump( show, 2 )}
```

`show` prints values too; the number is the depth. Use them only on a test siteaccess and remove them before
committing: they print internal data.

## Missing attributes

Reading an attribute that does not exist prints nothing and writes a warning to the debug output. Test optional
values first:

```tpl
{if is_set( $node.data_map.subtitle )}...{/if}
{if $node.data_map.image.has_content}...{/if}
```

## Related pages

- [TPL syntax basics](tpl-syntax-basics.md)
- [Nodes, objects and attributes](nodes-objects-attributes.md)
- [Template debugging](template-debugging.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
