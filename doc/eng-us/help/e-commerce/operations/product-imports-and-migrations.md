---
title: Product imports and migrations
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 77
summary: Loading a product catalog into Exponential - the ezcsvimport script and its file format, price and image columns, what it does not do (updates, header rows), converting prices to ezmultiprice, packages, code and remote services for larger migrations, and a migration plan from another shop system.
keywords: product import, CSV import, ezcsvimport, ezcsvexport, migration, catalog migration, price import, image import, storage-dir, packages, expproduct, setPrice, convertprice2multiprice, move from another shop
type: help
---
# Product imports and migrations

This page is for developers, agencies and merchants who load a catalog into an Exponential store, either once from
another shop system or regularly from a supplier file. It describes the tools that exist, their exact behavior and a
plan for a migration.

## Contents

1. [The tools](#the-tools)
2. [The CSV import in detail](#the-csv-import-in-detail)
3. [Preparing the file](#preparing-the-file)
4. [What the CSV import does not do](#what-the-csv-import-does-not-do)
5. [Larger migrations](#larger-migrations)
6. [A migration plan](#a-migration-plan)

## The tools

| Tool | Does | Fits |
|---|---|---|
| `bin/php/ezcsvimport.php` | Creates and publishes new objects of one class below a node from a CSV file | A first load of a simple catalog |
| `bin/php/ezcsvexport.php` | Writes a subtree to one CSV file per class | Templates for the import, checks, price lists |
| `bin/php/convertprice2multiprice.php` | Converts `ezprice` fields to `ezmultiprice` | Moving to several currencies ([Price fields compared](../catalog/price-fields-compared.md#changing-later)) |
| Packages | Move classes and content between installations | Moving a catalog between your own installations ([Packages](../../../guides/packages.md)) |
| Code | Creating and updating content from PHP | Regular feeds, updates, complex sources ([Creating content in code](../../creators/app-creators/creating-content-in-code.md)) |
| `expservices` product services | Products and prices over HTTP, including `expproduct::setPrice` | Updates from another system ([Headless commerce with expservices](headless-commerce-with-expservices.md)) |

Imports and exports are recorded in the audit trail (`data.*`, routed to the `commerce` channel).

## The CSV import in detail

```bash
php bin/php/ezcsvimport.php --class=product --creator=14 --storage-dir=import/images 2 products.csv
```

| Argument | Meaning |
|---|---|
| `--class` | Identifier of the class to create |
| `--creator` | User id recorded as the creator |
| `--storage-dir` | Directory prepended to image, file and media paths in the file |
| first argument | Node id of the parent folder |
| second argument | The CSV file |

From the code (`kernel/private/classes/commands/ezcsvimport.php`): each line becomes one new object; the fields are
filled **in the order of the class's attributes**, column by column, through each datatype's `fromString()`; then the
object is published.

## Preparing the file

- Separator `;`, enclosure `"`, escape `\`.
- No header row: the first line is imported as a product too.
- One column per class attribute, in the class's order; leave a column empty to leave the field empty.
- Prices in the datatype's text form: `ezprice` as `19.90|<VAT type id>|1` (1 including VAT, 0 excluding),
  `ezmultiprice` as `<VAT type id>|1|EUR|19.90|CUSTOM|USD|21.50|AUTO`
  ([Price fields compared](../catalog/price-fields-compared.md#text-formats-for-imports-and-exports)).
- Images, files and media as paths relative to `--storage-dir`.
- Export one existing product with `ezcsvexport.php` and use its line as the pattern.

## What the CSV import does not do

| Not done | Consequence |
|---|---|
| Update existing objects | Running a file twice creates every product twice |
| Match by item number | Updates need code or services |
| Skip a header row | Remove it before importing |
| Map columns by name | Reordering class attributes breaks old files |
| Create folders | Import each category into its own folder, one run per folder |

## Larger migrations

From another shop system, the catalog is only one part:

| Data | Approach |
|---|---|
| Products, prices, images | CSV import for simple catalogs; code for options, relations and translations |
| Categories | Folders created first, by hand or in code |
| Customers | Only with a lawful basis and a plan for passwords; often better to let customers register again |
| Past orders | Usually kept in the old system or exported for the records; the order tables are not designed for imports |
| URLs | Redirects from old product addresses ([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)) |

The trainers' migration course covers moving sites in general ([Migration course](../../trainers/migration/README.md)).

## A migration plan

1. Model the classes ([Catalog modeling in depth](../catalog/catalog-modeling-in-depth.md)).
2. Create VAT types, product categories and currencies; note their ids.
3. Export a sample from the old system; convert it to the column order and price format.
4. Import into a test copy; check **Store > Products overview** and ten products by hand.
5. Fix the converter, not the imported data; repeat until clean.
6. Set up redirects from old addresses.
7. Import into the live store before opening, then run the store test plan ([Testing a store](testing-a-store.md)).

## Related pages

- [Operating a store](README.md)
- [Catalog modeling](../catalog/README.md)
- [Packages and ezpkg](../../creators/app-creators/packages-and-ezpkg.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
