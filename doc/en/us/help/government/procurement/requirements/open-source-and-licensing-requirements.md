---
title: Open source and licensing requirements
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 130
summary: Requirement wording for open source licensing in a CMS tender - license of the software, source availability, rights to custom work, third-party components and documentation license - with the facts of Exponential's GPL and GFDL licensing.
keywords: open source, license, GPL, GPL-2.0-or-later, GFDL, source code, custom work, intellectual property, third-party components, Open Source Definition, public money public code, tender, requirements
type: help
---
# Open source and licensing requirements

This page is for procurement teams and legal advisers who want open source software and need to write that into a
tender in a verifiable way. It gives requirement wording and the licensing facts of Exponential, from
[Licensing questions: the GPL and Exponential](../../../project/licensing-faq.md) and
[The license of this documentation](../../../../README-LICENSE.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [Why it matters for a public body](#why-it-matters-for-a-public-body)
2. [Requirement wording](#requirement-wording)
3. [Exponential's licensing facts](#exponentials-licensing-facts)
4. [Custom work under the GPL](#custom-work-under-the-gpl)
5. [Pitfalls](#pitfalls)

## Why it matters for a public body

- **No license fee** for the software itself, so costs move to services: building, hosting, support, training.
- **Freedom to change supplier**: anyone may run, study, change and pass on the code.
- **Inspection**: auditors and security teams can read the source.
- **Reuse**: what one body pays to have built can, if published, be used by others.

The [Open Source Definition](https://opensource.org/osd) lists the criteria an open source license meets; ask for a
license that meets it rather than for "open source" as a label.

## Requirement wording

| Id | Requirement |
|---|---|
| OSS-01 | The core software is licensed under a license that meets the Open Source Definition; name the license. |
| OSS-02 | The complete source code of the delivered system, including extensions, designs and integration code, is delivered to the customer in a version control repository at each release and at the end of the contract. |
| OSS-03 | List every third-party component with its license; no component may restrict the customer's rights under OSS-01. |
| OSS-04 | Custom work paid for under this contract is licensed to the customer under <license>; the customer may publish it. |
| OSS-05 | The customer may have the system maintained by any other party without the supplier's consent. |
| OSS-06 | Documentation delivered is under a license allowing the customer to copy and adapt it. |
| OSS-07 | No part of the delivered system depends on a proprietary component or service that the customer cannot replace; list any that exist. |

## Exponential's licensing facts

| Topic | Fact | Source |
|---|---|---|
| Software license | GNU GPL version 2 or (at your option) any later version; `"license": "GPL-2.0-or-later"` in `composer.json` | [Licensing questions](../../../project/licensing-faq.md), [GPL v2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) |
| License fee | none; you may charge for copies, installation, support and updates | [Licensing questions](../../../project/licensing-faq.md) |
| Network use | Visiting a website is not distribution; nothing obliges the operator to publish the code it runs | [Licensing questions](../../../project/licensing-faq.md) |
| Source | Public repositories on GitHub under `se7enxweb`, installable with Composer | [Getting the code](../../../../install/03-getting-the-code.md) |
| Documentation | GNU Free Documentation License 1.3 or later | [The license of this documentation](../../../../README-LICENSE.md), [GFDL 1.3](https://www.gnu.org/licenses/fdl-1.3.html) |
| Copyright | "7x & Exponential Foundation" and eZ Systems AS for 1999 to 2014, plus contributors; kernel contributors give up control of their contributions under the CLA ([CONTRIBUTING.md](../../../../../../../CONTRIBUTING.md#copyright-and-control-of-your-contribution-cla)) | [Licensing questions](../../../project/licensing-faq.md) |
| Extension licenses | Each extension states its license in `ezinfo.php` and `extension.xml` | [Source control and releases](../../../../standards/source-control-and-releases.md), [Package licenses and versions](../../../../features/6.0/package-licenses-and-versions.md) |
| Third-party services | Some extensions call outside services when activated (analytics, CAPTCHA, social sign-in) | [Privacy standards](../../../../standards/privacy.md) |

## Custom work under the GPL

The project's licensing FAQ explains that extensions and designs are derivative works, so **if they are
distributed** they are distributed under the GPL version 2 or later. An agency building a site for a client acts as
the client's agent, which the FAQ says is not distribution. Two consequences for a tender:

- You can require the source of custom work (OSS-02) independently of the GPL; the GPL alone does not oblige a
  supplier who never distributes to you to hand over source in a usable form. Write it in.
- If you want to publish the custom work for others, the GPL already allows that once you have received it;
  OSS-04 makes the intent explicit.

See also the template [Intellectual property of custom work](../tender-templates/intellectual-property-of-custom-work-clause.md).

## Pitfalls

- Accepting "open core": an open base with closed extensions you depend on. OSS-07 catches it.
- Forgetting fonts, images and JavaScript libraries in the design; they have licenses too.
- Assuming documentation has the same license as the code. In Exponential it is the GFDL, not the GPL.

## Related pages
- [Requirements for a public-sector CMS](README.md)
- [Tender template: open source license and source availability](../tender-templates/open-source-license-and-source-availability-clause.md)
- [Exit and portability requirements](exit-and-portability-requirements.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
