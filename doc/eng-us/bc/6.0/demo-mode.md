# Demo mode: EXP_DEMO_MODE=1 for public demonstration sites (6.0.15)

A public demo of Exponential publishes its administrator sign-in, so that visitors can try the administration. Two
things then go wrong without help:

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
> panel: [Plesk](../../help/hosting/panels/plesk.md), [cPanel](../../help/hosting/panels/cpanel.md), [DirectAdmin](../../help/hosting/panels/directadmin.md), [ISPConfig](../../help/hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../guides/litespeed-page-cache.md).

1. A visitor changes or resets the administrator password, and the next visitor cannot sign in. On a demo that is
   rebuilt every few hours that breaks the demo until the next rebuild; on one that is not, for good.
2. Since 6.0.15 the installers refuse a well-known administrator password
   ([weak-passwords-and-reset-key-lifetime.md](weak-passwords-and-reset-key-lifetime.md)), so a demo installer
   could not install the password it publishes, and had to reset it after the installation.

Demo mode solves both. It is switched on by one **environment variable**, never by a setting:

```sh
EXP_DEMO_MODE=1
```

It is off on every installation that does not set it, which is every installation that has not been set up as a
demo on purpose. Nothing changes there.

## Contents

1. [What demo mode does](#what-demo-mode-does)
2. [Who counts as an administrator](#who-counts-as-an-administrator)
3. [Every path that is closed](#every-path-that-is-closed)
4. [The installers accept the demo password](#the-installers-accept-the-demo-password)
5. [Why it cannot be switched on by accident](#why-it-cannot-be-switched-on-by-accident)
6. [Switching it on](#switching-it-on)
7. [The banner and the log](#the-banner-and-the-log)
8. [Risks](#risks)
9. [The demo installer on the demo host](#the-demo-installer-on-the-demo-host)
10. [For developers](#for-developers)
11. [Compatibility](#compatibility)
12. [Tests](#tests)
13. [Where else demo mode is documented](#where-else-demo-mode-is-documented)

## What demo mode does

While `EXP_DEMO_MODE=1`:

- **The password of an administrator cannot be changed or reset**, by any path: the **Change password** page, the
  **Forgot your password?** form and its links, the user account in the edit form, `exp:resetuserpassword` (also with
  `--allow-root-user`), the web services, and any other code that stores a user (see
  [Every path that is closed](#every-path-that-is-closed)). Each refusal says, translated: "This installation is a
  public demo: the passwords of administrators cannot be changed or reset."
- **Every other account keeps every rule**: an editor or a visitor who registered changes and resets their own password
  as usual, and new passwords are still checked against the list of well-known ones.
- **The installers accept the demo password**: a well-known administrator password of any length, such as `publish`
  or `publishing`. Only the administrator password of a new installation is concerned; an empty one is still refused
  ([The installers accept the demo password](#the-installers-accept-the-demo-password)).
- **The administration shows a banner** on every page (admin4, admin4l, admin3 and the classic admin): "Demo mode. This
  installation is a public demo: anyone may know its administrator password, and administrator passwords cannot be
  changed or reset. Do not enter anything private here."
- **Every process records once that demo mode is on**: a line in `var/log/security.log` and the audit event
  `system.demo.mode` ([The banner and the log](#the-banner-and-the-log)).

Demo mode does not make an installation read-only, does not reset content, and does not restrict what an
administrator may do apart from the password. A demo that must undo what visitors do is rebuilt on a schedule (see
[The demo installer on the demo host](#the-demo-installer-on-the-demo-host)).

## Who counts as an administrator

An account counts as an administrator when its roles give it **unlimited access to every function of every module**:
a policy for all modules and all functions with no limitation, which is what the shipped role **Administrator** has.
That covers the account `admin` and every account in **Administrator users**, and any account that has been given
such a role directly.

An account whose roles cannot be read (a database error) counts as an administrator while demo mode is on: an error
never opens the password of an administrator. The check is `expDemoMode::isAdministrator()`.

An account with broad but limited access (an editor, a section administrator) is not protected. If a demo publishes
the sign-in of such an account too, give it a role that is not worth taking over, or rebuild the demo often.

## Every path that is closed

| Path | What happens in demo mode | Code |
|---|---|---|
| **Change password** (`user/password`), every design | the form says why and changes nothing; on admin and admin4 the page shows the reason before anything is typed | `kernel/private/classes/views/user/password.php` |
| **Forgot your password?** (`user/forgotpassword`), the request | no key and no mail for an administrator; the answer is the one every address gets, and the page says for every visitor that administrator passwords cannot be reset, so the form does not tell which addresses belong to an administrator | `kernel/private/classes/views/user/forgotpassword.php` |
| **Forgot your password?**, the link | a link of an administrator (sent before the mode was switched on) is refused and removed | same |
| The user account in **content/edit** (also the user's own profile) | a new password for an administrator is a validation error with the reason; the other fields can still be changed | `kernel/classes/datatypes/ezuser/ezusertype.php` |
| `exp:resetuserpassword` / `bin/php/resetuserpassword.php` | refused with the reason and exit code 1, also with `--allow-root-user` | `kernel/private/classes/commands/resetuserpassword.php` |
| Web services: `expaccount.reset`, `expaccount.changePassword`, `expuser.setPassword` | HTTP 403 with the reason | `extension/expservices/classes/users/expusersbase.php` |
| Web services: `expaccount.forgotRequest` | no key and no mail for an administrator; the same answer as for any address | `extension/expservices/classes/users/expaccountservices.php` |
| The operations `user/password` and `user/forgotpassword` (workflows, extensions) | status false with the reason `demo_mode` | `kernel/user/ezuseroperationcollection.php` |
| **Anything else that stores a user**: a CSV or package import, an extension, a script | `eZUser::store()` keeps the stored password of an administrator and stores everything else; the refusal is recorded | `kernel/classes/datatypes/ezuser/ezuser.php`, `expDemoMode::guardStore()` |

Every refusal is recorded in the audit as `access.user.password.change.failed` or
`access.user.password.reset.failed` with the reason `demo_mode` and the path ([audit.md](audit.md)), and as a
warning in the debug output.

Not affected: signing in (an existing password is never refused), and the hash upgrade at sign-in
(`[UserSettings] UpdateHash`), which stores the same password with a newer method.

## The installers accept the demo password

Every installer asks the same functions whether an administrator password may be installed:
`eZStepSiteAdmin::isWeakDefaultPassword()` (the well-known list) and `eZStepSiteAdmin::isTooShortAdminPassword()`
(`[UserSettings] MinPasswordLength`, 10 as shipped). That is the setup wizard, the kickstarter (`kickstart.ini`),
`exp:install` (`bin/php/install.php`) and every demo installer built on them. In demo mode both accept the demo
password. Exactly:

- **Relaxed:** the check against the list of well-known passwords (`expWeakPasswordList`, with its variants) and the
  minimum length, for the administrator password of a new installation only. The installation stores it inside
  `expDemoMode::installing()` and `expDemoMode::settingInstallationAdministratorPassword()`; only there does
  `eZUser::validatePassword()` let a password shorter than `MinPasswordLength` through.
- **Not relaxed:** an empty password is still refused (the kickstarter and `exp:install` generate one, the wizard asks
  again); every other account the installation creates, every password set after the installation and every form
  (`user/password`, registration, the user edit form, the console, the web services) keep `MinPasswordLength`, the
  `[PasswordSettings]` rules and the well-known list.

So a demo can install `publish` (seven characters, the classic) or `publishing` as it is, with the shipped settings.

The setup wizard says on its administrator page that demo mode is on and what it means. The installation's store of
the administrator password goes through the store guard because it runs inside `expDemoMode::installing()`.

## Why it cannot be switched on by accident

- **Environment only.** There is no INI setting, no request parameter, no cookie and no administration page for it. A
  settings file copied from a demo, a database restored from one, a package or a crafted request cannot switch it on.
  An HTTP header arrives as `HTTP_EXP_DEMO_MODE` and is never read.
- **Only the exact value `1`.** `0`, `true`, `yes`, `on`, an empty value and anything else leave it off.
- **Read on every call.** Nothing is cached, so a persistent worker (Exponential Velocity, FrankenPHP) answers for the
  environment it has now, and a compiled template asks again each time it runs.
- **Visible.** The banner on every administration page, the note in the setup wizard, the warning on the console
  (`Demo mode is on (EXP_DEMO_MODE=1) ...` on standard error) and the log line of every process.

The value is read with `getenv()` first (the process environment; under PHP-FPM also the FastCGI parameters), then
`$_SERVER` (Apache `SetEnv` with mod_php and similar SAPIs). A value in the process environment wins: `EXP_DEMO_MODE=0`
there keeps the mode off whatever `$_SERVER` says.

## Switching it on

Set the variable for **every** process of the demo: the web server's PHP, Exponential Velocity if it serves the site,
the cron jobs and every console command. A process without it runs with demo mode off.

| Where PHP runs | How |
|---|---|
| PHP-FPM | in the pool of the demo: `env[EXP_DEMO_MODE] = 1` (a pool with `clear_env = no` also passes the master's environment) |
| Apache with mod_php or proxying to PHP-FPM | `SetEnv EXP_DEMO_MODE 1` in the demo's virtual host (with PHP-FPM through `mod_proxy_fcgi` it reaches PHP as a FastCGI parameter) |
| Exponential Velocity | `export EXP_DEMO_MODE=1` in the demo's own **site envvars**, `/etc/vc/sites-available/<site>.envvars` (`<site>` is the name `./console exp:velocity layout` prints). `exp:velocity start` reads it for this installation's server only, so restarts and deploys from any shell keep it. The environment of the process that starts this installation's server works too (`Environment=EXP_DEMO_MODE=1` in its own systemd unit or a drop-in). **Never** `/etc/vc/envvars`: the server of every installation on the host reads it, and a Velocity worker refuses a demo mode value that comes from there ([Velocity: one installation only](#velocity-one-installation-only)) |
| FrankenPHP, RoadRunner | in the environment of the server process |
| Cron jobs and console commands | in the crontab (`EXP_DEMO_MODE=1` on its own line at the top) and in the shell of whoever runs commands |
| A container | `ENV EXP_DEMO_MODE=1` in the demo image, or `-e EXP_DEMO_MODE=1` / `environment:` for the demo service only |

Check it: the banner shows in the administration, and `grep 'demo/mode' var/log/security.log` lists the
processes that started with it.

**Velocity and restarts.** Put the variable in the demo's site envvars, beside its site file:

    # /etc/vc/sites-available/<site>.envvars   (<site>: see ./console exp:velocity layout)
    export EXP_DEMO_MODE=1

`exp:velocity start` reads that file at every start of this installation's server and passes what it sets to that
server only, so `exp:velocity restart` and `exp:velocity deploy` keep demo mode on whichever shell runs them.
`./console exp:velocity layout` lists the names it sets on the line `site envvars`. A variable already set in the
environment that starts the server is overridden by the site file. To end demo mode, remove the line and restart
Velocity. The file is read, never run, with the same rules as envvars: one `NAME=value` per line, and it cannot set
`PATH`, `LD_*`, `DYLD_*`, `MALLOC_*`, `HTTP_*`, `PHPRC`, `PHP_INI_SCAN_DIR`, `GLIBC_TUNABLES`, `HOME`, `USER` and the
other loader and shell variables (`expVelocityConfigLayout::ENV_DENIED`, the same list as the engine's). A restart made
with the engine's own `qbixctl` instead of `exp:velocity` does not read the site file: demo mode is then off until the
next `exp:velocity restart` (off is the safe direction: the site works, only visitors could change the password).

### Velocity: one installation only

The shared `/etc/vc/envvars` (or `/etc/qbix/envvars`) is read by the Velocity server of **every** installation on the
host, as `/etc/apache2/envvars` is by every virtual host. Demo mode set there would switch it on for production sites
too, whose administrators could then no longer change a password. So since this release:

- `exp:velocity start` never passes `EXP_DEMO_MODE` from the shared envvars (`expVelocityConfigLayout::SITE_ONLY_ENV`);
- a Velocity worker refuses a value the engine itself exported from an envvars file of a configuration directory
  outside the installation (`expDemoMode::isSharedEnvvarsValue()`, which reads the engine's own record
  `Q_WebServer_Layout::$exported` and the directories it read). Demo mode stays off, and the worker records once per
  process, in `var/log/security.log` and as a debug warning:
  `demo/refused pid=... EXP_DEMO_MODE=1 came from an envvars file other installations share ...`;
- a configuration tree inside the installation (`var/vc/qbix/etc`, used by an account without root) belongs to this
  installation alone, and its envvars is taken as before.

An installation started with demo mode on by an earlier release from `/etc/vc/envvars` has it **off** after the
upgrade's restart until the line moves to the site envvars. Move it before the restart:
`grep EXP_DEMO_MODE /etc/vc/envvars`, then put it in `/etc/vc/sites-available/<site>.envvars` of the demo only and
remove it from the shared file.

PHP-FPM, Apache, LiteSpeed, FrankenPHP and the console are not affected: each pool, virtual host or process already
has its own environment.

**Never on a site that is not a demo.** One site envvars file belongs to one installation; never copy a demo's to
another site.

**Never set it** in a shared PHP-FPM pool, a server-wide `SetEnv`, a global `/etc/environment`, a base container
image or a deployment template used for other sites.

## The banner and the log

- **Banner:** `design/admin4/templates/parts/exp_demo_mode_banner.tpl` (admin4, and admin4l through its fallback) and
  the same file in `design/admin` (the classic admin, and admin3 through its fallback). admin4 draws it at the top of the
  main area (`page_mainarea.tpl`, below the fixed header), admin4l also in front of the main zone of an admin layout,
  admin3 run as its own design at the top of its own main area (`design/admin3/templates/page_mainarea.tpl`), the
  classic admin below its header; always outside the cache-blocks, so it follows the environment of each request. It uses the shared component `.a4-notice.is-warn` of `components.css`
  (tokens with light and dark values; no CSS of its own). The template operator `exp_demo_mode()` returns whether the
  mode is on; a design of your own can show the banner with
  `{include uri='design:parts/exp_demo_mode_banner.tpl'}`.
- **Log:** at the start of the first web request (`ezpKernelWeb::requestInit()`) and of every script
  (`eZScript::initialize()`), each process writes one line to `var/log/security.log`, in the format of the request
  classifier's lines, so **Setup > Log files** groups it as `demo/mode`:

  ```
  [ Oct 19 2026 18:00:00 ] [203.0.x.x] demo/mode on pid=4711 sapi=fpm-fcgi EXP_DEMO_MODE=1: administrator passwords cannot be changed or reset, the installers accept a well-known administrator password; never use it in production
  ```

  and records the audit event `system.demo.mode` (severity warning, channel system, always on). A forked worker has
  its own pid and records it again; a persistent worker records it once. A web process that starts every request
  afresh (PHP-FPM, mod_php, LiteSpeed, PHP's built-in server) keeps an empty marker file per process in the cache
  directory (`<cache>/demo-mode/<pid>-<start time>`), so it too records once instead of on every request; clearing
  the caches lets the running processes record again. A console command also says
  `Demo mode is on (EXP_DEMO_MODE=1) ...` on standard error.

On a production site, a `system.demo.mode` event or a `demo/mode` line means that the variable reached a process it
should not have: remove it at once and check who changed the server's environment.

## Risks

- **Demo mode tells everyone that the administrator password is known.** Only run it on an installation whose
  content, users and settings are all throwaway: no real customer data, no real mail addresses, no secrets in
  settings, no connection to other systems.
- **Administrators keep every other power.** A visitor signed in as an administrator can create accounts, change
  roles, edit settings in the administration, install packages and upload files. Demo mode does not limit that. Rebuild
  the demo on a schedule, keep it on its own host or account, and give it no credentials that work elsewhere.
- **A well-known password is installed.** That is the purpose, and the reason the mode must never reach a real site.
- **Mail.** Visitors can trigger mail (registration, notifications). Send the demo's mail to a file or a sink, never
  to real addresses.
- **A lost variable switches it off silently.** A restart without the variable runs the demo with demo mode off:
  visitors can then change the administrator password again. Set it where it survives restarts, and watch for the
  banner.
- **Only administrators are protected.** Other published sign-ins (editor, member) can still be changed by whoever
  uses them.
- **A shared environment reaches every site in it.** A server-wide `SetEnv`, a shared PHP-FPM pool, `/etc/environment`,
  a base container image or the shell of an operator who exported the variable switches demo mode on for every
  installation that environment reaches; the administrators of those sites can then no longer change a compromised
  password. For Velocity the kernel refuses the shared `/etc/vc/envvars` itself
  ([Velocity: one installation only](#velocity-one-installation-only)); for the others, set it per installation only.
  Never run `exp:velocity start`, `exp:install` or the kickstarter for another site from a shell that has the variable.

## The demo installer on the demo host

The public demo is rebuilt from the current code every two hours by a script that runs `exp:install` with SQLite and
swaps the new site in. With demo mode it should set the variable twice:

1. **For the installation,** so `exp:install` installs the published demo password as it is, instead of replacing it
   with a random one and resetting it afterwards:

   ```sh
   EXP_DEMO_MODE=1 php bin/php/install.php --apply --db=sqlite --password='<the published demo password>' ...
   ```

   The reset step after the installation (`exp:resetuserpassword`) is then no longer needed, and in demo mode it is
   refused anyway. Any non-empty password works, also the seven-character `publish`.
2. **For the running site,** in the environment of the demo's PHP (its PHP-FPM pool, LiteSpeed's `lsphp`
   environment or the virtual host's `SetEnv`) and in the crontab of the demo's user, so the banner shows and the
   administrator password cannot be changed between two rebuilds. The preloader run after the swap
   (`bin/php/preload.php`) needs no variable.

The variable belongs only to the demo's own user and virtual host. The script must not write it into `config.php`,
`settings/` or anything else that is copied to other sites.

That is how the project's always-current demo runs since 19 October 2026, on LiteSpeed with `lsphp`: its build script
exports `EXP_DEMO_MODE=1` for every step, `exp:install` takes the published password as given, a generated password
fails the build (the old reset step is gone), and each build's `.htaccess` gets `SetEnv EXP_DEMO_MODE 1`;
`security.log` shows `demo/mode on ... sapi=litespeed`, and the sign-in and forgot-password pages show the notice. The
generic shape: [Recipe: run a public demo on LiteSpeed and Plesk](../../help/hosting/servers/run-a-public-demo-on-litespeed-and-plesk.md)
and [`install-demo-sqlite.sh`](../../examples/demo-mode/install-demo-sqlite.sh).

## For developers

- `expDemoMode::enabled()`: the mode is on (`EXP_DEMO_MODE` is exactly `1`); read on every call.
- `expDemoMode::protectsPassword( $user )`: the mode is on and the account (an `eZUser` or a content object id) is an
  administrator. Ask it before changing a password, and show `expDemoMode::refusalText()`.
- `expDemoMode::refused( $user, $path, $event )`: records a refusal (audit and debug output).
- `expDemoMode::installing( $callable )`: runs an installer's work that sets the administrator password; the store guard
  lets it through. The scope ends with the call, also on an exception. An installer of your own that sets the
  administrator password after the kernel's steps must use it, or its change is refused in demo mode.
- `expDemoMode::guardStore( $user )`: called by `eZUser::store()`; keeps an administrator's stored password while the
  mode is on. Code that writes `ezuser.password_hash` with its own SQL bypasses it: do not do that.
- The template operator `exp_demo_mode()` (`expDemoModeOperator`).
- Check the mode with `class_exists( 'expDemoMode' ) && expDemoMode::enabled()` in code that may run in a persistent
  worker started before the class existed.

## Compatibility

- Without `EXP_DEMO_MODE=1` nothing changes: no setting, no database change, no new default.
- New classes: `expDemoMode` (`kernel/classes/expdemomode.php`), `expDemoModeOperator`
  (`kernel/common/expdemomodeoperator.php`); regenerate the kernel autoloads when you update by copying files.
  Exponential Velocity loads classes when it starts: run `exp:velocity deploy --kernel` (or restart it) after the
  update.
- New audit event `system.demo.mode`; the existing events `access.user.password.change.failed` and
  `access.user.password.reset.failed` can have the reason `demo_mode`.
- New template variables (all optional, so an older design still renders): `demo_mode_locked` and `demo_mode_text`
  on `user/password.tpl`, `demo_mode_text` and `demo_mode_refused` on `user/forgotpassword.tpl`, `demo_mode` on
  `setup/init/site_admin.tpl`.
- The operations `user/password` and `user/forgotpassword` can return `status => false` with `reason => demo_mode`.
- The web services can answer HTTP 403 for a password change or reset of an administrator.
- Exponential Velocity: `EXP_DEMO_MODE` in the shared `/etc/vc/envvars` no longer switches demo mode on (it did for every
  installation on the host); use the site envvars `/etc/vc/sites-available/<site>.envvars`, read by `exp:velocity
  start`, or the environment that starts that installation's server. `exp:velocity layout` shows the line
  `site envvars`.
- New template `design/admin3/templates/page_mainarea.tpl` (the banner for admin3 run as its own design): clear the
  template-override cache after updating by copying files.

## Tests

- `tests/tests/kernel/classes/user/expDemoModeTest.php` (no database): the value rules, getenv before `$_SERVER` and
  never the request, no caching, who is an administrator, the store guard's decision, the `installing()` scope, the
  installers' relaxation and its limits, the once-per-process record, the template operator, the banner (one partial,
  shared component, no inline CSS, outside every cache-block of the admin, admin3, admin4 and admin4l pagelayouts),
  every password path, the audit catalogue and the catalogues, and (DM-13) that a Velocity value from a shared
  envvars file is not taken while one from a tree inside the installation is.
- `tests/tests/kernel/classes/velocitylayout/expVelocityConfigLayoutTest.php`: the shared envvars never passes
  `EXP_DEMO_MODE`; the site envvars of this installation does, wins over the shared file, and the site file of another
  installation is never read.
- `expAuditRecordTest` counts the new audit event.

## Where else demo mode is documented

Every page below says what it needs to about demo mode and links back here.

- **For each audience:** [Demo mode](../../help/demo-mode.md) (administrators), [Demo mode FAQ](../../help/demo-mode-faq.md),
  [Demo mode for hosts](../../help/hosting/security/demo-mode-for-hosts.md),
  [Demo mode for developers](../../help/extension-development/part-8-reference/demo-mode-for-developers.md).
- **Recipes and troubleshooting:** [Recipe: run a public demo on LiteSpeed and Plesk](../../help/hosting/servers/run-a-public-demo-on-litespeed-and-plesk.md),
  [Runbook: demo mode problems](../../help/hosting/runbooks/demo-mode-problems.md),
  [Demo mode examples](../../examples/demo-mode/README.md) (`.htaccess`, virtual host, PHP-FPM pool, nginx, systemd
  drop-in, crontab, container environment, a rebuild script), [Environment variables](../../guides/environment-variables.md).
- **Installation:** [the overview](../../INSTALL.md), [the chapters' index](../../install/README.md),
  [2 Requirements](../../install/02-requirements.md), [4 Choosing an install method](../../install/04-choosing-an-install-method.md),
  [5 Setup wizard](../../install/05-setup-wizard.md), [6 Kickstarter](../../install/06-kickstarter.md),
  [7 exp:install](../../install/07-console-install.md), [8 Serving the site](../../install/08-serving-the-site.md),
  [9 Databases](../../install/09-databases.md), [10 After installing](../../install/10-after-installing.md),
  [11 Upgrading](../../install/11-upgrading.md), [12 Troubleshooting](../../install/12-troubleshooting.md),
  [13 Security hardening](../../install/13-security-hardening.md), [Installing in one command](../../features/6.0/install-in-one-command.md).
- **Upgrading and settings:** [Upgrading](../../guides/upgrading.md), [Environment settings](../../features/6.0/environment-settings.md),
  `config.php-RECOMMENDED` and `config.env.php.example` (demo mode is not a constant),
  [Well-known passwords are refused](weak-passwords-and-reset-key-lifetime.md).
- **Users, passwords and roles:** [Changing your password](../../features/6.0/modern-password-change.md),
  [Reset a user password](../../features/6.0/reset-user-password.md), [Roles and policies](../../guides/roles-and-policies.md),
  [Change the administrator password](../../help/change-the-admin-password.md),
  [The default administrator password](../../help/default-administrator-password.md),
  [Accounts and sign-in](../../help/accounts-and-sign-in.md), [Logging into the admin](../../help/logging-into-the-admin.md).
- **Security, audit and logs:** [Audit](audit.md), [Security and audit](../../guides/security-and-audit.md),
  [Log viewer](../../features/6.0/log-viewer.md), [Security hardening](../../help/security-hardening.md),
  [Hardening for hosts](../../help/hosting/security/hardening-for-hosts.md),
  [Audit trail for hosts](../../help/hosting/security/audit-trail.md), [security.log](../../help/hosting/operations/security-log.md),
  [First sign-in and default passwords](../../help/hosting/security/first-sign-in-and-default-passwords.md).
- **Servers, panels and cron:** [Velocity engines](velocity-engines.md), [Velocity for hosts](../../help/hosting/servers/velocity-for-hosts.md),
  [LiteSpeed](../../help/hosting/servers/litespeed.md), [Apache with PHP-FPM](../../help/hosting/servers/apache-with-php-fpm.md),
  [nginx with PHP-FPM](../../help/hosting/servers/nginx-with-php-fpm.md), [FrankenPHP](../../help/hosting/servers/frankenphp.md),
  [Plesk](../../help/hosting/panels/plesk.md), [cPanel](../../help/hosting/panels/cpanel.md),
  [PHP selection and FPM pools](../../help/hosting/panels/php-selection-and-fpm-pools.md),
  [Cron and shell access](../../help/hosting/panels/cron-and-shell-access.md), [Cron jobs](../../help/hosting/operations/cron-jobs.md),
  [Cronjobs](../../guides/cronjobs.md), [Containers](../../help/hosting/models/containers-docker-and-podman.md),
  [SQLite for hosts](../../help/hosting/basics/sqlite-for-hosts.md), the hosting checklists
  ([new server](../../help/hosting/checklists/new-server.md), [go-live](../../help/hosting/checklists/new-customer-site-go-live.md),
  [security review](../../help/hosting/checklists/security-review.md)), and provisioning with
  [exp:install](../../help/hosting/provisioning/exp-install.md), [the kickstarter](../../help/hosting/provisioning/kickstarter.md)
  and [the setup wizard](../../help/hosting/provisioning/setup-wizard-on-shared-hosting.md).
- **Web services and developers:** [Services](backend_ezjscore_services.md), [REST API](../../guides/rest-api-v1-and-v2.md),
  [expservices](../../help/extension-development/part-8-reference/expservices.md),
  [eZUser and access](../../help/extension-development/part-8-reference/ezuser-and-access.md),
  [Console commands](../../help/extension-development/part-8-reference/console-commands-for-developers.md).
- **Glossaries:** [Glossary](../../glossary.md), [for hosts](../../help/hosting/glossary-for-hosts.md),
  [for extension developers](../../help/extension-development/part-8-reference/glossary-for-extension-developers.md),
  [for advocates](../../help/advocates/glossary-for-advocates.md), [for partners](../../help/partners/partner-glossary.md).
- **Press, advocates, trainers, partners and sales:** [Setting up a demo installation](../../help/press/setting-up-a-demo-installation.md),
  [Press: start here](../../help/press/press-start-here.md), [Live demo safety](../../help/advocates/live-demo-safety.md),
  [Advocates: start here](../../help/advocates/advocates-start-here.md), [Newcomer FAQ](../../help/advocates/newcomer-faq.md),
  [Trainers: start here](../../help/trainers/trainers-start-here.md), [Partners: start here](../../help/partners/partners-start-here.md),
  [Sales and marketing: start here](../../help/business/sales-and-marketing-start-here.md), [Demo scripts](../../help/business/demo-scripts.md),
  [Demo sites for designers](../../help/designers/distribution/demo-sites.md).

## Related pages

- [Demo mode](../../help/demo-mode.md) (administrators), [Demo mode for hosts](../../help/hosting/security/demo-mode-for-hosts.md),
  [Demo mode for developers](../../help/extension-development/part-8-reference/demo-mode-for-developers.md),
  [Demo mode FAQ](../../help/demo-mode-faq.md), [Demo mode problems](../../help/hosting/runbooks/demo-mode-problems.md)
- [Well-known passwords are refused](weak-passwords-and-reset-key-lifetime.md)
- [Chapter 13: Security hardening for production](../../install/13-security-hardening.md)
- [Setting up a demo installation](../../help/press/setting-up-a-demo-installation.md)
