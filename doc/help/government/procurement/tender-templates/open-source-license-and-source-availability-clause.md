---
title: "Tender template: open source license and source availability clause"
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 70
summary: Clause text requiring an open source license for the delivered system, delivery of the complete source in a version control repository, a list of components and licenses, and freedom to have the system maintained by anyone - with Exponential's license facts.
keywords: open source clause, license, GPL, GPL-2.0-or-later, source code delivery, repository, escrow, component list, SBOM, third-party licenses, Open Source Definition, tender template
type: help
---
# Tender template: open source license and source availability clause

This page is for procurement officers and legal advisers who want the delivered system to be open source in fact,
not only in name: the license allows the freedoms, the customer has the source, and nobody else's consent is needed
to maintain it. Background: [Open source and licensing requirements](../requirements/open-source-and-licensing-requirements.md).

**Template** - adapt to your procurement rules; not legal advice

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [The clause](#the-clause)
2. [Exponential's license facts for the bidder's answer](#exponentials-license-facts-for-the-bidders-answer)
3. [The component list](#the-component-list)
4. [Notes for the evaluator](#notes-for-the-evaluator)

## The clause

```text
L. LICENSE AND SOURCE CODE

L.1 License. The software on which the system is built shall be licensed under a
    license that meets the Open Source Definition of the Open Source Initiative.
    The Supplier shall name the license of every component.

L.2 No license fees. No license fee is payable for the software, unless listed in
    the pricing schedule with the component, its license and the reason.

L.3 Source delivery. The Supplier shall deliver the complete source code of the
    system as run in production - core, extensions, designs, templates,
    stylesheets, scripts in their unminified form, build files, configuration
    templates without secrets, and deployment scripts - in a version control
    repository with full history, controlled by the Customer, at <each release /
    every <interval>> and at the end of the contract.

L.4 Reproducibility. The delivery under L.3 shall allow a competent third party to
    build and install the system using only the repository and the documentation.
    The Supplier shall demonstrate this <once / at acceptance>.

L.5 Components. The Supplier shall maintain a list of every third-party component
    with its version, license and source location, and update it with each release.

L.6 Freedom to maintain. The Customer may have the system maintained, changed or
    hosted by any party, without the Supplier's consent and without fees to the
    Supplier.

L.7 Contributions. [The Supplier shall offer fixes of general interest to the
    maintainers of the software under their license.]
```

## Exponential's license facts for the bidder's answer

| Point | Fact | Source |
|---|---|---|
| L.1 | GNU GPL version 2 or any later version (`GPL-2.0-or-later`) | [Licensing questions](../../../project/licensing-faq.md), [GPL v2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) |
| L.1 | Meets the [Open Source Definition](https://opensource.org/osd) as a GPL license | |
| L.2 | No license fee; charges are for copies, installation, support and updates | [Licensing questions](../../../project/licensing-faq.md) |
| L.3 | Code is public on GitHub under `se7enxweb` and installed with Composer or Git clones with full history | [Getting the code](../../../../install/03-getting-the-code.md), [Develop with Git](../../../develop-with-git.md) |
| L.3 | For JavaScript and CSS, "source code" means the unminified file or the original it was built from | [Licensing questions](../../../project/licensing-faq.md) |
| L.5 | Extensions state version and license in `ezinfo.php` and `extension.xml`; the about page lists them | [About and package pages](../../../../features/6.0/about-and-package-pages.md) |
| L.7 | How to contribute an extension back | [Contribute extensions back](../../../contribute-extensions-back.md) |

## The component list

A practical L.5 list for an Exponential site has these sources:

- `composer.json` and `composer.lock` of the project (PHP packages and their declared licenses);
- the `extension/` directory, with each extension's `extension.xml`;
- the design's own fonts, icons and images, whose licenses the designer must state;
- JavaScript libraries shipped by the design.

**Setup > Updates and packages** and the about page in the administration (`/ezinfo/about`) show installed packages
and extension versions.

## Notes for the evaluator

- L.3 is the clause that matters in practice. A license gives rights; the repository gives the ability.
- Ask the bidder whether any part of the proposal is closed; score honestly declared closed parts better than
  undeclared ones found later.

## Related pages
- [Tender templates](README.md)
- [Intellectual property of custom work clause](intellectual-property-of-custom-work-clause.md)
- [Warranty of no lock-in clause](warranty-of-no-lock-in-clause.md)
- [The license of this documentation](../../../../README-LICENSE.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
