---
title: "Tender template: intellectual property of custom work clause"
audience: government, project-manager, partner, developer
category: public-sector
order: 80
summary: Clause text for the rights to custom extensions, designs, integrations and documentation paid for under a CMS contract, written to fit the GPL that Exponential extensions inherit, with options to publish the work for reuse by other public bodies.
keywords: intellectual property, custom work, copyright, GPL, derivative work, extension, design, publish, reuse, public code, license grant, assignment, tender template
type: help
---
# Tender template: intellectual property of custom work clause

This page is for procurement officers and legal advisers who decide who may use, change and publish the custom work
a supplier builds: extensions, designs, templates, integrations, migration scripts and documentation. For
Exponential, the GPL shapes the options: the project's licensing FAQ explains that extensions and designs are
derivative works and, when distributed, are distributed under the GPL version 2 or later
([Licensing questions](../../../project/licensing-faq.md)).

**Template** - adapt to your procurement rules; not legal advice

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [Two models](#two-models)
2. [The clause](#the-clause)
3. [Fitting the GPL](#fitting-the-gpl)
4. [Notes for the evaluator](#notes-for-the-evaluator)

## Two models

| Model | The customer gets | Fits when |
|---|---|---|
| **License grant** | the work under the GPL version 2 or later (code) and an open documentation license; the supplier keeps copyright and may reuse the work | you want the freedoms and are happy for the supplier to reuse what it built |
| **Assignment** | copyright in the custom work, then licensed onwards as the customer decides (within the GPL for code that is a derivative work) | your rules require ownership of paid work |

Either way, the customer can publish the work and other bodies can reuse it, because GPL code can be passed on.

## The clause

```text
IP. CUSTOM WORK

IP.1 Definition. "Custom Work" means all software, templates, stylesheets, scripts,
     designs, content type definitions, configuration, migration scripts and
     documentation created by the Supplier for the Customer under this contract.

IP.2 [Option A - license] The Supplier licenses all Custom Work that is software
     to the Customer under the GNU General Public License version 2 or any later
     version, and all Custom Work that is documentation under <documentation
     license>. The Supplier delivers it in source form as set out in <source
     clause>.

IP.2 [Option B - assignment] The Supplier assigns to the Customer all copyright in
     the Custom Work on payment of <milestone>. Software in the Custom Work that is
     a derivative work of GPL software remains subject to the GPL.

IP.3 Third-party material. The Supplier shall not include material in the Custom
     Work that the Customer cannot use, change and publish under IP.2, and shall
     list every third-party asset (fonts, images, icons, libraries) with its
     license.

IP.4 Publication. The Customer may publish the Custom Work [in a public
     repository] [and offer it to the maintainers of the software]. The Supplier
     shall remove secrets and personal data from the repository before delivery.

IP.5 Pre-existing work. Work the Supplier created before this contract and uses in
     the delivery is licensed to the Customer under [the GPL version 2 or later /
     <license>]; the Supplier lists it in <annex>.

IP.6 Moral rights and credits. <as required by applicable law>.
```

## Fitting the GPL

- Code that uses Exponential's PHP classes, and JavaScript that works with its GPL JavaScript, must be GPL or
  GPL-compatible when distributed. Images and other files that PHP only sends to browsers are data, not affected
  ([Licensing questions](../../../project/licensing-faq.md)).
- An agency building a site for its client acts as the client's agent, which the FAQ says is not distribution. That
  is why IP.2 grants the license explicitly rather than relying on distribution.
- A library under a GPL-incompatible license must not be loaded into the same PHP process in distributed code; IP.3
  catches it.
- Documentation of Exponential itself is under the GNU FDL 1.3 or later
  ([The license of this documentation](../../../../README-LICENSE.md)); custom documentation that copies from it
  inherits that license.
- To share the work with the community, see [Contribute extensions back](../../../contribute-extensions-back.md) and
  [Publishing an extension](../../../partners/publishing-an-extension.md).

## Notes for the evaluator

- Option A is common with open source suppliers and costs less; Option B may be required by your rules.
- Check that IP.3's asset list is complete for the design: fonts are the usual surprise.

## Related pages
- [Tender templates](README.md)
- [Open source license and source availability clause](open-source-license-and-source-availability-clause.md)
- [Open source and licensing requirements](../requirements/open-source-and-licensing-requirements.md)
- [Reading licenses before you reuse anything](../../../extension-development/part-1-from-user-to-developer/reading-licenses-before-reusing.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
