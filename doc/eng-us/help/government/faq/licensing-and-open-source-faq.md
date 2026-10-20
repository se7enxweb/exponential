---
title: "FAQ: licensing and open source"
audience: government, project-manager, sales-marketing, partner
category: public-sector
order: 60
summary: Answers to the licensing questions public bodies ask about Exponential - the GPL, license fees, ownership of custom code, sharing with other agencies, publishing your own extensions, the documentation license and the source code - based on the licensing FAQ.
keywords: licensing FAQ, GPL, GPL-2.0-or-later, open source, license fee, source code, custom code, ownership, reuse between agencies, GFDL, documentation license, public sector
type: help
---
# FAQ: licensing and open source

This page answers the licensing questions public bodies ask about Exponential. It summarizes
[Licensing questions: the GPL and Exponential](../../project/licensing-faq.md), which follows the project's own
licensing FAQ.

> **Note** This page describes features and common practice; it is not legal advice. Check your own case with your
> legal counsel.

## Contents

1. [Under which license is Exponential?](#under-which-license-is-exponential)
2. [Is there a license fee?](#is-there-a-license-fee)
3. [Is it open source by the usual definition?](#is-it-open-source-by-the-usual-definition)
4. [Where is the source code?](#where-is-the-source-code)
5. [Who owns the custom code our supplier writes?](#who-owns-the-custom-code-our-supplier-writes)
6. [Must we publish our site's code?](#must-we-publish-our-sites-code)
7. [Can we share our extensions with other public bodies?](#can-we-share-our-extensions-with-other-public-bodies)
8. [What license does the documentation have?](#what-license-does-the-documentation-have)

## Under which license is Exponential?

The GNU General Public License, version 2 or any later version (`GPL-2.0-or-later` in `composer.json`)
([Licensing questions](../../project/licensing-faq.md#under-which-license-is-exponential);
[GPL v2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)).

## Is there a license fee?

No. The GPL allows any use, including commercial and public use, without a fee. You pay for services: implementation,
hosting, support, training ([Licensing questions](../../project/licensing-faq.md#can-i-use-exponential-for-commercial-work)).

## Is it open source by the usual definition?

The GPL is a free software license that meets the [Open Source Definition](https://opensource.org/osd). You may run,
study, change and redistribute the code.

## Where is the source code?

On GitHub, in the `se7enxweb/exponential` repository and the extension repositories under the same organization, and
as Composer packages on Packagist ([3. Getting the code](../../../install/03-getting-the-code.md)). Every installation
contains the source.

## Who owns the custom code our supplier writes?

Copyright follows your contract. The licensing FAQ adds: extensions and designs are derivative works, so **when they
are distributed** they are distributed under the GPL version 2 or later, and the recipient gets the source. An agency
building a site for a client acts as the client's agent, which the project does not see as distribution
([Licensing questions](../../project/licensing-faq.md#agencies-and-clients)). Write into the contract that you receive
all source code and may use, change and pass it on.

## Must we publish our site's code?

No. Running a website is not distribution; you do not have to give the code to visitors or anyone else
([Licensing questions](../../project/licensing-faq.md#do-i-have-to-give-my-websites-code-to-its-visitors)).

## Can we share our extensions with other public bodies?

Yes. Giving them to another body is distribution, so it happens under the GPL, which is what makes sharing easy: the
other body may use and change them. Contributing them to the project lets everyone maintain them
([Contribute extensions back](../../contribute-extensions-back.md)).

## What license does the documentation have?

The GNU Free Documentation License 1.3 or later. You may copy, adapt, translate and republish this help, including in
your own manuals, if you keep the notices ([Free documentation](../../free-documentation.md),
[The license of this documentation](../../../README-LICENSE.md)).

## Related pages

- [Government FAQ](README.md)
- [Licensing questions: the GPL and Exponential](../../project/licensing-faq.md)
- [Licensing for partners](../../partners/licensing-for-partners.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
