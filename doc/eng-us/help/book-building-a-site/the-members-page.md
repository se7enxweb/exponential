---
title: "The members page: a custom content class, a list and an info page"
audience: builder, admin
category: books
order: 140
summary: Creating the Member class, adding members, a list view for the Members folder and a full view for one member, plus the privacy question the 2003 tutorial did not ask.
keywords: custom class, member, content class, attributes, list, line view, full view, override, privacy, personal data, Building book
type: help
---
# The members page: a custom content class, a list and an info page

The club wants a member list, not a static page. The members are data, so they become content: a **Members** folder with one
**Member** object each, shown as a list and, on a click, as one member's page. None of the built-in classes fits, which is the
usual situation, and the reason the class editor exists ([creating a content class](../book-learning/creating-a-content-class.md)).

## Decide what to store, and what not

The book's class holds a real name, a nickname, date of birth, gender, phone number, e-mail address and a face photo. Before
you copy that, think about the data. A public page with every member's date of birth and phone number is, today, a
privacy risk and in many countries a legal one: store only what the member agreed to publish, and keep contact
details out of the public view (use a contact form, or keep them in an attribute only a role may read). A class for the
club's public list needs less:

| Attribute | Datatype | Required |
|---|---|---|
| Real name (`name`) | Text line | yes |
| Nickname (`nickname`) | Text line | no |
| Rating (`rating`) | Integer | no |
| Member since (`since`) | Date | no |
| Photo (`photo`) | Image | no |
| About (`about`) | Rich text | no |

Create it in **Setup > Classes**, group **Content**: **New class**, name `Member`, identifier `member`, object name pattern
`<name>`, then add the attributes, setting **Required** for the name. Steps and what each box means are in
[creating a content class](../book-learning/creating-a-content-class.md) and
[Content classes for builders](../extension-development/part-2-builder/content-classes-for-builders.md); the datatypes are described
in [the datatypes tour](../book-learning/the-datatypes-tour.md).

## Add members

Create a folder **Members** at the root, then create **Member** objects in it (**Create here** in the folder). The role that lets
the club's editors do this is a `content/create` policy limited to the class `member` and the Members folder
([roles and policies](../book-learning/roles-and-policies-step-by-step.md)).

## The list

The Members folder shows a list of its children, each a link with the member's name (the **line** view of the class):

```ini
[full_members_folder]
Source=node/view/full.tpl
MatchFile=full/members_folder.tpl
Subdir=templates
Match[node]=47
```

```
{* full/members_folder.tpl *}
<h1>{$node.name|wash}</h1>
<ul class="member-list">
{foreach fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                         'class_filter_type', 'include',
                                         'class_filter_array', array( 'member' ),
                                         'sort_by', array( 'name', true() ) ) ) as $member}
    <li><a href={$member.url_alias|ezurl}>{$member.name|wash}</a>
        {if $member.data_map.nickname.has_content}({$member.data_map.nickname.content|wash}){/if}</li>
{/foreach}
</ul>
```

(47 stands for the node ID of your Members folder.) For a long list add `limit` and the navigator of [the news pages](the-news-pages.md).

## The member page

A full view for the class, `Match[class_identifier]=member`, source `node/view/full.tpl`, file `full/member.tpl`:

```
<h1>{$node.name|wash}</h1>
{if $node.data_map.photo.has_content}
    {attribute_view_gui attribute=$node.data_map.photo image_class='medium'}
{/if}
<dl>
    {if $node.data_map.rating.has_content}<dt>Rating</dt><dd>{$node.data_map.rating.content}</dd>{/if}
    {if $node.data_map.since.has_content}<dt>Member since</dt><dd>{$node.data_map.since.content.timestamp|l10n( 'shortdate' )}</dd>{/if}
</dl>
{attribute_view_gui attribute=$node.data_map.about}
```

Test by clicking from the list. `has_content` is the test for "was something entered"; look at the [attribute reference](../template-language/attribute-access.md)
for what each datatype's `.content` holds.

Next: [the guestbook](the-guestbook.md).

## Source and license

Adapted from the members page chapter of of the tutorial *Building an eZ publish site* (written for eZ publish 3.2, 2003), originally by
ez.no and released again under the GNU Free Documentation License by 7x in 2024,
[Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x and Graham Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later
version, with no Invariant Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rewritten for Exponential 6, with a data-protection note and a smaller member class.
