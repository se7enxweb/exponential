# PDF export upgrade

## Updated: PDF export footer, fonts and image handling

Feature description: [PDF export](../../features/6.0/pdf-export.md).

### Database

`ezpdf_export` gains two columns. Apply the statements for your engine from
`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` (the file also holds
the OPML and podcast columns; apply the whole file once, in order, and only the
statements your database does not already have):

```sql
-- mysql
ALTER TABLE ezpdf_export ADD COLUMN show_footer int(11) NOT NULL DEFAULT 1;
ALTER TABLE ezpdf_export ADD COLUMN footer_text varchar(255) NOT NULL DEFAULT '';
-- postgresql
ALTER TABLE ezpdf_export ADD COLUMN show_footer integer NOT NULL DEFAULT 1;
ALTER TABLE ezpdf_export ADD COLUMN footer_text character varying(255) NOT NULL DEFAULT '';
```

Clean installs get the columns from `share/db_schema.dba`. Existing exports keep
showing the shipped footer wording (`show_footer=1`, empty text).

### Settings whose defaults changed (`settings/pdf.ini [PDFGeneral]`)

| Key | Before | Now |
|---|---|---|
| `OutputCharset` | `iso-8859-1` | `windows-1252` |
| `Transliterate`, `LinkUnderline`, `ImageScaling` | did not exist | `enabled` |
| `MaxImageWidth`, `MaxImageHeight` | did not exist | empty (page size) |
| `FontEncoding` | did not exist | `WinAnsiEncoding` |

If you override `OutputCharset`, check it still agrees with `FontEncoding`.
If you rely on images drawn at their pixel size, set `ImageScaling=disabled`.

### Templates

`design/standard/templates/content/pdf/footer.tpl` reads the export's footer
settings. An override of that template keeps working but will not show the new
footer wording until it reads `show_footer` and `footer_text`.
