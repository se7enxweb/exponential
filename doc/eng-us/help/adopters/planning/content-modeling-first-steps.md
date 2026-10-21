---
title: Content modeling first steps for adopters
audience: adopter, project-manager, editor, developer
category: adoption
order: 34
summary: The first steps of designing an Exponential content model during an adoption - inventory, content types and their fields, relations, the content tree, languages and sites, and the mistakes of copying the old system - with a worksheet.
keywords: content model, content classes, content types, fields, attributes, relations, content tree, languages, inventory, workshop, worksheet
type: help
---
# Content modeling first steps for adopters

Exponential's "most notable feature is its fully customizable and extendable content model" (`README.md`). Adopters
benefit most when they design that model for their content instead of copying the old system.

## Contents

1. [Inventory](#inventory)
2. [From inventory to content types](#from-inventory-to-content-types)
3. [Relations, tree and languages](#relations-tree-and-languages)
4. [Mistakes to avoid](#mistakes-to-avoid)
5. [A worksheet](#a-worksheet)

## Inventory

List what you publish today: pages, articles, products, events, people, documents. For each, count the items and note
who writes them, how often, and in which languages.

## From inventory to content types

In Exponential, content types are "user defined content classes" with fields (`README.md`). For each kind of item:

- give it one content type with a clear name;
- list its fields: title, summary, body, image, date, price, relation ...;
- mark which fields are required and which are translated;
- decide which existing content types of a site package you can reuse.

Workshops to do this with stakeholders: [Content modeling workshops](../../leadership/content-modeling-workshops.md).

## Relations, tree and languages

| Decision | Questions |
|---|---|
| Relations | Which items point to others (an article to its author, a product to its category)? |
| The content tree | Where does each kind of item live? Which sections need different permissions? |
| Languages | Which content is translated, and which exists only in one language? ([Languages](../../languages.md)) |
| Sites | Is content shared between sites? ([Multisite and multilingual planning](../../leadership/multisite-and-multilingual-planning.md)) |

## Mistakes to avoid

- One generic "page" type with a large rich text field for everything: you lose structure that lists, search and
  apps could use.
- Fields named after the old system's technical quirks.
- Modeling for the design of today instead of for the content.
- Skipping the editors: they will fill these fields every day.

## A worksheet

```text
Content type:        <name>
Purpose:             <what it is for>
Fields:              <name - type - required? - translated?>
Relations:           <to which types>
Lives in the tree:   <where>
Who creates it:      <role>
Volume:              <items today, per month>
```

## Related pages

- [Planning the adoption](README.md)
- [Content modeling workshops](../../leadership/content-modeling-workshops.md)
- [A proof of concept](../evaluating/proof-of-concept.md)
- [Content migration checklist](../migrating/content-migration-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
