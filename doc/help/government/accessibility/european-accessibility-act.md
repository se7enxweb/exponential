---
title: The European Accessibility Act and Exponential
audience: government, project-manager, admin
category: public-sector
order: 50
summary: For public bodies and their suppliers in the EU - how the European Accessibility Act (EU) 2019/882 relates to a site run with Exponential, when it may matter to a public body, and what evidence the project can and cannot provide.
keywords: European Accessibility Act, EAA, 2019/882, EN 301 549, products and services, e-commerce, public sector, supplier, accessibility requirements, EU
type: help
---
# The European Accessibility Act and Exponential

This page is for public bodies in the European Union, their in-house digital teams and the suppliers who build and
run services for them. It explains, in general terms, where the European Accessibility Act can touch a project run
with Exponential, and what the project's own accessibility statement offers as evidence.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What the act is](#what-the-act-is)
2. [When it may concern a public body](#when-it-may-concern-a-public-body)
3. [What Exponential can show](#what-exponential-can-show)
4. [Practical steps](#practical-steps)
5. [Related pages](#related-pages)

## What the act is

Directive (EU) 2019/882 on the accessibility requirements for products and services, known as the European
Accessibility Act (official text: <https://eur-lex.europa.eu/eli/dir/2019/882/oj>), sets accessibility requirements
for a list of products and services placed on the EU market, transposed into national law by each member state.
The project's statement names EN 301 549 as its reference
([Accessibility standards: EN 301 549](../../../standards/accessibility.md#en-301-549)).

Public sector websites as such are covered by the Web Accessibility Directive (see "The EU Web Accessibility
Directive and Exponential" in this section). The act addresses economic operators and the products and services it
lists.

## When it may concern a public body

Ask your legal counsel whether any of these apply:

- your organization offers a service of a kind the act lists (for example an online shop for publications, tickets
  or permits run as a commercial service), possibly through a company it owns;
- a supplier builds or runs such a service for you, and you want the contract to require the act's level;
- your national transposition extends the requirements to more organizations or services.

If none applies, the act still matters as a benchmark in procurement: many tenders now ask suppliers for
EN 301 549 evidence regardless of the legal basis.

## What Exponential can show

| Question | Honest answer from the project's statement |
|---|---|
| Is the editing tool tested against WCAG? | Yes: admin4 and admin4l against WCAG 2.2 AA; status **Partly complies** |
| EN 301 549 conformance report? | No; status **Unverified** |
| Authoring tool requirements (EN 301 549 clause 11.8)? | Not assessed |
| VPAT or Accessibility Conformance Report? | No |
| Shop extension accessibility? | Extensions shipped as packages (shop included) are reviewed one by one; there is no overall statement |

The shop features (basket, checkout, orders) are described in
[Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md). Their public templates
are part of your design and need the same audit as every other page.

## Practical steps

1. **Classify** each online service you run: covered by the Web Accessibility Directive, by the act, by both, or by
   neither, with your legal counsel.
2. **Audit the service flow end to end**: product or service pages, basket, sign-in, checkout, confirmation mail,
   error pages. Use the routine in
   [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md).
3. **Write the evidence** your counsel asks for. Exponential provides none ready-made for the act; see "Preparing an
   Accessibility Conformance Report for your site" in this section for a format.
4. **Put it in the contract**: suppliers deliver templates that pass the audit, fix findings within agreed times and
   keep the evidence current (see "Procurement clauses for accessibility").

## Related pages

- [Accessibility standards](../../../standards/accessibility.md)
- [Shop overview](../../creators/website-creators/shop-overview.md)
- [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
