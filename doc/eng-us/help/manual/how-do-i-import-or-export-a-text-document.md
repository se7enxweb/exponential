---
title: How do I import or export a word processor document?
audience: editor
category: manual
order: 497
summary: The OpenOffice.org entry of an item's tree menu imports an OpenDocument text (.odt) as a new published item below it, replaces the item's content with a document as a new version, or exports the item to .odt (and, with a converter on the server, to Word or PDF).
keywords: ezodf, OpenDocument, odt, LibreOffice, OpenOffice, Word, import, replace, export, export PDF, export Word, sections, document import
context: ezodf/import
type: help
---
# How do I import or export a word processor document?

With the *ezodf* extension, a text written in LibreOffice or OpenOffice can become content, and content can go back
out as a document. Headings, paragraphs, lists, tables and images are taken over. An import is published at once, so
check the document before you import it.

## Who can do this

Editors whose role has the policies `ezodf/import` and `ezodf/export`, plus the right to create content below the
chosen item (for an import) or to edit the item (for a replacement).

## Steps

### Import a document as a new item

1. Save the document in OpenDocument format (`.odt`).
2. Open the tree menu of the folder where the new item belongs and choose **OpenOffice.org** > **Import OpenOffice**.
3. Choose the file and, if asked, the language under **Create or update the translation in:**.
4. Click **Import document**.
   The page says the document is imported and as which class. Open the new item and check it.

### Replace an item's content with a document

1. Open the tree menu of the item and choose **OpenOffice.org** > **Replace OpenOffice**.
2. Choose the file and the language, and click **Import document**.
   The document becomes a new published version of the item; the old content stays in its version history.

### Export an item

1. Open the tree menu of the item and choose **OpenOffice.org** > **Export OpenOffice**.
   Your browser downloads an `.odt` file.
2. **Export Word** and **Export PDF** work the same way on sites that run a document converter.

On sites with the website toolbar, the same functions are its **Import**, **Replace** and **Export** buttons.

## Which class an import becomes

A document becomes an **Article** by default: the file name, without extension, becomes the title and the whole text
the body. To fill other fields, or to create another class, divide the document into named **sections** in the word
processor (**Insert** > **Section**) and name each section after a field, for example `title`, `intro` and `body`.
When the section names match all the fields your site lists for a class, the document becomes that class. Ask an
administrator which classes and names your site accepts.

## What can go wrong

- **"Filetype" error.** Only `.odt` is accepted, unless the site runs a converter for Word (`.doc`) files. Save the
  document as `.odt`.
- **Attention: an import publishes at once.** It does not stop at a draft (unless an approval workflow holds it), and
  a replacement does not check whether someone has an open draft of the item. Prepare the document first, and tell
  colleagues who edit the item.
- **Some fields stay empty.** Imports fill text, rich text, date, date and time, image and matrix fields only.
  Fill in keywords, checkboxes and relations by hand.
- **Formatting is lost.** Fonts, colors and page layout are not taken over; the site's design decides the look.
- **Images need alternative texts.** Add them after the import.

## Related pages

- [OpenDocument import and export](../government/building/documents-and-publications/odf-import-and-export.md), the
  settings
- [How do I edit content from the website itself?](how-do-i-edit-from-the-website.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
