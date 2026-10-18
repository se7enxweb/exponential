---
title: "Copyright and author lines"
audience: designer
category: theme-extensions
order: 80
summary: How to write the copyright line of a theme in ezinfo.php, extension.xml, file headers and composer.json - years, holders, earlier authors of a ported or forked design - with real examples.
keywords: copyright, author, authors, holders, years, attribution, credit, ported design, fork, composer authors
type: help
---
# Copyright and author lines

This page is for designers who publish a theme, alone or with others. A copyright line says who holds the rights in
the work; an author line says who wrote it. Both must be true.

## Where they go

| File | Field | Example |
|---|---|---|
| `ezinfo.php` | `'Copyright'`, `'Author'` | `"2007 - 2026 CJW Network, JAC Systeme GmbH and 7x"`, `'7x'` |
| `extension.xml` | `<copyright>` | `Copyright (C) 2007 - 2026 CJW Network, JAC Systeme GmbH and 7x` |
| `composer.json` | `authors[]` with `name`, `homepage`, `email`, `role` | `{ "name": "7x", "homepage": "https://se7enx.com", "role": "Author" }` |
| `LICENSE` | the copyright notice above the license text | `Copyright (C) 2026 Your Name` |
| Source files (optional) | a header comment | `@copyright Copyright (C) 2026 Your Name` |

The examples are from `cjw_themes_jumper`, a port of an earlier CJW Network design: its copyright line keeps the
original holders and adds the one who ported it.

## Step by step

1. List every holder whose work is in the theme: you, your employer if the work was done for it, earlier authors of
   a design you ported or forked, contributors whose changes you merged (by their agreement, or simply under the
   license they contributed under).
2. Write the years: the first year of the work to the current year (`2019 - 2026`).
3. Put the same line into `ezinfo.php` and `extension.xml`.
4. Name the authors in `composer.json` `authors[]`. Use an organization's public contact address, never a private
   person's address without their consent.
5. Keep third-party notices (fonts, icon sets, libraries) in their own files next to those assets
   ([Third-party fonts, icons and libraries](../distribution/third-party-fonts-icons-and-libraries.md)).

## What not to do

- Do not remove earlier holders from a design you forked. The GPL requires that the notices stay
  ([Credits and attribution](../lifecycle/credits-and-attribution.md)).
- Do not name a company or a person as author who did not write the work.
- Do not put customer names into a public theme's files unless the customer agreed to be named.

## Related pages

- [The LICENSE file of a theme](license-file.md)
- [Copyright notices in files](../distribution/copyright-notices-in-files.md)
- [Rebranding a fork, fairly](../lifecycle/rebranding-a-fork.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
