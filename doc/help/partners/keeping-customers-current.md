---
title: Keeping customer sites current
audience: partner
category: partnership
order: 680
summary: How partners keep customer sites on maintained Exponential releases and supported PHP versions - an inventory, the support commitments of the project, maintenance contracts, upgrade cadence, and how to talk with customers about upgrades.
keywords: maintenance, upgrade, releases, PHP versions, inventory, maintenance contract, security fixes, support commitment, lifecycle
type: help
---
# Keeping customer sites current

This page is for partner owners and technical leads responsible for customer sites after launch. A site that is not
updated becomes a security risk and, eventually, an expensive rescue.

## The project's commitments

| Commitment | Source |
|---|---|
| "Exponential 6.0 is the maintained line. Security fixes are made on the `main` branch ... and published with the next 6.0.x release." | SECURITY.md |
| 5.x, 4.x, 3.x and older are not supported | SECURITY.md |
| PHP 8.0 to 8.5 are supported for the 6.0.15 line; PHP 7.4 only up to 6.0.7 | README, `doc/install/02-requirements.md` |
| "A published tag is never changed" | SECURITY.md |

The full list: [Who is the Exponential Foundation?, "The support commitment"](../who-is-the-exponential-foundation.md#the-support-commitment).

## Keep an inventory

```text
Customer site inventory
Site | Exponential version | PHP | Database | Engine | Extensions (versions) | Last upgrade | Next planned
-----|---------------------|-----|----------|--------|-----------------------|--------------|-------------
```

Review it monthly. Any site below the current 6.0.x release or on a PHP version near its end gets a plan.

## Maintenance contracts

Offer maintenance with every project. A useful contract states:

- which releases are applied and how fast (security fixes faster than feature releases);
- that each release is tested on staging first ([Testing releases before your customers do](testing-releases.md));
- PHP upgrades as part of the service;
- what is not included (new features, redesigns).

## Talking with customers

- Explain upgrades as protection, not as a sale.
- Show the changelog items that matter to them.
- Plan major steps (PHP versions, extension replacements) a year ahead
  ([Release and upgrade planning](../leadership/release-and-upgrade-planning.md)).

## Sites from the eZ Publish era

Many organizations still run sites built on the old code base before it became Exponential. They are partners'
natural customers. The migration chapters `doc/install/14-migrating-from-4x.md` to `17-migration-reference.md`
describe the paths.

## Related pages

- [Testing releases before your customers do](testing-releases.md)
- [Security responsibilities of partners](security-responsibilities.md)
- [Release and upgrade planning](../leadership/release-and-upgrade-planning.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
