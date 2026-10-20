---
title: Open data checklist
audience: government, project-manager, editor, admin
category: public-sector
order: 180
summary: One checklist for launching and running an open data catalog on Exponential, from the first decisions to the yearly review, with a link to the page that explains each item.
keywords: checklist, open data, launch, catalog, dataset, license, privacy, stable URL, feed, API, review, quality
type: help
---
# Open data checklist

This checklist is for project managers and data teams. Tick each item before launch and review it once a year.
Each item names the page of this section that explains it.

## Contents

1. [Decisions](#decisions)
2. [Building](#building)
3. [Each dataset](#each-dataset)
4. [Interfaces](#interfaces)
5. [Running it](#running-it)

## Decisions

- [ ] The body knows which open data rules apply to it (see "What open data means for a public body"; legal counsel).
- [ ] An inventory of datasets exists, with an owner per dataset.
- [ ] The approved licenses are listed (see "Licensing datasets").
- [ ] It is decided whether a national portal must harvest the catalog, and how (see "DCAT and national portals").
- [ ] The privacy check routine is agreed with the data protection officer (see "Privacy checks before publishing data").

## Building

- [ ] Dataset and distribution classes exist, with required license, publisher and contact fields (see "Designing a
  dataset class").
- [ ] A catalog folder in its own section, readable by Anonymous (see "Who may publish datasets").
- [ ] A Data publisher role and an approval workflow; the `frequent` cronjob group runs.
- [ ] A line view for datasets; the catalog page shows license, formats and dates (see "A data catalog page").
- [ ] Themes as tags or a selection (see "Tagging and themes for datasets").

## Each dataset

- [ ] Title, description, publisher, team contact mailbox, license, dates, update frequency filled in.
- [ ] Files in open, machine-readable formats, UTF-8, with stable names.
- [ ] Privacy check done and recorded; file properties removed.
- [ ] Download tested signed out.
- [ ] A short permanent alias ("Cite as") added (see "Stable URLs for datasets").

## Interfaces

- [ ] An RSS or Atom feed of new datasets, linked from the catalog (see "Feeds for new and changed datasets").
- [ ] REST API: authenticated or anonymous decided and documented (see "The REST API as a data interface").
- [ ] Bulk exports of registers kept as content are reproducible (preset or command) (see "Exporting content as CSV,
  JSON or XML").

## Running it

- [ ] Updates follow the agreed pattern (one current file, or one file per period) (see "Versioning and updating
  datasets").
- [ ] Withdrawn datasets keep their page with a notice; nothing is removed.
- [ ] Version history limits fit the record-keeping needs.
- [ ] **Setup > Link management** and **Setup > Search statistics** are reviewed for broken links and unmet searches.
- [ ] Roles and the Data team group are reviewed once a year.

## Related pages

- [What open data means for a public body](what-open-data-means.md)
- [Open data in Exponential at a glance](open-data-in-exponential-at-a-glance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
