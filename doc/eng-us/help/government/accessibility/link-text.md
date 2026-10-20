---
title: Link text
audience: government, editor, designer
category: public-sector
order: 180
summary: For editors and designers - how to write link texts that make sense on their own, how links to documents and new windows should be announced, and how Exponential's link tools and link management help keep links working.
keywords: link text, links, click here, read more, link purpose, WCAG 2.4.4, new window, target, documents, file size, broken links, link management, link check, editors
type: help
---
# Link text

This page is for editors who write links and designers who build lists and teasers. Screen reader users often list
all links of a page and choose from that list, so "click here" and "read more" twenty times are useless to them.
WCAG 2.2 covers this under 2.4.4 Link purpose (in context) (<https://www.w3.org/TR/WCAG22/>).

## Contents

1. [Rules for editors](#rules-for-editors)
2. [Links to documents](#links-to-documents)
3. [Links that open a new window](#links-that-open-a-new-window)
4. [Rules for designers](#rules-for-designers)
5. [Keeping links working](#keeping-links-working)
6. [Related pages](#related-pages)

## Rules for editors

| Instead of | Write |
|---|---|
| To apply, click here. | Apply for a parking permit (the whole phrase is the link) |
| More information | Opening hours of the citizens' office |
| https://www.example.gov/services/permits/parking | Parking permits |
| Download | Annual report 2025 (PDF, 2 MB) |

- Put the words that say where the link leads into the link.
- Make the same text lead to the same place on one page.
- Do not link whole sentences; link the key words.

## Links to documents

Say the format and size in the link text: "Budget plan (PDF, 1.2 MB)". People on slow connections and screen reader
users then know what will happen. Prefer a web page to a document where you can (see "Documents and PDFs").

## Links that open a new window

The rich text link has a target option; the standard design writes it as the `target` attribute
(`design/standard/templates/content/datatype/view/ezxmltags/link.tpl`). Opening a new window disorients many users.
Use it rarely, and when you do, say so in the text: "Map of the district (opens in a new window)". Exponential's own
help pages follow the same rule and add a note to links that open a new window
([Help system: Languages and accessibility](../../../features/6.0/help-system.md#languages-and-accessibility)).

## Rules for designers

- Teaser lists: make the title the link, not a separate "read more"; if a "read more" is needed, give it hidden text
  with the title (a visually hidden class, as admin4 does with `.a4-sr-only`).
- Links must look like links (not by color alone) and have a visible focus style.
- Icon-only links need an accessible name.

## Keeping links working

- **Setup > Link management** (`/url/list`) lists every external address published content links to, with its
  state (valid, invalid, never checked) and the objects that use it.
- The **link check** (`linkcheck.php`, cronjob part `infrequent`) tests them; its settings are in
  `cronjob.ini [linkCheckSettings]` ([Links, URL aliases, wildcards](../../../guides/urls-and-aliases.md#2-how-links-are-checked)).
- **Broken relations** (`/contentcheck/brokenrelations/<node id>`) finds content that links to or embeds removed,
  hidden or unpublished content ([The content checks](../../../features/6.0/content-checks.md)).

## Related pages

- [Accessible content for editors](accessible-content-for-editors.md)
- [Link and content checks](../../creators/website-creators/link-and-content-checks.md)
- [Editing content](../../editing-content.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
