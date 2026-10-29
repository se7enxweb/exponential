---
title: VPS hosting
audience: hosting, admin
category: hosting
order: 40
summary: How to offer Exponential on a virtual private server: choosing the distribution and PHP, serving with Velocity or a traditional web server, the database, users and permissions, a boot service, and what the host and the customer each look after.
keywords: VPS, virtual private server, virtual machine, cloud machine, root access, AlmaLinux, Red Hat Enterprise Linux, Debian, Ubuntu, PHP, Velocity, systemd, Apache, nginx, PHP-FPM, SQLite, PostgreSQL, permissions, responsibilities
type: help
---
# VPS hosting

This page is for hosting companies that offer Exponential on a virtual private server (VPS): one virtual machine per
customer or per small group of sites, with root or sudo access. It answers which operating system and PHP to put in
the image, how the site should be served, which database fits, how to set up users and permissions, and how to
divide the work between host and customer.

## Contents

1. [What the sources say about virtual machines](#what-the-sources-say-about-virtual-machines)
2. [The base image](#the-base-image)
3. [Serving the site](#serving-the-site)
4. [The database](#the-database)
5. [Users, permissions and cron](#users-permissions-and-cron)
6. [Who does what](#who-does-what)
7. [Checklist](#checklist)

## What the sources say about virtual machines

"Cloud virtual machines (Amazon EC2 including Graviton, Google Compute Engine, Azure, Hetzner, DigitalOcean and the
rest) are ordinary Linux machines: tier 2 or 3 by their distribution"
([Containers, cloud and hosting panels](../../os-support.md#containers-cloud-and-hosting-panels)). A VPS from your
own platform is the same: its support level is the support level of the distribution you install on it.

| Distribution in the image | Tier | PHP 8 from (per the sources) |
|---|---|---|
| AlmaLinux 9 | 1 (the project's development server) | AppStream (8.0 stock; module streams `php:8.1` to `php:8.3`), Remi (8.0 to 8.5), or Plesk's PHP |
| Red Hat Enterprise Linux 9 and 10, Rocky Linux, Oracle Linux, CentOS Stream 9 and 10 | 2 | AppStream, Remi |
| Debian 12 and 13 | 2 | the distribution (8.2, 8.4) or Ondřej Surý's repository |
| Ubuntu 22.04 and 24.04 | 2 | the distribution (8.1, 8.3) or the `ondrej/php` PPA |
| Alpine Linux | 2 | `apk` |
| Fedora, SUSE, Arch, Amazon Linux 2023 and others | 3 | their own packages |

Source: [Operating system support](../../os-support.md#tier-2-known-to-work). Processor architecture matters only
through PHP and the database: x86_64 is tier 1, aarch64 tier 2
([Processor architectures](../../os-support.md#processor-architectures)).

## The base image

A VPS image for Exponential carries:

- **PHP 8.0 or later**, for Apache, PHP-FPM and Velocity alike (Velocity runs on 8.0 from its release 0.0.4.54). The
  newest 8.x your distribution offers is the recommendation; 8.5 is supported
  ([2.2 PHP](../../../install/02-requirements.md#22-php)).
- **The required extensions**: `dom`, `libxml`, `simplexml`, `xsl`, `mbstring`, `iconv`, `intl`, `pcre`, `json`,
  `session`, `zlib`, one database extension, `gd` or ImageMagick; for Velocity also `sockets`, with `pcntl` and
  `openssl` ([2.3 PHP extensions](../../../install/02-requirements.md#23-php-extensions)).
- **64-bit PHP**, recommended "for support of more than 30 languages".
- **Composer 2** and **Git**, for installs and updates from source
  ([Composer and Git on servers](../basics/composer-and-git-on-servers.md)).
- **The same PHP for web and command line.** "Both must be of a supported version and have the same extensions"
  ([Command line and web PHP](../../../install/02-requirements.md#command-line-and-web-php)).

On Red Hat Enterprise Linux 9 and its rebuilds, the documented package set for the stock PHP is:

```bash
dnf install php php-fpm php-mysqlnd php-gd php-intl php-mbstring php-xml php-pdo php-opcache
```

That stock PHP 8.0 runs Velocity too (the el9 rpm installs on it without a module stream). For a newer PHP switch
to another module stream (`dnf module reset php`, `dnf module enable php:8.3`, `dnf distro-sync`). After an installation exists, let it check the machine:

```bash
php bin/php/console exp:velocity ext check
php bin/php/console exp:velocity ext install-hint intl xsl
```

The second command prints the package install command for the system it runs on
([Finding what is missing](../../../install/02-requirements.md#finding-what-is-missing)).

## Serving the site

On a VPS the customer may run long-running processes, so the recommended engine is available:

| Option | When on a VPS |
|---|---|
| **Exponential Velocity** | the default: no other web server, HTTPS with its own certificates and ACME, persistent workers, response cache |
| **FrankenPHP** (through `exp:velocity`) | one verified binary with its own PHP |
| **Apache or nginx with PHP-FPM** | the image already carries a panel or a tuned web server, or the machine only has PHP 8.0 |

Velocity binds `127.0.0.1` on ports 8088 (HTTP) and 8080 (HTTPS) as shipped. To face visitors directly it is set to
`Host=0.0.0.0`, `Port=80` and `HTTPSPort=443`, started as root; the workers then drop to the site's user
([8.3.5 Address, ports, user and group](../../../install/08-serving-the-site.md#835-address-ports-user-and-group)).
`exp:velocity` has no command that installs a boot service; Chapter 8 shows a derived systemd unit that runs
`exp:velocity start` ([8.3.11](../../../install/08-serving-the-site.md#8311-running-velocity-as-a-service)).

> **Tip**
> Put the boot service in your VPS image template, with the installation path and the database service in `After=`
> as placeholders your provisioning fills in. Then every customer machine comes back after a reboot.

More: [Velocity for hosts](../servers/velocity-for-hosts.md),
[Apache with PHP-FPM](../servers/apache-with-php-fpm.md), [nginx with PHP-FPM](../servers/nginx-with-php-fpm.md).

## The database

In the official order: 1. SQLite 3 (the default) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL.
Microsoft SQL Server is known to be possible with additional work at this time.

A VPS serves one site from one machine, which is exactly where **1. SQLite 3** is "a full production database".
Choose a server "when more than one machine must write the database", "when many editors publish large changes at
the same moment all day, or when you need replication or database-level users and grants"; then **2. PostgreSQL**
is "the recommended database server" ([Database support](../../database-support.md)). The database server can run on
the VPS itself or on a separate database machine of your platform.

See [Databases in the official order](../basics/databases-in-the-official-order.md).

## Users, permissions and cron

- **One user writes.** The PHP-FPM pool, the Velocity workers, cron jobs and console scripts all run as the site's
  user ([8.7.2](../../../install/08-serving-the-site.md#872-one-user-or-one-group)). Never let workers run as root
  (`[ServerSettings] AllowRootWorkers=disabled`, the default).
- **No world-writable files.** Set the hardened `[FileSettings]` values `0770` and `0660` that `site.ini` recommends
  ([13.11](../../../install/13-security-hardening.md#1311-file-permissions-and-ownership)).
- **Cron in the site user's crontab**: `crontab -e -u <site user>` or `php bin/php/console crontab:edit` as that user
  ([Chapter 10, Cronjobs](../../../install/10-after-installing.md#103-cronjobs)).

## Who does what

A VPS offer has to say who looks after which layer. The sources do not prescribe a split; these are the options a host
usually chooses between:

| Layer | Unmanaged VPS | Managed VPS |
|---|---|---|
| Hypervisor, network, snapshots | host | host |
| Operating system and PHP updates | customer | host |
| Exponential updates (Composer, extensions) | customer | host or customer, as agreed ([Managed hosting](managed-hosting.md)) |
| Backups of database and files | customer, or host snapshots | host, with restore tests |
| Certificates | customer (Velocity ACME, certbot) | host |
| Monitoring | customer | host |

> **Warning**
> A hypervisor snapshot of a running machine is not a substitute for a database backup. For SQLite "never copy a
> database file that is in use"; use `.backup` or `VACUUM INTO` ([Backup and restore](../../database-support.md#backup-and-restore)).

## Checklist

- [ ] Distribution of tier 1 or 2, PHP 7.4 or later for web and CLI, the same extensions in both.
- [ ] `exp:velocity ext check` clean for the features in use.
- [ ] Velocity with a boot service, or Apache/nginx with PHP-FPM; HTTPS with a CA certificate.
- [ ] Database chosen in the official order and documented.
- [ ] Everything runs as the site user; `[FileSettings]` hardened.
- [ ] Cron lines for `runcronjobs.php` in the site user's crontab.
- [ ] Backups of database, `var/storage`, `settings/override`, `settings/siteaccess`, restored once on another machine.

## Related pages

- [Choosing a hosting model](choosing-a-hosting-model.md), [Dedicated servers](dedicated-servers.md)
- [Operating systems for hosts](../basics/operating-systems-for-hosts.md)
- [Checklist: new server](../checklists/new-server.md)
- [Chapter 2: Requirements](../../../install/02-requirements.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
