# OPML exports

## Added: OPML 2.0 as a format an RSS export can be written in

An RSS export has always been a feed of articles gathered from content. It can
now also be an **OPML document**: a list of other feeds, which is what readers
import when somebody publishes their subscriptions.

An export set to OPML has no content source and no class mapping. What it has
instead is a set of **outlines**, each naming another export on this
installation, a content node, or a plain address. The address of each one is
worked out when the document is written, so renaming a feed cannot leave a dead
entry behind.

Nothing that already existed changes. `rss_version` still stores `1.0`, `2.0`
and `ATOM`; `OPML` is a fourth value alongside them.

---

## Turning an export into an OPML document

`/rss/edit_export/<id>` → **Feed format** → `OPML 2.0 (list of feeds)`.

The page then shows the OPML controls in place of the content sources. The
format drop-down was relabelled in this release: it reads *Feed format* rather
than *RSS version*, and each option says which format it is —
`RSS 1.0 (RDF)`, `RSS 2.0`, `Atom 1.0`, `OPML 2.0 (list of feeds)`. **The value
behind each option is unchanged**, so anything that reads or writes
`rss_version` keeps working.

The feed is served from `/rss/feed/<access url>` as `text/x-opml`.

---

## Database

### `ezrss_export_opml_item` (new)

One row per outline. Draft and valid rows live side by side under `status`,
exactly as `ezrss_export_item` does, so editing an OPML export is as reversible
as editing any other.

| column | type | meaning |
|---|---|---|
| `id` | auto | |
| `status` | int | `0` draft, `1` valid — part of the primary key |
| `rssexport_id` | int | the OPML export this belongs to |
| `parent_id` | int | the outline this one sits inside, `0` for top level |
| `priority` | int | position in the document, lowest first |
| `target_export_id` | int | the export this outline lists, `0` if none |
| `source_node_id` | int | the content node this outline points at, `0` if none |
| `subnodes` | int | include the node's children |
| `outline_type` | varchar(50) | `rss`, `link`, `include`, `group`, `text` |
| `outline_text` | varchar(255) | the outline's `text`; empty means take it from the target |
| `title` | varchar(255) | `title`; empty means use `text` |
| `description` | varchar(255) | `description` |
| `category` | varchar(255) | `category` |
| `language` | varchar(50) | `language` |
| `xml_url` | varchar(255) | override; empty means work it out from the target |
| `html_url` | varchar(255) | override |
| `url` | varchar(255) | used by `link` and `include` |
| `is_comment` | int | `isComment` |
| `is_breakpoint` | int | `isBreakpoint` |
| `created` | int | timestamp, written as `created` |

Primary key `(id, status)`, index `ezrss_export_opml_rsseid (rssexport_id)`.

`outline_text` rather than `text`, because `text` is a type name in more than
one dialect.

### `ezrss_export.opml_head` (new column)

`longtext` (`text` on PostgreSQL). The OPML head fields as JSON. One column
rather than a dozen: they are written once, read once, never searched on, and an
installation with no OPML export never fills it in.

Keys: `ownerName`, `ownerEmail`, `ownerId`, `docs`, `expansionState`,
`vertScrollState`, `windowTop`, `windowLeft`, `windowBottom`, `windowRight`.

### Upgrading

Both are in `update/database/{mysql,postgresql,sqlite}/6.0/dbupdate-6.0.0-6.0.15.sql`,
and in `kernel/sql/*` and `share/db_schema.dba` for a clean install.

---

## PHP API

### `eZRSSExportOPMLItem` — `kernel/classes/ezrssexportopmlitem.php`

An `eZPersistentObject` over `ezrss_export_opml_item`.

**Constants**

| | |
|---|---|
| `MAX_BULK` (250) | rows one button press may add or remove |
| `MAX_TEXT` (255) | width of the text columns |
| `MAX_SHORT` (50) | width of the short columns |
| `MAX_DEPTH` (20) | how deep outlines may nest before the writer stops |
| `ALLOWED_SCHEMES` (`http,https`) | schemes an outline address may use |

**Rows**

- `create( $exportId, $targetExportId = 0, $priority = 0 )` — a new row, status draft.
- `fetch( $id, $asObject = true, $status = STATUS_VALID )`
- `fetchList( $exportId, $status = STATUS_VALID )` — in document order.
- `fetchListCount( $exportId, $status = STATUS_VALID )`
- `fetchTree( $exportId, $status = STATUS_VALID, $limit = false )` — the rows
  arranged as OPML nests them: `array( array( 'item' => …, 'children' => array( … ) ) )`.
  A parent that has gone leaves its children at the top rather than dropping
  them; two rows inside each other are written nowhere rather than for ever.
- `selectedTargetIDs( $exportId, $status = STATUS_VALID )` — export ids already
  listed, keyed by themselves.
- `nextPriority( $exportId, $status = STATUS_DRAFT )`
- `addTargets( $exportId, array $targetIDs, $status = STATUS_DRAFT )` — returns
  how many were added. Skips what does not exist, what is listed already, and
  the export itself; stops at `MAX_BULK`; checks the lot in one query.
