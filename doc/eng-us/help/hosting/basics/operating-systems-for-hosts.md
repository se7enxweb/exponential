---
title: Operating systems for hosts
audience: hosting, admin
category: hosting
order: 140
summary: Which operating systems a hosting company can run Exponential on, sorted by the project's four tiers, with the reference platform, the Linux families, panels, containers, processor architectures, Windows and how to check a machine.
keywords: operating system, OS, Linux, RHEL, AlmaLinux, Rocky Linux, CentOS Stream, Debian, Ubuntu, Alpine, Plesk, cPanel, DirectAdmin, FreeBSD, Windows, IIS, containers, Docker, Podman, Kubernetes, ARM, aarch64, tiers, reference platform, hosting
type: help
---
# Operating systems for hosts

This page is for system administrators and product managers at a hosting company who choose the operating system of
the servers an Exponential offer runs on. It answers: which systems are tested, known to work or only expected to
work, what that means for a hosting platform, and what to watch on panels, containers, other architectures and
Windows. The full support statement with every system is
[Operating system support](../../os-support.md); this page arranges it for hosts.

## Contents

1. [The short answer](#the-short-answer)
2. [The four tiers](#the-four-tiers)
3. [Linux distributions for hosting](#linux-distributions-for-hosting)
4. [Panels](#panels)
5. [Containers and cloud](#containers-and-cloud)
6. [Processor architectures](#processor-architectures)
7. [Windows and the BSDs](#windows-and-the-bsds)
8. [Check a machine](#check-a-machine)
9. [Checklist](#checklist)
10. [Related pages](#related-pages)

## The short answer

Exponential 6.0.x runs on almost any operating system that has PHP 8.0 or later and a web server. It is plain PHP
files plus a database; it has no compiled code of its own and no dependency on one C library
([OS support](../../os-support.md#what-the-operating-system-has-to-provide)). For a host the practical choice is a
current Linux distribution with a maintained PHP 8.

## The four tiers

| Tier | Meaning | Examples for hosts |
|---|---|---|
| **1. Tested here** | the project runs it every day, or its CI runs on it on every push | AlmaLinux 9 (the development server, with Plesk); Ubuntu in GitHub Actions |
| **2. Known to work** | covered by the documentation, packages or the Velocity engine's CI | RHEL 9 and 10, Rocky Linux, AlmaLinux, Oracle Linux, CentOS Stream 9 and 10; Debian 12 and 13; Ubuntu 22.04 and 24.04; Alpine; Plesk hosts; FreeBSD 14; Windows x64; Docker and Podman |
| **3. Expected to work** | a maintained PHP 8 exists and nothing known stands in the way, but nobody has watched Exponential run there | Fedora, SUSE and openSUSE, Amazon Linux 2023, cPanel and DirectAdmin hosts, Kubernetes |
| **4. Not possible today** | no PHP 8 exists for the system | of no practical concern to a host |

A tier 3 row is "a reasoned expectation, not a test result". If you run Exponential on a tier 3 system, the project
asks to be told on the [software tracker](https://github.com/se7enxweb/exponential/issues), and the row moves up.

> **Tip**
> For a new hosting platform, choose a tier 1 or tier 2 system. That keeps your platform on the paths the project's
> own documentation and packages describe.

## Linux distributions for hosting

| Family | PHP 8 from | Notes for hosts |
|---|---|---|
| RHEL 9 and 10 and rebuilds | AppStream (PHP 8.0 stock in EL 9; module streams `php:8.1` to `php:8.3`), Remi (8.0 to 8.5) | the documented reference platform; Velocity ships `.rpm` packages for EL 9 and EL 10; the stock 8.0 does not run Velocity |
| Debian 12 and 13 | the distribution (8.2, 8.4) or Ondřej Surý's repository | Velocity ships `.deb` packages |
| Ubuntu 22.04 and 24.04 | the distribution (8.1, 8.3) or the `ondrej/php` PPA | Velocity ships `.deb` packages; the project's CI runs on Ubuntu |
| Alpine (musl) | `apk` | Velocity's CI proves its server there; common as a container base |

Package names per family are on [PHP versions and extensions](php-versions-and-extensions.md#packages-per-system-family)
and in [chapter 2.9](../../../install/02-requirements.md#29-operating-system-notes).

## Panels

| Panel | Tier | What the documentation says |
|---|---|---|
| Plesk | 1 | the project's development server; per-domain PHP-FPM pools, PHP under `/opt/plesk/php/<version>/`, service `plesk-php<XY>-fpm` |
| cPanel, DirectAdmin | 3 | per-account PHP; shared hosting may forbid long-running processes, so Velocity may not be allowed: use Apache or LiteSpeed with `.htaccess` |

On shared hosting without shell access the setup wizard in the browser is the installer. See
[Control panels](../panels/README.md).

## Containers and cloud

From [OS support](../../os-support.md#containers-cloud-and-hosting-panels):

- Any image with PHP 8 and the required extensions runs Exponential. Velocity publishes images at
  `ghcr.io/se7enxweb/exponential-velocity` for amd64 and arm64, one per PHP version and variant.
- Keep `var/` (storage, logs, the SQLite database) and `settings/override` on a **persistent volume**.
- With SQLite, run **one** container that writes: WAL needs shared memory on one host, so several replicas cannot
  share one database file over a network volume. Several replicas need a database server (PostgreSQL first) and
  shared storage for `var/storage`.
- Cloud virtual machines are ordinary Linux machines, in the tier of their distribution.
- Platform-as-a-service offerings with a writable persistent disk are expected to work; a read-only or ephemeral file
  system needs `var/` on persistent storage and a database server.

See [Containers: Docker and Podman](../models/containers-docker-and-podman.md) and [Kubernetes](../models/kubernetes.md).

## Processor architectures

Exponential has no machine code, so the architecture matters only through PHP and the database:

| Architecture | Tier |
|---|---|
| x86_64 | 1 |
| aarch64 (ARM64: Graviton, Ampere, Apple Silicon, Raspberry Pi 4 and 5) | 2; Velocity publishes aarch64 binaries and images |
| armv7, i386 | 3; 32-bit PHP limits a site to 30 languages |
| ppc64le, s390x | 3; Velocity's CI proves its server on both |
| riscv64 | 3; tested only under emulation |

## Windows and the BSDs

**Windows** (tier 2): the official PHP 8 builds with Apache or IIS (FastCGI). What is missing there
([OS support: Windows](../../os-support.md#windows)):

- `pcntl` and `posix` do not exist, so asynchronous publishing is unavailable and Velocity runs its workers as
  `php-cgi` subprocesses.
- Velocity's single-file Windows binaries are built without `intl`, which Exponential requires; run Velocity on
  Windows through the phar with the official PHP.
- The documentation and its shell commands are written for Linux.

**FreeBSD 14 and DragonFly BSD** (tier 2): Velocity's CI boots and serves there; PHP 8 from `pkg`. Exponential itself
is not run there by the project. OpenBSD and NetBSD are tier 3.

## Check a machine

```bash
php -v                          # 8.0 or later (for Velocity too)
php -m                          # the required extensions
php -i | grep "Integer Size"    # 64 bits recommended
```

Then let the installer check the rest: the setup wizard's **System check**, the kickstarter or `exp:install`. With
Velocity installed, `php bin/php/console exp:velocity ext check` compares the machine with the extension set Velocity
expects ([OS support](../../os-support.md#check-a-machine-in-two-minutes)).

## Checklist

- [ ] The platform's operating system is tier 1 or tier 2.
- [ ] A maintained PHP 8 source chosen (distribution, module stream, Remi, Ondřej Surý's packages or the panel's builds).
- [ ] 64-bit PHP.
- [ ] Containers keep `var/` and `settings/override` on persistent volumes; one writer for SQLite.
- [ ] Panels that forbid long-running processes are offered with Apache or LiteSpeed, not Velocity.

## Related pages

- [Operating system support](../../os-support.md)
- [Requirements at a glance](requirements-at-a-glance.md)
- [Choosing a hosting model](../models/choosing-a-hosting-model.md)
- [Choosing a web server](../servers/choosing-a-web-server.md)
- [Chapter 2.9: Operating system notes](../../../install/02-requirements.md#29-operating-system-notes)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
