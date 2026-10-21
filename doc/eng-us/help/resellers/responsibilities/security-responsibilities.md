---
title: Security responsibilities of resellers
audience: reseller, partner, hosting
category: partnership
order: 420
summary: What a reseller is responsible for in the security of the Exponential sites it sells - the shared responsibility split with the project, hosts and customers, a per-site security checklist, credentials, updates, private disclosure to security@se7enx.com, demo sites, and a short incident plan.
keywords: security, responsibilities, shared responsibility, hardening, updates, credentials, passwords, two-step sign-in, Content Security Policy, audit trail, vulnerability, security@se7enx.com, incident, demo mode
type: help
---
# Security responsibilities of resellers

This page is for owners and technical leads at resellers. When you sell a site, a plan or a product, the customer
trusts **you** with its security, whatever part the project, a host or 7x plays behind you. The partners' version is
[Security responsibilities of partners](../../partners/security-responsibilities.md).

## Contents

1. [Shared responsibility](#shared-responsibility)
2. [Per-site checklist](#per-site-checklist)
3. [Credentials](#credentials)
4. [Vulnerabilities and updates](#vulnerabilities-and-updates)
5. [Incidents](#incidents)
6. [Demo and training sites](#demo-and-training-sites)

## Shared responsibility

| Who | Responsible for |
|---|---|
| The project | Secure code in the kernel and its extensions; fixes published in releases (`SECURITY.md`) |
| The host (you, or your supplier) | Servers, PHP, database, TLS, network, backups |
| You as the reseller | Configuration, extensions chosen, custom code, updates applied, monitoring, incident response, as your contract says |
| The customer | Accounts and passwords of their editors, the content, who they give access to |

Write this split into each contract ([Contracts with customers](../customers/contracts-with-customers.md)). If you buy
in hosting or white-label services, you remain responsible to the customer.

## Per-site checklist

- [ ] Hardened as in [Security hardening](../../security-hardening.md) and chapter 13 of the installation book
      ([Security hardening chapter](../../../install/13-security-hardening.md)).
- [ ] Default and shared passwords changed ([Default administrator password](../../default-administrator-password.md)).
- [ ] Setup wizard and installer files not reachable.
- [ ] TLS everywhere ([TLS and HTTPS](../../hosting/security/tls-and-https.md)); the administration's Content Security
      Policy not weakened ([Content Security Policy](../../hosting/security/content-security-policy.md)).
- [ ] Debug output off in production.
- [ ] Two-step sign-in offered to administrators ([Two-step sign-in](../../../guides/two-factor-login.md)).
- [ ] The administration restricted where possible ([Restricting the administration](../../hosting/security/restricting-the-administration.md)).
- [ ] Audit trail on and reviewed ([Audit trail](../../../features/6.0/audit-trail.md)).
- [ ] Custom code reviewed for SQL injection, XSS and CSRF.
- [ ] Updates applied on a schedule ([Keeping customers current](../customers/keeping-customers-current.md)).
- [ ] Backups tested by a restore.
- [ ] Each customer isolated from the others ([Multi-tenant isolation](../../hosting/security/multi-tenant-isolation.md)).

## Credentials

- Never in tickets, chats, e-mails or Git repositories.
- Hand credentials to customers through a secure channel; make them change passwords on first sign-in.
- Personal accounts for your staff, never a shared administrator account.
- Remove accounts of staff who leave, on your side and in every customer's administration.

## Vulnerabilities and updates

- A vulnerability in Exponential goes privately to **security@se7enx.com**; "You can expect a first answer within one
  business day" (`SECURITY.md`). Never report it in public ([Escalation for resellers](../customers/escalation.md#security-vulnerabilities)).
- Fixes are made on `main` and published with the next 6.0.x release; "a published tag is never changed". Your
  customers are protected only when you apply the release.
- Promise a time to apply security releases only after measuring how long a tested update across all your sites takes
  ([Service level design](../offers/service-level-design.md#security-fixes)).

## Incidents

Have a short plan for every customer:

1. **Contain**: take the affected part offline or block the attack path (maintenance mode helps:
   [Maintenance mode](../../../guides/maintenance-mode.md)).
2. **Preserve**: keep logs and copies for analysis ([Security log](../../hosting/operations/security-log.md)).
3. **Inform**: the customer at once; authorities if the law requires it ([Data protection for resellers](data-protection.md)).
4. **Fix**: apply the fix, rotate credentials, restore clean backups.
5. **Learn**: write down what happened and change the process.

The step-by-step runbook is [Compromised site](../../hosting/runbooks/compromised-site.md).

## Demo and training sites

A public demo runs in demo mode (`EXP_DEMO_MODE=1`), which only protects administrator passwords: a visitor signed in
as an administrator can still change settings and upload files. Keep demos on their own system user, pool and
database, never with real data, and make sure the variable never reaches a production process
([Demo sites for resellers](../offers/demo-sites.md)).

## Related pages

- [Security responsibilities of partners](../../partners/security-responsibilities.md)
- [Hardening for hosts](../../hosting/security/hardening-for-hosts.md)
- [Security review checklist](../../hosting/checklists/security-review.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
