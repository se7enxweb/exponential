---
title: Translation help
audience: ai-agents, editor, admin
category: ai-agents
order: 27
summary: How Exponential Intelligence helps with translations - the Translate field action, asking for a translated draft in the Intelligence tab, and the translation sync that classifies every translation after the source changed (unchanged, patch or full retranslation), queues small patches below a cost threshold and leaves the rest for an editor to approve.
keywords: translation, machine translation, AI translation, Translate field action, translation sync, patch, full retranslation, cost threshold, source language, expai/translation_sync, workflow event, multilingual, review
type: help
---
# Translation help

This page is for editors and administrators of multilingual sites who want help translating content. Exponential
Intelligence offers three levels: one field, one object, and keeping every translation in step with its source. In
each case the result is a **draft** for a person who reads the target language to review.

## Contents

1. [One field: the Translate action](#one-field-the-translate-action)
2. [One object: ask in the Intelligence tab](#one-object-ask-in-the-intelligence-tab)
3. [Every translation: the translation sync](#every-translation-the-translation-sync)
4. [Setting up the translation sync](#setting-up-the-translation-sync)
5. [Reviewing machine translations](#reviewing-machine-translations)

## One field: the Translate action

In the edit form, the field action **Translate** translates the value of one field into another language of the
site; it asks for the language. The new value goes into the field with an **Undo** link; nothing is stored until you
store or publish the draft ([What Exponential Intelligence does](what-exponential-intelligence-does.md#the-edit-form)).

## One object: ask in the Intelligence tab

In the **Intelligence** tab you can ask, for example, "Translate this article into German as a new draft." The
assistant writes a draft of the translation as you, with your permission to edit in that language. You review it and
publish it, or send it for approval.

## Every translation: the translation sync

When the source language of an object changes, its translations are out of date. The translation sync finds what
changed and decides per translation how to update it
([extension documentation: translation sync](https://github.com/se7enxweb/expai/blob/main/doc/translation-sync.md)):

| Decision | When | What happens |
|---|---|---|
| `noop` | Nothing translatable changed | The translation is marked up to date |
| `patch` | A small share changed, few blocks, the estimate at or below the cost threshold | **Queued at once**; only the changed blocks are sent, with their old and new source and the current translation of that block |
| `patch`, waits | A patch above the threshold or with more changes | Waits for an editor to approve |
| `full` | Most of the source changed, the translation no longer has the structure of its base, or no base was found | Waits for an editor to approve |

The rule in one sentence: **patches below the cost threshold are queued automatically; full translations and anything
above the threshold wait for an editor.** Markup travels as placeholders, so links, emphasis and embedded objects stay
exactly as they are; unchanged blocks keep the existing translation byte for byte.

The result is always a **draft** of the translation, labeled Exponential Intelligence. Nothing is published.

In the Intelligence tab, objects with more than one language get a **Translations** section that lists every
translation with its status, decision, changed share, estimate and draft. Users with `expai/translation_sync` (and
`expai/write`) have the buttons Check translations, Approve, Translate in full and Skip; Approve and Skip also need
permission to edit in that language.

## Setting up the translation sync

1. The job queue (extension `expqueue`) must run, with its cron part or a worker.
2. Add the workflow event **Exponential Intelligence: translation sync** to a workflow connected to the trigger
   **content / publish / after**. If that trigger already has a workflow, add the event to it (a trigger has one
   workflow). The event never stops a publish.
3. Give the translators' role `expai/translation_sync`.
4. Check the cost threshold and the patch limits in `[TranslationSync]` of your expai settings.

From the command line, for one object:

```
./console ext:expai:translation-sync --object=<id> --languages=<language> --approve --login=<user>
```

## Reviewing machine translations

A machine translation is a first draft, not a final text. Before you publish one:

- Have it read by someone who knows the target language and the subject.
- Check names, numbers, dates, units and legal terms against the source.
- Check that links still point to the page in the right language.
- Watch for text that was correct in the source but reads oddly in the target.

The advocates' guide [Reviewing a machine-assisted translation](../../advocates/reviewing-a-machine-assisted-translation.md)
and the [review checklist](../checklists/reviewing-ai-drafts.md) go further.

## Related pages

- [Languages](../../languages.md)
- [Multilingual businesses](../../businesses/multilingual/README.md)
- [Chains and queued work](chains-and-queued-work.md)
- [Cost control](../governance/cost-control.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
