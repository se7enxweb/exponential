# Getting started: install Exponential and have a working site in minutes

This guide is for anyone who starts with Exponential: an editor who wants a site to try, a developer, an operator.
You need no prior knowledge. At the end you have an installed site, you are logged in to the administration
interface, you have published a first piece of content, and you have changed a template and seen the change on the
site. Allow ten to fifteen minutes. The install itself takes about a minute on SQLite.

Every command is run from the root of the installation (the directory that holds `console`, `index.php` and
`settings/`). Commands that start with `./console` are the same programs as `php bin/php/<name>.php`.

## 1. What you need

| Need | Detail |
|---|---|
| PHP | 8.0 or newer; `composer.json` allows 8.0 up to 8.8. Exponential Velocity, the built-in server of step 4, needs 8.1 or newer. The command line PHP needs the PDO driver of your database (SQLite is enough to start). |
| Composer | version 2.x, only for the Composer install paths below |
| A database | none to start: the default is SQLite in a file. MySQL or MariaDB, PostgreSQL, MongoDB and Oracle are choices of `--db=`. |
| A web server | any for production (Apache with the shipped `.htaccess`, or PHP-FPM behind a server). To try the site at once, Exponential brings its own, see step 4. |
| `memory_limit` and `date.timezone` | set both in `php.ini`; the installer's checks ask for them |

Check the PHP you will use:

```bash
php -v
```

Expected: a line beginning `PHP 8.`.

## 2. Get the files

Pick one path. All three give the same installation.

**A. Composer project** (the quickest for a new site):

```bash
cd /var/www
composer create-project se7enxweb/exponential exponential
cd exponential
```

**B. Git clone** (when you want the history and to follow changes):

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
composer install
```

**C. Packages.** Single add-ons (content, classes, templates, files) are `.ezpkg` archives that you install after the
base installation with `ezpm`, see [ezpm](../features/6.0/ezpm-package-manager-cli.md) and
[package compare and import](../features/6.0/package-compare-and-import.md). The installer in step 3 itself installs
a site package (default `sevenx_multisite`), so you need nothing else for a first site.

Check that the console runs:

```bash
./console --version
```

Expected: the first line reads `console (Exponential) 6.0.15stable` (or the version you installed), and the text ends
with `Written by 7x (se7enx.com) and the Exponential contributors.`
If you run as `root`, add `--allow-root-user` to the scripts that ask for it. The script's message says so.

## 3. Install

### 3.1 The one command

```bash
./console exp:install --random-password --url=http://localhost:8087
```

This installs the multisite site package on SQLite, in `eng-US`, with the siteaccesses `site` (the public site) and
`admin` told apart by the first part of the address. The administrator password is generated (24 characters) and shown
once at the end, and also written to `var/log/initial-admin-password`; `--random-password` asks for that explicitly.
A password of your own with `--password=...` is kept as given when it has at least 10 characters and is not a
well-known one such as `publish` or `admin`; otherwise a generated one replaces it and the summary says so.
The install refuses to run over an existing installation unless you add `--force`, and `--help` lists every option.

Expected, at the end (values differ):

```text
================================================================
  Exponential is installed
================================================================
  Installed:     <date> (<seconds>s)
  Site:          http://localhost:8087/site/
  Admin login:   http://localhost:8087/admin/user/login
  Username:      admin
  Password:      <the generated password>
  ...
================================================================
```

Copy the Admin, Username and Password lines now. A copy of the configuration used, with passwords masked, is in
`var/log/`; the progress of the installation is in `var/log/setup.log` ([what the logs tell you](../features/6.0/install-in-one-command.md#what-the-logs-tell-you)).

### 3.2 Other databases

```bash
EXP_INSTALL_DB_PASSWORD='YOUR_PASSWORD' ./console exp:install --db=mysql --db-host=127.0.0.1 \
    --db-name=mysite --db-user=root --db-action=remove --random-password --url=http://localhost:8087
```

`--db-action=remove` empties the named database first, so use a database that holds nothing you need. The default
ports and names of each engine are in [Install in one command](../features/6.0/install-in-one-command.md).

### 3.3 From a file (repeatable installs)

For the same installation on several servers, write the answers once and run them:

```bash
php bin/php/console exp:kickstarter ini        # writes kickstart.ini after a few questions
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --force
```

`--dry-run` checks the answers and the packages and writes nothing. See
[Kickstarter](../features/6.0/kickstarter-cli.md) and, for every key, [Kickstarter CLI](../bc/6.0/kickstartercli.md).
Keep your `kickstart.ini` out of version control; it holds the database password.

### 3.4 The browser wizard

Open the site address in a browser before any installation exists and the setup wizard starts. SQLite is listed
first and the bundled site is preselected, so pressing **Next** on every step installs the default site. See the
[setup wizard](../features/6.0/setup-wizard-and-editor-siteaccess.md) and [clean install defaults](../features/6.0/clean-install-defaults.md).

## 4. See the site

You need a web server that answers on the address you gave. For a first look use the one Exponential ships:

```bash
./console exp:velocity start --engine=php
./console exp:velocity status --engine=php
```

The `php` engine is PHP's own server and listens on port 8087 by default (`velocity.ini [PHPServerSettings] Port`), which is why
step 3 used `--url=http://localhost:8087`. Open `http://localhost:8087/site/` in a browser: you see the front page of
the installed site. Stop it again with `./console exp:velocity stop --engine=php`.

