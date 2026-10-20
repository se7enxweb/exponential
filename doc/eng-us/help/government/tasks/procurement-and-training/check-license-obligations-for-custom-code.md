---
title: Check license obligations for custom code
audience: government, project-manager, partner, admin
category: public-sector
order: 240
summary: Step by step, check that custom code delivered for a public body's Exponential site respects the GPL and gives you the rights you need - source received, licenses of extensions and libraries, third-party assets, and sharing with other bodies - based on the licensing FAQ.
keywords: license check, GPL, custom code, extensions, libraries, compatibility, source code, third-party assets, sharing, procurement task
type: help
---
# Check license obligations for custom code

**Who:** the project manager with a developer, and legal counsel for doubtful cases. **When:** at delivery and before
acceptance. The rules come from [Licensing questions: the GPL and Exponential](../../../project/licensing-faq.md).

> **Note** This page describes features and common practice; it is not legal advice. Check your own case with your
> legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Result](#result)
4. [Pitfalls](#pitfalls)

## Before you start

- The list of delivered extensions, designs and libraries.
- The contract's clauses on ownership and source ([Intellectual property of custom work clause](../../procurement/tender-templates/intellectual-property-of-custom-work-clause.md)).

## Steps

1. **Check you received the source** of everything: PHP and templates as written, JavaScript and CSS with their unminified
   originals, images in their editable form ([Licensing questions](../../../project/licensing-faq.md#what-does-source-code-mean-for-a-web-application)).
2. **Check the license of each extension**: its `ezinfo.php`, `extension.xml` or `composer.json` states it; the about
   page lists extensions with their license ([About and package pages](../../../../features/6.0/about-and-package-pages.md)).
   Code that extends Exponential and is distributed to you is under the GPL version 2 or later.
3. **Check libraries** loaded into the same PHP process: they must be GPL-compatible; libraries reached only over the
   network are separate ([Licensing questions](../../../project/licensing-faq.md#my-extension-uses-a-library-under-another-license-is-that-allowed)).
4. **Check assets**: fonts, icons and images need a license that allows your use.
5. **Check that nothing restricts you**: no clause or license that forbids changing the code, giving it to another
   supplier or to another public body.
6. **Decide on sharing**: generally useful extensions can be contributed to the project or shared with other bodies
   under the GPL ([Contribute extensions back](../../../contribute-extensions-back.md)).
7. **Record the result** in the acceptance file.

## Result

A list of delivered components with their licenses, and confirmation that you hold the source and the rights to
maintain, change and pass on the code.

## Pitfalls

- Receiving only minified or obfuscated code.
- A "proprietary" extension combined in the same process with GPL code; ask counsel.
- Assuming that running a website obliges you to publish your code: it does not
  ([Licensing questions](../../../project/licensing-faq.md#do-i-have-to-give-my-websites-code-to-its-visitors)).

## Related pages

- [Procurement and training tasks](README.md)
- [Open source and licensing requirements](../../procurement/requirements/open-source-and-licensing-requirements.md)
- [FAQ: licensing and open source](../../faq/licensing-and-open-source-faq.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
