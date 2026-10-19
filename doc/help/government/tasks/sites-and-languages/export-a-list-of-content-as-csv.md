---
title: Export a list of content as CSV
audience: government, admin, editor
category: public-sector
order: 250
summary: Task for administrators and editors: export the objects of one class below a folder (a register, a list of service points) as CSV, with xrowextract in the administration or the kernel's exp:ezcsvexport, and check it before publishing.
keywords: CSV export, xrowextract, ezcsvexport, exp:ezcsvexport, register export, spreadsheet, open data, one class
type: help
---
# Export a list of content as CSV

This task is for administrators and editors. Background:
[Exporting content as CSV, JSON or XML](../../open-data/exporting-content-as-csv-json-xml.md).

## With xrowextract (when active)

1. **Export** in the top menu (`/xrowextract/csv`), **One class of content export**.
2. Choose the class and the start node; choose CSV; add filters (visible only, a date range) if needed.
3. **Preview**; check the columns and remove personal data columns.
4. Download the file, or save the settings as a preset for next time.

Command line equivalent:

```bash
php extension/xrowextract/bin/php/csv.php --class=<class> --node=<node id> --visibility=visible --output=var/export/list.csv
```

## With the kernel command

```bash
./console exp:ezcsvexport --storage-dir=var/export/csv <node id>
```

One CSV per class below the node; no filters or column choice.

## Check

- Open the file in a spreadsheet and in a text editor: UTF-8, header row, the expected number of rows.
- No columns with owners, creators, e-mail addresses or internal notes, unless intended.

## Problems

| Problem | Fix |
|---|---|
| No **Export** tab | `xrowextract` is not active, or your role lacks its policies |
| Accents garbled in the spreadsheet | import the file as UTF-8 |

## Related pages

- [xrowextract](../../../../features/6.0/extensions/xrowextract.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