- `removeItems( $exportId, array $itemIDs )` — bounded the same way.
- `removeByExport( $exportId, $status = false )` — `false` means every status.
- `copyStatus( $exportId, $fromStatus, $toStatus )` — used when an export is
  opened for editing and when it is published.

**Reading a row**

- `targetExport()` / attribute `target` — the listed export, or `null`.
- `targetGone()` / attribute `target_gone` — it was deleted since.
- `sourceNode()` / attribute `source_node`
- `sourcePath()` / attribute `source_path`
- `outline( $baseURL = '' )` / attribute `outline` — the line as OPML says it, or
  `null` when there is nothing worth writing. Overrides win; what is left empty
  comes from the target.

**Values**

- `safeText( $value, $max = MAX_TEXT )` — drops control characters XML cannot
  carry, trims, cuts to `$max` characters without splitting one. Anything that
  is not a string, or is not valid UTF-8, comes back empty.
- `safeURL( $value, $max = MAX_TEXT )` — `safeText`, then only
  `ALLOWED_SCHEMES`, protocol-relative and site-relative addresses survive.
- `siteOf( $url )` — the site part, with any `/rss/feed/…` taken back off.
- `absolute( $baseURL, $path )`
- `outlineTypes()` — type ⇒ the word for it in the interface.
- `opmlVersionOf( $rssVersion )` — `1.0` ⇒ `RSS1`, `2.0` ⇒ `RSS2`, `ATOM` ⇒ `ATOM`.

### `eZRSSExport` — additions

- `isOPML()` / attribute `is_opml`
- `opmlItemList()` / attribute `opml_item_list`
- `opmlHead()` / attribute `opml_head_data` — the head fields as a hash, with
  defaults. A column full of rubbish reads as nothing filled in.
- `setOPMLHead( array $head )` — keeps only the keys OPML defines, and only
  values that suit them: `docs` must be an address, `ownerEmail` an email, the
  window and scroll fields numbers or lists of numbers.
- `generateOPML()` — the document, built with DOM.
- `emptyOPML( $title = '' )` — a valid document to fall back on.
- `opmlDate( $timestamp )` — RFC 822, which is what OPML asks for.
- `opmlMaxOutlines()` — `[RSSSettings] OPMLMaxOutlines`, default 5000, never
  above 50000.
- `formatLabels()` / `formatLabel( $version )` — the words for a stored format
  value. A value nobody has a name for is shown as it stands.
- `fetchList( $asObject = true, $offset = false, $limit = false, $sorts = null )`
  — now takes a slice and an order; no arguments still returns everything.
- `fetchListCount()`
- `sortableFields()` — the only columns that may reach an order clause.
- `fetchBrowserList( $search, $offset, $limit, $sorts, $excludeID )` /
  `fetchBrowserListCount( $search, $excludeID )` — the feed browser's page.
  `$search` looks in name, address and description, with its own wildcards
  defused.

### `eZRSSListPager` — `kernel/rss/ezrsslistpager.php`

Shared by `/rss/list` and by the browser inside the edit page.

- `limits()` — `array( 25, 50, 250 )`
- `limit( $requested, $remembered = false )`
- `offset( $offset, $limit, $count )`
- `data( $count, $limit, $offset, $offsetName, $suffix, $window = 9 )`
- `sort( $field, $direction, array $allowed, $default )` — always adds `id` as a
  tie break unless the sort is already by `id`.
- `suffix( array $parameters )` / `suffixExcept( array $state, array $names )`

### `eZRSSImport` — additions

- `fetchableURL( $url )` — the address to fetch, or `false`.
- `isFetchableURL( $url )` — the same, as a boolean.

---

## INI

`settings/site.ini`, `[RSSSettings]`:

```ini
AvailableVersionList[]=OPML

# The most outlines one OPML document will carry. Bounded at 50000 whatever
# this says.
OPMLMaxOutlines=5000

NumberOfObjectsList[]=100
NumberOfObjectsList[]=250
NumberOfObjectsList[]=350
NumberOfObjectsList[]=500
NumberOfObjectsList[]=1000
```

---

## Templates

| file | |
|---|---|
| `design/admin/templates/rss/edit_export_opml.tpl` | the OPML half of the edit page |
| `design/standard/templates/rss/sortbutton.tpl` | a sortable heading inside a form |
| `design/standard/templates/rss/pagination.tpl` | the `/rss/list` navigator |
| `design/standard/templates/rss/sortheader.tpl` | a sortable heading as a link |

Variables the edit view sets for OPML: `rss_is_opml`, `opml_head`,
`opml_items`, `opml_groups`, `opml_outline_types`, `opml_browser_list`,
`opml_browser_pager`, `opml_browser_search`, `opml_browser_limits`,
`opml_selected_ids`, `rss_version_options`.

`rss_version_array` is still set, for override templates that use it.

### Why the browser uses buttons

