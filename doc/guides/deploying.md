# Deploying Exponential: Apache with PHP-FPM, Velocity or FrankenPHP

This guide takes a freshly installed Exponential site to a server visitors can reach, over HTTPS, and shows how to ship a code change safely and keep the site fast. Read it top to bottom. Every command exists in the installation; where a result is shown, it is what the command prints.

You need: a Linux server, shell access in the installation directory (the folder that holds `index.php` and `console`), and a site that already runs. If you have no site yet, run `./console exp:install` first ([Installing in one command](../features/6.0/install-in-one-command.md)).

Commands use `./console`, the shortcut for `bin/php/console`. The examples add `--allow-root-user` because they are run as root; leave it off when you run as the site user.

## 1. Choose how the site is served

Exponential runs behind one of three setups. Pick one for now; you can switch later without touching content.

| Setup | Choose it when | Time to a running site |
|---|---|---|
| Apache (or nginx) with PHP-FPM | the server already has a web server and a hosting panel, or you want the classic setup | 10 minutes |
| Exponential Velocity (the `qbix` engine) | you want the fastest engine, persistent workers, a built-in response cache and no web server to configure | 2 minutes |
| FrankenPHP | you want one verified binary with TLS and HTTP/2 and no PHP on the machine to maintain | 5 minutes |

Velocity is the recommended engine for every stage. The three can run side by side on different ports, so trying one never takes the others down ([engines compared](../bc/6.0/velocity-engines.md)).

## 2. Check the machine before you start

```bash
php -v | head -1
./console exp:velocity ext check --allow-root-user
```

The first line shows the PHP version (8.1 or newer). The second lists every PHP extension the installation wants, marks what is missing and prints the install command for your system, for example `sudo dnf install -y php-odbc`. Install what it reports for the features you use, then restart PHP-FPM or the server so the extensions load. Missing optional extensions do not stop the site.

## 3. Option A: Apache with PHP-FPM

### 3.1 Point the web server at the installation

The document root is the installation directory itself. Exponential keeps its own rewrite rules in a file you enable by copying:

```bash
cp .htaccess_root .htaccess
```

The copy turns on `.htaccess` based virtual host mode: dot files, `.git` and scripts below the front controllers answer 404, `/api/` goes to `index_rest.php`, static design files are served directly and everything else goes to `index.php`. Do not edit security rules out of it to make something work ([security defaults](../specifications/6.0/security-defaults-2026-09.md)).

A minimal Apache virtual host for PHP-FPM (adjust the paths and the socket to your system):

```apache
<VirtualHost *:80>
    ServerName example.com
    DocumentRoot /var/www/example.com
    DirectoryIndex index.php

    <Directory /var/www/example.com>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>
```

`AllowOverride All` is what lets Apache read the `.htaccess` you just created, and `mod_rewrite`, `mod_proxy` and `mod_proxy_fcgi` must be loaded. Check and reload:

```bash
apachectl configtest      # Syntax OK
systemctl reload apache2  # or httpd on Red Hat style systems
```

### 3.2 Test it

```bash
curl -sI http://example.com/ | head -1
```

Expect `HTTP/1.1 200 OK` (or a redirect to the first page). If you get 500, open the PHP-FPM and Apache error logs; if you get 404 for every page, `.htaccess` is not being read (`AllowOverride`) or `mod_rewrite` is off.

### 3.3 HTTPS

Obtain a certificate with your usual tool (certbot, your hosting panel) and add a `*:443` virtual host with `SSLEngine on`, `SSLCertificateFile` and `SSLCertificateKeyFile`, the same `Directory` block and the same `SetHandler`. Test with `curl -sI https://example.com/ | head -1`.

### 3.4 After every PHP change: reload the right PHP-FPM

On a server with several PHP versions, the FPM master that serves your site is not always the one named `php-fpm`. On a Plesk server, for example, `systemctl restart php-fpm` restarts a PHP no site uses. `exp:velocity deploy` (section 7) finds the right one for you; the setting is `[DeploySettings] PhpFpmService`:

```bash
./console exp:velocity config get DeploySettings PhpFpmService --allow-root-user
```

It prints `auto`, which looks for the pool named after the installation's hosting domain and reloads its service.

## 4. Option B: Exponential Velocity (the `qbix` engine)

Velocity is a PHP application server that ships with Exponential. It needs no Apache, nginx or FPM.

### 4.1 Select the engine and start it

```bash
./console exp:velocity config set ServerSettings Engine qbix --allow-root-user
./console exp:velocity start --allow-root-user
./console exp:velocity status --allow-root-user
```

`config set` writes `settings/override/velocity.ini.append.php`, the one file for this installation's settings (the shipped defaults stay in `settings/velocity.ini`). `status` lists the addresses it serves, the number of processes, whether HTTPS is on and where the logs are:

```
Process    52 processes, parent ... · port 8088, 8080
HTTPS      on, port 8080
Access     var/vc/qbix/log/site-access.log
Errors     var/vc/qbix/log/site-error.log
```

