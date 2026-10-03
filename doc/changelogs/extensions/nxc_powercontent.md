# nxc_powercontent (content from code and REST): release notes

What each release of `nxc_powercontent` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/nxc_powercontent.md); the story is in the [chronicle](../../history/extensions/nxc_powercontent.md).

## v1.4.3 (2026-10-02)

**Updated**

- content/edit declares its redirect helper only once, so a persistent PHP worker can run it more than once ([`23a8bc3`](https://github.com/se7enxweb/nxc_powercontent/commit/23a8bc3))

1 version, merge or metadata commit not listed.

## v1.4.2 (2026-10-02)

**Updated**

- The PDF export no longer stops with a fatal error: the content cache info is read from an object instance, as PHP 8 requires ([`dbb4c21`](https://github.com/se7enxweb/nxc_powercontent/commit/dbb4c21))

1 version, merge or metadata commit not listed.

## v1.4.1 (2026-10-02)

**Maintenance, documentation and packaging**

- The copyright notices name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices ([`d55466a`](https://github.com/se7enxweb/nxc_powercontent/commit/d55466a))

1 version, merge or metadata commit not listed.

## v1.4.0 (2026-09-22)

**Updated**

- Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. ([`3d0e28a`](https://github.com/se7enxweb/nxc_powercontent/commit/3d0e28a))

## v1.3.0 (2026-07-12)

**Added**

- feat(nxc_powercontent): add class_list fetch function and update related methods for broader compatibility ([`5bbe3d2`](https://github.com/se7enxweb/nxc_powercontent/commit/5bbe3d2))

## v1.2.0 (2026-06-21)

**Added**

- Add Copy and Hide/Unhide action handlers to content/action.php ([`85f728e`](https://github.com/se7enxweb/nxc_powercontent/commit/85f728e))

**Updated**

- Updated class method to provide nullable type bugfix required for php 8.5 support. Enhancement ([`e554e30`](https://github.com/se7enxweb/nxc_powercontent/commit/e554e30))

1 version, merge or metadata commit not listed.

## v1.1.0 (2024-10-29)

**Updated**

- Extend and refactor solution for use within a rest api extension ezprestapi for v2 legacy rest crud calls functionality ([`58c1fc4`](https://github.com/se7enxweb/nxc_powercontent/commit/58c1fc4))

## Related

* [Feature page](../../features/6.0/extensions/nxc_powercontent.md)
* [Chronicle](../../history/extensions/nxc_powercontent.md)
* [Change ledger](../../history/ledger/nxc_powercontent.md)
* [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
