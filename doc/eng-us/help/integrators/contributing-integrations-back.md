---
title: Contributing integrations back
audience: integrator, developer, partner
category: integration
order: 420
summary: What integrators can give back to Exponential and why it pays - fixes found while integrating, generic connectors as public extensions, documentation of an integration pattern - how to separate generic from customer code, licensing points, and the path from a private repository to a pull request or a published extension.
keywords: contribute, contributing back, open source, pull request, connector, public extension, GPL, license, customer code, generic code, documentation, community, Packagist, partner points
type: help
---
# Contributing integrations back

This page is for integrators and integration companies. Integration projects find bugs at the edges of a system,
build connectors that other customers need too, and learn patterns worth writing down. Giving those back makes the
next project cheaper for you and for everyone, and it is how Exponential grows. The general guide is
[Contribute extensions back](../contribute-extensions-back.md).

## Contents

1. [What to give back](#what-to-give-back)
2. [Separate generic from customer code](#separate-generic-from-customer-code)
3. [Licensing points](#licensing-points)
4. [From private repository to public](#from-private-repository-to-public)
5. [What it brings you](#what-it-brings-you)

## What to give back

| Contribution | Example | Where it goes |
|---|---|---|
| A kernel or extension fix found while integrating | A datatype's `fromString()` stored an invalid value; a REST route answered the wrong status | A pull request to the repository on <https://github.com/se7enxweb> ([Contributing back with pull requests](../extension-development/part-5-extend-fork-contribute/contributing-back-with-pull-requests.md)) |
| A bug report with a reproduction | A setting that does not do what its comment says | An issue in the repository ([Writing good issues](../extension-development/part-5-extend-fork-contribute/writing-good-issues.md)) |
| A generic connector | A mail transport for a provider's API, a login handler for an identity provider, a search engine plugin | A public extension of its own |
| An extension point | A filter or event the kernel should announce so integrations need no override | A pull request with the event and its documentation |
| Documentation | A pattern, a pitfall, a recipe | A pull request to the documentation ([Contributing docs and translations](../partners/contributing-docs-and-translations.md)) |

## Separate generic from customer code

Design the integration as two extensions from the start ([Building an integration as an extension](building-an-integration-as-an-extension.md#one-extension-or-several)):

- **The connector**: the client of the other system's API, its error handling, the mail transport or login handler.
  Configured by settings, with no customer names, node ids, URLs or credentials in the code.
- **The customer extension**: the mapping, the classes, the schedule, the customer's settings.

Check the connector before publishing:

- [ ] No customer names, domains, node ids, internal URLs or test data from the customer.
- [ ] No credentials in code, settings files, tests or the Git history (search the history, not only the last commit).
- [ ] Settings documented, with safe defaults.
- [ ] Tests that run without the other system.
- [ ] A README with the purpose, requirements, installation and an example configuration.

## Licensing points

The Exponential kernel is licensed under the GNU General Public License v2 (or any later version). An extension that
runs inside Exponential and is distributed to others must respect that license; choosing GPL v2 or later for a public
extension keeps it compatible. Using an extension only yourself, or for a customer who receives it as part of a
project, has different obligations than publishing it. Read [Contribute extensions back](../contribute-extensions-back.md#what-the-gpl-requires-and-when)
and [Licensing for partners](../partners/licensing-for-partners.md), and agree in the contract who owns what
([Statements of work](statements-of-work-for-integrations.md)). This is not legal advice.

## From private repository to public

1. Agree with the customer, in writing, that the generic connector may be published.
2. Move the connector into its own repository with a clean history (or start a new one from the cleaned code).
3. Add `LICENSE`, `README.md`, `CHANGELOG.md`, `composer.json`, `ezinfo.php` and `extension.xml` with the same version
   ([Releasing](../extension-development/part-7-releasing/README.md)).
4. Publish a first release; tags are permanent, so check before tagging
   ([Tags are permanent](../extension-development/part-7-releasing/tags-are-permanent.md)).
5. Register the package on Packagist and announce it
   ([Composer and Packagist](../extension-development/part-7-releasing/composer-and-packagist.md)).
6. Let the customer extension require the published connector.

## What it brings you

- Fixes upstream mean no private patches to carry through every upgrade.
- A public connector is reviewed and improved by others, and shows your company's competence to prospects.
- For Exponential Partners, community work counts toward the points of the partner program
  ([Earning points through community work](../partners/earning-points-through-community-work.md)).

## Related pages

- [Contribute extensions back](../contribute-extensions-back.md)
- [Contributing extensions back as a partner](../partners/contributing-extensions-back.md)
- [Contributing code back](../partners/contributing-code-back.md)
- [Part 5 of the extension book: extend, fork, contribute](../extension-development/part-5-extend-fork-contribute/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
