---
title: Testing releases before your customers do
audience: partner
category: partnership
order: 670
summary: A release-testing routine for partners - following releases and main, a staging copy per customer, a test checklist, testing development versions early, feeding results back as issues, and why early testing protects both customers and the project.
keywords: testing, release, staging, upgrade, regression, main branch, changelog, checklist, test plan, feedback
type: help
---
# Testing releases before your customers do

This page is for partners who maintain customer sites. Testing a release on your customers' setups before you deploy
it is the best way to avoid surprises, and testing it before it is released helps everyone.

## Know what is coming

- Releases are tags of <https://github.com/se7enxweb/exponential>; watch its releases on GitHub.
- The changes are in the changelog of each version (`doc/changelogs/6.0/`) and the release overview
  (`doc/en/us/release-overview`).
- Development happens on `main`; "Security fixes are made on the `main` branch ... and published with the next 6.0.x
  release" (SECURITY.md).
- "A published tag is never changed" (SECURITY.md): a fix arrives as the next release.

## A staging copy per customer

- Same PHP version, database, engine and extensions as production.
- Content: a copy without personal data where possible, or anonymized ([Protecting customer data](protecting-customer-data.md)).
- Installed from Git, so you can move between versions quickly ([Develop with Git](../develop-with-git.md)).

## Test checklist

- [ ] Upgrade steps of the release applied (`doc/en/us/install/11-upgrading.md`)
- [ ] Front page, a content page, search and forms work
- [ ] Sign-in to the administration; edit and publish a draft; upload an image
- [ ] Each language and siteaccess
- [ ] Custom extensions and templates: no errors in the logs
- [ ] Cronjobs run
- [ ] Caches cleared; performance comparable
- [ ] The customer's critical journeys (checkout, membership, ...)

## Test early: development versions

Testing `main` before a release finds problems while they are cheap to fix. Use a staging copy, never production.
Report what you find as issues ([Filing bugs well](filing-bugs-as-a-partner.md)).

## Feed back

- Report regressions with exact versions.
- Tell the project when an upgrade went well for a type of site; such reports help others plan.

## Related pages

- [Keeping customer sites current](keeping-customers-current.md)
- [Release and upgrade planning](../leadership/release-and-upgrade-planning.md)
- [Filing bugs well](filing-bugs-as-a-partner.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
