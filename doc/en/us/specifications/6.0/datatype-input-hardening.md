# Specification: datatype and input hardening (27 September 2026)

This page is the reference of what each kernel datatype and shared input helper refuses since 27 September 2026,
and what it does instead. Read it if a form now shows a validation message where it used to accept a value, if
you import packages, or if you write a datatype or a login handler.

On that day every kernel datatype, the shared input validators, the mail class, the download helper and the INI
writer were reviewed for the same kind of fault: input that PHP 8 treats strictly (an array where text was
expected, `null`, a number too large for its column), or input a hostile client could craft (script URLs,
directory parts in file names, line breaks in headers). For the wider list of security defaults, see
[Security defaults of September 2026](security-defaults-2026-09.md); for the earlier sweep, see
[Security hardening 6.0.13](security-hardening-6.0.13.md).

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `image.ini` | `ImageSettings` | `MaxImagePixels` | `100000000` (100 million), read by the code; not listed in the shipped `settings/image.ini` | installation or siteaccess; add it to an override to change it |

Everything else on this page is fixed behaviour with no setting.

## Rules every datatype now follows

1. **A posted array is not text.** A form field sent as `name[]=x` is treated as
   not given, or as a validation error, never as a `TypeError` (HTTP 500).
2. **Missing package elements keep what the site has.** An import (`fromString`,
   package install) that lacks an element leaves that field as it is and logs it,
   instead of a fatal error or overwriting with `0`/empty.
3. **Out-of-range numbers are refused at validation**, not stored for the column
   to truncate.
4. **Output is escaped** in the view, edit, collect and class templates.
5. **Paths stay inside the storage directory.** Names and mime types with a
   directory part cannot point a download, export, delete, trash or package import
   elsewhere.
6. **The stored format did not change.** For each datatype the old and the new
   class were run in separate processes against the live database (or a corpus
   where the database had no examples): they read every stored value the same,
   write byte-identical XML or rows, and the old class reads what the new one
   writes. No data conversion is needed when you upgrade.

## Shared helpers

| Component | Before | Now |
|---|---|---|
| `eZRegExpValidator`, `eZIntegerValidator`, `eZFloatValidator`, `eZDateTimeValidator` | Array or `null` reached `preg_match()`, `checkdate()`, `mktime()`, `trim()`: `TypeError` or deprecation. | An array or object is invalid (`STATE_INVALID`); `fixup()` and the locale parsers return it untouched so the validator after it refuses it; `null` is the empty string; a non-numeric date part makes the date invalid. |
| `eZLocale::internalNumber()`, `internalCurrency()` | `trim()` on arrays. | Type checked first. |
| `eZMail::validate()` | Pattern anchored with `$`, so an address followed by a line break passed; control characters accepted in the quoted local part. | Requires a string, refuses every control character, anchors with the `D` modifier. |
| `eZMail` setters | Values written into the header block as given: a line break in a sender name, receiver, subject, message id or extra header could add a `Bcc` or any header. | Every setter that feeds a header passes values through `eZMail::cleanHeaderValue()` (each CR/LF run becomes one space, other control characters except TAB are dropped) and header names through `cleanHeaderName()`. Non-ASCII names and subjects (MIME-encoded) are sent as before. |
| `eZFile::downloadHeaders()` / `contentDispositionHeader()` | File name appended to `Content-Disposition` unquoted: a semicolon added parameters, a line break made PHP refuse the header. | Control characters removed; a quoted ASCII fallback in `filename=` (anything outside printable ASCII becomes `_`) plus the exact name as RFC 5987 `filename*=UTF-8''...` when they differ. A download without a name keeps the bare `attachment` or `inline`. |
| `eZUser::trimAuthString()` and login | Arrays posted as `Login[]`, `Password[]` ended in 500; an unknown user name returned instantly while a known one ran the password hash. | Non-strings are treated as not given (so login handlers in extensions are covered); an unknown account computes a hash of equal cost. A password or stored hash that is not a string never authenticates (`authenticateHash()` compares strictly, no `(string)` cast; before, `true` matched a stored `true` with the plaintext hash type). |
| INI writer (ini setting datatype, settings saved on object publish) | A value with a line break added any setting or section; `*/` closed the PHP comment around an `.ini.append.php` file; file, section and setting names from a class definition were used unchecked (`../` wrote anywhere; an unknown location index wrote into `settings/siteaccess/`). | Values, names and locations are checked at object validation, class validation and again where the file is written. A saved setting is written inside the PHP comment wrapper. |
| `eZTimeType` / `eZDateTime` / timestamp conversion | Deprecations; `is_valid` left unset. | Typed, `is_valid` always set. |
| Character transformation tables | `hexdec()` fed non-hex characters. | Only hex digits. |

