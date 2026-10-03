# Velocity packages, Docker images and binaries

*Applies to: Exponential Velocity 0.0.4.28 and later. History: [August 2026](../../history/velocity/2026-08.md) (first binaries), [23 September](../../history/velocity/2026-09c.md) (platform matrix), [24 September](../../history/velocity/2026-09d.md) (packages), [25 to 30 September](../../history/velocity/2026-09e.md) (rename, `sbin/`).*

## What it is

Every Velocity release is published in the forms people actually install:

| Form | For |
|---|---|
| **Composer package** `se7enxweb/exponential-velocity` | Exponential installations (the usual way; Exponential's `exp:velocity` finds the engine in `vendor/`) |
| **deb and rpm packages** | A system service on Debian 12 and 13, Ubuntu 22.04 and 24.04, Enterprise Linux 9 and 10, using the distribution's own PHP |
| **Docker images** on `ghcr.io/se7enxweb/exponential-velocity` | Containers, amd64 and arm64, one image per PHP version and variant |
| **Static binaries** | One file with PHP built in: Linux x86-64 and aarch64, macOS arm64, Windows x64 |
| **The phar** `sbin/qbixserver.phar` | The server as one PHP archive, run by any PHP 8.1+ |
| **A source kit** | A build kit for a platform, architecture or PHP version no release covers |

All files of a release carry checksums in `SHA256SUMS`.

## Why use it

You choose the form that fits the machine instead of building anything: apt or dnf for a server, Docker for a platform, a single file for a laptop or an appliance. The extension set is defined once (the *extension baseline*), so every form is checked against the same list, and `qbixctl ext:check` tells you what a given PHP lacks and the exact command that installs it.

## Operating-system packages

```bash
curl -LO https://github.com/se7enxweb/exponential-velocity/releases/download/v<release>/exponential-velocity_<release>-1+deb12_all.deb
sudo apt install ./exponential-velocity_<release>-1+deb12_all.deb          # Debian, Ubuntu
sudo dnf install ./exponential-velocity-<release>-1.el10.noarch.rpm        # EL 10
```

| Distribution | Package | PHP |
|---|---|---|
| Debian 12 | `exponential-velocity_<release>-1+deb12_all.deb` | 8.2 |
| Debian 13 | `...+deb13_all.deb` | 8.4 |
| Ubuntu 22.04 | `...+ubuntu22.04_all.deb` | 8.1 |
| Ubuntu 24.04 | `...+ubuntu24.04_all.deb` | 8.3 |
| EL 9 | `exponential-velocity-<release>-1.el9.noarch.rpm` | 8.2 (enable the stream first: `sudo dnf module enable -y php:8.2`) |
| EL 10 | `...el10.noarch.rpm` | 8.4 |

The packages are architecture-independent (the server is PHP). Nothing starts on install. Start the service when the configuration is ready:

```bash
sudo systemctl enable --now exponential-velocity
systemctl status exponential-velocity
sudo systemctl reload exponential-velocity     # graceful: finishes requests in flight
qbixctl ext:check                              # what this PHP has and lacks
```

The service runs as the `qbix` system user with `/var/lib/exponential-velocity` as its state directory, and can bind ports below 1024. Its settings are in `/etc/default/exponential-velocity`:

| Setting | Default | Meaning |
|---|---|---|
| `QBIX_ROOT` | `/usr/share/exponential-velocity/web` | Document root (a welcome page until you point it at your application) |
| `QBIX_SITE` | `/etc/qbix/sites-enabled/default.conf` | The site file |
| `QBIX_OPTS` | empty | Any other `qbixserver` options, for example `--workers=16 --https-port=8443` |

The server listens on 8080 until `/etc/qbix/ports.conf` says otherwise.

### What goes where

```
/usr/share/exponential-velocity/        the server: phar, console tools, baseline, designs, docs
/usr/sbin/qbixserver, qbixctl, qbixconsole      the programs (0.0.4.41 and later)
/usr/bin/qbixserver, qbixctl, qbixconsole       links to the three above (their former paths)
/usr/bin/vc-qshell                      the shell (0.0.4.42 and later)
/etc/qbix/                              the configuration tree: qbix.conf, ports.conf, envvars, *-available, *-enabled
/etc/default/exponential-velocity       the service's settings
/var/lib/exponential-velocity/          state, owned by qbix
```

Everything under `/etc` is configuration: an upgrade never overwrites a file you changed. Enable and disable sites, snippets and modules with `qbixctl ensite`, `dissite`, `enconf`, `disconf`, `enmod`, `dismod` ([layout](../../bc/6.0/velocity-ondisk-layout.md)).

### Moving from the `qbix-webserver` package

Install `exponential-velocity` over it. The old package is removed (the new one replaces and provides it) and, once: `/etc/default/qbix-webserver` becomes `/etc/default/exponential-velocity` with paths rewritten; the state in `/var/lib/qbix-webserver` is copied; an enabled or running old service is stopped and the new one enabled or started in its place; `/usr/share/qbix-webserver` becomes a link. Where apt removed the old service before the new package was unpacked, run `sudo systemctl enable --now exponential-velocity`. The service user stays `qbix` and `/etc/qbix` is unchanged.

## Docker

```
ghcr.io/se7enxweb/exponential-velocity:<release>-php<version>-<variant>   fixed
ghcr.io/se7enxweb/exponential-velocity:php<version>-<variant>             moves with each release
ghcr.io/se7enxweb/exponential-velocity:latest                             newest PHP, standard variant
```

`<version>` is 8.2, 8.3, 8.4 or 8.5; `<variant>` is `mini`, `lite`, `standard` or `full`. Every tag is multi-architecture. Pin a fixed tag in production.

```bash
docker run -d --name web -p 8080:8080 \
  -v "$PWD/public:/app" \
  ghcr.io/se7enxweb/exponential-velocity:php8.3-standard
```

The document root is `/app`; any server option can be passed by replacing the command (`qbixserver --root=/srv/site/public --port=8080 --workers=16`). The standard and full variants also build with Oracle, Firebird and ODBC drivers. The old image `ghcr.io/se7enxweb/qbix-webserver` is deprecated: it keeps its tags but gets no new ones; change the name and keep the tag.

## Variants

| Variant | Carries | Size (Linux x86-64, PHP 8.3) | For |
|---|---|---|---|
| `mini` | Only what the server needs: process isolation, sockets, TLS, sessions, its own metrics store | 16 MB | Static sites, small scripts |
| `lite` | mini plus what a typical application platform requires: XML, images (gd), MySQL/MariaDB, SQLite, cURL, intl, zip, opcache | 68 MB | Most applications |
| `standard` | lite plus the recommended tier: PostgreSQL, MongoDB, caches (apcu, redis, memcached, igbinary), LDAP, SOAP, bcmath and more | 73 MB | The default; what the documentation assumes |
| `full` | Everything static-php-cli can build on that platform, best effort | about 90 to 120 MB | An extension outside standard |
| `source` | A build kit: the phar, the baseline, the console tool and a recipe for every platform, PHP version and variant | n/a | A platform no release covers |

A `full` build leaves some extensions out where they cannot be built (for example `rar` and `gmssl` everywhere); `php sbin/qbixctl.php ext:plan --variant=full --platform=windows-x64` lists what and why.

### Binary file names

```
qbixserver-<platform>-php<version>-<variant>[.exe]     the server, one file
php-<platform>-php<version>-<variant>[.exe]            the same PHP as a plain interpreter
qbixserver-windows-x64-php<version>-<variant>-gui.exe  Windows, no console window
qbixserver-source-kit-<release>.tar.gz                 the source kit
```

Platforms: `linux-x86_64`, `linux-aarch64`, `macos-arm64`, `windows-x64`.

```bash
curl -LO https://github.com/se7enxweb/exponential-velocity/releases/latest/download/qbixserver-linux-x86_64-php8.3-standard
curl -LO https://github.com/se7enxweb/exponential-velocity/releases/latest/download/SHA256SUMS
sha256sum --ignore-missing -c SHA256SUMS
chmod +x qbixserver-linux-x86_64-php8.3-standard && ./qbixserver-linux-x86_64-php8.3-standard --version
```

Check what any binary's PHP carries: `QBIX_STATIC_BUILD=1 ./php-linux-x86_64-php8.3-standard sbin/qbixctl.php ext:check --variant=standard`.

## Custom binaries and signing

`--pack=DIR --output=FILE` bundles an application into a binary (add `--gui` on Windows). A binary can be signed by several people and verified against a threshold: `--sign-binary --key=alice.pem --signer=Alice`, then `--verify-binary --m=2`; signatures can be published to Sigstore Rekor. `--verify-binary` says so (instead of printing PHP warnings) for an unsigned file, and `--sign-binary` exits non-zero when it cannot sign (fixed in 0.0.4.27).

## Platforms we watch but do not ship

A platform matrix runs the phar on systems no binary exists for: musl, DragonFly, FreeBSD, NetBSD, OpenBSD, illumos (on demand), RISC-V and ARMv5. The platform documentation lists three honest tiers (proven, expected, not today). Windows binaries have no `pcntl`/`posix` and no `intl`.

## From Exponential

Exponential's `exp:velocity` uses the Composer copy of the engine; the package is installed with the rest of the installation; see [Velocity engines](../../bc/6.0/velocity-engines.md) for choosing and running it. The engine archive and Exponential's own archive are different things: see [phar](../../bc/6.0/phar.md).

## Limits

- The packages need PHP 8.1 or later; EL 9 needs the PHP stream enabled first.
- Some recommended extensions (`mongodb`, `redis`, `memcached`) come from EPEL or Remi on EL; `qbixctl ext:check` names them.
- Release assets exist only for tags that have a section in the engine's changelog; three early tags (`v0.0.4.21`, `v0.0.4.22`, `v0.0.4.26`) have no release.

## See also

- Specification: [Engine settings](../../specifications/6.0/velocity-engine-settings.md) (programs and the `sbin/` and `bin/` layout), [Velocity engines](../../bc/6.0/velocity-engines.md).
- Upgrade: [Velocity engine upgrade notes](../../bc/6.0/velocity-engine-upgrade-notes.md) (package and path renames), [Velocity on-disk layout](../../bc/6.0/velocity-ondisk-layout.md).
- Related: [uwebserver](velocity-uwebserver.md), [HTTPS and certificates](velocity-https-certificates.md), [Q shell](velocity-q-shell.md) (`vc-qshell`).
- History: [August](../../history/velocity/2026-08.md), [23 September](../../history/velocity/2026-09c.md), [24 September](../../history/velocity/2026-09d.md), [25 to 30 September](../../history/velocity/2026-09e.md); [changelog](../../changelogs/extensions/exponential-velocity.md).
