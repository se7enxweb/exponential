# Specification: the URL map file formats

This page is the normative description of the files that carry a URL map in Exponential 6.0.15: a list of old
addresses and where each one goes, as used for the global URL aliases (the URL translator) and the URL wildcards. It
covers the row model, the columns and kinds, how addresses are normalized, and each file format with what it reads,
what it writes and what it cannot express. It is for developers and for administrators who prepare such files by
hand or with other tools.

Version of this specification: 1 (`exp-url-map/1`).

The classes are in `kernel/classes/urlmap/` and `kernel/classes/spreadsheet/`; they read and write files only and
use no database. Importing the rows into an installation, the preview and the undo are not part of this page.

## The row

Every format carries the same six columns:

| Column | Content |
|---|---|
| `old_url` | The old address: a path (`/about-us`) or a full URL (`https://old-site.com/about-us`). A `*` in the path makes a wildcard. |
| `new_url` | The target: a path on this site (`/About`), a node (`node:123` or `eznode://123`), a full URL, or for wildcards a pattern with `{1}`, `{2}` placeholders. |
| `kind` | Empty (automatic) or one of the kinds below. |
| `siteaccess` | Optional: the siteaccess the mapping belongs to. Letters, digits, `_` and `-`. |
| `language` | Optional: a locale such as `eng-GB`. |
| `note` | Optional free text. |

### Kinds

| Kind | Meaning |
|---|---|
| (empty) | Automatic: a wildcard (forward) when `old_url` has a `*`, else a redirect |
| `redirect` | A global URL alias that redirects with 301 to the target |
| `alias` | A global URL alias without a redirect: the target page is served at the old address |
| `wildcard-forward` | A URL wildcard that redirects |
| `wildcard-direct` | A URL wildcard that serves the target without a redirect |

A kind is compared in lower case, and `_` or a space counts as `-` (`Wildcard_Forward` is `wildcard-forward`).
`auto` is the same as empty. Any other value is kept as written and makes the row not importable.

### Normalization

The old and the new address are normalized when a row is read, so that the same address gives the same row in
every format:

- A scheme and a host are split off. The scheme and the host are written in lower case; a default port (`:80` for
  http, `:443` for https) and a trailing dot of the host are dropped. `//host/path` is a host without a scheme;
  `www.example.com/path` is a host because it begins with `www.`. Any other text without a scheme is a path.
- Only `http` and `https` are accepted; a user name or password in the address is a problem.
- The path starts with `/`; repeated slashes become one; `.` and `..` segments are resolved.
- **Trailing slash:** a trailing slash is dropped (the root `/` stays). Exponential serves `/about` and `/about/` as
  the same page, so `/about/` and `/about` are the same old address.
- **Percent-encoding:** `%XX` sequences that encode UTF-8 text are decoded (`/%C3%9Cber` is `/Über`). `%2F`, `%3F`,
  `%23` and `%25` stay encoded, because decoding them would change the address; spaces and control characters are
  encoded (`/Über uns` is `/Über%20uns`); the hex digits that stay are written in upper case. Bytes that are not
  UTF-8 stay encoded.
- The fragment (`#...`) is dropped; the query (`?...`) is kept as written. A wildcard cannot have a query.
- The case of the path is kept.
- A target `node:123` or `eznode://123` is written `node:123`. A target with a host but no scheme gets `https`.

### Validation

A row is importable when it has none of these problems:

| Problem | Example |
|---|---|
| `old_url` or `new_url` is empty | `/old,` |
| An unknown kind | `moved` |
| A `*` in `old_url` with a kind that is not a wildcard | `/a/*,/b,redirect` |
| A wildcard kind without a `*` | `/a,/b,wildcard-forward` |
| A placeholder in `new_url` of a row that is not a wildcard, or one with no matching `*` | `/a/*,/b/{2}` |
| A `*` in the host, a query on a wildcard | `https://*.example.com/a` |
| The same path as old and new address | `/a/,/a` |
| A siteaccess or language that is not a name or a locale | `my site`, `english` |
| A scheme other than http(s), a user name or password | `ftp://...` |
| A node reference that is not `node:<number>` | `node:abc` |

Each row keeps the place it came from (`line()`): the line a CSV record starts on, the row number in a spreadsheet,
the entry number in JSON (from 1, without the header), the line of the directive in Apache and nginx files. A file
that cannot be read at all (invalid JSON, not a workbook, an unknown format) is refused as a whole; a single bad row
is a row with its problems, so a preview can list it.

## CSV

Read and written. Extensions `.csv`, `.tsv` and `.txt`.

- RFC 4180: fields may be quoted with `"`; a quoted field may hold the delimiter, line breaks and doubled quotes.
- The delimiter is `,`, `;` or a tab, found from the first 20 lines (the one that splits most lines into the same
  number of fields). Spreadsheet programs with a German locale write `;`, "Unicode text" from Excel is tab-separated.
- Encoding: UTF-8, with or without a byte order mark; UTF-16 with a byte order mark is converted; text that is not
  valid UTF-8 is read as Windows-1252. Line ends `\r\n`, `\n` and `\r`.
