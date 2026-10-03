# Package licenses and Semantic Versioning

Every package you create in the administration (and every extension the RAD
wizards write) now names its license from a strict list, and package versions
follow Semantic Versioning. You can no longer ship a package whose license is a
typo, and you can add your own license to the list in one settings block.

## Choose a license when you create a package

**Setup > Packages > Create new package**, on the *Package information* step of
the content object, content class, extension and site style wizards:

1. Open the **License** drop-down. Licenses are grouped (software,
   documentation, Creative Commons 4.0 to 1.0, public domain, proprietary).
2. Pick one. A link to the license's own page appears beside the choice.
3. Continue. The server accepts only a configured identifier (or an alias such
   as `GPL`) and refuses anything else with a message.

The identifier is stored in `package.xml` (`<licence>`). `package/view/full`
shows the license name as a link to its page, together with its identifier.
Packages that stored `GPL` show as `GPL-2.0-or-later`; any other stored text is
shown as it is. A package whose license is not `GPL-2.0-or-later` gets a
`LICENCE` document naming it.

### Licenses shipped in `settings/package.ini`

Default: `GPL-2.0-or-later`, the license of Exponential itself and what "GPL"
always meant.

| Group | Identifiers |
|---|---|
| Software licenses | `GPL-2.0-or-later`, `GPL-2.0-only`, `GPL-3.0-or-later`, `GPL-3.0-only`, `LGPL-2.1-or-later`, `LGPL-2.1-only`, `LGPL-3.0-or-later`, `LGPL-3.0-only`, `AGPL-3.0-or-later`, `AGPL-3.0-only`, `MIT` |
| Documentation licenses | `GFDL-1.3-or-later`, `GFDL-1.3-only` |
| Creative Commons 4.0 / 3.0 / 2.5 / 2.0 / 1.0 | `CC-BY`, `CC-BY-SA`, `CC-BY-ND`, `CC-BY-NC`, `CC-BY-NC-SA`, `CC-BY-NC-ND` in each version |
| Public domain | `CC0-1.0`, `LicenseRef-PDM-1.0` |
| Proprietary licenses | `LicenseRef-Proprietary` (all rights reserved; it has no page to link to) |

Identifiers are SPDX identifiers where one exists, otherwise an SPDX-style
`LicenseRef-<name>`.

### Settings

`settings/package.ini`

| Block | Key | Default | Meaning |
|---|---|---|---|
| `[LicenseSettings]` | `DefaultLicense` | `GPL-2.0-or-later` | Preselected choice. Must be in `LicenseList`. |
| `[LicenseSettings]` | `GroupList[]` | software, documentation, cc-4.0 ... cc-1.0, public-domain, proprietary | Drop-down groups in order. A license whose group is not listed appears under "Other licenses". |
| `[LicenseSettings]` | `LicenseList[]` | the identifiers above | Licenses that can be chosen, in order within their group. |
| `[LicenseSettings]` | `AliasList[<name>]` | `AliasList[GPL]=GPL-2.0-or-later` | Old names accepted and mapped to an identifier. |
| `[License_<identifier>]` | `Name`, `URL`, `Group`, `Description` | per license | Official name (not translated), page, group, optional text under the drop-down. |
| `[LicenseGroup_<group>]` | `Name` | per group | Heading of the group. |
| `[CreationSettings]` | `HandlerList[]` | `ezcontentobject`, `ezcontentclass`, extension, site style | Order of the wizards on the Create package page: content first, then its classes, then extensions, then site styles. |

Scope: the whole installation; extensions and `settings/override` can add to
or replace the list.

### Add your own license

`settings/override/package.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[LicenseSettings]
LicenseList[]=Apache-2.0

[License_Apache-2.0]
Name=Apache License 2.0
URL=https://www.apache.org/licenses/LICENSE-2.0
Group=software

*/ ?>
```

To replace the whole list, start with an empty `LicenseList[]` line and list
only the licenses you want, then set `DefaultLicense` to one of them. Clear the
INI cache afterwards.

## The RAD wizards use the same list

The datatype wizard and every extension wizard under Setup > RAD (content,
design, handler, kernel override, module, module extension, settings, template
and workflow event) offered three fixed licenses and silently replaced anything
else with the first. They now offer the same strict choice, grouped the same way,
with the configured default preselected, from one shared drop-down template.
Each wizard's `problems()` refuses a license that is not configured (identifiers
and package.ini aliases count, as do the short names the wizards used to write,
such as `GPL-2.0`, when the license they stand for is configured).

The generated `ezinfo.php`, `extension.xml` and `composer.json` carry the chosen
SPDX identifier; the file notices, the readme line and `LICENSE` name the chosen
license, using the Free Software Foundation's wording for the GNU licenses. The
generated text for `GPL-2.0-or-later` is unchanged. MIT and proprietary are
offered only when `package.ini` lists them.

## Versions: Semantic Versioning

- New packages start at `1.0.0`, not `1.0`.
- The creation wizards accept a strict Semantic Versioning 2.0.0 version:
  `MAJOR.MINOR.PATCH` with optional `-prerelease` and `+build`, at most 24
  characters so that `<version>-<release>` fits the `ezpackage` table. The old
  check allowed only one digit per part.
- `eZPackageVersion` reads, validates and compares versions in one place.
  Existing packages are read as they are and never rewritten:
  - `1.0` counts as `1.0.0`
  - four parts compare after the patch number
  - a letter suffix such as `3.4.0beta1` is a prerelease
  - in a `<version>-<release>` pair such as `1.0-1` a trailing number is the
    release, compared after the version
  - unreadable versions sort first
- The setup wizard's site package requirement and newer-version checks use it
  instead of `version_compare()`, which ordered `1.0-1` after `1.0.0-1`.

## Related pages

- [Package compare and import](package-compare-and-import.md)
- [RAD tools](rad-tools.md)
- [Extension metadata specification](../../specifications/6.0/extension-metadata.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
