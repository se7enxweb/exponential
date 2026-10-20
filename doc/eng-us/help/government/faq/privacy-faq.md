---
title: "FAQ: privacy and data protection"
audience: government, project-manager, admin
category: public-sector
order: 40
summary: Answers to the privacy questions public bodies ask about Exponential - GDPR, cookies, consent, data sent to third parties, access and erasure requests, logs and retention, AI features - backed by the privacy standards page, with the decisions that stay with the organization.
keywords: privacy FAQ, GDPR, data protection, cookies, cookie banner, consent, third parties, analytics, erasure, access request, retention, logs, pseudonymization, public sector
type: help
---
# FAQ: privacy and data protection

This page answers the privacy questions data protection officers in public bodies ask about Exponential. The answers
come from [Privacy standards](../../../standards/privacy.md), which states that Exponential is software, not a data
controller: compliance is the responsibility of the organization that runs the site.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Is Exponential GDPR compliant?](#is-exponential-gdpr-compliant)
2. [Which cookies does it set? Do we need a banner?](#which-cookies-does-it-set-do-we-need-a-banner)
3. [Does it send data to third parties?](#does-it-send-data-to-third-parties)
4. [How do we answer an access or erasure request?](#how-do-we-answer-an-access-or-erasure-request)
5. [How long are logs kept, and what do they contain?](#how-long-are-logs-kept-and-what-do-they-contain)
6. [How is e-mail consent handled?](#how-is-e-mail-consent-handled)
7. [What about the AI assistant?](#what-about-the-ai-assistant)

## Is Exponential GDPR compliant?

Software cannot be compliant on its own; the organization is. Exponential provides features that help: a consent log
for e-mail, opt-in mail categories, erasure on account removal, audit pseudonymization, IP masking in logs and no
third-party assets in the administration. The status is **Partly complies (features provided)**
([Privacy standards](../../../standards/privacy.md#gdpr)). Purposes, legal bases, the privacy notice, the record of
processing and processing agreements stay with you.

## Which cookies does it set? Do we need a banner?

The kernel sets only functional cookies: the session cookie, `is_logged_in`, the setup wizard's own cookie during
installation, an optional mobile detection cookie, and interface state cookies in the administration
([Privacy standards](../../../standards/privacy.md#eprivacy-directive-cookies)). There is **no cookie consent banner**.
If your site adds analytics, embeds or advertising, you must add one.

## Does it send data to third parties?

Not from the administration: its scripts and fonts are served locally. Some shipped extensions do when activated:
Google Analytics (`bcwebsitestatistics`), reCAPTCHA and hCaptcha, and social login providers. They are installed but
**not active** until a site activates them ([Privacy standards](../../../standards/privacy.md#third-party-services-in-shipped-extensions)).
Check `ActiveExtensions[]` on your site.

## How do we answer an access or erasure request?

- Mail preferences and consent history: exportable as JSON or CSV by the person or an administrator.
- Audit records about a user: `./console exp:audit export --subject-user=<id>`.
- Erasure: removing the account erases mail preferences and anonymizes the consent log; audit records are kept for
  their retention period and pseudonymized.

There is **no single "export all my data" or "delete me" function**: content, form submissions and orders are reached
through separate admin tools, and accounts are removed one at a time ([Privacy standards](../../../standards/privacy.md#known-gaps)).

## How long are logs kept, and what do they contain?

The audit trail keeps 90 days live and two years archived per channel, with addresses truncated and no passwords or
tokens. The 404, security and CSP logs have age limits; other kernel logs are rotated by size without an age limit
unless you set one ([Log rotation](../../../features/6.0/log-rotation.md)). The log viewer masks IP and e-mail
addresses ([Log viewer](../../../features/6.0/log-viewer.md)). Web server access logs are outside Exponential.

## How is e-mail consent handled?

Optional e-mail categories are off until the person turns them on; every change is logged with the text shown; every
optional mail has a one-click unsubscribe ([E-mail preferences](../../../features/6.0/mail-preferences.md)).

## What about the AI assistant?

Exponential Intelligence is an extension that is inactive until activated. When used, the editor's request goes to the
chosen provider: Anthropic, an OpenAI-compatible service (the feature page names an EU service among them) or a local
model through Ollama, which keeps data on your own server ([Exponential Intelligence](../../../features/6.0/extensions/expai.md)).
Choose the provider with your data protection officer, and name it in your records.

## Related pages

- [Government FAQ](README.md)
- [Privacy checklist](../checklists/privacy-checklist.md)
- [Privacy standards](../../../standards/privacy.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
