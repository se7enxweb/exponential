---
title: The license of this documentation
audience: editor, admin, developer, user
category: reference
summary: The documentation of Exponential is free documentation under the GNU Free Documentation License 1.3 or later. The copyright notice, what it covers, and the older pages that keep their own terms.
keywords: license, copyright, GFDL, GNU Free Documentation License, free documentation, attribution, copying, permission
type: reference
---
# The license of this documentation

## Copyright and permission notice

Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.

Permission is granted to copy, distribute and/or modify this document under the terms of the GNU Free
Documentation License, Version 1.3 or any later version published by the Free Software Foundation; with no
Invariant Sections, no Front-Cover Texts, and no Back-Cover Texts. A copy of the license is included in the
section entitled "GNU Free Documentation License".

## GNU Free Documentation License

The license is the file [LICENSE-GFDL-1.3.txt](LICENSE-GFDL-1.3.txt) in this directory: the text of the
GNU Free Documentation License, Version 1.3, 3 November 2008, exactly as the Free Software Foundation publishes it
at <https://www.gnu.org/licenses/fdl-1.3.txt>. It is also on the web at <https://www.gnu.org/licenses/fdl-1.3.html>.
Version 1.3 is the latest version of the license.

The help in the administration (Help, the "?" button) shows the license on every page it opens, and links to
[Free documentation: use it to help yourself and others](help/free-documentation.md), which explains in plain
words what the license lets you do.

## What the notice covers

The notice covers the documentation the Exponential project wrote and maintains in the `doc/` directory of the
repository: the help pages (`doc/eng-us/help/`), the guides, the installation chapters, the feature, upgrade and
specification documents of the 6.x line (`doc/eng-us/features/6.0/`, `doc/eng-us/bc/6.0/`, `doc/eng-us/specifications/6.0/`), the
changelogs of 6.x and of the extensions, the history, and the other pages at the top of `doc/`. This includes
every 6.x page contributed through pull requests, whose copyright the contributors assigned to the Exponential
Foundation and 7x.

There is no separate copyright of "the Exponential contributors": every contributor assigns the copyright in a
contribution to the Exponential Foundation and 7x through the Contributor Licensing Agreement, so the notice names
only those two. Contributors are credited (changelogs, commit authorship, the recognition pages), and credit is not
a copyright claim; see [Copyright and copyleft](help/copyright-and-copyleft.md).

The software itself is not documentation: its source code stays under the GNU General Public License, version 2
or any later version ([LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE) and
`COPYRIGHT.md` in the repository).

## Older pages with their own terms

Some files in `doc/` were inherited from the documentation of the original software, written by eZ Systems AS and
its contributors between 1999 and 2014, and kept here for reference. The Exponential project does not hold their
copyright and does not relicense them. They remain distributed under the terms they came with, the license of the
repository (the GNU General Public License, version 2), and the copyright notice of eZ Systems AS applies to them.
The help shows a different notice on these pages. They are:

- the feature, upgrade, specification and changelog documents of the versions before 6.0
  (`doc/eng-us/features/2.x` to `doc/eng-us/features/5.x`, `doc/eng-us/bc/4.x` and `doc/eng-us/bc/5.x`, `doc/eng-us/specifications/3.x`,
  `doc/eng-us/specifications/4.x` and `doc/eng-us/specifications/trunk`, `doc/changelogs/2.9` to `doc/changelogs/5.x`, and
  `doc/changelogs/README.md`);
- `doc/eng-us/shop/`, `doc/eng-us/release-overview` and `doc/eng-us/standards/header.txt`.

The example configuration files `doc/eng-us/examples/htaccess` and `doc/eng-us/examples/ezpublish.conf` were rewritten for
Exponential 6 and are free documentation like the rest; the rewrite rules in them are those of `.htaccess_root`, part
of the software and under its license.

`doc/eng-us/branch-overview.md` is new documentation under the GNU Free Documentation License, except its section "Historical branches
(original SVN, 3.x)", which keeps the branch records of the original SVN repository under the terms they came with and
says so in the page.

The list is the setting `[License] OtherLicensePathRules[]` in `settings/exphelp.ini`, which the help reads to
choose the notice of a page.

## Contributing to the documentation

A change to a page under the notice above is accepted under the same license, the GNU Free Documentation License
1.3 or any later version, so that everyone keeps the same freedoms with the changed page. A contribution that
cannot be published under these terms (text copied from a source with a different license, for example) has to
say so in its pull request.

## Copyright and copyleft

Since 29 October 2026 the copyright of Exponential is held by the Exponential Foundation and 7x together, the
Foundation named first: "Exponential Copyright © 1998-2026 Exponential Foundation and 7x", in files
"Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved." Who holds it and why, what the
GPL lets you do, the third-party notices that stay and how to write the notice in new files and extensions:
[Copyright and copyleft](help/copyright-and-copyleft.md).
