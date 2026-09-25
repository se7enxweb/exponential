# Exponential 6.0.x
![Exponential - Powered by SQLite Logo](https://github.com/user-attachments/assets/b16a5e96-7483-4e83-b658-ac4fe92e84b8)
<img width="1200" height="693" alt="MongoDB-exp" src="https://github.com/user-attachments/assets/ef51c832-149e-4742-8b41-6f2c07ea6b6b" />
![Exponential - Project Logo](https://github.com/user-attachments/assets/c2f9e973-0b4f-4e58-ac76-f0308775e3c1)

# Important Notice for New or first time Users

**Exponential 6.0.x specifically requires the php package manager called Composer to be installed and run the command `composer install;` run before Exponential can be used.**

To install Exponential, first download the softare (this repository) and run composer install inside of the root Exponential 6.0.x installation directory.
To do this, you must first:

1. Install Composer from [getcomposer.org](https://getcomposer.org/download/)
1. Download Exponential 6.0.x and then from within the web server root directory:
2. Run `composer install` in the root directory of the Exponential installation

Failure to perform these required steps will result in the software not running properly or at all.

Please install responsibly and install (via composer) the software's php libraries required before attempting to use the software.

# Exponential Project Notice : 2025.08.12

"Please Note: This project is not associated with the original eZ Publish software or its original developer, eZ Systems or Ibexa".

# Exponential Project Status

**Exponential has made it beyond it's end of life in 2021 and survived. Current releases are primarily aimed at easing the requirements to support current versions of the PHP language like PHP 8.2, 8.3, 8.4, 8.5, 8.6 and beyond php9**

# Who is 7x

[7x](https://se7enx.com) is the North American corporation driving The Continued General Use, Support, Development, Hosting, Design of Exponential Enterprise Open Source Content Management System in 2025.

7x has been in busines supporting Exponential Website Customers and Projects for over 24 years. 7x took over leadership of the project and it's development, support, adoption and community growth in 2023.

7x represents a serious company leading the open source community based effort to improve Exponential and it's available community resources to help users continue to adopt and use the platform to deliver the very best in web applications websites and headless applications in the cloud.

Previously before 2022, 7x was called Brookins Consulting who was the outspoken leader in the active Exponential Community and it's Portals for the past 24 years.

# What is Exponential?

## Recent improvements to Exponential
Exponential (the application of interest) is delivered to users worldwide by a web
server. It runs behind any of the usual ones, and since 6.0.15 it also ships one
of its own — **Exponential Velocity** — which is considerably faster. See
[How Exponential is served](#how-exponential-is-served) below.

Exponential with a full complement of all popular and available php extensions installed like SQLite3 users no longer require a dedicated database server anymore with Exponential 6.

With PHP we require composer to install Exponential software and no other software required to run
the application. This is an incredible improvement to the kernel (core) of Exponential.

## What does Exponential provide for end users building websites?

Exponential is a professional PHP application framework with advanced CMS (content management system) functionality. As a CMS its most notable feature
is its fully customizable and extendable content model.
It is also suitable as a platform for general PHP development, allowing
you to develop professional Internet applications, fast.

Standard CMS functionality, like news publishing, e-commerce and forums is
built in and ready for you to use. Its stand-alone libraries can be
used for cross-platform, secure, database independent PHP projects.

Exponential is database, platform and browser independent. Because it is
browser based it can be used and updated from anywhere as long as you have
access to the Internet.

(Referred to as `legacy` in Exponential Platform 5.x and Ibexa OSS)


# How Exponential is served

Exponential is a PHP application, so something has to accept the request, find
the right file, and hand PHP the work. You have a choice, and since 6.0.15 that
choice includes a server built for this application specifically.

## The traditional options

Exponential runs behind any web server that can rewrite a URL — that is, turn a
readable address like `/recipes/summer-salad` into a request for `index.php`.
Every server below can do it, and Exponential ships or documents the rules for
each:

| Server | How it rewrites | Notes |
|---|---|---|
| **Apache HTTP Server** | `mod_rewrite`, `.htaccess` | The most widely used; `.htaccess` ships with Exponential |
| **Nginx** | `try_files` / `rewrite` | Very common; rules go in the server block, not `.htaccess` |
| **LiteSpeed** and **OpenLiteSpeed** | `.htaccess`-compatible rewrite engine | Reads Apache rules directly in most cases |
| **Caddy** | `rewrite` / `handle` / `try_files` | Automatic HTTPS out of the box |
| **Microsoft IIS** | URL Rewrite Module (`web.config`) | Windows hosting |
| **lighttpd** | `mod_rewrite` | Small footprint |
| **H2O** | `redirect` / `file.custom-handler` | HTTP/2 focused |
| **Traefik** | `ReplacePathRegex` middleware | Usually in front of another server |

And for development only:

| **PHP built-in server** | `php -S` with a router script | **Development only.** Single-threaded, no concurrency, not hardened. Never expose it to the internet. |

All of these work. Exponential does not require any particular one.

## Exponential Velocity — the fast option

**Velocity is a web server written in PHP, bundled with Exponential, that keeps
the application loaded between requests.**

It is based on the Qbix server and is developed alongside Exponential, so the
two are tuned against each other rather than merely made compatible.

### Why it is faster

A traditional setup starts PHP fresh for every request: load the framework,
read the settings, connect to the database, render, throw it all away, repeat.
That work is the majority of a page's cost, and it is identical every time.

Velocity loads the framework **once** into a parent process and forks workers
that share it. A request arrives and the application is already in memory. On
top of that it adds a response cache that answers a repeat request without
waking the application at all.

### What that measures as

These are the figures currently documented for this stack:

> **0.36 ms per cached page, 8,941 requests per second sustained, and repeat
> visits never touch the network.**

Read honestly, they mean:

- **0.36 ms** is the server's own work to answer a cached page — measured as a
  median over hundreds of requests, not a best case.
- **8,941 req/s sustained** was measured on a 12-core machine serving a real
  page, with **zero errors** at every concurrency tried, up to 384 connections
  in flight.
- **Repeat visits never touch the network** refers to the optional navigation
  cache (a service worker): once a visitor has a page, a return visit is
  answered by their own browser with no round trip at all.

Every one of those is reproducible with scripts that ship with the project —
they are not marketing numbers, and the methodology is written down alongside
them.

### The part that matters even if you never run Velocity

**Most of the speed came from fixing Exponential and the cache, not from the
server process model.** Those fixes apply wherever Exponential runs:

- a returning visitor's reload is answered with **202 bytes instead of the whole
  page**, because cached pages now answer conditional requests properly
- a page's identity is derived from **its content**, so rebuilding a cache entry
  that produced identical output no longer forces every visitor to download it
  again
- when a cached page expires, **one** request rebuilds it while everyone else is
  served the existing copy — instead of every visitor present rebuilding it at
  once
- a "not found" is remembered instead of being rebuilt from scratch every time
- pages are no longer shipped padded with template indentation

**Exponential 6.0.15+ is fast under load on Apache or Nginx too.** Velocity
raises the ceiling further; it is not what makes the application quick.

### Using it

```bash
./bin/php/console exp:velocity start     --allow-root-user
./bin/php/console exp:velocity status    --allow-root-user
./bin/php/console exp:velocity restart   --allow-root-user
./bin/php/console exp:velocity graceful  --allow-root-user   # no dropped connections
./bin/php/console exp:velocity stop      --allow-root-user
```

`graceful` re-executes the server without releasing the listening socket, so a
deploy does not drop requests. `status --json` is there for monitoring.

### Engines: FrankenPHP, PHP's built-in server or Qbix

The same commands drive three servers, chosen with `[ServerSettings] Engine` in
`velocity.ini`:

- **FrankenPHP**: Caddy with PHP built in, downloaded as one checksum-verified
  binary. The engine for production.
- **PHP's built-in server**: nothing to install. For development, and the
  shipped default because it always works.
- **Qbix**: the bundled Qbix server. Experimental, for tests.

```bash
./bin/php/console exp:velocity config set ServerSettings Engine frankenphp --allow-root-user   # production
./bin/php/console exp:velocity install --allow-root-user   # fetch and verify the binary
./bin/php/console exp:velocity start   --allow-root-user
./bin/php/console exp:velocity start --all --allow-root-user   # all three side by side, for tests
```

What each engine supports, the ports, the settings they share and who may open
the views each server answers itself are in
[doc/bc/6.0/velocity-engines.md](doc/bc/6.0/velocity-engines.md).

Settings live in `settings/override/velocity.ini.append.php`. Every one of them
has a default that works, so a server that configures nothing still runs.

### One thing to know before you rely on it

**Workers keep the application in memory between requests.** After changing a
template, a setting or a class, restart the server — clearing caches alone will
not dislodge what a worker already holds. The same applies if the engine is
being loaded from its archive: rebuild it first.

### When a traditional server is still the right answer

- **You need per-site operating-system isolation.** If several customers must
  not be able to read each other's data, run one Velocity instance per site
  behind a proxy, each as its own operating-system user. A single server process
  serving several sites is a routing convenience, not a security boundary — the
  process that serves one site can read every file the others hold.
- **Your hosting will not let you run a long-lived process.** Shared hosting
  usually will not. Apache or LiteSpeed with `.htaccess` is the answer there.
- **You already have a tuned Nginx or Apache in front.** Keep it. Velocity is
  happy behind a reverse proxy, and that is the recommended shape for TLS and
  certificates anyway.

## In short

| | Traditional server | Exponential Velocity |
|---|---|---|
| Application load | every request | once, shared by all workers |
| Cached page | rendered or proxied | **0.36 ms**, no worker involved |
| Sustained throughput | depends on pool size | **8,941 req/s** measured |
| Repeat visit | a round trip | **no network at all** (optional) |
| Isolation between sites | per vhost/user, as configured | one instance per site |
| Shared hosting | yes | usually not |
| Setup | vhost plus rewrite rules | one console command |

# Requirements
- PHP
- (Optional) Web server. Used to deliver the website to the end user. Apache,
  Nginx, LiteSpeed, Caddy, IIS, lighttpd and others all work — or use the
  bundled **Exponential Velocity**, which needs nothing installed beyond PHP.
- (Optional) Database server. Used to store website content (and application information)
- Composer. Used to download Exponential software packages for installation, also notebly installs the required Zeta Components php libraries.
- Computer to run the PHP website application.

## What version of PHP is required

Exponential Legacy supports PHP 8.1 -> 8.5+ please use the latest version of PHP available on your OS.

PHP 7 Support is deprecated but still available from our older stable and usable past releases up to version 6.0.7.

Developer note: PHP 7 support can be regained at any time by reverting php8.1 specific code changes in very small number of class function definitions. That said. 7x stronly recommends you to upgrade and leverage the latest PHP 8.x security improvements instead of trying to support unsupported software which is now insecure.

# Main Exponential features

- User defined content classes and objects
- Version control
- Advanced multi-lingual support
- Built in search engine
- Separation of content and presentation layer
- Fine grained role based permissions system
- Content approval and scheduled publication
- Multi-site support
- Multimedia support with automatic image conversion and scaling
- RSS feeds
- Contact forms
- Built in webshop
- Flexible workflow management system
- Full support for Unicode
- Template engine
- A headless CRUD REST API
- Database abstraction layer supporting MariaDB, MySQL, SQLite, PostgreSQL, Oracle and MongoDB
- MVC architecture
- Support for the latest Image and Video File Formats (webp, webm, png, jpeg, etc)
- Support for highly available and scalable configurations (multi-server clusters)
- XML handling and parsing library
- SOAP communication library
- Localisation and internationalisation libraries
- Several other reusable libraries
- SDK (software development kit)
  and full documentation
- Support for the latest Image and Video File Formats (webp, webm, png, jpeg, etc)
- plugin API with thousands of open-source extensions available, including:
    - content rating and commenting
    - landing page management
    - advanced search engine
    - wysiwyg rich-text editor
    - in-site content editing
    - content geolocation

# Installation

Read [doc/INSTALL.md](doc/INSTALL.md) or go to [exponential.doc.exponential.earth/Exponential/Technical-manual/6.x/Installation.html](https://exponential.doc.exponential.earth/Exponential/Technical-manual/6.x/Installation.html)

# Issue tracker

## Exponential (6.x) Issue tracker

Submitting bugs, improvements and stories is possible on [Exponential Project Issue Tracker](https://issues.exponential.earth)
If you discover a [security issue](SECURITY.md), please responsibly report such issues via email to security@exponential.earth

## Exponential Community Project Issue tracker

Submit bugs, stories and issues in general about almost anything Exponential Project / Community / Ecosystem / Website Software related to the [https://github.com/se7enxweb/exponential-community/issues](https://github.com/se7enxweb/exponential-community/issues)

# Where to get more help

Exponential documentation: [exponential.doc.exponential.earth/Exponential](https://exponential.doc.exponential.earth/Exponential)

Exponential Community forums: [share.exponential.earth/forums](https://share.exponential.earth/forums)

Exponential Project Website: [exponential.earth](https://exponential.earth)

Support Exponential! Project extension support Website: [support.exponential.earth](https://support.exponential.earth)

Share Exponential! Telegram Community Support Chat
[https://t.me/exponentialcms](https://t.me/exponentialcms)

# How to contribute new features and bugfixes into Exponential

Everyone is encouraged to [contribute](CONTRIBUTING.md) to the development of new features and bugfixes for Exponential 6.

# Donate and make a support subscription. 
## Help fund Exponential!

You can support this project and it's community by making a donation of what ever size donation you feel willing to give to the project.

If we have helped you and you would like to support the project with a subscription of financial support you may. This is what helps us deliver more new features and improvements to the software. Support Exponential with a subscription today!

A wide range of donation options avaialble at [sponsor.se7enx.com](https://sponsor.se7enx.com), [paypal.com/paypalme/7xweb](https://www.paypal.com/paypalme/7xweb) and [github.com/sponsors/se7enxweb](https://github.com/sponsors/se7enxweb)

# What is eZ Platform?

eZ Platform was Exponential's technological successor before being abandoned by Ibexa. eZ Platform is a highly extensible, pure Content Managment Platform built upon the ideals core to Symfony. It provides the same flexible content model at it's core like Exponential, and has a growing amount of additional features outside the traditional CMS scope provided by means of "Bundles" extending it.

It is built on top of the full Symfony Framework, giving developers access to "standard" tools for rapid web & application development.

eZ Platform in some users view suffered a slow road to a stable datatype compatability with existing custom implementations of Exponential. Today all of these conserns are now gone with a solid choice left leaving both Exponential Platform and eZ Platform as serious contenders to be carefully considered. [Netgen's Media Website Core software](https://github.com/netgen/media-site) represents a much more modern eZ Platform core powered by Ibexa OSS. If your going to choose; Choose wisely.

Further reading on the archived: [https://ezplatform.com/](http://web.archive.org/web/20200328165348/https://ezplatform.com/)

Note: Ibexa has discontinued support for the eZ Platform Product Line favoring instead to support only the Ibexa DXP / OSS Product line going forward.

eZ Platform is survived in part by Exponential Platform Project by 7x who has forked, mirrored, updated it's last free release eZ Platform stack of software and rebranded it as Exponential Platform 3.2.9 and Exponential Platform Legacy 2.5.0.1 (with legacy support) which provided much needed updates to provide for required php 8.3+ support allowing this software to continue to serve the users who fell in love with it from the start. Exponential Platform is just one of the many Symfony based PHP CMS + Ecommerce Solutions available under free / open source licenses.

Since the release of Exponential Platform; 7x has released the next generation Exponential Platform Nexus which represents the future of modern Symfony based CMS Development.

# What is Exponential Platform?

In short Exponential Platform is the direct continuation of the software stack known as eZ Platform created by eZ Systems and discontinued by Ibexa. This software represents the latest product line before the move to rebrand the stack to Ibexa DXP/OSS upgraded to support PHP 8.3+ and rebranded to avoid any trademark issues with the continued use of the software by users with a need or desire to build smarter, faster and with greater ease of maintinence within a Symfony based PHP CMS.

Exponential Platform currently comes in nine different distributions that are very powerful and very stable.

- **Exponential Platform Legacy 5.0.x** Dual Kernel Symfony CMS which also includes a full copy of Exponential 6

- Exponential Platform Legacy 4.6.x Dual Kernel Symfony CMS which also includes a full copy of Exponential 6

- Exponential Platform Legacy 3.3.x Dual Kernel Symfony CMS which also includes a full copy of Exponential 6

- Exponential Platform Legacy 2.5.x Dual Kernel Symfony CMS which also includes a full copy of Exponential 6

- Exponential Platform 3.2.9 Symfony only CMS

- **Exponential Platform Nexus 1.3.0.x** is Platform v5.x Dual Kernel Symfony 7.4+ CMS which also includes a full copy of Exponential 6.x+

- Exponential Platform Nexus 1.2.0.x is Platform v4.x Dual Kernel Symfony 6+ CMS which also includes a full copy of Exponential 6.x+

- Exponential Platform Nexus 1.1.0.x is Platform v3.x Dual Kernel Symfony 5.4+ 3.4+ CMS which also includes a full copy of Exponential 6.x+

- Exponential Platform Nexus 1.0.0.x is Platform v2.5.x Dual Kernel Symfony 3.4+ CMS which also includes a full copy of Exponential 6.x+

Learn more at https://platform.exponential.earth

Start a discussion at Share @ https://share.exponential.earth/forums/exponential-platform

# What is Ibexa DXP OSS?

Ibexa DXP OSS, the rebranded and evolved successor of eZ Platform, is a modern and highly extensible Digital Experience Platform built on the robust Symfony framework. Staying true to the foundational principles of Exponential, it retains the flexible content model at its core while expanding its capabilities to include a broad spectrum of features that go beyond traditional CMS functionality. These features are delivered through "Bundles," providing developers with powerful tools for creating advanced web and digital solutions.

Built on top of Symfony, Ibexa DXP OSS offers developers access to standardized tools for efficient and scalable web and application development. Its architecture supports rapid innovation while ensuring compatibility with modern development standards.

Ibexa DXP OSS has matured into a robust and versatile platform, serving as a serious option for organizations seeking a cutting-edge CMS solution. For those looking to build modern web experiences, tools like Netgen's Media Website Core further enhance Ibexa OSS, showcasing its potential as a flexible and future-ready platform.

If you're making a choice, choose with care—and consider the power and flexibility of Ibexa DXP OSS.

Ibexas DXP OSS is Dual Licensed with the non-free ibexa licenses or at your option the GNU GPLv2.

Further reading: [ibexa.co](https://ibexa.co/)

Documentation for Ibexa DXP: [doc.ibexa.co/en/latest/](https://doc.ibexa.co/en/latest/)

Ibexa DXP Developer Portal: [developers.ibexa.co](https://developers.ibexa.co/)


# License

Exponential is GNU GPL licensed. You can no longer choose between the GNU GPL and the eZ Systems' eZ Publish Professional License. The GNU GPL gives you the right to use, modify and redistribute Exponential under certain conditions. The GNU GPL license is distributed with the software, see the file LICENSE. It is also available at http://www.gnu.org/licenses/gpl.txt

Using Exponential under the terms of the GNU GPL is free of charge.

The Professional License is no longer available. This effectively makes Exponential GNU GPL ONLY.
