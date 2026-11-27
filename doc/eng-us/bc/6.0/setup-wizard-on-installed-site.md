# The setup wizard on an installed site: CheckValidity=true lets it run again (6.0.15)

Early 6.0.15 code refused the setup wizard on an installed site: with `[SiteAccessSettings] CheckValidity=true` it
showed "This site is already installed" and offered no way through, unless `[SetupSettings]
AllowWizardOnInstalledSite=enabled` was also set. Setting `CheckValidity=true` is an administrator's explicit request
for the wizard, so that was a regression. From this change on:

| `AllowWizardOnInstalledSite` | Installed site, `CheckValidity=true` | Installed site, served normally (`setup/init` in the admin) |
|---|---|---|
| `auto` (the new default) | the explanation page with **Run the setup wizard anyway** | the explanation page only |
| `enabled` | the explanation page with the button | the explanation page with the button |
| `disabled` | the explanation page only | the explanation page only |

A fresh installation is not affected: the wizard runs as before. A value other than the three counts as `auto`.

## What stays as it was

- **Nothing is written before the confirm.** Opening the wizard, the explanation page and the button change nothing:
  no maintenance marker, no run in `var/log/setup.log`. The button leads to the confirm page ("Reinstall this
  site?"), and only that page's POST, with its one-time token, starts the run. The token is paired with a cookie of this browser
  (`exp_setup_confirm`, 15 minutes), since the wizard runs before the session and the database start. Every page of
  the wizard is sent with `Cache-Control: no-store, private`, so no cache keeps a token page.
- **The maintenance lease.** Once confirmed, the whole site is in maintenance mode until the wizard finishes or has
  been left for 15 minutes (`expMaintenance::WIZARD_LEASE`); a step posted after the lease ran out returns to the
  confirm page.
- **The explanation.** The page says the site is installed and that it is reinstalled with the wizard, the kickstarter
  (`kickstart.ini`, then `php bin/php/kickstarter.php run --apply`) or a fresh installation.

## The wizard's forms post to the site root

Through the validity check every address of the site is the wizard (`index.php` hands each request to it). The
explanation page, the confirm page and every step now post back to the site root (`/`), no longer to `/setup/init`:
`/setup/...` is the address of an admin module, which a frontend firewall rightly refuses. With request-shield's
Exponential rules, `EXP-MODULES` answered it with 404; take the rule `EXP-SETUP-POST` (`allow POST /`) of
exp_request_shield's `rules/exponential.rules`, or the wizard's POST to `/` is answered 405 by `EXP-POST`.

## What to do

- **A live site:** keep `CheckValidity=false`, as the installer writes it. To make sure the wizard never runs, even if
  someone sets `CheckValidity=true`, set `AllowWizardOnInstalledSite=disabled` in `settings/override/site.ini.append.php`.
- **To reinstall with the wizard:** set `CheckValidity=true`, open the site's root, press **Run the setup wizard
  anyway**, then **Start the setup wizard**. The wizard sets `CheckValidity=false` again at its end.
- **Behind request-shield:** add `EXP-SETUP-POST` if your rules are a copy of an older `exponential.rules`.

Code: `kernel/classes/expsetupwizardguard.php` (`mode()`, `allowedOnInstalledSite()`, `decide()`, `formAction()`),
`kernel/private/classes/views/setup/ezsetup.php`, `design/standard/templates/setup/init/installed.tpl` and
`confirm_reinstall.tpl`. Tests: `tests/tests/kernel/classes/setup/SetupWizardGuardTest.php` (no database).
