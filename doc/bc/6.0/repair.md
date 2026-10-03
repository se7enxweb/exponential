# Repairing an installation whose libraries are missing

Read this page if your site shows "the libraries of this installation are missing", answers every page with HTTP 503,
or if you want to know what Exponential does when `vendor/` is gone. Without its Composer libraries the site cannot
start: every class from a library is "not found" (the first one is usually `ezcBaseOptions`). That happens when
`vendor/` was moved, renamed or deleted, when an installation was copied without it, or when `composer install` was
never run.

Exponential 6.0.15 recognises this state and shows its own page that explains what is wrong and how to fix it,
instead of the general error page. With a one-time repair key, an administrator can also repair the installation from
that page, in the browser.

## In short

| | |
|---|---|
| What changed | A dedicated HTTP 503 page for missing libraries, with an optional in-browser repair started by a one-time key. |
| Who is affected | Any installation whose `vendor/` is missing. Nothing changes while the libraries are present. |
| How to check | `ls vendor/autoload.php ../vendor/autoload.php` — if neither exists, the page is shown. |
| How to fix | Put `vendor/` back, or run `composer install`; or create a repair key and repair from the page. |

## Fix it in two minutes

**If `vendor/` was moved or renamed**, put it back in the installation directory. Done.

**Otherwise**, either install the libraries yourself:

```bash
composer install --no-dev          # in the installation directory; --no-dev on a production server
php bin/php/ezcache.php --clear-all --allow-root-user
```

(under Exponential Velocity, restart it afterwards), or repair from the browser:

```bash
sudo -u <web server user> php bin/php/exprepair.php --create-key
# Repair key (shown once, used once): AbC...
```

Open any page of the site, paste the key into the field and press Enter. The page runs the repair and opens the site
again when it is done.

## The page

Every page of the site answers **HTTP 503** with `Retry-After: 60` and is never cached, because the site is not
broken, only incompletely installed. The page shows:

- the Exponential logo, the explanation, and three steps for the administrator:
  1. If `vendor/` was moved or renamed, put it back in the installation directory.
  2. Otherwise install the libraries: in the installation directory, run `composer install` (with `--no-dev` on a
     production server).
  3. Then clear the caches (`php bin/php/ezcache.php --clear-all`) and, under Exponential Velocity, restart it.
- the repair panel (below), or how to set it up;
- the error reference (the same reference is in `var/log/error.log`), the technical detail when debug output is on,
  and the copyright footer.

The general error page (500 or 503 for other failures) is unchanged. The case is chosen from the installation's
state, not from the error message: Composer's autoloader is not loaded, and neither `vendor/autoload.php` nor
`../vendor/autoload.php` exists (`eZExecution::errorCase()`). So it also works with debug output off.

## Repairing from the browser

### Step 1: create a repair key, as the web server user

```bash
sudo -u <web server user> php bin/php/exprepair.php --create-key
# Repair key (shown once, used once): AbC...
```

The key is shown once. Only its hash is stored, in `settings/override/exprepair.ini.append.php` (mode 0640; never
commit the `settings/override/` tree). The command runs without the libraries, so you can use it while the site is
down.

### Step 2: start the repair on the page

Open any page of the site. The cursor is in the key field: paste the key and press Enter. The page then shows the
installer status bar:

- the three steps, each **waiting**, **running**, **done** or **failed**;
- a progress bar, the time elapsed, and the user the repair runs as;
- the end of the log, refreshed every two seconds;
- when it is done, the page opens again after five seconds (or with the button).

The steps are fixed:

| Step | Command |
|---|---|
| Install the Composer libraries | `composer install --no-dev --no-interaction --no-plugins --no-scripts --no-progress` |
| Regenerate the autoload arrays | `php bin/php/ezpgenerateautoloads.php -e`, then `-k` (with working copies inside the installation excluded) |
| Clear the caches | `php bin/php/ezcache.php --clear-all` |

`--no-plugins` means the installer plugin that places extension packages into `extension/` is not run, so nothing in
`extension/` is changed. Extensions that are git checkouts keep their working trees. The packages are installed into
`vendor/se7enxweb/` instead, where nothing loads them (Composer's autoloader maps only packages that declare
autoloading, such as the Velocity engine).

### Step 3: when the lock file does not match composer.json

If `composer.json` asks for versions that `composer.lock` does not hold, `composer install` refuses (exit code 4). The
status bar says so and offers **Update the lock file and install**. After a confirmation it runs
`composer update --no-dev --no-interaction --no-plugins --no-scripts --no-progress`, which writes a new
`composer.lock` and installs those versions, followed by the other two steps. This second run is authorised by the
failed run's token, within 15 minutes; no new key is needed.

To bring the lock file up to date without installing anything (for example in a repository, before a release):

```bash
sudo -u <web server user> composer update --no-install --no-plugins --no-scripts --no-dev
```

## How it is secured

