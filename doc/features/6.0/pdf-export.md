# PDF export

The PDF export turns content into a PDF file from **Setup > PDF export**. In
6.0 it produces files that open, text that reads correctly, images that fit the
page, real UTF-8 with a TrueType font, and a footer line you word yourself.

## What was repaired

- The export could not produce a file at all, and when it did the pages were
  unreadable. The library under `lib/ezpdf` now writes valid files.
- HTML entities and link addresses reached the printed page unprocessed; they
  are now decoded before they are drawn.
- Accents, dashes, euro signs, bullets and curly quotes survive the default
  font (see `OutputCharset` below).
- Images are scaled down to fit the page instead of running off the paper.
- Underlines under links are as long as the link.

## Footer line

Every page of every export used to read "Exponential PDF export", because the
words were in `content/pdf/footer.tpl`. The wording now belongs to the export.
On the export's edit page:

| Field | Column in `ezpdf_export` | Default | Meaning |
|---|---|---|---|
| Show footer | `show_footer` (int) | 1 | Whether the footer carries a line of text at all. |
| Footer text | `footer_text` (varchar 255) | empty | The wording. Empty with the box ticked keeps the shipped wording. |

An export made before this change looks exactly as it did.

Existing installation: run the database update, see
[PDF export upgrade](../../bc/6.0/pdf-export.md).

## Settings (`settings/pdf.ini`)

| Key | Default | Meaning |
|---|---|---|
| `OutputCharset` | `windows-1252` | The charset text is written in. Ignored when `Font` is a `.ttf` file. For the bundled fonts it must be single byte; `utf-8` there is refused (debug log error) and `windows-1252` used. Change it only together with `FontEncoding`. Was `iso-8859-1`, which dropped characters in 0x80 to 0x9F. |
| `Transliterate` | `enabled` | Degrade a character the charset cannot hold to its closest ASCII (Lódz rather than a question mark). Ignored with a `.ttf` font. |
| `LinkUnderline` | `enabled` | Draw a rule under links as well as colouring them. |
| `MaxImageWidth` | empty | Largest image width in points (72 to the inch). Empty means what the page has room for, paper less margins. |
| `MaxImageHeight` | empty | The same for height. |
| `ImageScaling` | `enabled` | Scale images down, keeping proportions. `disabled` draws them at the size handed over. |
| `Font` | `lib/ezpdf/classes/fonts/Helvetica` | A bundled face (Helvetica, Times-Roman, Courier) or the full path of a `.ttf` file. |
| `FontEncoding` | `WinAnsiEncoding` | Which glyph each of a simple font's 256 slots holds: `WinAnsiEncoding` (windows-1252), `MacRomanEncoding` (mac-roman) or `StandardEncoding`. |

### Unicode text: use a TrueType font

```ini
# settings/override/pdf.ini.append.php
[PDFGeneral]
Font=/usr/share/fonts/dejavu-sans-fonts/DejaVuSans.ttf
```

The font is embedded as a composite font, UTF-8 passes through unchanged, and
the text stays selectable and searchable. DejaVu Sans covers Latin, Latin
Extended, Greek, Cyrillic, Hebrew, Armenian and Georgian. For Chinese,
Japanese or Korean point `Font` at a font that has them (a Noto CJK face, for
instance). All keys in the table sit in
`[PDFGeneral]`.

The list of exports is paged by `ItemsPerPage[pdf/list]`
([pagination settings](../../bc/6.0/pagination-settings.md)).
