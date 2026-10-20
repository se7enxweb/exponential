# ez-support-tools: system information for administrators

This page is for administrators and support engineers of the Symfony based platform. `ez-support-tools` is the
platform bundle that shows **information about the system** the platform runs on (the System Info screens). It is a
Symfony bundle, not a legacy extension. The se7enxweb fork keeps it so that it installs next to the other
`se7enxweb/ezplatform-*` packages.

## What changed in the fork

| Version or date | Change |
|---|---|
| 2024 | License information and copyright year of the upstream bundle; the hardcoded end-of-maintenance dates for version 3.3. |
| 2.3.13 (1 July 2025) | The `require` section names the 7x vendor packages. |
| 2.3.14 (28 September 2025) | Package descriptions and supported PHP versions updated; requires `se7enxweb/ezplatform-core`. |
| 11 April 2026 | The PHP requirement allows PHP 8.5. A guard for a **null version** from `InstalledVersions::getVersion()`. |

About the null version guard: a package installed from a path or VCS repository, or with no recorded version,
returned null. Passing null to `getStability(string $version)` was a fatal `TypeError` at boot that broke every
console command. The code now falls back to `getMinimumStability()`.

The platform side of the project is described in the [ecosystem overview](../../../history/ecosystem.md).

## Related pages

- [Chronicle of the repository](../../../history/ecosystem/ez-support-tools.md) and [release notes](../../../changelogs/extensions/ez-support-tools.md) (covered with the platform repositories)
- [Change ledger](../../../history/ledger/ez-support-tools.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
