---
title: Testing apps against Exponential
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 800
summary: The landing page of the testing pages for app developers - where to test (a public demo, a local installation, a staging copy of production), what each is good for, and the tests an app on Exponential needs.
keywords: testing, demo, local installation, staging, probe, test data, integration tests, contract tests, mobile app
type: help
---
# Testing apps against Exponential

This folder is for app developers who need an Exponential installation to develop and test against. The choice of
installation matters: a public demo is quick to look at, but only an installation you control lets you make keys,
classes and test content freely.

## Contents

1. [Where to test](#where-to-test)
2. [Tests an app needs](#tests-an-app-needs)
3. [Pages in this folder](#pages-in-this-folder)

## Where to test

| Installation | Good for | Not good for |
|---|---|---|
| A public demo | seeing the administration and content; probing which APIs answer | keys, writes, your own classes; it is rebuilt on a schedule |
| A local installation of your own | everything during development; automated tests | production data and load |
| A staging copy of the production site | final checks with the real classes, roles and content | destructive tests |

## Tests an app needs

- **Contract tests** against a local or staging installation: one read per route and service the app uses, checking
  the keys and types it parses ([Fields and field values](../rest-api/fields-and-field-values.md)).
- **Permission tests**: the app's user can read what it shows and nothing it must not, and its writes are refused
  outside its folder ([Permissions for app users](../publishing-workflow/permissions-for-app-users.md)).
- **Error tests**: `401`, `403`, `404`, `429` and timeouts are handled ([Errors and status codes](../rest-api/errors-and-status-codes.md)).
- **Offline tests**, if the app syncs ([Syncing and offline reading](../offline-and-performance/syncing-and-offline-reading.md)).

## Pages in this folder

| Page | Answers |
|---|---|
| [Testing against a demo](testing-against-a-demo.md) | Probing an installation, and what a public demo allows |
| [A local test installation](a-local-test-installation.md) | Installing one in minutes and preparing it for an app |

## Related pages

- [Testing applications](../../creators/app-creators/testing-applications.md)
- [Launch checklist](../checklists/launch-checklist.md)
- [Demo mode FAQ](../../demo-mode-faq.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
