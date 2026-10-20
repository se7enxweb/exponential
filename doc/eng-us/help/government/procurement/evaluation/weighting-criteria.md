---
title: Weighting the evaluation criteria
audience: government, project-manager
category: public-sector
order: 40
summary: How a public body decides the weights of its CMS evaluation criteria - quality versus price, mandatory versus scored requirements, and which criteria matter more for public websites - with a worksheet to record the reasons. No weights are prescribed.
keywords: weighting, criteria weights, award criteria, quality price ratio, most economically advantageous tender, MEAT, mandatory requirements, scored requirements, evaluation model
type: help
---
# Weighting the evaluation criteria

This page is for the project manager and procurement officer who must publish the award criteria and their weights
before bids arrive. It explains the decisions behind a weighting model for a content management system and gives a
worksheet that records why each weight was chosen. It prescribes no numbers: the weights are your policy decision
and must follow your procurement rules.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [Mandatory or scored](#mandatory-or-scored)
2. [Quality and price](#quality-and-price)
3. [Criteria that public websites usually weigh](#criteria-that-public-websites-usually-weigh)
4. [What the documentation can evidence and what it cannot](#what-the-documentation-can-evidence-and-what-it-cannot)
5. [A weighting worksheet](#a-weighting-worksheet)
6. [Common mistakes](#common-mistakes)

## Mandatory or scored

Decide for every requirement whether it is **mandatory** (a bid that does not meet it is excluded) or **scored**
(better answers earn more points).

| Make it mandatory when | Make it scored when |
|---|---|
| the law requires it of you (for example accessibility of public websites in many jurisdictions) | several levels of quality are possible and useful |
| without it the system cannot be used at all | the market may not meet it fully yet |
| it can be checked with a yes or no | you want to compare how well bidders meet it |

A mandatory requirement that nobody can evidence excludes every bidder. An example: "the product has a published
VPAT" would exclude Exponential, because there is none ([Accessibility standards](../../../../standards/accessibility.md#section-508)).
"The bidder delivers an Accessibility Conformance Report for the delivered site before acceptance" can be met by a
bidder that does the work, whatever the product.

## Quality and price

- Many public bodies award to the most economically advantageous tender: a mix of quality and price. In the EU,
  [Directive 2014/24/EU on public procurement](https://eur-lex.europa.eu/eli/dir/2014/24/oj) sets this framework; check your national rules for the exact method.
- The share of price versus quality is a policy choice. Write down why you chose it.
- Compare price as **total cost of ownership** over the contract period, not as license price. Exponential has no
  license fee under the GNU GPL ([Licensing questions](../../../project/licensing-faq.md)), but implementation, hosting,
  support and training still cost money; see the folder total-cost-of-ownership.

## Criteria that public websites usually weigh

| Criterion | Why it matters in the public sector | Exponential evidence to expect in a bid |
|---|---|---|
| Accessibility | public bodies are often legally required to publish accessible sites | [Accessibility standards](../../../../standards/accessibility.md) plus the bidder's plan for the public design |
| Security | public sites are targets; security duties often apply | [Security standards](../../../../standards/security.md), [Security hardening](../../../security-hardening.md) |
| Privacy | personal data of citizens | [Privacy standards](../../../../standards/privacy.md) |
| Languages | official or minority languages, right-to-left scripts | [Internationalization](../../../../standards/internationalization.md), [Right to left](../../../../features/6.0/right-to-left.md) |
| Editorial workflow | approvals, versions, accountability | [Workflows](../../../../guides/workflows.md), [Audit trail](../../../../features/6.0/audit-trail.md) |
| Hosting freedom and data location | where data is stored | [Hosting: choosing a model](../../../hosting/models/choosing-a-hosting-model.md) |
| Exit and portability | the ability to change supplier | [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md) |
| Support and continuity | a site lives for years | [Getting support](../../../project/getting-support.md), `SECURITY.md` |
| Training | editors across departments | [Course catalogue](../../../trainers/course-catalogue.md) |

## What the documentation can evidence and what it cannot

Weight criteria knowing which ones a bidder can prove:

- **Evidenced by the repository**: features, settings, tests, release policy, export tools, the status of each
  standard (Complies, Partly complies, Unverified) as defined in [Standards: how to read the status](../../../../standards/README.md#how-to-read-the-status).
- **Not evidenced by the repository**: a VPAT or Accessibility Conformance Report, an OWASP ASVS assessment, an
  external penetration test, an EN 301 549 evaluation, published SLAs of the project ([SLA options](../../../hosting/business/sla-options.md)
  states that the project publishes none). These must come from the bidder, or be part of the contract work.

## A weighting worksheet

```text
Criterion                    Mandatory parts                 Scored parts          Weight   Reason for the weight
---------------------------  ------------------------------  --------------------  -------  ---------------------
Accessibility                <e.g. ACR for delivered site>   <...>                 <...>    <policy, law, users>
Security                     <...>                           <...>                 <...>    <...>
Privacy                      <...>                           <...>                 <...>    <...>
Languages                    <...>                           <...>                 <...>    <...>
Editorial functions          <...>                           <...>                 <...>    <...>
Hosting and data location    <...>                           <...>                 <...>    <...>
Support and maintenance      <...>                           <...>                 <...>    <...>
Training and documentation   <...>                           <...>                 <...>    <...>
Exit and portability         <...>                           <...>                 <...>    <...>
Price (total cost)           -                               formula: <...>        <...>    <...>
Approved by: <name, role, date>
```

## Common mistakes

- Weights that do not add up, or sub-criteria that were not published.
- A heavy weight on a criterion nobody can evidence: evaluators then score claims.
- Weighing license price alone, which favors products with no license fee without counting the work they need, or
  the reverse.
- Forgetting exit: a low price with high switching costs later.

## Related pages
- [Evaluation: overview](README.md)
- [Procurement: overview](../README.md)
- [Licensing questions](../../../project/licensing-faq.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
