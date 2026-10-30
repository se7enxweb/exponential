---
title: Attribution and copyright notices
audience: developer, designer, app-creator
category: extension-development
order: 70
summary: Where credit goes in an extension - file headers, license files, ezinfo.php Includes, composer.json authors, README credits, Git history - and how to add your name without removing anyone else's.
keywords: attribution, copyright, notice, credit, file header, LICENSE, COPYRIGHT, Includes, authors, README, Git history, original author
type: help
---
# Attribution and copyright notices

Part 5, chapter 7. Credit is not a courtesy you may skip: the licenses require the notices to stay, and the people
who wrote the code deserve to be named. This chapter lists every place credit lives in an extension and shows how to
add your own without removing anyone else's.

## The places credit lives

| Place | What goes there | Required? |
|---|---|---|
| File headers | the copyright line(s) and the license notice of that file | keeping existing ones: yes |
| `LICENSE` (or `LICENSE.md`) | the full text of the extension's license | yes, for distribution |
| `COPYRIGHT.md` or a credits file | who holds copyright in what | recommended |
| Bundled libraries' own `LICENSE` files | the library's license text, next to it | yes |
| `ezinfo.php` and `extension.xml` | `Copyright`, `License`, and `Includes` entries for included software | the convention of this project |
| `composer.json` | `authors`, `license` | strongly recommended |
| `README.md` | a "Credits" section: who made what, with links | recommended |
| Git history | every commit with its author | keep it: fork, do not copy |

## File headers

A typical GPL header in an old extension:

```php
<?php
//
// Copyright (C) 2009 Original Author. All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
```

When you change the file substantially, add your line below theirs. Do not replace it, do not "update" their year:

```php
<?php
//
// Copyright (C) 2009 Original Author. All rights reserved.
// Copyright (C) 2026 Your Name: PHP 8 port and Content Security Policy changes.
//
// This program is free software; ...
```

The second line also serves the GPL 2 requirement to mark changed files "stating that you changed the files and the
date". A note in the changelog is the other half.

A new file you write from scratch carries only your line (and the extension's license notice).

## ezinfo.php: Includes

The administration's about page shows, under each extension, the software it says it includes. The kernel reads any
`ezinfo.php` key that starts with `Includes`, as one entry or a list. se7enxweb/expchangeclass names the extension it
was forked from:

```php
'Includes' => array(
    array( 'Name' => "eZChangeclass",
           'Copyright' => "Copyright (C) 2007 (the original author)",
           'License' => "GNU General Public License v2.0" ) )
```

(In the real file the author's name is written out; it is shortened here.) Each entry may also carry `Version` and
`Info_url`.

## composer.json: authors

```json
"authors": [
    { "name": "Your Name", "homepage": "https://example.org", "role": "Maintainer" },
    { "name": "Original Author", "role": "Original author" }
]
```

expchangeclass lists its maintainer and, second, the original author "with the role Original author of
eZChangeclass". Packagist shows the list on the package's page.

## README: credits

A short section, as se7enxweb/sevenx_dse has: a table of each part, who made it, and a link. sevenx_dse's credits name
the embedded database tool, the project it was forked from, and the extension's own integration work, each with its
maker, followed by a sentence of thanks. The thanks is not required; it is the right tone.

## Removing a notice is never a cleanup

You will be tempted, during a cleanup, to remove "old" headers, author tags (`@author`), or a copyright line that
names a company that no longer exists. Do not. A notice that is no longer accurate about the present is still
accurate about the past, and the license requires it. Add, never subtract.

The same goes for `@author` tags in docblocks: keep them on code that person wrote.

## Your work in someone else's project

When you contribute to another project by pull request, your credit is your commit: your name and email in the Git
history. Follow the project's own rules for file headers; most projects do not want contributors to add copyright
lines to their files, and that is their decision. Never put credit for yourself into a project's interface texts.

## Checklist

- [ ] Every existing copyright line and license notice is still there.
- [ ] Changed files carry a line saying who changed them and when.
- [ ] Each bundled library has its own license file next to it.
- [ ] `ezinfo.php` has an `Includes` entry per included piece of software.
- [ ] `composer.json` `authors` names the original authors.
- [ ] The README has a credits section.

## Related pages

- Previous: [Licenses and how they combine](licenses-and-compatibility.md)
- Next: [Contributing back with pull requests](contributing-back-with-pull-requests.md)
- [Rebranding ethically](rebranding-ethically.md)
- [Modernizing ezinfo.php and extension.xml](../part-4-upgrading/modernizing-ezinfo-and-extension-xml.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

## Copyright and copyleft

Since 29 October 2026 the copyright of Exponential is held by the Exponential Foundation and 7x together, the
Foundation named first: "Exponential Copyright © 1998-2026 Exponential Foundation and 7x", in files
"Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved." Who holds it and why, what the
GPL lets you do, the third-party notices that stay and how to write the notice in new files and extensions:
[Copyright and copyleft](../../copyright-and-copyleft.md).
