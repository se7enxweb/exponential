---
title: Annual publications page outline
audience: non-profit, businesses, editor, website-creator
category: business-solutions
order: 50
summary: An outline to copy for the page where a non-profit publishes its annual review, financial statements, filings and governance documents on Exponential - the structure by year, what each entry shows (title, year, format, size, a short summary), accessibility, corrections and a checklist for the yearly update.
keywords: annual review, annual publications, financial statements, accounts, filings, governance documents, page outline, downloads, file class, by year, PDF, accessibility, transparency
type: help
---
# Annual publications page outline

Copy this outline when you build the page where supporters, funders and regulators find your annual review, your
financial statements and your governance documents. How to publish the files themselves is explained in
[Annual publications as downloads](../transparency/annual-publications-as-downloads.md).

## Contents

1. [The structure](#the-structure)
2. [The landing page](#the-landing-page)
3. [One entry per document](#one-entry-per-document)
4. [Accessibility](#accessibility)
5. [Corrections](#corrections)
6. [Yearly update checklist](#yearly-update-checklist)

## The structure

A folder per year keeps addresses stable and the archive tidy:

```text
About us
  Accountability                  <- the landing page
    2026                          <- one folder per year
      Annual review 2026          (file)
      Financial statements 2026   (file)
      Statement of activities     (file or article)
    2025
      ...
    Governance                    <- documents that are not tied to a year
      Articles / bylaws           (file)
      Board members               (article)
      Board meetings              (folder, see Board and minutes pages)
      Policies                    (folder of files)
```

Content tree advice: [Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md).
Board pages: [Board and minutes pages](../transparency/board-and-minutes-pages.md).

## The landing page

| Part | Content |
|---|---|
| Introduction | Two or three sentences: who you are, what you publish here and why |
| Latest | The newest annual review and financial statements, at the top |
| Archive | Earlier years, newest first |
| Governance | Bylaws, board, policies |
| Registration | Your legal name and the registration details your law requires |
| Contact | Who answers questions about these documents |
| Machine-readable feed | An RSS link for new publications, if you offer one ([Open data, RSS and REST](../transparency/open-data-rss-and-rest.md)) |

## One entry per document

Each File object (class `file`, datatype `ezbinaryfile`) is downloaded through `content/download`, which checks that
the visitor may read it ([Files and downloads](../../creators/website-creators/files-and-downloads.md)). In the list,
show for each document:

| Field | Example (replace) |
|---|---|
| Title | Annual review 2026 |
| Period covered | January to December 2026 |
| Format and size | PDF, 2.4 MB |
| Summary | One or two sentences of what is inside |
| Date published | |
| Other versions | HTML summary, other languages |

Tell visitors the format and size before they click (same source).

## Accessibility

- An accessible PDF, or an HTML version of the key content next to it
  ([Documents and PDFs](../../government/accessibility/documents-and-pdfs.md)).
- Tables in an HTML summary with proper headers ([Tables in content](../../government/accessibility/tables-in-content.md)).
- Link text that names the document, not "click here" ([Link text](../../government/accessibility/link-text.md)).
- Exponential can also turn content into PDF files at **Setup > PDF export**
  ([PDF export](../../../features/6.0/pdf-export.md)); check the result for accessibility like any other PDF.

## Corrections

- Do not replace a published financial document silently. Publish a corrected file with "corrected" in the title and
  a short note of what changed and when.
- Exponential keeps earlier versions of each object ([Versions of an object](../../../guides/content-history.md)),
  and the audit trail records who changed what ([Versions and the audit trail](../transparency/versions-and-the-audit-trail.md)).
- See [Withdrawing and correcting published information](../../government/transparency-and-foi/withdrawing-and-correcting-published-information.md).

## Yearly update checklist

- [ ] New year folder created.
- [ ] Annual review and financial statements uploaded, with format and size shown.
- [ ] Accessible PDF or HTML summary checked.
- [ ] "Latest" on the landing page updated.
- [ ] Governance documents still current (board list, bylaws, policies).
- [ ] Giving page links to the new annual review ([The giving page](../donations/the-giving-page.md)).
- [ ] Old links still work.

## Related pages

- [Templates and worksheets](README.md)
- [Transparency](../transparency/README.md)
- [Proactive disclosure](../../government/transparency-and-foi/proactive-disclosure.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
