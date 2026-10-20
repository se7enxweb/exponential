---
title: What you own
audience: customer, businesses
category: customer-success
order: 5
summary: What a customer of Exponential owns and controls: the software's license, your content and data, the code written for you, the accounts, the domain and the backups, and the questions to settle with your partner and host so that you can always move or continue.
keywords: ownership, license, GPL, free software, content, data, code, extensions, domain, accounts, backups, handover, lock-in, exit
type: help
---
# What you own

Owning a site means more than paying for it. When a partner leaves, a host closes or a contract ends, you want to
be able to continue: with the same content, the same addresses and, ideally, the same code. This page lists what
you own or control, and the questions to settle so that it stays that way.

> **Note**
> This page describes the software's license and good practice. It is not legal advice. Your contracts decide who
> owns the code written for you and what a provider must hand over.

## The software: free, under the GNU GPL

Exponential is free software. The repository's `README.md` ("License") says that it is licensed under the GNU
General Public License version 2, that using it under the GPL is free of charge, and that the old professional
license is no longer available. The license text is in the file `LICENSE`.

What that means for you as a customer:

- **No license fee** for the software itself.
- **The right to use, study, change and pass it on**, under the conditions of the GPL.
- **No dependency on one vendor** for the right to run it: any partner or host can work on your installation.

The help you are reading is free documentation under the GNU Free Documentation License 1.3 or later
([Free documentation](../free-documentation.md)).

## Your content and your data

Your articles, products, pages, images and files, and the data people leave on your site (accounts, orders, form
submissions), are stored in your installation's database and storage directory. Make sure that:

- you know **where** they are stored (which host, which country) ([What to ask your host](healthy/what-to-ask-your-host.md));
- there are **backups** you could hand to another provider ([Backups](healthy/backups.md));
- you can get a **complete copy** on request, in a form another provider can restore, and your contract says so
  ([Contracts and SLAs](partners-and-hosts/contracts-and-slas.md)).

Personal data in your site is your responsibility as the organization that runs it. See
[GDPR requests](commerce/gdpr-requests.md) for what the software offers when someone asks for their data.

## The code written for you

Most sites have some code of their own: a design, templates, perhaps an extension with special functions.
Exponential keeps such code in extensions and designs, apart from the kernel (`README.md`: "Separation of content and
presentation layer", "plugin API"). Questions to settle with your partner:

| Question | Why it matters |
|---|---|
| Who owns the copyright of the code written for you, and under which license do you receive it? | Decides whether another partner may continue it |
| Where is the code kept (a repository you can access)? | Without the code, nobody can change the site |
| Is every change in that repository, or are there changes made directly on the server? | Changes only on the server are lost at the next upgrade or move |
| Which third-party extensions does the site use, and under which licenses? | Each one has its own terms and its own upkeep |

The [Handover checklist](partners-and-hosts/handover-checklist.md) turns these into a list to complete before a
provider changes.

## Accounts, domain and certificates

- **Administrator accounts**: at least one belongs to someone in your organization, not only to the partner
  ([Admin accounts and access](healthy/admin-accounts-and-access.md)).
- **The domain name** is registered in your organization's name, and you can reach the registrar account.
- **Third-party accounts** (payment provider, mail delivery, analytics, an AI provider key) are in your
  organization's name where possible, so a change of partner does not cut them off.

## Published versions do not change

Exponential's releases are published versions that never change: "A published tag is never changed: a fix for a
problem in `v6.0.14` or an earlier tag reaches you as the next release, not as a corrected copy of the old one"
(`SECURITY.md`). For an owner this means that the version number your provider names is exact: two installations of
the same version run the same code.

## Related pages

- [Handover checklist](partners-and-hosts/handover-checklist.md)
- [Taking over a site](partners-and-hosts/taking-over-a-site.md)
- [Licensing for buyers](../business/licensing-for-buyers.md)
- [Who is 7x?](../who-is-7x.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
