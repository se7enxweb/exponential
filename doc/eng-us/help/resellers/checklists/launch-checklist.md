---
title: Launch checklist for resellers
audience: reseller, partner, hosting
category: partnership
order: 470
summary: The checklist a reseller works through before a customer's Exponential site, plan or product goes live - contract and commercial items, technical readiness, security, backups, monitoring, content and accessibility checks, training, documentation and the first day after launch.
keywords: launch, go-live, checklist, launch checklist, customer site, readiness, security, backups, monitoring, DNS, TLS, training, documentation, Powered by Exponential
type: help
---
# Launch checklist for resellers

This page is for project leads at resellers. Work through it before every customer launch. The technical items
follow the hosting section's go-live list ([New customer site go-live](../../hosting/checklists/new-customer-site-go-live.md));
this list adds the reseller's commercial and customer items.

## Contents

1. [Commercial](#commercial)
2. [Technical](#technical)
3. [Security](#security)
4. [Content and quality](#content-and-quality)
5. [People and documents](#people-and-documents)
6. [Launch day and the day after](#launch-day-and-the-day-after)

## Commercial

- [ ] Contract signed, including data processing terms and exit clause ([Contracts with customers](../customers/contracts-with-customers.md)).
- [ ] Tier and service levels confirmed in writing ([Service level design](../offers/service-level-design.md)).
- [ ] First invoice scheduled ([Invoicing and taxes](../responsibilities/invoicing-and-taxes.md)).
- [ ] Subcontractors (hosting supplier, white-label services) confirmed for this customer.
- [ ] "Powered by Exponential" credit agreed or declined ([Showing Powered by Exponential](../sales/powered-by-exponential.md)).

## Technical

- [ ] Server meets the requirements; web PHP and command-line PHP match ([Hosting requirements](../offers/hosting-requirements-for-offers.md)).
- [ ] Newest 6.0.x release and current extension versions installed ([Keeping customers current](../customers/keeping-customers-current.md)).
- [ ] **Setup > Upgrade check** clean ([The upgrade check](../../../guides/upgrade-check.md)).
- [ ] Every host name in DNS, the web server or Velocity, and the certificate; each siteaccess reaches the right site
      ([Multi-site offers](../offers/multi-site-offers.md)).
- [ ] Cron jobs running ([Cronjobs](../../../guides/cronjobs.md)).
- [ ] Mail delivery tested ([Mail delivery](../../hosting/operations/mail-delivery.md)).
- [ ] Caches and performance checked under expected load ([Benchmarking a hosted site](../../hosting/performance/benchmarking-a-hosted-site.md)).

## Security

- [ ] Hardening applied; debug output off ([Security responsibilities of resellers](../responsibilities/security-responsibilities.md)).
- [ ] Default passwords changed; personal accounts only; customer has own administrator account.
- [ ] Two-step sign-in offered to administrators.
- [ ] Demo mode is **not** set: no `EXP_DEMO_MODE` in the site's pool, virtual host, service units or crontab
      ([Demo mode for hosts](../../hosting/security/demo-mode-for-hosts.md#keeping-it-away-from-production)).
- [ ] Backups running; one restore tested ([Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md)).
- [ ] Monitoring of front page and administration ([Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md)).

## Content and quality

- [ ] Accessibility of the public design checked ([Accessibility standards](../../../standards/accessibility.md)).
- [ ] Redirects from old addresses in place ([URLs, redirects and moved pages](../../customers/day-to-day/urls-redirects-and-moved-pages.md)).
- [ ] Privacy notice and consent texts written by the customer.
- [ ] Store sites: payment, VAT and confirmation mail tested ([Store launch checklist](../../businesses/stores/store-launch-checklist.md)).
- [ ] Languages: every language reachable, right-to-left tested if needed ([Multilingual offers](../offers/multilingual-offers.md)).

## People and documents

- [ ] Editors trained ([Training reseller](../models/training-reseller.md)).
- [ ] Customer manual handed over, with notices kept ([Documentation you can give your customers](../customers/documentation-for-your-customers.md)).
- [ ] Welcome letter with contacts and support hours sent ([Onboarding customers](../customers/onboarding-customers.md#welcome-letter-template)).
- [ ] Site added to your inventory and your monthly routine.

## Launch day and the day after

- [ ] Switch DNS or go live in an agreed window; staff on hand.
- [ ] Check front page, administration, a test publish, forms, search.
- [ ] Check **Setup > Log files** after the first hours.
- [ ] Send the customer a short "live" note with what to watch for.
- [ ] One week later: review logs, tickets and editor questions.

## Related pages

- [New customer site go-live](../../hosting/checklists/new-customer-site-go-live.md)
- [Onboarding customers](../customers/onboarding-customers.md)
- [Monthly checklist for resellers](monthly-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
