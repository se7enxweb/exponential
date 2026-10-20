---
title: Write a security requirement for a tender
audience: government, project-manager, admin
category: public-sector
order: 30
summary: Step by step, write security requirements for a tender for an Exponential site - baseline hardening, the defaults to change, patching, audit, testing and reporting - that a bidder can answer and you can verify.
keywords: security requirement, tender, hardening, OWASP, ASVS, two-factor, lockout, patching, audit trail, penetration test, vulnerability, procurement task
type: help
---
# Write a security requirement for a tender

**Who:** the procurement officer with the security officer. **When:** while drafting the requirements. The facts come
from [Security standards](../../../../standards/security.md) and [Security hardening](../../../security-hardening.md).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Example](#example)
4. [Result](#result)
5. [Pitfalls](#pitfalls)

## Before you start

- Read the summary of [Security standards](../../../../standards/security.md#summary): OWASP Top 10 **Partly complies**,
  ASVS **Unverified**, CSP **Partly complies**.
- Know your organization's security framework (for example the [NIST Cybersecurity Framework](https://www.nist.gov/cyberframework)
  or ISO/IEC 27001, [iso.org](https://www.iso.org/standard/27001)).

## Steps

1. **Require a hardening baseline** by reference: the steps of [13. Security hardening for production](../../../../install/13-security-hardening.md)
   and [Security hardening](../../../security-hardening.md), with any deviation documented.
2. **Name the defaults you want changed**, because they ship off or open
   ([Security standards](../../../../standards/security.md#known-gaps)):
   - two-step sign-in for administrators ([Two-step sign-in](../../../../guides/two-factor-login.md));
   - account lockout or rate limiting in front of the site;
   - minimum password length (ASVS level 1 asks 12; the default is 10);
   - HSTS once the site is HTTPS-only;
   - Content Security Policy enforcement on the public site, where the design allows.
3. **Require patching**: released security fixes applied within a time you set
   ([Security updates and patching cadence](../../procurement/lifecycle/security-updates-and-patching-cadence.md)).
4. **Require logging**: audit trail on, alerts to named people, chain verified regularly
   ([Security and audit](../../../../guides/security-and-audit.md)).
5. **Require testing**: if your rules ask for it, a penetration test of the delivered site by an independent tester
   before go-live; the project publishes none.
6. **Require incident duties**: notification time and cooperation ([Incident readiness](../../checklists/incident-readiness.md)).

## Example

```text
SEC-01  The Supplier shall harden the Site according to chapter 13 of the Exponential installation
        book and document every deviation.
SEC-02  Administrator accounts shall use two-step sign-in. Failed sign-ins shall be limited by
        <account lockout | a request filter>. Minimum password length: <n>.
SEC-03  Released security fixes of Exponential and its extensions shall be applied within <time>.
SEC-04  The audit trail shall be enabled, its alerts sent to <role>, its chain verified every <period>.
SEC-05  Before acceptance, <an independent tester> shall test the Site against <scope/method>;
        findings rated <level> or higher shall be fixed before acceptance.
```

## Result

Requirements that a bidder answers with a plan and you verify at acceptance with the
[Security checklist](../../checklists/security-checklist.md).

## Pitfalls

- "The system shall be secure": nobody can test it.
- Requiring an ASVS certification of the product, which does not exist; require an assessment of your site instead.
- Forgetting that fixes arrive only as new releases: without an update obligation, no fix reaches you.

## Related pages

- [Procurement and training tasks](README.md)
- [Security requirements](../../procurement/requirements/security-requirements.md)
- [Security clause](../../procurement/tender-templates/security-clause.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