- A header row is optional. It is recognized when it names `old_url` or `new_url` (also `old`, `from`, `source`,
  `new`, `to`, `target`, `destination`, and the German `Alte URL`, `Quelle`, `Neue URL`, `Ziel` and the like) and
  its first cell is not an address. Its columns may be in any order; unknown columns are ignored. Without a header
  the columns are in the order above.
- Blank lines and lines whose first cell starts with `#` are skipped. A row without a header that has more than six
  filled columns is a problem.
- Written with a header, CRLF line ends, fields quoted only when they need it, and a UTF-8 byte order mark (the
  option `bom`, on by default, so that Excel reads UTF-8). A `.tsv` file is written with tabs.

```text
old_url,new_url,kind,siteaccess,language,note
/about-us.html,/About,,,,old about page
https://old-site.com/news/2019/,/News,redirect,site,eng-GB,
/products/*,/Shop/{1},,,,"catalogue, all pages"
/team,node:42,alias,,,
```

## JSON

Read and written. Extension `.json`.

An array of objects with the canonical keys. The first entry may be the header `{"schema": "exp-url-map/1"}`; an
object `{"schema": "exp-url-map/1", "rows": [...]}` is read as well. Another schema is refused. The other column
names of CSV headers are accepted as keys; numbers are read as text; an entry that is not an object, or a value that
is an array, is a row with a problem. Written as the array with the header first and all six keys in every object,
pretty-printed, slashes and Unicode not escaped.

```json
[
    { "schema": "exp-url-map/1" },
    { "old_url": "/about-us.html", "new_url": "/About", "kind": "", "siteaccess": "", "language": "", "note": "" },
    { "old_url": "/products/*", "new_url": "/Shop/{1}", "kind": "", "siteaccess": "", "language": "", "note": "" }
]
```

## Excel (.xlsx)

Read and written. Extension `.xlsx`. Needs the PHP extension zip (ZipArchive); without it, reading and writing
spreadsheets stops with a message that says so, and CSV and JSON still work.

