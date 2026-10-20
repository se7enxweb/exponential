---
title: Publication schemes
audience: government, project-manager, editor, website-creator
category: public-sector
order: 40
summary: How to build a publication scheme (a guide to the information a public body publishes) in Exponential, as one page per class of information with links, owners and formats, and how to keep it accurate.
keywords: publication scheme, guide to information, information asset register, classes of information, transparency, FOI, proactive publication
type: help
---
# Publication schemes

This page is for managers and site builders who must publish a guide to the information their body makes available,
often called a publication scheme. Whether one is required, and its required headings, depend on your jurisdiction.
In Exponential a scheme is ordinary content: a page that lists classes of information, each linking to where the
information is.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What a scheme contains](#what-a-scheme-contains)
2. [Building it as content](#building-it-as-content)
3. [A class for scheme entries](#a-class-for-scheme-entries)
4. [Keeping it accurate](#keeping-it-accurate)
5. [Checklist](#checklist)

## What a scheme contains

For each class of information, a scheme usually says:

- what the information is;
- where to find it (a link) or how to ask for it;
- the format (web page, PDF, CSV, paper on request);
- whether there is a charge;
- the language versions available.

## Building it as content

1. Create a folder "Publication scheme" below the transparency area.
2. Write an introduction in the folder: what the scheme is, how to request information not listed, contact mailbox.
3. Create one child per class of information (see the class below).
4. Let the folder's full view list the children with their summary and link.

Because every entry is an object, it has versions, can be translated into each content language, and changes are
in the audit trail ([Content history](../../../guides/content-history.md), [Audit trail](../../../features/6.0/audit-trail.md)).

## A class for scheme entries

Create a class `scheme_entry` in **Setup > Classes** ([Content classes for websites](../../creators/website-creators/content-classes-for-websites.md)):

| Attribute | Datatype | Note |
|---|---|---|
| Title | Text line | the class of information |
| Description | Rich text | what it contains |
| Where to find it | Object relation list | links to the folders or documents on the site |
| External address | URL | when it is published elsewhere |
| Formats | Selection, multiple | web page, PDF, CSV, paper |
| Charge | Text line | "free" or the rule |
| Owner | Text line | a team or function, not a person |
| Last reviewed | Date | printed on the page |

Object relations keep links correct when the target moves, because they point to the object, not to an address
([Relations and reuse](../../creators/website-creators/relations-and-reuse.md)).

## Keeping it accurate

- Review each entry at the interval your policy sets; update "Last reviewed".
- **Setup > Link management** shows external links that stopped working.
- **Broken relations** in Setup (`/contentcheck/brokenrelations`) finds relations to content that was removed.
- When a class of information is discontinued, keep the entry with a note rather than removing it.

## Checklist

- [ ] Every class of information has an entry, an owner and a format.
- [ ] Every entry links to the information or says how to ask.
- [ ] The scheme exists in every official language of the site.
- [ ] Review dates are kept.

## Related pages

- [Proactive disclosure](proactive-disclosure.md)
- [Relations and reuse](../../creators/website-creators/relations-and-reuse.md)
- [Link and content checks](../../creators/website-creators/link-and-content-checks.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
