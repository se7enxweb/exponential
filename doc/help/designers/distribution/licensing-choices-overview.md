---
title: "Licensing choices: an overview"
audience: designer, sales-marketing
category: theme-distribution
order: 240
summary: The license decisions a theme author makes - code, documentation, images and third-party parts - the licenses Exponential and its published themes use, and where each choice is explained. Not legal advice.
keywords: license, licensing, GPL, GFDL, Creative Commons, MIT, proprietary, theme license, third-party, compatibility
type: help
---
# Licensing choices: an overview

This page is for designers deciding under which terms others may use their theme. It describes the options and the
choices the Exponential project made. **It is not legal advice**: for a contract, a commercial license or a doubt
about compatibility, ask a lawyer who knows software licensing.

## What Exponential uses

| Part | License | Where it says so |
|---|---|---|
| The software | GNU General Public License, version 2 or later (`GPL-2.0-or-later`) | the root `composer.json` |
| The documentation (`doc/`) | GNU Free Documentation License 1.3 or later | [The license of this documentation](../../../README-LICENSE.md) |
| The 7x themes (`sevenx_themes_simple`, `sevenx_themes_media`, `cjw_themes_jumper`) | `GPL-2.0-or-later` | each theme's `composer.json` and `LICENSE` |

## The four decisions

| Decision | Common choice | Page |
|---|---|---|
| Templates, CSS, JavaScript, PHP of the theme | `GPL-2.0-or-later` | [The GPL for theme code](gpl-for-theme-code.md) |
| The theme's documentation | GFDL 1.3 or a Creative Commons license | [The GFDL for theme documentation](gfdl-for-theme-documentation.md) |
| Your own images and graphics | A Creative Commons license | [Creative Commons for images](creative-commons-for-images.md) |
| Fonts, icons and libraries by others | Whatever their authors chose; you must comply | [Third-party fonts, icons and libraries](third-party-fonts-icons-and-libraries.md) |

And if you want to earn money: [Commercial terms next to the GPL](commercial-terms-and-gpl.md) and
[The GPL and selling](../sales/gpl-and-selling.md).

## The licenses the wizards offer

The package and RAD wizards offer the licenses configured in `package.ini [LicenseSettings]`: the GPL, LGPL and AGPL
families, MIT, the GFDL, Creative Commons 1.0 to 4.0, CC0, the Public Domain Mark, and `LicenseRef-Proprietary`
([Choosing a license in the package wizard](../extensions/package-licenses-in-the-wizard.md)).

## Step by step

1. Decide each of the four rows above.
2. Put the license texts into the theme: `LICENSE` for the code, separate files next to assets under other terms
   ([The LICENSE file of a theme](../extensions/license-file.md)).
3. List every license in the README, with what it covers.
4. Name the code license in `composer.json`, `ezinfo.php` and `extension.xml`.

## Related pages

- [Licensing explained for buyers](../../business/licensing-for-buyers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