## Per datatype

| Datatype | Hardened against | Notes |
|---|---|---|
| Text line | `TypeError` when a request posts a list. | |
| Text block | Array stored as array, package without `text-column-count` fatal, rows stored as posted. | Rows are a whole number from 1 to 1000, default 10; value escaped in edit, collect and class view. |
| Checkbox | Import stored whatever the file held (`yes`, `abc`); missing default fatal. | Non-zero number, `true`, `yes`, `on` become 1, everything else 0; missing default is unchecked; an optional checkbox in a collection form validates as accepted. |
| Email | Address with a line break or surrounding spaces stored. | Trimmed and refused when it has control characters. |
| Integer | Numbers its column cannot hold; posted list crash; empty field. | Range-checked; an emptied field is stored as no value. |
| Float | Applied another float field's limits; huge numbers; classes without min or max. | Limits per attribute; finite numbers only. |
| Date, Date and time, Time | A word, a half-filled date, a posted list, a missing minute crashed the edit form or object creation. | A validation error is shown instead. |
| Selection | Stored options the class does not have; classes without options fatal. | Only the class's own options; export and import tolerate no options. |
| Enum | A tampered form stored foreign elements. | Only the class's own elements. |
| Price | `"12x50"` accepted (unescaped `.`), 400 digits became `INF`, `name[]=x` crashed, VAT fields stored as `Array`, export left out `<vat-type>` and the import then died. | A point or a comma to the very end; a decimal comma is stored as a point; finite numbers; scalar posted values; VAT id and inc/ex stored as integers; packages without VAT type import. |
| Multi-price | `"1x5"` accepted; a currency the shop does not have; "Set custom price" for a currency with no price was fatal; removing by currency unset a copy of the list. | Same price rules; the currency gets a new custom price of 0; attribute ids and currency codes in the sort SQL are cast and escaped. |
| Product category | Name stored instead of id. | Stored as the category id; names escaped in forms. |
| Option sets, Multi-option, Range option | Unescaped names in polls; `step` of 0, negative or huge hung the page; deep or looping groups ended the request; edit changed the wrong multioption; imported option ids clashed. | Names escaped; the step is validated; depth is capped; the clicked multioption changes; imported options get unique ids; unknown basket choices are ignored; an empty range option round-trips. |
| Author list | `count()`/`trim()` `TypeError` on odd input, removal spliced the array inside a `foreach`, empty `data_text` threw on export. | Posted lists are read in one place; more than 1,000 authors is refused; XML-invalid characters are dropped; broken XML reads as an empty list; the author id is escaped in the edit template. |
| Country | Accepted unknown or numeric codes; a single posted code was accepted and then dropped. | Known codes only; a single posted code is stored. |
| Keyword | Repeated keywords stored twice. | Stored once; odd input does not break the edit form. |
| Object relation, Object relation list | Related missing or unreadable objects; broken XML. | Only existing readable objects; broken XML reads empty. |
| ISBN | PHP error on unexpected form input. | Validation error. |
| Identifier | Two objects published together got the same value (the counter was read before incremented, relying on a table lock that does nothing without `LOCK TABLES`); unlimited digits (`str_pad()` for two billion ran out of memory); negative digits `ValueError`. | The counter is incremented first and read back in the same transaction; digits 1 to 50; start value within `int(11)`; texts within 50 characters; lists refused; the identifier is escaped. |
| URL | `javascript:`, `vbscript:` and `data:` addresses became clickable links (also when disguised by case, entities, control characters or line breaks); array input `TypeError`; link text `0` lost on import. | Such an address is refused at validation and on import; values stored earlier are shown as text, not a link. |
| Image | Trusted the file name: an SVG, an HTML page or a GIF header followed by markup was stored if named `.png`; `fromString()` read any path the process could open (`..`, stream wrappers); a 60000 by 60000 image was accepted; stored paths were deleted or moved wherever they pointed; `unserialize()` with classes. | Uploads and inserted files must be raster images by content (`getimagesize`, not SVG, no markup at the start) within `[ImageSettings] MaxImagePixels` in `image.ini` (default 100 million, read by the code and not listed in the shipped `settings/image.ini`; add it to an override to change it); `fromString()` reads only inside the installation, its var directory or the temporary directory; failed uploads and array entries are validation errors; alternative text is plain text; files are deleted or moved only when the stored path is a relative path inside a storage directory; the LIKE lookup has its `ESCAPE` clause on SQLite; the view template escapes size, style and link attributes. |
| File (binary file) | A stored name or mime with `../` reached outside `var/storage/original`; a failed or partial upload was accepted and dropped without an error; array posts. | Paths from a sanitised mime group and file name; delete and trash never touch a name with a directory part; failed uploads are validation errors; `fromString()` reads `path|original name`. |
| Media | Width, height, quality, controls, plugin page, flags, file name and mime printed unescaped; width and height stored as posted. | Escaped; whole pixels only; same path handling as files. |
| Rich text (XML text) | Empty stored value made `loadXML()` throw; broken XML printed libxml warnings, broke export and opened the editor with a fatal error; tables without rows crashed; stored `javascript:`, `data:`, `vbscript:` links rendered; attribute values unescaped; unlimited nesting; url ids uncast in SQL; custom tag name used as a template path. | Broken or empty XML renders empty; script URLs are dropped on output and refused on input (disguised too); attribute values escaped; nesting capped below the libxml limit; ids cast; custom tag names must be plain names; arrays refused. All 691 stored values rendered, converted and indexed identically with old and new code. |
| User account | Typed passwords kept in the session in plain text (`GeneratedPassword`) and written to the kernel-user debug output; email with control characters; array login/email/password `TypeError`; draft in `data_text` that is not what `serializeDraft()` writes overwrote the account with nulls; `fromString()` shifted the hash into the email for an address containing `|`; the package import dropped `is_enabled`; the PDF template escaped for HTML. | Only a generated password is kept in the session for the registration to show; the debug output only says whether one was given; bad input is taken as empty; an invalid draft is ignored. |
| Package datatype | The posted package name was appended to the repository path unchecked (`../` looked up a `package.xml` elsewhere); the posted siteaccess became the directory a sitestyle `design.ini.append.php` was written to. | Names that leave the repository (path separator, NUL byte, `.` or `..`) are not stored or looked up; a sitestyle is saved only for `Global` or a siteaccess of `AvailableSiteAccessList`; view mode is 0 or 1; export and import carry the stored name. |
| Matrix | Crashed on some input. | Stays usable with any input it can be given. |
| Generic reader (`eZDataType::unserializeContentObjectAttribute()`) for datatypes without `object_serialize_map` | A package that omitted `data-int`, `data-float` or `data-text` made it read a property of `null` and overwrite the field with `0`/empty. | Each element is looked up on its own; a missing element leaves that field as it is. |

