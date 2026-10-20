---
title: Modeling services
audience: government, website-creator, project-manager
category: public-sector
order: 20
summary: How to create a Service content class in Exponential's class editor, with the attributes, datatypes, required flags and translation settings that make every service page consistent, and how to test it.
keywords: service class, content class, class editor, attributes, datatypes, required, translatable, object name pattern, ezstring, eztext, ezxmltext, ezurl, ezobjectrelation, eztags, ezdate
type: help
---
# Modeling services

This page is for website creators and project managers who create the content class for service pages. One class
for all services keeps the pages consistent, makes lists and search predictable, and lets editors fill a form
instead of designing a page. The class editor is described in
[Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md); the datatypes
named are those of `kernel/classes/datatypes/` and the eztags extension.

## Contents

1. [The attributes](#the-attributes)
2. [Creating the class](#creating-the-class)
3. [Settings worth deciding](#settings-worth-deciding)
4. [Testing the class](#testing-the-class)
5. [Variations](#variations)

## The attributes

| Attribute | Identifier | Datatype (class editor name) | Required | Translatable |
|---|---|---|---|---|
| Service name | `title` | Text line (`ezstring`) | yes | yes |
| In short | `summary` | Text block (`eztext`) | yes | yes |
| Who can use it | `eligibility` | XML block (`ezxmltext`) | no | yes |
| What you need | `requirements` | XML block | no | yes |
| What it costs | `cost` | XML block | no | yes |
| How to apply | `how_to_apply` | XML block | yes | yes |
| Apply online | `online_link` | URL (`ezurl`) | no | yes |
| How long it takes | `processing_time` | Text line | no | yes |
| What happens next | `next_steps` | XML block | no | yes |
| Contact | `contact` | Object relation (`ezobjectrelation`) | no | no |
| Related documents | `documents` | Object relations (`ezobjectrelationlist`) | no | no |
| Topics and life events | `tags` | Tags (`eztags`) | no | no (tags have their own translations) |
| Next review | `review_date` | Date (`ezdate`) | yes | no |
| Meta data | `metadata` | Metadata (`xrowmetadata`) | no | yes |

Object name pattern `<title>`. The `xrowmetadata` and `eztags` datatypes come from extensions that the default
installation activates ([Extensions it activates](../../../site-packages/sevenx-multisite/extensions-it-activates.md)).

## Creating the class

1. **Setup > Classes**, group **Content**, **New class**.
2. Name `Service`, identifier `service`, object name pattern `<title>`.
3. For each attribute: choose the datatype in **Add attribute**, press **Add**, fill name and identifier, tick
   **Required** and **Translatable** as in the table, **Searchable** for text attributes.
4. For **Contact**, set the relation's allowed class to your Contact point class (see
   [Contact points and offices](contact-points-and-offices.md)); for **Related documents**, allow File.
5. For **Next review**, the Date datatype can default to empty; keep it required so editors must set it.
6. Press **OK**. Check: the class appears in the list; create a test service.

## Settings worth deciding

| Setting | Recommendation | Why |
|---|---|---|
| Container | no | services do not hold other content; documents are related, not children |
| Default object availability | as your language policy says | "always available" shows the main language where a translation is missing |
| Attribute order | the reading order of the page | the edit form follows it |
| Category (tabs) of attributes | "Content" and "Management" (contact, tags, review) | a shorter first tab for writers |
| Text line **Trim the stored value** | on for Service name | stray spaces break sorting ([Content model and editing, 9](../../../../guides/content-model-and-editing.md#9-required-alternative-text-trimmed-text-lines-and-names-made-by-php)) |

## Testing the class

- [ ] Create a service with only the required fields: it publishes.
- [ ] Leave **How to apply** empty: the form refuses with a message at the field.
- [ ] Translate it: dates, tags and relations are shared; texts are per language.
- [ ] The full view shows the fields in the right order (see [The service page and plain language](service-page-and-plain-language.md)).
- [ ] Search finds the service by a word from **In short**.

## Variations

| Need | Change |
|---|---|
| Fees that change every year in one place | a separate Fee object related from services, edited once |
| Services in several languages with different legal texts | keep legal texts in related File objects per language |
| Services for businesses and for residents | one class; an Audience selection (`ezselection`) or tags |
| Very simple services | the same class; optional fields left empty, templates hide empty fields |

Move the class to other installations as a package, never by recreating it by hand ([Packages](../../../../guides/packages.md)).

## Related pages

- [Building a service catalog](README.md)
- [The service page and plain language](service-page-and-plain-language.md)
- [Choosing datatypes for your fields](../../../creators/website-creators/choosing-datatypes.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
