---
title: What exists and what does not
audience: individual, website-creator, customer
category: website-creation
order: 1506
summary: A plain feature map for individuals - what ships with Exponential for personal sites, portfolios, blogs, family sites and club sites, what needs an optional extension, what needs an outside service, and what does not exist at all, each with its source.
keywords: features, limits, what exists, not built in, comments, ezcomments, importer, WordPress, birthday reminders, ticketing, membership fees, domain, mail, autorss, syndication, ezpm, outside service, extension
type: help
---
# What exists and what does not

This page is for individuals who want to know, before they start, which features they can rely on. Each line was
checked against the repository: the root `composer.json` (what is installed by default), the `extension/` folder
and the content classes of the shipped site packages. "Ships" means it is part of the default installation.

## Contents

1. [Ships with Exponential](#ships-with-exponential)
2. [Ships, but needs setup](#ships-but-needs-setup)
3. [Optional extensions you add yourself](#optional-extensions-you-add-yourself)
4. [Needs an outside service](#needs-an-outside-service)
5. [Does not exist](#does-not-exist)
6. [If you need something missing](#if-you-need-something-missing)

## Ships with Exponential

| Feature | Source | Page |
|---|---|---|
| Blog, Blog post, Comment, Gallery, Image, Event, Event calendar, Poll, Feedback form, Forum classes | the `sevenx_classes` package of the default site package | [Content classes for websites](../creators/website-creators/content-classes-for-websites.md) |
| Drafts, versions, scheduled publication | kernel; `README.md` "Main Exponential features" | [Publishing and versions](../publishing-and-versions.md) |
| RSS 1.0, RSS 2.0, Atom, OPML and podcast feeds | kernel module `rss` | [RSS feeds for your blog](blogs/rss-feeds-for-your-blog.md) |
| Image variations (thumbnails and sizes) | `settings/image.ini` | [Image variations for portfolios](portfolios/image-variations-for-portfolios.md) |
| Image editor in the edit form | `se7enxweb/ezie` in `composer.json` | [Editing images with ezie](portfolios/editing-images-with-ezie.md) |
| Multi-file upload | `se7enxweb/ezmultiupload` | [Uploading many images](portfolios/uploading-many-images.md) |
| Tags | `se7enxweb/eztags` | [Tags and categories](blogs/tags-and-categories.md) |
| Star rating field | `se7enxweb/ezstarrating`; the *Gallery* and *Image* classes have a `rating` field | [ezstarrating](../../features/6.0/extensions/ezstarrating.md) |
| Sections, roles, private areas | kernel | [Private areas](family-sites/private-areas-with-roles-and-sections.md) |
| Self-registration with activation by mail | kernel `user/register` | [Members and registration](club-sites/members-and-registration.md) |
| Forms and polls (information collector) | kernel | [Forms, polls and sign-ups](club-sites/forms-polls-and-sign-ups.md) |
| Newsletters | `se7enxweb/cjw_newsletter` | [Club newsletters](club-sites/club-newsletters.md) |
| Date of birth field | `se7enxweb/birthday` (datatype `ezbirthday`) | [Birthdays and family dates](family-sites/birthdays-and-family-dates.md) |
| Maps for a location | `se7enxweb/ezgmaplocation` | [ezgmaplocation](../../features/6.0/extensions/ezgmaplocation.md) |
| Two-step sign-in | `se7enxweb/sevenx_authentication_2fa` | [Two-factor sign-in](running-it/two-factor-sign-in.md) |
| CAPTCHA fields | `se7enxweb/recaptcha`, `se7enxweb/hcaptcha` (each needs keys from its service) | [Spam and security basics](running-it/spam-and-security-basics.md) |
| SQLite 3 database | kernel, the default | [Running on a small budget](setup/running-on-a-small-budget.md) |
| Velocity web server | `se7enxweb/exponential-velocity` | [Shared hosting or a small server](setup/shared-hosting-or-a-small-server.md) |
| Backups from the administration | `se7enxweb/git_manager` (**Setup > Backup**) | [Backups for one person](running-it/backups-for-one-person.md) |
| Writing assistant | `se7enxweb/expai` (drafts only) | [The writing assistant](running-it/writing-assistant-expai.md) |

## Ships, but needs setup

| Feature | What you must arrange |
|---|---|
| Comments on blog posts | Tick "enable comments" per post and give the right role permission to create *Comment* objects |
| Newsletters, scheduled publishing, feed import | A cron job that runs `runcronjobs.php` ([Cronjobs](../../guides/cronjobs.md)) |
| Registration, password reset, form mail, newsletters | Outgoing mail that reaches inboxes (a hosting matter) |
| Two-step sign-in | Activate the extension and its login handler in the settings |
| Writing assistant | A provider key or a local model, written into the settings by whoever runs the server |
| CAPTCHA | A site key and secret from reCAPTCHA or hCaptcha |
| HTTPS | A certificate: from your host, or Velocity can obtain one from Let's Encrypt |

## Optional extensions you add yourself

These are present on the demonstration installation but are **not** in the root `composer.json` `require` list, so
a new installation does not have them until you add them:

| Extension | What it does | Note |
|---|---|---|
| `autorss` | A workflow event that creates an RSS export automatically when you publish | composer package `se7enxweb/autorss`; the built-in RSS exports work without it |
| `ezpm` | Private messages between members | suggested in `composer.json`, not required |
| `ezfind` | Solr-based search | suggested; needs a Solr server; the built-in search is enough for a small site |

`syndication`, which is installed, is **not** an RSS tool: it synchronizes content between separate Exponential
installations over SOAP ([syndication](../../features/6.0/extensions/syndication.md)).

## Needs an outside service

| Need | Why outside |
|---|---|
| A domain name and its DNS | Rented from a registrar ([Domain names and HTTPS](setup/domain-names-and-https.md)) |
| Mail delivery | Your host's mail server or a mail provider sends the mail Exponential writes |
| AI text generation | A provider (or a local model you run) does the writing; `expai` only talks to it |
| CAPTCHA checks | reCAPTCHA or hCaptcha |
| Off-site backup storage | Anywhere that is not the same server |
| Payments | A payment provider, if you use the shop ([Payment gateways](../creators/website-creators/payment-gateways.md)) |

## Does not exist

Said plainly, so you can plan around it:

- **No `ezcomments` extension.** Comments are the shipped *Comment* class; there is no threaded comment system,
  no reply-by-mail and no "likes".
- **No one-click importer** from WordPress or another blog platform. There is a documented manual path
  ([Moving from another blog platform](blogs/moving-from-another-blog-platform.md)).
- **No birthday reminders or greeting mails.** The `birthday` extension only stores the date.
- **No ticketing, event registration with payment or membership fees.** Sign-up forms exist; payment for them does not.
- **No domain registration or DNS management.**
- **No built-in off-site backup storage.** Backups are written on the server; copying them away is your job.
- **No hosted service run by the project for individuals.** You run it, your host runs it, or a partner does.

## If you need something missing

1. Check whether an extension exists: [Extensions](../../guides/extensions.md) and the feature pages under
   [Extensions in 6.0](../../features/6.0/extensions/README.md).
2. Ask the community whether someone has built it ([Community channels](../advocates/community-channels.md)).
3. Pay a partner to build it, and consider giving it back ([When to hire help](running-it/when-to-hire-help.md),
   [Feature requests and sponsored features](../partners/feature-requests-and-sponsored-features.md)).

## Related pages

- [Individuals: start here](individuals-start-here.md)
- [Is Exponential right for you?](is-exponential-right-for-you.md)
- [Default extension distribution](../../features/6.0/default-extension-distribution.md)
- [What you can build](../creators/website-creators/what-you-can-build.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