- **The web request runs no command.** It checks the key, writes the job to `var/repair/status.json` and starts the
  worker in the background (`setsid php bin/php/exprepair.php --run`). The worker runs the fixed command list with
  `proc_open` and argument arrays; nothing from the request reaches a shell.
- **One-time key.** Starting a repair removes the key's hash; the next repair needs a new key. Five wrong keys in 15
  minutes lock the form for 15 minutes (`var/repair/failures.json`).
- **Starting works only while the libraries are missing.** Once `vendor/autoload.php` exists, a start request goes to
  the kernel like any other request.
- **Status and update need the run's token**, a random value returned when the run started.
- **One run at a time** (`var/repair/worker.lock`). A run that stops reporting for two minutes may be replaced by a new
  one.
- **The right owner for every file.** The worker runs as the owner of the installation (the owner and group of
  `index.php`, the web server user). Started as root, as under Exponential Velocity, it switches to that user
  (`posix_initgroups`, `posix_setgid`, `posix_setuid`) before anything else, and sets `HOME` for Composer.
- **No cron or service needed.** The page starts the worker itself.
- `var/repair/` is created with mode 0770 and an `.htaccess` that denies web access.

## Where the requests are answered

The page's requests carry `exp_repair`:

| Request | Answered by | When |
|---|---|---|
| `POST exp_repair=start`, `exp_repair_key` | `index.php`, before the kernel (`ezpRepairQueue::handleRunRequest()`) | only while the libraries are missing |
| `GET exp_repair=status&token=…` | `index.php`, before the kernel | any time, with the run's token |
| `POST exp_repair=update`, `token` | `index.php`, before the kernel | after a run failed on the lock file, within 15 minutes |

They are answered before `autoload.php` because the repair brings the libraries back halfway through: from then on the
kernel starts again, and its pages would replace the page's answers.

## Command line

Run these as the web server user. They are plain PHP and work while the libraries are missing.

```bash
php bin/php/exprepair.php --create-key   # a new one-time key (shown once)
php bin/php/exprepair.php --disable      # turn the page's repair off (removes the key)
php bin/php/exprepair.php --status       # the state of the last repair and the end of its log
php bin/php/exprepair.php --run          # run the queued repair (the page starts this itself)
```

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/override/exprepair.ini.append.php` | `RepairSettings` | `Enabled` | written as `true` by `--create-key` | installation |
| `settings/override/exprepair.ini.append.php` | `RepairSettings` | `KeyHash` | the `password_hash` of the key | installation |
| `settings/override/exprepair.ini.append.php` | `RepairSettings` | `Composer` | `composer` on the PATH | installation |
| `settings/error.ini` | `ErrorSettings` | `StaticErrorPage[dependencies]` | the built-in page | installation |

`--create-key` and `--disable` write the first file:

```ini
[RepairSettings]
Enabled=true
KeyHash=<password_hash of the key>
# optional: the composer binary to use (default: composer on the PATH)
Composer=/usr/local/bin/composer
```

To use a page of your own for the missing-libraries case, set in `settings/error.ini [ErrorSettings]`:

```ini
# placeholders: {status} {title} {message} {steps} {repair} {reference} {home} {detail}
StaticErrorPage[dependencies]=design/standard/errors/dependencies.html
```

## Files

| File | What |
|---|---|
| `lib/ezutils/classes/ezexecution.php` | `errorCase()`, `renderErrorCasePage()`: the page |
| `lib/ezutils/classes/ezprepairqueue.php` | `ezpRepairQueue`: key, queue, worker, the panel and its status bar |
| `bin/php/exprepair.php` | the command line, and the worker (`--run`) |
| `index.php` | answers the repair's requests before the kernel |
| `var/repair/` | `status.json`, `repair.log`, `worker.lock`, `failures.json` |

## Troubleshooting

| What you see | Why | What to do |
|---|---|---|
| "To repair from this page, create a one-time repair key" | No key is set up, or the last repair used it. | `php bin/php/exprepair.php --create-key` |
| "No repair key is set up on the server." | The key was used by a repair that already started. | Create a new key; `--status` shows that run. |
| "That is not the repair key." | A used or mistyped key. | Create a new key. Five wrong keys lock the form for 15 minutes. |
| Step 1 failed; the log says the lock file does not satisfy composer.json | `composer.lock` is out of date. | Press **Update the lock file and install**, or update the lock file on the command line. |
| Step 1 failed with "composer: not found" | Composer is not on the web server user's PATH. | Set `Composer=/path/to/composer` in the settings file. |
| The steps never leave "waiting" | The worker could not start (PHP binary, `exec` disabled). | Run `php bin/php/exprepair.php --run` as the web server user; check `var/repair/worker.out`. |
| Velocity still fails after a repair | Its workers loaded the old state. | `./console exp:velocity restart --allow-root-user` |

## Related pages

- [Repair from the browser](../../features/6.0/repair-from-the-browser.md)
- [Behaviour changes of 1 and 2 October 2026](behaviour-changes-2026-10.md)
- [Velocity engines](velocity-engines.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
