# Upgrading to 6.0.15: the request shield

Exponential 6.0.15 brings the request shield, the extension `exp_request_shield` (request-shield 0.4.0, a small web
application firewall that decides every request before the kernel). This page says what changes for an existing
installation that moves to the 6.0.15 line, and what you have to do yourself.

Feature reference: [The request shield](../../features/6.0/request-shield.md) · Guide:
[Setting up the request shield](../../guides/request-shield.md) · Help:
[Request shield: start here](../../help/request-shield/README.md)

## In short

- **Nothing is filtered until the hook is in `config.php`.** An upgrade never edits the site's `config.php`. The
  package arrives with `composer.json`, the administration shows Setup > Request shield, but every request reaches
  the site as before until you add the two hook lines.
- The root `composer.json` requires `se7enxweb/exp_request_shield` (`~0.0.1.0`). `composer update` installs it in
  `extension/exp_request_shield/`.
- `config.php-RECOMMENDED` has a new section **REQUEST SHIELD** that loads the hook, above the HTTP cache's early
  exit. A `config.php` made from it for a **new** installation is protected from the first request.
- Activate the extension yourself on an existing installation (`ActiveExtensions[]=exp_request_shield`); the 6.0.15
  site packages do it for new installations.
- `settings/menu.ini` lists **Request shield** in the Setup menu right after **Log files**; the link is shown only
  when the extension is active and the user holds `requestshield/read`.
- Two new policies, `requestshield/read` and `requestshield/manage`. Only the Administrator role holds them.
- Under Exponential Velocity, `index.php` now runs the file named in `EXP_REQUEST_PREPEND_FILE`; that is how the
  shield reaches every Velocity request.

## Contents

1. [What changes](#what-changes)
2. [What to do](#what-to-do)
3. [The new Setup menu entry and policies](#the-new-setup-menu-entry-and-policies)
4. [Under Velocity](#under-velocity)
5. [If you do not want it](#if-you-do-not-want-it)
6. [Check after the upgrade](#check-after-the-upgrade)

## What changes

| Where | Before 6.0.15 | From 6.0.15 |
|---|---|---|
| `composer.json` (root) | no request shield | requires `se7enxweb/exp_request_shield` `~0.0.1.0` |
| `config.php-RECOMMENDED` | no shield | section **REQUEST SHIELD**: the hook, before the HTTP cache's early exit, with the constants `EXP_REQUEST_SHIELD_RULES` and `EXP_REQUEST_SHIELD_DISABLED` as comments |
| The site's own `config.php` | unchanged | **unchanged**: the upgrade does not touch it |
| `settings/menu.ini` | | `Links[request_shield]=requestshield/dashboard` in the **System** group of the Setup menu, after `logs` |
| `index.php` | | runs the file named in `EXP_REQUEST_PREPEND_FILE` at the start of every request (for Velocity) |
| Site packages `sevenx_site`, `sevenx_multisite`, `cjw_multisite` and their `_clean` variants | | activate `exp_request_shield` in new installations |
| The HTTP cache | | a request the shield marks `allow-uncached` is answered and never stored ([HTTP cache notes](httpcache.md#with-request-shield-in-front)) |

`composer create-project` of the distributions resolves to v6.0.14 until a 6.0.15 release is tagged; such a new
installation gets none of this and can add the shield with `composer require se7enxweb/exp_request_shield`.

## What to do

On an existing installation that moved to the 6.0.15 line:

1. Make sure the package is there (`extension/exp_request_shield/`); otherwise `composer require
   se7enxweb/exp_request_shield`.
2. Activate it in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=exp_request_shield
   ```

3. Regenerate the autoloads and clear the caches (`php bin/php/ezpgenerateautoloads.php -e`,
   `php bin/php/ezcache.php --clear-all`), or `./console exp:velocity deploy --packer` where it exists.
4. Give the site its own rule file and check it
   ([guide, section 2](../../guides/request-shield.md#2-give-the-site-its-own-rules)): the shipped rules
   **enforce**. Keep `www.example.org` in a host line, so the shipped examples pass.
5. Add the hook to `config.php`, above the line that loads `ezphttpcacheearlyexit.php`:

   ```php
   if ( is_file( __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php' ) )
       require __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php';
   ```

   Compare your `config.php` with `config.php-RECOMMENDED` for the comment that goes with it.
6. Under Velocity, `./console exp:velocity restart`.
7. Read Setup > Request shield > Log in the first days and add exceptions for anything a real visitor or editor
   did; switch to monitor while you look.

## The new Setup menu entry and policies

| Policy | Allows |
|---|---|
| `requestshield/read` | the dashboard, the log and the rules page (Setup > Request shield) |
| `requestshield/manage` | the switches on the dashboard: pause and switch on, enforce, monitor, off |

Only the Administrator role, which holds every policy, has them after the upgrade. Give them to another role in
**Roles and policies** if someone else should see or switch the shield. Users without `requestshield/read` do not
see the menu entry.

The pages exist in admin4 and admin4l, exp_adminui's adminui design and the classic admin design, light and dark.
Their stylesheet is packed by ezjscore: after the upgrade clear the `ezjscore-packer` and `template-block` caches
(part of `--clear-all`) so the pages are styled.

## Under Velocity

- The shield needs the 6.0.15 `index.php`, which runs `EXP_REQUEST_PREPEND_FILE`. The hook defines that constant
  under Velocity; an older `index.php` never runs it, so Velocity's requests are then not filtered while PHP-FPM's
  are.
- Restart Velocity after adding or removing the hook. Rule changes need no restart.
- With PHP-FPM and Velocity serving the same site, `set store file` in the rule file lets both share the counters.
- The answers are identical to PHP-FPM's.
- `EXP_REQUEST_PREPEND_FILE` names one file. The hook defines it only when it is not defined yet: if your
  `config.php` already names a file of its own there, the shield does not run under Velocity. Then require
  `extension/exp_request_shield/bootstrap/prepend.php` at the start of your own file.

## If you do not want it

Leave the hook out of `config.php`: nothing is filtered. To keep the code but hide the administration pages, do not
activate the extension. On an installation whose root `composer.json` requires the package, keep it installed rather
than removing the requirement, so the next `composer update` does not fight you.

To switch an installed shield off at once: create `var/request-shield/PAUSED`, or define
`EXP_REQUEST_SHIELD_DISABLED` as `true` before the hook line.

## Check after the upgrade

- Setup > Request shield shows the mode, the main rule file and no notice "did not run for this page".
- `php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules` and
  `test` pass, run as the web user.
- `curl -sI https://<your host>/settings/site.ini` answers 404, and `var/request-shield/log/shield.log` has the line.
- The front page, the login and an edit in the administration work as before (the administration asks for the
  browser check once per pass).
