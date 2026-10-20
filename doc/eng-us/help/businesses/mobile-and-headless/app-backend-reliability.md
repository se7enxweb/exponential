---
title: App backend reliability
audience: businesses, admin, app-creator
category: business-solutions
order: 100
summary: How a business keeps the Exponential installation behind its app, portal or partner integration available - what can make the API unavailable, health checks, maintenance windows that answer 503, rate limits, caching, scaling, credentials that expire, and what the app should do when the back end does not answer.
keywords: app backend, reliability, availability, REST API, health check, monitoring, maintenance mode, 503, Retry-After, rate limit, Velocity watchdog, scaling, caching, business
type: help
---
# App backend reliability

This page is for owners and product managers whose app, customer portal or partner integration depends on an
Exponential installation, and for the administrators who keep it running. When the website is slow, visitors notice;
when the API behind an app fails, the app looks broken in every customer's pocket. This page lists what can make the
back end unavailable, what Exponential provides against each risk, and what the app itself must do.

## Contents

1. [What can make the API unavailable](#what-can-make-the-api-unavailable)
2. [Watching it: health checks](#watching-it-health-checks)
3. [Planned work: maintenance windows](#planned-work-maintenance-windows)
4. [Load: caching, limits and scaling](#load-caching-limits-and-scaling)
5. [Credentials that stop working](#credentials-that-stop-working)
6. [What the app must do](#what-the-app-must-do)
7. [Reliability checklist for an app back end](#reliability-checklist-for-an-app-back-end)

## What can make the API unavailable

| Cause | What helps | Read |
|---|---|---|
| The server process stopped | Velocity's watchdog restarts the engine; monitoring alerts | [Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md) |
| Planned maintenance | Maintenance mode answers 503 with `Retry-After` | below |
| Too many requests | Caching, rate limits per key, more capacity | below |
| An expired or revoked credential | Key lifetimes and the API keys overview | below |
| A changed server configuration | The `Authorization` header must still reach PHP | [REST API v1 and v2 for applications](../../creators/app-creators/rest-api.md#configuration) |
| An upgrade that changed answers | Release notes; for the REST API in 6.0.15 the answers stayed "byte for byte the same" | [REST API v1 and v2 for businesses](rest-api-v1-and-v2-for-businesses.md#what-6015-changed-for-existing-apps) |

The reliability section covers the platform as a whole ([Reliability](../reliability/README.md)); this page adds what
is specific to apps.

## Watching it: health checks

From [Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md):

- Check from outside: the public site, and the administration login page, because the front page "is often served
  from a cache ... so it can answer while the database is down". A page whose content you can check is stronger than a
  status code.
- On the server: `./console exp:systeminfo --json` exits 1 when a health check fails; status commands with exit codes
  suit automated checks.
- Under Velocity: the endpoint `/Q/health` answers `{"status":"ok"}` when the server process answers; "it does not
  render a page of the site, so keep the external page checks as well". The watchdog starts the engine again after a
  crash and backs off after three failed starts in a row.

For an app, add one more check: an **authenticated API request** with a monitoring key that may only read one small
public item, for example `GET /api/ezp/v1/content/node/<id>`. It proves the REST routes, authentication and the
database together.

## Planned work: maintenance windows

While maintenance mode is on, the REST API (`index_rest.php`) answers with the maintenance page and status 503, like
the site ([Maintenance mode guide](../../../guides/maintenance-mode.md)). The responses carry `Retry-After` with the
seconds until the expected end ([Planned maintenance is not an outage](../../hosting/operations/health-checks-and-uptime.md#planned-maintenance-is-not-an-outage)).

For an app that means: treat a 503 with `Retry-After` as "come back later", show a friendly message, and retry after
the given time. Announce windows to partners who integrate with you.

> **Note**
> "Maintenance never ends by itself when the expected time passes." Alert when a window lasts longer than announced.

## Load: caching, limits and scaling

| Lever | Effect | Source |
|---|---|---|
| The REST answer cache | Repeated reads answered from cache; cleared with the content tag | [Headless Exponential](../../creators/app-creators/headless-exponential.md#caching-and-performance) |
| Caching in the app or a CDN, purged on publish | Fewer requests reach the server | same |
| Paged lists with limits | No request fetches everything | same; expservices pages at most 200 items |
| Rate limit per API key | 120 requests per key and minute by default, counted in APCu; not enforced without APCu | [Personal API keys](../../../guides/api-keys.md#8-auditing-and-limits) |
| Velocity and its persistent workers | The application stays warm between requests | [Scaling applications](../../creators/app-creators/scaling-applications.md) |
| Several web servers | Horizontal capacity, with shared storage and one database | [Scaling applications](../../creators/app-creators/scaling-applications.md#several-web-servers) |

Ask your developers to measure before scaling: the app creators' guide starts with "Measure first".

## Credentials that stop working

Personal API keys expire (365 days at most by default) and end when their owner is disabled; OAuth tokens expire
after their lifetime (one hour by default) and are renewed with a refresh token
([Apps and API keys](apps-and-api-keys.md)). A forgotten expiry date is a self-inflicted outage:

- put every key's end date in the operations calendar;
- check **Setup > oAuth admin > API keys** for keys "expiring within seven days";
- never let an integration run under a person's account that may be disabled when they leave.

## What the app must do

Write these into the app's requirements:

- [ ] Show cached content when the API does not answer, with a note that it may be out of date.
- [ ] Treat 503 with `Retry-After` as a planned pause; retry with growing intervals, not in a tight loop.
- [ ] Treat 401 as "credential expired or revoked": refresh the token, or ask the user to sign in again.
- [ ] Treat 403 as "not allowed", not as an error to retry.
- [ ] Respect rate limits; batch and page requests.
- [ ] Send every request over HTTPS.
- [ ] Log failures with time and status so support can match them with the server's logs
      ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)).

## Reliability checklist for an app back end

- [ ] External monitoring of the site, the administration login and one authenticated API read.
- [ ] Velocity's watchdog installed, if Velocity serves the site.
- [ ] Maintenance windows announced to app users and partners; the app handles 503.
- [ ] Rate limits configured, and APCu available where they must be enforced.
- [ ] Key and token expiry dates in the calendar; one credential per app and partner.
- [ ] Backups restored in a drill, so a restore of the back end is a known procedure
      ([Restore drills](../reliability/restore-drills.md)).
- [ ] An incident contact for the app team and for the server team
      ([Incident response for small teams](../reliability/incident-response-for-small-teams.md)).

## Related pages

- [Headless with the REST API](headless-with-the-rest-api.md)
- [Mobile checklist](mobile-checklist.md)
- [Health checks and monitoring](../reliability/health-checks-and-monitoring.md)
- [Maintenance mode and planned work](../reliability/maintenance-mode-and-planned-work.md)
- [Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
