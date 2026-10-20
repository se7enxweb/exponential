# About and Copyright pages

This page is for administrators who need to know which Exponential they run, what it is made of and under which
licenses, and for extension maintainers who want their extension to show up correctly. **Info > About** and
**Info > Copyright** in the admin answer those questions. Between 27 and 30 September 2026 they were rebuilt as proper
pages of the admin design and made trustworthy: every extension is listed with a description, license and website,
whatever its metadata looks like.

## Read the About page

Open **Info > About** (`/ezinfo/about`). You see:

- a summary with the version, the number of extensions and of third-party components, and links to the sections;
- **What is Exponential**, the license in a scrollable block, and the copyright notice;
- **Third-party software** as a table: software, version, license and the extension that includes it;
- **Extensions** as a table you can sort by loading order, name, identifier, version and license. Under each name:
  description, copyright, author and included software, with a link to its website.

The tables become cards when the content column is narrow, so neither page scrolls sideways on a phone or at 200
percent zoom. The styles are in `stylesheets/ezinfo.css`, loaded by these two pages only. Every text is translatable
(English, `eng-US`, and German are complete).

## Read the Copyright page

Open **Info > Copyright** (`/ezinfo/copyright`). It shows the copyright, license, warranty and original authors as
sections in the interface language, with the notice as distributed (English) below. It states Exponential's copyright
and the GNU General Public License version 2 (or any later version), the warranty disclaimer, and keeps the
attribution to the original developers that the license requires. It no longer offers a business use license
Exponential cannot grant.

Designs can present the notice themselves: the page renders through the `ezinfo/copyright` template, with the plain
notice as the fallback. The about and copyright views also hand designs table rows and the parts of the notice.

## Make your extension show up well

State `Name`, `Version`, `Copyright`, `License` and `Info_url` in `ezinfo.php`, and the same in `extension.xml`. The
release rule is that the number inside the extension equals the release number. See
[Extension metadata](../../specifications/6.0/extension-metadata.md).

The RAD extension wizard names the class of a generated `ezinfo.php` after the extension (`<extension>Info`), as the
kernel expects, so extensions with an underscore in their name show their details.

## How an extension's row is filled

`eZExtension::extensionInfo()` collects the facts in this order, and each source fills only the fields the earlier
ones did not give:

1. `<metadata>` of `extension.xml`, and `ezinfo.php`
2. the top level of `extension.xml` (summary, license, version, copyright), for an `extension.xml` without
   `<metadata>`
3. `composer.json`: description, license, version, support source or homepage, authors
4. the https address of the git origin in `.git/config` (read, never run)

An extension that declares no name, or only its directory name, is listed under the name its description starts with
(for example "Exponential Layouts - ..." gives "Exponential Layouts"), otherwise under its directory name as before. A
broken `ezinfo.php` no longer breaks the about page.

The kernel-shipped extensions `ezjscore`, `ezoe` and `ezformtoken` now state the Exponential version they ship with,
name their license in full ("GNU General Public License v2.0 (or any later version)") and link to their repositories.
About two dozen extensions released new versions in the same sweep, so that every row of the about page has a version,
license and website.

## Related pages

- [Extension metadata specification](../../specifications/6.0/extension-metadata.md)
- [Package compare and import](package-compare-and-import.md), [package licenses and Semantic Versioning](package-licenses-and-versions.md)
- [Translations and languages](translations-and-languages.md)
- [The product is called Exponential](rebranding-to-exponential.md), [a clean installation that fits shared hosting](clean-install-defaults.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- History: [16 to 30 September 2026](../../history/2026/2026-09b.md), [August 2025](../../history/2025/2025-08.md), [July 2025](../../history/2025/2025-07.md), [January 2025](../../history/2025/2025-01.md)
