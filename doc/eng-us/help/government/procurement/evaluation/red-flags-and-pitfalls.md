---
title: Red flags and pitfalls in CMS evaluations
audience: government, project-manager, sales-marketing, partner
category: public-sector
order: 140
summary: The claims and bid patterns that should make an evaluation panel stop and ask for evidence when a bid is based on Exponential, and the mistakes panels make themselves - each with the documentation that shows the true position.
keywords: red flags, warning signs, pitfalls, unsupported claims, overclaiming, vendor lock-in, evaluation mistakes, due diligence, accessibility claims, security claims
type: help
---
# Red flags and pitfalls in CMS evaluations

This page is for every member of an evaluation panel. It lists bid statements about Exponential that go beyond what
the product's documentation says, patterns that hide future costs or lock-in, and mistakes evaluation panels make.
Each item links the page that shows the documented position, so you can ask the bidder a precise question.

## Contents
1. [Claims that contradict the documentation](#claims-that-contradict-the-documentation)
2. [Patterns that hide cost or lock-in](#patterns-that-hide-cost-or-lock-in)
3. [Data and privacy warning signs](#data-and-privacy-warning-signs)
4. [Mistakes panels make](#mistakes-panels-make)
5. [How to respond to a red flag](#how-to-respond-to-a-red-flag)

## Claims that contradict the documentation

| Claim in a bid | Documented position |
|---|---|
| "Exponential is WCAG 2.2 AA compliant" | admin4/admin4l "Partly complies"; public designs not audited ([Accessibility standards](../../../../standards/accessibility.md)) |
| "A VPAT / Accessibility Conformance Report is available for Exponential" | none exists; Section 508 and EN 301 549 are "Unverified" |
| "Certified against OWASP ASVS" | no ASVS assessment was made: "Unverified" ([Security standards](../../../../standards/security.md#owasp-asvs-403)) |
| "Brute-force protection is built in and on" | account lockout is off by default; brute force is detected by audit alert rules |
| "A strict Content Security Policy protects the site" | report-only on administration, none on sites by default |
| "GDPR compliant out of the box" | features provided; compliance is the organization's job, and there is no cookie banner ([Privacy standards](../../../../standards/privacy.md)) |
| "Guaranteed response times from the Exponential project" | the project publishes no SLA; only a first answer to private security reports within one business day ([SLA options](../../../hosting/business/sla-options.md)) |
| "Our version is the official long-term support release" | `SECURITY.md` names the 6.0.x line as maintained; fixes come in the next 6.0.x release |
| "Built-in GIS / mapping" | no GIS feature is documented; the [ezgmaplocation](../../../../features/6.0/extensions/ezgmaplocation.md) extension adds a map location datatype that uses Google Maps, a third-party service |

A bidder may still deliver what the claim promises through its own work. Ask it to restate the claim as a
commitment for **your** site, with how it will be evidenced at acceptance.

## Patterns that hide cost or lock-in

- **A private fork of the kernel** that only the bidder maintains. Ask whether changes go back upstream
  ([Contributing code back](../../../partners/contributing-code-back.md)); a private fork makes every upgrade the
  bidder's monopoly.
- **Closed-source extensions** delivered without source. Under the GPL, whoever receives an extension also receives
  its source ([Licensing questions](../../../project/licensing-faq.md)); require source delivery in the contract.
- **"License" or "subscription" fees for the core software.** The software has no license fee. Fees for support,
  hosting or services are normal; make sure the bid says which is which.
- **Hosting tied to the supplier with no export.** The data can be exported in open formats
  ([Leaving Exponential](../../../../install/migration/leaving-exponential.md)); ask the bidder to demonstrate it.
- **Upgrades priced as new projects.** Ask how the bidder keeps sites current
  ([Keeping customers current](../../../partners/keeping-customers-current.md)) and what a 6.0.x update costs.
- **An old line.** A bid on 5.x or older offers an unsupported version (`SECURITY.md`).

## Data and privacy warning signs

- A demo on a copy of a real site with real personal data.
- Third-party analytics, captcha or social sign-in enabled without a consent plan: the
  [Privacy standards](../../../../standards/privacy.md#third-party-services-in-shipped-extensions) page lists which
  shipped extensions send data to third parties when activated.
- No answer to "who processes our personal data, where, and under which agreement?"
- Credentials sent by e-mail during the tender.

## Mistakes panels make

- Scoring presentation quality instead of requirements.
- Accepting "yes" in a compliance matrix without a source.
- Writing a mandatory requirement nobody can meet (for example a product VPAT), and then waiving it unevenly.
- Comparing license price only, ignoring hosting, staff, training and exit costs.
- Forgetting the editors: no editor sits on the panel or tries the system.
- Not testing the exit: export is checked only after the contract is signed.

## How to respond to a red flag

1. Write a clarification question that quotes the claim and the documentation page.
2. Ask for evidence: a document, a test you can repeat, or a contractual commitment with an acceptance test.
3. Score the answer, not the original claim.
4. Record the exchange in the evaluation file.

## Related pages
- [Evaluation: overview](README.md)
- [Procurement: overview](../README.md)
- [Questions to ask during a CMS demo](questions-to-ask-in-a-demo.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
