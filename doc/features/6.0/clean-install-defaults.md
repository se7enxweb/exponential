# A clean installation that fits shared hosting and says Exponential

July and August 2026 made the first run of a new installation smoother. This
page lists what a person installing Exponential 6.0.15 now gets, in the order
they meet it.

## 1. The setup wizard works with your own database only (16 July)

Commit `fdf0d52028`. The wizard used to run `SHOW DATABASES` and open the `mysql`
system database, which needs privileges a shared host does not give.

- For MySQL and MariaDB the database step has a **Database name** field. The name
  you type is used directly; the wizard does not look at other databases.
- A failed connection, a wrong password or a missing database is shown as an
  ordinary message on the page. It no longer ends in a blank error page.
- The details go to `var/log/setup.log` (for example *eZStepSiteDetails: database
  requirement check failed (error code ...) for server ... user ... db ...*),
  never the password.
- The progress reporter no longer fails in a web request because the constant
  `STDOUT` does not exist outside the command line.

You need privileges only on your own database.

## 2. Packages bring what they need (29 July)

Commit `cb57cdd026`. `eZPackage::install()` first installs every package named under
*requires* in the package, then the package itself. A site package that is "import
only" (it carries no files of its own) still pulls in its image or demo content
package. A required package that cannot be found logs a warning; one that fails
stops the install. A package that is already being installed is skipped, so circular requirements do not loop.

## 3. The default content no longer says eZ and speaks American English (15 August)

Commits `4d9f7681b2`, `3dc808f3ec`, `71045a6cd8`, `e0f6916a36`, `ded9116491`,
`5f6edbd111`, `3ebaf8dcd9` change the seed data in `share/db_data.dba` that the
installer loads before a site package:

| What | Before | After |
|---|---|---|
| Default content language | `eng-GB` (English, United Kingdom) | `eng-US` (English, United States), also for every class, attribute and object name |
| Root folder name | the old product name | `Websites` |
| Welcome article | the old product name | `Welcome to Exponential` |
| Root description and metadata | product text of the former vendor | an introduction of Exponential with links to exponential.earth, share.exponential.earth (articles, forums, support), software.se7enx.com (downloads), projects.exponential.earth and the ezpedia.exponential.earth wiki |
| Author, copyright | former vendor | Exponential Foundation |
| URL alias keywords | former product name | `exponential`, `welcome to exponential` |

The seed touches the tables `ezcontent_language`, `ezcontentclass`,
`ezcontentclass_attribute`, `ezcontentobject_attribute` and `ezcontentobject_name`.
All the links in the description were checked to answer before they went in.

If you install with a site package, the package's own content comes after the seed
and wins where they overlap.

## 4. Kickstart names (15 August)

All access types of a kickstart install register the siteaccesses `site` and
`admin`; see [Kickstarter](kickstarter-cli.md).

## 5. PHP 8 and other install blockers (15 August, 31 July)

- Unserializing `ezinteger`, `ezobjectrelation`, `ezobjectrelationlist` and `ezxmltext`
  values tolerates missing XML nodes and null values (`28d414f422`).
- libxml errors during XML text parsing are suppressed; an embedded `object_remote_id`
  is resolved to an object id in XHTML output.
- The URL alias and class attribute caches ignore null and empty values.
- A content operation returns false only when a node is really missing.
- `settings/transform.ini [search]` has `Commands[]=lowercase`, which adds the lowercase transformation to the search group.
- `eZPersistentObject` returns an integer when it computes the next order number
  on PHP 8.4 (`9cad41280a`), which had broken installs.

## 6. Content package export is more forgiving (7 August)

A node whose initial language is missing, or whose serialized attribute node is
absent, is skipped by the content package export instead of ending the run
(`752adad843`, `8af48c1e59`). See [ezpm](ezpm-package-manager-cli.md).

## Related

- [Kickstarter](kickstarter-cli.md), [Platform SQLite installer](platform-sqlite-install.md)
- Month pages: [July 2026](../../history/2026/2026-07.md), [August 2026](../../history/2026/2026-08.md).
