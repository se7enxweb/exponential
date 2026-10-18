---
title: Reading licenses before you reuse anything
audience: user, developer, designer, app-creator
category: extension-development
order: 150
summary: The licenses you meet around Exponential, in plain words: GPL 2 or later, GPL 3, MIT, GNU FDL, all rights reserved and no license, and where to find them. Not legal advice.
keywords: license, licence, GPL, GPL-2.0-or-later, GPL-3.0, MIT, BSD, GFDL, all rights reserved, NOASSERTION, no license, copyright, reuse, compatibility
type: help
---
# Reading licenses before you reuse anything

Part 1, chapter 15. Every idea you find in Parts 1's other chapters comes with a license, and the license decides what
you may do with the code or text. This chapter explains the licenses you will meet, in plain words, and where to find
them in an extension. It is a practical guide, **not legal advice**; for a real decision about a commercial project,
ask someone qualified, and always read the license text itself.

## Ideas are free, code and text are not

You may always learn from what you read and build the same *idea* yourself in your own words and your own code. What
a license governs is **copying**: the code, the text, the images. The rest of this chapter is about copying.

## Where to look in an extension

| Place | What it tells you |
|---|---|
| `LICENSE`, `LICENSE.md`, `COPYING`, `doc/LICENSE` | The license text. This is what counts. |
| `COPYRIGHT.md`, `doc/COPYRIGHT` | Who holds the copyright, often several holders over the years |
| `ezinfo.php` | `'License'` and `'Copyright'` lines; older extensions credit their original authors here |
| `extension.xml` | `<license>` |
| `composer.json` | `"license"`, an SPDX identifier such as `GPL-2.0-or-later` |
| The header comment of each PHP file | `@copyright` and `@license` lines |
| The GitHub sidebar | The license GitHub *detects*; a guess from the license file, not a statement |

When these disagree, the license text and the file headers count over a summary line. GitHub's `NOASSERTION` means
GitHub could not recognize the license file, not that there is none: open the file and read it.

## The licenses you will meet

### GNU GPL version 2 or later (the kernel's license)

Exponential is free software under the GNU General Public License version 2 or, at your option, any later version
(`COPYRIGHT.md` and `LICENSE.md` of the kernel). Most extensions use the same license.

- You may use, study, change and share the code, for any purpose, also commercially.
- When you **distribute** the code or a changed version, you must do it under the same license, with its source, and
  keep the copyright and license notices.
- Running a site on your own server is not distributing the extension to your visitors; giving the extension to a
  customer, or publishing it, is.

Read it at [gnu.org/licenses/old-licenses/gpl-2.0.html](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html).

### GNU GPL version 2 only

Some files say "version 2" without "or later" (GitHub shows `GPL-2.0-only`, for example `enhancedselection2`). Code
under "2 only" cannot be combined into a work under GPL 3, because neither license allows the other's terms. When
you combine code from several extensions, check this detail.

### GNU GPL version 3 or later

For example `owsimpleoperator` (its README and `ezinfo.php` say version 3 or any later version). Code under GPL 3
can be combined with code under "GPL 2 *or later*" (the combination is distributed under GPL 3), but not with
"GPL 2 only".

### MIT and BSD style licenses

Short, permissive licenses: you may do nearly anything, including use in non-free software, as long as you keep the
copyright notice and the license text. Code under MIT may be included in a GPL extension; keep its notice in the
copied files. Some upstream packages that Exponential extensions were ported from are under MIT: Packagist lists
`netgen/layouts-core` and `netgen/query-translator` (the sources of `explayouts_core` and `expquery_translator`) as
MIT, while `netgen/site-bundle` and `netgen/ibexa-site-api` are listed as GPL-2.0-only. Part 5 shows how such ports
give credit
(Case study: porting a package from another project with credit).

### GNU Free Documentation License (for documentation)

This help, eZpedia and two of the free books use the GNU FDL. You may copy, adapt and translate the text, under the
same license, with credit. See [Free documentation](../../free-documentation.md).

### "All rights reserved"

A site or a file that says only "All rights reserved" gives you **no** permission to copy, beyond the short quotation
that copyright law itself allows. Link to it instead. The project forge's own pages are an example
([Exponential Projects](projects-exponential-earth.md)); the projects listed there have their own licenses.

### No license at all

Code without any license statement is not free to copy, even when it is public on GitHub. Ask the author to add a
license; until they do, take only the idea.

## Combining licenses: a quick table

| You want to put into a GPL 2 or later extension | Allowed? |
|---|---|
| Code under GPL 2 or later | Yes |
| Code under GPL 2 only | Yes, but the combination can then no longer move to GPL 3 |
| Code under GPL 3 or later | Only if the whole extension is then distributed under GPL 3 |
| Code under MIT or BSD | Yes, keep the notice |
| A forum post with no license | Only the idea, unless the author agrees |
| Text under the GNU FDL | Into documentation, yes, under the GNU FDL; not into the code |

For a deeper explanation of how licenses combine, see the FSF's
[license list](https://www.gnu.org/licenses/license-list.html) and
[choosealicense.com](https://choosealicense.com/licenses/).

## Your own extension's license

Choose one before your first commit and write it in every place listed above. Most extensions in this ecosystem use
"GNU General Public License v2.0 (or any later version)", which keeps your work combinable with the kernel and with
nearly every other extension. How to write it into `ezinfo.php` and `extension.xml` is in
ezinfo.php; the deeper story is
Licenses and how they combine.

## Checklist before you copy

- [ ] I found the license text, not only a summary.
- [ ] It allows copying and changing.
- [ ] It is compatible with the license of my extension.
- [ ] I keep every copyright line and the license notice in the files I copy.
- [ ] I say where the code came from (see Attribution and copyright notices).

## Related pages

- Previous: [Learning from the free books](learning-from-the-books.md)
- Next: [Linking versus copying: citing sources fairly](linking-versus-copying.md)
- Licenses and how they combine
- [The license of this documentation](../../../README-LICENSE.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
