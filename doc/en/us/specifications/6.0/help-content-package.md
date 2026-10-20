# The help classes as a package: contents, build and handoff

Status: 19 October 2026, Exponential 6.0.15. Partner request: the help content classes as a package, with a build
handoff. Plan: [help-system.md](help-system.md); what the classes are: [help maintained as content](../../features/6.0/help-content.md).

Who should read it: whoever builds the package for a release and hands it to the package server, and an
administrator who wants the help classes on a site through **Setup > Packages** instead of the command line.

## In short

- The package **exp_help_classes** (vendor `7x`, type `contentclass`) holds the eight help classes in the class
  group **Help**: `help_manual`, `help_section`, `help_concept`, `help_task`, `help_reference`,
  `help_troubleshooting`, `help_screenshot`, `help_glossary_entry`.
- It is built from an installation that has the classes, with one command:
  `./console exp:help:content package -s <admin siteaccess>`. The class files are the kernel's own class XML, so the
  package installs as every other class package does.
- `./console exp:help:content verify-package -s <admin siteaccess> --file=<.ezpkg>` compares a built package with
  the classes of the release, **without a database**.
- The package is **not** the whole help system. Sections, the object state group, the folder tree and the roles hold
  ids of the installation; `./console exp:help:content install` makes them, and completes the classes a package
  installed. It is idempotent: after the package it prints only the parts the package does not carry.
- Building is done here; **publishing to the package server is the owner's step** (below).

## Contents