Plain HTTP is on `Port` (8088 shipped), HTTPS on `HTTPSPort` (8080 shipped), both bound to `Host` (`127.0.0.1` shipped). Open `http://127.0.0.1:8088/` on the same machine. To serve the network, set `Host`:

```bash
./console exp:velocity config set ServerSettings Host 0.0.0.0 --allow-root-user
./console exp:velocity restart --allow-root-user
```

Ports below 1024 need root; the usual setup is Velocity on its own ports behind a hosting panel or a firewall rule, or a front web server proxying to it.

### 4.2 Stop, restart, reload without dropping requests

```bash
./console exp:velocity graceful --allow-root-user    # re-exec, listening socket kept
./console exp:velocity restart --allow-root-user
./console exp:velocity stop --allow-root-user
```

### 4.3 Look around

```bash
./console exp:velocity layout --allow-root-user      # every file the server uses
./console exp:velocity config list ServerSettings --allow-root-user
```

`layout` shows the configuration tree in the style of Apache on Debian (`/etc/vc` with `sites-available`, `sites-enabled`, `conf-available` and so on). Sites, snippets and modules are switched on and off like `a2ensite`: `./console exp:velocity site enable <name> --allow-root-user` ([on-disk layout](../bc/6.0/velocity-ondisk-layout.md)). The server also has a control panel ([Velocity control panel](../features/6.0/velocity-control-panel.md)).

Depth: [Velocity web server](../features/6.0/velocity-web-server.md), [persistent workers](../features/6.0/velocity-persistent-worker-server.md), [worker pool](../specifications/6.0/velocity-worker-pool.md), [engine settings](../specifications/6.0/velocity-engine-settings.md).

## 5. Option C: FrankenPHP

FrankenPHP is the Caddy web server with PHP built in, as one file. Exponential downloads a pinned release and checks its SHA-256 before running it.

```bash
./console exp:velocity install --engine=frankenphp --allow-root-user
./console exp:velocity start --engine=frankenphp --allow-root-user
./console exp:velocity status --engine=frankenphp --allow-root-user
```

By default it serves plain HTTP on port 8089 and HTTPS on 8444, so `https://` works from the first start (a self-signed certificate stands in until you give it a real one). Useful variants:

```bash
./console exp:velocity start --engine=frankenphp --no-https --allow-root-user   # plain HTTP, this start only
./console exp:velocity install --engine=frankenphp --check --allow-root-user    # re-hash the installed binary
```

PHP runs in classic mode (clean state per request, as under PHP-FPM). The engine has no response cache yet; if you rely on one, use Velocity. Two things bite on a first install: the embedded PHP has no machine `php.ini`, so set the database socket under `[PHPSettings] IniOptions[]` if your database is reached over a socket, and a re-published release can make the pinned checksum stale. The complete tested walk-through, with both fixes, is [FrankenPHP as the Exponential web engine](../bc/6.0/frankenphp.md).

To run all three engines side by side, give each its own port and use `--engine=<name>` or `--all` on the control commands.

## 6. HTTPS

| Setup | How |
|---|---|
| Apache | the certificate in the `*:443` virtual host (section 3.3) |
| Velocity | `[HTTPSSettings]` in `velocity.ini` and the certificate subsystem |
| FrankenPHP | on by default; `[HTTPSSettings] Certificate` and `Key` supply your own |

For Velocity with a certificate you already have (a PEM file with certificate and key; both settings may name the same file):

```bash
./console exp:velocity config set HTTPSSettings Certificate /etc/ssl/example.com/fullchain.pem --allow-root-user
./console exp:velocity config set HTTPSSettings Key /etc/ssl/example.com/privkey.pem --allow-root-user
./console exp:velocity config set HTTPSSettings Enabled true --allow-root-user
./console exp:velocity restart --allow-root-user
curl -skI https://127.0.0.1:8080/ | head -1
```

Expect `HTTP/2 200` (or a redirect). The Velocity server can also make a self-signed certificate on its own, obtain and renew Let's Encrypt certificates and swap a renewed certificate in without a restart: [Velocity HTTPS and certificates](../features/6.0/velocity-https-certificates.md).

Strict-Transport-Security is sent on HTTPS answers with `[HTTPSSettings] HSTSMaxAge` (300 seconds shipped). Raise it to a year (`31536000`) only when every name of the site is served over HTTPS for good: a browser remembers the value even after you lower it ([HTTP/2 and security](../specifications/6.0/velocity-http2-and-security.md)).

## 7. Ship a PHP change with one command

After you change a PHP class, an operator or an INI file, run:

