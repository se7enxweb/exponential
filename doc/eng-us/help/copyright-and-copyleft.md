---
title: "Copyright and copyleft: who owns Exponential and what the GPL lets you do"
audience: editor, admin, developer, user, integrator, partner, government, press, trainer, community
category: support
order: 94
summary: Exponential's one copyright notice, "Exponential Copyright © 1998-2026 Exponential Foundation and 7x", and its file header form; the historical shift of 2026-10-29 from 7x alone to the Exponential Foundation and 7x together and why the Foundation is named first; what the GNU GPL version 2 or later means for users, integrators and extension authors; the support window; contributors and the CLA; the third-party notices that stay; and how to write the notice in new files and extensions, with examples.
keywords: copyright, copyleft, notice, Exponential Foundation, 7x, GPL, GNU General Public License, GPL-2.0-or-later, license, header, @copyright, ezinfo.php, extension.xml, CLA, contributors, third-party notices, eZ Systems, attribution, LICENSE, COPYRIGHT.md, 2026-10-29
type: help
---
# Copyright and copyleft: who owns Exponential and what the GPL lets you do

Exponential has **one copyright notice**. Wherever a person sees it (the footer of a page, the sign-in page, the about
page, the help, the console) it reads, word for word:

> **Exponential Copyright © 1998-2026 Exponential Foundation and 7x**

In the source files, in the metadata of extensions and in the license files, the same notice is written in the form
programmers have always used for file headers:

> **Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.**

The software is free software under the **GNU General Public License, version 2 or (at your option) any later
version** (GPL-2.0-or-later). The copyright says *who* owns the work; the license says *what everybody else may do*
with it. This page explains both, in plain words, and the rules every file, extension and page of the project follows.

This page is a summary, not legal advice. The license text in the file `LICENSE` and the notices in `COPYRIGHT.md`
are what count.

## Contents