- Reading takes the first sheet of the workbook (by the workbook's own order) as a table, as CSV: the same header
  detection, the same columns. Shared strings (also with formatted runs), inline strings, numbers (whole numbers
  without `.0` or an exponent), booleans (`TRUE`, `FALSE`) and the last result of formulas are read; styles, other
  sheets, merged cells and comments are ignored. A cell with an error is empty.
- Writing makes one sheet "URL map" with the header row (bold and frozen) and column widths; every cell is an
  inline string, so nothing turns into a number, date or formula. The package has only the parts a workbook needs:
  `[Content_Types].xml`, `_rels/.rels`, `xl/workbook.xml`, `xl/_rels/workbook.xml.rels`, `xl/styles.xml` and
  `xl/worksheets/sheet1.xml`.
- A part with a `DOCTYPE` or larger than 100 MB uncompressed is refused.

## OpenDocument spreadsheet (.ods)

Written only (for LibreOffice Calc). Extension `.ods`. Needs the PHP extension zip. One sheet "URL map" with the
header row in bold; the `mimetype` entry comes first and uncompressed, as OpenDocument requires; spaces, tabs and
line breaks in a cell are written as OpenDocument elements so that they survive. To import a map edited in Calc,
save it as .xlsx or CSV.

## Apache

Read and written. Extensions `.htaccess`, `.apache`, `.apacheconf`, the file name `.htaccess`, and `.conf` when the
content has Apache directives.

Read:

| Directive | Row |
|---|---|
| `Redirect [status] /old /new`, `RedirectPermanent`, `RedirectTemp` | A redirect of that one path. Apache's `Redirect` also matches the paths below it; for a subtree, use `RedirectMatch` or a wildcard row. |
| `RedirectMatch [status] ^/old/(.*)$ /new/$1` | A wildcard (forward) when the expression is simple (below) |
| `RewriteRule ^/?old$ /new [R=301,L]` | A redirect, or a wildcard (forward) with groups |
| `RewriteRule ^/?old/(.*)$ /new/$1 [L]` (no `R`) | An internal rewrite: an alias, or a wildcard (direct) |
| `RewriteCond %{HTTP_HOST} ^old\.com$ [NC]` before a rule | The host of the old address (a leading `(www\.)?` is left out) |
| `RewriteCond %{QUERY_STRING} ^id=5$` before a rule | The query of the old address |
| `ServerName` inside `<VirtualHost>` | The host of the rows in that block |
| `RewriteBase /base/` | The base of relative targets |
| `#exp:<kind> <old> <new>` | A row of that kind (written by Exponential, below) |

A redirect with 302, 303 or 307 is read as a redirect with a note ("the source answered with 302; Exponential
redirects with 301"). Not importable, with the reason in the row and the directive in its note: `gone`/410 and other
status codes, other conditions (`%{REQUEST_FILENAME}`, `[OR]`), the flags `F`, `G`, `P`, `C`, `S`, `E`, `CO`, `T`,
`H`, `N`, a negated pattern, the target `-`, `%1` and `%{...}` in the target. The flags `L`, `NC`, `QSA`, `QSD`,
`NE`, `END` and `PT` do not change the row. Lines continued with `\` are joined. Other directives are ignored.

Written, for a `<VirtualHost>` or the `.htaccess` of the document root:

```apache
RewriteEngine On

# old about page
RewriteRule ^/?about\-us\.html/?$ /About [R=301,L]

RewriteCond %{HTTP_HOST} ^old\-site\.com$ [NC]
RewriteRule ^/?news/?$ /News [R=301,L]

RewriteCond %{QUERY_STRING} ^id\=5$
RewriteRule ^/?index\.php/?$ /Home? [R=301,L]

RewriteRule ^/?products/(.*)$ /Shop/$1 [R=301,L]

#exp:alias /team /Team
#exp:redirect /staff node:42
```

`^/?` and `/?$` make the rule work in both places and match with and without a trailing slash. A query condition
ends the target with `?`, so the old query is not carried over. Aliases and wildcards (direct) are served by
Exponential itself without a redirect, and a target `node:<id>` is only known to Exponential, so they are written as
`#exp:` comments, which Apache ignores and the reader reads back. A note, siteaccess and language go in a comment
above the rule. Rows that are not importable are left out with a `# not written:` comment.

## nginx

Read and written. Extension `.nginx`, and `.conf` when the content has nginx directives.

Read:

| Directive | Row |
|---|---|
| `rewrite ^/old$ /new permanent;` | A redirect (`redirect` is 302 and gets the note); with groups a wildcard (forward) |
| `rewrite ^/old/(.*)$ /new/$1 last;` (`last`, `break`) | An internal rewrite: an alias or a wildcard (direct) |
| `location = /old { return 301 /new; }` | A redirect |
| `location ~ ^/old/(.*)$ { return 301 /new/$1; }` | A wildcard (forward) |
| `if ($args = "id=5") { return 301 /new; }` inside `location =` | A redirect of the old address with that query |
| `map $uri $new { /old /new; ~^/old/(.*)$ /new/$1; }` | A redirect (or wildcard) per entry; `$request_uri` and `$document_uri` too. The `return 301 $new;` that uses the map gives no row. |
| `server_name` in a `server` block | The host of the rows in that block (the first name without `*`, `~` or `$`) |
| `#exp:<kind> <old> <new>` | A row of that kind |

`$scheme://$host` (or `https://$host`) in front of a target is the same host and is dropped. Not importable: a
`return` in a prefix location (`location /old` matches every path that starts with `/old`) or outside a location, a
`return` with another status (410, 404), a `rewrite` inside an `if`, a `return` inside an `if` with another
condition, other variables in the target, a `map` of something other than a path.

Written, for inclusion in the server block of the site:

```nginx
location = /about-us.html {
    return 301 /About;
}

location = /index.php {
    if ($args = "id=5") { return 301 /Home; }
    return 301 /;
}

rewrite ^/products/(.*)$ /Shop/$1 permanent;

#exp:alias /team /Team

server {
    server_name old-site.com;

    location = /news {
        return 301 /News;
    }
}
```

Exact redirects of one path are one `location =` block (with an `if` per query); a second redirect of the same
address is left out with a comment, as nginx refuses two equal locations. Rows of another host go into a `server`
block of their own, which needs the site's `listen` lines added. `location =` matches the path exactly, including a
trailing slash.

## Regular expressions and wildcards

A rewrite rule becomes a wildcard when its expression is one a wildcard can say:

- anchored at the start with `^`; a `/` or `/?` after it is optional (per-directory rules have no leading slash)
- literal text, with `\` before punctuation; an unescaped `.` is read as a literal dot, as nearly every rule with
  one means it
- the groups `(.*)`, `(.+)`, `(.*?)`, `(.+?)` and `([^/]+)`, `([^/]*)`, each a `*` (the last two with a note: a `*`
  also matches `/`); a bare `.*` is a `*` too
- an optional trailing slash (`/?`) before the end, which is dropped
- `$` at the end; without it the expression matches every path that starts with it, which is a `*` at the end

`$1`, `$2` in the target become the placeholders of their groups' `*` (a bare `.*` counts as a `*` but not as a
group, so the numbers are mapped). Anything else, such as `\d`, `[a-z]`, `a|b`, `x?`, `x+`, `{2}` or `(?:...)`,
makes the rule not importable, with the construct named in the reason.

When a map is written as rules, each `*` becomes `(.*)` and each `{N}` becomes `$N`; literal text is escaped.

## Choosing the format

`expUrlMapFormats` chooses the reader and writer: by an explicit format name (`csv`, `json`, `xlsx`, `ods`,
`apache`, `nginx`), else by the file's extension, else by its content (a ZIP archive is .xlsx, `[` or `{` first is
JSON, Apache or nginx directives, else CSV). ODS can only be written.

```php
$rows = expUrlMapFormats::readFile( 'old-site.htaccess' );
foreach ( $rows as $row )
{
    if ( !$row->isImportable() )
        echo $row->line(), ': ', implode( '; ', $row->problems() ), "\n";
}
expUrlMapFormats::writeFile( 'map.xlsx', $rows );
```

## Related pages

- [The migration file format](migration-file-format.md)
- [Specifications 6.0](README.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
