---
title: "Lesson 4.1: Rich text basics"
audience: trainer, editor
category: training
order: 41
summary: Learners format text in the online editor (TinyMCE 8), with paragraph styles, headings, bold and italic, lists, undo and redo, special characters and full screen, and paste text cleanly. 45 minutes.
keywords: editors 101, online editor, rich text, TinyMCE 8, ezoe, toolbar, paragraph, heading, bold, italic, list, undo, paste, fullscreen, keyboard shortcuts
type: help
---
# Lesson 4.1: Rich text basics

Fields such as an article's **Intro** and **Body** are rich text: they hold formatted text, links, tables and
embedded images. They are edited with the **online editor**, which on current installations is TinyMCE 8. This
lesson covers everyday formatting; links come in lesson 4.2, embedding and tables in lesson 4.3.

## At a glance

| | |
|---|---|
| Course | [Editors 101](../README.md) |
| Module | [Module 4: The Online Editor](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | New editors and authors |
| Prerequisites | [Module 3](../module-03-creating-and-editing/README.md): each learner has an article in a course folder |

## Learning objectives

After this lesson, learners can:

- name the groups of the toolbar and find a button by its tooltip;
- structure a text with paragraphs, headings and lists instead of empty lines and bold text;
- use bold, italic, undo and redo with the mouse and the keyboard;
- insert a special character and work in full screen;
- paste text from another program and check the result.

## Trainer's notes

### Which editor learners see

Exponential's online editor (the extension **ezoe**) has two engines: TinyMCE 3 and TinyMCE 8. Both write the same
stored text, so editors can switch at any time. Which one is used is decided per site, per siteaccess or per user;
an installation may also offer further editors (for example **Tiptap**). Check on your training installation which
one is the default before the class, and show TinyMCE 8 if you can. Source:
[The online editor on TinyMCE 8](../../../../features/6.0/online-editor-tinymce8.md).

### How to present it

1. **The toolbar** (5 minutes). Open an article with **Edit** and go to **Body**. Walk along the toolbar: the
   paragraph style list, bold, italic and underline, subscript and superscript, alignment, lists and indents, undo
   and redo, links and anchors, embedded objects, custom tags, special characters, tables, **Fullscreen**, **Help**
   and the source code button. Every button explains itself when you point at it. Source:
   [Rich text](../../../admin-tour.md#3-rich-text).
2. **Structure, not looks** (4 minutes). Use the paragraph style list for headings, never a bold line in a larger
   size. Headings and lists are what screen readers, search engines and the site's design use. Show a heading and a
   bulleted list.
3. **Keyboard** (2 minutes). `Ctrl+B` bold, `Ctrl+I` italic, `Ctrl+Z` undo (`Cmd` on a Mac). The **Help** button of
   TinyMCE lists every shortcut.
4. **Pasting** (2 minutes). Paste a paragraph from a word processor and look at the result. The stored text keeps
   only what the editor allows; check the headings and lists after pasting.
5. **Status bar** (2 minutes). In TinyMCE 8 the status bar shows the path of the element the cursor is in, for
   example *Path: paragraph » embed*; clicking an element there opens its dialog.

### Common questions

**Can I change the font or the color?** No, and that is on purpose: the site's design decides how text looks. You
decide what the text is (a heading, a list, an emphasis).

**What is the source code button for?** It shows the text as the editor's markup. Editors rarely need it; changes
there can be lost if the markup is not allowed.

**Why does my colleague's editor look different?** Another engine (TinyMCE 3, TinyMCE 8 or Tiptap), chosen by the
site or by the user. Lesson 4.3 shows where to choose.

**Do I need to save the field?** No. The field is part of the draft; **Store draft and exit** or **Send for
publishing** keeps it.

### Pitfalls

- **Empty paragraphs for spacing.** Learners press Enter twice for space. The site's design sets the spacing;
  remove empty paragraphs.
- **Bold instead of headings.** Show the difference in the status bar path: *heading* versus *paragraph » strong*.
- **Pasting formatting from other programs.** If a pasted text looks wrong, undo it and paste again as plain text
  with your browser's paste-as-plain-text command, then format it in the editor.

## Hands-on exercise

Use the training installation and your article *My first article* (lesson 3.2).

1. Open your article and press **Edit**. Go to the **Body** field.
2. Point at each toolbar button and read its tooltip. Write down three buttons you did not expect.
3. Put the cursor at the start of the body and type `What I want to learn`. With the cursor in that line, choose a
   heading in the paragraph style list.
4. Below the heading, type three things you want to learn, each on its own line. Select the three lines and press
   the bulleted list button.
5. Make one word **bold** with `Ctrl+B` and one word *italic* with `Ctrl+I`.
6. Press `Ctrl+Z` twice and then the redo button twice. Watch what changes.
7. Insert a special character (for example `©` or `€`) with the special characters button.
8. Press **Fullscreen**, write one more sentence, and leave full screen with the same button.
9. Copy a short paragraph from any document or web page and paste it at the end. Check its formatting and remove
   anything that does not fit.
10. Click into the heading and read the status bar path at the bottom of the editor.
11. Press **Send for publishing** and look at the article with **View on site**.

## Expected result

- The published article shows a heading, a bulleted list with three entries, one bold and one italic word, a special
  character and the pasted paragraph.
- On the site, the heading and the list use the site's design.
- The learner read a path such as *Path: heading* in the status bar (TinyMCE 8).
- The trainer checks by opening one learner's article on the site on the projector.

## Quiz

1. Why use the paragraph style list for headings instead of making a line bold?
2. Which shortcut makes text italic?
   A. `Ctrl+B` B. `Ctrl+I` C. `Ctrl+U` D. `Ctrl+H`
3. Where do you find the full list of the editor's keyboard shortcuts?
4. True or false: TinyMCE 3 and TinyMCE 8 store the text differently, so you cannot switch.
5. A pasted paragraph brings odd formatting with it. What do you do?

<details>
<summary>Answers</summary>

1. Headings give the text a structure that screen readers, search engines and the site's design use.
2. **B.** `Ctrl+I` (`Cmd+I` on a Mac) ([Keyboard shortcuts](../../../keyboard-shortcuts.md#the-edit-form)).
3. Under the **Help** button of TinyMCE.
4. **False.** Both write the same stored XML, so editors can switch back and forth
   ([The online editor on TinyMCE 8](../../../../features/6.0/online-editor-tinymce8.md)).
5. Undo, paste again as plain text, and format it with the toolbar.

</details>

## Further reading

- [The online editor on TinyMCE 8](../../../../features/6.0/online-editor-tinymce8.md): the engines, the switch and
  every dialog.
- [Creating and editing content](../../../editing-content.md#rich-text): rich text in the edit form.
- [Keyboard shortcuts](../../../keyboard-shortcuts.md): the keys of the edit form.

## Related pages

- [Module 4: The Online Editor](README.md)
- [Editors 101: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