1. [What the package contains](#what-the-package-contains)
2. [What it does not contain, and why](#what-it-does-not-contain-and-why)
3. [Building it](#building-it)
4. [Checking it](#checking-it)
5. [Version](#version)
6. [Handoff to the package server](#handoff-to-the-package-server)
7. [Installing it on a site](#installing-it-on-a-site)
8. [Files](#files)
9. [Related pages](#related-pages)

## What the package contains

| Part | Content |
|---|---|
| `package.xml` | name `exp_help_classes`, vendor `7x`, type `contentclass`, install type `install`, license GPL, state `stable`, the release (below), one `provide` per class and one install item per class, in the order of `expHelpContentSchema::classes()` (the relation lists name their allowed classes by identifier, so the order does not matter) |
| `ezcontentclass/class-<identifier>.xml` | each class as `eZContentClassPackageHandler::classDOMTree()` writes it: names and descriptions, identifier, remote_id (`help-class-<identifier>`), name pattern, container flag, the group **Help**, and every attribute with its datatype, flags and datatype parameters (string lengths, the selection options, the relation list's allowed classes) |

The selection options (`audience`, `platform`) are the ones of the installation the package is built from. Build on
an installation with the default options (`exphelp.ini [ContentSettings] Audiences[]`, `Platforms[]` not
overridden), so the package carries Visitors, Members, Editors, Administrators and Web, Admin, Mobile. A site with
other options gets its own appended by `exp:help:content install`; options are only ever appended, never reordered.

## What it does not contain, and why

| Not in the package | Made by | Why |
|---|---|---|
| sections `help_public`, `help_internal` | `exp:help:content tree` | a section is a row with an id of the installation; the roles' limitations name it |
| the object state group `help_status` (Draft, Review, Released) | `exp:help:content tree` | the same, and its first state is given to every object of the installation once |
| the folder tree (`help-root`, `help-pool` ...) | `exp:help:content tree` | it goes below the node `[ContentSettings] ParentNode` names, which differs per site |
| the roles Help editor, Help release, the Anonymous policy | `exp:help:content roles` | they limit by section, state and subtree ids of the installation |
| topics, manuals, figures, the glossary | `exp:help:content seed-glossary`, `seed-topics`, or the editors | content, not structure; the seeds ship in `share/help/` |

A content class package cannot express any of these without node, section or state ids that are wrong on the next
installation. The command finds and creates them by identifier and remote_id on every site, idempotently.

## Building it

On an installation of the release (a throwaway installation is best; never a production site with changed help
settings):

```bash
./console exp:help:content classes -s <admin siteaccess>             # the classes, if not yet there
./console exp:help:content classes -s <admin siteaccess>             # "No change." (the acceptance test)
./console exp:help:content package -s <admin siteaccess> --dry-run   # what would be written
./console exp:help:content package -s <admin siteaccess> [--output=<dir>] [--version=6.0.15]
```

`package`:

1. reads the eight classes by identifier; if one is missing it stops with "run exp:help:content classes first";
2. writes the package directory `<dir>/exp_help_classes/` (`package.xml`, `ezcontentclass/`), the maintainer and the
   change log entry from the user of `--login` (default `admin`);
3. exports it as `<dir>/exp_help_classes-<version>.ezpkg` (a gzip compressed tar file, as `ezpm.php export` writes);
4. checks the archive with `verify-package` and prints `PASS wrote ...`, or the differences and `FAIL` (exit 1).

`--output` defaults to `var/<site>/cache/exphelp/packages`. An existing package directory and archive of the same
version in that directory are replaced. Nothing is written into `var/storage/packages/`: the package is not
imported into the installation it was built on.

## Checking it

```bash
./console exp:help:content verify-package -s <admin siteaccess> --file=<dir>/exp_help_classes-6.0.15.ezpkg
./console exp:help:content verify-package -s <admin siteaccess> --file=<dir>/exp_help_classes      # a directory
```

`verify-package` reads the archive in PHP (no unpacking to disk, no program is started) and compares it with
`expHelpContentSchema::classes()` of the release it runs on:

- `package.xml`: the name, the type `contentclass`, a `provide` and an install item for each of the eight classes, no
  other class;
- each class file: its identifier, the container flag, the class group **Help**, every attribute of the schema with
  its datatype and required flag, and no attribute the schema does not have.

It prints `PASS ... matches the help classes of this release` (exit 0) or one line per difference and `FAIL` (exit 1).
It needs no database, so it can run on the package server's checkout too.

The acceptance test of a release, on a **new throwaway installation** (never the live site):

1. install the package (**Setup > Packages > Upload**, then **Install**, or `php ezpm.php import <file>` and
   `php ezpm.php install exp_help_classes`);
2. `./console exp:help:content classes -s <admin siteaccess>` prints **No change.**: the package installed every
   class, attribute and option the command would have made;
3. `./console exp:help:content install -s <admin siteaccess>` then makes the sections, states, tree and roles, and a
   second run prints **No change.**;
4. remove the throwaway installation.

## Version

- The package's version is the Exponential release it was built from, split as the 7x packages are: version
  `6.0`, release `15` for 6.0.15 (`expHelpContentPackage::release()`); the file is `exp_help_classes-6.0.15.ezpkg`.
- `--version=` builds another number (a correction of a release's package: build it from the corrected release and
  give it the next number; a published file is never replaced, see the release rules of the project).
- A new package is needed when `expHelpContentSchema::classes()` changes (a class or attribute added). The schema
  only grows: attributes are never changed or removed, so an older package still installs, and
  `exp:help:content classes` adds what it lacks.

## Handoff to the package server

The builder hands over; the owner publishes. What the builder delivers:

| Item | Where |
|---|---|
| the archive `exp_help_classes-<version>.ezpkg` | the output directory of `package` |
| the output of `verify-package` (PASS) | in the handoff note |
| the output of the acceptance test (steps 2 and 3: "No change.") | in the handoff note |
| the commit of the release the package was built from | in the handoff note |

What the owner does (the package server's own procedure, outside this repository): copy the archive into the
package server's checkout for the release (`exponential/6.0/6.0.15/`), add it to that release's `index.xml` if it is
to be offered by the setup wizard, rebuild and commit the package server, then check that the download URL answers.
The setup wizard reads the index of its release from `package.ini [RepositorySettings] RemotePackagesIndexURL`.

Nothing is uploaded, pushed or published by the build itself.

## Installing it on a site

An administrator who prefers packages to the command line:

1. **Setup > Packages > Upload**, choose `exp_help_classes-<version>.ezpkg`, then **Install** on its page. The classes
   appear in the class group **Help**.
2. Run `./console exp:help:content install -s <admin siteaccess>` once (sections, states, tree, roles). It finds the
   classes the package installed and changes nothing on them.
3. Optionally `seed-glossary` and `seed-topics` for the starting content, as on any installation.

Uninstalling the package removes the classes (it asks first when objects of a class exist, and removes them with
it); `exp:help:content uninstall --with-content` is the complete way back, with the tree, states and sections.

## Files

- `kernel/classes/help/content/exphelpcontentpackage.php`: `expHelpContentPackage` (definition, `build()`,
  `verifyDirectory()`, `verifyArchive()`, `readArchive()`).
- `kernel/private/classes/commands/exphelpcontent.php`: the actions `package` and `verify-package`.
- `tests/tests/kernel/classes/help/expHelpContentPackageTest.php` (no database).

## Related pages

- [Help maintained as content](../../features/6.0/help-content.md)
- [Help system plan](help-system.md)
- [Help standards](help-standards.md)
- [Packages](../../guides/packages.md)
