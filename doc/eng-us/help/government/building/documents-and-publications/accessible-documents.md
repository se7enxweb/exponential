---
title: Accessible documents
audience: government, editor, website-creator, project-manager
category: public-sector
order: 70
summary: How public bodies publish accessible documents with Exponential - an HTML summary page for every document, accessible source files, file information in links, alternatives on request - and what Exponential does not check in uploaded files.
keywords: accessible documents, accessible PDF, tagged PDF, PDF/UA, HTML summary, document accessibility, WCAG, EN 301 549, alternative format, file information, link text
type: help
---
# Accessible documents

This page is for editors, website creators and project managers who publish documents. Accessibility rules for
public-sector websites usually cover documents too: in the EU the Web Accessibility Directive,
[Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj), and [EN 301 549](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf)
(clause 10 covers non-web documents); in the United States, [Section 508](https://www.section508.gov/). Exponential
stores and serves the files you upload; it does **not** check whether they are accessible.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [HTML first, documents second](#html-first-documents-second)
2. [Accessible source files](#accessible-source-files)
3. [On the page](#on-the-page)
4. [Alternatives on request](#alternatives-on-request)
5. [What Exponential checks and what it does not](#what-exponential-checks-and-what-it-does-not)

## HTML first, documents second

| Content | Practice |
|---|---|
| Information people need to act | an HTML page, not a PDF |
| A formal document | an HTML summary page (a Publication object) with the document attached |
| A long report | an HTML summary with the main findings; the full report as an accessible PDF |
| A form | an online form; the document as an alternative |

## Accessible source files

Accessibility of a PDF is decided in the program that makes it. In the source (a word processor or layout program):

- real headings (styles), not bold text;
- alternative text for images and charts;
- tables with header rows;
- a set document language and title in the file properties;
- reading order that follows the content;
- sufficient contrast;
- export as a tagged PDF.

Check the result with an accessibility checker of your PDF tool. Exponential's own PDF export is a print convenience,
not a tagged PDF ([PDF export](pdf-export.md#accessibility-limits)).

## On the page

| Practice | Example of the shape |
|---|---|
| Link text says what the document is | "Annual report 2025" rather than "Download" |
| Format and size in or next to the link | "(PDF, 2.4 MB)" ([Files and the File class](files-and-the-file-class.md#download-links)) |
| Language if it differs from the page | "(in <language>)" |
| A summary on the page | the Publication class's summary |

## Alternatives on request

Not every old document can be made accessible at once. Say on the page or in the accessibility statement how to ask for
an accessible version, and answer within a stated time. Accessibility statements are described by W3C:
[Developing an accessibility statement](https://www.w3.org/WAI/planning/statements/). Exponential ships no statement
template ([Accessibility standards](../../../../standards/accessibility.md)).

## What Exponential checks and what it does not

| Checked | Not checked |
|---|---|
| Image fields can require an alternative text ([Content model and editing, 9](../../../../guides/content-model-and-editing.md#9-required-alternative-text-trimmed-text-lines-and-names-made-by-php)) | the contents of uploaded PDF, Word or other files |
| Dangerous file names refused in the editor's upload | whether a PDF is tagged |
| SVG uploads sanitized | reading order, contrast or language of documents |

The expai extension's *Missing alt text* list finds images without alternative text; there is no equivalent for
documents. An inventory of documents with their accessibility status is a spreadsheet task, helped by the sub-items
table's CSV export.

## Related pages

- [Documents and publications](README.md)
- [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md)
- [Testing accessibility](../../../creators/website-creators/testing-accessibility.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
