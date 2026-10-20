---
title: "Choosing a license in the package wizard"
audience: designer, admin
category: theme-extensions
order: 390
summary: The license drop-down of the package and RAD wizards - the configured list in package.ini [LicenseSettings] (GPL, LGPL, AGPL, MIT, GFDL, Creative Commons, public domain, proprietary), the default, how to add one, and how to choose for a theme.
keywords: license, licence, package.ini, LicenseSettings, LicenseList, DefaultLicense, SPDX, GPL-2.0-or-later, CC-BY-SA-4.0, LicenseRef-Proprietary, AliasList
type: help
---
# Choosing a license in the package wizard

This page is for designers filling the **Package information** step of the package wizard, or the **License** field
of the RAD wizards. Both offer a strict choice from a configured list and refuse any other value. This page is not
legal advice.

## The configured list

`settings/package.ini [LicenseSettings]` sets:

- `DefaultLicense=GPL-2.0-or-later`, chosen when the step opens;
- `LicenseList[]`, grouped in the drop-down:

| Group | Identifiers |
|---|---|
| Software | `GPL-2.0-or-later`, `GPL-2.0-only`, `GPL-3.0-or-later`, `GPL-3.0-only`, `LGPL-2.1-or-later`, `LGPL-2.1-only`, `LGPL-3.0-or-later`, `LGPL-3.0-only`, `AGPL-3.0-or-later`, `AGPL-3.0-only`, `MIT` |
| Documentation | `GFDL-1.3-or-later`, `GFDL-1.3-only` |
| Creative Commons | `CC-BY`, `CC-BY-SA`, `CC-BY-ND`, `CC-BY-NC`, `CC-BY-NC-SA`, `CC-BY-NC-ND` in versions 4.0, 3.0, 2.5, 2.0 and 1.0 |
| Public domain | `CC0-1.0`, `LicenseRef-PDM-1.0` |
| Proprietary | `LicenseRef-Proprietary` |

The value stored in `package.xml` is the identifier: the SPDX identifier where one exists, otherwise a
`LicenseRef-<name>`. `AliasList[GPL]=GPL-2.0-or-later` lets an old package that says `GPL` be read correctly.

## How to choose for a theme

| What the package holds | A common choice | Why |
|---|---|---|
| Templates, CSS, JavaScript, PHP | `GPL-2.0-or-later` | The license of Exponential and of the published themes |
| Documentation only | `GFDL-1.3-or-later` | The license of the Exponential documentation |
| Images and graphics only | a Creative Commons license | Made for works that are not software |
| Code you will not publish | `LicenseRef-Proprietary` | See [Commercial terms next to the GPL](../distribution/commercial-terms-and-gpl.md) first |

Templates and PHP that run inside Exponential are usually treated as GPL work; read
[The GPL for theme code](../distribution/gpl-for-theme-code.md) before choosing anything else for code.

## Adding a license to the list

An administrator adds one in `settings/override/package.ini.append.php`:

```ini
[LicenseSettings]
LicenseList[]=Apache-2.0

[License_Apache-2.0]
Name=Apache License 2.0
URL=https://www.apache.org/licenses/LICENSE-2.0
Group=software
```

Then clear the INI cache. The new entry appears in the drop-down of every wizard.

## Related pages

- [Licensing choices: an overview](../distribution/licensing-choices-overview.md)
- [The LICENSE file of a theme](license-file.md)
- [Package licenses and versions](../../../features/6.0/package-licenses-and-versions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
