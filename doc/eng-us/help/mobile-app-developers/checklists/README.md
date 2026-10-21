---
title: Checklists for app developers
audience: mobile-app-developer, app-creator, developer, project-manager
category: mobile-apps
order: 900
summary: The landing page of the checklists for apps on Exponential - security, launch and app store review readiness - with when to use each and who signs it off.
keywords: checklists, security, launch, app store review, release, sign-off, mobile app
type: help
---
# Checklists for app developers

This folder is for app developers and the people who sign off a release. Each checklist links to the page that
explains its items. Print them, copy them into your tracker, adapt them to your project.

## Contents

1. [The checklists](#the-checklists)
2. [Who signs off what](#who-signs-off-what)

## The checklists

| Checklist | Use it | Read |
|---|---|---|
| Security | before the first release and after every change to credentials, roles or endpoints | [Security checklist](security-checklist.md) |
| Launch | in the week before an app goes live against a production installation | [Launch checklist](launch-checklist.md) |
| App store review readiness | before submitting to an app store | [App store review readiness](app-store-review-readiness.md) |

## Who signs off what

| Item | Signed off by |
|---|---|
| Roles, integration users, OAuth clients, key policies | the installation's administrator |
| Server settings (`rest.ini`, caches, CORS at the web server) | the hosting company or server team |
| Content model, entry folders, image aliases | the site team |
| App behavior, secrets on devices, store submission | the app team |

The business view of the same launch is the [Mobile checklist](../../businesses/mobile-and-headless/mobile-checklist.md).

## Related pages

- [Mobile app developers: start here](../mobile-app-developers-start-here.md)
- [Security for applications](../../creators/app-creators/security-for-applications.md)
- [Launch checklist for websites](../../creators/website-creators/launch-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
