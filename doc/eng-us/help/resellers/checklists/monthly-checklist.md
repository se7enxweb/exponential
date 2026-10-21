---
title: Monthly checklist for resellers
audience: reseller, partner, hosting
category: partnership
order: 480
summary: The monthly routine of a reseller of Exponential-based offers - releases and the site inventory, backups and restore tests, logs and security, support statistics, partner activity, pipeline and invoicing - with a monthly customer report template.
keywords: monthly checklist, routine, releases, inventory, backups, restore test, logs, security, support statistics, partner tickets, pipeline, invoicing, customer report
type: help
---
# Monthly checklist for resellers

This page is for operations and account staff at resellers. Run it once a month for all customers; it keeps sites
current and gives you the material for the customer reports that keep customers renewing. The hosting section has a
deeper technical routine ([Monthly operations](../../hosting/checklists/monthly-operations.md)).

## Contents

1. [Sites](#sites)
2. [Support](#support)
3. [Business](#business)
4. [Customer report template](#customer-report-template)

## Sites

- [ ] Check for new Exponential 6.0.x releases and extension releases; read the changelog
      ([Keeping customers current](../customers/keeping-customers-current.md)).
- [ ] Update the site inventory: version, PHP, extensions per site.
- [ ] Apply pending releases in the agreed window, staging first; record the three results (version, upgrade check,
      test publish).
- [ ] Backups ran every day; restore-test the sites due this month
      ([Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md)).
- [ ] Review **Setup > Log files** and the security log for each site ([Security log](../../hosting/operations/security-log.md)).
- [ ] Review the audit trail for unexpected administrator actions ([Audit trail for hosts](../../hosting/security/audit-trail.md)).
- [ ] Certificates: none expiring within 30 days ([Certificate expiry](../../hosting/runbooks/certificate-expiry.md)).
- [ ] Disk space and growth of `var/storage` ([Disk full](../../hosting/runbooks/disk-full.md)).
- [ ] Accounts of staff who left removed from every customer's administration.

## Support

- [ ] Tickets per customer by severity; measured first response against your promises
      ([Service level design](../offers/service-level-design.md)).
- [ ] Partner support tickets used this month versus your plan's allowance
      ([Partner plans and prices](../../partners/partner-plans-and-prices.md#the-plans)).
- [ ] Recurring questions turned into customer documentation ([Documentation you can give your customers](../customers/documentation-for-your-customers.md)).
- [ ] Bugs reported upstream and their status ([Escalation for resellers](../customers/escalation.md)).

## Business

- [ ] Invoices sent; extra hours billed ([Invoicing and taxes](../responsibilities/invoicing-and-taxes.md)).
- [ ] Pipeline reviewed; stale leads closed ([Sales process](../sales/sales-process.md#pipeline-record)).
- [ ] Referred leads followed up; commission records updated ([Lead handoffs](../sales/lead-handoffs.md#commission-records)).
- [ ] Partner activity logged for points ([Logging your partner activity](../../partners/logging-partner-activity.md)).
- [ ] Renewals due in the next 90 days started ([Renewals and churn](../customers/renewals-and-churn.md#the-renewal-cycle)).

## Customer report template

```text
Monthly report for <Customer>, <month>

Your site: <example.com>, Exponential 6.0.__, PHP 8.__
Updates applied: <release, date> (or: none due)
Backups: daily, all successful / <exceptions>; last restore test: <date>
Availability: <measured>, incidents: <none / short description>
Support: <n> requests; average first response <time>
Security: <nothing to report / what was done>
Next month: <planned update window, PHP change, training, open wishes>
```

Keep it short; its purpose is to show the work the customer pays for.

## Related pages

- [Monthly operations](../../hosting/checklists/monthly-operations.md)
- [Yearly checklist for resellers](yearly-checklist.md)
- [Managed service reseller](../models/managed-service-reseller.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
