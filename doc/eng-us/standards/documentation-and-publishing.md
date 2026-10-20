---
title: Documentation and publishing standards
audience: developer, admin
category: standards
order: 110
summary: Which documentation and document format standards Exponential follows (DITA 1.3, CommonMark and GFM, GNU FDL 1.3, GPL v2, EPUB 3.3, EPUB Accessibility 1.1, PDF, OpenDocument, Doxygen), with evidence and gaps.
keywords: documentation, publishing, DITA, DITA 1.3, OASIS, CommonMark, GFM, GitHub Flavored Markdown, Markdown, GFDL, GNU Free Documentation License, GPL, GPL-2.0-or-later, license, EPUB, EPUB 3.3, EPUB Accessibility 1.1, PDF, ISO 32000, PDF/UA, tagged PDF, OpenDocument, ODF, ezodf, Doxygen, API documentation, help
type: reference
---
# Documentation and publishing standards

This page is for developers who write or render documentation for Exponential, and for administrators who need to
know in which formats and under which licenses the help, the manuals and exported content come out. It lists the
documentation, markup, license and document format standards that apply, says for each one what Exponential 6.0.x
actually does, points to the files and tests that show it, and names the gaps.

> **Note** "Complies" on this page means that the code or the files checked do what the standard asks for the part
> Exponential uses. None of the formats below has been certified by an outside body, and no external validator
> (epubcheck, Ace by DAISY, veraPDF, an ODF validator) runs in the project's CI.

## Summary

| Standard | Version | Status | Evidence (short) |
|---|---|---|---|
| DITA (Darwin Information Typing Architecture), information model | 1.3 | **Partly complies** | topic types concept, task, reference, troubleshooting as content classes in `kernel/classes/help/content/exphelpcontentschema.php`; no DITA XML export yet |
| CommonMark + GitHub Flavored Markdown | CommonMark 0.31, GFM 0.29-gfm | **Partly complies** | own renderer `kernel/classes/help/exphelpmarkdownrenderer.php`, a documented subset; raw HTML is not passed through by design; no run of the official spec tests |
| GNU Free Documentation License (documentation) | 1.3 or later | **Complies** | [README-LICENSE.md](../README-LICENSE.md), [LICENSE-GFDL-1.3.txt](../LICENSE-GFDL-1.3.txt), `settings/exphelp.ini [License]`, `tests/tests/kernel/classes/help/expHelpLicenseTest.php` |
| GNU General Public License (code) | 2.0 or later | **Complies** | `LICENSE`, `composer.json` `"license": "GPL-2.0-or-later"`, file headers; wording of headers not uniform |
| EPUB | 3.3 | **Partly complies** | `expHelpContentExport::writeEpub()` writes an EPUB 3 package of a help manual; not checked with epubcheck |
| EPUB Accessibility | 1.1 | **Partly complies** | accessibility metadata written in the package document; not checked with Ace by DAISY |
| PDF (ISO 32000) | files declare PDF 1.3 | **Partly complies** | `lib/ezpdf` writes `%PDF-1.3`; help PDFs embed and subset TrueType fonts with `ToUnicode` maps |
| PDF/UA (ISO 14289), tagged PDF | 1 | **Does not comply** | no structure tree, no `MarkInfo`, no document language in any PDF writer |
| OpenDocument Format (ODF) | 1.0 written | **Partly complies** | `ezodf` extension 6.1.11 imports and exports OpenDocument text, writes `office:version='1.0'` |
| Doxygen API documentation | Doxyfile 1.5.4 format | **Partly complies** | `doc/eng-us/doxygen/Doxyfile` exists, is outdated and not run in CI; its output is excluded from the help |

## DITA 1.3

### What it is

The Darwin Information Typing Architecture (DITA) 1.3 is an OASIS Standard (December 2015) for writing and
publishing topic-based technical documentation. It defines topic types (concept, task, reference, and in 1.3 a
troubleshooting topic), maps that order topics into manuals, and filtering by attributes such as `audience` and
`platform`.

### How Exponential complies

Exponential uses DITA 1.3 as the **information model** of help maintained as content, not as a file format:

- The plan names DITA 1.3 as the documentation standard ([help system plan](../specifications/6.0/help-system.md),
  "Decisions", row 1) and the [help style guide](../specifications/6.0/help-style-guide.md) says that every topic has
  exactly one DITA type.
