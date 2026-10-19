---
title: Licenses and how they combine
audience: developer, designer, app-creator
category: extension-development
order: 60
summary: How the licenses you meet in extensions combine when you put code together - GPL 2 or later, GPL 2 only, GPL 3, LGPL, MIT, BSD, Apache 2.0 - which mixes are allowed, which are not, and how to record them.
keywords: license, compatibility, GPL-2.0-or-later, GPL-2.0-only, GPL-3.0, LGPL, MIT, BSD, Apache-2.0, combine, SPDX, bundled library
type: help
---
# Licenses and how they combine

Part 5, chapter 6. When you bring code from several places into one extension (a library, a snippet, another
extension's class), their licenses have to fit together. Part 1 introduced the licenses one by one
([Reading licenses before you reuse anything](../part-1-from-user-to-developer/reading-licenses-before-reusing.md),
with its table "Combining licenses: a quick table"). This chapter goes further: why the combinations work the way
they do, and how to record what you combined.

## The kernel's license sets the frame

Exponential is licensed under the GNU GPL version 2 or any later version (the project's `CONTRIBUTING.md` says
contributions are released under the same license). An extension runs inside the kernel's process and calls its
classes, so the usual view, and the one the Free Software Foundation takes, is that an extension distributed for
Exponential must be under a license compatible with the GPL. The simplest choice for your own extension is the
kernel's own: GPL-2.0-or-later.

## Compatible into a GPL extension

| License of the code you bring in | Into a GPL-2.0-or-later extension? | Note |
|---|---|---|
| GPL-2.0-or-later | yes | the same license |
| GPL-2.0-only | yes, but the whole then can be distributed only under GPL 2 | the "or later" of your other files cannot be used for the combination |
| GPL-3.0-or-later | yes, but the whole then is GPL 3 | your GPL-2.0-or-later files allow that |
| LGPL-2.1 or LGPL-3.0 | yes | the LGPL allows it |
| MIT, BSD-2-Clause, BSD-3-Clause | yes | keep their copyright notice and license text |
| Apache-2.0 | only with GPL 3 | Apache 2.0 is not compatible with GPL 2 only; with GPL-2.0-or-later the combination goes as GPL 3 |
| "All rights reserved", no license | no | you have no permission at all |
| Creative Commons non-commercial (NC) or no-derivatives (ND) | no | not free software licenses |

Two points people get wrong most often:

- **GPL-2.0-only and GPL-3.0 do not combine.** A library under "GPL 2 only" and another under "GPL 3" cannot go
  into the same distributed whole. "Or later" on your side does not fix that.
- **Permissive does not mean no conditions.** MIT and BSD code may go into a GPL extension, but its copyright line and
  license text must stay with it.

## Documentation and images

Code licenses are written for code. For other material:

| Material | Common licenses |
|---|---|
| Documentation | GNU FDL 1.3 (the license of this book), CC BY-SA 4.0 |
| Images, icons | CC BY 4.0, CC BY-SA 4.0, the icon set's own license (the kernel ships Bootstrap Icons under MIT) |
| Fonts | SIL Open Font License |

Keep each one's notice next to the file, or in a credits file that names each file.

## Recording what you combined

Write it down in three places:

1. **Next to the code.** A bundled library keeps its own `LICENSE` file in its folder.
2. **In `ezinfo.php`.** One `Includes` entry per piece of included software, with name, version, license and
   copyright. The administration's about page lists them under your extension
   ([Modernizing ezinfo.php and extension.xml](../part-4-upgrading/modernizing-ezinfo-and-extension-xml.md)).
3. **In `composer.json`.** The `license` field holds your extension's license as an SPDX identifier. If the whole is
   available under two licenses, an array: `"license": ["GPL-2.0-or-later", "MIT"]` means the user may choose either.

## A worked example

You write an extension under GPL-2.0-or-later and want to include a small MIT-licensed JavaScript date library and a
PHP class from another extension that is GPL-2.0-or-later:

- The MIT library: allowed. It goes into `design/standard/lib/datelib/` with its `LICENSE` file, and gets an
  `Includes` entry.
- The PHP class: allowed. Keep its file header with the original copyright, add your name below if you change it,
  and name the extension it came from in an `Includes` entry or your README's credits.
- Your `composer.json`: `"license": "GPL-2.0-or-later"`.

Now suppose the library were Apache-2.0: still possible, but the combined extension can then only be distributed
under GPL 3. Say so in your README, or pick another library.

## When in doubt

- [choosealicense.com](https://choosealicense.com) explains the common licenses in plain words.
- The Free Software Foundation keeps a list of licenses and their compatibility with the GPL at
  [gnu.org/licenses/license-list.html](https://www.gnu.org/licenses/license-list.html).
- For anything involving money or a dispute, ask a lawyer. This page is not legal advice.

## Related pages

- Previous: [Rebranding ethically](rebranding-ethically.md)
- Next: [Attribution and copyright notices](attribution-and-copyright-notices.md)
- [Reading licenses before you reuse anything](../part-1-from-user-to-developer/reading-licenses-before-reusing.md)
- [Contribute extensions back](../../contribute-extensions-back.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
