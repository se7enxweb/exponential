# PDF export upgrade

Read this page if you upgrade an installation that uses **PDF export** (`content/pdf`, the PDF export pages in the
admin). The export gained a configurable footer, better fonts and image scaling. Upgrading needs two database
columns, and some `pdf.ini` defaults changed. The feature itself is described in [PDF export](../../features/6.0/pdf-export.md).

## In short

| | |
|---|---|
| What changed | `ezpdf_export` has two new columns; six `[PDFGeneral]` defaults are new or changed; `footer.tpl` reads the new footer settings. |
| Who is affected | Every installation that keeps PDF exports. Installations that override `OutputCharset` or `content/pdf/footer.tpl`. |
| How to check | Look for `show_footer` in the `ezpdf_export` table, and for overrides of `OutputCharset` and `footer.tpl`. |
| How to fix | Apply the database update; align `OutputCharset` with `FontEncoding`; update an overridden `footer.tpl`. |

## Step 1: update the database

`ezpdf_export` gains two columns. The statements are in
`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`; the file exists for `mysql`, `postgresql` and `sqlite`.
The same file also holds the OPML and podcast columns. Apply the whole file once, in order, and only the statements
your database does not have yet.

```sql
-- mysql
ALTER TABLE ezpdf_export ADD COLUMN show_footer int(11) NOT NULL DEFAULT 1;
ALTER TABLE ezpdf_export ADD COLUMN footer_text varchar(255) NOT NULL DEFAULT '';
-- sqlite (same two columns; int type is `integer`, text `varchar(255)`)
ALTER TABLE ezpdf_export ADD COLUMN show_footer integer NOT NULL DEFAULT 1;
ALTER TABLE ezpdf_export ADD COLUMN footer_text varchar(255) NOT NULL DEFAULT '';
-- postgresql
ALTER TABLE ezpdf_export ADD COLUMN show_footer integer NOT NULL DEFAULT 1;
ALTER TABLE ezpdf_export ADD COLUMN footer_text character varying(255) NOT NULL DEFAULT '';
```

A clean install gets the columns from `share/db_schema.dba`. Existing exports keep showing the shipped footer
wording (`show_footer=1`, empty text).

## Step 2: review the changed settings

File `settings/pdf.ini`, block `[PDFGeneral]`, scope: installation.

| Key | Before | Now |
|---|---|---|
| `OutputCharset` | `iso-8859-1` | `windows-1252` |
| `Transliterate` | did not exist | `enabled` |
| `LinkUnderline` | did not exist | `enabled` |
| `ImageScaling` | did not exist | `enabled` |
| `MaxImageWidth`, `MaxImageHeight` | did not exist | empty (page size) |
| `FontEncoding` | did not exist | `WinAnsiEncoding` |

- If you override `OutputCharset`, make sure it still agrees with `FontEncoding`.
- If you rely on images drawn at their pixel size, set `ImageScaling=disabled`.

Check your overrides:

```bash
grep -rn "OutputCharset\|ImageScaling\|FontEncoding" settings/override settings/siteaccess extension/*/settings 2>/dev/null
```

No output means you use the shipped defaults.

## Step 3: update an overridden footer template

`design/standard/templates/content/pdf/footer.tpl` reads the export's footer settings. An override of that
template keeps working, but it does not show the new footer wording until it reads `show_footer` and `footer_text`.
Copy those parts from the shipped template into your override.

## Related pages

- [PDF export](../../features/6.0/pdf-export.md)
- [September 2026, first half: 14 September](../../history/2026/2026-09a.md#14-september-pdf-rss-and-the-rad-tools)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- [OPML upgrade](opml.md), which uses the same database update file
