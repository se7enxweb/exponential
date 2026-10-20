---
title: Modeling catalog records
audience: businesses, website-creator, editor
category: business-solutions
order: 20
summary: How to turn a library or archive record into an Exponential content class - one class per record type, the datatypes for titles, identifiers, dates, creators, subjects and files, relations between records and people, required and translatable fields, and changing the model safely later.
keywords: catalog record, content class, datatypes, ezisbn, ezobjectrelationlist, ezdate, ezselection, ezmatrix, metadata, relations, authors, record type, class design
type: help
---
# Modeling catalog records

This page is for the cataloging lead and the website creator who agree on how records are stored. It shows how a
typical record maps onto a content class, which shipped datatype fits each field, and how records relate to people and
collections. The general rules are in
[Designing your content model](../../creators/website-creators/designing-your-content-model.md); this page applies them
to catalogs.

## Contents

1. [Classes, objects and fields](#classes-objects-and-fields)
2. [A record field by field](#a-record-field-by-field)
3. [People and organizations as their own objects](#people-and-organizations-as-their-own-objects)
4. [Collections and series](#collections-and-series)
5. [Required, searchable and translatable](#required-searchable-and-translatable)
6. [Names and addresses of records](#names-and-addresses-of-records)
7. [Changing the model later](#changing-the-model-later)
8. [Review checklist](#review-checklist)

## Classes, objects and fields

In Exponential each kind of record is a **content class** (for example "Book"), each record is a **content object**
of that class, and each field is an **attribute** with a **datatype** that decides what it stores and how it is edited
([Content classes for websites](../../creators/website-creators/content-classes-for-websites.md)). The product ships 35
datatypes ([Choosing datatypes](../../creators/website-creators/choosing-datatypes.md)); extensions add more, such as
the tags field of `eztags`.

## A record field by field

A worked mapping for a book record. Adapt the identifiers to your own rules.

| Record field | Datatype | Why |
|---|---|---|
| Title | `ezstring` (Text line) | Short text, used in the object name |
| Subtitle | `ezstring` | Optional |
| ISBN | `ezisbn` | A shipped datatype for ISBNs ([Choosing datatypes](../../creators/website-creators/choosing-datatypes.md#the-shipped-datatypes)) |
| Other identifiers (call number, inventory number) | `ezstring` or `ezidentifier` | Not translatable |
| Creators (authors, editors) | `ezobjectrelationlist` | Several related Person objects, in order |
| Publisher | `ezobjectrelation` or `ezstring` | A relation if you describe publishers, a text line if not |
| Date of publication | `ezdate` | Sortable and filterable; a year-only field can be an `ezinteger` |
| Language of the item | `ezselection` | A fixed list editors choose from |
| Format (print, e-book, audio) | `ezselection` | A fixed list |
| Subjects | tags field of `eztags` | Controlled headings ([Tagging and subject headings](tagging-and-subject-headings.md)) |
| Summary or abstract | `ezxmltext` (Rich text) or `eztext` | Rich text when editors need paragraphs and links |
| Cover image | `ezimage` | With required alternative text |
| Digitized file | `ezbinaryfile` | Downloaded through `content/download` ([Files and digital collections](files-and-digital-collections.md)) |
| External link (catalog entry in the library system) | `ezurl` | A link to the master record |
| Table of contents or extra data | `ezmatrix` | A table of values, when the values are data |

The "choosing by need" table of the website creator's guide gives the same choices with alternatives, for example
"A date to sort or filter by: `ezdate` or `ezdatetime`" and "Several related items (authors, related articles):
`ezobjectrelationlist`" ([Choosing datatypes](../../creators/website-creators/choosing-datatypes.md#choosing-by-need)).

> **Tip**
> Ask "will anyone sort, filter or browse by this?" for each field. If yes, give it a datatype that stores a value
> (a date, a selection, a relation, a tag), not free text.

## People and organizations as their own objects

Model creators as their own class (Person, Organization) and relate records to them. Then:

- one person page lists everything they wrote, through reverse relations ("everything that points to this object",
  [Relations and reuse](../../creators/website-creators/relations-and-reuse.md#reverse-relations));
- a correction to a name is made once;
- the person can carry their own fields (dates, biography, portrait).

Relation fields can limit which classes may be chosen, so editors can only pick a Person in the Creators field
(datatype options, [Content classes for websites](../../creators/website-creators/content-classes-for-websites.md#attribute-settings)).

## Collections and series

A collection, series or shelf is a **container**: a class whose objects have children. Records then live below
their collection in the content tree, which gives the menu and the URLs
([Designing your content model, Containers and children](../../creators/website-creators/designing-your-content-model.md#containers-and-children)).
A record that belongs to several collections can have additional locations, or be related from each collection
instead. Topics that cut across collections belong in tags, not in the tree
([Tagging with eztags](../../creators/website-creators/tagging-with-eztags.md#when-to-use-tags)).

## Required, searchable and translatable

| Setting | Use in a catalog |
|---|---|
| Required | Title, at least one creator or a "creator unknown" rule, date; the record cannot be published while a required field is empty |
| Searchable | Title, subtitle, creators, summary, identifiers readers type; not internal notes |
| Translatable | Titles and summaries if you describe in several languages; not ISBN, dates or call numbers |
| Category | Group fields into tabs: Description, Identifiers, Files, Internal |

Source: [Content classes for websites, Attribute settings](../../creators/website-creators/content-classes-for-websites.md#attribute-settings).
Image fields can require an alternative text, which helps accessibility of covers and scans.

## Names and addresses of records

The **object name pattern** builds each record's name from its fields, for example `<title>` or
`<short_title|title>` (the first that is not empty). The **URL alias name pattern** builds the address; empty means
the object name ([Content classes for websites, Class settings](../../creators/website-creators/content-classes-for-websites.md#class-settings)).
Readable addresses help readers and search engines; see
[URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md).

## Changing the model later

Some changes are safe on a live catalog, others are not
([Changing a class on a live site](../../creators/website-creators/content-classes-for-websites.md#changing-a-class-on-a-live-site)):

| Change | Safe? |
|---|---|
| Add a field | Yes; existing records get an empty value |
| Rename a field's label | Yes |
| Change a field's identifier | Risky; every template using it must change at once |
| Remove a field | Destructive; the data is deleted from every record |
| Change a datatype | Not possible in place: add a new field, migrate, remove the old one |

Large classes take a while to change because every object gets the new attribute; change them outside busy hours and
with a backup (same page). Build and test class changes on a development copy and move them as a package.

## Review checklist

- [ ] One class per record type, not per subject.
- [ ] Every field that is sorted, filtered or browsed has a value datatype.
- [ ] Creators and publishers are objects if you describe them; relation fields limited to their classes.
- [ ] Subjects are a tags field with a controlled vocabulary.
- [ ] Required, searchable and translatable set on purpose for every field.
- [ ] Object name and URL alias patterns give readable names.
- [ ] A sample of real records entered and reviewed by catalogers before the import.

## Related pages

- [Libraries and catalogs](README.md)
- [Catalog planning](catalog-planning.md)
- [Designing your content model](../../creators/website-creators/designing-your-content-model.md)
- [Content model and editing (guide)](../../../guides/content-model-and-editing.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