If you ran step 3 with another `--url`, use the Site address that the installer printed.

For production use Apache with PHP-FPM, or Velocity's own `qbix` or `frankenphp` engines: [Velocity engines](../bc/6.0/velocity-engines.md).
If a page is an error, nothing is hidden by the cache yet; read `var/log/error.log` and see
[Repairing an installation](../bc/6.0/repair.md).

## 5. First login

1. Open the Admin login address from step 3, for example `http://localhost:8087/admin/user/login`.
2. User name `admin`, and the password the installer printed.
3. You land on the dashboard of the administration interface.
4. Use the **Change password** link of the admin's user menu and choose a password of your own.
   If the installer wrote `var/log/initial-admin-password`, delete that file afterwards.

The first thing to know about the menus: a person only sees the entries their roles allow. `admin` is in the
Administrator role and sees everything.

## 6. First content

1. In the admin, open the **Content** tab, **Content structure**.
2. Select the top node (the front page folder) and choose **Create here** with the class **Article** (or **Folder**),
   then **Create**.
3. Fill in the title and the text in the editor, then press **Send for publishing**.
4. Open `http://localhost:8087/site/` (reload) and look for your item. Its own page address is shown in the admin
   under **Locations**.

Class names and fields depend on the site package you installed; the content model is explained in
[Content model and editing](content-model-and-editing.md).

If an item is published but missing from a list, clear the content cache once:

```bash
php bin/php/ezcache.php --clear-tag=content --allow-root-user
```

The cache command and its groups: `./console exp:cache --help`; a dry run of any clear is `--dry-run`.

## 7. First template change

A template is a `.tpl` file. The site shows the design named by the public siteaccess; ask Exponential which one:

```bash
./console exp:ini where site/DesignSettings/SiteDesign site
```

Expected: the files that set it, in load order, and `In effect:` with the design name (for the multisite package the
last line reads `SiteDesign=media` or similar; use the name you are shown).

### Click path (safe, no files)

1. Admin menu: **Design** > **Template Editor**.
2. Choose a template of your design, for example the one for the article full view, and open it.
3. Add a visible line, for example `<p>Hello from my template</p>`, and save.
4. Reload the item on the site: the line is there.

How the editor creates, orders and edits overrides: [Template editor](../features/6.0/template-editor-overrides.md).

### File path (what you put in version control)

1. Find the template that renders what you want to change. Switch on the template path comments to see it in the page source:
   see [Template path comments](../features/6.0/template-path-comments.md).
2. Copy the file from where it is (for example `design/standard/templates/`) to the same relative path under your
   design's directory `design/<design>/templates/` (or its `override/templates/`), and edit the copy.
3. If you added a new template path rather than changing an existing file, clear the template caches once:
   `php bin/php/ezcache.php --clear-all --allow-root-user`.

The order in which designs and overrides are tried is in [Template override ordering](../features/6.0/template-override-ordering.md)
and [Extension loading order](../features/6.0/extension-loading-order.md).

## 8. When something goes wrong

| Symptom | Check |
|---|---|
| `exp:install` says an installation exists | Add `--force` only if you mean to replace it. |
| A white page or a 500 error | Read `var/log/error.log`. Check that `php -v` shows 8.0 or newer (8.1 for Velocity) and that `vendor/` exists (run `composer install`). |
| The site address does not answer | Is the server running (`./console exp:velocity status --engine=php`)? Does the port match `--url`? |
| You forgot the password | `exp:install` showed it once; a generated one is also in `var/log/initial-admin-password`. The setup wizard and the kickstarter write theirs once to `var/log/initial-admin-password`. Set a new one with `php bin/php/resetuserpassword.php --allow-root-user -u admin -g`, see [Reset a user password](../features/6.0/reset-user-password.md). |
| A change does not show | Clear the content cache (step 6) and reload the page. |

## Related pages

| You want to | Read |
|---|---|
| Understand classes, objects and nodes | [The content model and editing content](content-model-and-editing.md) |
| Change how pages look | [Templates and design](templates-and-design.md) |
| Add or write an extension | [Extensions](extensions.md), [RAD tools](../features/6.0/rad-tools.md) |
| Put the site on a real server | [Deploying](deploying.md) |
| Keep the site healthy | [Operating a site](operating-a-site.md), [Security and audit](security-and-audit.md) |
| Move to a newer release | [Upgrading](upgrading.md) |
| Change settings from the command line | [exp:ini](../features/6.0/exp-ini-command.md) |
| Keep an eye on changes made in the admin | [Audit trail](../features/6.0/audit-trail.md) |
| Run recurring jobs | [Cronjobs console](../features/6.0/cronjobs-console.md) |
| See what changed and when | [Chronicle by month](../history/README.md), [Changelog 6.0.15](../changelogs/6.0/6.0.15.md) |
| Know what an install does by default | [Clean install defaults](../features/6.0/clean-install-defaults.md), [Installer logs and seed data](../specifications/6.0/installer-logs-and-seed-data.md) |
| Look up a word | [Glossary](../glossary.md) |
