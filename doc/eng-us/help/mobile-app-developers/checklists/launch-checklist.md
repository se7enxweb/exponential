---
title: Launch checklist for apps
audience: mobile-app-developer, app-creator, developer, project-manager, admin
category: mobile-apps
order: 920
summary: A checklist for the week before an app on Exponential goes live - the production installation and its APIs, accounts and credentials, content and entry points, images, caching and load, error handling, monitoring, support and the plan for server upgrades.
keywords: launch, checklist, go live, production, release, REST, expservices, credentials, content, image aliases, caching, rate limit, monitoring, support, upgrades, mobile app
type: help
---
# Launch checklist for apps

This checklist is for the app team and the people who run the Exponential installation, in the week before the app
goes live against production. Each item links to the page that explains it.

## Contents

1. [The installation](#the-installation)
2. [Accounts and credentials](#accounts-and-credentials)
3. [Content](#content)
4. [Performance](#performance)
5. [Errors and monitoring](#errors-and-monitoring)
6. [After launch](#after-launch)

## The installation

- [ ] The probes answer as expected on the production address
      ([Probe before you build](../testing/testing-against-a-demo.md#probe-before-you-build)).
- [ ] `ezprestapi` 1.2.6 or later is active and `ezprestapiprovider` is removed
      ([API versions v1 and v2](../rest-api/api-versions-v1-and-v2.md#the-deprecated-provider)).
- [ ] `expservices` is active if the app uses remote services, and its token table exists.
- [ ] The app's base URL is configuration, and points at production in the release build.

## Accounts and credentials

- [ ] The integration user and its role exist on production, not only on staging.
- [ ] The OAuth client is registered on production with the exact production redirect address.
- [ ] Production keys and secrets are in the back end's secret store, and the test ones are revoked.
- [ ] The [Security checklist](security-checklist.md) is done.

## Content

- [ ] The entry folders exist on production with the remote ids the app is configured with
      ([Node ids, remote ids and deep links](../content-modeling/node-ids-remote-ids-and-deep-links.md)).
- [ ] Every class the app shows has items, and the fields the app needs are filled.
- [ ] The image aliases the app uses exist on production
      ([Image aliases for screen sizes](../media-and-images/image-aliases-for-screen-sizes.md)).
- [ ] Editors know which fields the app shows and how long publishing takes to reach it
      ([Drafts, preview and publishing](../publishing-workflow/drafts-preview-and-publishing.md#what-editors-should-know)).

## Performance

- [ ] Lists are paged with small limits; detail calls are not made per row
      ([Response groups](../rest-api/response-groups.md#choosing-groups-for-screens)).
- [ ] The app caches with stale-while-revalidate ([Caching in the app](../offline-and-performance/caching-in-the-app.md)).
- [ ] A back end that uses one API key stays below `RateLimitPerMinute`.
- [ ] The server team knows the expected traffic and the REST cache TTL is agreed
      ([Caching on the server](../offline-and-performance/caching-on-the-server.md)).

## Errors and monitoring

- [ ] Every status of [Errors and status codes](../rest-api/errors-and-status-codes.md) is handled, including `429`
      and timeouts.
- [ ] The app reports API errors with route, status and time to your crash or analytics tool, without credentials.
- [ ] Someone watches the audit's refusals for the first days.
- [ ] Support knows how to tell "the app is down" from "the site is down"
      ([App backend reliability](../../businesses/mobile-and-headless/app-backend-reliability.md)).

## After launch

- [ ] The server's upgrade plan includes a test of the app against the new version first
      ([A local test installation](../testing/a-local-test-installation.md)).
- [ ] Class changes that touch the app's fields are announced to the app team.
- [ ] Key and secret rotation dates are in the calendar.

## Related pages

- [Mobile checklist (for businesses)](../../businesses/mobile-and-headless/mobile-checklist.md)
- [App store review readiness](app-store-review-readiness.md)
- [Deployment pipeline](../../creators/app-creators/deployment-pipeline.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