- `kernel/classes/help/content/exphelpcontentschema.php` maps the content classes to DITA topic types in
  `expHelpContentSchema::TOPIC_TYPES`: `help_concept` to concept, `help_task` to task, `help_reference` to reference,
  `help_troubleshooting` to troubleshooting. `help_manual` and `help_section` are the maps
  (`MAP_CLASSES`; the class description of `help_manual` reads "A manual (DITA bookmap)").
- The fields follow the DITA elements: a task has exactly one numbered list of steps, a troubleshooting topic has
  `condition`, `cause` and `remedy`, every topic has a `shortdesc` and a stable `help_id` (the planned DITA `id`),
  `related` is the planned `related-links`.
- Audiences and platforms are modeled as filter values (`DEFAULT_AUDIENCES`, `DEFAULT_PLATFORMS` in the same class).
- `kernel/classes/help/content/exphelpcontentvalidator.php` checks the machine-checkable rules of the style guide
  (for example the `help_id` pattern); `./console exp:help:content check` prints the findings and exits 1 while an
  error is left.
- Tests: `tests/tests/kernel/classes/help/expHelpContentTest.php` (suite `kernel-classes`).

The documentation pages in `doc/` (such as this one) are Markdown with front matter, not DITA topics. Their `type`
key is a label for the help viewer only.

### Status

**Partly complies.** The topic types and their required parts follow DITA 1.3. There is no DITA XML anywhere: no
topic, map, bookmap or DITAVAL file is written or read.

### Known gaps