1. [The notice in one minute](#the-notice-in-one-minute)
2. [A historical shift: 29 October 2026](#a-historical-shift-29-october-2026)
3. [Why the Exponential Foundation is named first](#why-the-exponential-foundation-is-named-first)
4. [The two forms and where each is used](#the-two-forms-and-where-each-is-used)
5. [What "copyleft" means](#what-copyleft-means)
6. [What the GPL means for you](#what-the-gpl-means-for-you)
7. [The support window and why the notice matters for it](#the-support-window-and-why-the-notice-matters-for-it)
8. [Contributors and the CLA](#contributors-and-the-cla)
9. [Notices that stay: eZ Systems AS and third-party software](#notices-that-stay-ez-systems-as-and-third-party-software)
10. [Writing the notice in new files](#writing-the-notice-in-new-files)
11. [Writing the notice in an extension](#writing-the-notice-in-an-extension)
12. [Showing the notice in a design or theme](#showing-the-notice-in-a-design-or-theme)
13. [Translations: the notice is never translated](#translations-the-notice-is-never-translated)
14. [How the project checks the notice](#how-the-project-checks-the-notice)
15. [The documentation's own license](#the-documentations-own-license)
16. [Questions people ask](#questions-people-ask)
17. [Where to read more](#where-to-read-more)

## The notice in one minute

| Question | Answer |
|---|---|
| Who holds the copyright of Exponential? | The **Exponential Foundation and 7x**, together. |
| Since when does the notice read this way? | Since **29 October 2026** (the 6.0.15 line). |
| Which years does it name? | **1998 to 2026**. The end year moves with the releases. |
| What does a user see? | "Exponential Copyright © 1998-2026 Exponential Foundation and 7x" |
| What does a file header say? | "Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved." |
| Under which license is the software? | GNU General Public License, version 2 or any later version (GPL-2.0-or-later). |
| Under which license is this documentation? | GNU Free Documentation License, version 1.3 or any later version. |
| Is the original developer still named? | Yes: "Copyright (C) 1999 - 2014 eZ Systems AS", the developer of eZ Publish, the discontinued product Exponential grew from. |
| Is the notice translated? | No. The words around it are; the notice is the same in every language. |
| Do I have to change my own extension? | Only if it says it is copyright of 7x or of the Foundation. Your own work carries your own notice. |

## A historical shift: 29 October 2026

On **29 October 2026** the copyright of Exponential moved from **7x alone** to the **Exponential Foundation and 7x
together**, and the project replaced every notice it had with one. It is a milestone in the history of the
software, and it is recorded here, in `COPYRIGHT.md` and in the
[6.0.15 changelog](../../changelogs/6.0/6.0.15.md#2026-10-29-one-copyright-notice-exponential-foundation-and-7x).

### How the notice read before

Over the years the notice of the project's own work had grown into several spellings, each from a different time
and a different file:

| Where | How it read before 2026-10-29 |
|---|---|
| Footer of the administration | "Exponential Copyright © 1998-2026 7x and others." |
| Sign-in page of the administration | "Exponential Copyright © 1998-2026 7x and others." |
| Standard design footer | "Copyright © 1998-2026 7x (except where otherwise noted). All rights reserved." |
| Setup wizard | "Exponential copyright © 1998-2026 7x & Exponential Foundation" |
| Fatal error page | "Copyright © 1998-2026 7x and others." |
| `./console --version` | "Copyright (C) 1998-2026 7x / Exponential Foundation." |
| /ezinfo/copyright | "Copyright (C) 1998-2026 7x / Exponential Foundation. All rights reserved." |
| `LICENSE` | "Copyright (C) 1998 - 2026 7x / Exponential Foundation. All rights reserved." |
| `COPYRIGHT.md` | "Copyright © 1998 - 2025, 7x / Exponential Foundation." and "Copyright (C) 1998 - 2026 7x & Exponential Foundation." |
| Most PHP file headers (about 2,850) | "@copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved." |
| Many newer PHP file headers (about 590) | "@copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved." |
| Other headers | "Copyright (C) 7x / Exponential Foundation.", "Copyright (C) 7x.", "Copyright (C) 1998 - 2026 7x.", "Copyright (C) 1998 - 2026 7x and others." |
| Translation catalogues | "Copyright © 1999-2014 7x AS and others." (a leftover of the 2014 rename of the original notice) |

Before the 2026-10-02 change "Every copyright notice names 1998 - 2026 7x & Exponential Foundation first", many files
named only eZ Systems AS. That change put the project's own line above the line of eZ Systems in some 3,000 files.
The change of 2026-10-29 finishes the work: one holder statement, one order, two forms, everywhere.

### What changed on 29 October 2026

- Every footer and page a user sees shows the visible notice (see [the list](#the-two-forms-and-where-each-is-used)).
- About 3,600 file headers use the header form: the line was rewritten by a script, and nothing else in the files
  changed.
- `LICENSE` and `COPYRIGHT.md` name the new holders. **The text of the GNU General Public License was not touched**:
  only the holder lines above it changed.
- The metadata of the extensions shipped in the kernel (`ezinfo.php`, `extension.xml`) and the generator of new
  extensions (`./console exp:ini:move`) write the header form.
- `composer.json` lists both holders as authors.
- Every third-party notice and the notice of eZ Systems AS stayed exactly as it was.
- A test (`CopyrightNoticeTest`) and a check script fail if a file names 7x or the Foundation in any other form.

The extensions that live in repositories of their own (the se7enxweb extensions and their `-ezpackage` twins,
Exponential Velocity, the installers and site packages) follow in their next releases, each with its version raised
in `ezinfo.php` and `extension.xml`.

## Why the Exponential Foundation is named first

The order of the names is deliberate. The **Exponential Foundation** is named first because it is the steward of the
project: the nonprofit that operates Exponential for its users and community, and that is meant to carry the
software's rights, its license and its history through the whole life of a release line, independent of the fate
of any one company. **7x** is named second and remains a full holder: the company that leads the development,
maintains the code, reviews and merges the contributions and supports the customers.

Naming the Foundation first says, in the place every user and every lawyer looks first, that Exponential is not the
product of a single vendor. That is the lesson of the software's own history: when the original developer stopped
the free product, users who had relied on it for years had to find a new steward. A notice that puts a nonprofit
first, with the company that does the work next to it, is the project's answer to that history.

The order is the same in every form and every language: "Exponential Foundation and 7x", never "7x and the
Exponential Foundation" and never "7x & Exponential Foundation".

## The two forms and where each is used

### The visible notice

> Exponential Copyright © 1998-2026 Exponential Foundation and 7x

Used wherever a person reads it on a screen or a page:

| Place | File |
|---|---|
| Footer of the administration (admin, admin3, admin4, admin4l, editor) | `design/admin/templates/page_copyright.tpl`, `design/admin4/templates/page_copyright.tpl` |
| Sign-in page of the administration | `design/admin/templates/page_login_copyright.tpl`, `design/admin4/templates/page_login_copyright.tpl` |
| Footer of the standard design (plain, print, popup, full screen, login and OAuth layouts) | `design/standard/templates/page_copyright.tpl` |
| Footer of the base design and its sign-in page | `design/base/templates/pagelayout.tpl`, `design/base/templates/loginpagelayout.tpl` |
| Setup wizard | `design/standard/templates/setup_pagelayout.tpl`, `design/standard/templates/setup/page_copyright.tpl` |
| Page of an undefined module | `design/standard/templates/undefinedmodule.tpl` |
| Fatal error page | `lib/ezutils/classes/ezexecution.php` |
| The copyright page (/ezinfo/copyright) | `kernel/private/classes/views/ezinfo/copyright.php`, `design/*/templates/ezinfo/copyright.tpl` |
| The help: the license box of each page, the PDF of a page | `settings/exphelp.ini` `[License]`, `kernel/classes/help/exphelplicense.php` |
| `./console --version` | `bin/php/console` |
| The system card | `share/syscard/about.txt` |

In HTML the sign is written `&copy;`. Links may sit on the words (the admin footer links "Exponential", "Exponential
Foundation" and "7x"), but the text a reader sees is always exactly the notice.

### The header form

> Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.

Used in files and metadata: the `@copyright` line of PHP docblocks, the `// COPYRIGHT NOTICE:` line of JavaScript
and C++ sources, comment headers of templates, style sheets and scripts, `<copyright>` of `extension.xml`,
`'Copyright'` of `ezinfo.php`, `LICENSE` and `COPYRIGHT.md`. "(C)" stands for the copyright sign in plain ASCII, which
every file can hold. "All rights reserved" is the traditional closing of a header notice; it does not take away any
freedom the GPL gives you (see [Questions people ask](#questions-people-ask)).

## What "copyleft" means

**Copyleft** is a way of using copyright to keep software free. The holders use their copyright to attach a license
that says: anyone may use, study, change and share this work, **on the condition that** everyone who passes it on,
changed or not, passes on the same freedoms with it. The GNU General Public License is the best-known copyleft
license.

The copyright notice and the copyleft belong together. Without a holder, there is nobody who can grant the license
or enforce it; without the license, the holder's copyright would forbid copying. The notice says who can enforce the
GPL on behalf of everyone who relies on it. That is why the project keeps it precise.

## What the GPL means for you

Exponential's license is the **GNU General Public License, version 2 or any later version**. In short:

### If you use Exponential (site owners, editors, administrators)

- You may **run** it for any purpose, commercial or not, on as many servers and sites as you like. No fee, no
  registration, no license key.
- You may **change** it for your own use. Changes you only run on your own servers, without giving copies to others,
  create no duty to publish them.
- The notices stay in place. You may not remove the copyright notice or the license from the files, and a footer
  you change in an override template should keep the notice visible when it shows one.

### If you build sites for others (integrators, agencies, hosting companies)

- You may **deliver** Exponential to customers, installed or as files, and charge for your work, your support and
  your hosting.
- When you give a customer a copy (the files of the installation, a repository, an archive), the GPL travels with
  it: keep `LICENSE`, `COPYRIGHT.md` and every notice, and give the customer the source code of what you deliver,
  including your changes to the kernel.
- Hosting a site for a customer on your servers, without handing over the files, is not distribution under
  GPL version 2.
- See [GPL obligations when distributing](bundlers/gpl-obligations-when-distributing.md) and
  [License texts and notices to ship](bundlers/license-texts-and-notices-to-ship.md).

### If you write extensions (extension authors)

- **Your extension is your work.** Its copyright belongs to you (or your employer), and its notice names you, not 7x
  and not the Foundation.
- An extension that runs inside Exponential and uses its classes and templates is generally treated as a work based
  on Exponential, so when you **distribute** it, distribute it under the GPL version 2 or later, or a license
  compatible with it. Extensions you keep for your own sites need no license at all.
- If your extension contains code copied from Exponential, keep the notice of that code
  ("Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.") next to yours. See
  [Attribution and copyright notices](extension-development/part-5-extend-fork-contribute/attribution-and-copyright-notices.md).

### If you fork or redistribute Exponential

- You may fork it, rename your fork and publish it. Keep every notice, add your own for your changes, and keep the
  license GPL-2.0-or-later.
- You may not present your fork as the Exponential project or use the notice to suggest that the Foundation or 7x
  stand behind it.

## The support window and why the notice matters for it

Exponential 6.x is meant to be used for a long time: the project plans releases and fixes for this line over a support
window of **twenty years and more**, in line with a code base whose history goes back to 1998 and that its users
have relied on for more than two decades. A release made in 2026 must still be clear about its rights in 2046.

A clear, single notice protects that window:

- **Enforcement.** Copyleft only works if the holders can be named and can act. One notice, with two holders that
  can each act, leaves no doubt who can defend the GPL for the users.
- **Continuity.** With the Foundation as a holder, the rights do not depend on the life of one company.
- **Due diligence.** Buyers, public bodies and legal departments read the notice first. One form, everywhere, with the
  same years and names, answers their question in seconds.
- **No drift.** The test that checks every notice stops new files from reintroducing old spellings.

## Contributors and the CLA

Exponential is written by many people. Their contributions reach the kernel in two ways:

- **Code contributions to the kernel** (the repository se7enxweb/exponential) are merged after the author has signed
  the **7x Contributor Licensing Agreement (CLA)**, version 1.0.1. As the agreement reads today, it assigns the copyright
  in the contribution to 7x. 7x holds those rights as one of the two holders named in the notice. The text of the
  CLA did not change on 2026-10-29; read it before you sign: [The CLA explained](community/contributing/the-cla-explained.md),
  `CONTRIBUTING.md`.
- **Documentation contributions** are accepted under the GNU Free Documentation License 1.3 or later (see
  [The license of this documentation](../README-LICENSE.md)).

Contributors are credited in the git history, which is the record of who wrote what, and on the about page
(/ezinfo/about). A contributor's name in the history is not a copyright notice and is never removed.

Your own extensions, themes and site packages that you keep in your own repositories are **not** contributions and
need no CLA. They carry your notice.

## Notices that stay: eZ Systems AS and third-party software

The GPL requires every copyright notice that came with the code to stay. The project never changes, removes or
reorders them. The ones kept on 2026-10-29:

| Notice | Where | Why it stays |
|---|---|---|
| "Copyright (C) 1999 - 2014 eZ Systems AS. All rights reserved." and "@copyright Copyright (C) eZ Systems AS. All rights reserved." | `LICENSE`, `COPYRIGHT.md`, about 1,390 PHP files, the `// COPYRIGHT NOTICE` lines of 39 JavaScript files, the /ezinfo/copyright page | eZ Systems AS developed eZ Publish, the discontinued product Exponential grew from. Their notice stays in every file that contains their code, below the project's own line. |
| "Copyright (C) 1999 - 2026 7x / Brookins Consulting / eZ Systems AS" | `lib/eztemplate/classes/eztemplateroleoperator.php` | A joint notice of Brookins Consulting (7x's name before 2022) and eZ Systems AS for the original operator; the approved line is directly above it. |
| "Copyright CJW Network and Exponential Foundation. All rights reserved." | about 40 files (the image editor `expimageeditor`, the package archive classes, a number of tests) | The notice of CJW Network, a co-author of these files; another author's notice is kept as it is. |
| "Copyright (C) Mugo Web, GPL v2" | templates and classes with code taken from LoveStack | Code derived from Mugo Web's work keeps its notice and names the commit it came from. |
| "Copyright (C) 1989, 1991 Free Software Foundation, Inc." | `LICENSE`, `LICENSE.md`, the `LICENSE` files of ezjscore, ezoe and ezformtoken | Part of the license text, which must never change. |
| "Copyright ..., Moxiecode Systems AB" and "Ephox Corporation DBA Tiny Technologies, Inc." | TinyMCE in `extension/ezoe` | The editor component bundled with the online editor. |
| "Copyright (c) Unicode, Inc., Unicode License v3" | `share/locale` (CLDR data) | Locale data from the Unicode CLDR. |
| "Copyright (C) 2009 Nokia Corporation and/or its subsidiary(-ies)", "Trolltech AS" | `support/ezlupdate-qt4.5` | The Qt Linguist sources of the translation updater. |
| "Copyright (C) 2002 and following years KDE Artists" | `share/icons/crystal*` | The Crystal icon set. |
| "Copyright (C) 1999-2010 Tango_Icon_Library" | `extension/ezoe` | Icons of the online editor. |
| The book notices ("Copyright (c) ... 7x and Graham ...") | documentation about the books | The books are separate works with their own copyright. |

The complete list of these exceptions, with the reason for each, is the file
`tests/tests/bin/copyright-notice-allowlist.json`. Notices that name only other holders need no entry there: the test
recognizes them as third-party notices and leaves them alone.

## Writing the notice in new files

Every new file of the kernel starts with the header form, in the comment style of its language. Keep the layout of
the surrounding files: the `@copyright` line, then the `@license` line.

### PHP

```php
<?php
/**
 * File containing the expExampleThing class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */
```

A file that also contains code of eZ Systems AS keeps their line directly below:

```php
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
```

### JavaScript

```js
/**
 * Exponential: the bookmark button of the page header.
 *
 * Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * GNU General Public License v2.0 (or any later version).
 */
```

Older JavaScript and C++ files use the `// COPYRIGHT NOTICE:` style; keep it in those files:

```js
// COPYRIGHT NOTICE: Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
// COPYRIGHT NOTICE: Copyright (C) 1999-2014 eZ Systems AS
```

### Templates (.tpl)

```
{* The footer of the example page.
   Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
   GNU General Public License v2.0 (or any later version) *}
```

### INI files, shell and Python scripts

```ini
# Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
# GNU General Public License v2.0 (or any later version)
```

### Style sheets

```css
/* Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
   GNU General Public License v2.0 (or any later version) */
```

Rules for all of them:

- Write the notice **exactly**: "Copyright (C) 1998 - 2026", a space on each side of the dash, "Exponential
  Foundation and 7x", a full stop, "All rights reserved." with its full stop.
- Never "7x & Exponential Foundation", "7x and the Exponential Foundation", "7x / Exponential Foundation", "7x and
  others" or "7x" alone.
- Never remove or reword another holder's line. Add yours above it.
- Do not change the years of an existing line to make it "fit"; the project changes the end year for all files at
  once, at the turn of the year.

## Writing the notice in an extension

### An extension of the project (shipped in the kernel or released by se7enxweb)

`ezinfo.php`:

```php
<?php
/**
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 */
class exampleextInfo
{
    static function info()
    {
        return array( 'Name' => "Example extension",
                      'Version' => "1.0.0",
                      'Copyright' => "Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.",
                      'License' => "GNU General Public License v2.0 (or any later version)",
                      'Info_url' => "https://github.com/se7enxweb/exampleext" );
    }
}
```

`extension.xml`:

```xml
<metadata>
    <name>Example extension</name>
    <version>1.0.0</version>
    <copyright>Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.</copyright>
    <license>GNU General Public License v2.0 (or any later version)</license>
    <info_url>https://github.com/se7enxweb/exampleext</info_url>
</metadata>
```

An extension that contains code of eZ Systems AS keeps both notices in one value, the project's first:

```xml
<copyright>Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved. Copyright (C) eZ Systems AS. All rights reserved.</copyright>
```

`./console exp:ini:move` writes exactly these files when it creates a settings extension.

### Your own extension

Your extension carries **your** notice, for example:

```php
 * @copyright Copyright (C) 2026 Example Agency GmbH. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
```

Only if you copied code from Exponential into it, add the project's line for that code. Do not put the project's
notice on code you wrote: it would give your copyright away on paper.

## Showing the notice in a design or theme

A front-end theme does not have to show the notice. Many sites show their own footer instead, which is their right.
If your design shows the software's notice, show it exactly:

```
<a href="https://exponential.earth">Exponential</a> Copyright &copy; 1998-2026 <a href="https://foundation.exponential.earth">Exponential Foundation</a> and <a href="https://se7enx.com">7x</a>
```

An override of `page_copyright.tpl` in your design may move the notice, style it or wrap it in links, but should not
change its words.

## Translations: the notice is never translated

The notice is a legal statement and a name, so it reads the same in every language. In the catalogues
(`share/translations/<locale>/translation.ts`) the footer message's translation is the notice itself, in all
156 catalogues; `translation-identical-allowlist.json` lists it as allowed to stay identical. Translators translate the
words around it ("For more information see ezinfo/about", "Powered by ...", "Help center"), never the notice. A
translation that changes the notice is a bug.

The German documentation explains the notice in German, but quotes it in its original form:
"Exponential Copyright © 1998-2026 Exponential Foundation and 7x".

## How the project checks the notice

- **`tests/tests/bin/CopyrightNoticeTest.php`** (PHPUnit, testsuite `quality`, no database) reads every file of the
  repository and fails on any line that names 7x or the Exponential Foundation as a copyright holder in a form other
  than the two approved ones, unless the line is listed in `tests/tests/bin/copyright-notice-allowlist.json` with its
  reason.
- **`tests/tests/design/CopyrightFooterNoticeTest.php`** checks the footers, the sign-in pages, the setup wizard, the
  error page, the console, the copyright page and the help, and that no catalogue translates the notice.
- Both run with the continuous integration on every push; the change-based test runner maps changes of any file to
  the first test.

A line counts as a notice when it has the word "copyright" and a notice marker: "(C)", "©" (or `&copy;`), a year,
`@copyright`, `<copyright>`, `'Copyright' =>`, or `Copyright=` / `Copyright:`. Prose such as "the Foundation and 7x
hold the copyright" is not a notice and is not checked.

## The documentation's own license

This documentation is free documentation under the GNU Free Documentation License, version 1.3 or any later version.
Its notice names the same holders; see [The license of this documentation](../README-LICENSE.md) and
[Free documentation](free-documentation.md). The pages inherited from the original software keep the notice of
eZ Systems AS and their own terms.

## Questions people ask

**Does "All rights reserved" contradict the GPL?**
No. It is the traditional ending of a copyright notice and means the holders keep their copyright. The GPL is the
permission the holders give on top of it; you keep every freedom the license grants.

**Why 1998?**
1998 is the first year of the notice of the project's own work, as it has read since the 6.x line began. eZ Systems
AS's work is dated separately, 1999 to 2014.

**Does the order of the names mean the Foundation owns more?**
No share is implied by the order. Both are holders. The Foundation is named first for the reasons
[above](#why-the-exponential-foundation-is-named-first).

**Did the license change on 29 October 2026?**
No. Only the holder notice changed. The license is still the GNU General Public License, version 2 or any later
version, and its text is unchanged.

**I quoted the old notice in my own documentation. Do I have to change it?**
Your text is yours. We recommend quoting the current notice in anything new.

**My extension's `ezinfo.php` says "Copyright (C) 1998 - 2026 7x & Exponential Foundation". Is that wrong now?**
If the extension is one of the project's, its next release fixes it. If it is your own extension that copied the
project's metadata as a template, replace the notice with your own.

**Can I remove the notice from the footer of my site?**
The GPL is about the source code: keep every notice in the files. A footer is part of your site's design, and a
customized front-end footer does not have to show the software's notice. The administration's footer belongs to the
software; leave it as it is.

## Where to read more

- `LICENSE` and `LICENSE.md`: the license text, with the holder notice above it.
- `COPYRIGHT.md`: the copyright notice of the project, with this history.
- [The license of this documentation](../README-LICENSE.md)
- [Free documentation: use it to help yourself and others](free-documentation.md)
- [Attribution and copyright notices](extension-development/part-5-extend-fork-contribute/attribution-and-copyright-notices.md)
- [Copyright and author lines of an extension](designers/extensions/copyright-and-author-lines.md)
- [Copyright notices in files](designers/distribution/copyright-notices-in-files.md)
- [The CLA explained](community/contributing/the-cla-explained.md)
- [GPL obligations when distributing](bundlers/gpl-obligations-when-distributing.md)
- [The Exponential Foundation and 7x](government/sovereignty/the-exponential-foundation-and-7x.md)
- The [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#2026-10-29-one-copyright-notice-exponential-foundation-and-7x)
