# sevenx_dse (Database Source Editor): release notes

What each release of `sevenx_dse` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/sevenx_dse.md); the story is in the [chronicle](../../history/extensions/sevenx_dse.md).

## v1.1.4 (2026-09-30)

**Maintenance, documentation and packaging**

- The description calls the product Exponential ([`c2c8e9a`](https://github.com/se7enxweb/sevenx_dse/commit/c2c8e9a))

1 version, merge or metadata commit not listed.

## v1.1.3 (2026-09-29)

**Updated**

- Fixed: The navigation part has its own identifier and the menu texts are translated ([`fdadaae`](https://github.com/se7enxweb/sevenx_dse/commit/fdadaae))

2 version, merge or metadata commits not listed.

## v1.1.2 (2026-09-27)

**Updated**

- Fixed: The ezinfo.php and extension.xml report the release version and name the license in full, so the about page shows them ([`420424c`](https://github.com/se7enxweb/sevenx_dse/commit/420424c))

## v1.1.1 (2026-09-24)

**Updated**

- Fixed: Fixed the AdminNeo content security policy nonce staying the same for every request a persistent worker serves. ([`84688cc`](https://github.com/se7enxweb/sevenx_dse/commit/84688cc))
- Fixed: Fixed a second DSE dashboard request in the same persistent worker dying on AdminNeo redeclaring its classes. ([`8274510`](https://github.com/se7enxweb/sevenx_dse/commit/8274510))

## v1.1.0 (2026-09-22)

**Updated**

- Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. ([`e84b3d1`](https://github.com/se7enxweb/sevenx_dse/commit/e84b3d1))

## v1.0.0 (2026-04-23)

**Updated**

- Initial Import of 7x Database Source Editor Exponential Extension sevenx_dse. ([`f320455`](https://github.com/se7enxweb/sevenx_dse/commit/f320455))

**Maintenance, documentation and packaging**

- Add screenshots section to README ([`baa6a74`](https://github.com/se7enxweb/sevenx_dse/commit/baa6a74))

## Related

* [Feature page](../../features/6.0/extensions/sevenx_dse.md)
* [Chronicle](../../history/extensions/sevenx_dse.md)
* [Change ledger](../../history/ledger/sevenx_dse.md)
* [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