- The DITA bookmap export, DITAVAL filtering, DITA-OT processing and the regular DITA export into a git repository
  are planned, not implemented ([help maintained as content](../features/6.0/help-content.md), "E-books": "Not
  yet"). The export command accepts only `--format=epub` or `--format=pdf`
  (`kernel/private/classes/commands/exphelpcontent.php`).
- The help plan also names IEC/IEEE 82079-1:2019 and ISO/IEC/IEEE 26514 as the rules for content. These are writing
  rules; nothing checks them automatically. **Unverified.**

## CommonMark and GitHub Flavored Markdown

### What it is

CommonMark is a strongly specified Markdown syntax with a test suite (specification version 0.31.2, January 2024).
GitHub Flavored Markdown (GFM, specification 0.29-gfm, GitHub) is a strict superset that adds tables, task lists,
strikethrough, autolinks and the disallowed raw HTML extension.

### How Exponential complies

Every page of the help in the administration is Markdown from `doc/`, rendered on the server by Exponential's own
renderer, `expHelpMarkdownRenderer` (`kernel/classes/help/exphelpmarkdownrenderer.php`). Its class comment documents
the subset:

| Part | Supported |
|---|---|
| CommonMark blocks | ATX and setext headings, paragraphs, block quotes, ordered and bullet lists, fenced and indented code, thematic breaks, link reference definitions |
| GFM extensions | tables with column alignment, task lists, strikethrough, footnotes, autolinked URLs |
| Callouts | GitHub alerts (`> [!NOTE]`), `> **Note** ...` style quotes, and `:::tip Title ... :::` blocks; kinds note, tip, important, warning, caution |
| Headings | ids with GitHub's slugs, so a link written for GitHub lands on the same heading; a table of contents |
| Code | highlighted on the server (`kernel/classes/help/exphelphighlighter.php`) |
| Front matter | flat YAML at the top of the page (`kernel/classes/help/exphelpfrontmatter.php`) |

Raw HTML is never passed through. The renderer escapes the document's characters and emits only its own markup. A
short allowlist of inline formatting elements without attributes (`b`, `strong`, `i`, `em`, `code`, `kbd`, `sub`,
`sup` and a few more), `<a>` and `<img>` through the link and image policies, `<details>`/`<summary>` and
`<abbr title>` are rebuilt by the renderer; HTML comments are dropped; every other tag (`<script>`, `<style>`,
`<iframe>`, `on*` attributes) is shown as text. Links other than http(s), mailto, page anchors and known relative
paths (for example `javascript:` or `data:`) are shown as text.

Tests: `tests/tests/kernel/classes/help/expHelpMarkdownRendererTest.php` (13 test methods, among them
`testRawHtmlIsHarmless`, `testBadLinksDropped`, `testMarkdownSubset`, `testPathologicalInputs` and
`testEveryDocPageRendersSafely`). `./console exp:help check` (`kernel/classes/help/exphelplinkcheck.php`) reports
missing link targets, unsafe links and unknown anchors in the Markdown pages.

### Status

**Partly complies.** The renderer covers the block and inline constructs the documentation uses and handles
untrusted input safely. It is not a complete CommonMark implementation and is not tested against the CommonMark or
GFM specification test suites.

### Known gaps

- No run of the official CommonMark or GFM spec examples; conformance to the edge cases of emphasis, list and link
  parsing is **Unverified**.
- HTML blocks and most inline HTML are shown as text instead of passed through. This deviates from CommonMark on
  purpose (safety); GFM's own "disallowed raw HTML" extension filters only a fixed list of tags.
- Callouts with `:::` fences are not GFM and do not render on GitHub.
- Documents over 4,000,000 bytes or nested deeper than 16 levels are shown as plain text
  (`expHelpMarkdownRenderer::MAX_LENGTH`, `MAX_DEPTH`).

## GNU Free Documentation License 1.3

### What it is

The GNU Free Documentation License (GFDL), version 1.3 of 3 November 2008, published by the Free Software
Foundation, is a copyleft license for manuals and other documentation.

### How Exponential complies

- [README-LICENSE.md](../README-LICENSE.md) carries the copyright and permission notice: version 1.3 "or any later
  version", with no Invariant Sections, no Front-Cover Texts and no Back-Cover Texts.
- [LICENSE-GFDL-1.3.txt](../LICENSE-GFDL-1.3.txt) is the unmodified license text.
- `settings/exphelp.ini [License]` drives the notice the help shows on every page, the dashboard, the print view and
  the PDF: `Name=GNU Free Documentation License`, `Version=1.3`, `OrLater=true`, `TextPage=LICENSE-GFDL-1.3.txt`,
  `NoticePage=README-LICENSE.md`, `ExplainerPage=help/free-documentation.md`. The logic is in
  `kernel/classes/help/exphelplicense.php`.
- Inherited pages keep their own terms. `[License] OtherLicensePathRules[]` lists the documentation inherited from
  the code base of eZ Publish 4 (`doc/eng-us/features/[2-5].*`, `doc/eng-us/bc/[2-5].*`, `doc/eng-us/specifications/[2-5].*`,
  `doc/eng-us/specifications/trunk/*`, `doc/changelogs/[2-5].*`, `doc/eng-us/shop/*` and others). For those, the help shows
  `OtherCopyright` and `OtherLicenseName=GNU General Public License, version 2` instead of the GFDL notice; the
  project does not relicense them.
- Readers get a plain explanation in [Free documentation](../help/free-documentation.md).
- Tests: `tests/tests/kernel/classes/help/expHelpLicenseTest.php`, among them `testLicenseTextIsTheOfficialText`,
  `testSettings`, `testTemplatesCarryTheLicenseBlock` and `testPagesAndTheirLicense`.

### Status

**Complies.**

### Known gaps

- The notice is added by the help viewer, not written into each Markdown file. A page copied out of the repository
  by itself carries no notice; [README-LICENSE.md](../README-LICENSE.md) is the record.

## GNU General Public License, version 2 or later (code)

### What it is

The GNU General Public License, version 2 (June 1991, Free Software Foundation), is the copyleft license of the
source code. "Or later" lets a recipient follow the terms of any later GPL version as well.

### How Exponential complies

- `LICENSE` at the repository root states that the source code is provided under the GNU General Public License,
  version 2, and contains the full license text. `COPYRIGHT.md` holds the copyright notices.
- `composer.json` declares `"license": "GPL-2.0-or-later"` (SPDX identifier).
- New kernel files carry `@license GNU General Public License v2.0 (or any later version)` (about 680 files in `kernel/`
  and `lib/` at the time of writing); most inherited files point to the `LICENSE` file instead ("For full copyright
  and license information view LICENSE file distributed with this source code").
- Extensions declare their license in `ezinfo.php` and `extension.xml` (for example `ezodf`:
  "GNU General Public License v2.0 (or any later version)").

### Status

**Complies.**

### Known gaps

- The wording is not uniform. The historical header template `doc/eng-us/standards/header.txt` says "version 2.0" without
  "or later", and `LICENSE` itself does not repeat the "or later" grant that `composer.json` makes.
- Third-party code bundled inside `lib/` keeps its own notices; a full license inventory of bundled code was not
  made for this page. **Unverified.**

## EPUB 3.3 and EPUB Accessibility 1.1

### What it is

EPUB 3.3 is a W3C Recommendation (May 2023) for e-books: a ZIP container (OCF) with a package document, XHTML
content documents and a navigation document. EPUB Accessibility 1.1 (W3C Recommendation, May 2023) defines the
accessibility metadata and the conformance requirements for EPUB publications, based on WCAG.

### How Exponential complies

An EPUB export exists for **manuals of help maintained as content** (not for arbitrary content):

- `expHelpContentExport::writeEpub()` in `kernel/classes/help/content/exphelpcontentexport.php`, pure PHP on
  `ZipArchive`. Command (one line):

  ```bash
  ./console exp:help:content export -s <admin siteaccess> --manual=<remote id> --format=epub [--output=file]
  ```

  In the help, **Download EPUB** on the manual's page
  (`kernel/private/classes/views/exphelp/manual.php`). See [help maintained as content](../features/6.0/help-content.md),
  "E-books".
- Container: the `mimetype` file comes first and is stored uncompressed, as OCF requires, followed by
  `META-INF/container.xml`.
- Package and navigation: a package document with `xml:lang`, a `nav.xhtml` with `epub:type="toc"` and
  `role="doc-toc"`, XHTML content documents; only Released chapters and topics the user may read are included.
- Accessibility metadata (`opf()` in the same class): `schema:accessMode` textual and visual,
  `schema:accessModeSufficient` textual, `schema:accessibilityFeature` structuralNavigation, tableOfContents and
  alternativeText, `schema:accessibilityHazard` none, an `accessibilitySummary`, and
  `dcterms:conformsTo` "EPUB Accessibility 1.1 - WCAG 2.2 Level AA". Figures keep their alternative text and caption.
- Tests: `tests/tests/kernel/classes/help/expHelpContentExportTest.php` (`testXhtmlFragment`, `testXhtmlDocument`,
  `testAnchorsAndLanguage` and others).

See [Accessibility standards](accessibility.md) for WCAG itself.

### Status

**Partly complies** (both). The structure and metadata the standards ask for are written; no generated book has been
validated.

### Known gaps

- epubcheck and Ace by DAISY are not shipped and not run in CI; the code comment says checking is left to the site.
  Conformance of a generated book is **Unverified**.
- The package element declares `version="3.0"`, which is the value EPUB 3.3 still uses; the export has not been
  checked against EPUB 3.3's newer requirements.
- The `dcterms:conformsTo` claim is written for every book; it is not derived from a check of that book.
- There is no EPUB export of ordinary content or of the `doc/` help pages.

## PDF (ISO 32000) and PDF/UA

### What it is

PDF is standardized as ISO 32000 (ISO 32000-1:2008 for PDF 1.7, ISO 32000-2:2020 for PDF 2.0). PDF/UA (ISO 14289)
adds requirements for accessible, tagged PDF.

### How Exponential complies

Two PDF writers exist, both on the kernel's own library `lib/ezpdf` (`lib/ezpdf/classes/class.pdf.php`):

- **Help pages and help manuals.** "Download as PDF" of a help page (`exphelp/pdf/<page id>`,
  `./console exp:help pdf <page id>`; `kernel/classes/help/exphelppdf.php`, `exphelppdfwriter.php`,
  `exphelppdfcanvas.php`) and the PDF of a manual (`expHelpContentExport::writePdf()`). Nothing is fetched from the
  network and no outside program runs. Settings: `settings/exphelp.ini [PdfSettings]` (`Enabled`, `PageSize` A4 or
  Letter, `FontFiles[]`, `BoldFontFiles[]` and the other font lists).
  - Fonts: a TrueType font is embedded as a composite font (`/Encoding /Identity-H`) with a `ToUnicode` map, so text
    stays selectable and searchable, and is cut down to the glyphs used
    (`kernel/classes/help/exphelppdffontsubset.php`). A fallback font takes characters the main font lacks. Without
    any TrueType font the PDF core fonts are used and text is transliterated to Windows-1252.
  - The document title is set (`addUnicodeInfo( 'Title', ... )`), headings get named destinations so in-page links
    work, links are clickable.
  - Tests: `tests/tests/kernel/classes/help/expHelpPdfTest.php` (`testOutputIsAPdfWithTitleAndText`,
    `testTrueTypeEmbeddedAndSubset`, `testRightToLeft` and others).
- **Content PDF exports.** Setup > PDF export (`/pdf/list`), one PDF from a part of the content tree
  (`kernel/classes/ezpdfexport.php`, `kernel/classes/pdfexport/`, `settings/pdf.ini`). See
  [PDF exports](../guides/pdf-exports.md).

### Status

- PDF: **Partly complies.** The files declare `%PDF-1.3` (`lib/ezpdf/classes/class.pdf.php`). PDF 1.3 predates ISO
  32000 and its features are a subset of what ISO 32000-1 covers, so conforming readers open the files; the writers
  do not target ISO 32000-1 or -2 explicitly, and no file has been checked with a validator.
- PDF/UA and tagged PDF: **Does not comply.** No PDF writer in the kernel writes a structure tree
  (`StructTreeRoot`), `MarkInfo`, a document language (`/Lang`) or XMP metadata; a search of `lib/ezpdf`,
  `kernel/classes/help` and `kernel/classes/pdfexport` finds none of them.

### Known gaps

- PDFs are not tagged, so screen readers get no reading order or heading structure. Readers who need accessible
  output should use the EPUB export or the help in the browser.
- No PDF/A profile (archiving).
- Arabic letters are drawn in their isolated forms, without contextual shaping (class comment of `expHelpPdfWriter`).
- The help PDF does not write a bookmark outline; `lib/ezpdf` writes an empty `/Outlines` object.

## OpenDocument Format

### What it is

The Open Document Format for Office Applications (ODF) is an OASIS Standard, also published as ISO/IEC 26300. ODF
1.0 dates from 2005; later versions are 1.1, 1.2 and 1.3.

### How Exponential complies

The `ezodf` extension (package `se7enxweb/ezodf`, required by the root `composer.json` as `~6.1.11`; it lives in
`extension/ezodf/` of an installation, not in the kernel repository) imports OpenDocument text files as content and
exports content as OpenDocument text:

- Modules `ezodf/import` and `ezodf/export`; the export answers with `application/vnd.oasis.opendocument.text`.
- `classes/ezoogenerator.php` writes the meta, settings and styles parts with the OASIS namespaces
  (`urn:oasis:names:tc:opendocument:xmlns:office:1.0`) and `office:version='1.0'`.
- Uploaded files are checked as ZIP archives before they are read (`tests/eZOOImportArchiveTest.php`,
  `eZOOImportZipSlipTest.php` in the extension).
- Conversion of Word `.doc` uploads and exports to PDF and Word needs an external conversion daemon
  (`settings/odf.ini.append.php`, `OOConverterAddress`, `OOConverterPort`); it is disabled by default.

### Status

**Partly complies.** Documents are written as ODF 1.0, not as a current version, and no generated file has been
checked with an ODF validator.

### Known gaps

- ODF 1.0 output; ODF 1.2 and 1.3 features (for example digital signatures and the RDF metadata of 1.2) are not
  written.
- The extension's tests are not part of the kernel repository's CI. **Unverified** whether they run anywhere.

## Doxygen API documentation

### What it is

Doxygen is a widely used open source generator of API documentation from source comments. It is a tool, not a formal
standard; it is listed here because it defines how the API reference would be produced.

### How Exponential complies

- `doc/eng-us/doxygen/Doxyfile` configures Doxygen for the kernel: `PROJECT_NAME = "Exponential "`,
  `OUTPUT_DIRECTORY = doc/generated`, `INPUT` from `lib/ezdb/classes`, `lib/ezpdf/classes`, `lib/ezsoap/classes`,
  `lib/eztemplate/classes`, `kernel/classes`, `kernel/common` and other `lib/` directories.
- The help never serves Doxygen output: `settings/exphelp.ini [IndexSettings] ExcludePatterns[]=doc/eng-us/doxygen/*` and the same
  default in `kernel/classes/help/exphelpallowlist.php`; `kernel/classes/help/exphelpcontextregistry.php` refuses
  page ids under `doxygen/`.

### Status

**Partly complies.** A configuration exists but is out of date, and no API reference is generated or published by
the project.

### Known gaps

- The Doxyfile is in the format of Doxygen 1.5.4 and still says `PROJECT_NUMBER = 5.90.0alpha1`.
- `INPUT_ENCODING = latin1`, while the sources are UTF-8.
- Two input directories no longer exist (`lib/ezwebdav/classes`, `lib/ezxml/classes`).
- No CI workflow runs Doxygen (`.github/workflows/` was searched).

## Related pages

- [The license of this documentation](../README-LICENSE.md) and [Free documentation](../help/free-documentation.md)
- [Help system plan](../specifications/6.0/help-system.md) and [Help style guide](../specifications/6.0/help-style-guide.md)
- [Help maintained as content](../features/6.0/help-content.md) and [The help system](../features/6.0/help-system.md)
- [PDF exports](../guides/pdf-exports.md)
- [Accessibility standards](accessibility.md)