## Other changes in the same sweep

- **Anonymous forms.** Content published through an anonymous form no longer
  subscribes the shared anonymous account to notifications.
- **Image variations** generated by several requests at once are never read half
  written.
- **Forms posting arrays** no longer fail login, forgot password, password change,
  account activation or search with a 500 under PHP 8. Account activation also
  casts the posted `MainNodeID` (it cast the result of `hasPostVariable()`).
- **Sorting a node** (`content/action`) requires permission to edit it and accepts
  only a known sort field and order.

## Check an installation

1. On a development installation, create a test object of a class that uses each datatype and enter the hostile
   value from the table. Expected: a validation message, not an HTTP 500.
2. After a crawl, search the error log. Expected: no matches from the datatypes above.

   ```bash
   grep -cE "TypeError|Undefined array key" var/log/error.log
   ```

3. Run the PHPUnit suite. It carries tests for the authentication hash comparison (including the "true is no
   password" case) and for the REST lazy database set-up (`tests/tests/kernel/classes/ezpRestLazyTest.php`).

## Related pages

- Specifications: [Security defaults of September 2026](security-defaults-2026-09.md), [Security hardening 6.0.13](security-hardening-6.0.13.md), [The August 2026 security patches](security-hardening-2026-08.md)
- Upgrade notes: [Hardening guide](../../bc/6.0/hardening.md), [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md), [RAD tools — security](../../bc/6.0/rad-security.md)
- Features: [Form expired page](../../features/6.0/form-expired-page.md) (a refused form is a 403 page)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md), [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md), [February 2026](../../history/2026/2026-02.md)
