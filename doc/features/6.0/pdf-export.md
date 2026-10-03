# PDF export

This page is for editors and administrators who turn content into PDF files. The PDF export lives at
**Setup > PDF export** (`/pdf/list`, edit page `/pdf/edit`, policy `pdf/edit`). In 6.0 it produces files that open,
text that reads correctly, images that fit the page, real UTF-8 with a TrueType font, and a footer line you word
yourself.

**Upgrading?** Run the database update first: see [PDF export upgrade](../../bc/6.0/pdf-export.md).

## Make an export with your own footer

1. Open **Setup > PDF export** and create or edit an export.
2. Tick **Show footer** and type your wording in **Footer text**, for example "Example Ltd, product sheet".
3. Save and export. Every page carries your line instead of "Exponential PDF export".

| Field | Column in `ezpdf_export` | Default | Meaning |
|---|---|---|---|
| Show footer | `show_footer` (int) | 1 | Whether the footer carries a line of text at all |
| Footer text | `footer_text` (varchar 255) | empty | The wording. Empty with the box ticked keeps the shipped wording. |

Before, the words were fixed in `content/pdf/footer.tpl`. An export made before this change looks exactly as it did.

## Print any language: use a TrueType font

The bundled fonts hold one single-byte character set. For Unicode text, point `Font` at a `.ttf` file:

```ini
# settings/override/pdf.ini.append.php
[PDFGeneral]
Font=/usr/share/fonts/dejavu-sans-fonts/DejaVuSans.ttf
```

The font is embedded as a composite font, UTF-8 passes through unchanged, and the text stays selectable and
searchable. DejaVu Sans covers Latin, Latin Extended, Greek, Cyrillic, Hebrew, Armenian and Georgian. For Chinese,
Japanese or Korean, point `Font` at a font that has them (a Noto CJK face, for instance).

## What was repaired

- The export could not produce a file at all, and when it did, the pages were unreadable. The library under
  `lib/ezpdf` now writes valid files.
- HTML entities and link addresses reached the printed page unprocessed; they are now decoded before they are drawn.
- Accents, dashes, euro signs, bullets and curly quotes survive the default font (see `OutputCharset` below).
- Images are scaled down to fit the page instead of running off the paper.
- Underlines under links are as long as the link.

## Settings

All keys are in `settings/pdf.ini`, block `[PDFGeneral]`. Scope: global. Check them with
`grep -v '^#' settings/pdf.ini`. The `[Header]` and `[Footer]` blocks (margins, line thickness) are older and unchanged.

| Key | Default | Meaning |
|---|---|---|
| `OutputCharset` | `windows-1252` | The charset text is written in. Ignored when `Font` is a `.ttf` file. For the bundled fonts it must be single byte; `utf-8` there is refused (debug log error) and `windows-1252` used. Change it only together with `FontEncoding`. Was `iso-8859-1`, which dropped characters in 0x80 to 0x9F. |
| `Transliterate` | `enabled` | Degrade a character the charset cannot hold to its closest ASCII (Lódz rather than a question mark). Ignored with a `.ttf` font. |
| `LinkUnderline` | `enabled` | Draw a rule under links as well as colouring them |
| `MaxImageWidth` | empty | Largest image width in points (72 to the inch). Empty means what the page has room for, paper less margins. |
| `MaxImageHeight` | empty | The same for height |
| `ImageScaling` | `enabled` | Scale images down, keeping proportions. `disabled` draws them at the size handed over. |
| `Format`, `Orientation`, `TopMargin`, `BottomMargin`, `LeftMargin`, `RightMargin` | `A4`, `portrait`, `80`, `100`, `80`, `80` | Page size and margins, unchanged by this work |
| `Font` | `lib/ezpdf/classes/fonts/Helvetica` | A bundled face (Helvetica, Times-Roman, Courier) or the full path of a `.ttf` file |
| `FontEncoding` | `WinAnsiEncoding` | Which glyph each of a simple font's 256 slots holds: `WinAnsiEncoding` (windows-1252), `MacRomanEncoding` (mac-roman) or `StandardEncoding` |

The list of exports is paged by `admininterface.ini [PaginationSettings] ItemsPerPage[pdf/list]`
([pagination settings](../../bc/6.0/pagination-settings.md)).

## Related pages

- [PDF export upgrade](../../bc/6.0/pdf-export.md) (database columns, changed defaults)
- [Paging, sorting and page sizes](admin-list-paging.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- [Chronicle: September 2026, first half, 14 September](../../history/2026/2026-09a.md#14-september-pdf-rss-and-the-rad-tools)
