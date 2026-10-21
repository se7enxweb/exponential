---
title: The field types it ships
audience: dxp-developer
category: dxp-development
order: 18
summary: Every field type a DXP project has out of the box, with its identifier as read from the installed core and field type packages - text, numbers, dates, media, relations, users, rich text, matrix and content query - and what to use each for.
keywords: field types, ibexa_string, ibexa_text, ibexa_richtext, ibexa_matrix, ibexa_content_query, ibexa_image, ibexa_image_asset, relation, selection, identifiers, fieldtype
type: help
---
# The field types it ships

This page is for developers who design content types. It lists the field types of a DXP project. The identifiers
were read from the `Type` classes of the installed packages (`se7enxweb/exponential-platform-dxp-core`,
`se7enxweb/fieldtype-richtext`, `ibexa/fieldtype-matrix`, `ibexa/fieldtype-query`) on a Platform v5 installation.

## Contents

1. [Text and numbers](#text-and-numbers)
2. [Dates and choices](#dates-and-choices)
3. [Media and files](#media-and-files)
4. [Relations and people](#relations-and-people)
5. [Structured fields](#structured-fields)
6. [Identifiers changed in v5](#identifiers-changed-in-v5)

## Text and numbers

| Field type | Identifier | Use it for |
|---|---|---|
| Text line | `ibexa_string` | Titles, short labels |
| Text block | `ibexa_text` | Plain multi-line text |
| Rich text | `ibexa_richtext` | Formatted body text with embeds (package `se7enxweb/fieldtype-richtext`) |
| Integer | `ibexa_integer` | Counts, ranks |
| Float | `ibexa_float` | Prices without currency logic, measurements |
| Checkbox | `ibexa_boolean` | Yes or no flags |
| Email address | `ibexa_email` | Contact addresses |
| URL | `ibexa_url` | External links with a text |
| ISBN | `ibexa_isbn` | Book numbers, validated |
| Keywords | `ibexa_keyword` | Free tags |

## Dates and choices

| Field type | Identifier | Use it for |
|---|---|---|
| Date | `ibexa_date` | A day |
| Date and time | `ibexa_datetime` | A moment |
| Time | `ibexa_time` | A time of day |
| Selection | `ibexa_selection` | One or more choices from a fixed list |
| Country | `ibexa_country` | Countries |
| Map location | `ibexa_gmap_location` | Latitude, longitude and an address |

`exponential:timestamps:to-utc` converts stored date and time values to UTC
([Console commands](console-commands-and-renames.md)).

## Media and files

| Field type | Identifier | Use it for |
|---|---|---|
| Image | `ibexa_image` | An image stored with the content item, served in variations ([Image variations](image-variations.md)) |
| Image asset | `ibexa_image_asset` | A relation to an image content item in the media library, reusable across content |
| File | `ibexa_binaryfile` | Downloads |
| Media | `ibexa_media` | Audio and video files |

## Relations and people

| Field type | Identifier | Use it for |
|---|---|---|
| Content relation (single) | `ibexa_object_relation` | One related content item |
| Content relations (multiple) | `ibexa_object_relation_list` | Several related items |
| Authors | `ibexa_author` | Names and emails of authors |
| User account | `ibexa_user` | Login, email and password of a user content item |

## Structured fields

| Field type | Identifier | Package | Use it for |
|---|---|---|---|
| Matrix | `ibexa_matrix` | `ibexa/fieldtype-matrix` | A table of rows with named columns |
| Content query | `ibexa_content_query` | `ibexa/fieldtype-query` | A field whose value is the result of a query, for example "the latest five articles below this location" |

## Identifiers changed in v5

On Platform v5 the identifiers start with `ibexa_` (for example `ibexa_string`). Older lines used `ez...` names. If you
import content types or templates from an older project, map the identifiers; the core has a field type alias
registry for this (`FieldTypeAliasRegistry` in the installed core), but write new definitions with the `ibexa_`
names. Check the identifier of any field type in your project with:

```bash
grep -rh "return 'ibexa_" vendor/se7enxweb/exponential-platform-dxp-core/src/lib/FieldType/*/Type.php | sort -u
```

## Related pages

- [Content, content types and locations](content-types-and-locations.md)
- [Twig functions you use daily](twig-functions-you-use-daily.md)
- [Coming from Ibexa OSS or eZ Platform](coming-from-ibexa-oss-or-ez-platform.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
