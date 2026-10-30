---
title: "Installing, then and now: the files, the database, the virtual host"
audience: user, developer, admin, builder
category: books
order: 100
summary: What the Learning book's first chapter had you do by hand (unpack, create a database, write a virtual host, set up images and cron, read the ini files), and what each step is in Exponential 6.
keywords: install, installation, tarball, database, virtual host, mod_rewrite, ini files, settings, override, siteaccess, Learning book
type: help
---
# Installing, then and now: the files, the database, the virtual host

Chapter 1 of the book *Learning eZ Publish* opens with the work every installation needs: get the files, make a
database, point the web server at the files, set up image conversion and a regular job, and learn where the
settings live. The steps still exist. Most of them are one command today. This page walks the book's order and says
what to do now.

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
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../guides/litespeed-page-cache.md).

## Unpacking the installation

The book downloaded an archive of about 4 MB, expanded it to about 20 MB and transferred the folders to the web
server. Exponential has three ways to get the same files (see [Getting started](../../guides/getting-started.md)):

- a Git clone of the kernel, with the extensions as clones (the way to build a website you maintain),
- `composer create-project se7enxweb/exponential`,
- and, for single add-ons later, packages installed with `ezpm`.

After either of the first two, the folders the book listed are all there:

| Book | Exponential 6 |
|---|---|
| `design` | `design/`: the default templates and styles. Your own go in an extension's `design/` folder, not here. |
| `index.php` | `index.php`: every page request still enters here. |
| `kernel`, `lib` | `kernel/`, `lib/`: the code. Do not edit them; see [kernel overrides, the last resort](../extension-development/part-3-extension-developer/kernel-overrides-the-last-resort.md). |
| `runcronjobs.php` | `runcronjobs.php`: still the script a regular job runs ([Cronjobs](../../guides/cronjobs.md)). |
| `settings` | `settings/`: defaults, plus `override/` and `siteaccess/` for yours. |
| `var` | `var/`: uploaded files, caches and logs. The web server's user must be able to write here. |

New in Exponential: `extension/` (add-ons), `console` (the command line front end, `./console list`) and `vendor/`
(Composer packages).

## Initializing the database

The book created a MySQL database and a user that may use only that database, with `CREATE DATABASE` and `GRANT ALL
PRIVILEGES`, and recommended that user rather than a powerful one, because the password is kept in a settings file
in plain text. That advice is unchanged. Two things are new:

- You need no database server to start: the default is SQLite in a file. MySQL or MariaDB, PostgreSQL, MongoDB and
  Oracle are choices of `exp:install --db=` and of the setup wizard ([Databases](../../install/09-databases.md)).
- In `GRANT`, MySQL 8 and MariaDB no longer accept `IDENTIFIED BY` inside it; create the user first with
  `CREATE USER`, then `GRANT`. The [installation book](../../install/09-databases.md) shows the statements.

## The virtual host and rewriting

The book explains why a virtual host with `mod_rewrite` is the right setup: all requests go to `index.php`, URLs
such as `/node/view/253` become readable addresses like `/contact`, and only that one script is reachable. The two
rewrite lines of the book (everything that is not a script, image or style file goes to `index.php`) are what the
shipped `.htaccess` does today, in a longer and safer form. With Apache, use the shipped `.htaccess` or put its rules
in the host's configuration. To try a site without any web server, Exponential brings its own, Velocity
([Serving the site](../../install/08-serving-the-site.md)).

Running the site in a subfolder, which the book allows, is not recommended any more: the siteaccess and URL alias
logic is built for a site at the root of a host name.

## Image settings: ImageMagick and GD

Images are converted on the server: the original you upload becomes the sizes your templates ask for (the *image
aliases*). The book names two converters, ImageMagick (a separate program, the more powerful) and GD (a library
inside PHP, a fallback). Both are still there, in `settings/image.ini`. The difference now: GD is first in the list
(it works inside PHP and starts no program), and ImageMagick is the fallback for formats GD cannot read, such as
TIFF or PDF, and for filters only it has. To prefer ImageMagick again, override the list in
`settings/override/image.ini.append.php`:

```ini
[ImageConverterSettings]
ImageConverters[]
ImageConverters[]=ImageMagick
ImageConverters[]=GD
```

The book's old note about GD before version 2 (poor scaling) is history: every GD in a PHP that runs Exponential is
version 2 or later. The setup wizard turns ImageMagick off by itself when it finds no `convert` program.

## Cron jobs

The book asks for a job every 15 minutes that runs `runcronjobs.php`, for workflows, notifications and other
periodic work. Same today. The book's line passed `-C` to PHP; it is not needed, and the options that matter are `-q` (quiet) and `-s <siteaccess>`. The cron table of
[After installing, 10.3](../../install/10-after-installing.md#103-cronjobs) is the current one, with a separate
line for each part (`frequent`, the default, `infrequent`, `cache_cleanup`). Without the job the site works, but
workflows that wait for the job and notifications by e-mail do not.

## Configuration files

The book's explanation is still exactly right: settings live in `.ini` files; the defaults are in `settings/`; the
folders `settings/siteaccess/<name>/` and `settings/override/` change them; the order is **override, then
siteaccess, then the defaults** (the first value found for a setting wins, from the top). The files a builder
touches most are as in the book: `site.ini`, `override.ini`, `image.ini`, and `i18n.ini` for languages. The file
name should end in `.ini.append.php`. The reason the book gives is still the reason: if a web server ever served the
file, it would run as PHP, and the settings are written as PHP comments, so nothing is shown.

Where it changed: you rarely need to edit those files by hand. `./console exp:ini` changes a setting with a backup
and shows the difference ([Changing settings with exp:ini](../extension-development/part-2-builder/the-exp-ini-command.md)),
and the administration has a settings page ([Settings in the admin](../extension-development/part-2-builder/settings-in-the-admin.md)).
The details of the order are in [Settings scopes and their order](../extension-development/part-2-builder/settings-scopes-and-their-order.md).

## Next

[The setup wizard, then and now](the-setup-wizard-then-and-now.md).

## Source and license

Adapted from chapter 1, "Installing eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: shortened and rearranged; every step, path and setting checked against Exponential 6 and updated; the product is named Exponential.
