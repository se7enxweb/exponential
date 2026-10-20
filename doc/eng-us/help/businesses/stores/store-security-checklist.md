---
title: Store security checklist
audience: businesses, admin, hosting
category: business-solutions
order: 210
summary: The security tasks a store owner should check, in plain language - accounts and two-factor sign-in, shop permissions, payments, HTTPS, updates, backups, logs and the audit trail - with links to the detailed hardening pages.
keywords: store security, webshop security, checklist, two-factor, roles, permissions, HTTPS, updates, backups, audit trail, form tokens, Content Security Policy, vulnerability report
type: help
---
# Store security checklist

This page is for store owners and managers who are responsible for their store's security but are not security
specialists. It turns the project's hardening advice into a checklist for a store, says who usually does each item,
and links to the detailed page. Hand it to your host or partner and ask them to confirm each line.

> **Note**
> These are basics, not legal advice. Card security, data protection and consumer law obligations depend on where you
> sell and on your contracts. Read the rules that apply to you and ask your lawyer, data protection officer or payment
> provider.

## Contents

1. [Why a store is a target](#why-a-store-is-a-target)
2. [Accounts and sign-in](#accounts-and-sign-in)
3. [Shop permissions](#shop-permissions)
4. [Payments](#payments)
5. [The site and the server](#the-site-and-the-server)
6. [Updates](#updates)
7. [Backups](#backups)
8. [Watching](#watching)
9. [Customer data](#customer-data)
10. [If something happens](#if-something-happens)

## Why a store is a target

A store holds customer names, addresses and order histories, and it can change prices and order statuses. Attackers
look for weak administrator passwords, outdated software and payment steps they can bypass. The project's view of the
threats is in [Security hardening](../../security-hardening.md#who-attacks-a-cms-and-how).

## Accounts and sign-in

| Check | Who | Read |
|---|---|---|
| The installation's initial administrator password has been changed | Host or partner | [Change the admin password](../../change-the-admin-password.md) |
| Each person has their own account; nobody shares one | Owner | [Security hardening](../../security-hardening.md#checklist) |
| Two-step sign-in is required for administrators and shop staff | Host or partner | [Two-factor login](../../../guides/two-factor-login.md), [sevenx_authentication_2fa](../../../features/6.0/extensions/sevenx_authentication_2fa.md) |
| Accounts of people who left are disabled the same day | Owner | [Access reviews](../security/access-reviews.md) |
| The administration runs on its own host and is restricted | Host | [Restricting the administration](../../hosting/security/restricting-the-administration.md) |

## Shop permissions

The shop's functions are `buy`, `administrate`, `setup`, `edit_status` and `setstatus` (`kernel/shop/module.php`).

- [ ] Visitors and customers have only `shop/buy`.
- [ ] Order handlers have `shop/administrate` and `shop/setstatus`, limited by `FromStatus` and `ToStatus` where that
  fits ([Orders and order statuses](orders-and-order-statuses.md#who-may-change-a-status)).
- [ ] Only the owner or accountant has `shop/setup` (VAT, currencies, discounts).
- [ ] Editors who maintain products have content permissions only on the product folders.
- [ ] Roles are reviewed twice a year ([Access reviews](../security/access-reviews.md)).

## Payments

- [ ] A payment step is on checkout; the Store dashboard does not warn "no payment step"
  ([Store dashboard and reports](store-dashboard-and-reports.md#next-steps)).
- [ ] A redirect gateway is used where possible, so card data never reaches your server
  ([Card payments and PCI basics](card-payments-and-pci-basics.md)).
- [ ] With ezauthorize: card data storage reviewed by a developer before launch (same page).
- [ ] Payment notifications are checked against amount and currency, as ezpaypal does
  ([PayPal payments](paypal-payments.md#what-the-extension-checks)).
- [ ] Provider test mode and sandbox addresses removed on launch day.
- [ ] Secrets (gateway keys, the receipt secret) live in override files, never in shipped files or in version control
  ([Environment settings and secrets](../../creators/app-creators/environment-settings-and-secrets.md)).

## The site and the server

From the project's hardening checklist ([Security hardening](../../security-hardening.md#checklist)):

- [ ] HTTPS everywhere, with HSTS and secure cookies ([TLS and HTTPS](../../hosting/security/tls-and-https.md)).
- [ ] `ezformtoken` active, so forms carry a per-session token against cross-site request forgery.
- [ ] Content Security Policy enforced, first on the administration, then on the sites
  ([Content Security Policy](../../../features/6.0/content-security-policy.md)).
- [ ] Debug output off and PHP errors not displayed.
- [ ] The installation wizard cannot run on the installed site.
- [ ] Code read-only for the web server's user.
- [ ] A request shield or request rules in front ([Request rules](../../../features/6.0/request-rules.md)).

## Updates

- [ ] Someone follows Exponential's releases and applies security fixes. Exponential 6.0 is the maintained line, and
  fixes are published with the next 6.0.x release ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).
- [ ] Extensions (including payment gateways) are updated with the system.
- [ ] Updates are tested on a copy before the live store ([Upgrades without surprises](../reliability/upgrades-without-surprises.md)).

## Backups

- [ ] The database is backed up daily at least; orders live there.
- [ ] Backups are kept off the server and protected, since they contain customer data.
- [ ] A restore has been tested ([Backups that restore](../reliability/backups-that-restore.md),
  [Backups and restores](../../creators/website-creators/backups-and-restores.md)).

## Watching

- [ ] The audit trail is on (it is by default) and someone reads it. It records orders, payments (never card data),
  VAT, currencies and discounts in its `commerce` category ([Audit trail](../../../features/6.0/audit-trail.md)).
- [ ] Audit alerts reach a person, and the trail is forwarded off the machine (hardening checklist).
- [ ] Logs are read weekly ([Log viewer](../../../features/6.0/log-viewer.md)).

## Customer data

- [ ] Only staff who need it can open the customer list and orders.
- [ ] Order records are kept as long as your bookkeeping requires, and no longer
  ([Data retention and deletion](../security/data-retention-and-deletion.md)).
- [ ] Marketing mail only with consent ([Opt-in and consent](../newsletters/opt-in-and-consent.md)).
- [ ] Your privacy notice describes what the store stores ([GDPR basics](../security/gdpr-basics.md)).

## If something happens

- Follow the host's runbook for a compromised site ([Compromised site](../../hosting/runbooks/compromised-site.md)).
- Report a vulnerability in Exponential privately to the address in [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md), not in a
  public issue.
- Tell your payment provider at once if payment data may be involved.
- Write down what happened ([Incident response for small teams](../reliability/incident-response-for-small-teams.md)).

## Related pages

- [Security hardening](../../security-hardening.md)
- [Security basics for businesses](../security/security-basics-for-businesses.md)
- [Store launch checklist](store-launch-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
