---
title: The writing assistant
audience: individual, customer, editor
category: website-creation
order: 1644
summary: What Exponential Intelligence (expai) can do for one person writing a blog, a portfolio or a club site - drafts only, never publishing; what it needs (a provider key or a local model, set up in the settings); where your text goes; costs and limits; and when not to use it, for example on private family content.
keywords: writing assistant, Exponential Intelligence, expai, AI, drafts, provider key, API key, Anthropic, OpenAI-compatible, Ollama, local model, alt text, costs, limits, privacy, data policy, individual
type: help
---
# The writing assistant

This page is for individuals who wonder whether the built-in writing assistant could help with their posts, captions
and pages. Exponential Intelligence (`expai`, in the root `composer.json`) is a writing assistant for the
administration interface: you ask in plain language, it writes **drafts**, as you and with your permissions. Nothing
is ever published by itself ([Exponential Intelligence](../../customers/growth/exponential-intelligence.md),
[expai](../../../features/6.0/extensions/expai.md)).

## Contents

1. [What it can do for you](#what-it-can-do-for-you)
2. [What it needs](#what-it-needs)
3. [Switching it on](#switching-it-on)
4. [Where your text goes](#where-your-text-goes)
5. [Costs and limits](#costs-and-limits)
6. [When not to use it](#when-not-to-use-it)
7. [Using it well](#using-it-well)

## What it can do for you

| Task | Where |
|---|---|
| Draft a post or page from a short brief | The **Intelligence** tab of a folder or blog |
| Rewrite, shorten, translate or suggest for one field | Field actions and the **Improve** menu in the edit form |
| Fill title, summary, meta description, keywords | Presets per class in the edit form |
| Suggest alternative text for images | From a vision-capable model; a "Missing alt text" report |

Everything lands as a draft or as a suggestion in the form for you to review.

## What it needs

From the feature page: Exponential 6.0.14 or later, PHP 8.0+ with `curl`, `dom`, `json` and `mbstring`, `ezjscore`
and `ezformtoken`, `expqueue` for queued work, and **a provider key or a local model**.

| Provider type | Examples named in the documentation | Note |
|---|---|---|
| Anthropic | Anthropic's own service | A key from the provider |
| Any OpenAI-compatible service | Melious.ai (EU), Hugging Face, OpenRouter, Mistral | A key from the provider |
| A local open-weight model | Through Ollama | No outside provider; needs a machine strong enough to run it |

Nothing works until you bring one of these. The project does not supply a key.

## Switching it on

On your own site you do this once ([expai, Switch it on](../../../features/6.0/extensions/expai.md#switch-it-on)):

1. Activate `expai` for the administration siteaccess.
2. Put the key in `settings/override/expai.ini.append.php` only (file mode 0640, never in version control):
   `[AISettings] Enabled=true` and `ApiKey=<your provider key>`.
3. Create its tables with `php extension/expai/bin/php/expaischema.php --install -s <admin siteaccess>`.
4. Give your role `expai/write` and `ezjscore/call` (function list `expai`).

If this is beyond you, it is a short, well-defined task for a partner ([When to hire help](when-to-hire-help.md)).

## Where your text goes

Text you ask about is sent to the provider you configured. `expai` lets you choose a region per provider (`us`, `eu`,
`local`) and retention, set rules per section and class for what may be sent, and records per request where it went;
prompts, answers and keys are not recorded in the audit trail. A local model keeps everything on your machine.
**Setup > EI: Summary** prints every provider's region, retention and key source on one page.

## Costs and limits

Providers charge per use. `expai` shows an estimated cost per request and lets you set limits per user, day, month and
site, so a mistake cannot run up a bill ([Costs and limits](../../customers/growth/exponential-intelligence.md#costs-and-limits)).
Set a low monthly limit first and raise it if you find it useful.

## When not to use it

- **Private family content**: names, children, health, addresses. Do not send it to an outside provider. Exclude the
  private section in the data policy, or use a local model.
- **Members' data** on a club site.
- **Your own voice**: if readers come for how *you* write, use it for chores (summaries, alt text, meta descriptions),
  not for the posts.
- **Facts you cannot check.** It can be confidently wrong; every fact in a draft is yours to verify.

## Using it well

- Give it a short, concrete brief: audience, length, tone, the points to make.
- Treat every draft as a first draft; rewrite in your words.
- Check names, dates, numbers and links.
- Say on your site if you use AI help, where your readers would want to know.

See [Using AI drafts responsibly](../../customers/growth/using-ai-drafts-responsibly.md).

## Related pages

- [Running it alone](README.md)
- [Exponential Intelligence](../../customers/growth/exponential-intelligence.md)
- [expai](../../../features/6.0/extensions/expai.md)
- [Writing for your own site](../personal-sites/writing-for-your-own-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
