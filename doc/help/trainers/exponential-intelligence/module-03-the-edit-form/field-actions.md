---
title: "Lesson 3.1: Field actions in the edit form"
audience: trainer, editor
category: training
order: 31
summary: Learners use the Exponential Intelligence buttons on a field, Write, Rewrite, Shorten, Translate and Suggest, read the defaults line, undo a suggestion, and handle the review that appears when structure would be lost. 45 minutes.
keywords: exponential intelligence, edit form, field actions, write, rewrite, shorten, translate, suggest, undo, defaults line, view record, apply anyway, keep the original
type: help
---
# Lesson 3.1: Field actions in the edit form

In the edit form, the assistant works one field at a time: each text field gets a small bar of buttons. A suggestion
goes into the field for the editor to read; nothing is stored until the editor stores or publishes the draft. This
lesson practices the field actions.

## At a glance

| | |
|---|---|
| Course | [Exponential Intelligence](../README.md) |
| Module | [Module 3: The edit form](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Editors |
| Prerequisites | [Module 2](../module-02-the-intelligence-tab/README.md); an article in the learner's course folder |

## Learning objectives

After this lesson, learners can:

- find the bar *Exponential Intelligence* with **Write**, **Rewrite**, **Shorten**, **Translate** and **Suggest** on a
  field;
- read the defaults line: model, region, lane and estimated cost per click;
- run an action with an instruction, read the suggestion and **Undo** it;
- handle the review that appears instead of writing (**Apply anyway**, **Keep the original**);
- open the request's record with **View record**.

## Trainer's notes

### How to present it

Sources: the edit form and presets pages under **EI: Help** (`extension/expai/doc/edit-view-integration.md`,
`extension/expai/doc/presets.md`).

1. **The bar** (3 minutes). Each writable text field shows, after its label, a bar such as *Exponential Intelligence
   [Write] [Rewrite] [Shorten] [Translate]*; some fields also have **Suggest**. A button that needs input opens a small
   field (an instruction, or a language) with Go and Cancel.
2. **The defaults line** (2 minutes). Above the fields, one line names what a click uses: the model, its region, the
   lane and the estimated cost of one click, and that nothing is stored until you store the draft.
3. **What is sent** (2 minutes). The item's name and class, the field, its current value, the other fields' current
   values (about 30 KB at most) and your instruction. Never your name, login or password.
4. **The answer** (3 minutes). The value is put into the field and highlighted, with a note, **Undo** and **View
   record**. Rich text keeps its structure; when an answer would lose structure (embedded objects, a table) or the
   field changed meanwhile, the bar shows a review instead: the reason, **Apply anyway**, **Keep the original** and,
   where possible, **Copy text**.
5. **Storing** (2 minutes). The draft is stored as usual; autosave may store it, as for typed text. Nothing is
   published.

### Common questions

**Can I use field actions on a published article?** On the draft you are editing; the published version stays as it
is until you publish.

**Why is a button disabled?** The datatype is not writable by the assistant (images, relations, files), or the
assistant is switched off.

**Does Translate translate the whole item?** It translates the field's value into the language you choose; translating
an item into another language is done with a translation (Editors 201) and can be helped by translation sync.

### Pitfalls

- **Accepting without reading.** Read every suggestion; **Undo** is there.
- **Apply anyway by habit.** It can drop embedded images or tables; read the reason first.
- **Cost per click.** Each click is a request; repeated clicking adds up.

## Hands-on exercise

Use the training installation and your published article from module 2.

1. Open the article with **Edit**. Find the defaults line above the fields and write down the model and the cost per
   click.
2. On **Intro** (or the first text field), press **Shorten**. Read the suggestion.
3. Press **Undo**. The original text is back.
4. On **Intro**, press **Rewrite**, type the instruction `Friendlier tone, for complete beginners`, and press Go.
5. Read the suggestion and the note; press **View record** (it opens in a new tab) and look at the record.
6. On **Title**, press **Suggest** (or **Write** with an instruction) and keep the result you like best.
7. On **Body**, press **Translate**, choose another language and look at the result; then **Undo**.
8. Press **Store draft and exit**, open the draft again and press **Send for publishing** (or discard it if you prefer
   the original).

## Expected result

- The learner used four actions, undid two, and stored or discarded the draft.
- The record showed the model, tokens and estimated cost of the request.
- The trainer checks by asking two learners what the defaults line said.

## Quiz

1. Where in the edit form is the assistant's bar of buttons?
2. What does the defaults line tell you?
3. True or false: a field action stores the draft at once.
4. When does a review with **Apply anyway** and **Keep the original** appear?
5. What is sent to the provider with a field action?
   A. Your password B. The item's name and class, the field and the other fields' values, and your instruction C. Your
   e-mail address D. Other users' drafts

<details>
<summary>Answers</summary>

1. After the label of each writable text field.
2. The model, its region, the lane and the estimated cost of one click.
3. **False.** The value goes into the form; you store or publish as usual.
4. When the answer would lose structure (embedded objects, tables) or the field changed while it was written.
5. **B.**

</details>

## Further reading

- [expai: For editors](../../../../features/6.0/extensions/expai.md#for-editors)
- The edit form, presets and security pages under **EI: Help**.

## Related pages

- [Module 3: The edit form](README.md)
- [Exponential Intelligence: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
