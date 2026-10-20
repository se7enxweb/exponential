---
title: Evaluating security evidence in a CMS bid
audience: government, project-manager, admin, hosting
category: public-sector
order: 100
summary: How a security specialist on an evaluation panel judges a bid based on Exponential - the documented OWASP mapping, the defaults that need changing, the disclosure and release process, what is not evidenced (ASVS, penetration test, dependency scanning) and what the bidder and the host must add.
keywords: security evaluation, OWASP Top 10, ASVS, penetration test, security questionnaire, two-factor, audit trail, CSP, hardening, vulnerability disclosure, SECURITY.md, security.txt
type: help
---
# Evaluating security evidence in a CMS bid

This page is for the information security officer on an evaluation panel. It shows what the Exponential
documentation evidences, which defaults a public site should change, what is missing, and what to demand from the
bidder and the host. The facts come from [Security standards](../../../../standards/security.md),
[Security hardening](../../../security-hardening.md) and `SECURITY.md` in the repository root.

## Contents
1. [Three layers to evaluate](#three-layers-to-evaluate)
2. [What the product documentation evidences](#what-the-product-documentation-evidences)
3. [Defaults to check in the bid](#defaults-to-check-in-the-bid)
4. [What is not evidenced](#what-is-not-evidenced)
5. [What to demand from the bidder and the host](#what-to-demand-from-the-bidder-and-the-host)
6. [Tests in the proof of concept](#tests-in-the-proof-of-concept)

## Three layers to evaluate

| Layer | Responsible | Evidence |
|---|---|---|
| The software | the project, through releases | [Security standards](../../../../standards/security.md) |
| The configuration and custom code | the bidder | its hardening plan and code review |
| The hosting | the host (internal or external) | its operating procedures ([Hosting security](../../../hosting/security/README.md)) |

## What the product documentation evidences

| Area | Status | In short |
|---|---|---|
| OWASP Top 10 (2021) | **Partly complies** | mitigations named per category, three documented hardening passes |
| OWASP ASVS 4.0.3 | **Unverified** | no assessment; many level 1 controls exist |
| Content Security Policy Level 3 | **Partly complies** | per-request nonces and reporting; report-only on administration, none on sites by default |
| Logging and monitoring (A09) | **Complies** | tamper-evident audit trail on by default, alert rules, security log ([Audit trail](../../../../features/6.0/audit-trail.md)) |
| Coordinated disclosure | **Partly complies** | private reporting in `SECURITY.md`; no `security.txt` shipped |

Further facts an evaluator can rely on: passwords are stored with PHP's `password_hash()`, every form carries a CSRF
token, security headers are sent on every page, and published release tags are never changed
([Source control and releases](../../../../standards/source-control-and-releases.md#tags-are-permanent)).

## Defaults to check in the bid

| Default | As shipped | What a public site usually needs |
|---|---|---|
| Account lockout | off (`MaxNumberOfFailedLogin=0`) | a decision: lockout, or rate limiting in front ([request-shield](../../../security-hardening.md#a-shield-in-front-request-shield)) |
| Two-step sign-in | opt-in extension | on for every administrator and editor ([Two-step sign-in](../../../../guides/two-factor-login.md)) |
| Minimum password length | 10 | 12 or more if you follow ASVS level 1 (V2.1.1) |
| HSTS | empty | set once the whole site is HTTPS-only |
| Content Security Policy | report-only on admin, none on sites | enforce where the design allows ([Content Security Policy](../../../../features/6.0/content-security-policy.md)) |
| `security.txt` | not shipped | publish one following [RFC 9116](https://www.rfc-editor.org/rfc/rfc9116) |

Ask the bidder to state each of these settings for your site in the bid.

## What is not evidenced

- No external penetration test or security audit is published for the product.
- No OWASP ASVS assessment ([OWASP ASVS](https://owasp.org/www-project-application-security-verification-standard/)).
- No dependency vulnerability scan in CI; `composer audit` is recommended in the hardening guide.
- No prepared statements in the database layer; escaping is used, and extension code must follow the same rule.
- Output escaping in templates is not automatic.
- Commits and tags are not signed.
- The project publishes no service level agreement; the one response commitment is a first answer to a private
  security report within one business day (`SECURITY.md`).

## What to demand from the bidder and the host

1. A hardening plan based on [chapter 13 of the installation book](../../../../install/13-security-hardening.md)
   and the [hardening checklist](../../../security-hardening.md#checklist), with each item marked done or not.
2. A code review or test of the bidder's own templates and extensions (escaping with `wash`, SQL escaping).
3. A penetration test of the delivered site before go-live, by a party you accept.
4. A patch commitment: apply released Exponential security fixes within a time you set, and PHP and operating
   system updates within a time you set ([SLA options](../../../hosting/business/sla-options.md)).
5. Incident handling: who is called, how fast, and how the audit trail is preserved
   ([Compromised site runbook](../../../hosting/runbooks/compromised-site.md)).
6. A mapping to your own framework, for example the [NIST Cybersecurity Framework 2.0](https://www.nist.gov/cyberframework)
   or [ISO/IEC 27001](https://www.iso.org/standard/27001), if your policy requires one. The project provides none.

## Tests in the proof of concept

- Sign in with a wrong password several times and look for the audit entries.
- Run `./console exp:audit verify` to check the audit chain.
- Set up two-step sign-in for a test account.
- Read the response headers of a page (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`).
- Run **Setup > Upgrade check** to see the file consistency check.

## Related pages
- [Evaluation: overview](README.md)
- [Procurement: overview](../README.md)
- [Security standards](../../../../standards/security.md)
- [Security and audit guide](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
