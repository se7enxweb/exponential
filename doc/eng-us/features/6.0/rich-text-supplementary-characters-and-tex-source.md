# Rich text: characters above U+FFFF and TeX source in custom tags

This page is for administrators and developers whose rich text (the XML block datatype, edited in the Online Editor)
holds emoji, mathematical letters such as `𝔸` (U+1D538) or `𝑥` (U+1D465), or LaTeX formulas in a custom tag.

## Characters above U+FFFF

XML 1.0 allows the characters #x10000-#x10FFFF. Up to 6.0.15 the rich text input removed each of them when a text was
opened in an editor and replaced each with a space when it was saved. They are now kept where the database can store
them.

`ezxml.ini`:

```ini
[InputSettings]
# auto (default), enabled or disabled
SupplementaryCharacters=auto
```

| Value | Effect |
|---|---|
| `auto` | Kept, unless the database is MySQL or MariaDB on a connection charset other than `utf8mb4`. |
| `enabled` | Always kept. |
| `disabled` | Replaced as before (removed on opening, a space on saving). |

Why `auto` leaves MySQL out: `[DatabaseSettings] Charset=utf-8` connects to MySQL and MariaDB as `utf8`, which holds
three bytes per character at most. A character above U+FFFF sent over that connection is stored as `????` and, in
strict SQL mode, makes the whole save fail. A site whose tables are `utf8mb4` can set
`[DatabaseSettings] Charset=utf8mb4` in site.ini, or `SupplementaryCharacters=enabled` in ezxml.ini. SQLite,
PostgreSQL, Oracle and MongoDB store them as they are.

Text that is not valid UTF-8 no longer empties the whole text: each invalid byte sequence becomes U+FFFD.

## TeX source in custom tags

`content.ini`:

```ini
[CustomTagSettings]
TeXSource[]
TeXSource[math_latex]=true
```

The text of a custom tag named here is read as TeX reads it when the Online Editor opens it: an unescaped `%`
comment is dropped with the rest of its line, the line break and the indentation of the next line. After a control
word a space stays (`\alpha%` followed by `beta` on the next line gives `\alpha beta`). Leading white space is dropped
instead of being stored as a no-break space. No tag is set by default; other custom tags only get their line breaks
turned into spaces.

A line break in a custom attribute value (multi-line MathML) becomes a space instead of vanishing, for every custom
tag.

## Tests

`eZXMLInputSupplementaryCharactersTest` (suite kernel-datatypes) and `expOEFormulaTextTest` (suite ezoe), both
without a database.

## Related pages

- [ezoe](extensions/ezoe.md), [Online editor on TinyMCE 8](online-editor-tinymce8.md)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
