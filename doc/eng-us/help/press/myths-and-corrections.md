---
title: Myths and corrections
audience: press, advocate
category: press-media
order: 176
summary: Ten myths about Exponential that circulate in forums, comments and older articles - that it is dead, that it is Ibexa's, that it is old PHP, that the AI publishes, that it is insecure because it is open, and more - each with what the sources actually show.
keywords: myths, misconceptions, corrections, is it dead, still alive, Ibexa, old PHP, AI, security, open source, free, legacy, fact check
type: help
---
# Myths and corrections

This page is for journalists who meet claims about Exponential in comments, forums or older articles. Each myth is
answered with what the sources show. The advocates answer the same doubts in conversation on their objection pages,
for example ["Is it still alive?"](../advocates/objection-is-it-still-alive.md).

## Contents

1. [The ten myths](#the-ten-myths)
2. [Why the myths exist](#why-the-myths-exist)
3. [Using this page](#using-this-page)

## The ten myths

### 1. "It is dead; eZ Publish ended in 2021."

The eZ Publish line ended; the code base did not. 7x released 6.0.0 on 1 January 2024 and fourteen more releases
until 7 June 2026, with 6.0.15 in development ([The 6.0.x releases at a glance](the-6-0-x-releases-at-a-glance.md)).
The README: "Exponential has made it beyond it's end of life in 2021 and survived".

### 2. "It is Ibexa's product."

"This project is not associated with the original eZ Publish software or its original developer, eZ Systems or Ibexa"
(README; [Not associated with eZ Systems or Ibexa](not-associated-with-ez-systems-or-ibexa.md)).

### 3. "It only runs on old PHP."

It supports PHP 7.4 to 8.5; PHP 7.4 sites can run 6.0.15 too, without support ([Story: current PHP](story-current-php.md)).

### 4. "The AI writes and publishes your site."

Exponential Intelligence writes drafts, as the signed-in editor with that editor's permissions, and never publishes on
its own ([Story: an assistant that writes drafts only](story-ai-that-writes-drafts-only.md)).

### 5. "Open source means insecure."

The project has a private reporting process with a first answer within one business day, fixes in the next release, a
Content Security Policy, an audit trail and a hardening guide ([Story: security](story-security.md)). No software is
"secure" as an absolute; the process is what can be checked.

### 6. "Free means unmaintained."

Development is led by 7x and funded by its contributions and sponsors; the repository shows the work commit by commit
([Two organizations, one project](two-organizations-one-project.md), [The history pages as a source](the-history-pages-as-a-source.md)).

### 7. "It needs a big database server."

SQLite is the default and needs no server; PostgreSQL, MongoDB, MariaDB, Oracle and MySQL are supported
([Story: SQLite by default](story-sqlite-by-default.md)).

### 8. "Legacy means abandoned."

"Legacy" names Exponential 6 when it runs beside a Symfony platform; Exponential 6 itself is the maintained line
([The product family for reporters](the-product-family-for-reporters.md)).

### 9. "It is a new CMS from 2025."

The code base has been developed since 1999; the name is from August 2025 ([Timeline for reporters](timeline-for-reporters.md)).

### 10. "7x owns it and can close it."

The Exponential Foundation and 7x hold the copyright together and the code is under the GPL, which lets anyone keep and
continue it ([Licenses explained for reporters](licenses-explained-for-reporters.md)).

## Why the myths exist

- **Old articles**: coverage from the eZ Publish years describes a product and a company situation that ended.
- **The rename**: readers who knew the old name do not always connect it with Exponential.
- **Similar names**: eZ Platform, Ibexa DXP and Exponential Platform are different products from different owners.
- **AI fears**: general worries about AI are applied to a feature that was designed to write drafts only.

## Using this page

- Answer a myth with the source, not with adjectives.
- When you correct a myth in an article, link the primary source, not only this page.
- If you find a myth not listed here, tell the project on the
  [community issue tracker](https://github.com/se7enxweb/exponential-community/issues).

## Related pages

- [Common errors in coverage](common-errors-in-coverage.md)
- [Answering objections as an advocate](../advocates/answering-objections-as-an-advocate.md)
- [Reporter FAQ](reporter-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
