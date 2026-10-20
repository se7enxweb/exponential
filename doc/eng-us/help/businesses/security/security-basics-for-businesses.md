---
title: Security basics for businesses
audience: businesses, admin, project-manager
category: business-solutions
order: 10
summary: The ten security basics a business site on Exponential needs, what the software already does for each, what you must decide, and who in a small organization usually does it. Not legal advice.
keywords: security basics, HTTPS, updates, passwords, two-step sign-in, roles, least privilege, backups, restore, administration, monitoring, audit trail, incident, responsibility, small business
type: help
---
# Security basics for businesses

This page is for owners and managers who are responsible for a website application but are not security
specialists. It lists ten basics, says what Exponential already does for each, what you must still decide, and who
in a small organization usually takes care of it. The technical "how" is in the linked pages.

> **Warning**
> This is a summary of basics, not legal or professional security advice. Laws and contracts may ask more of you;
> read them, and ask a lawyer, your data protection officer or a security professional where needed.

## Contents

1. [Who is responsible for what](#who-is-responsible-for-what)
2. [The ten basics](#the-ten-basics)
3. [What the software does by default](#what-the-software-does-by-default)
4. [What you must decide](#what-you-must-decide)
5. [A one-page checklist](#a-one-page-checklist)

## Who is responsible for what

Security is shared. A typical split in a small organization:

| Party | Usually responsible for |
|---|---|
| Site owner (you) | Deciding who gets access, which data you collect, contracts, telling people after an incident |
| Administrator (staff or partner) | Roles, accounts, updates of the application, settings, the audit trail |
| Host | Server, operating system, PHP, network, backups of the machine, physical security |
| Developer or partner | Custom templates and extensions, keeping them safe across updates |
| The Exponential project | Fixing vulnerabilities in the software and publishing releases (`SECURITY.md`) |

Write down who does which job for your site. The [Project brief template](../templates/project-brief-template.md)
and the RACI matrix in [Project templates](../../leadership/project-templates.md) have room for it.

## The ten basics

| # | Basic | What Exponential provides | Read |
|---|---|---|---|
| 1 | HTTPS everywhere | Velocity serves HTTPS itself, including Let's Encrypt certificates; secure cookies on HTTPS | [TLS and HTTPS](../../hosting/security/tls-and-https.md) |
| 2 | Current software | One maintained line; fixes come with the next 6.0.x release | [Security updates and disclosure](security-updates-and-disclosure.md) |
| 3 | Strong sign-in | bcrypt hashes, common passwords refused, two-step sign-in | [User accounts and passwords](user-accounts-and-passwords.md) |
| 4 | Least privilege | Fine-grained roles and policies; nobody gets anything no role gives | [Roles and policies](../../../guides/roles-and-policies.md) |
| 5 | Backups that restore | A documented list of what to back up and a restore test procedure | [Backups that restore](../reliability/backups-that-restore.md) |
| 6 | A protected administration | Its own host name, network limits, request-shield in front | [Restricting the administration](../../hosting/security/restricting-the-administration.md) |
| 7 | Watching | Request logs, a log viewer, and audit alerts for brute force and risky changes | [The audit trail](../../../features/6.0/audit-trail.md) |
| 8 | Know your personal data | Mail consent, form exports, account lists, retention settings | [Personal data in Exponential](personal-data-in-exponential.md) |
| 9 | A way to report problems | Private reports to the project, first answer within one business day | `SECURITY.md` |
| 10 | An incident plan | Runbooks for a compromised or downed site | [Compromised site](../../hosting/runbooks/compromised-site.md) |

## What the software does by default

From [Security and audit](../../../guides/security-and-audit.md) and [Security hardening](../../security-hardening.md):

- Every page carries security headers such as `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` and
  `Permissions-Policy`.
- The session cookie is `HttpOnly`, `SameSite=Lax`, and `Secure` on HTTPS; logging out ends the session for good.
- Uploaded images are checked by their content, not their name; SVG images are stored only sanitized.
- No installer leaves the old default administrator password in place
  ([The default administrator password](../../default-administrator-password.md)).
- The sign-in does not reveal which accounts exist; audit alert rules report brute-force attempts.
- A Content Security Policy is sent report-only on the administration
  ([Content Security Policy](../../../features/6.0/content-security-policy.md)).
- The audit trail is on by default, needs no setup, and records sign-ins, content changes, settings writes and
  orders ([The audit trail](../../../features/6.0/audit-trail.md)).

## What you must decide

- [ ] Whether two-step sign-in is required, at least for administrators and editors.
- [ ] Who may sign in to the administration, and from where.
- [ ] Which personal data the site collects, and for how long it keeps it ([Data retention and deletion](data-retention-and-deletion.md)).
- [ ] Who receives the audit trail's alert mail (default: the site's `AdminEmail`).
- [ ] How fast you apply a security release, and who does it.
- [ ] Whether the Content Security Policy should be enforced on your public site after testing.
- [ ] What you tell customers if something goes wrong, and who decides.

## A one-page checklist

Print it and tick it once a year, or after any big change:

- [ ] The site answers only on HTTPS; the certificate renews by itself.
- [ ] The newest 6.0.x release is installed; extensions are current.
- [ ] Every person has a personal account; no shared administrator account.
- [ ] Two-step sign-in is on for administrators.
- [ ] Roles reviewed this year ([Access reviews](access-reviews.md)).
- [ ] A restore was tested this quarter.
- [ ] Audit alerts reach a person who reads them.
- [ ] The list of personal data and its retention is written down.
- [ ] Staff know to report suspected vulnerabilities to security@se7enx.com, not in public.
- [ ] The incident runbook is known to whoever is on duty.

The full technical checklist is in
[Accessibility, security and compliance checklists](../../leadership/accessibility-security-and-compliance-checklists.md).

## Related pages

- [Security hardening](../../security-hardening.md)
- [Security and audit guide](../../../guides/security-and-audit.md)
- [Hardening for hosts](../../hosting/security/hardening-for-hosts.md)
- [Store security checklist](../stores/store-security-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
