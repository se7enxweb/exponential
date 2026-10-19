---
title: What Exponential can and cannot evidence
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 160
summary: An honest list, taken from the standards pages, of what the Exponential repository can prove to a public-sector evaluator and what it cannot - no VPAT, no ASVS assessment, no external audit, no cookie banner, no accessibility statement template - and how to handle each gap in a tender.
keywords: gaps, evidence, VPAT, ACR, ASVS, penetration test, certification, conformance, cookie banner, accessibility statement, security.txt, signed commits, limitations, honesty, tender
type: help
---
# What Exponential can and cannot evidence

This page is for evaluators who need to know what they will and will not find, and for bidders who want to answer
honestly. Every item comes from the "Known gaps" and status lines of the
[standards pages](../../../../standards/README.md). A gap is not a reason to exclude a product by itself: many gaps
are closed by configuration or by the supplier's work. But a bid that claims what the repository cannot show should
lose points.

## Contents
1. [What the repository can evidence](#what-the-repository-can-evidence)
2. [What it cannot evidence](#what-it-cannot-evidence)
3. [How to treat each gap in a tender](#how-to-treat-each-gap-in-a-tender)
4. [Questions that reveal overclaiming](#questions-that-reveal-overclaiming)

## What the repository can evidence

| Area | Evidence you can check yourself |
|---|---|
| Accessibility of the admin4 and admin4l administration | design tests in `tests/tests/design/`, run in CI; documented criteria per WCAG area ([Accessibility standards](../../../../standards/accessibility.md)) |
| Security mitigations per OWASP Top 10 category | settings, classes and tests named per row ([Security standards](../../../../standards/security.md)) |
| A tamper-evident audit trail | `./console exp:audit verify` ([Audit trail](../../../../features/6.0/audit-trail.md)) |
| Privacy features (consent log, erasure on account removal, log masking, retention) | [Privacy standards](../../../../standards/privacy.md) |
| Unicode, locales, right-to-left | [Internationalization standards](../../../../standards/internationalization.md) |
| Open source license | `LICENSE`, `COPYRIGHT.md`, `composer.json` ([Licensing questions](../../../project/licensing-faq.md)) |
| Release policy and supported versions | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md), [Source control and releases](../../../../standards/source-control-and-releases.md) |
| Export of all content in open formats | [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md) |
| Automated tests and CI | [Testing and continuous integration](../../../../standards/testing-and-ci.md) |

## What it cannot evidence

| Gap | Source |
|---|---|
| **No VPAT or Accessibility Conformance Report**; Section 508 status Unverified | [Accessibility standards](../../../../standards/accessibility.md) |
| **No evaluation against EN 301 549**; status Unverified; authoring-tool clause 11.8 not reviewed | same |
| **Public site designs not audited** for accessibility; online editor not audited | same |
| **No accessibility statement template** for sites | same |
| **No automated accessibility engine in CI**; axe runs are manual | same |
| **No OWASP ASVS assessment**; status Unverified; default password minimum (10) below ASVS level 1 (12) | [Security standards](../../../../standards/security.md) |
| **No external security assessment** documented ("Complies" would need one) | same |
| **Account lockout off by default**; two-step sign-in opt-in | same |
| **No dependency vulnerability scan in CI** | same |
| **No `security.txt`** shipped | same |
| **CSP not enforced by default** (report-only on administration, off on sites) | same |
| **No cookie consent banner** | [Privacy standards](../../../../standards/privacy.md) |
| **No single "export all my data" or "delete me"** function | same |
| **Kernel logs without an age limit** by default | same |
| **No tagged PDF** (PDF/UA Does not comply) | [Documentation and publishing standards](../../../../standards/documentation-and-publishing.md) |
| **No plural-form support** in interface translations | [Internationalization standards](../../../../standards/internationalization.md) |
| **No OpenAPI description** of the kernel REST API | [APIs and protocols standards](../../../../standards/apis-and-protocols.md) |
| **Unsigned commits and tags**; no branch protection on `main` | [Source control and releases](../../../../standards/source-control-and-releases.md) |
| **No published code coverage** | [Testing and continuous integration](../../../../standards/testing-and-ci.md) |
| **No published SLA** from the project | [SLA options](../../../hosting/business/sla-options.md) |

## How to treat each gap in a tender

| Kind of gap | Treatment |
|---|---|
| Closed by configuration (lockout, password length, CSP enforcement, HSTS, `security.txt`) | Make the configuration a delivery requirement; check it at acceptance. |
| Closed by the supplier's work (public design accessibility, accessibility statement, cookie consent) | Put the work and its evidence (for example a WCAG-EM evaluation) in the scope. |
| Closed by an external assessment (ASVS, penetration test, EN 301 549 evaluation) | Budget it separately; you choose the assessor. |
| Closed by your organization (records schedule, privacy notice) | Keep it in your own project plan. |
| Not closable in the contract period (signed commits upstream, plural forms) | Note the risk; ask the bidder for its view; score accordingly. |

## Questions that reveal overclaiming

- "Send the VPAT for the version you offer." The honest answer for Exponential is that none exists.
- "Which ASVS level was assessed, by whom, when?" None was.
- "Which settings will differ from the shipped defaults on our site?" A bidder who has none to name has not planned
  the hardening.
- "Show the accessibility evaluation of the public design you propose." Shipped public designs have none.

## Related pages
- [Requirements for a public-sector CMS](README.md)
- [Mapping requirements to evidence](mapping-requirements-to-evidence.md)
- [Accessibility requirements](accessibility-requirements.md)
- [Security requirements](security-requirements.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
