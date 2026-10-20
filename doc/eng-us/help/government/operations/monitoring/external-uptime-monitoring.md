---
title: External uptime monitoring
audience: government, admin, hosting, project-manager
category: public-sector
order: 120
summary: Checking a public website from outside, as citizens reach it - which addresses to check, what counts as healthy, how maintenance windows look to a monitor, certificate expiry, and reporting availability honestly.
keywords: uptime monitoring, availability, external checks, synthetic monitoring, status code, response time, maintenance 503, certificate expiry, status page, availability report, SLA
type: help
---
# External uptime monitoring

This page is for IT teams and service owners who must know, and often report, whether a public website is available.
Exponential does not include an outside monitor; any monitoring service or tool that can request a URL works. This
page says what to check, based on [Health checks and uptime (hosting)](../../../hosting/operations/health-checks-and-uptime.md).

## Contents
1. [What to check](#what-to-check)
2. [Maintenance windows](#maintenance-windows)
3. [Certificates](#certificates)
4. [Where to run the checks](#where-to-run-the-checks)
5. [Reporting availability](#reporting-availability)
6. [Related pages](#related-pages)

## What to check

| Check | Example | Healthy |
|---|---|---|
| Public front page over HTTPS | `curl -s -o /dev/null -w '%{http_code} %{time_total}\n' https://www.example.gov/` | `200`, time in your normal range |
| Administration sign-in page | `curl -s -o /dev/null -w '%{http_code}\n' https://www.example.gov/admin/user/login` | `200` |
| A word only the rendered page contains | your monitor's content match | found |
| One important service page (a form, a search) | its address | `200` |

The front page is often served from a cache (Velocity's response cache, the HTTP cache, a CDN), so it can answer while
the database is down; the sign-in page and a content match catch that.

## Maintenance windows

During maintenance mode every page answers `503 Service Unavailable` with `Retry-After` and `noindex`. Either pause the
monitor for the announced window, or accept a 503 with `Retry-After` while `php bin/php/maintenance.php status` says
maintenance is on, and alert when the window lasts longer than announced: maintenance never ends by itself
([Maintenance windows](../upgrades-and-change/maintenance-windows.md)). Static files are still served, so a check on an
image stays green during maintenance.

## Certificates

Point the monitor at the address visitors use, with HTTPS, so an expiring certificate or a broken redirect is an
alert. Under Velocity, `qbixconsole ssl:show` prints the certificate in use; Let's Encrypt certificates are renewed by
the server itself ([Velocity HTTPS and certificates](../../../../features/6.0/velocity-https-certificates.md)). See
[Certificate expiry](../incidents/certificate-expiry.md).

## Where to run the checks

From outside your own network (citizens are outside), and from at least two places, so one network problem does not
look like an outage. With several web servers or engines, add one check per node or port, as the hosting page advises.

## Reporting availability

If your organization reports availability (to management, in a service level agreement or publicly), define in
writing what counts as "available" (which checks, from where, how often), whether announced maintenance counts, and
how incidents are classified. The figures are an organizational choice; Exponential provides no availability
percentage of its own ([Availability targets and service levels](../availability/availability-targets-and-service-levels.md)).

## Related pages
- [Site down](../incidents/site-down.md)
- [Monitoring overview](monitoring-overview.md)
- [Check security headers](../../tasks/operations/check-security-headers.md)
- [Several web servers behind a load balancer](../availability/several-web-servers-behind-a-load-balancer.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
