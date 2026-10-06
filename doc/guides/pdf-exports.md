# PDF exports: one PDF document from a part of the content tree

This guide teaches the PDF export pages of the administration interface (Setup > PDF export, `/pdf/list`): what a
PDF export is, what every part of the list and the edit form says, how to create, download, regenerate and remove
an export, and what to do when a PDF is empty or will not generate.

It is for administrators who hand out content as a PDF (a handbook, a product sheet, a recipe collection). Every
figure and message below was checked against the code (`kernel/private/classes/views/pdf/list.php`,
`kernel/private/classes/views/pdf/edit.php`, `kernel/classes/pdfexport/*.php`, `kernel/classes/ezpdfexport.php`)
on the demonstration server (alpha.se7enx.com) on 6 October 2026, on Apache with PHP-FPM and on FrankenPHP.

[Guides](README.md) · Related: [Templates and design](templates-and-design.md),
[Paging, sorting and page sizes](../features/6.0/admin-list-paging.md), [Safe redirects](../features/6.0/safe-redirects.md)

## In short

- A **PDF export** makes one PDF of a **source node**: its name as the first heading, then its attributes. A
  **tree** export adds the nodes below it, at every level, but only those of the **classes** you tick.
- **Generated once**: the PDF is written when the export is saved and again with **Regenerate**; the file is kept in
  `var/<site>/storage/pdf/<file name>` and downloaded from the list. Fast, but it shows the content as it was then.
- **Generated on the fly**: the PDF is made anew for every download. Always current; a large tree takes a while
  every time.
- The list leads with figures, a search, filters and an order, then one card per export with **Download**,
  **Regenerate** and **Edit**. **Remove selected** asks first and says which stored files go.

## 1. Opening the page

Setup > PDF export, or `/pdf/list` in the admin siteaccess. Both pages need the `pdf/edit` policy (Administrator has
it). Downloads go through `/pdf/edit/<id>/generate`, which needs the same policy.

The web server does not serve files from the storage directory except images, so a stored PDF has no public address
of its own. Before 6.0.15 the list linked `var/<site>/storage/pdf/<name>`, which always answered 404; use
**Download**, or publish the PDF as a File object if visitors should get it.

## 2. The list

| Part | What it says |
|---|---|
| Figures | Exports; generated once; generated on the fly; stored files; their size together; exports that need attention |
| Unfinished drafts | New exports that were started and never saved. They are removed once `content.ini [PDFExportSettings] DraftTimeout` (7200 seconds) has passed and somebody starts a new export |
| Find an export | Title, file name, ID, source node or class; every word must match. The search is part of the address (`?search=`) |
| Show | All, Generated once, On the fly, With a stored file, Need attention, each with its count |
| Sort by | Title, Modified, Generated (date of the stored file), File size, ID; a second click reverses |
| Per page | The sizes of `admininterface.ini [PaginationSettings]` (`ItemsPerPage[pdf/list]`, `ItemsPerPageList_pdf_list[]`); your choice is remembered |

Each card shows:

- **Badges**: Generated once or On the fly; File ready or No file; Source missing; Open in the editor by a user who
  has the form open.
- **Source node** with its class and node ID, or "Node N (no longer exists)".
- **Contains**: the source node only, or the source node and the nodes below it of the listed classes.
- **Stored file** with size and date, or "not generated"; for an export on the fly the **Download name**.
- **Front page** and **Modified** (when and by whom).
- **Warnings**: no source node chosen; the source node gone; the file not generated or removed; a file name that
  cannot be used; a tree without classes (only the source node is exported); classes that no longer exist.

**Download** sends the stored file, or makes the PDF at once for an export on the fly. **Regenerate** writes the
stored file anew from the current content and says how large it is; it is greyed out, with the reason, when the
source node is gone. A new file is written under a temporary name first, so a failed run leaves the old file alone.

## 3. Creating or editing an export

**New PDF export** opens the form at `/pdf/edit/<id>`; until you press OK it is a draft nobody else sees in the list.

1. **Title**: the name in the list. It is not printed in the PDF.
2. **Content**: choose the **source node** with **Browse** (what you typed is kept). The panel shows its path,
   class, section and number of children, or says that the node no longer exists. Pick **Node** (the source only) or
   **Tree**, and for a tree tick the classes to include. A node of an unticked class is left out *with everything
   below it*, so tick the folders that hold the content as well as the articles.
3. **Frontpage**: an optional first page with a large and a smaller line of text.
4. **Footer text**: the line beside the page number. Empty uses the default wording; untick for no text.
5. **Export type** and **File name**: Generate once or on the fly. The file name is letters, digits, `.`, `_` and
   `-`, up to 100 characters; `.pdf` is added when it is missing. A stored export's name must not be used by another
   stored export. On the fly, it is the name the download is offered under.

**OK** checks the form. An error is listed at the top with a link to its field and shown under the field:

| Message | Fix |
|---|---|
| Give the export a title. | Type a title |
| Choose the source node with Browse. / The chosen source node no longer exists. | Browse and pick a node |
| Choose at least one class to include below the source node, or export the source node only. | Tick classes, or choose Node |
| The file name must be a name only, without a folder, "..", or a leading dot. / Use only letters, digits, dots, dashes and underscores in the file name. | Rename the file |
| Another stored export already writes a file of that name. | Pick another name |

When everything is right the export is stored and, if generated once, its file is written; the list then says how
large it is, or why it could not be written. Changing the name, or switching to on the fly, deletes the old file.

**Cancel** throws the changes away and goes back to the page the form was opened from, else to the list. If
somebody else has the export open, the form says who and since when; saving replaces their unsaved changes.

## 4. Removing exports

Tick the cards and press **Remove selected**. The confirmation lists each export with its source node and the file
that is deleted with it (name, size, date), and says that the content is not touched. **Remove** removes the
export, a draft somebody has open and the stored file; **Cancel** changes nothing. The list then names what was
removed. Reloading never removes twice.

## 5. Problems

| Symptom | Cause and fix |
|---|---|
| The PDF has only the first page of content | A tree without classes, or the folders between the source and the articles are not ticked. Tick them |
| "Its source node no longer exists" | The node was removed or moved to the trash. Edit the export and Browse for another node, or remove it |
| "The PDF templates wrote no file" | An override of `node/view/pdf.tpl` or `node/view/execute_pdf.tpl` failed. See `var/log/error.log` |
| A stored PDF shows old content | Generated once keeps the file; press Regenerate, or switch to on the fly |
| A large tree times out on the fly | Switch it to Generate once and regenerate it outside busy hours |

## For developers

- `expPDFExportFile`: file name rules (`normalizeName()`, `isSafeName()`, `nameProblem()`), `path()`, `facts()`,
  `remove()` and `send()` (chunked, session closed, buffers dropped; the caller calls `cleanExit()` outside any catch).
- `expPDFExportGenerator`: `render()` (the two templates, as before, with the same variables), `generateFile()`,
  `stream()`, `problemOf()`. `generatePDF()` is kept and calls them.
- `expPDFExportInfo`: the card (`infoOf()`), figures, search, filters, order, unfinished drafts and `remove()`.
- `expPDFExportForm::read()`: the form, with the checks that need the database passed in.
- Tests without a database: `php vendor/bin/phpunit tests/tests/kernel/classes/expPDFExportTest.php`.
- Velocity: the classes are kernel classes; deploy with `exp:velocity deploy --kernel`.
