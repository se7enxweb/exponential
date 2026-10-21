---
title: Appliances
audience: bundler, partner
category: partnership
order: 360
summary: How to bundle Exponential as a hardware or sealed virtual appliance - what an appliance owes its owner under the GPL, a first-boot setup, updates and security fixes over the appliance's life, backups, support, and an appliance checklist. Not legal advice.
keywords: appliance, hardware, sealed virtual machine, box, first boot, setup, GPL, source code, updates, security fixes, backup, support, OEM, bundle
type: help
---
# Appliances

This page is for vendors who sell Exponential inside a box: a hardware device, or a virtual machine the customer
does not administer. An appliance is the bundle with the longest life and the least visible software, so its
obligations need the most care.

## Contents

1. [What makes an appliance different](#what-makes-an-appliance-different)
2. [The GPL and a box](#the-gpl-and-a-box)
3. [First start](#first-start)
4. [Updates over the appliance's life](#updates-over-the-appliances-life)
5. [Backups and data ownership](#backups-and-data-ownership)
6. [Appliance checklist](#appliance-checklist)

## What makes an appliance different

| Aspect | Appliance |
|---|---|
| Who administers the system | you, through your update channel |
| Who administers the site | the customer, in the Exponential administration |
| How long it runs | years |
| Network | sometimes none ([Offline bundles](offline-bundles.md)) |
| Database | usually on the box: SQLite 3 for one machine ([SQLite bundles](sqlite-bundles.md)) |
| Web server | Velocity, with its own TLS ([Velocity in a bundle](velocity-in-a-bundle.md)) |

## The GPL and a box

An appliance distributes the GPL software on it. The customer must receive the license texts, the notices and the
corresponding source, and you may add no restrictions the GPL forbids
([GPL obligations](gpl-obligations-when-distributing.md)). In practice:

- put the notices on the box's documentation and in its web interface (**Info > Copyright** stays reachable);
- publish or offer the source of the exact software image, for each image you ship;
- be careful with measures that stop an owner from changing the GPL software on a device they own; ask counsel which
  license versions of your components set rules for that.

This is not legal advice. OEM resale through the partner program is described on
[OEM and the partner program for bundlers](oem-and-the-partner-program-for-bundlers.md).

## First start

A setup screen of yours collects what the installer needs and calls it once
([First boot and install once](first-boot-and-install-once.md)):

1. the site's address, or the box's address if it has none yet;
2. the administrator's e-mail;
3. the administrator password: generated (shown once) or typed by the owner, at least `MinPasswordLength` (10) and
   not well known;
4. then `exp:install` with those values, and the setup screen disables itself.

## Updates over the appliance's life

- Security fixes for Exponential come "on the `main` branch ... and published with the next 6.0.x release"
  (`SECURITY.md`). Your channel must deliver new releases, not patch old tags.
- Every update has a code half and a database half ([Updates and upgrade paths](updates-and-upgrade-paths.md)).
- After every update: caches, autoloads, Velocity restart; `exp:velocity deploy` "runs every step a PHP change needs,
  in order" ([Velocity engines](../../bc/6.0/velocity-engines.md#in-short)).
- State in your terms how long you deliver updates for each model.

## Backups and data ownership

The site's content is the customer's. Give them a way to back it up and take it with them: the database and
`var/storage`, `settings/override`, `settings/siteaccess`
([Backups: what to back up](../hosting/operations/backups-what-to-back-up.md)). Content exports and packages let them
move to another installation ([Moving a site between hosts](../hosting/migrations/moving-a-site-between-hosts.md)).

## Appliance checklist

- [ ] License notices, third-party list and source offer for each image.
- [ ] No shared default password; first start generates or asks.
- [ ] HTTPS, security headers and hardening applied ([Security defaults](security-defaults-of-a-bundle.md)).
- [ ] An update channel with a documented support period.
- [ ] Backup and export available to the owner.
- [ ] Offline install tested if the box may have no network.
- [ ] Support boundaries written down ([Support boundaries](support-boundaries.md)).

## Related pages

- [OEM and reselling](../partners/oem-and-reselling.md)
- [Server and virtual machine images](server-and-virtual-machine-images.md)
- [Protecting customer data](../partners/protecting-customer-data.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
