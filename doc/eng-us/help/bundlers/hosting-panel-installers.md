---
title: Hosting panel installers
audience: bundler, hosting
category: bundling
order: 300
summary: How to bundle Exponential as a one-click install in a hosting control panel or an app installer - which installer to call, the steps around it (account, database, code, install as the account's user, web server rules, cron, password hand-over), safety rules, and what the customer should receive.
keywords: panel, control panel, one-click installer, app installer, Plesk, cPanel, DirectAdmin, ISPConfig, Webmin, exp:install, setup wizard, database, password, cron, bundle
type: help
---
# Hosting panel installers

This page is for bundlers who write the "Install Exponential" button of a hosting control panel or an application
installer. The hosting section has the operator's view in
[One-click installers for Exponential](../hosting/provisioning/one-click-installers.md); this page is the checklist
for the person who builds and ships the installer.

## Contents

1. [Which installer to call](#which-installer-to-call)
2. [What the installer package contains](#what-the-installer-package-contains)
3. [The install sequence](#the-install-sequence)
4. [Safety rules](#safety-rules)
5. [What the customer receives](#what-the-customer-receives)
6. [Panel notes](#panel-notes)

## Which installer to call

| Panel situation | Call |
|---|---|
| The panel can run a command as the account's user | `exp:install`: "Prefer `exp:install` in automation" ([4.6](../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)) |
| The account has no shell, only file upload and a browser | place the code and let the customer finish in the setup wizard, optionally pre-filled by a `kickstart.ini` ([The setup wizard on shared hosting](../hosting/provisioning/setup-wizard-on-shared-hosting.md)) |
| You maintain fixed templates per plan | the kickstarter with a template per plan ([kickstart.ini templates](kickstart-ini-templates.md)) |

## What the installer package contains

- The code: a prepared archive of a pinned release built from your lock file, or the commands to fetch it
  ([Getting the code](getting-the-code-for-a-bundle.md)).
- The mapping from the panel's form fields to `exp:install` options: site URL to `--url`, e-mail to `--email`, the
  database the panel created to `--db`, `--db-host`, `--db-name`, `--db-user`, the password through
  `EXP_INSTALL_DB_PASSWORD`, the site package to `--package`.
- The web server rules for the panel's server ([Web server and engine choices](web-server-and-engine-choices.md)).
- An uninstall description: which files, which database, which cron entries.
- Your notices and the source offer ([License texts and notices](license-texts-and-notices-to-ship.md)).

## The install sequence

From [What the one click must do](../hosting/provisioning/one-click-installers.md#what-the-one-click-must-do):

1. Prepare the account: system user, home directory, PHP-FPM pool or Velocity instance.
2. Prepare the database: nothing for SQLite 3; for a server engine, an empty database and a user that may create
   tables.
3. Put the code in place.
4. Run the installer as the site's user, never as root, with a dry run first:

   ```bash
   sudo -u <site user> env EXP_INSTALL_DB_PASSWORD='<from your secret store>' \
     php bin/php/console exp:install --dry-run --db=pgsql --db-user=<db user> --url=https://www.example.com
   sudo -u <site user> env EXP_INSTALL_DB_PASSWORD='<from your secret store>' \
     php bin/php/console exp:install --apply --db=pgsql --db-user=<db user> --url=https://www.example.com \
     --email=<customer contact>
   ```

5. Serve the site.
6. Install the crontab for `runcronjobs.php`.
7. Hand over the password and delete `var/log/initial-admin-password`.
8. Register the site in backups and monitoring.

## Safety rules

- "Never point `--db-action=remove` at a database that holds data." A one-click installer creates a new, empty
  database each time.
- Keep secrets off the command line: an option "is visible to every user of the machine in the process list (`ps`)
  while the installation runs".
- "Check the exit status. `0` installed, `1` anything failed; on failure maintenance mode stays on, and
  `var/log/setup.log` says why."
- "Do not leave install files behind": a `kickstart.ini` holds passwords in clear text.

Source: [Safety rules](../hosting/provisioning/one-click-installers.md#safety-rules).

## What the customer receives

- The site address and the administration address.
- The login `admin` and its password, once, through a secure channel, with advice to change it and create personal
  accounts ([Default credentials and first sign-in](default-credentials-and-first-sign-in.md)).
- The site package you chose, with or without demo content.
- A link to the help in their administration, and to your own support page ([Support boundaries](support-boundaries.md)).

## Panel notes

The hosting section has a page per panel: [Plesk](../hosting/panels/plesk.md), [cPanel](../hosting/panels/cpanel.md),
[DirectAdmin](../hosting/panels/directadmin.md), [ISPConfig](../hosting/panels/ispconfig.md),
[Webmin](../hosting/panels/webmin.md), and on [PHP selection and FPM pools](../hosting/panels/php-selection-and-fpm-pools.md)
and [Cron and shell access](../hosting/panels/cron-and-shell-access.md). Whether a particular app-installer catalog
lists Exponential is not documented here; ask its vendor.

## Related pages

- [One-click installers for Exponential](../hosting/provisioning/one-click-installers.md)
- [Automating provisioning](../hosting/provisioning/automating-provisioning.md)
- [Unattended install with exp:install](unattended-install-with-exp-install.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