```bash
./console exp:velocity deploy --dry-run --allow-root-user   # what it would do, nothing else
./console exp:velocity deploy --allow-root-user             # the usual case
./console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

The dry run prints the thirteen steps, each marked `DRY`:

```
[ 1/13] DRY   extension autoloads: php bin/php/ezpgenerateautoloads.php -e -q
[ 2/13] DRY   INI caches: php bin/php/ezcache.php --clear-tag=ini --allow-root-user
...
[ 8/13] DRY   reload PHP-FPM: systemctl reload <your pool's service>
[ 9/13] DRY   restart Velocity: php bin/php/velocity.php restart
[10/13] DRY   content cache ...
[13/13] DRY   Velocity response cache: exp:velocity cache clear
```

A real run prints PASS, FAIL or SKIP and the time for each step, and stops at the first failure with exit status 1. The order matters: the caches that hold finished pages are cleared last, after PHP-FPM and Velocity run the new code, so a page rendered by old code is never cached again. Options: `--no-fpm` (Velocity only), `--no-velocity` (Apache only), `--no-autoload`, `--rebuild-phar`, `--packer` (also clear packed scripts and styles, when an `ezjscServer_*` function changed). Details and every step: [Deploying a PHP change](../bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy).

Edits to a template or a stylesheet need less: clear the caches without a restart.

```bash
./console exp:cache clear --id=template --allow-root-user
./console exp:velocity cache clear --allow-root-user
```

Never run only `exp:cache clear --all` and then forget the web server: FPM and Velocity keep the old classes in memory until they are reloaded.

## 8. Make it fast

Work down this list; each item is a switch or a command that exists, and `exp:cache status` shows where you stand.

```bash
./console exp:cache status --allow-root-user
```

It prints one line per layer, for example `httpcache  the HTTP cache is on: ... entries`, `querycache  the SQL query cache is on`, `velocity  response cache ...: N files`, and `php  OPcache not enabled for this server` when the opcode cache is off for the command line (normal; it matters for the web process).

1. **Opcode cache.** On PHP-FPM keep `opcache.enable=1` in the pool's PHP configuration. Velocity passes its own opcode cache options to the server process; the shipped values are in `[PHPSettings]` of `settings/velocity.ini`. Measured effect and how to profile: [Velocity and the opcode cache](../features/6.0/velocity-opcode-cache-and-profile.md).
2. **Response cache (Velocity).** `[CacheSettings] Enabled=enabled`, `DefaultTtl=30` seconds. A page served from it never reaches PHP. Clear it after a template change with `./console exp:velocity cache clear --allow-root-user`. See [Velocity response cache](../features/6.0/velocity-response-cache.md).
3. **HTTP cache.** The role-aware page cache for any engine: `./console exp:cache httpcache status --allow-root-user` ([HTTP cache](../bc/6.0/httpcache.md)).
4. **SQL query cache.** On by default; `./console exp:cache querycache status --allow-root-user` ([SQL query cache](../bc/6.0/sql-query-cache.md)).
5. **Static files.** Velocity serves them with a long `StaticMaxAge` and can precompress them (`[ServerSettings] StaticMaxAge`, `PrecompressStatic`) ([static files and images](../features/6.0/velocity-static-files-and-images.md)).
6. **Static cache.** Whole pages written as files for Apache to serve; shipped off in the installer. `exp:cache static status` says whether it is on ([static cache generator](../features/6.0/static-cache-generator.md), [defaults](../bc/6.0/static-cache-defaults.md)).
7. **Workers.** `[ServerSettings] Workers` (4 shipped) is the Velocity process count; raise it with the number of CPU cores and measure again after each change.

Measure with the same request before and after; a single page load proves nothing:

```bash
curl -s -o /dev/null -w '%{time_total}s\n' http://127.0.0.1:8088/
```

Run it three times: the first request fills the caches, the others show the cached time.

## 9. Check that you are done

- `./console exp:velocity status --allow-root-user` (Velocity) or `curl -sI` on your address (Apache) answers 200.
- The browser shows the padlock on the HTTPS address.
- `./console exp:velocity deploy --dry-run --allow-root-user` lists a `reload PHP-FPM` step with the right service.
- `./console exp:cache status --allow-root-user` shows the layers you expect.

## Where to go next

- Features: [Velocity web server](../features/6.0/velocity-web-server.md), [Velocity HTTPS and certificates](../features/6.0/velocity-https-certificates.md), [Installing in one command](../features/6.0/install-in-one-command.md).
- Specifications: [Velocity engine settings](../specifications/6.0/velocity-engine-settings.md), [worker pool](../specifications/6.0/velocity-worker-pool.md), [HTTP/2 and security](../specifications/6.0/velocity-http2-and-security.md).
- Upgrade notes: [Velocity engines](../bc/6.0/velocity-engines.md), [FrankenPHP](../bc/6.0/frankenphp.md), [Velocity engine upgrade notes](../bc/6.0/velocity-engine-upgrade-notes.md), [cache console](../bc/6.0/cache-console.md).
- History: [Velocity chronicle](../history/velocity/2026-09a.md), [October 2026](../history/2026/2026-10.md).
