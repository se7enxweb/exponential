# Look inside a package, compare it with your site, import single items

This page is for administrators who install content packages on a site that already has content. A content package
used to be a sealed box: install it and hope. Since 29 and 30
September 2026 you can browse every file in it, see how each of its content
classes and objects differs from your site, and import only the items you
choose, after a confirmation that lists exactly what will change.

All of it is in the kernel (no extension needed) and uses the administration's
package views.

## 1. Browse a package's files

Open **Setup > Packages**, click a package. `package/view/full/<name>` now has a
contents browser.

- Every file of the package's own directory is listed: `package.xml`, every
  content class and object item, `simplefiles/`, `documents/`.
- 25 to 1,000 files, or all, per page; filter by type or by a path search.
- Click **View** on a row: an `.xml` item is pretty-printed, a content object
  shows its own attributes and translations read straight out of the file, an
  image is shown inline. **Download** gives the raw bytes (`package/viewfile`).
- A hidden file or directory (the package's own `.cache/`) is never shown.
- A package that is already installed offers **Reinstall** next to Uninstall,
  going through the same install wizard. The install wizard's own header and
  error pages link back to the package and the package list on every step.

The browser keeps its state in view parameters, like other paged kernel views:

```
package/view/full/slash_quotes/(offset)/4300/(file)/4342
```

The parameters are `(type)`, `(search)`, `(limit)`, `(offset)` and `(file)`,
each left out at its default. The search (the only free text) is encoded twice,
because the path is decoded once before it is split. Old links with query
strings are redirected once to the view-parameter form.

The page is laid out as an administration page: header badges for version,
type and install state; one definition list for summary, state, license,
maintainers, documents and description; an actions bar that leads with the safe
primary action (Install, or Export to file when installed) and sets Uninstall
apart on the right; a file list with type badges and sizes; a pager above and
below. Buttons follow their own policy now: Install, Reinstall and Uninstall
need `package / install`, Export to file needs `package / export`; when there is
nothing to install the page says why (a site package imported by the setup
wizard with the packages it requires, a package without install items, or a user
without `package / install`).

Security fixes in this group: a package's description is printed as text (it
comes from an uploaded `package.xml`), and a file opened from `package/viewfile`
is sent with a sandboxing Content-Security-Policy so an SVG with script cannot
run as the signed-in user.

## 2. Compare a package with the site

Click **Compare** (before Reinstall) on a package that installs content, or open
`package/compare/<name>` (policy `package / read`, like `package/view`).

The comparison is read-only: nothing is installed or stored.

- **Content classes** are matched by identifier. Both sides are serialized by
  the same code that writes a class into a package, so names, settings and per
  attribute the datatype, names, flags, placement and datatype parameters
  compare like for like.
- **Content objects** are matched by remote id. The site's current version is
  serialized by each datatype's own package serializer, then values are
  normalised per datatype: plain values as text, rich text as canonical XML,
  relations by the remote ids they point to, images and files by name, size and
  checksum, anything else by its serialized XML. Translations only on one side
  and node placement are compared too. Objects under a package's top node that
  the package does not carry are listed as "only on the site".

Each item gets a status shown as a chip with its own glyph and label as well as a
colour (so colour is never the only signal): **new**, **changed**, **only on
the site**, **identical**, **class missing**. The chips also filter. You can
filter by status and class, search by name or remote id, show 25 to 250 rows per
page and sort by status, name, class or number of differences (all as view
parameters such as `(sort)/<column>/(dir)/<asc|desc>`, kept by the pager).

Open an item to see its values side by side (stacked on a narrow screen):
removed text struck through in red, added text underlined in green, the XML of
rich text and serialized values at hand, and its placement. **Compare again**
rebuilds the cached comparison. The whole comparison is built once, in batches
with single-table queries, and cached in the cache directory keyed by the
package's files and the site's content and class state; every value from the
package is escaped.

## 3. Import chosen items

With `package / install` on top of `read`, each row that is not identical has an
**Import** button and a checkbox, beside **Import selected** and **Import all
changes of this filter**. The open item has **Import this item** and a tick per
value, so a value can be kept as the site has it.

Every import is a POST guarded by the form token and leads to a confirmation that
lists exactly what will be set and kept, and why an item cannot be imported.
Confirm to import; the result is shown item by item, with the next step when a
filter holds more than one step (at most 25 items per request).

Rules the importer follows (`eZPackageComparisonImport`):

| Situation | What happens |
|---|---|
| New object | Created where the package places it. |
| Changed object | Gets the package's values; values you unticked keep the site's value. |
| New class | Created. |
| Changed class | Updated in place (the class installer can update an existing class; the install item answer is "update existing"). |
| Locations the site already has | Never duplicated or moved. |
| Something only on the site | Never removed. |
| Translations and settings only the site has | Kept. Before importing, the object's language masks are repaired to list every language it has values in; afterwards a missing translation is copied back; the main language and always-available setting stay the site's. |
| Object whose values need a class imported first | Imported together with that class; the confirmation lists the class first, marks the values it lets in as "Added by the class import" and follows the class's own tick. A class left out stays out, and so do the values that need it. |
| Not published, class differs, or no values | Refused before confirming, with the reason. |
| A new version with a value twice or a lost translation | The whole item is rolled back with the reason. |

After a class import the cached comparison is rebuilt; after an object import
only the imported objects are compared again. A package's install now also places
objects whose parent comes later in the package: passes are repeated per parent,
parents are found by remote id regardless of language, and the package's node
remote id is kept on the placed node. A package's new version of an existing
object no longer carries a translation's values twice.

## Package wizards: Back button and order

Every step of the package creation wizards has a **Back** button beside **Next**.
What you entered is kept when valid, and a step is initialized only on its first
visit. Back from the first step returns to the choice of wizard, and Back there
to the package list. The class and extension steps show the choices made on them
when you return. The content object export wizard leaves templates related to
the objects out unless you choose them. The Create package page lists the
wizards in `[CreationSettings] HandlerList[]` order (content, classes,
extensions, site styles).

## Old packages work again

- An archive in the old V7 tar format (no `ustar` magic; the class packages of
  the 3.x releases) made the import hang forever. It is now opened with the V7
  reader; every other archive keeps the GNU reader so long file names work.
- Class definitions from before 3.9 that carry only `<name>` install again.
- A datatype written for PHP 4 and 5 with a constructor named after its class
  (for example the newsletter list and edition types) is created without
  `eZDataType::__construct()` and its old constructor is called, so classes of
  such packages can be installed again.
- Importing the first package into an installation whose package repository does
  not exist yet raises no warning; a user with no package policy gets a plain
  false.

## Related pages

- [ezpm: the package manager on the command line](ezpm-package-manager-cli.md), [package installer batching](package-installer-batching.md), [package licenses and versions](package-licenses-and-versions.md)
- [Kickstarter: install a whole site from one file](kickstarter-cli.md), [installer logs and seed data](../../specifications/6.0/installer-logs-and-seed-data.md)
- [About and copyright pages](about-and-package-pages.md), [translations and languages](translations-and-languages.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- History: [16 to 30 September 2026](../../history/2026/2026-09b.md), [June 2026, second half](../../history/2026/2026-06b.md)
