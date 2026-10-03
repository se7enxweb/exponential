# Changelog: exponential-platform-nexus-starter

Releases made by the se7enxweb team, newest first. Each release lists the team's changes since the previous tag, grouped as Added, Updated, Removed and Renamed. Upstream history carried by the fork is not repeated here; it is in the [repository page](../../history/ecosystem/exponential-platform-nexus-starter.md).

Related: [Nexus starter](../../features/6.0/platform-nexus-starter.md) · [package map](../../specifications/6.0/platform-package-map.md) · [upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

## 1.0.0.0 (2026-04-26)

Added: v5 Ibexa OSS on Symfony 7.4, PHP 8.4, SQLite dev db (`41883758e`)

Added: Importing from exponential-platform-nexus .x Default installation configured for instant use using sqlite as database. (`829e63b43`)

Updated: Update project name, license, and description in composer.json (`6546733e6`)

Updated: Upgraded from Symfony 7.3 (End of Life / Support) to Symfony 7.4. Upgrade. (`ee6e72d14`)

Updated: Upgraded repo node version from node 18 to node 22. Upgrade (`450c92332`)

Updated: resolve dependency installation failures on fresh project creation (`8e3aea2a7`)

Updated: correct sevenx-recipes Flex endpoint ref from flex/main to master (`7047d3f53`)

Updated: restore Netgen Layouts bundles/routes after recipe unconfigure; install se7enxweb/layouts-core (`982545fa8`)

Updated: add full 7x INSTALL guide and update README with DB conversion stub (`1aa134eaf`)

Updated: chore(deps): bump se7enxweb/exponential-platform-dxp-core v5.0.6 → v5.0.7 (`94c7a8fbf`)

Updated: Update project title and description in README (`40098be54`)

Removed: Remove section on 7x Forks & Upstream Incompatibility Fixes (`b69680701`)

Removed: Removed vendor from .gitignore (`4bb4dab3a`)

Removed: Removed var from being specifically referenced in .gitignore (`385b756c8`)
