---
title: Newsletters
audience: government, website-creator, admin, project-manager
category: public-sector
order: 60
summary: How a public body sends a periodic news digest with the cjw_newsletter extension in Exponential - lists, editions built from news articles, double opt-in, one-click unsubscribe, rehearsal with the file transport - and what a newsletter is not for.
keywords: newsletter, news digest, cjw_newsletter, edition, list, double opt-in, subscribe, unsubscribe, List-Unsubscribe, blacklist, bounces, file transport, consent
type: help
---
# Newsletters

This page is for website creators, administrators and project managers who send a regular news digest by e-mail. The
`cjw_newsletter` extension, activated by the default installation, provides lists, editions written as content,
double opt-in, scheduled sending, bounce handling and an archive. This page covers its use for news; the full
newsletter guides are [Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md),
[Newsletter lists and subscriptions](../../../creators/website-creators/newsletter-lists-and-subscriptions.md) and
[Sending newsletters](../../../creators/website-creators/sending-newsletters.md). The Governments help has its own
newsletters guide in the building section ([Newsletters](../newsletters/README.md)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [A news digest](#a-news-digest)
2. [Consent and unsubscribing](#consent-and-unsubscribing)
3. [Rehearsing before real mail](#rehearsing-before-real-mail)
4. [What a newsletter is not for](#what-a-newsletter-is-not-for)

## A news digest

| Part | In cjw_newsletter |
|---|---|
| The newsletter system and its list | content below `[NewsletterSettings] RootFolderNodeId`; visitors subscribe to the list |
| An edition | content (`cjw_newsletter_edition`) with articles (`cjw_newsletter_article`); drafts and preview like any content |
| Reusing news | the article pool (**Newsletter**, article pool list) to pick existing content for an edition |
| Sending | `newsletter/send` schedules; the cronjob parts `cjw_newsletter` and `cjw_newsletter_mailbox` build, send and handle bounces |
| The administration | **Newsletter** (`/newsletter/index`) |

A monthly digest of news, consultations and events works well for public bodies; it complements RSS for people who
prefer e-mail.

## Consent and unsubscribing

| Requirement | cjw_newsletter and the kernel |
|---|---|
| Opt-in | double opt-in: the subscription counts only after the confirmation mail's link |
| Easy unsubscribing | `newsletter/unsubscribe`; Exponential adds `List-Unsubscribe` and `List-Unsubscribe-Post` headers to optional mail sent through its mail gate ([Privacy standards](../../../../standards/privacy.md)) |
| Blocked addresses | a blacklist; bounced addresses marked |
| Records | subscription statuses: pending, confirmed, approved, removed, bounced |

Collect only an e-mail address (and a name if you use it). In the EU, the [ePrivacy Directive](https://eur-lex.europa.eu/eli/dir/2002/58/oj)
and the [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj) apply to marketing-type mail; public information
newsletters are often treated alike in practice.

## Rehearsing before real mail

The newsletter has its own transports; set them to file first, send a test edition to a test list with addresses on a
reserved domain, and read the mails written to disk ([Sending newsletters](../../../creators/website-creators/sending-newsletters.md)).
Only then switch to real sending.

## What a newsletter is not for

| Need | Use instead |
|---|---|
| Urgent warnings | the website, warning systems, notifications ([The urgent notices pattern](urgent-notices-pattern.md)) |
| Personal case information | the case system's own messages |
| Mail to everyone who ever registered | only subscribers who opted in |

## Related pages

- [News and alerts](README.md)
- [cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md)
- [Notifications and subscriptions](notifications-and-subscriptions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
