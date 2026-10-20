---
title: "Demo script: Exponential Intelligence"
audience: press
category: press-media
order: 158
summary: A six-minute video script for Exponential Intelligence, the writing assistant that writes drafts only - the off-camera setup with a provider key you can revoke, the Intelligence tab, the edit form actions, the drafts it wrote, and the data and cost controls - with what to say, what never to show and what not to claim.
keywords: demo script, Exponential Intelligence, expai, AI, writing assistant, drafts only, provider key, Intelligence tab, edit form, data policy, region, cost, video, creator
type: help
---
# Demo script: Exponential Intelligence

This page is for creators who show Exponential Intelligence, the writing assistant of the administration. The one
message that must survive any edit of your video: **it writes drafts only**, as the signed-in editor and with that
editor's permissions, and never publishes on its own (`README.md`, "Exponential Intelligence";
[Exponential Intelligence](../../features/6.0/extensions/expai.md)).

It is the extension `se7enxweb/expai`, release 0.0.1.0, required in `composer.json` since 6.0.15, which is in
development. Say so: "the 6.0.15 development line" or the version you really run. The script runs about six minutes;
the setup is done **off camera**.

## Contents

1. [Off camera: the setup](#off-camera-the-setup)
2. [The script](#the-script)
3. [What never to show](#what-never-to-show)
4. [What not to claim](#what-not-to-claim)
5. [Without a provider key](#without-a-provider-key)

## Off camera: the setup

On a new installation of the development line the README says the assistant is already active for the
administration siteaccesses, its tables exist and the Administrator role holds its policies; it "sends nothing
anywhere until you add a provider key". On an older installation follow the upgrade steps in the README or
[Switch it on](../../features/6.0/extensions/expai.md#switch-it-on).

```text
[ ] A provider key with a low spending limit, created only for this recording, that you can revoke afterwards
[ ] The key is in settings/override/expai.ini.append.php ([AISettings] Enabled=true, ApiKey=...), never in a
    committed file, and that file is never opened on screen
[ ] The cron parts expqueue and expai run, if you show queued work (chains, alt text reports)
[ ] The demo editor has expai/write and ezjscore/call, or you record as the administrator
[ ] A folder with invented content to write below
[ ] You tried the exact prompt beforehand and know roughly what it costs
```

The data policy decides where content may go: each provider states its region (`us`, `eu` or `local`) and its
retention, and every request records where it went (`README.md`, "Safety").

## The script

| Time | Click | Say |
|---|---|---|
| 0:00 | Title card | Disclosure line. "This is Exponential Intelligence, the writing assistant of Exponential, on my own test installation, <version>." |
| 0:15 | Open a folder; choose the **Intelligence** tab | "A conversation per user and per item. I ask in plain language." |
| 0:30 | Type a short, harmless request, for example one short article about an invented event below this folder | "It looks up what it needs, such as the content type and the tree around it, and then writes." |
| 1:00 | Show the step list | "Each lookup and each draft is a step I can follow." |
| 1:45 | Point at the drafts it wrote | "These are drafts. Nothing is published." |
| 2:10 | Open the draft, then go back | "It wrote as me, with my permissions. **Save as drafts** keeps them; **Send for approval** runs the normal publish operation as me, so an approval workflow still holds the version." |
| 3:00 | Open an item's edit form | "In the form the assistant offers actions per field: write, rewrite, shorten, translate, suggest, and an **Improve** menu." |
| 3:30 | Use **Rewrite** or **Shorten** on one field | "The value goes into the form for me to review. Nothing changes until I accept it and store the draft." |
| 4:10 | Point at the line with model, region and cost | "Each click shows the model, the region and what it costs." |
| 4:40 | Back to the **Intelligence** tab; discard the draft | "I throw the draft away; the site is unchanged." |
| 5:10 | Title card | "Every request is recorded in the audit trail, without prompts, answers or keys. The documentation is linked below." |

## What never to show

- The provider key, the file `settings/override/expai.ini.append.php`, the provider's own dashboard or billing page.
- Content you are not allowed to send to an outside service: use invented text only.
- Real people's names in prompts. Prompts may be sent to the provider you chose.

After the recording, revoke the key at the provider, even if you believe it never appeared
([Keeping secrets off screen](keeping-secrets-off-screen.md)).

## What not to claim

| Do not say | Say instead |
|---|---|
| "The AI publishes for you." | "It writes drafts only." |
| "Your data never leaves the server." | "With a local model through Ollama the data never leaves the server; with a hosted provider it goes to that provider's region." |
| "It's free." | "The extension is free software; a hosted provider charges for its use, and the assistant shows the estimated cost." |
| "It works with every AI service." | "Anthropic, any OpenAI-compatible service, or a local model through Ollama; only models that passed a live probe may write drafts." |
| "It's compliant with <law>." | "There is a printable EI: Summary page for a data protection officer." Leave legal conclusions to them |
| "It is in 6.0.14." | It is required from 6.0.15, which is in development; it needs Exponential 6.0.14 or later |

## Without a provider key

Show the documentation page instead and say: "It sends nothing anywhere until a provider key is added." That is a
complete, honest segment ([Demo scripts, Part 6](../business/demo-scripts.md#part-6-exponential-intelligence-4-minutes-optional)).

```text
DESCRIPTION BLOCK
Shows Exponential Intelligence (expai <the version you installed>) in Exponential <version>, on my own test installation.
It writes drafts only and never publishes on its own.
Provider used in this video: <provider, region>. Costs of this recording were paid by <me | who>.
Feature page: <link to "Exponential Intelligence"> - Repository: https://github.com/se7enxweb/expai
```

## Related pages

- [Talking points: Exponential Intelligence](../advocates/talking-points-exponential-intelligence.md)
- [Objection: "What about the AI features?"](../advocates/objection-ai-features.md)
- [Keeping secrets off screen](keeping-secrets-off-screen.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
