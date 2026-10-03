# ezwebin-ezpackage: the installer packages of ezwebin

`ezwebin-ezpackage` is the repository of the **setup wizard packages** of the Website Interface: the content classes, sample content, settings and installer
code (`ezwebin_site`, `ezwebin_site_clean` and their installers) that the setup wizard imports to create a site. The code of the design itself is
[ezwebin](ezwebin.md); this repository is the package source the design was split from ("mass updates from parent repository ezwebin-ezpackage").

## What changed

* December 2023 to March 2024: the package installer settings no longer cause a fatal error when installing through the setup wizard; **SQLite** database support;
  the `eZArchive` class uses a PHP 5 style constructor and works with **MySQL 8**; a MySQL 8 fix for the blog content template code; the vendor and package names
  and the suggested and required vendors in `composer.json`; internals of `ezwebin` and `ezwebin_site_clean` updated for release testing and a fix for the
  clean installer.
* 19 July 2026: HTML5 markup in the packaged ezwebin templates: XHTML self-closing slashes and obsolete `type` attributes removed from 56 files, the same
  cleanup as in [ezwebin](ezwebin.md) 6.0.3.

## Related

* [ezwebin](ezwebin.md), [ezdemo](ezdemo.md)
* [Chronicle](../../../history/extensions/ezwebin-ezpackage.md) and [release notes](../../../changelogs/extensions/ezwebin-ezpackage.md)
