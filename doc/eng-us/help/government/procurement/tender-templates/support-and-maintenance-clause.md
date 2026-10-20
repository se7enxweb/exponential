---
title: "Tender template: support and maintenance clause"
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 110
summary: Clause text for support and maintenance of an Exponential site - scope, channels, upgrades within the maintained line, escalation to the upstream project, warranty on custom work, knowledge transfer and reporting - with blanks to fill.
keywords: support clause, maintenance, upgrades, escalation, upstream, partner, 7x, warranty, defects, help desk, knowledge transfer, tender template
type: help
---
# Tender template: support and maintenance clause

This page is for procurement officers who contract ongoing support and maintenance of an Exponential site. It
complements the [Service levels clause](service-levels-clause.md), which sets times; this clause sets scope and
duties. Background: [Support and maintenance requirements](../requirements/support-and-maintenance-requirements.md).

**Template** - adapt to your procurement rules; not legal advice

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [The clause](#the-clause)
2. [Scope table](#scope-table)
3. [Escalation beyond the supplier](#escalation-beyond-the-supplier)
4. [Notes for the evaluator](#notes-for-the-evaluator)

## The clause

```text
M. SUPPORT AND MAINTENANCE

M.1 Scope. The Supplier supports and maintains the items in <scope table>: the
    Exponential software, the extensions, the design and templates, the
    integrations and the hosting environment [if hosted by the Supplier].

M.2 Channels. Support requests are made through <ticket system / e-mail / phone>
    by the Customer's named contacts. Severity 1 incidents may also be reported by
    <phone number role>.

M.3 Corrective maintenance. The Supplier corrects defects in the delivered system.
    Defects in Custom Work are corrected without charge for <period> after
    acceptance [and thereafter under <terms>].

M.4 Preventive maintenance. The Supplier applies releases of the maintained
    Exponential line, extension releases and platform updates as set out in
    <patching clause>, and reports their effect.

M.5 Upgrades. The Supplier proposes a yearly upgrade plan by <month>. Upgrades
    within the maintained line are included; changes to a new major line are
    quoted separately.

M.6 Upstream issues. Where a defect is in the software rather than the delivery,
    the Supplier reports it to the maintainers through the documented channel,
    provides a workaround where possible, and applies the fix when released.

M.7 Small changes. <hours / days> per <period> of change work are included, used
    on request and reported.

M.8 Knowledge. The Supplier keeps the system documentation current (see
    <documentation clause>) and records every change in the repository.

M.9 Staff. The Supplier names the support team and its qualifications, and informs
    the Customer of changes.

M.10 Reporting. Monthly report as set out in <service levels clause>.
```

## Scope table

| Item | In scope | Notes |
|---|---|---|
| Exponential kernel | yes | maintained 6.0 line ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) |
| Extensions in `ActiveExtensions[]` | <yes / listed ones> | list them; versions on the about page (`/ezinfo/about`) |
| Design, templates, stylesheets | yes | |
| Integrations | <list> | |
| Content | <no / editorial support hours> | editors' own work |
| Hosting platform | <yes if hosted> | see hosting clause |
| Editor questions ("how do I") | <yes / limited> | the in-product help answers many ([Help system](../../../../features/6.0/help-system.md)) |

## Escalation beyond the supplier

Name the path in the bid. Documented options, from [Getting support](../../../project/getting-support.md):

| Path | Commitment |
|---|---|
| Public issue tracker | none; open to everyone ([When something goes wrong](../../../when-something-goes-wrong.md)) |
| Community forums and chats | none |
| Partner support of the project | response times by partner level, as published ([Support levels and response times](../../../partners/support-levels-and-response-times.md)) |
| Paid support from 7x | agreed with 7x ([Who is 7x?](../../../who-is-7x.md)) |
| Security reports | private e-mail, first answer within one business day |

## Notes for the evaluator

- M.5 keeps the yearly upgrade from becoming a surprise invoice; budget for it (see the total cost of ownership pages
  of this procurement section).
- M.6 should name who opens the issue and who tests the fix; a bidder who answers "we wait for upstream" has no
  workaround plan.

## Related pages
- [Tender templates](README.md)
- [Service levels clause](service-levels-clause.md)
- [Vulnerability disclosure and patching clause](vulnerability-disclosure-and-patching-clause.md)
- [Keeping customers current (for partners)](../../../partners/keeping-customers-current.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
