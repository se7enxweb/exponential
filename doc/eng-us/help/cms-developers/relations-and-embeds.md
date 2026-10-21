---
title: Relations and embeds
audience: cms-developer
category: cms-development
order: 14
summary: For CMS developers, how objects refer to each other. It covers relation attributes, embeds and links in rich text, the four relation types, reading relations and reverse relations in templates and PHP, and what happens when a related object is removed.
keywords: relations, object relations, ezobjectrelationlist, ezobjectrelation, related objects, reverse relations, embed, embed-inline, link, relation types, class constraint, default placement, reverse relation check, related_objects fetch
type: help
---
# Relations and embeds

This page is for developers who model content that refers to other content: an event and its speakers, an article
and its images, a product and its downloads. Exponential records every such reference as a **relation** between
objects, so you can follow it in both directions and know what breaks before something is removed.

## Contents

1. [Three ways to refer to content](#three-ways-to-refer-to-content)
2. [Relation attributes](#relation-attributes)
3. [Embeds and links in rich text](#embeds-and-links-in-rich-text)
4. [The relation types](#the-relation-types)
5. [Relations in templates](#relations-in-templates)
6. [Reverse relations](#reverse-relations)
7. [Relations in PHP](#relations-in-php)
8. [Removing related content](#removing-related-content)

## Three ways to refer to content

| Way | Use it when | Example |
|---|---|---|
| A relation attribute (`ezobjectrelationlist`) | the reference is structured data the template places | an event's `speakers`, an article's `related_articles` |
| An embed in rich text | an editor places an object at a point in the text | an image or a video in the middle of an article |
| A link in rich text | text points to another page | "read our [privacy notice]" |

A fourth option, a second **location**, puts the same object in two places of the tree. It is not a relation: use
it only when the object truly belongs in both places ([Objects, nodes and locations](objects-nodes-and-locations.md)).

## Relation attributes

`ezobjectrelationlist` (Object relations) holds an ordered list of objects. In the class editor you set, per
attribute (`kernel/classes/datatypes/ezobjectrelationlist/ezobjectrelationlisttype.php`):

| Setting | Effect |
|---|---|
| Selection method | *browse* (pick in the tree), or a list of candidates in the edit form |
| Allowed classes (`class_constraint_list`) | only objects of these classes can be chosen, for example `exp_speaker` |
| Default location (`default_placement`) | where browsing starts, and where objects created from the edit form go |

One edit form or browse takes at most 1000 related objects for one attribute (`MAX_RELATIONS`). `ezobjectrelation` holds a single object
with fewer options; prefer the list datatype even for one target
([Choosing datatypes](choosing-datatypes.md#decisions-that-are-hard-to-undo)).

## Embeds and links in rich text

In `ezxmltext`, an embed is `<embed object_id="..." view="embed"/>` (or `node_id`), and an inline embed is
`<embed-inline>`. Links to content use `<link object_id="...">` or `node_id`, and external links point to an entry of
the link list. Each embed is drawn with the **embed** view mode of the embedded object's class, so the article
template never needs to know what was embedded. Which view modes and classes editors can choose for embeds is set in
`content.ini [embed]` and `[embed-inline]` (`AvailableViewModes[]`, `AvailableClasses[]`). Templates:
[Embed view](../designers/templates/embed-view.md) and [Embed-inline view](../designers/templates/embed-inline-view.md).

## The relation types

Every relation is a row in `ezcontentobject_link` with a type, constants of `eZContentObject`:

| Constant | Value | Made by |
|---|---|---|
| `RELATION_COMMON` | 1 | the **Relations** of an object, added by editors on its related objects list |
| `RELATION_EMBED` | 2 | an embed in rich text |
| `RELATION_LINK` | 4 | a link to content in rich text |
| `RELATION_ATTRIBUTE` | 8 | a relation attribute (`ezobjectrelation`, `ezobjectrelationlist`) |

The relations are stored per version, so an old version keeps the relations it had.

## Relations in templates

The relation attribute's content is an array with the key `relation_list`; each item has `contentobject_id` and
`node_id`. The simplest output is `attribute_view_gui`, which draws each target with its embed view. For your own
markup:

```smarty
{def $speakers = $node.data_map.speakers}
{if $speakers.has_content}
<ul class="exp-speakers">
    {foreach $speakers.content.relation_list as $item}
        {def $speaker = fetch( 'content', 'object', hash( 'object_id', $item.contentobject_id ) )}
        {if and( $speaker, $speaker.can_read )}
            <li>{content_view_gui view='line' content_object=$speaker}</li>
        {/if}
        {undef $speaker}
    {/foreach}
</ul>
{/if}
```

Check `can_read`: a relation does not grant access to its target. The shipped view template of the datatype
(`design/standard/templates/content/datatype/view/ezobjectrelationlist.tpl`) also skips targets in the trash and
hidden targets; the datatype is documented in
[ezobjectrelationlist](../../examples/datatypes/ezobjectrelationlist.md).

The fetch `related_objects` returns the targets of an object directly, filtered by attribute if you like:

```smarty
{def $related = fetch( 'content', 'related_objects', hash(
        'object_id', $node.contentobject_id,
        'attribute_identifier', 'exp_event/speakers' ) )}
```

## Reverse relations

`reverse_related_objects` answers the opposite question: "which objects point at this one?" On a speaker's page it
lists the speaker's events without a field on the speaker:

```smarty
{def $events = fetch( 'content', 'reverse_related_objects', hash(
        'object_id', $node.contentobject_id,
        'all_relations', true() ) )}
```

This is why you store a reference on one side only: the other side is a fetch away. Counting variants exist for
both (`related_objects_count`, `reverse_related_objects_count`).

## Relations in PHP

```php
<?php
$object = eZContentObject::fetchByRemoteID( 'exp-event-2026-opening' );
$speakers = $object->relatedObjects( false, false, 0, false,
    array( 'AllRelations' => eZContentObject::RELATION_ATTRIBUTE ) );
$pointingHere = $object->reverseRelatedObjectList( false, 0, false,
    array( 'AllRelations' => true ) );
```

Read the signatures in `kernel/classes/ezcontentobject.php` before you rely on a parameter; the book's reference has
the common calls ([eZContentObject](../extension-development/part-8-reference/ezcontentobject.md)). To set a relation
attribute from an import, pass the target object IDs in the attribute's `fromString()` format
([Content from PHP](content-from-php.md)).

## Removing related content

Removing or hiding an object that others point at leaves gaps. With `content.ini [RemoveSettings]
ReverseRelationCheck=warn` (and the same key in `[HideSettings]`), the shipped default, the administration lists the
objects that refer to it, including Layouts blocks, before it removes or hides anything
([Reverse relation check](../../features/6.0/reverse-relation-check.md)). Templates should still expect a target to
be missing, hidden or unreadable, as the example above does.

## Related pages

- [Rich text](rich-text.md)
- [Designing a content model, step 4](designing-a-content-model.md#step-4-decide-what-is-a-relation)
- [Fetching content in templates](fetching-content-in-templates.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
