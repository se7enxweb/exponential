---
title: "Lesson 3.2: Text blocks: Title, Text, Rich text, Markdown and HTML"
audience: trainer, editor, website-creator
category: training
order: 32
summary: Learners choose between the text blocks, fill a Title, a Rich text and a Markdown block, and explain why the HTML snippet block is for trusted authors only. 45 minutes.
keywords: exponential layouts, title block, text block, rich text block, markdown block, html snippet, quote, heading level, content security policy, trusted authors
type: help
---
# Lesson 3.2: Text blocks: Title, Text, Rich text, Markdown and HTML

Most pages need words that are not a content item of their own: a section heading, an introduction, a short notice.
The text blocks hold such words inside the layout. This lesson compares them and fills the most common ones.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 3: Blocks](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Editors and site builders |
| Prerequisites | [Lesson 3.1](adding-moving-and-removing-blocks.md) |

## Learning objectives

After this lesson, learners can:

- choose between **Title**, **Text**, **Rich text**, **Markdown**, **HTML snippet** and **Quote** for a need;
- fill a **Title** block and set its heading level;
- format text in a **Rich text** block with its toolbar;
- write a short **Markdown** block;
- explain the risk of the **HTML snippet** block and who should use it.

## Trainer's notes

### How to present it

Sources: [Layout types, zones and blocks](../../../creators/website-creators/layout-types-zones-and-blocks.md#blocks-by-purpose)
and [Exponential Layouts: Block catalogue](../../../../bc/6.0/LAYOUTS.md#block-catalogue).

1. **The family** (4 minutes):

   | Block | Use it for |
   |---|---|
   | **Title** | A heading, with its level |
   | **Text** | Plain text |
   | **Rich text** | Formatted text: paragraphs, headings, lists, links, tables |
   | **Markdown** | Text written in Markdown, rendered on the page |
   | **HTML snippet** | Raw markup, for trusted authors only |
   | **Quote** | A pull quote |

2. **Title** (2 minutes). The title text and a heading level; keep headings in order on the page (Editors 201,
   lesson 8.2).
3. **Rich text** (3 minutes). Its toolbar offers **Paragraph format** (**Paragraph**, **Heading 1** to **Heading 3**,
   **Preformatted**), **Bold**, **Italic**, **Underline**, **Strikethrough**, **Remove formatting**, **Link**,
   **Bulleted list**, **Numbered list**, indents, **Block quote**, alignment, **Horizontal line**, **Insert table**,
   **Undo** and **Redo**. A link address that cannot be used is refused: *This address cannot be used as a link.*
4. **Markdown** (2 minutes). `#` for headings, `**` around bold words, `-` for list items, and links written as the text in square brackets followed by the address in round brackets.
5. **HTML snippet** (3 minutes). Prints raw markup. Give it only to trusted authors; a site's Content Security Policy
   blocks inline scripts in it. It is the block most likely to break a page.

### Common questions

**Rich text block or an article?** Words that belong to this page's arrangement (a section intro) go in a block; words
that are content in their own right (news, a page text) belong in a content item, shown with a listing or Full view
block.

**Why does my heading look different on the site?** The site's design styles headings; the block sets the level, not
the look.

**Can I paste from a word processor into Rich text?** Yes; check the result and use **Remove formatting** if needed.

### Pitfalls

- **Words lost in blocks.** Text in blocks is not found by the site's content search the way articles are. Keep
  important content in content items.
- **Translations.** Block texts belong to the layout; on multilingual sites check how your installation handles
  translated layouts before promising them.
- **HTML for styling.** Use the **Design** tab's CSS class (lesson 3.3) instead of HTML.

## Hands-on exercise

Use the training installation and your course layout.

1. Open your course layout with **Open**. (If the editor asks what to do with an existing draft, choose to continue
   it.)
2. Select the **Title** block in *POST_HEADER*. In **Content**, type `Welcome to <your first name>'s page` and set the
   heading level to 1 if the block offers it.
3. Replace the **Text** block in *MAIN* with a **Rich text** block: add a **Rich text** block, then delete the
   **Text** block.
4. In the **Rich text** block, write two paragraphs, make one word **Bold**, add a **Bulleted list** with three
   points, and a **Link** to an `https://` address the trainer gives.
5. Add a **Markdown** block below it with:

   ```text
   ## Opening hours

   - Monday to Friday: 9 to 17
   - [Contact us](https://example.invalid/contact)
   ```

6. Look at both blocks in the middle of the editor.
7. Add an **HTML snippet** block, read its field, and delete it again without filling it.
8. Choose **Save draft and close**.

## Expected result

- The draft shows a heading block, a formatted rich text with a list and a link, and a Markdown block with a heading
  and a list.
- No HTML snippet block remains.
- The trainer checks by opening one learner's draft on the projector.

![The template editor, listing the block view templates](../../../images/trainers/ly-templates.webp)

## Quiz

1. Which block do you use for formatted text with a table?
   A. **Title** B. **Text** C. **Rich text** D. **Quote**
2. Why should the **HTML snippet** block be used by trusted authors only?
3. Write the Markdown for a link with the text "Contact us".
4. Where should a news text live: in a block or in a content item? Why?
5. What does the message *This address cannot be used as a link.* mean?

<details>
<summary>Answers</summary>

1. **C.**
2. It prints raw markup that can break the page; a Content Security Policy blocks inline scripts in it
   ([Layout types, zones and blocks](../../../creators/website-creators/layout-types-zones-and-blocks.md#blocks-by-purpose)).
3. `[Contact us](https://example.invalid/contact)` (with the real address).
4. In a content item: it is content in its own right, searchable and reusable; the layout lists or shows it.
5. The link address was refused by the rich text editor; use a valid address.

</details>

## Further reading

- [Layout types, zones and blocks](../../../creators/website-creators/layout-types-zones-and-blocks.md): every block by
  purpose.
- [Exponential Layouts: Block catalogue](../../../../bc/6.0/LAYOUTS.md#block-catalogue)

## Related pages

- [Module 3: Blocks](README.md)
- [Exponential Layouts: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
