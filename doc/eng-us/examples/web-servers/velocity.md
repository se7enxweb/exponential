---
title: Velocity
audience: admin, developer
category: examples
order: 15
summary: Exponential Velocity serving a public site on ports 80 and 443 with no separate web server: the velocity.ini override, the generated /etc/vc site file, enabling sites, conf snippets and modules, a systemd unit and deploying a change.
keywords: Velocity, vc, exp:velocity, qbix, engine, velocity.ini, settings/override, /etc/vc, sites-available, sites-enabled, conf-available, mods-available, HTTPS, Let's Encrypt, HSTS, workers, response cache, systemd, deploy, example
type: example
---
# Velocity

**Exponential Velocity** is the application server that ships for Exponential and the recommended way to serve it at
every stage. With its `qbix` engine **no separate web server and no separate HTTPS server is needed**: Velocity
listens on the HTTP and HTTPS ports itself, keeps the application loaded in persistent workers, answers repeat pages
from its own response cache and manages its certificates. Its routing is `.htaccess_root`'s, built in
(`expVelocity::STATIC_PATHS`, `FRONT_CONTROLLERS`, `ENTRY_SCRIPTS`), so there are no rewrite rules to copy. The list
also serves the icon themes of extensions (`extension/<name>/icons/`, SVG, PNG and GIF images with exactly one
extension); after an upgrade it takes effect with the restart that `exp:velocity deploy` does.

Everything is configured in `velocity.ini`; the `/etc/vc` tree is generated from it on every start.

## 1. The settings

[`velocity.ini.append.php.example`](velocity.ini.append.php.example) is an override for a public site on the standard
ports. Every setting in it exists in `settings/velocity.ini`, which describes each one.

```ini
<?php /* #?ini charset="utf-8"?
# Exponential 6: Velocity serving a public site on ports 80 and 443
#
# Free documentation: GFDL 1.3 or later, see doc/eng-us/README-LICENSE.md.
#
# Copy to settings/override/velocity.ini.append.php (drop ".example"), or
# set each value with "exp:velocity config set <Block> <Setting> <value>",
# which writes the same file. Every setting is described in
# settings/velocity.ini; only what differs from the shipped value goes here.
# Explained in velocity.md next to this file.
#
# After a change: php bin/php/console exp:velocity restart --allow-root-user

[ServerSettings]
# qbix: the recommended engine (persistent workers, response cache, TLS,
# HTTP/2, the /etc/vc tree). The shipped default is php (development only).
Engine=qbix
# Every address, the standard ports. The shipped 127.0.0.1:8088 keeps the
# server reachable only locally or through a reverse proxy.
Host=0.0.0.0
Port=80
HTTPSPort=443
# Started as root (needed for ports below 1024), the workers become this
# user and group, the owner of the installation's files
User=example
Group=example
# Worker processes; SpareWorkers adds a dynamic pool for peaks
Workers=8
SpareWorkers=8

[HTTPSSettings]
# HTTPS is on only when Enabled is true AND both files exist
Enabled=true
Certificate=/etc/ssl/example.com/fullchain.pem
Key=/etc/ssl/example.com/privkey.pem
# Start short; raise to 31536000 once every name is served over HTTPS for good
HSTSMaxAge=300

[CacheSettings]
# The response cache for anonymous visitors (seconds a page is kept)
Enabled=enabled
DefaultTtl=30

[LayoutSettings]
# The name of the site file in /etc/vc/sites-available; empty: the host of
# site.ini [SiteSettings] SiteURL
SiteName=example.com
*/ ?>
```

Install it as `settings/override/velocity.ini.append.php` (merge it into an existing one), or set the values one by
one, which writes the same file:

