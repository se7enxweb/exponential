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
| Bring rows from a spreadsheet, CSV, XML or JSON file in as content items, once or on a schedule, with a dry run first | **Imports (7x SQLI Import)** | import | a folder, a class | CSV, TSV, XLSX, ODS, XML, JSON, NDJSON, RSS, Atom and many more (see its **Catalogue**) | **Imports** tab, or **Import/Export > Import into this folder…** on a folder |
| Bring content in from another system: a REST API, a database, a WordPress export, a shop | **Imports (7x SQLI Import)** | import | a folder, a class | REST APIs, SQL and NoSQL databases, WordPress WXR, Drupal, Joomla, Shopify, WooCommerce … | **Imports > Catalogue**, **New import** on the card of the format |
| Fill a module's own table (a data table) from a file instead of making content items | **Imports (7x SQLI Import)** | import | data tables | every format of Imports | **Imports > New import**, step Processing: **Rows of a data table** |
| Let another system (a shop, a CRM) start an import when its data is ready | **Imports (7x SQLI Import)** | import | a folder, a class, data tables | the import's own format, sent in the call | the import's step Schedule: **Started by another system (webhook or API)** |
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
| Create or upgrade a module's table on another database, browse its rows, or set up a connection to an external database | **Data tables** | both | data tables, classes | SQL, .dba | **Setup > Data tables** |
| Keep a data table and content of one class in step, once, on a schedule or live, in either direction | **Data tables** (synchronisations) | both | data tables, a class and a folder | (between a table and content) | **Setup > Data tables > Synchronisations**, or the group **Data tables** of the Imports catalogue |

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
| Imports: the fields of an import | Data tables (module table wizard) | **More > New data table from its fields** on an import opens the wizard with the import's columns; the import can then fill the new table. |
| Data tables | Content, and back | A synchronisation copies a table into content or content into a table, or keeps both in step with a conflict rule. |

### Example: change products in a spreadsheet and load them back

1. In the **Export** tab, export the class *Folder* of the products folder as CSV. The file has a column with each
   item's remote ID, and a column per attribute.
2. Open the file in a spreadsheet, correct the names of three products, save it as CSV (UTF-8).
3. Either load it with Export's own **Import**, or, to see a dry run first, open **Imports > New import**, choose the
   format *Export (xrowextract) file* and upload the file. Upload the export's `<file>.manifest.json` with it and the
   mapping is exact; without it, set the option *Class* to *Folder*, and the columns named after its attributes are
   mapped.
4. In the preview step, the dry run lists the three products as **Updated**, with the old and the new name; the
   others are **Unchanged**. **Save and run** publishes only the three changed items, as new versions.

Because the remote ID column finds the same items again, nothing is created twice. If the result is not what you
wanted, **Undo this run** on the run page brings back the previous versions
([Imports and runs](sqli-import/imports-and-runs.md#undo-a-run)).

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

- On every tool's main page, a **Looking for…?** box names the other tools and the ones that read its files. The
  label **Works with this tool** marks the tools whose files this one reads, or that read its files:

  ![The box Looking for another import or export? at the end of the Imports dashboard: Export (xrowextract), RSS export, Collected information export (CIE) and Data tables marked Works with this tool, then Packages, Syndication, Documents, RSS import, Collected information, Newsletter subscribers, Audit export and PDF export, and a link to Which import or export tool do I need?](images/sqli-import/sqli-import-looking-for.webp)
- On an item, the **Import/Export** button lists what you can do with that item: export it, import into it (on a
  folder you may add items to) and copy it to another site.
- On a class page, **Import and export** lists the tools that work on the items of that class.
- The **Setup** menu and the left menu of each tool have an **Import and export** list of all tools you may use.
- The search in the header suggests matching tools when you search for a task such as "import csv" or "copy to
  another site".
- The **?** help menu on a tool's page links back to this page.

## Where your runs are listed

Every run of Imports, Export, CIE and the synchronisations of Data tables is recorded in one run history: who ran
it, when, how long it took, how many records it handled and how it ended. The run list and the **Statistics** page of
Imports have a **Tool** filter: choose Imports, Export, CIE, Data tables or all import and export tools. A run of Export or CIE links to its own page (the job
with its file). Each tool's own pages keep their lists and have a link **All import and export runs**.

You see your own runs, and every user's runs of a tool when your role allows it (for Export, the "all jobs" policy).
Long exports run in the background on the job queue, or on the content jobs when the queue is not set up; you can
leave the page and come back.

## Read more about Imports

How to make an import, try it with a dry run, run it, undo it and schedule it:
[7x SQLI Import: start here](sqli-import/README.md). Its own manual, with a worked example for every format, is in
the help under Extensions (`/exphelp/viewer/ext/sqliimport/index.md`); its page on the other tools is
`/exphelp/viewer/ext/sqliimport/transfer.md`.

## For developers

The tools are listed by the transfer registry of the kernel: each tool declares itself in `kernelservices.ini`, and
every list and link above reads it. See [The transfer registry](../features/6.0/transfer-registry.md). How a tool runs
in the background and records its runs: [Transfer runs](../features/6.0/transfer-runs.md).
