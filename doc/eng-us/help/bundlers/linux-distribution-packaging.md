---
title: Linux distribution packaging
audience: bundler, hosting, developer
category: bundling
order: 310
summary: What a Linux distribution packager should know before packaging Exponential as deb or rpm - the project publishes no distribution package of the CMS (Velocity has deb and rpm packages), the installation writes into its own tree, how Composer dependencies relate to distribution policies, what the post-install step should and should not do, and updates.
keywords: Linux distribution, packaging, deb, rpm, Debian, Ubuntu, Fedora, Enterprise Linux, FHS, /usr/share, /var/lib, Composer, vendor, post-install, Velocity package, bundle
type: help
---
# Linux distribution packaging

This page is for packagers who want Exponential installable with `apt` or `dnf`. It states what exists and what
packagers must decide themselves. The project publishes **no deb or rpm package of the Exponential CMS**; it does
publish deb and rpm packages of **Velocity**, its web server.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../examples/web-servers/nginx.md), [LiteSpeed](../../examples/web-servers/litespeed.md),
> [Caddy](../../examples/web-servers/caddy.md), [IIS](../../examples/web-servers/iis.md), [Velocity](../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../hosting/panels/plesk.md), [cPanel](../hosting/panels/cpanel.md), [DirectAdmin](../hosting/panels/directadmin.md), [ISPConfig](../hosting/panels/ispconfig.md).

## Contents

1. [What the project publishes](#what-the-project-publishes)
2. [How the installation is laid out](#how-the-installation-is-laid-out)
3. [Composer and distribution policy](#composer-and-distribution-policy)
4. [The post-install step](#the-post-install-step)
5. [Updates through the package manager](#updates-through-the-package-manager)
6. [Before you publish](#before-you-publish)

## What the project publishes

| Item | Form | Source |
|---|---|---|
| Exponential 6 | Git tags and the Composer package `se7enxweb/exponential` | [3.1](../../install/03-getting-the-code.md#31-versions-tags-and-branches) |
| Extensions | Composer packages, one per extension | `composer.json` |
| Velocity | Composer package, deb for Debian 12/13 and Ubuntu 22.04/24.04, rpm for Enterprise Linux 9/10, Docker images, binaries | [Velocity packages](../../features/6.0/velocity-packages-and-binaries.md#operating-system-packages) |

The Velocity packages are a working model of how the project packages: architecture-independent, "Nothing starts on
install", a `qbix` system user, settings in `/etc/default/exponential-velocity`.

## How the installation is laid out

Exponential is "a directory of PHP files" whose document root is the installation root
([Web server examples](../../examples/web-servers/README.md)). It writes into its own tree:

| Directory | Written | Note |
|---|---|---|
| `var/` | always | caches, logs, uploads, autoload arrays, the SQLite database |
| `settings/`, `settings/override`, `settings/siteaccess` | by the installers and the administration | `ManagedByDeployment=enabled` stops the administration from writing settings |
| `design/`, `extension/` | while packages are installed from the administration | |
| `autoload/`, `var/autoload/` | by `ezpgenerateautoloads.php` | kernel and extension arrays |

Source: [Writable directories and permissions](../hosting/basics/writable-directories-and-permissions.md#what-must-be-writable).

What this means for a package:

- Install the tree in one place (for example under `/usr/share/<name>/`) and **symlink or bind** the writable parts
  to `/var/lib/<name>/` and `/etc/<name>/` only after testing that every writer follows the links. The documentation
  does not describe a split layout; you own that test.
- `site.ini [FileSettings] VarDir` is set per siteaccess by the installer (`var/<package>` or `var/site`), and extra
  extension roots can be added with `[ExtensionSettings] AdditionalExtensionDirectories[]`
  ([Additional extension directories](../../bc/6.0/AdditionalExtensionDirectories.md)). Those are the settings a
  layout can lean on.
- The web server configuration you ship must keep the routing of `.htaccess_root`.
- `.htaccess` and `config.php` are installation files, not package files: ship the templates (`.htaccess_root`,
  `.htaccess_root_static`, `config.php-RECOMMENDED`) and let the administrator, or your post-install step, copy them
  with `exp:setup:htaccess` and `exp:setup:config`. An update then never overwrites a changed copy: the commands show
  the diff and refuse unless given `--force`
  ([Setting up .htaccess and config.php from the console](../../guides/setup-files-from-the-console.md)).

## Composer and distribution policy

Exponential depends on many Composer packages (`vendor/`) and on extensions installed by a Composer plugin into
`extension/`. Distributions differ in whether they accept vendored dependencies. Decide with your distribution's
policy, and note:

- the extension set is defined by `composer.json` `require`; leaving required extensions out changes the product
  ([Adding and removing extensions](adding-and-removing-extensions.md));
- `composer licenses` gives the license of every Composer package for your copyright file
  ([Third-party components](third-party-components-and-their-licenses.md)).

## The post-install step

A distribution package should not install a site by itself:

- **Do:** create the system user, set ownership of the writable directories, install a disabled web server and cron
  configuration, regenerate autoloads, print where the documentation and the install command are. Putting `.htaccess`
  and `config.php` into place is safe here, because both commands leave an existing file alone and change nothing on
  a second run; run as root they need `--allow-root-user` and `--owner=<user>:<group>` for the site's user:

  ```bash
  php bin/php/console exp:setup:htaccess --apply --allow-root-user --owner=www-data:www-data
  php bin/php/console exp:setup:config --apply --allow-root-user --owner=www-data:www-data
  ```
- **Do not:** create a database, run `exp:install` with defaults, or start a web server. The administrator runs the
  install once, with their own URL, database and e-mail ([Unattended install with exp:install](unattended-install-with-exp-install.md)).

## Updates through the package manager

A package update replaces code; Exponential updates have a second half, the database update files and the checklist
of each period ([Chapter 11.6](../../install/11-upgrading.md#116-from-any-60x-to-today)). The package can:

- clear caches and regenerate autoloads after the update ([Caches and autoloads](caches-and-autoloads-after-building.md));
- tell the administrator when `.htaccess` no longer matches the new `.htaccess_root`: `exp:setup:htaccess` without
  `--apply` exits 1 and prints the diff, and `exp:config:list` shows settings new in `config.php-RECOMMENDED` as
  `(not set)`;
- tell the administrator that a database update may be due, with the link;
- never run database updates unattended on a site it does not know.

## Before you publish

- [ ] Copyright file lists Exponential (GPL-2.0-or-later), every extension and every vendored package.
- [ ] Source package available, as distributions require anyway.
- [ ] The package name and description follow [Naming and branding a bundle](naming-and-branding-a-bundle.md).
- [ ] Installation, upgrade and removal tested on a clean system.
- [ ] Security fixes have a path: you track releases and rebuild ([Updates and upgrade paths](updates-and-upgrade-paths.md)).

## Related pages

- [Operating system support](../os-support.md)
- [Velocity packages, Docker images and binaries](../../features/6.0/velocity-packages-and-binaries.md)
- [GPL obligations when you distribute](gpl-obligations-when-distributing.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
