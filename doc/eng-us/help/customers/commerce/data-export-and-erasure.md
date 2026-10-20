---
title: Data export and erasure
audience: customer, admin, hosting
category: customer-success
order: 220
summary: What a store owner can export and erase in Exponential, and how - mail data, accounts, form submissions, orders, newsletter records, logs and backups - with a yearly retention routine and the questions to settle with your host. Not legal advice.
keywords: data export, data erasure, retention, delete customer data, remove orders, archive orders, mail preferences export, collected information, backups, retention schedule, not legal advice
type: help
---
# Data export and erasure

This page is for the owner and the administrator. It lists, for each kind of data a store holds, how it is exported
and how it is erased, and gives a yearly routine to keep data no longer than you decided. The full tables of defaults
and settings are on [Data retention and deletion](../../businesses/security/data-retention-and-deletion.md); this page
is the store's view of them.

> **Warning**
> Not legal advice. How long you must or may keep each kind of data is a legal and bookkeeping decision. Ask your
> lawyer, your accountant or your data protection officer.

## Export and erase, by kind of data

| Data | Export | Erase | Source |
|---|---|---|---|
| Mail preferences and consent | The person's own download; `./console exp:mail:preferences export --email=<address>` | `./console exp:mail:preferences erase --email=<address> --yes` (consent log anonymized, withdrawal kept as proof) | [Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md) |
| User account | Read in **Users** | Remove the user: final, no trash, erases mail preferences | [Check user accounts](../../../features/6.0/user-hygiene.md) |
| Orders | One customer's orders in the customer order view; each receipt as a downloaded file | Archive (`shop/archiveorder`) or remove (`shop/removeorder`) | `kernel/shop/module.php`, [Permanent order receipts](../../../features/6.0/order-receipts.md) |
| Form submissions | **Setup > Collected information**, CSV export | Same page: remove collections | [Collected information](../../../guides/collected-information.md) |
| Newsletter subscribers | The `cjw_newsletter` administration | The `cjw_newsletter` user list | [cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md) |
| Audit records | `exp:audit export --subject-user=<id>` | Not one by one; kept for the retention period, personal fields pseudonymized | [Data retention and deletion](../../businesses/security/data-retention-and-deletion.md) |
| Baskets | Not needed | Removed by the `basket_cleanup.php` cron job | `settings/cronjob.ini` |
| Backups | Your host | Expire under your backup retention | [Backups: what to back up](../../hosting/operations/backups-what-to-back-up.md) |

> **Note**
> The shop has no export of all orders to a spreadsheet, and no command that removes or anonymizes all orders of one
> customer among the shop views (`kernel/shop/module.php`, and [What the built-in shop does](../../businesses/stores/what-the-built-in-shop-does.md)). For a single
> request, the customer order view finds that customer's orders by e-mail address (`shop/customerorderview`).

## Archive or remove orders?

| | Archive | Remove |
|---|---|---|
| Leaves the active order list | yes | yes |
| Can be brought back | yes (`shop/unarchiveorder`) | no |
| Kept for bookkeeping | yes | no |
| Personal data still stored | yes | no |

Most stores archive finished orders after their returns period and remove them only when the bookkeeping period has
passed. Write your periods into a retention schedule; the one on
[Data retention and deletion](../../businesses/security/data-retention-and-deletion.md) has a line for orders.

## The defaults that run on their own

Several kinds of data are removed by cron jobs, with periods you can change, for example sessions after 3 days
(`site.ini [Session] SessionTimeout`), `404.log` after 30 days and `security.log` after 90 days
([Data retention and deletion](../../businesses/security/data-retention-and-deletion.md)).

> **Warning**
> If the cron jobs do not run, nothing is removed. Ask your host to confirm that they are scheduled
> ([Cron jobs](../../../guides/cronjobs.md)).

## A yearly retention routine

- [ ] Retention schedule reviewed with the adviser: periods for orders, accounts, form submissions, logs, backups.
- [ ] Settings still match the schedule; your administrator changes them in `settings/override`.
- [ ] Orders past the bookkeeping period removed, others archived.
- [ ] Form submissions older than needed removed in **Setup > Collected information**.
- [ ] Long-unused customer accounts reviewed with **Users > Check user accounts**.
- [ ] Newsletter subscribers who are gone handled in the newsletter administration.
- [ ] Export files on staff computers and in `var/tmp/` deleted.
- [ ] Backup retention confirmed with the host.

## What to settle with your host

- How long backups are kept, and where.
- Whether erased data must be removed from backups, or expires with them.
- How a restore avoids bringing erased data back into use.
- Which server, mail and panel logs they keep outside Exponential, and for how long.

## Related pages

- [Data retention and deletion](../../businesses/security/data-retention-and-deletion.md)
- [GDPR requests](gdpr-requests.md)
- [Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md)
- [Log and data retention for hosts](../../hosting/security/log-and-data-retention.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
