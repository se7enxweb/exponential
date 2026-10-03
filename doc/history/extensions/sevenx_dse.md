# sevenx_dse (Database Source Editor): chronicle

The Database Source Editor was imported on 21 April 2026 (1.0.0) and fixed for persistent workers (a fresh nonce per request, a fresh worker after each dashboard request) and for its menu identifier in September. See the [feature page](../../features/6.0/extensions/sevenx_dse.md).

This page lists **every one of the 11 changes** of the repository `sevenx_dse` between 2026-04-21 and 2026-09-30, by month, with what kind of change each is. Read it to find out when a behaviour arrived and which release you need for it. The complete machine-made record, with sizes, is the [change ledger](../ledger/sevenx_dse.md); what each release contains is in the [release notes](../../changelogs/extensions/sevenx_dse.md); how to use the extension is on its [feature page](../../features/6.0/extensions/sevenx_dse.md).

| Kind | Changes |
|---|---|
| feature | 1 |
| fix | 4 |
| security | 1 |
| docs | 2 |
| release | 2 |
| no user benefit | 1 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2026-04-23 | v1.0.0 | [`baa6a74`](https://github.com/se7enxweb/sevenx_dse/commit/baa6a74) |
| 2026-09-22 | v1.1.0 | [`e84b3d1`](https://github.com/se7enxweb/sevenx_dse/commit/e84b3d1) |
| 2026-09-24 | v1.1.1 | [`8274510`](https://github.com/se7enxweb/sevenx_dse/commit/8274510) |
| 2026-09-27 | v1.1.2 | [`420424c`](https://github.com/se7enxweb/sevenx_dse/commit/420424c) |
| 2026-09-29 | v1.1.3 | [`a02495d`](https://github.com/se7enxweb/sevenx_dse/commit/a02495d) |
| 2026-09-30 | v1.1.4 | [`8203c12`](https://github.com/se7enxweb/sevenx_dse/commit/8203c12) |

## Timeline

### 2026-04

The month across all extensions: [April 2026](months/2026-04.md). [Ledger of this month](../ledger/sevenx_dse.md#2026-04-2-changes).

- 2026-04-21 [`f320455`](https://github.com/se7enxweb/sevenx_dse/commit/f320455) (feature) Initial Import of 7x Database Source Editor Exponential Extension sevenx_dse.
- 2026-04-23 [`baa6a74`](https://github.com/se7enxweb/sevenx_dse/commit/baa6a74) (docs) Add screenshots section to README **Release v1.0.0.**

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/sevenx_dse.md#2026-09-9-changes).

- 2026-09-22 [`e84b3d1`](https://github.com/se7enxweb/sevenx_dse/commit/e84b3d1) (fix) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v1.1.0.**
- 2026-09-24 [`84688cc`](https://github.com/se7enxweb/sevenx_dse/commit/84688cc) (security) Fixed: Fixed the AdminNeo content security policy nonce staying the same for every request a persistent worker serves.
- 2026-09-24 [`8274510`](https://github.com/se7enxweb/sevenx_dse/commit/8274510) (fix) Fixed: Fixed a second DSE dashboard request in the same persistent worker dying on AdminNeo redeclaring its classes. **Release v1.1.1.**
- 2026-09-27 [`420424c`](https://github.com/se7enxweb/sevenx_dse/commit/420424c) (fix) Fixed: The ezinfo.php and extension.xml report the release version and name the license in full, so the about page shows them **Release v1.1.2.**
- 2026-09-28 [`a8c104f`](https://github.com/se7enxweb/sevenx_dse/commit/a8c104f) (no user benefit) GitHub funding metadata, the same as the other se7enxweb packages
- 2026-09-29 [`fdadaae`](https://github.com/se7enxweb/sevenx_dse/commit/fdadaae) (fix) Fixed: The navigation part has its own identifier and the menu texts are translated
- 2026-09-29 [`a02495d`](https://github.com/se7enxweb/sevenx_dse/commit/a02495d) (release) Version 1.1.3 **Release v1.1.3.**
- 2026-09-30 [`c2c8e9a`](https://github.com/se7enxweb/sevenx_dse/commit/c2c8e9a) (docs) The description calls the product Exponential
- 2026-09-30 [`8203c12`](https://github.com/se7enxweb/sevenx_dse/commit/8203c12) (release) Version 1.1.4 **Release v1.1.4.**

## Related pages

- [Feature page](../../features/6.0/extensions/sevenx_dse.md)
- [Release notes](../../changelogs/extensions/sevenx_dse.md)
- [Change ledger](../ledger/sevenx_dse.md)
- [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
