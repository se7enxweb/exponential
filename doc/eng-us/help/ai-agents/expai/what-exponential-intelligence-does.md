---
title: What Exponential Intelligence does
audience: ai-agents, editor, admin
category: ai-agents
order: 21
summary: The features of Exponential Intelligence for editors - the Intelligence tab, the edit form actions and presets, alt text, Vector, chains and translation sync - and exactly what each one sends to the model and what it never sends.
keywords: Exponential Intelligence, expai, Intelligence tab, Fill the fields, field actions, Write, Rewrite, Shorten, Translate, Suggest, Improve menu, presets, what is sent, panel, conversation
type: help
---
# What Exponential Intelligence does

This page is for editors who want to know what the writing assistant does before they use it, and for administrators
who must explain it. It walks through each place the assistant appears and says what is sent to the model in each
case.

## Contents

1. [The Intelligence tab](#the-intelligence-tab)
2. [The edit form](#the-edit-form)
3. [Bulk and background work](#bulk-and-background-work)
4. [What is sent to the model](#what-is-sent-to-the-model)
5. [What it cannot do](#what-it-cannot-do)
6. [Example requests](#example-requests)

## The Intelligence tab

Open a node in the administration and choose the **Intelligence** tab. You get a conversation for you and that node.

- You write a request in plain language, for example "write three short articles about our spring events under this
  folder".
- The assistant looks up what it needs: the node, its children, the tree around it, the definition of the content
  class it should create. Each lookup is shown as a compact step.
- It asks back when a request is unclear.
- It writes **drafts** and lists them with links to edit, view and discard each one. **Save as drafts** keeps them;
  **Send for approval** runs the normal publish operation as you ([Drafts and human review](drafts-and-human-review.md)).
- The history is one collapsible row per exchange. "New conversation" starts over.

The tab is shown only to users with the policy `expai/write`, and only while the assistant is switched on
([Permissions for AI features](permissions-for-ai-features.md)).

## The edit form

| Control | What it does |
|---|---|
| **Fill the fields** | Fills the fields of the draft from a short brief |
| Field actions **Write**, **Rewrite**, **Shorten**, **Translate**, **Suggest** | Work on one field; Translate translates into another language of the site |
| **Presets** | One click for a title, a summary, a meta description or keywords |
| **Improve** menu | On every text field |
| "Defaults in use" line | Names the model, the region and the estimated cost of a click |
| **Write alt text** on image fields | Suggests the alternative text of an image ([Alt text for images](alt-text-for-images.md)) |

Values go **into the form** for you to review. Nothing is stored until you store or publish the draft yourself;
autosave, the Online Editor and Tiptap keep working as with typed text. Field actions work only on a draft you own and
may edit.

## Bulk and background work

- **Vector** is a workspace of threads for larger jobs: a scope, a plan with a cost estimate, and staged review of
  every change per field before anything is written ([Vector and staged review](vector-and-staged-review.md)).
- **AI chains** write in steps through the job queue: write, review, revise, prepare SEO fields, stop at a human
  checkpoint, then translate ([Chains and queued work](chains-and-queued-work.md)).
- **Translation sync** keeps translations in step after the source language changed
  ([Translation help](translation-help.md)).
- **The "Missing alt text" report** queues alt text suggestions for a subtree as drafts.

Every version written by the assistant carries the label **Exponential Intelligence**, in My drafts, the drafts page,
version views, edit forms and approval items.

## What is sent to the model

From the extension's [security page](https://github.com/se7enxweb/expai/blob/main/doc/security.md):

| Place | Sent |
|---|---|
| Intelligence tab | The instructions (with the open node's name, class, path and the site's default language), the tool definitions, your messages, and the results of the tools the model calls: names and field values of content you may read, class definitions. Images are not sent here, only their alt texts; files and user account fields never |
| Edit form | The object's name and class, the field, its current value and the other fields' current values in the form (about 30 KB at most), your instruction |
| Alt text | The stored image of the field (scaled down if large), the page title, the text of the other fields and the language, to a vision model whose provider the data policy allows |
| Never | Your name, login or e-mail, passwords, session data, the content of other users' drafts |

What reaches the model is further limited by the **data policy**, which can keep sections and classes away from a
provider ([Privacy and data leaving the site](../governance/privacy-and-data-leaving-the-site.md)).

## What it cannot do

- It cannot publish, delete, move, change permissions or send mail.
- It cannot upload images or files, or set relations; it says so, and you set them in the edit form.
- It cannot do anything you may not do: every lookup and every draft runs with your own content permissions.

More limits: [What AI in Exponential does not do](../what-ai-in-exponential-does-not-do.md).

## Example requests

From the extension's [usage guide](https://github.com/se7enxweb/expai/blob/main/USAGE.md):

| Ask | What to expect |
|---|---|
| "Write three short articles about our spring events under this folder, friendly tone, no images." | New drafts below the folder, each listed with its links |
| "Rewrite the intro of this article: shorter and clearer." | A new draft version of the article; the published version stays as it is |
| "Translate this article into German as a new draft." | A draft in German for you to review |
| "Which kinds of content may I create here?" | An answer in words, from the classes you may create at that place |

The field actions, as the usage guide describes them:

| Button | What it does | Asks for |
|---|---|---|
| Write | writes a new value from your instruction | what to write |
| Rewrite | clearer and better written, same meaning and length | optionally, how to change it |
| Shorten | about half as long, same key facts | nothing |
| Translate | into another language of the site | the language |
| Suggest | a value based on the other fields | nothing |

The new value goes into the field with a short note and an **Undo** link.

## Related pages

- [Exponential Intelligence for AI users](README.md)
- [Drafts and human review](drafts-and-human-review.md)
- [Reviewing AI drafts](../checklists/reviewing-ai-drafts.md)
- [expai: Exponential Intelligence](../../../features/6.0/extensions/expai.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
