# ez-support-tools: system information for administrators

`ez-support-tools` is the platform bundle that shows **information about the system** the platform runs on, intended to help administrators and support
engineers. It is a Symfony bundle (the System Info screens), not a legacy extension, kept in the se7enxweb fork so it installs next to the other
`se7enxweb/ezplatform-*` packages.

## What changed in the fork

* 2.3.13 (1 July 2025): the `require` section names the 7x vendor packages.
* 2.3.14 (28 September 2025): package descriptions and the supported PHP versions updated; requires `se7enxweb/ezplatform-core`.
* 11 April 2026: the PHP requirement allows PHP 8.5; a guard for a **null version** from `InstalledVersions::getVersion()`. A package installed from a path or
  VCS repository, or with no recorded version, returned null, and passing it to `getStability(string $version)` was a fatal `TypeError` at boot that broke every
  console command; the code now falls back to `getMinimumStability()`.
* Earlier in the ledger window (2024): the license information and copyright year of the upstream bundle, and the hardcoded end-of-maintenance dates for
  version 3.3.

The platform side of the project is described in the [ecosystem overview](../../../history/ecosystem.md).

## Related

* [Chronicle of the repository](../../../history/ecosystem/ez-support-tools.md) and [release notes](../../../changelogs/extensions/ez-support-tools.md) (covered with the platform repositories)
