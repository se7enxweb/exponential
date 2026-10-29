---
title: "The setup wizard, then and now"
audience: user, developer, admin, builder
category: books
order: 110
summary: The twelve pages of the Learning book's setup wizard, matched to the pages of the Exponential 6 wizard, with what changed (database choices, site packages, siteaccess methods) and the commands that skip the wizard.
keywords: setup wizard, installer, system check, database, language, site package, siteaccess, URL, port, hostname, kickstarter, exp:install, Learning book
type: help
---
# The setup wizard, then and now

The book walks through the wizard page by page. The wizard is still there, with more pages and more choices, and two
commands can fill it in for you. This page lines up the book's pages with today's. The exact screens, fields and
messages are in [The setup wizard](../../install/05-setup-wizard.md) of the installation book; read that when you
install.

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

## The pages

| Book (3.x) | Exponential 6 | What it asks |
|---|---|---|
| 1 Welcome | Welcome | The language of the wizard. A **Finetune** button appears when an optional test failed. |
| 2 System check | System check | Directories the server must write (`var/`, `settings/`, and more), PHP version and extensions, `memory_limit`. |
| (none) | System finetuning | Hints for the optional tests that did not pass. |
| 3 E-mail settings | Outgoing Email | How mail leaves the site: sendmail, SMTP, or the file transport that only writes mail to files. |
| 4 Choose a database | Choose database system | MySQL or MariaDB, PostgreSQL, SQLite, MongoDB, Oracle. |
| 5 Database initialization | Database initialization | Host, user, password, database; what to do when it already holds data. |
| 6 Language support | Language support | The default language and any others; add more later in the administration. |
| 7 Site packages | Site package, Package language options | A ready-made site to install. |
| 8 Site access configuration | Site access configuration | URL, port or hostname. |
| 9 Site details | Site details, Site administrator | Title, URL, database name, administrator account. |
| 10 Site security | Site security | The `.htaccess` file, when you do not use a virtual host. |
| 11 Site registration | Registration | Optional. |
| 12 Finished | Creating sites, Finished | The addresses of the new site and its administration. |

## What the book's advice still means

**The system check.** The book says to give the web server write access to `var`, `settings` and `design`, with
`chmod 777` as the quick (and, on shared hosting, unsafe) way, and `chown` to the web server's user as the better way.
Keep the better way: give the web server's user (or the PHP-FPM pool's user) ownership of the directories it must
write, and nothing wider. Never `777` on a server with other users. See [Permissions and ownership](../../install/12-troubleshooting.md#123-permissions-and-ownership).

**Choose a database.** The book's advice "if you do not have a reason for PostgreSQL, you probably do not need it"
generalizes: choose what you can run and back up. SQLite needs no server and is right for trying a site and for small
ones; MySQL or MariaDB is the common production choice ([Databases](../../install/09-databases.md)).

**Languages.** Unchanged: do not add languages you are not sure of; you can [add one later](../manual/how-do-i-add-a-content-language.md).

**Site packages.** The book lists eight packages (Blog, Corporate, Forum, Gallery, Intranet, News, Plain, Webshop) and
recommends *Plain* to build from scratch. The packages of an Exponential installation are different: the default is
`sevenx_multisite`, and the packages the installer offers are those found in the package repositories it can reach
([Packages](../../guides/packages.md)). For a site built from nothing, choose the smallest package offered and read
[Building a site without code](../extension-development/part-2-builder/building-a-site-without-code.md).

**Site access.** The three access types of the book are unchanged. **URL** (`/admin`) is the simplest; **port** and
**hostname** need web server configuration (and DNS, for host names). Every installation now gets three
siteaccesses: `site` for visitors, `admin` for the administration and `editor` for editors. See
[Siteaccess settings](../extension-development/part-2-builder/siteaccess-settings.md).

**Existing data.** When the database already holds data, the wizard asks what to do, as in the book: add to it, remove
it, leave it. Back up before you choose.

## Skipping the wizard

- `./console exp:install --apply` installs from the command line, with the database given as options
  ([Console install](../../install/07-console-install.md)).
- The kickstarter reads `kickstart.ini` and answers the wizard's pages for you
  ([Kickstarter](../../install/06-kickstarter.md)).

## Next

[Installation problems, then and now](installation-problems-then-and-now.md).


## Source and license

Adapted from chapter 1, "Installing eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: the pages matched to Exponential 6; advice on permissions, databases and site packages brought up to date.
