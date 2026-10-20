---
title: Packages for export
audience: government, admin, developer
category: public-sector
order: 130
summary: For administrators and developers - how Exponential packages carry content objects, classes and files out of an installation (the package wizard, .ezpkg in classic XML or JSON, download, ezpm.php export), the permissions involved, and their use and limits for transfers and archives.
keywords: packages, ezpkg, package wizard, content object export, content class export, JSON package format, package/export, package/create, ezpm.php, download, transfer, archive, ManagedByDeployment, public sector
type: help
---
# Packages for export

This page is for administrators and developers. A **package** is a directory with a `package.xml` (or `package.json`)
and the files it names: content classes, content objects, extensions, settings, images and documents. Packages can be
created from an installation and exported as one `.ezpkg` file ([Packages](../../../guides/packages.md)).

## Contents

1. [Creating a content package](#creating-a-content-package)
2. [Downloading it](#downloading-it)
3. [Permissions and locks](#permissions-and-locks)
4. [Use and limits for records](#use-and-limits-for-records)
5. [Related pages](#related-pages)

## Creating a content package

**Setup > Packages** (`/package/list`), **Create new package** (`/package/create`): one tile per wizard your role may
use, among them **content object export** and **content class export**. The wizard asks for the parts, then name,
summary, version, license and maintainer, and the storage format (Classic XML or JSON), and writes the package into
the `local` repository. Nothing on the site changes ([The package wizard](../../../features/6.0/package-wizard.md)).

## Downloading it

- **Download** on the list or the package's page sends a gzip-compressed tar `<name>-<version>.ezpkg`; **Download as
  JSON** sends the JSON format. The package itself is only read.
- From a shell: `php ezpm.php export <name> -d var/tmp --allow-root-user`.
- Packages are installed elsewhere through upload and install, with the same archive checks.

## Permissions and locks

| Action | Policy |
|---|---|
| list, read, download | `package/list`, `package/read`, `package/export` |
| create | `package/create` |
| upload, install, remove | `package/import`, `package/install`, `package/remove` |

With `ManagedByDeployment=enabled` on the administration siteaccess, package create, export, upload, install and
uninstall are locked in the web interface; the command line still works
([Configuration managed by the deployment](../../../features/6.0/managed-by-deployment.md)).

## Use and limits for records

- Good for moving a set of content objects with their files and classes to another Exponential installation, for
  example a records copy kept as a separate, read-only site.
- A package holds the objects as exported, not their version history or audit records.
- The format is specific to Exponential; for long-term preservation, keep a format your archive accepts as well (see
  "File formats for preservation").
- Packages can carry settings files; review a package before giving it to anyone.

## Related pages

- [Packages](../../../guides/packages.md)
- [The JSON package format](../../../bc/6.0/package-json-format.md)
- [Exporting content](exporting-content.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
