---
title: Mapping content classes to external data
audience: integrator, developer
category: integration
order: 290
summary: Turning records of another system into content objects - designing the class, choosing datatypes, the fromString() text formats of the common datatypes, images and files from a DAM, relations, languages, locations, and a mapping table template.
keywords: mapping, content class, datatype, fromString, toString, ezstring, eztext, ezxmltext, ezboolean, ezinteger, ezdatetime, ezimage, ezbinaryfile, ezobjectrelationlist, ezselection, ezprice, ezurl, ezkeyword, eztags, DAM, PIM, languages, locations, field mapping
type: help
---
# Mapping content classes to external data

This page is for integration developers and content architects who decide how records of a PIM, an ERP, a DAM or an
event system become content in Exponential. The mapping is the contract of the integration: once data flows,
changing it is a migration. Settle it in writing before the first import.

## Contents

1. [Design the class for the integration](#design-the-class-for-the-integration)
2. [Text formats of the common datatypes](#text-formats-of-the-common-datatypes)
3. [Images and files from a DAM](#images-and-files-from-a-dam)
4. [Relations between imported records](#relations-between-imported-records)
5. [Languages and locations](#languages-and-locations)
6. [A mapping table](#a-mapping-table)

## Design the class for the integration

| Decision | Recommendation |
|---|---|
| One class per kind of record | `product`, `product_category`, `event`; do not overload a generic `article` |
| Identifier names | Stable and lower case (`sku`, `price`, `stock`); the import uses identifiers, never attribute ids |
| Fields owned by the source | Mark them in the attribute description ("set by the ERP import; changes are overwritten") and consider a role that cannot edit them |
| Fields owned by editors | Keep them separate (an editorial `intro` next to the imported `description`); the import never touches them |
| Hidden technical fields | A text attribute for the source's checksum or last change, not shown in templates |
| Required attributes | Only what the source always delivers; a required field the source sometimes leaves empty makes the create fail |
| Searchable | Mark what visitors search for; leave technical fields out of the index |

Ship the class with your extension as a package, so every environment has the same identifiers
([Packages](packages-import-and-export.md)). The content model for a whole site is a workshop topic
([Content modeling workshops](../leadership/content-modeling-workshops.md)).

## Text formats of the common datatypes

`eZContentFunctions::createAndPublishObject()` and `updateAndPublishObject()` take every value as a string and hand it
to the datatype's `fromString()`; `toString()` gives the same format back. From the kernel's datatypes
(`kernel/classes/datatypes/<type>/<type>type.php`):

| Datatype | Format | Example |
|---|---|---|
| `ezstring`, `eztext`, `ezemail`, `ezisbn` | The text | `Trail shoe 42` |
| `ezinteger`, `ezfloat` | A number | `12`, `19.95` |
| `ezboolean` | `1` or `0` (other text is mapped to 0 or 1) | `1` |
| `ezdate`, `ezdatetime` | A Unix timestamp | `1793404800` |
| `ezprice` | `price\|VAT type id\|VAT included (1 or 0)` | `119.00\|1\|1` |
| `ezurl` | `address\|link text` (unsafe schemes are refused) | `https://example.com/manual.pdf\|Manual` |
| `ezselection` | Option names, separated by `\|` | `Red\|Blue` |
| `ezkeyword` | Keywords separated by commas | `trail,running` |
| `ezobjectrelationlist` | Object ids separated by `-` | `5678-5679` |
| `ezauthor` | Authors separated by `&`, each `name\|e-mail\|id` | `Ada Example\|ada@example.com\|0` |
| `ezimage` | `path\|alternative text`; the path must be a file inside the installation, its var or temporary directory | `var/import/img/10442.jpg\|Trail shoe, side view` |
| `ezbinaryfile`, `ezmedia` | A file path | `var/import/files/manual-10442.pdf` |
| `ezxmltext` | The stored XML (see below) | `<?xml version="1.0" encoding="utf-8"?><section ...><paragraph>...</paragraph></section>` |

When in doubt, read the datatype's `fromString()`: it is short, and several were hardened in 6.0.15 to refuse invalid
values instead of storing them ([The eZDataType API](../extension-development/part-8-reference/ezdatatype-api.md)).

**Rich text.** `ezxmltext` stores its own XML (`section`, `paragraph`, `header`, `link` ...), not HTML. Convert the
source's HTML to that XML in your import, keep the set of allowed elements small, and escape text. The
`nxc_powercontent` extension, used by the REST create route, accepts HTML for `ezxmltext` attributes
([nxc_powercontent](../../features/6.0/extensions/nxc_powercontent.md)). Extension datatypes such as `eztags` have their
own formats; read their `fromString()`.

## Images and files from a DAM

1. Download the file into a directory below `var/` of the installation (for example `var/import/`), never into the
   web root of another site.
2. Give the path relative to the installation in the attribute value, or pass `storage_dir` once and file names per
   record (`createAndPublishObject()` prefixes `storage_dir` for `ezimage`, `ezbinaryfile` and `ezmedia`).
3. After the import, remove the downloaded copies; the datatype has stored its own.
4. Compare a checksum of the source file before downloading again, so an unchanged image is not re-imported.

For large media libraries, ask whether the site needs the files at all, or only a link to the DAM's delivery URL
(an `ezurl` or text attribute), which avoids copying.

## Relations between imported records

Relations store object ids, which you only know after both objects exist:

1. Import all records of the related class first (categories before products).
2. Resolve each source reference through its remote id: `eZContentObject::fetchByRemoteID( 'pim-category-shoes' )`,
   then use its `id` in the `ezobjectrelationlist` value.
3. If a reference cannot be resolved yet, import without it and set it in a second pass.

## Languages and locations

- **Languages**: one object per record, one translation per language. Pass `language` (for example `ger-DE`) to
  `createAndPublishObject()` for the first language and to `updateAndPublishObject()` for each further one. The
  language must exist in the installation ([Content languages](../../guides/content-languages.md)).
- **Locations**: `parent_node_id` gives the main location. A record that belongs in several places gets more
  locations (`expobject` and the content jobs offer adding locations; in PHP the node operations). Decide whether a
  category change at the source **moves** the object (`eZContentObjectTreeNodeOperations::move()`) or adds a location.
- **Sections and states**: `section_id` on create; object states for workflow status such as "withdrawn".

## A mapping table

Copy this into the interface specification, one table per class:

```text
Class: product (identifier)          Source: ERP article export, file articles.csv
remote_id scheme: erp-product-<ARTNR>
Parent: node <id> "Products" (main location); category as an extra location

Source field   | Attribute     | Datatype   | Transformation              | Owner  | Required
---------------+---------------+------------+-----------------------------+--------+---------
ARTNR          | sku           | ezstring   | as is                       | ERP    | yes
NAME_EN        | name          | ezstring   | trim                        | ERP    | yes
NAME_DE        | name (ger-DE) | ezstring   | translation                 | ERP    | no
PRICE          | price         | ezprice    | "<PRICE>|1|1"               | ERP    | yes
STOCK          | stock         | ezinteger  | as is                       | ERP    | no
IMAGE_URL      | image         | ezimage    | download to var/import/     | DAM    | no
-              | intro         | ezxmltext  | not imported                | editor | no
```

## Related pages

- [Idempotent imports keyed by remote_id](idempotent-imports-keyed-by-remote-id.md)
- [Writing an import script](writing-an-import-script.md)
- [Content classes for builders](../extension-development/part-2-builder/content-classes-for-builders.md)
- [Interface specification template](interface-specification-template.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
