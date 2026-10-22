---
title: How do I write rich text without the Online Editor?
audience: editor
category: manual
order: 495
summary: Click Disable editor below a rich text field to edit its text as simple tags (paragraphs, headings, bold, links, embeds); Enable editor switches back; the setting lasts for your session, and the text is checked when you store or publish.
keywords: disable editor, enable editor, simplified XML, rich text tags, markup, header, strong, emphasize, link, embed, eznode, ezobject, validation, required data is either missing or is invalid
type: help
---
# How do I write rich text without the Online Editor?

The Online Editor is a convenience: what it stores is structured text made of tags. You can switch it off and type
those tags yourself, for example to fix markup the editor cannot reach, to paste prepared text, or when the editor does
not load in your browser.

## Who can do this

Editors who may edit the item. The **Disable editor** button is shown to users whose role has `ezoe/disable_editor`.

## Steps

1. Edit the item and click **Disable editor** below the rich text field.
   The field turns into a plain text box with the text as tags.
2. Edit the text. Common tags:

   | You want | Type |
   |---|---|
   | a new paragraph | press Enter twice (one Enter gives a new line in the same paragraph) |
   | a heading | `<header level="2">Title</header>` |
   | bold, italic | `<strong>...</strong>`, `<emphasize>...</emphasize>` (or `<b>`, `<i>`) |
   | a list | `<ul><li>...</li></ul>` or `<ol><li>...</li></ol>` |
   | a link to another site | `<link href="https://example.org">text</link>` |
   | a link to a page of this site | `<link href="eznode://123">text</link>`, with the node number |
   | an embedded image or item | `<embed href="ezobject://456" size="medium" />`, with the object number |
   | text shown exactly as typed | `<literal>...</literal>` |

3. Click **Store draft** to check the text.
   If a tag is wrong, the form shows a message at the field. Correct it and store again.
4. Click **Enable editor** to switch back, or **Send for publishing**.

The node number is shown as **Node** on the item's page; the object number is on its **Details** tab. Links made with
`eznode://` or `ezobject://` keep working when the item moves.

## What can go wrong

- **"Required data is either missing or is invalid".** A tag is not closed, is misspelled, or is placed where it is
  not allowed (for example a heading inside a list). The message at the field names the problem.
- **The editor stays off on other fields.** The choice is kept for your session. Click **Enable editor** once, or sign
  out and in again.
- **There is no Disable editor button.** Your role does not include it, or the site has removed it from the toolbar.

## Related pages

- [Rich text tags](../book-learning/rich-text-tags.md), the full list with every attribute
- [Creating and editing content](../editing-content.md#rich-text)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
