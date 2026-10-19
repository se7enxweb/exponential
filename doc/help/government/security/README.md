---
title: Security for public bodies
audience: government, admin, project-manager, developer, hosting
category: public-sector
order: 10
summary: The security section of the Governments help - Exponential's documented security posture (OWASP Top 10, ASVS, CSP), hardening, the audit trail, two-step sign-in, CSP enforcement, request-shield, passwords and sessions, disclosure and security.txt, patching, NIS2, NIST CSF and ISO 27001 framing, penetration tests and incident response.
keywords: security, public sector, government, OWASP, ASVS, hardening, audit trail, two-step sign-in, MFA, CSP, request-shield, HSTS, password policy, lockout, security.txt, vulnerability disclosure, patching, NIS2, NIST CSF, ISO 27001, incident response
type: help
---
# Security for public bodies

This section is for information security officers, administrators, developers, hosting teams and project managers of
public bodies that run a site with Exponential. Every product statement comes from the repository: the project's
[Security standards](../../../standards/security.md) (whose status words "Partly complies", "Unverified" and, for
OWASP A09, "Complies" are kept as written), [Security hardening](../../security-hardening.md),
[Chapter 13 of the installation guide](../../../install/13-security-hardening.md), the audit guide and the feature pages.
Gaps are named where they exist: lockout off by default, two-step sign-in opt-in, CSP report-only on the administration
and off on sites, HSTS empty by default, no `security.txt` shipped, no dependency scan in the project's CI.

## Contents

1. [Reading order](#reading-order)
2. [Pages in this section](#pages-in-this-section)
3. [Related pages](#related-pages)

## Reading order

- **Security officers and project managers**: posture at a glance, the OWASP mappings, the framing pages (NIS2, NIST
  CSF, ISO 27001), go-live checklist, penetration tests, incident response.
- **Administrators**: hardening overview, then the pages on sign-in, sessions, headers, HTTPS, CSP, the audit trail,
  logs and backups.
- **Developers and hosting teams**: CSP plan, uploads, form tokens, proxies, file permissions, configuration managed by
  the deployment, request-shield.

## Pages in this section

| Page | Answers |
|---|---|
| [Exponential's security posture at a glance](security-posture-at-a-glance.md) | status by standard; on and off by default; known gaps |
| [Mapping the OWASP Top 10 to Exponential](owasp-top-10-mapping.md) | mitigations, your part and evidence per category |
| [Mapping OWASP ASVS to Exponential](owasp-asvs-mapping.md) | controls by chapter; the password length gap |
| [Hardening an Exponential site for a public body](hardening-overview.md) | the layers in order |
| [Go-live security checklist for public sites](go-live-security-checklist.md) | checklist with evidence to file |
| [The audit trail for public bodies](audit-trail-for-public-bodies.md) | what is recorded (and not), retention, who reads |
| [Audit alerts and their recipients](audit-alerts-and-recipients.md) | the seven shipped rules; routing mail |
| [Audit integrity and verification](audit-integrity-and-verification.md) | hash chains, checkpoints, verify, broken chains |
| [Forwarding the audit trail to a SIEM](forwarding-audit-to-a-siem.md) | syslog and webhook sinks |
| [Two-step sign-in for public staff](two-step-sign-in.md) | turning on, requiring, rolling out, lost phones |
| [Content Security Policy for public sites](content-security-policy.md) | how it ships, settings, reports |
| [From report-only to an enforced CSP](csp-from-report-only-to-enforce.md) | a staged plan with rollback |
| [request-shield in front of a public site](request-shield.md) | a separate firewall; monitor first |
| [Request logs and the security log](request-logs-and-the-security-log.md) | `404.log`, `security.log`, masking, retention |
| [Security headers](security-headers.md) | the five defaults; checking and changing |
| [HTTPS and HSTS](https-and-hsts.md) | steps, settings, raising HSTS safely |
| [Password policy](password-policy.md) | settings and a length-based policy |
| [Account lockout](account-lockout.md) | off by default; enabling, unlocking |
| [Sessions and cookies](sessions-and-cookies.md) | cookie flags, timeouts, signing users out |
| [Roles and least privilege](roles-and-least-privilege.md) | sensitive policies, a role design, access reviews |
| [File uploads and SVG](file-uploads-and-svg.md) | upload defenses and the SVG sanitizer |
| [Configuration managed by the deployment](managed-by-deployment.md) | locking configuration changes in production |
| [File consistency check](file-consistency-check.md) | detecting changed files |
| [Vulnerability disclosure](vulnerability-disclosure.md) | the project's policy and your own |
| [Adding security.txt](adding-security-txt.md) | RFC 9116 file and the rewrite rule |
| [Patching cadence](patching-cadence.md) | what to watch and an update routine |
| [NIS2 and an Exponential site](nis2-framing.md) | general framing of common measures |
| [NIST Cybersecurity Framework and an Exponential site](nist-csf-framing.md) | the six functions |
| [ISO/IEC 27001 and an Exponential site](iso-27001-framing.md) | ISMS scope and evidence |
| [Penetration tests of an Exponential site](penetration-tests.md) | scope, preparation, findings |
| [Secrets in settings/override](secrets-in-settings-override.md) | which files hold secrets and how to protect them |
| [Separating the administration](separating-the-administration.md) | own host, network restriction, request rules |
| [Log review routine](log-review-routine.md) | weekly and monthly checks |
| [Debug output in production](debug-output-in-production.md) | safe defaults; debugging for one address |
| [CSRF and form tokens](csrf-and-form-tokens.md) | `ezformtoken` and custom code |
| [Proxies and trusted headers](proxies-and-trusted-headers.md) | `TrustedProxies[]`, reading from the right |
| [File permissions and ownership](file-permissions-and-ownership.md) | one writer, read-only code, hardened modes |
| [Backups and recovery](backups-and-recovery.md) | consistent backups, restore tests, smaller recoveries |
| [Incident response for an Exponential site](incident-response.md) | contain, investigate, rebuild, notify |

Tasks that apply these pages step by step are in the compliance tasks of this help ([Compliance tasks](../tasks/compliance/README.md)).

## Related pages

- Up: [Governments: start here](../government-start-here.md)
- [Security standards](../../../standards/security.md)
- [Security hardening](../../security-hardening.md)
- [Security for hosts](../../hosting/security/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
