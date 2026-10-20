---
title: "Copyright notices in files"
audience: designer
category: theme-distribution
order: 300
summary: Where a theme states its copyright and license - LICENSE, README, metadata files and optional file headers - the notice the Free Software Foundation recommends for GPL code, and how to treat notices of others.
keywords: copyright notice, file header, license notice, GPL notice, attribution, headers, template comment
type: help
---
# Copyright notices in files

This page is for designers who want the copyright and license of their theme to be clear wherever a file ends up.

## The minimum

1. `LICENSE` with the full license text.
2. The README with a copyright line and the license name.
3. `ezinfo.php` and `extension.xml` with the same line ([Copyright and author lines](../extensions/copyright-and-author-lines.md)).

## File headers (optional, recommended for PHP)

For GPL code, the Free Software Foundation recommends a short notice at the top of each source file. For a PHP file
of a theme:

```php
<?php
/**
 * My Theme - template operators.
 *
 * @copyright Copyright (C) 2026 Your Name
 * @license GNU General Public License v2.0 or later
 */
```

The design extension wizard writes a license notice into the files it generates for the license you chose.

For templates, a comment at the top works and is not sent to the browser:

```
{* My Theme (C) 2026 Your Name, GNU General Public License v2.0 or later *}
```

Keep CSS and JavaScript headers short: a comment such as `/* My Theme (C) 2026 Your Name, GPL-2.0-or-later */`.

## Others' notices

- **Never remove** a copyright or license notice from a file you took from someone else.
- When you change such a file, add your line below theirs.
- When you combine files under different licenses, keep each file's notice.

## Step by step

1. Decide whether you use file headers; if yes, add them to every PHP file.
2. Add a one-line comment to `pagelayout.tpl` and the main stylesheet.
3. Search for notices you might have removed by accident: `git log -p -S'Copyright' --oneline | head`.

## Related pages

- [Credits and attribution](../lifecycle/credits-and-attribution.md)
- [The GPL for theme code](gpl-for-theme-code.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