```bash
php bin/php/console exp:velocity config set ServerSettings Engine qbix --allow-root-user
php bin/php/console exp:velocity config set ServerSettings Host 0.0.0.0 --allow-root-user
php bin/php/console exp:velocity config set ServerSettings Port 80 --allow-root-user
php bin/php/console exp:velocity config set ServerSettings HTTPSPort 443 --allow-root-user
php bin/php/console exp:velocity config set ServerSettings User example --allow-root-user
php bin/php/console exp:velocity config set ServerSettings Group example --allow-root-user
php bin/php/console exp:velocity config set HTTPSSettings Certificate /etc/ssl/example.com/fullchain.pem --allow-root-user
php bin/php/console exp:velocity config set HTTPSSettings Key /etc/ssl/example.com/privkey.pem --allow-root-user
php bin/php/console exp:velocity config set HTTPSSettings Enabled true --allow-root-user
php bin/php/console exp:velocity config list --allow-root-user      # * marks what you set
php bin/php/console exp:velocity restart --allow-root-user
php bin/php/console exp:velocity status --allow-root-user
```

Notes:

- **The file is PHP-wrapped** (`<?php /* ... */ ?>`) like every override, so it never prints its content if requested.
  The example in this directory ends in `.example` so it is never a PHP file in the documentation tree.
- **Ports below 1024 need root.** Started as root, the parent keeps root (it binds the ports and reads the key) and
  every worker drops to `User` and `Group` before any application code runs.
