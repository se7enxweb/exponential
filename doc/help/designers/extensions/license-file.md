---
title: "The LICENSE file of a theme"
audience: designer
category: theme-extensions
order: 90
summary: Add the LICENSE file a theme needs before anyone may use it - which text to copy, where it goes, how the license is named in ezinfo.php, extension.xml and composer.json, and what the design wizard writes for you.
keywords: LICENSE, license file, GPL-2.0-or-later, SPDX, license text, gnu.org, composer license, extension license
type: help
---
# The LICENSE file of a theme

This page is for designers who publish a theme. Without a license file, a published theme gives nobody the right to
use, change or share it, whatever the README says. The [Licensing choices](../distribution/licensing-choices-overview.md)
pages help you choose; this page is about putting the choice into files. It is not legal advice.

## What the published themes use

`sevenx_themes_simple`, `cjw_themes_jumper` and `sevenx_themes_media` are licensed **GPL-2.0-or-later**, like
Exponential itself (`"license": "GPL-2.0-or-later"` in the root `composer.json`). GitHub shows "GPL-2.0" for them.

## Step by step

1. Get the official text of the license you chose from its publisher. For the GPL version 2:
   `https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt`. Never retype or shorten it.
2. Save it as `LICENSE` at the root of the theme (some repositories also keep `LICENSE.md`; one file is enough).
3. Above it, or in the README, put your copyright notice ([Copyright and author lines](copyright-and-author-lines.md)).
4. Name the license in three more places, consistently:

   | File | Form | Example |
   |---|---|---|
   | `composer.json` `license` | SPDX identifier | `GPL-2.0-or-later` |
   | `ezinfo.php` `'License'` | Full name | `GNU General Public License v2.0 (or any later version)` |
   | `extension.xml` `<license>` | Full name | the same |

5. If parts of the theme are under other terms (a font, an icon set, photos), keep their license files next to them
   and list them in the README ([Third-party fonts, icons and libraries](../distribution/third-party-fonts-icons-and-libraries.md)).

## What the wizards write

- The [design extension wizard](design-extension-wizard.md) writes a `LICENSE` part on by default ("an extension with
  no licence file says nothing about how it may be used") for the license you pick. It offers only the licenses
  configured in `package.ini [LicenseSettings] LicenseList[]` and refuses any other.
- The [package creator wizard](package-creator-wizard-step-by-step.md) stores the license identifier in the
  package's `package.xml` ([Choosing a license in the package wizard](package-licenses-in-the-wizard.md)).

## Check

```bash
head -3 extension/mytheme/LICENSE
grep -n "'License'" extension/mytheme/ezinfo.php
grep -n '<license>' extension/mytheme/extension.xml
grep -n '"license"' extension/mytheme/composer.json
```

Expected: the license's title on the first lines, and the same license named in the other three files.

## Related pages

- [Licensing choices: an overview](../distribution/licensing-choices-overview.md)
- [The GPL for theme code](../distribution/gpl-for-theme-code.md)
- [Licensing explained for buyers](../../business/licensing-for-buyers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
