---
title: Using AI drafts responsibly
audience: customer, editor, project-manager
category: customer-success
order: 13
summary: House rules for a team that uses Exponential Intelligence - a person is responsible for every published word, what to check in an AI draft, what not to ask for, sensitive content, images and alternative texts, translations, disclosure and a monthly review - with a short policy template.
keywords: AI drafts, responsible use, editorial policy, fact checking, Exponential Intelligence, review, disclosure, sensitive content, translation, house rules, template
type: help
---
# Using AI drafts responsibly

This page is for the owner and editor-in-chief of a team that uses the writing assistant
([Exponential Intelligence](exponential-intelligence.md)). The software makes sure an AI draft cannot reach visitors
without a person: it writes drafts only, with the editor's own permissions, labeled "Exponential Intelligence"
(`README.md`). What it cannot do is check that a draft is true, fair and in your voice. That is the team's job, and
this page gives you rules to start from.

## Contents

1. [The principle](#the-principle)
2. [Checking a draft](#checking-a-draft)
3. [What not to ask for](#what-not-to-ask-for)
4. [Images and alternative texts](#images-and-alternative-texts)
5. [Translations](#translations)
6. [Telling readers](#telling-readers)
7. [A monthly review](#a-monthly-review)
8. [Policy template](#policy-template)

## The principle

**The person who publishes is responsible for every word, as if they had written it.** An AI draft is a starting
point, not a source. Keep your approval workflow where it was; AI drafts go through it like any other version
([Approvals and review](../day-to-day/approvals-and-review.md)).

## Checking a draft

- [ ] Every fact, name, date, number and quote checked against a source you trust.
- [ ] Nothing promised that your organization does not offer (prices, delivery times, opening hours, legal terms).
- [ ] Links lead where the text says.
- [ ] Tone and words match your house style; jargon removed.
- [ ] Headings, lists and link texts follow the [Accessibility checklist for editors](../day-to-day/accessibility-checklist-for-editors.md).
- [ ] For a change to an existing page: **compare** the draft with the published version on the versions page, so
      you see exactly what changed ([Versions of an object](../../../guides/content-history.md)).
- [ ] For bulk work in Vector: review each change per field before anything is written, and use **What changed**
      with revert afterwards ([expai](../../../features/6.0/extensions/expai.md)).

## What not to ask for

- Content about real, named private persons beyond what your organization may publish anyway.
- Medical, legal or financial advice published without review by a qualified person.
- Text on topics or sections that your rules exclude from outside providers. Your administrator can enforce this:
  rules per section and content class decide which content may go to which provider (`README.md`).
- Personal data from form submissions, orders or user accounts pasted into a request.

## Images and alternative texts

The assistant can suggest alternative texts for images from a vision model, and a "Missing alt text" report can queue
suggestions for a subtree ([expai](../../../features/6.0/extensions/expai.md)). Treat a suggestion as a draft: does
it say what matters in **this** context, and does it avoid guessing who a person in the photo is?

## Translations

The assistant can prepare translations as drafts and keep existing translations in step after the source changes,
patching only changed passages ([Assisted translation with Exponential Intelligence](../../businesses/multilingual/assisted-translation-with-exponential-intelligence.md)).
A language owner who reads the language reviews before publishing, through per-language approval where you have it
([Translation routine](../day-to-day/translation-routine.md)).

## Telling readers

Whether and how to tell readers that a text was prepared with AI help is your organization's decision, and in some
places a legal question; this page is not legal advice. Decide once, write it in the policy below, and apply it
consistently.

## A monthly review

| Look at | Why |
|---|---|
| The assistant's dashboard: cost, requests, drafts, limits | budget and unusual use |
| Drafts it wrote that were never published | wasted work or unclear briefs |
| Corrections after publishing of AI-assisted pages | where checking failed |
| The audit channel `ai`, with the administrator | who used it for what ([The audit trail](../../../features/6.0/audit-trail.md)) |

## Policy template

**Template.**

```text
AI DRAFTS POLICY for ______________________   Agreed on: __________   Owner: ______________
1. A person reviews and is responsible for everything published.
2. Allowed uses: ______________________________________________________
3. Not allowed: _______________________________________________________
4. Sections/classes never sent to an outside provider: ___________________
5. Who may use it (policy expai/write): ________________________________
6. Approval required for AI drafts in: _________________________________
7. Disclosure to readers: ______________________________________________
8. Monthly limit: ________   Review by: ______________ every month
```

## Related pages

- [Exponential Intelligence](exponential-intelligence.md)
- [Drafts, versions and undo](../day-to-day/drafts-versions-and-undo.md)
- [Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
