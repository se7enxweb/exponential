---
title: Patterns in kernel/classes/pdfexport
audience: developer
category: standards
order: 509
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/pdfexport with its design patterns.
keywords: kernel/classes/pdfexport, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/pdfexport

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/pdfexport` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (4)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expPDFExportFile` | class | [Utility (static helper)](utility.md) | `kernel/classes/pdfexport/exppdfexportfile.php:19` | The file of a PDF export that is generated once: its name, where it is kept, what is known about it, sending it and removing it. |
| `expPDFExportForm` | class | [Utility (static helper)](utility.md) | `kernel/classes/pdfexport/exppdfexportform.php:19` | Reads and checks the form of pdf/edit. |
| `expPDFExportGenerator` | class | [Builder](builder.md), [Utility (static helper)](utility.md) | `kernel/classes/pdfexport/exppdfexportgenerator.php:27` | Generates the PDF of an export: into its stored file, or into a temporary file that is then sent and removed. |
| `expPDFExportInfo` | class | [Utility (static helper)](utility.md) | `kernel/classes/pdfexport/exppdfexportinfo.php:19` | What the PDF export pages (pdf/list and its removal confirmation) say about an export beyond its row: how it is produced, whether its source node sti… |
