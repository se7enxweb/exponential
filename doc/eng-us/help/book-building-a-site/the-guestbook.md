---
title: "The guestbook: visitors write content"
audience: builder, admin
category: books
order: 150
summary: A guestbook as a class and a folder: the entry class, the display template, the button and form that let visitors add an entry, the policy that allows it, and the spam question to settle first.
keywords: guestbook, content class, anonymous, content/action, NewButton, ClassID, NodeID, policy, role, edit template, form token, spam, Building book
type: help
---
# The guestbook: visitors write content

A guestbook lets **visitors** add content, which no other part of the club site does. In the system, that is nothing special: an
entry is an object of a class, created in a folder by someone whose role allows it, here the anonymous visitor.

## The class and the folder

Create a class **Guestbook entry** (identifier `guestbook_entry`): a **Name** (text line, required), a **Message** (plain text, required),
and optionally an e-mail address (kept out of the public view). Name pattern `<name>`. Create a folder **Guestbook**, in
the Guestbook section made earlier ([sections](sections-and-the-welcome-page.md)), and one or two entries as an administrator
to have something to show. The book shows the **content** of the page as a table of name, date and message.

## Showing the entries

A full view for the guestbook folder (override on its node) lists the children newest first:

```
<h1>{$node.name|wash}</h1>
{foreach fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                         'class_filter_type', 'include',
                                         'class_filter_array', array( 'guestbook_entry' ),
                                         'sort_by', array( 'published', false() ),
                                         'limit', 20 ) ) as $entry}
    <article class="entry">
        <h2>{$entry.data_map.name.content|wash}</h2>
        <p class="date">{$entry.object.published|l10n( 'shortdate' )}</p>
        {$entry.data_map.message.content|wash|break}
    </article>
{/foreach}
```

(`wash` escapes what visitors wrote, always; `break` keeps the line breaks.) The book also shows a message in the template
saying how many entries there are, with `list_count`.

## The button that starts a new entry

A page can start the creation of a new object with a form that posts to `content/action`. The form needs the button name
`NewButton`, the class (`ClassID`) and the parent node (`NodeID`). A shipped template has the exact markup (take it from the
**Create here** button of a standard folder view, such as `design/standard/templates/node/view/full.tpl`). In outline:

```
<form method="post" action={'content/action'|ezurl}>
    <input type="hidden" name="ClassID" value="{$class_id}" />
    <input type="hidden" name="NodeID" value="{$node.node_id}" />
    <input type="hidden" name="ContentLanguageCode" value="{ezini( 'RegionalSettings', 'ContentObjectLocale', 'site.ini' )}" />
    <input class="button" type="submit" name="NewButton" value="Add an entry" />
</form>
```

Forms in Exponential carry a form token (the `ezformtoken` extension adds it to every form automatically); that is
part of what the old tutorial did not have ([Security in extensions](../extension-development/part-3-extension-developer/security-in-extensions.md)).
The class ID is `$class_id`, fetched with `fetch( 'content', 'class', hash( 'class_id', 'guestbook_entry' ) )`.

## The input form

The page the visitor lands on is the standard **edit** template, with all the editing fields. For a guestbook it should show only the
name and the message. Override the edit template for the class (`edit/guestbook_entry.tpl`, source `content/edit.tpl`, `Match[class_identifier]=guestbook_entry`;
start from the standard `content/edit.tpl`) and keep the three buttons (`PublishButton`, `DiscardButton`, `VersionEditButton`
as there). Read [view modes and edit templates](../extension-development/part-2-builder/view-modes-for-builders.md) before you do; a broken edit template
loses entries.

## The policy

The anonymous role must be allowed, for this class and this folder only: `content/create` limited to the class
`guestbook_entry` and the Guestbook **section** (or the node), `content/edit` and `content/publish` for the same, so that the visitor can finish the entry
the system made for them. Everything else stays as it was. See [roles and policies](../book-learning/roles-and-policies-step-by-step.md).
Test it as the anonymous visitor (a private window).

## Before you open it to everyone

An open form on the public internet will be found by spam robots within days. The book moves on to an **approval workflow**
for exactly this reason: [approving guestbook entries](approving-guestbook-entries.md). Do not open the policy until the
workflow is in place. Also consider a captcha extension or a rate limit at the web server.

Next: [approving guestbook entries](approving-guestbook-entries.md).

## Source and license

Adapted from the guestbook and approval chapters of of the tutorial *Building an eZ publish site* (written for eZ publish 3.2, 2003), originally by
ez.no and released again under the GNU Free Documentation License by 7x in 2024,
[Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x and Graham Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later
version, with no Invariant Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rewritten for Exponential 6: current workflow screens, the cronjob command, form tokens, and a spam warning.
