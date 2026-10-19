---
title: "Story: an assistant that writes drafts only"
audience: press
category: press-media
order: 55
summary: A feature story on one design decision of Exponential Intelligence - it writes drafts and never publishes on its own - with the safeguards around it (permissions, approval workflow, audit, master switch) and the facts and sources to print.
keywords: story, Exponential Intelligence, expai, drafts only, human in the loop, approval workflow, permissions, audit, AI safety, press
type: help
---
# Story: an assistant that writes drafts only

This page is for journalists who write about responsible use of AI in publishing. It focuses on one decision in
Exponential Intelligence: the assistant writes drafts, and a person decides what is published. The broader story is
[Story: Exponential Intelligence](story-exponential-intelligence.md).

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [The safeguards, one by one](#the-safeguards-one-by-one)
4. [Facts you can print](#facts-you-can-print)
5. [What not to claim](#what-not-to-claim)
6. [Angles and headline ideas](#angles-and-headline-ideas)
7. [Who to ask](#who-to-ask)
8. [Related pages](#related-pages)

## The story in one paragraph

Many AI tools publish for you. Exponential Intelligence does not: "Drafts only. It never publishes on its own. 'Send
for approval' runs the normal publish operation as the editor, so an approval workflow holds the version for the
approver" ([README, Safety](https://github.com/se7enxweb/exponential/blob/main/README.md#safety)). The assistant runs with the content permissions of the
person who asked, every version it wrote carries the label "Exponential Intelligence", and every request is an audit
event. The decision keeps the newsroom rule intact: a person is responsible for what goes live.

## What is new, and when

The assistant joined the Exponential distribution on 12 October 2026, in the 6.0.15 development line, which is not
released ([6.0.15 changelog](../../changelogs/6.0/6.0.15.md#12-october-2026-exponential-intelligence-in-the-distribution)).
The drafts-only rule is described in the extension's feature page and the README from that point on.

## The safeguards, one by one

| Safeguard | What it means | Source |
|---|---|---|
| Drafts only | The assistant writes drafts; it never publishes on its own. | [expai, In short](../../features/6.0/extensions/expai.md#in-short) |
| The editor's permissions | Every lookup and draft runs as the signed-in editor, who cannot do more through it than without it. | [README, Safety](https://github.com/se7enxweb/exponential/blob/main/README.md#safety) |
| Approval still applies | "Send for approval" runs the normal publish operation, so an approval workflow holds the version. | [expai, For editors](../../features/6.0/extensions/expai.md#for-editors) |
| A visible label | "Exponential Intelligence" on every version it wrote: My drafts, version views, edit forms, approval items. | [README, What it does for editors](https://github.com/se7enxweb/exponential/blob/main/README.md#what-it-does-for-editors) |
| Review before writing | In the Vector workspace, staged review of every change per field before anything is written, and "What changed" with revert. | same |
| Separate policies | `expai/write`, `expai/vector` and others, held only by the Administrator role in a new installation. | [README, Safety](https://github.com/se7enxweb/exponential/blob/main/README.md#safety) |
| Audit | Every request, draft and state change is an audit event; never a prompt, an answer or a key. | same |
| One switch | `[AISettings] Enabled` turns it off everywhere, or per siteaccess; a veto hook can refuse a request with a reason. | same |

## Facts you can print

| Fact | Source |
|---|---|
| "It never publishes on its own." | [expai, In short](../../features/6.0/extensions/expai.md#in-short) |
| Suggestions in the edit form change nothing until the editor accepts them and stores the draft. | [Creating and editing content](../editing-content.md#exponential-intelligence) |
| Without a provider key it sends nothing anywhere. | [README, How to enable it](https://github.com/se7enxweb/exponential/blob/main/README.md#how-to-enable-it) |
| Only models that passed a live tool-calling probe (or are trusted by configuration) may write drafts. | [README, Providers](https://github.com/se7enxweb/exponential/blob/main/README.md#providers-hosted-in-the-eu-or-on-your-own-server) |

## What not to claim

- "AI-generated content is impossible to publish by mistake." A person can still publish a poor draft; the rule is
  that a person decides.
- "It is safe" in general terms. Describe the safeguards instead.
- "It replaces editors." The sources describe an assistant to editors.
- Anything about who is legally responsible for AI text: that is a matter for each organization.

## Angles and headline ideas

```text
"The AI that is not allowed to press Publish"
"Human in the loop by design: Exponential's assistant writes drafts only"
"Your approval workflow still applies when the AI wrote the draft"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| Why drafts only, and how it is enforced | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| The full safety documentation | the extension's repository | <https://github.com/se7enxweb/expai> |

## Related pages

- [Story: Exponential Intelligence](story-exponential-intelligence.md)
- [Talking points: Exponential Intelligence](../advocates/talking-points-exponential-intelligence.md#points-for-the-skeptical)
- [Story: the audit trail](story-audit-trail.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
