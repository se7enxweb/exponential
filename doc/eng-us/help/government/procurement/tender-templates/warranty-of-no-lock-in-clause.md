---
title: "Tender template: warranty of no lock-in clause"
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 170
summary: Clause text in which a supplier warrants that the delivered Exponential site creates no technical, contractual or commercial lock-in - no closed components, no proprietary formats, no exclusive access, no exit fees - with the checks that test each promise.
keywords: lock-in, warranty, vendor lock-in, portability, interoperability, closed components, proprietary formats, exit fees, exclusive access, open standards, tender template
type: help
---
# Tender template: warranty of no lock-in clause

This page is for procurement officers who want one clause that captures the purpose of buying open source: the
freedom to change supplier. Lock-in rarely comes from the license of the core software; it comes from closed
extensions, undocumented integrations, missing source, exclusive accounts and exit fees. The warranty below lists
these one by one, so that each can be checked.

**Template** - adapt to your procurement rules; not legal advice

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [Where lock-in comes from](#where-lock-in-comes-from)
2. [The clause](#the-clause)
3. [How to check each promise](#how-to-check-each-promise)
4. [Notes for the evaluator](#notes-for-the-evaluator)

## Where lock-in comes from

| Kind | Example | Countered by |
|---|---|---|
| Technical | a closed extension the site depends on; data in an undocumented format | W.1, W.2 |
| Knowledge | no documentation; configuration known only to the supplier | W.3 |
| Access | domain, certificates, repository or hosting accounts held by the supplier | W.4 |
| Commercial | fees for exports, for access to own data, or for another party to maintain the system | W.5 |
| Contractual | exclusivity; consent needed for changes by others | W.6 |

## The clause

```text
W. WARRANTY OF NO LOCK-IN

The Supplier warrants that, for the duration of the contract and after it:

W.1 Components. The delivered system contains no component that the Customer
    cannot use, change and have maintained by another party under an open source
    license, except those listed in <annex> with their license, purpose and a
    named replacement.

W.2 Formats and interfaces. All content and data can be exported by the Customer
    in open, documented formats, and all interfaces built under the contract are
    documented.

W.3 Knowledge. The documentation delivered under <documentation clause> is
    sufficient for a competent third party to operate, change and rebuild the
    system.

W.4 Accounts and assets. Domain names, certificates, source repositories,
    hosting and third-party service accounts used for the Customer's system are
    registered to the Customer, or transferred to it on request within <time>.

W.5 No fees for freedom. No fee is charged for exports, for the return package,
    for access to the Customer's own data, or for another party maintaining the
    system.

W.6 No exclusivity. Nothing in the contract or in the Supplier's licenses
    requires the Supplier's consent for the Customer or a third party to change,
    host or maintain the system.

W.7 Remedy. If a warranty in this clause is found to be untrue, the Supplier
    remedies it at its own cost within <time> [and <other remedy>].
```

## How to check each promise

| Promise | Check with Exponential |
|---|---|
| W.1 | Compare `ActiveExtensions[]` and `composer.json` with the component list; extension licenses on the about page (`/ezinfo/about`) ([About and package pages](../../../../features/6.0/about-and-package-pages.md)). The kernel is GPL version 2 or later ([Licensing questions](../../../project/licensing-faq.md)) |
| W.2 | Run a site archive export and open the manifest ([Migrating away from Exponential](../../../../install/migration/leaving-exponential.md)); REST API for live access ([REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md)) |
| W.3 | Have your own staff or a third party follow the deployment documentation on a test machine; `./console exp:install` gives a clean base in one command ([Installing Exponential in one command](../../../../features/6.0/install-in-one-command.md)) |
| W.4 | Ask for the list of accounts and their registrant |
| W.5, W.6 | Read the pricing schedule and license terms |

Public facts that help: the code is public on GitHub and Packagist, any company may support it, and the project has
partners as well as 7x ([Getting support](../../../project/getting-support.md),
[Implementation partners](../../../partners/implementation-partners.md)).

## Notes for the evaluator

- A bidder may honestly list a closed component under W.1; score the replacement plan rather than excluding the bid.
- Run W.2 during the proof of concept, not after signing.

## Related pages
- [Tender templates](README.md)
- [Exit and data return clause](exit-and-data-return-clause.md)
- [Open source license and source availability clause](open-source-license-and-source-availability-clause.md)
- [Exit and portability requirements](../requirements/exit-and-portability-requirements.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
