---
title: Which import or export tool do I need?
audience: editor, admin
category: editing
order: 40
summary: One table from the task to the tool, for every import and export tool of Exponential 6.0.15, with round trips and where each one is found.
keywords: import, export, which tool, csv, xlsx, spreadsheet, excel, package, ezpkg, rss, atom, feed, syndication, copy to another site, move content, backup, form submissions, subscribers, newsletter, word, odt, docx, pdf, audit, re-import, round trip, Imports, Export, CIE
type: help
---
# Which import or export tool do I need?

Exponential has several import and export tools, each made for a different job. This page takes you from what you
want to do to the tool that does it. You do not need to know the tools' names first.

The administration shows you only the tools that are switched on in your installation and that your role lets you
use. So a tool in this table may be missing from your menus: then it is not active here, or ask your administrator.

## Find the tool by task

| I want to… | Tool | Direction | Works on | Formats | Where |
|---|---|---|---|---|---|
| Bring rows from a spreadsheet, CSV, XML or JSON file in as content items | **Imports (7x SQLI Import)** | import | a folder, a class | CSV, TSV, XLSX, ODS, XML, JSON, RSS | **Imports** tab, or **Import/Export > Import into this folder…** on a folder |
| Download the items of one class, a folder or the whole site as a spreadsheet file | **Export (xrowextract)** | export | site, folder, class | CSV, XML, JSON, ZIP archive | **Export** tab, or **Import/Export > Export…** on an item |
| Change many items in a spreadsheet and load the changes back | **Export (xrowextract)** and its import | both | folder, class | CSV, XML, JSON | export, edit the file, then **Export > Import** (or **Re-import this file** in the export's history) |
| Copy a folder with its classes to another Exponential installation, once | **Packages** | both | site, folder, item, class | ezpkg | **Import/Export > This item and its subtree as package…**, **Setup > Packages** |
| Keep a folder in step on another Exponential site, again and again | **Syndication** | both | folder, item | (between sites) | **Syndication** tab, **Import/Export > Copy to another site…** |
| Move class definitions, an extension or a design to another installation | **Packages** | both | class, site | ezpkg | **Setup > Packages > Create**, the class page's **Export classes as a package…** |
| Show news from another website on this one | **RSS import** | import | a folder | RSS, Atom | **Setup > RSS** |
| Offer a folder as a feed for others to subscribe to | **RSS export** | export | a folder | RSS, Atom, OPML | **Setup > RSS** |
| Download what a form collected (contact form, survey) | **Collected information** or **Collected information export (CIE)** | export | form submissions | CSV (CIE: also SYLK and other character sets, scheduled) | **Setup > Collected information**, **CIE** tab |
| Add subscribers to a newsletter list from a file, or download a list | **Newsletter subscribers** | both | subscribers | CSV | **Newsletter** tab, the list's import and export |
| Turn a Word or OpenDocument file into an item, or download an item as a document | **Word and OpenDocument** | both | an item | DOCX, DOC, ODT | **Import/Export > Import a Word or OpenDocument file…** on a folder |
| Print a folder or send it as a document | **PDF export** | export | a folder | PDF | **Setup > PDF export** |
| Hand the audit trail to an auditor | **Audit export** | export | audit records | CSV, JSON lines, JSON | **Audit > Export** |

## Round trips: what reads what

Some exports can be imported again. These pairs are made to work together:

| You export with | You import with | Notes |
|---|---|---|
| Export (xrowextract) CSV, XML, JSON | Export (xrowextract) **Import** | The export's manifest file carries the columns, so the import maps them for you. **Re-import this file** on the export's history page opens the import with the file. |
| Export (xrowextract) CSV | Imports (7x SQLI Import) | Imports has a preset for xrowextract files: the columns are mapped to the fields of the same class. |
| Collected information export (CIE) CSV | Imports (7x SQLI Import) | With the CIE preset, form submissions become content items (for example to publish testimonials). |
| Packages (ezpkg) | Packages | Upload the file in **Setup > Packages** on the other installation and install it. |
| RSS export | RSS import, Imports | One site's feed is another site's import. |
| Imports: mapping and results | Imports | An import's mapping can be downloaded and loaded into another installation; its results can be downloaded as CSV. |

## Which one when two seem to fit

- **Once or again and again?** For a one-off copy to another installation, use **Packages**. To keep two Exponential
  sites in step, use **Syndication**. To fetch the same file or feed on a schedule, use **Imports** (schedules) or
  **Export** (schedules and destinations).
- **Your own data or someone else's?** Files you exported from Exponential go back in best with the tool that made
  them (Export's import, Packages). A file from another system (a shop, a CRM, a spreadsheet someone made) goes in with
  **Imports**, which lets you map its columns to your class's fields and see a preview first.
- **Content or records?** Content items (articles, products, pages) are exported by **Export** and **Packages**.
  Records that are not content items (form submissions, subscribers, audit records) have their own export where
  they are kept.

## Where to find the tools in the administration

- On every tool's main page, a **Looking for…?** box names the other tools and the ones that read its files.
- On an item, the **Import/Export** button lists what you can do with that item: export it, import into it (on a
  folder you may add items to) and copy it to another site.
- On a class page, **Import and export** lists the tools that work on the items of that class.
- The **Setup** menu and the left menu of each tool have an **Import and export** list of all tools you may use.
- The search in the header suggests matching tools when you search for a task such as "import csv" or "copy to
  another site".
- The **?** help menu on a tool's page links back to this page.

## For developers

The tools are listed by the transfer registry of the kernel: each tool declares itself in `kernelservices.ini`, and
every list and link above reads it. See [The transfer registry](../features/6.0/transfer-registry.md).
