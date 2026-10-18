---
title: Answering objections as an advocate
audience: advocate
category: advocacy
order: 30
summary: A method for answering doubts about Exponential as a community advocate, in public threads and private conversations - listen, find the real concern, answer with a source, offer a check, admit limits - with reply templates and the list of objection pages.
keywords: objections, doubts, criticism, answering, method, forum, social media, reply templates, skeptics, honest answers, sources
type: help
---
# Answering objections as an advocate

This page is for advocates who meet doubts: "Isn't it dead?", "PHP is old", "Nobody uses it", "Why not X?". Sales
people answer buyers in meetings ([Handling objections](../business/objection-handling.md)); advocates answer peers,
often in public, where many others read along. That changes the goal: you are not trying to win the person in front of
you, you are leaving an accurate, friendly answer for everyone who reads it later.

## Contents

1. [The method](#the-method)
2. [Public versus private](#public-versus-private)
3. [Reply templates](#reply-templates)
4. [The objection pages](#the-objection-pages)
5. [When to stop](#when-to-stop)

## The method

| Step | What you do | Example |
|---|---|---|
| 1. Listen | Read the whole message; quote it fairly | "You wrote that the project looks abandoned." |
| 2. Find the real concern | Ask what lies behind it, if unclear | "Do you mean security fixes, releases, or people to ask?" |
| 3. Acknowledge | The concern is usually reasonable | "That is a fair thing to check before choosing any CMS." |
| 4. Answer with a source | One fact, one link | "Fifteen 6.0.x releases since January 2024: (link)." |
| 5. Offer a check | A command, a page, a screen | "`git tag -l 'v6.0.*' --sort=version:refname` shows them all." |
| 6. Admit limits | Say what the sources do not say | "The sources do not count users, so I will not guess." |
| 7. Invite | A next step, never pressure | "If you try it, the forums and the Telegram chat are open." |

## Public versus private

| | Public (forum, social media, comments) | Private (chat, e-mail, in person) |
|---|---|---|
| Your real audience | Everyone who reads later | The person in front of you |
| Length | Short, with links | As long as they want |
| Tone | Calm, factual, kind; no sarcasm | Personal, curious |
| Mistakes | Correct them openly ([Claims you must not make](claims-you-must-not-make.md#when-you-got-it-wrong)) | Correct them and follow up with the source |
| Security details | Never | Never: send to `SECURITY.md` ([Security reports stay private](security-reports-stay-private.md)) |

## Reply templates

Adapt these; never paste them unchanged.

```text
ACKNOWLEDGE AND ANSWER
That's a fair question. <Fact in one sentence> (<link>). You can check it yourself with <command or page>.
If you have a specific case in mind, I'm happy to look at it.

WHEN YOU DO NOT KNOW
Good question - I don't know, and I don't want to guess. I'll look for a source and come back here.
(Then do come back, with the source or with "I could not find one".)

WHEN THE ANSWER IS "NO"
You're right that Exponential doesn't <thing>. <What it does instead, if anything> (<link>).
If <thing> is essential for you, <the family member or alternative approach that fits, if documented>.

WHEN THEY ARE PARTLY RIGHT
Part of that is right: <the true part, with source>. One detail has changed since: <fact> (<link>).
```

## The objection pages

| Objection | Page |
|---|---|
| "Isn't it dead? Isn't that the discontinued product?" | [Objection: "Is it still alive?"](objection-is-it-still-alive.md) |
| "PHP is old", "the interface looks dated" | [Objection: PHP and modern technology](objection-php-and-modern-technology.md) |
| "Nobody knows it; we cannot hire" | [Objection: "Can we find people?"](objection-can-we-find-people.md) |
| "Why not another CMS?" | [Objection: "Why not another CMS?"](objection-why-not-another-cms.md) |
| "Open source is less secure" | [Objection: "Is open source secure?"](objection-open-source-security.md) |
| "AI will ruin content" / "we need AI" | [Objection: "What about the AI features?"](objection-ai-features.md) |
| A thread that turns hostile | [Handling hostile threads](handling-hostile-threads.md) |

For buyers, with more rows (support, upgrades, hosting): [Handling objections](../business/objection-handling.md#quick-reference).

## When to stop

- After two replies, if the other person is not asking questions any more. Readers have what they need.
- When the discussion becomes personal. See [Handling hostile threads](handling-hostile-threads.md).
- When the honest answer is "Exponential is not the right fit for that". Say it, and wish them luck
  ([Where Exponential is not the right fit](../business/comparing-exponential-factually.md#where-exponential-is-not-the-right-fit)).

## Related pages

- [Responding in public](responding-in-public.md)
- [Claims you must not make](claims-you-must-not-make.md)
- [Handling objections](../business/objection-handling.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