- **HTTPS** is on only when `Enabled` is true *and* both files exist; a certificate that fails its checks is replaced by
  a self-signed stand-in rather than shown. A renewed certificate is picked up within a minute, without a restart. For
  Let's Encrypt run by Velocity itself, see `exp:velocity ssl` in
  [chapter 8, section 8.3.9](../../install/08-serving-the-site.md#839-https-served-by-velocity-itself).
- **HSTS** starts at 300 seconds; raise `HSTSMaxAge` to a year only once every name is served over HTTPS for good.
- **Headers.** `[ServerSettings] ResponseHeaders[]` ships the kernel's default security headers for what Velocity
  answers itself, and `ResponseHeadersOnScripts` adds them to pages that lack them: nothing to configure.

## 2. The /etc/vc tree

Velocity's configuration on disk mirrors Debian's `/etc/apache2`:

```
/etc/vc/
├── vc.conf                   settings shared by every site        (apache2.conf)
├── ports.conf                listen ports                         (ports.conf)
├── envvars                   environment of the server process    (envvars)
├── conf-available/*.conf     shared snippets                      -> conf-enabled/  (symlinks)
├── mods-available/*.conf     engine modules: http2, cache, ...    -> mods-enabled/  (symlinks)
├── sites-available/*.conf    one file per installation            -> sites-enabled/ (symlinks)
├── designs/                  the server's own page designs
└── ssl/                      certificates
```

`exp:velocity start` and `restart` write `ports.conf`, the module files it manages and
`sites-available/<SiteName>.conf` (mode `0600`) from `velocity.ini`. A generated site file has this shape (paths
shortened to an example installation, the static path list cut):

```json
{
    "_generated": "exp:velocity",
    "Q": {
        "web": {
            "static": {
                "paths": [
                    "^/(design/[^/]+/(stylesheets|images|javascript|fonts)/|share/icons/|...|w3c/p3p\\.xml$)"
                ]
            },
            "cache": {
                "dir": "/var/www/example.com/var/cache/qbix-reverse"
            }
        },
        "webserver": {
            "scripts": ["/index.php", "/index_rest.php", "/index_treemenu.php"],
            "frontControllers": {
                "^/(api/|index_rest\\.php)": "index_rest.php",
                "^/([^/]+/)?content/treemenu": "index_treemenu.php"
            },
            "log": {
                "dir": "/var/www/example.com/var/vc/qbix/log"
            },
            "headers": {
                "X-Content-Type-Options": "nosniff",
                "X-Frame-Options": "SAMEORIGIN",
                "Referrer-Policy": "strict-origin-when-cross-origin",
                "Permissions-Policy": "camera=(), microphone=(), payment=(), usb=()"
            },
            "headersOnScripts": true,
            "hsts": {
                "maxAge": 300
            }
        }
    }
}
```

`scripts` lists every script the server runs. The SOAP server (`index_soap.php`, called `soap.php` before 6.0.15) is
not on it, and was not before either: serve SOAP through the Apache or nginx front end of the installation (see
[the SOAP entry script is now index_soap.php](../../bc/6.0/soap-entry-script-index-soap.md)).

Do not edit a generated file: change `velocity.ini` and restart. To keep a file of your own, remove its
`"_generated"` marker; it is then never overwritten, like a dpkg conffile. Enable and disable like `a2ensite`,
`a2enconf` and `a2enmod`:

```bash
php bin/php/console exp:velocity layout --allow-root-user                 # every path, what is enabled, the files in use
php bin/php/console exp:velocity site enable example.com --allow-root-user
php bin/php/console exp:velocity conf enable reverse-proxy --allow-root-user
php bin/php/console exp:velocity mod disable http2 --allow-root-user
php bin/php/console exp:velocity ctl configtest --allow-root-user         # the engine's own check of the tree
```

## 3. Start at boot

`exp:velocity` installs no boot service. [`exponential-velocity.service`](exponential-velocity.service) is a systemd
unit that starts it through `exp:velocity`, so every setting stays in `velocity.ini`:

```ini
# Exponential 6: a systemd unit that starts Velocity through exp:velocity
#
# Free documentation: GFDL 1.3 or later, see doc/eng-us/README-LICENSE.md.
#
# Not shipped as a unit; derived from what expVelocity::start() does (it
# starts the server detached and returns once the port answers, leaving the
# pid in var/vc/qbix/run/server.pid) and from doc/eng-us/install/08-serving-the-site.md,
# 8.3.11. It keeps every setting in velocity.ini. Explained in velocity.md.
#
# Install: /etc/systemd/system/exponential-velocity-example.service, replace
# /var/www/example.com and the database unit, then
#   systemctl daemon-reload
#   systemctl enable --now exponential-velocity-example
#
# It runs as root so the server can bind ports 80 and 443 and read the key;
# the workers still drop to [ServerSettings] User and Group. With Instances
# above 1, PIDFile names instance 0 only. Do not combine it with the unit of
# the operating-system package for the same ports.

[Unit]
Description=Exponential Velocity for example.com
After=network-online.target mysqld.service
Wants=network-online.target

[Service]
Type=forking
WorkingDirectory=/var/www/example.com
ExecStart=/usr/bin/php bin/php/console exp:velocity start --allow-root-user
ExecStop=/usr/bin/php bin/php/console exp:velocity stop --allow-root-user
ExecReload=/usr/bin/php bin/php/console exp:velocity graceful --allow-root-user
PIDFile=/var/www/example.com/var/vc/qbix/run/server.pid
TimeoutStartSec=90
Restart=on-failure
RestartSec=5

[Install]
WantedBy=multi-user.target
```

## 4. Deploy a change

Workers keep classes, INI values and compiled templates in memory. After a code or settings change, one command
rebuilds the autoloads, clears the caches, reloads PHP-FPM if one serves the site too, restarts Velocity and clears the
caches that hold rendered output, in that order:

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user   # what it would do, nothing else
php bin/php/console exp:velocity deploy --allow-root-user
php bin/php/console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

## Check it

```bash
curl -sI https://example.com/ | head -1                                         # HTTP/2 200 or a redirect
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/.git/config        # 403 from the qbix engine
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]' # 0: a page, not the file
php bin/php/console exp:velocity ssl show --allow-root-user                     # the certificate in use
```

How this example was checked: every `[Block] Setting` of the override exists in the shipped `settings/velocity.ini`;
the unit passed `systemd-analyze verify`; the site file shape is a generated file with this machine's paths replaced.

## Related pages

- [Chapter 8, section 8.3: Exponential Velocity, the recommended way](../../install/08-serving-the-site.md#83-exponential-velocity-the-recommended-way),
  [8.3.10 The configuration tree](../../install/08-serving-the-site.md#8310-the-configuration-tree-etcvc-debian-style),
  [8.3.11 Running Velocity as a service](../../install/08-serving-the-site.md#8311-running-velocity-as-a-service).
- [Velocity engines](../../bc/6.0/velocity-engines.md) and [FrankenPHP](frankenphp.md).
- [Chapter 13, section 13.12: Velocity-specific hardening](../../install/13-security-hardening.md#1312-velocity-specific-hardening).
- [Web server examples](README.md) and [all examples](../README.md).
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
