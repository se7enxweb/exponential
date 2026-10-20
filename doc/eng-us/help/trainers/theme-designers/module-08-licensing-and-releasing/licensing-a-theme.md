---
title: "Lesson 8.1: Licensing a theme"
audience: trainer, designer, developer
category: training
order: 81
summary: Learners make the four licensing decisions of a theme (code, documentation, own images, third-party assets), take an inventory of their own theme with the license of every part, and write it into LICENSE, the README and the metadata. 45 minutes.
keywords: theme design, license, licensing, GPL-2.0-or-later, GFDL 1.3, Creative Commons, MIT, third-party fonts, icons, libraries, LICENSE file, SPDX, package.ini LicenseSettings, not legal advice
type: help
---
# Lesson 8.1: Licensing a theme

A theme without a clear license cannot safely be used by anyone, and a theme that copies someone else's fonts or icons
without their license can get its users into trouble. This lesson gives learners the four decisions every theme needs
and lets them apply them to the files they made in this course.

> **Note** This lesson explains licensing choices as the documentation describes them. It is not legal advice; for a
> contract or a doubt about compatibility, ask a lawyer who knows software licensing.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 8: Licensing and releasing](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Designers and developers who publish or sell themes |
| Prerequisites | [Module 7](../module-07-packaging-a-theme/README.md) |

## Learning objectives

After this lesson, learners can:

- name the licenses Exponential itself uses for its software, its documentation and its themes;
- make the four licensing decisions for a theme;
- list every part of their theme with its author and license;
- name the license consistently in `LICENSE`, the README, `composer.json`, `ezinfo.php` and `extension.xml`.

## Trainer's notes

### How to present it

Source: [Licensing choices: an overview](../../../designers/distribution/licensing-choices-overview.md) and the pages it
links.

1. **What Exponential uses** (4 minutes). The software: GNU GPL version 2 or later (`GPL-2.0-or-later`). The
   documentation: GNU FDL 1.3 or later. The published 7x themes: `GPL-2.0-or-later`.
2. **The four decisions** (8 minutes).

   | Part | Common choice | Page |
   |---|---|---|
   | Templates, CSS, JavaScript, PHP | `GPL-2.0-or-later` | [The GPL for theme code](../../../designers/distribution/gpl-for-theme-code.md) |
   | The theme's documentation | GFDL 1.3 or a Creative Commons license | [The GFDL for theme documentation](../../../designers/distribution/gfdl-for-theme-documentation.md) |
   | Your own images and graphics | A Creative Commons license | [Creative Commons for images](../../../designers/distribution/creative-commons-for-images.md) |
   | Fonts, icons and libraries by others | Whatever their authors chose; you must comply | [Third-party fonts, icons and libraries](../../../designers/distribution/third-party-fonts-icons-and-libraries.md) |

3. **What the wizards offer** (3 minutes). The design extension wizard and the package wizard list the licenses of
   `package.ini [LicenseSettings]`: the GPL, LGPL and AGPL families, MIT, the GFDL, Creative Commons licenses and more.
4. **Where it is written** (5 minutes). `LICENSE` for the code; separate license files next to assets under other
   terms; every license in the README with what it covers; the code license in `composer.json` (SPDX), `ezinfo.php`
   and `extension.xml`.

### Common questions

**Can I sell a GPL theme?** Yes; the GPL allows selling, with conditions. See
[The GPL and selling](../../../designers/sales/gpl-and-selling.md) and
[Commercial terms next to the GPL](../../../designers/distribution/commercial-terms-and-gpl.md).

**The icons of admin4 are MIT. Does my theme ship them?** No: they are part of the installation
(`share/icons/bootstrap-icons/`, with their license beside them). Your theme ships only what is in its own folder.

**Can I use a font from a font service?** Only under its license, and with the privacy question of loading it from
another host; ship a copy when the license allows it.

### Pitfalls

- **Copying files from a shipped theme or icon set without checking their license.** The crystal icon themes, for
  example, carry their own `COPYING` file.
- **A license in `composer.json` that differs from `LICENSE`.** Check all five places.
- **Treating "free to download" as "free to reuse".** Read the license, not the download page.

## Hands-on exercise

On your training installation, in `extension/training_theme/`. Work alone, then compare with a neighbour.

1. List the theme's files: `find . -type f -not -path './.git/*' | sort`.
2. Make a table with the columns **File or folder**, **Author**, **License**, **Where the license is written**.
3. Fill a row for the templates, the stylesheets, the script, `ezinfo.php` and `extension.xml` (your own code).
4. Fill a row for the SVG flag you drew in lesson 6.2, and for the PNG icons of lesson 6.1. If you copied the PNGs
   from `share/icons/crystal-admin/`, read `share/icons/crystal-admin/COPYING` and write down what it says about
   reuse.
5. Fill a row for the README (documentation).
6. Decide each row's license using the four decisions. Where a part is under another license than the code, write the
   name of the separate license file you would add next to it.
7. Check the five places for the code license: `LICENSE`, README, `composer.json`, `ezinfo.php`, `extension.xml`.
   Correct any that differ.
8. Add a section "Licenses" to the README that lists each license and what it covers.

## Expected result

- Every file of the theme has an author and a license in the table; nothing is "unknown".
- Copied icons are either replaced by the learner's own, or listed with their license and its file.
- The five places name the same code license; the README lists every license.
- The trainer checks two learners' tables and the README section.

## Quiz

1. Under which license is the Exponential software published?
   A. MIT B. `GPL-2.0-or-later` C. GFDL 1.3 D. CC BY 4.0
2. Name the four licensing decisions of a theme.
3. Under which license is this documentation published?
4. Your theme uses a font by another designer. What decides whether you may ship it?
5. Name the five places that must state the theme's code license.

<details>
<summary>Answers</summary>

1. **B.** ([Licensing choices](../../../designers/distribution/licensing-choices-overview.md#what-exponential-uses))
2. The code, the documentation, your own images, and third-party fonts, icons and libraries.
3. The GNU Free Documentation License 1.3 or later.
4. The font's license, chosen by its author; you must comply with it.
5. `LICENSE`, the README, `composer.json`, `ezinfo.php` and `extension.xml`.

</details>

## Further reading

- [Licensing choices: an overview](../../../designers/distribution/licensing-choices-overview.md).
- [Assets under separate licenses](../../../designers/sales/assets-under-separate-licenses.md).
- [Copyright notices in files](../../../designers/distribution/copyright-notices-in-files.md) and
  [Copyright and author lines](../../../designers/extensions/copyright-and-author-lines.md).
- [Choosing a license in the package wizard](../../../designers/extensions/package-licenses-in-the-wizard.md).

## Related pages

- [Module 8: Licensing and releasing](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
