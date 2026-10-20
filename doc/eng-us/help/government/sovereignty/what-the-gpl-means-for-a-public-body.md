---
title: What the GPL means for a public body
audience: government, project-manager
category: public-sector
order: 80
summary: The practical questions a public body settles about the GPL: running a website, agencies that build for you, sharing code with other public bodies, publishing your own extensions, combining with proprietary code, and the warranty.
keywords: GPL, public body, distribution, agency, shared code, inter-agency, extensions, proprietary code, warranty, license compliance, open source policy
type: help
---
# What the GPL means for a public body

This page is for managers, legal and procurement staff of a public body using Exponential. It turns the license into
the situations that come up in public administrations. It is based on
[Licensing explained for buyers](../../business/licensing-for-buyers.md), which quotes the license sections; the
license text is the [GNU GPL version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Running your website](#running-your-website)
2. [An agency builds the site for you](#an-agency-builds-the-site-for-you)
3. [Sharing with other public bodies](#sharing-with-other-public-bodies)
4. [Your own extensions](#your-own-extensions)
5. [Combining with proprietary code](#combining-with-proprietary-code)
6. [Warranty and liability](#warranty-and-liability)
7. [Questions for your legal counsel](#questions-for-your-legal-counsel)

## Running your website

Running Exponential on your own servers, or on servers a provider operates for you, to serve your site is running the
program. The license says "The act of running the Program is not restricted" (section 0). No source has to be
published because of that use.

## An agency builds the site for you

When an agency delivers the code of your site to you, the agency distributes it to you. You receive the GPL rights,
including the source of what was delivered. Write into the contract that you receive the complete source of every
custom extension and design, in a repository you control, with the right to give it to another supplier. The license
already grants that for GPL code; the contract makes the delivery practical.

## Sharing with other public bodies

Giving your extensions or your whole installation to another public body is distribution. That is allowed: keep the
notices, include the source (or a written offer), license the whole under the GPL, and mark your changes. Whether
handing code between two parts of the same government is "distribution" in your legal system is a question for your
counsel; treating it as distribution and following the conditions is the safe path.

## Your own extensions

You choose the license of extensions you write yourself, within what the GPL requires for works based on Exponential.
The project recommends GPL 2 or later for extensions you give back ([Contribute extensions back](../../contribute-extensions-back.md)).
Publishing them is never required for your own use, but it lets others maintain them with you (see "Contributing back
as a public body").

## Combining with proprietary code

If you plan to combine Exponential with code you want to keep proprietary and distribute the combination, ask legal
counsel before you start. Separate programs that only talk to each other over a network or are merely shipped side by
side ("mere aggregation") are a different case from code linked into Exponential.

## Warranty and liability

The GPL comes without warranty (sections 11 and 12). A public body that needs warranty, support response times or
liability gets them by contract from a support provider (see "Choosing a support provider").

## Questions for your legal counsel

1. Will we give copies to anyone outside our legal entity? To whom?
2. Do our contracts with agencies require delivery of the complete source in our repository?
3. Under which license do we publish code we write?
4. Does our open source policy or procurement law say anything about GPL software?
5. Are third-party components (fonts, scripts, extensions not from the project) under licenses we accept?

## Related pages

- [The open source license (GPL)](open-source-license-gpl.md)
- [Licensing explained for buyers](../../business/licensing-for-buyers.md)
- [Contribute extensions back](../../contribute-extensions-back.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