The feed browser sits inside the edit form. A link would leave the page and take
everything typed into it along, so every control — search, page size, column
headings, page numbers — is a submit button that writes the draft first and then
acts. The page needs no JavaScript; the script that is there only widens the
click target and makes Enter in the search box search rather than save.

---

## Security

Tests: `tests/tests/kernel/classes/security/eZRSSSecurityTest.php`.

| id | |
|---|---|
| **RSS-01** | An outline address may only be one a reader should follow. An OPML document is consumed by other people's software, which follows what it finds. `javascript:`, `data:`, `vbscript:`, `file:`, `php://`, `gopher:` and everything else are dropped, including when split with a newline or a tab (`java\nscript:`) or hidden behind leading whitespace. Checked when stored **and** again when written. |
| **RSS-02** | Characters no XML document can carry never reach one. A control character stored years ago would otherwise produce a document nothing can parse, from a public address. Text is written through `createTextNode`, so markup in a title is escaped rather than injected. |
| **RSS-03** | A sort column is chosen from `sortableFields()`, never taken from the request, and a direction is one of two words. Every sort carries `id` as a tie break, so paging a column full of equal values cannot show a row twice or skip one. |
| **RSS-04** | A page size is one of `limits()`. A request that could name its own size could ask for every row, which is a way of exhausting the server from one address. |
| **RSS-05** | An offset is pulled back inside the list and onto a page boundary. |
| **RSS-06** | An import fetches `http` and `https` only, and fetches **exactly the string that was checked**. curl will open `file://` and follows redirects, so a feed address is otherwise a way of reading the disk or reaching inside the network the server sits in. A null byte is refused wherever it sits. Imported XML is parsed with `LIBXML_NONET`, `resolveExternals = false` and `substituteEntities = false`, so a feed declaring a DTD cannot read a local file or make the server fetch anything. |
| **RSS-07** | The format drop-down was relabelled; the stored values did not change. |

Also:

- Every POST that reaches these views is already covered by `ezformtoken`,
  which validates a per-session token on every POST from a logged-in user and
  injects the field into every form. The OPML controls inherit it.
- A feed that is **not active** is not listed in a document. It cannot be
  fetched, so listing it would hand a reader a dead address and publish the name
  of a feed somebody deliberately took out of service.
- An export cannot list itself.

---

## Not failing

| | |
|---|---|
| Public address | `/rss/feed/…` catches everything `generateOPML` can throw and falls back to `emptyOPML()`. A version nobody writes, or a document that could not be produced, answers with an error rather than an empty body and a 200. |
| Empty export | An export with no outlines still produces a valid document — OPML requires at least one outline in the body. |
| Missing draft | The edit draft can be collected by a timeout, or removed from another window, between drawing the page and pressing Save. The published row is taken up again so the save lands; an export that is genuinely gone is reported. *(Before this, that was a fatal: `Call to a member function setAttribute() on null`.)* |
| Cancelling an edit | Removing a draft removes only the draft's own outlines. *(Before this, it emptied the published document.)* |
| Long values | Text is cut to the column width before it is stored, not by the database — a strict server refuses the row instead of trimming it, and the save would fail with nothing to show for it. |
| Bulk | Adding and removing stop at `MAX_BULK`; the page never offers more than that, so a longer list did not come from the page. |
| Nesting | The writer stops at `MAX_DEPTH`. Two outlines inside each other are written nowhere rather than followed. |
| Size | `opmlMaxOutlines()` bounds how much of a document is built at once. |
| Rubbish in the database | A head column that is not JSON reads as nothing filled in. A row pointing at a feed that has been deleted is left out. |

---

## Testing

**Unit and security, no database:**

```sh
php vendor/bin/phpunit --testsuite security     --filter eZRSSSecurityTest
php vendor/bin/phpunit --testsuite kernel-classes --filter eZRSSExportOPMLItemTest
```

**Functional, against a live installation.** These need a database with RSS
exports in it and are run from the installation root:

| script | |
|---|---|
| `ai/bin/one/test_opml_export.php` | the outlines, the document, the spec checks, the edit page |
| `ai/bin/one/test_opml_hardening.php` | hostile values, bulk, nesting, the search, the import guards |
| `ai/bin/one/test_rss_list_pagination.php` | `/rss/list` paging and sorting |
| `ai/bin/one/make_rss_examples.php --count=4000` | example feeds to page through; `--remove` takes them out |
| `ai/bin/one/make_opml_feed.php --export=2 --feeds=12` | make one export an OPML document; `--revert` puts it back |
| `ai/bin/one/clear_rss_draft.php --export=2` | clear an edit draft left behind by an interrupted edit |

Each functional script builds what it needs and removes it again. They do not
touch an export somebody may have open, because a draft left behind locks that
export's edit page until the timeout runs out.

**By hand:**

1. `/rss/edit_export/<id>` → **Feed format** → `OPML 2.0` → **OK**.
2. Reopen it. Search the browser, change the page size, sort a column, tick some
   feeds, **Add selected feeds**.
3. **Add group**, put feeds inside it with the *Inside* menu, **OK**.
4. Fetch `/rss/feed/<access url>` and put it through an OPML validator.
