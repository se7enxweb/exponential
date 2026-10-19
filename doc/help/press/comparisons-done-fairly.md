---
title: Comparisons done fairly
audience: press, advocate
category: press-media
order: 140
summary: How journalists and reviewers compare Exponential with other content management systems fairly - same criteria and versions on both sides, each product described by its own sources, no benchmarks without a published method, disclosure of interests, and a comparison table template.
keywords: comparison, compare, versus, vs, alternatives, other CMS, WordPress, Drupal, Ibexa, benchmark, fair comparison, criteria, review, methodology
type: help
---
# Comparisons done fairly

This page is for reviewers, journalists and creators who compare Exponential with another CMS. Comparisons help
readers decide, and they go wrong in predictable ways. The project itself compares only what it can source
([Comparing Exponential factually](../business/comparing-exponential-factually.md)); this page applies the same
discipline to your coverage.

## Contents

1. [The rules](#the-rules)
2. [Criteria that can be checked](#criteria-that-can-be-checked)
3. [Performance comparisons](#performance-comparisons)
4. [A comparison table template](#a-comparison-table-template)
5. [Words to avoid](#words-to-avoid)

## The rules

1. **Same criteria, both sides.** Every row of your table has a value for every product.
2. **Each product by its own sources.** Describe the other product from its own documentation, as you describe
   Exponential from this one.
3. **Same date, comparable versions.** Name the versions and the date you checked.
4. **Facts in the table, opinions in the text.** Say which is which.
5. **Disclose interests.** If you work with, are paid by or sponsor any of the products, say so
   ([Disclosure and sponsorship ethics](disclosure-and-sponsorship-ethics.md)).
6. **Ask both sides** when a row is unclear.

## Criteria that can be checked

| Criterion | Exponential source |
|---|---|
| License | [Licenses explained for reporters](licenses-explained-for-reporters.md) |
| PHP versions | README, "What version of PHP is required" |
| Databases | [Database support](../database-support.md) |
| Operating systems | [Operating system support](../os-support.md) |
| Languages, right to left | [Languages in Exponential](../../languages.md) |
| Content model | [The content model and editing content](../../guides/content-model-and-editing.md) |
| Multisite | [Story: multisite](story-multisite.md) |
| Built-in shop | README, "Main Exponential features" |
| Security process | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Release history | [The 6.0.x releases at a glance](the-6-0-x-releases-at-a-glance.md) |

## Performance comparisons

The project publishes figures for its own stack and no comparison with other products. If you compare speed:

- run both products on the same hardware, with comparable pages, caches configured as each project recommends;
- publish your method, versions, configuration and raw results;
- say what you did not test.

Without that, do not write "faster than" ([Verifying numbers](verifying-numbers.md)).

## A comparison table template

```text
COMPARISON: Exponential <version> and <Other CMS> <version>        checked <date>

CRITERION            EXPONENTIAL                         OTHER CMS                    SOURCES
License              GPL v2 or later                     <its license>                <links>
PHP versions         8.0 - 8.5                           <...>                        <links>
Databases            SQLite, PostgreSQL, MongoDB,        <...>                        <links>
                     MariaDB, Oracle, MySQL
Multisite            built in                            <...>                        <links>
Shop                 built in                            <...>                        <links>
Right-to-left        yes                                 <...>                        <links>
Security reporting   private, first answer within        <...>                        <links>
                     one business day
Disclosure           <your relationship to each product>
```

## Words to avoid

| Avoid | Use |
|---|---|
| "better", "best", "killer" | What it does, with the source |
| "outdated" for the other product | Its version and date, with its own description |
| "the eZ Publish alternative" | Exponential is the same code base, renamed ([Writing about the eZ Publish years](writing-about-the-ez-publish-years.md)) |
| "like Ibexa but free" | Separate products; Exponential is not associated with Ibexa ([Not associated with eZ Systems or Ibexa](not-associated-with-ez-systems-or-ibexa.md)) |

## Related pages

- [Writing a fair review](writing-a-fair-review.md)
- [Objection: "Why not another CMS?"](../advocates/objection-why-not-another-cms.md)
- [Comparing Exponential factually](../business/comparing-exponential-factually.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
