---
title: Personal data in Exponential
audience: businesses, admin, developer
category: business-solutions
order: 30
summary: Where an Exponential installation stores personal data - user accounts, sessions, orders, form submissions, mail preferences and consent, newsletter subscribers, notifications, search phrases, logs, the audit trail, exports and backups - each checked against the database schema and the documentation, with who may see it and how to remove it. Not legal advice.
keywords: personal data, data inventory, data map, user accounts, ezuser, orders, ezorder, information collector, ezinfocollection, newsletter, cjw_newsletter, cjwnl_user, consent log, expmail, sessions, logs, audit trail, backups, GDPR
type: help
---
# Personal data in Exponential

This page is for site owners, data protection officers and administrators who need a map of where personal data is
kept in an Exponential installation. You cannot protect, export or delete data you have not found. Each row below
was checked against the database schema (`kernel/sql/mysql/kernel_schema.sql`, the `cjw_newsletter` schema) and the
documentation named.

> **Warning**
> This is a technical map, not legal advice. Your own site may store more: custom content classes, extensions and
> templates can add personal data anywhere. Check them with your developer, and take legal questions to a lawyer
> or your data protection officer.

## Contents

1. [The map at a glance](#the-map-at-a-glance)
2. [Accounts and sign-in](#accounts-and-sign-in)
3. [Store: baskets and orders](#store-baskets-and-orders)
4. [Forms: the information collector](#forms-the-information-collector)
5. [Mail preferences and newsletters](#mail-preferences-and-newsletters)
6. [Logs and the audit trail](#logs-and-the-audit-trail)
7. [Everything else to check](#everything-else-to-check)
8. [Building your own data inventory](#building-your-own-data-inventory)

## The map at a glance

| Data | Where | Typical contents | Read |
|---|---|---|---|
| User accounts | `ezuser` table and the user's content object | login, e-mail address, password hash; name and other fields of the user class | [Accounts and sign-in](../../accounts-and-sign-in.md) |
| Sign-in activity | `ezuservisit` | last visit, number of sign-ins, failed attempts | [Check user accounts](../../../features/6.0/user-hygiene.md) |
| Sessions | `ezsession` (database handler) or PHP's session files | user id, session data | [Sessions](../../../guides/sessions.md) |
| Orders | `ezorder` | e-mail address, user id, and an account record with name, address, phone and comment | [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md) |
| Form submissions | `ezinfocollection`, `ezinfocollection_attribute` | whatever visitors typed, often names and e-mail addresses | [Collected information](../../../guides/collected-information.md) |
| Mail preferences and consent | `expmail_preference`, `expmail_consent_log`, `expmail_pending` | choices per category; consent history with time, source, text shown, IP address, acting user | [E-mail preferences](../../../guides/mail-preferences-administrator.md) |
| Suppression list | `expmail_suppression` | a salted hash of each address, never the address | same |
| Newsletter subscribers | `cjwnl_user`, `cjwnl_subscription`, `cjwnl_blacklist_item` | e-mail, salutation, first and last name, organization, birthday, phone number, bounce state | [cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md) |
| Notification subscriptions | `ezsubtree_notification_rule` | which user follows which content | [Notifications](../../../guides/notifications-administrator.md) |
| Search phrases | `ezsearch_search_phrase` | phrases visitors typed, which can contain personal data | [Accessibility, security and compliance checklists](../../leadership/accessibility-security-and-compliance-checklists.md#data-protection-and-privacy-questions-to-answer) |
| Request and security logs | `var/<site>/log/` | masked addresses, shortened user agents | [Request logs](../../../features/6.0/request-logs.md) |
| Audit trail | `var/<site>/log/audit/` and the `expaudit_*` index tables | who did what; addresses truncated, sessions hashed | [The audit trail](../../../features/6.0/audit-trail.md) |
| Exports | files you download, and files in `var/tmp/` | CSV and JSON exports of forms, mail data, audit records | [Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md) |
| Backups | wherever your host keeps them | everything above | [Backups: what to back up](../../hosting/operations/backups-what-to-back-up.md) |

## Accounts and sign-in

- **The account** is a row in `ezuser` (login, e-mail address, password hash) tied to a content object of the user
  class, which holds the name and any other fields your class defines. Passwords are stored as bcrypt hashes
  ([Security hardening](../../security-hardening.md#brute-force-and-credential-stuffing-on-the-sign-in)).
- **Activity**: `ezuservisit` keeps the last visit, the number of sign-ins and failed attempts; **Users > Check user
  accounts** reads it ([Check user accounts](../../../features/6.0/user-hygiene.md)).
- **Keys**: activation keys and password reset keys have their own tables (`ezuser_accountkey`, `ezforgot_password`).
- **Two-step sign-in**: the authenticator secret is stored with the user; a `SecretKey` encrypts it in the database
  ([Two-step sign-in](../../../guides/two-factor-login.md)).
- **Removal**: a user object never goes to the trash; removing it is final. Removing an account also erases its mail
  preferences ([Check user accounts](../../../features/6.0/user-hygiene.md),
  [Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md#requests-from-data-subjects)).

## Store: baskets and orders

- `ezorder` keeps the customer's e-mail address and user id, and, in its account record, the first and last name,
  e-mail address, street, ZIP code, city, state, country, phone and comment entered at checkout
  (`kernel/private/classes/views/shop/userregister.php`).
- `ezbasket` ties a basket to a session; `ezwishlist` to a user.
- Orders can be archived and removed in the administration (`shop/archiveorder`, `shop/removeorder`,
  [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md)). Your bookkeeping
  duties may require keeping orders for a period; decide with your accountant.
- Card data: the audit trail records payments "never card data" ([The audit trail](../../../features/6.0/audit-trail.md));
  see [Card payments and PCI basics](../stores/card-payments-and-pci-basics.md).

## Forms: the information collector

Each time a visitor sends a form, one collection is stored with one value per collector field. **Setup > Collected
information** lists, exports (CSV) and removes collections; it needs the policy `infocollector/read`. Each export is
recorded in the audit trail with the number of rows, never the values
([Collected information](../../../guides/collected-information.md)). Mail to a receiver puts the same data in an
inbox as well ([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).

## Mail preferences and newsletters

- The kernel's mail gate keeps each person's choices, a consent log and a suppression list stored as salted hashes
  ([Consent and forms](consent-and-forms.md)).
- The newsletter extension `cjw_newsletter` keeps its own subscriber records (`cjwnl_user`) with the fields listed
  above, subscriptions per list, and a blacklist with address and hash. Open and click tracking is off by default
  (`[TrackingSettings] Tracking=disabled`) ([Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md#what-stays-with-the-host-and-the-customer)).

## Logs and the audit trail

- `404.log` and `security.log` mask client addresses by default (`MaskIP=enabled`); the log viewer masks e-mail
  addresses, IP addresses and secrets in what it shows ([Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md#privacy-defaults-in-the-logs)).
- Velocity's access log is in Apache's combined format and "names visitors"; its files are readable by the owner and
  group only (same source).
- The audit trail truncates addresses, hashes sessions, shortens user agents, and never records passwords, tokens or
  secrets ([The audit trail](../../../features/6.0/audit-trail.md)).
- Web server, mail server and panel logs outside Exponential follow your host's settings.

## Everything else to check

- [ ] Custom content classes with personal fields (members, staff profiles, comments, event sign-ups).
- [ ] Content authorship: each object and version records its creator and owner.
- [ ] Private messages, if `ezpm` is active (it adds inboxes and outboxes; `composer.json` description).
- [ ] Exponential Intelligence, if switched on: which provider receives which text
      ([expai](../../../features/6.0/extensions/expai.md)).
- [ ] Third-party services embedded by templates: fonts, maps, analytics, videos.
- [ ] Exported files on staff computers.
- [ ] Backups and their retention.

## Building your own data inventory

Copy this table into your record and fill it in per row:

```text
DATA INVENTORY
Data | Purpose | Legal basis (ask your adviser) | Where stored | Who may see it | Retention | How deleted
-----|---------|--------------------------------|--------------|----------------|-----------|------------
User accounts      |  |  | ezuser + user objects |  |  |
Orders             |  |  | ezorder               |  |  |
Form submissions   |  |  | ezinfocollection      |  |  |
Mail consent       |  |  | expmail_*             |  |  |
Newsletter         |  |  | cjwnl_*               |  |  |
Logs               |  |  | var/<site>/log        |  |  |
Audit trail        |  |  | var/<site>/log/audit  |  |  |
Backups            |  |  | (host)                |  |  |
```

## Related pages

- [GDPR basics](gdpr-basics.md)
- [Data retention and deletion](data-retention-and-deletion.md)
- [Form submissions](../../creators/website-creators/form-submissions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
