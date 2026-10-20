---
title: Add a related documents list
audience: government, editor
category: public-sector
order: 200
summary: For editors - add a list of related documents or pages to a page with a relation field (Add existing, Upload new) or embeds in the text, so each document exists once and every page that needs it points to it.
keywords: related documents, relations, Object relations, Add existing, Upload new, embed, related files, reverse relations, documents list, reuse
type: help
---
# Add a related documents list

This page is for editors who want a "Documents" or "See also" list on a page. Exponential reuses content by relations:
a document is one File object, and every page that needs it points to it
([Relations and reuse](../../../creators/website-creators/relations-and-reuse.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps with a relation field](#steps-with-a-relation-field)
3. [Steps with embeds in the text](#steps-with-embeds-in-the-text)
4. [Check the result](#check-the-result)
5. [Pitfalls](#pitfalls)

## Before you start

- Check whether the page's class has a relation field (for example **Related documents**, **Documents** or
  **Attachments**); the field type is *Relation*. If not, use embeds in rich text, or ask the web team to add a field.
- Know whether the documents already exist on the site (search first).

## Steps with a relation field

1. Open the page, **Edit**.
2. In the relation field:
   - **Add existing**: browse or search for the document, select it, confirm;
   - **Upload new**: choose a file from your computer; it becomes a File object and is related.
3. Repeat for each document; the order in the field is the order on the page (move items if the field allows it).
4. **Send for publishing**.

## Steps with embeds in the text

1. In a rich text field, place the cursor where the link should be.
2. Use **Embed object** (or the link dialog) and pick the document; choose a view that shows it as a link.
3. Embeds create relations too, so the document's reverse relations list this page.

## Check the result

- [ ] Signed out, the list shows each document with its name, format and size.
- [ ] On a document, the **Relations** tab, **Reverse related objects**, lists the pages that use it.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Uploading the same PDF again for each page | search and use **Add existing** |
| Removing a document still related elsewhere | the remove confirmation lists the pages; fix them first |
| Link text "Document 1" | give File objects real titles |
| Relations to hidden or restricted content | visitors see a missing item; relate public content |

## Related pages

- [Publishing tasks](README.md)
- [Upload a PDF report with an accessible summary](upload-a-pdf-report-with-an-accessible-summary.md)
- [Creating and editing content: relations and images](../../../editing-content.md#relations-and-images)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
