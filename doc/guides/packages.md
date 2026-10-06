# Packages: what they carry, where they live, and how to handle them safely

This guide explains the package pages of the administration interface (**Setup > Packages**): what a package is,
where packages are kept, how the list, a package's page, the upload, the install and uninstall steps, the download
and the removal work, which packages the installer depends on, what is checked before a package is accepted, and
what to do when something does not behave as expected.

It is written for administrators who move classes, content or extensions between installations, and for whoever
looks after the packages that new installations are built from. Every page, field and rule below was checked
against the code of this repository on 6 October 2026; the files are listed under [References](#references).

[Guides](README.md) · Related: [Audit trail](../features/6.0/audit-trail.md) ·
[Paging in the administration interface](../features/6.0/admin-list-paging.md) ·
[Safe redirects](../features/6.0/safe-redirects.md) · [Extensions](extensions.md)

## In short

- A **package** is a directory with a `package.xml` and the files it names: content classes, content objects,
  extensions, settings files, images and documents. A package is **installed** (its items are created on the site),
  **imported** (it is only kept, as a site package is) or **exported** (written as one `.ezpkg` file).
- Packages are kept in **repositories**, the directories of `var/storage/packages/`: `local` for packages made or
  imported here, and one directory per vendor (`7x`, `ez-systems`, ...).
- **The setup wizard installs new sites from these packages, and the published packages are built from them.** A
  package the installer takes as a source is marked **Installer source** everywhere, and removing one needs its own
  confirmation.
- **Setup > Packages** (`/package/list`) shows every repository with its counts and one card per package, with a
  search, filters by repository, type and state, a sort and pages. A package's page is read-only until you press a
  button.
- An upload is inspected before anything of it is written: a file that is not a package, too large, or that holds a
  path outside the package, a link or a device is refused with a reason. An existing package is never overwritten.
- **Removing a package deletes its directory; it does not uninstall it.** What the package installed stays.

## Contents

- [1. Where packages live](#1-where-packages-live)
- [2. The package list](#2-the-package-list)
- [3. A package's page](#3-a-packages-page)
- [4. Installer sources](#4-installer-sources)
- [5. Downloading a package](#5-downloading-a-package)
- [6. Uploading a package](#6-uploading-a-package)
- [7. Installing and uninstalling](#7-installing-and-uninstalling)
- [8. Creating a package](#8-creating-a-package)
- [9. Removing packages](#9-removing-packages)
- [10. Permissions](#10-permissions)
- [11. From a shell](#11-from-a-shell)
- [12. Troubleshooting](#12-troubleshooting)
- [References](#references)

## 1. Where packages live

```
var/storage/packages/          site.ini [FileSettings] StorageDir + package.ini [RepositorySettings] RepositoryDirectory
├── local/                     packages made by the creation wizards or uploaded without a vendor
├── 7x/                        package.ini [RepositorySettings] Vendor: the setup wizard's repository
│   ├── sevenx_classes/
│   │   ├── package.xml        the definition: name, version, type, maintainers, dependencies, install items
│   │   ├── ezcontentclass/    one XML file per class
│   │   └── .cache/            the kernel's parsed copy of package.xml (not part of the package)
│   └── ...
└── ez-systems/
```

A directory of a repository without a `package.xml` is not a package. The list counts such directories per
repository ("directories without a package definition") but does not show them: they are leftovers of exports or
wizards that ended early. A directory whose `package.xml` cannot be read is listed in a warning above the list
with its path, instead of breaking the page as it did before.

The repository a package lands in on upload is the one its `<vendor>` names, turned into a directory name
(`eZ systems` becomes `ez-systems`); a package without a vendor goes to `local`.

## 2. The package list

**Setup > Packages**, `/package/list` (every repository) or `/package/list/<repository>` (one).

1. **The figures**: packages, installed, not installed, imported only, installer sources, total size and files, and
   unreadable directories.
2. **Repositories**: one tile per repository with its packages, size, install states, installer sources and
   leftovers. The setup wizard's repository is marked. A tile opens that repository's list.
3. **Find packages**: a search over name, summary, type, vendor, version, maintainers and required packages (every
   word must match), the repository, the type, the state (installed, not installed, imported only, no install
   items, installer sources) and the sort (name, last change, size, version, type, repository, install state).
   **Apply** turns the choice into the page address, so it can be bookmarked and the pages keep it:
   `/package/list/7x/(search)/demo/(type)/contentobject/(sort)/size`. **Reverse** turns the order around.
4. **The cards**: name and version, type, install state, **Installer source**, summary, repository and vendor,
   maintainers, the packages it requires (a missing one is marked) and the packages that require it, size and files
   (the kernel's `.cache` left out), the last change (the newest file of its directory) and when it was packaged.
   **View** opens its page, **Download** its `.ezpkg`.
5. **Per page** and the pages. The sizes on offer are `admininterface.ini [PaginationSettings]
   ItemsPerPageList_package_list` (10, 25, 50, 100 when not set); your choice is kept as a preference. Without a
   choice the list uses `ItemsPerPage[package/list]` (25). See [paging](../features/6.0/admin-list-paging.md).
6. **Remove selected**, **Import new package** and **Create new package**, each greyed out when your role does not
   allow it.

Before 6.0.15 the list showed only the `local` repository unless another was chosen, so the installer's own
packages were out of sight; the pager lost the repository; and the removal title read "Remove section?".

## 3. A package's page

`/package/view/full/<name>` or, for a package outside `local`, `/package/view/full/<name>/<repository>`. Nothing on
this page changes anything until a button is pressed.

- **The title row**: name, version, type, install state, Installer source.
- **The actions**: Compare (the package's content against the site, `package/compare`), Install or Reinstall,
  Download, Uninstall, Remove. Each is shown only when your role allows it and the package can do it; when there is
  nothing to install the page says why.
- **Details**: repository and vendor, state, license, maintainers, documents, released, packaged (time and host),
  the Exponential version it was made for, what it requires and what requires it, size and last change, and its
  description.
- **What it carries**: its content classes, content (install items and object files), extensions, settings files,
  other install items, and a table of its files by kind with their sizes.
- **Changelog**, then **Package contents**: every file, filtered by kind and searched by path, paged, each viewable
  (the first megabyte, XML pretty-printed, a content object summarised) and downloadable.

An address that names a view mode without a template (`/package/view/nosuchmode/...`), a package name with a path
in it (`..`, a slash) or a repository the storage does not have is answered with "not found".

## 4. Installer sources

A package is an installer source when at least one of these holds:

| Reason shown | Rule |
|---|---|
| In the setup wizard's repository | It is in `package.ini [RepositorySettings] Vendor` (`7x`); the published packages are built from this repository |
| A site package the setup wizard offers | Its type is `site`; the setup wizard lists every site package |
| Required by another installer source | An installer source requires it (`<require type="ezpackage">`), directly or through others |

On alpha this marks `sevenx_classes`, `sevenx_multisite` and `sevenx_multisite_democontent`. The card has a blue
edge, the package's page says why, and a removal of one needs the box "I understand that ... installer source(s)
will be deleted" ticked as well.

## 5. Downloading a package

**Download** (the list and a package's page) is `GET /package/export/<name>[/<repository>]`; the older **Export to
file** button (`ExportButton`) still posts to the package's page and does the same. The package is written as a
gzip compressed tar (`<name>-<version>.ezpkg`) into your own export directory
(`var/site/cache/packages/export<user id>/`, the cache directory of `site.ini [FileSettings] VarDir`), sent and removed again. The package itself is only read.

The file is sent in pieces of 256 KiB, never read whole, after every output buffer of the page is closed. A
`Range` header (`bytes=a-b`, `bytes=a-`, `bytes=-n`) gets `206 Partial Content`, a range beyond the end
`416`. Under Exponential Velocity the engine collects a persistent worker's answer before it sends it, so a very
large package costs that much memory there for the moment of the download; the download is byte for byte the same
on both servers. The request ends outside any `try`/`catch`, so Velocity's way of ending it cannot append the page
to the file.

Files out of a package (Download in the contents) come from `/package/viewfile/<name>/<index>[/<repository>]` the
same way; an image is shown inline, everything else is offered as a download with `nosniff` and a sandboxing
Content-Security-Policy.

## 6. Uploading a package

**Import new package** on the list opens `/package/upload`. Choose an `.ezpkg`, `.tar.gz` or `.tgz` file and
press **Import package**. Before anything is written, the archive is inspected:

1. its name ends in an allowed suffix and the file starts with the gzip signature;
2. it is at most `MaxArchiveSize` bytes and has at most `MaxEntries` entries that unpack to at most
   `MaxUnpackedSize` bytes;
3. every entry is a plain file or directory with a relative path: no `..`, no absolute path, no back slash, no
   control character, no symbolic or hard link, no device or fifo;
4. it has a `package.xml` at its top that is well formed, has `<package>` as its root and no document type, names a
   valid package (lower case letters, digits and `_`) and has its version and packaging information, and its vendor
   gives a repository name;
5. no package of that name exists in any repository.

The page names the first rule that failed. When all hold, the package is extracted into its repository; a package
with install items opens its install step next, any other its page.

The limits, with their defaults:

```ini
# settings/override/package.ini.append.php
[UploadSettings]
MaxArchiveSize=268435456
MaxUnpackedSize=2147483648
MaxEntries=50000
AllowedSuffixes[]
AllowedSuffixes[]=ezpkg
AllowedSuffixes[]=tar.gz
AllowedSuffixes[]=tgz
```

PHP's own `upload_max_filesize` and `post_max_size` apply first; the page shows the server's limit.

The archive check also runs inside `eZPackage::import()`, so the setup wizard's upload and `ezpm import` refuse the
same archives.

## 7. Installing and uninstalling

**Install** on a package's page opens `/package/install/<name>`: the list of what will be installed, with
**Install package** and **Skip installation**. A package that is installed already says that installing again
repeats every item. Each item is installed in turn; an item may ask how to handle a conflict (a class that exists,
for instance) with its own step.

**Uninstall** opens `/package/uninstall/<name>`: what uninstalling removes from the site, content included, with
**Uninstall package** and **Skip uninstallation**. Uninstalling cannot be undone.

Both need `package/install` for the package's type (they asked for the policy for any type before).

## 8. Creating a package

**Create new package** opens `/package/create`: one tile per wizard your role may use (content object export,
content class export, extension export, site style). The wizard asks for the parts, then name, summary, version,
license and maintainer, and writes the package into `local`. Nothing on the site changes.

## 9. Removing packages

1. Tick the packages on the list (or press **Remove** on a package's page) and press **Remove selected**.
2. The confirmation lists **what goes**: per package its directory, size and files, whether it is installed, which
   packages require it, and why it is an installer source. Above the list: how many packages, files and bytes,
   installed packages, installer sources and packages required by packages that stay.
3. Anything selected that cannot be removed is listed with its reason: not a package name, no such package, a name
   in more than one repository, not allowed for its type, or a directory containing links.
4. Press **Remove n package(s)**; with installer sources among them, tick the box first.

The directory is deleted with everything in it. The package is not uninstalled: classes, content and files it
installed stay; only the record that it is installed goes, so it can no longer be uninstalled from here. Download a
package first to keep a copy. Each removal is recorded in the audit trail as `system.package.remove` with the
package's name, version, repository, files, bytes, whether it was installed and whether it was an installer source.

Only what the confirmation page offered can be removed: a `ConfirmRemovePackageButton` posted without it removes
nothing. A package whose directory contains a symbolic link is never offered: the kernel's recursive delete
follows a link to a directory and would empty its target.

## 10. Permissions

| Action | Policy | Limited by |
|---|---|---|
| See the list | `package/list` | |
| A package's page, its files, Compare | `package/read` | Type |
| Download | `package/export` | Type |
| Upload | `package/import` | |
| Install, Reinstall, Uninstall | `package/install` | Type |
| Remove | `package/remove` | Type |
| Create | `package/create` | Type, creator, role |

Every form carries the form token; a post without it is refused with 403.

## 11. From a shell

```bash
php ezpm.php list --allow-root-user                            # the packages of the repositories
php ezpm.php export sevenx_classes -d var/tmp --allow-root-user  # write an .ezpkg (only reads the package)
php ezpm.php import var/tmp/x.ezpkg --allow-root-user            # the same archive checks as the upload page
php ezpm.php help --allow-root-user                            # every command
```

## 12. Troubleshooting

| You see | Why | What to do |
|---|---|---|
| A package is missing from the list | Its `package.xml` cannot be read, or names another package than its directory | The warning above the list names the directory; fix or replace `package.xml` |
| "N directories without a package definition" | Leftovers of exports or wizards that ended early | Look at them on the server; nothing reads them |
| Upload: "points outside the package" or "is a link or a device" | The archive was made with links or unsafe paths | Make the archive again from the package directory (`ezpm export`) |
| Upload: "not a well formed package definition" | `package.xml` is broken | Check it with `xmllint --noout package.xml` |
| Upload: "already exists" | A package of that name is in some repository | Remove the old one first, or give the new one another name |
| Download answers "not found" | Exporting failed (the debug output says why) | Check that `var/site/cache/packages/` (the cache directory) is writable for the web server and Velocity |
| Remove is greyed out for one package | Its directory contains a link | Remove it on the server after checking where the link points |

## References

- Views: `kernel/private/classes/views/package/list.php`, `view.php`, `export.php`, `viewfile.php`, `upload.php`,
  `install.php`, `uninstall.php`; module definition `kernel/package/module.php`.
- Classes: `eZPackageCatalog` (repositories, cards, installer sources, search, sort, contents),
  `eZPackageRemovalPlan`, `eZPackageUploadInspector`, `eZPackageDownload`, `eZPackageRequestGuard`,
  `eZPackageFileBrowser`, `eZPackage` (all in `kernel/classes/`).
- Templates: `design/admin/templates/package/` and `design/admin4/templates/package/` (`exp_style.tpl`, `list.tpl`,
  `confirmremove.tpl`, `view/full.tpl`, `upload.tpl`, `install.tpl`, `uninstall.tpl`, `create.tpl`).
- Tests (no database): `tests/tests/kernel/classes/packages/eZPackageCatalogTest.php`,
  `eZPackageUploadInspectorTest.php`, `eZPackageRequestGuardTest.php`, `eZPackageDownloadTest.php`.
- [Changelog 6.0.15](../changelogs/6.0/6.0.15.md#6-october-2026-the-package-pages)
