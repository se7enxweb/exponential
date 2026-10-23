# Environment variables

This page lists the environment variables Exponential 6 reads, what each one does and who sets it. It is for
administrators, hosts and developers who configure a server, a container or a deployment pipeline.

Exponential is configured through its INI files (`settings/`) and, for a few things that must be known before the
settings are read, through constants in `config.php` and `config.env.php` (`EXP_ENV`, `EZP_INI_FILEMTIME_CHECK` and
the like). Environment variables are used where the setting must belong to **one server process** and must never
travel with a copy of the settings: demo mode, the engine archive, passwords handed to an installer, and values one
command passes to the processes it starts.

PHP reads an environment variable with `getenv()`. Under PHP-FPM that also returns the FastCGI parameters of the
request (`env[...]` in the pool, `SetEnv` in Apache with `mod_proxy_fcgi`, `fastcgi_param` in nginx); under Apache with
mod_php and LiteSpeed with `lsphp` a `SetEnv` arrives the same way. Persistent workers (Exponential Velocity,
FrankenPHP) inherit the environment of the server process when it starts.

## Contents

1. [Variables you set](#variables-you-set)
2. [Variables Exponential sets for its own processes](#variables-exponential-sets-for-its-own-processes)
3. [Constants that are not environment variables](#constants-that-are-not-environment-variables)
4. [Where to set an environment variable](#where-to-set-an-environment-variable)
5. [Related pages](#related-pages)

## Variables you set

| Variable | Values | What it does | Read by |
|---|---|---|---|
| `EXP_DEMO_MODE` | `1` (exactly) | **Demo mode** for a public demo: administrator passwords cannot be changed or reset by any path, the installers accept a well-known or short administrator password (`publish`, `publishing`; never an empty one), the administration shows a banner, and each process records `demo/mode` in `security.log` and `system.demo.mode` in the audit. Any other value, or none, leaves it off. Read on every call (`getenv()`, then `$_SERVER`); never from an INI file or a request. Under Velocity never from the shared `/etc/vc/envvars` (use the site envvars). Never set it for a real site. [Demo mode](../bc/6.0/demo-mode.md) | `expDemoMode` (`kernel/classes/expdemomode.php`) |
| `EXP_ENGINE_PHAR` | the path of an engine archive | Runs the kernel and library classes from that archive instead of the files on disk (only when the file exists). `exp:velocity` sets it for the servers it starts when an archive is configured. [Velocity engines](../bc/6.0/velocity-engines.md) | `autoload.php`, `expVelocity` |
| `EXP_INSTALL_DB_PASSWORD` | a password | The database password for `exp:install` when `--db-password` is not given, so it does not appear in the process list or the shell history. [Chapter 7](../install/07-console-install.md) | `bin/php/install.php` |
| `EXP_KICKSTART_LOG` | `0` | `0` turns off `var/log/kickstart.log` of `exp:kickstarter run`. [Chapter 6](../install/06-kickstarter.md) | the kickstarter |
| `EXP_BENCHMARK_AUTH` | `user:password` | HTTP basic authentication for `exp:benchmark` when `--auth` is not given | the benchmark command |
| `EXP_MONGO_TRACE` | a file path | Writes every MongoDB statement to that file before it runs, to find a statement that never returns. Off and free when unset | the MongoDB driver (`lib/ezdb/classes/expmongodb.php`) |
| `EXP_SETUP_LOG_DIR` | a directory | Where `setup.log` and the web setup's state are kept instead of `var/log` (the tests use it so they never rotate a real installation's log) | `expSetupLog` |
| `PHP_CLI_SERVER_WORKERS` | a number | PHP's own variable: the workers of PHP's built-in web server, which Velocity's `php` engine uses | PHP; reported by **Setup > System information** |

## Variables Exponential sets for its own processes

These are set by one Exponential command for a process it starts. Do not set them yourself.

| Variable | Set by | Meaning |
|---|---|---|
| `EXP_SETUP_RUN` | the setup, for the commands it runs | the commands work with the settings as they are before the installation; the audit does not look the user up |
| `EXP_KICKSTART_LOG_CHILD` | `exp:kickstarter run` | the run is the logged child process |
| `EXP_PRELOAD_DETACHED`, `EXP_PRELOAD_ERRORS` | the preloader's background job | the job runs detached; where its errors go |
| `EXP_PHAR_REEXEC` | `exp:phar build` | the build re-ran itself with `phar.readonly` off |
| `EXP_VELOCITY_ENGINE`, `EXP_VELOCITY_ROOT`, `EXP_VELOCITY_STATIC_PATHS`, `EXP_VELOCITY_NEVER_STATIC`, `EXP_VELOCITY_FOLLOW_SYMLINKS` | `exp:velocity` for the `php` and `frankenphp` engines | the engine's name, the document root and which files are served as they are |

## Constants that are not environment variables

`EXP_ENV` (the environment name, `config.env.php`), `EZP_INI_FILEMTIME_CHECK`, `EZP_DIR_MODE_MAX`,
`EZP_FILE_MODE_MAX` and the other `EZP_*` / `EXP_*` names in `config.php-RECOMMENDED` and `config.env.php.example` are
PHP constants, defined in those files. Setting an environment variable of the same name does nothing. The reverse
holds for `EXP_DEMO_MODE`: defining a constant of that name in `config.php` does nothing; demo mode comes only from the
environment.

## Where to set an environment variable

| Where PHP runs | How |
|---|---|
| Apache (mod_php, or PHP-FPM through `mod_proxy_fcgi`) | `SetEnv NAME value` in the virtual host or the site's `.htaccess` (`mod_env`) |
| LiteSpeed / OpenLiteSpeed with `lsphp` | `SetEnv` in `.htaccess` (LiteSpeed reads it like Apache), or the virtual host's environment |
| PHP-FPM | `env[NAME] = value` in the site's pool, then reload that pool's master |
| nginx with PHP-FPM | `fastcgi_param NAME value;` where requests are passed to PHP-FPM, or the pool |
| Exponential Velocity | `export NAME=value` in `/etc/vc/envvars`: the server reads the file at every start and exports what it sets into its own environment before it forks, so every worker sees it and a restart or deploy from any shell keeps it. The environment of the process that runs `exp:velocity start` (`Environment=` in its systemd unit or a drop-in, or `export` in the shell) wins over the file. `/etc/vc/envvars` is read by the server of every installation on the host; a variable for one installation only goes in its site envvars, `/etc/vc/sites-available/<site>.envvars`, which `exp:velocity start` passes to that installation's server only and which wins over both. `EXP_DEMO_MODE` is taken only from there (or the start environment): a worker refuses it from the shared file. The files are read, never run, and refuses `PATH`, `LD_*`, `DYLD_*`, `MALLOC_*`, `HTTP_*`, `PHPRC`, `PHP_INI_SCAN_DIR`, `GLIBC_TUNABLES`, `HOME`, `USER` and the other names listed in [the on-disk layout](../bc/6.0/velocity-ondisk-layout.md) |
| cron | a `NAME=value` line at the top of the crontab |
| console | `export NAME=value`, or `NAME=value command` for one command |
| containers | `ENV` in the image, `-e`, `--env-file` or `environment:` / `env_file:` for one service |

Examples for demo mode, one per place: [Demo mode examples](../examples/demo-mode/README.md).

## Related pages

- [Demo mode](../bc/6.0/demo-mode.md), [Demo mode FAQ](../help/demo-mode-faq.md)
- [Chapter 2: Requirements](../install/02-requirements.md), [Chapter 13: Security hardening](../install/13-security-hardening.md)
- [Velocity engines](../bc/6.0/velocity-engines.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
