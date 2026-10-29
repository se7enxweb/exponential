---
title: Microsoft IIS
audience: hosting
category: hosting
order: 70
summary: What the Exponential sources say about Windows hosting with Microsoft IIS and FastCGI PHP: the status, what the kernel detects, what Windows lacks, the routing to reproduce in web.config, and the tests to run before offering it.
keywords: IIS, Microsoft IIS, Windows, FastCGI, cgi-fcgi, URL Rewrite, web.config, IIS_WasUrlRewritten, ForceVirtualHost, non-thread-safe PHP, pcntl, WSL2, tier 3
type: help
---
# Microsoft IIS

This page is for hosting companies that offer Windows hosting and want to know whether Exponential can be part of
it. It answers: what the sources say about IIS, what works differently on Windows, what routing an IIS site must
reproduce and what to test. IIS's own management tools are not described here; check Microsoft's documentation for
them.

## Contents

1. [Status](#status)
2. [What Windows changes](#what-windows-changes)
3. [What the kernel knows about IIS](#what-the-kernel-knows-about-iis)
4. [Routing to reproduce](#routing-to-reproduce)
5. [Tests before offering it](#tests-before-offering-it)
6. [Alternatives on Windows machines](#alternatives-on-windows-machines)
7. [Checklist](#checklist)

## Status

| Source | Statement |
|---|---|
| `README.md`, "The traditional options" | Microsoft IIS rewrites with the "URL Rewrite Module (`web.config`)", for "Windows hosting" |
| [Operating system support, Web servers](../../os-support.md#web-servers) | Microsoft IIS with FastCGI: **tier 3**; "the URL Rewrite module (`web.config`); the kernel's request handling has tests for IIS 7 server variables" |
| [Operating system support, PHP SAPIs](../../os-support.md#php-sapis) | CGI / FastCGI (`cgi-fcgi`) is the SAPI for IIS on Windows |
| [Operating system support, Windows](../../os-support.md#windows) | use the official PHP 8 builds for Windows (x64, non-thread-safe for FastCGI) with Apache or IIS |

No `web.config` ships with Exponential. Tier 3 means expected to work, not tested here
([The four tiers](../../os-support.md#the-four-tiers)).

## What Windows changes

From [Operating system support, Windows](../../os-support.md#windows):

- `pcntl` and `posix` do not exist on Windows. Asynchronous publishing (`bin/php/ezasynchronouspublisher.php`) is not
  available.
- Velocity runs its workers as `php-cgi` subprocesses on Windows, without copy-on-write memory sharing. Its
  single-file Windows binaries are built **without `intl`**, which Exponential's setup requires; run Velocity on
  Windows through the phar with the official PHP rather than through its binary (expected to work but untested,
  according to the engine's own platform notes).
- The documentation and the shell commands in this help are written for Linux; translate paths and permissions.
- The setup wizard looks for ImageMagick under Windows names too (`convertim.exe`, `convert.exe`, in
  `settings/setup.ini`).

> **Note**
> For a host, the practical consequence is that a Windows plan behaves like a classic FastCGI plan: no Velocity
> worker pool with shared memory, no asynchronous publishing. State that in the plan description.

## What the kernel knows about IIS

- `eZSys` (in `lib/ezutils/classes/ezsys.php`) detects IIS's rewritten requests by the server variable
  `IIS_WasUrlRewritten` and then works in virtual host mode, as it does for Apache's `.htaccess` mode. A site can also
  force that mode with `site.ini [SiteAccessSettings] ForceVirtualHost=true`.
- The server-variable fixtures of the kernel's tests include IIS 7 and IIS 7.5 with FastCGI
  (`tests/tests/lib/ezutils/server/`), in virtual host and non-virtual host mode.

Those fixtures were recorded with old PHP versions; they cover how IIS reports its variables, not a current IIS with
PHP 8. Test your own stack.

## Routing to reproduce

IIS never reads `.htaccess`, so `exp:setup:htaccess` does not protect an IIS site
([Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)).
Whatever IIS rule set you write must do what `.htaccess_root` does
([Chapter 8, How a request is served](../../../install/08-serving-the-site.md#82-how-a-request-is-served)):

| Request | Must go to |
|---|---|
| a dot file or dot directory (`.git`, `.env`, `.htaccess`), `.well-known` excepted | 404 |
| an existing `.php`, `.phtml` or `.phar` below the root | 404 |
| `/api/...` | `index_rest.php` |
| `/content/treemenu...`, `/<siteaccess>/content/treemenu...` | `index_treemenu.php` |
| a listed asset path (design and extension stylesheets, images, scripts and fonts; `var/*/storage/images`; image originals; stored PDF exports; public caches; `share/icons`; the icon themes of extensions, `extension/<name>/icons/`, images with exactly one extension; package previews; `favicon.ico`, `robots.txt`, `index.js`, `sw.js`, `w3c/p3p.xml`) | sent as a file |
| everything else | `index.php` |

The regular expressions of each line are in `.htaccess_root` and, for the asset list, in
`expVelocity::STATIC_PATHS` (`kernel/classes/expvelocity.php`). The derived nginx block on
[nginx with PHP-FPM](nginx-with-php-fpm.md) shows the same list translated once already.

> **Warning**
> Keep the order: the dot-file and script rules first, the catch-all last. A catch-all placed first, or an asset rule
> that matches `.php`, can run or leak files.

## Tests before offering it

Run the four routing checks of [Web servers and engines](README.md#what-every-setup-has-in-common), then:

- the front page, a content page, `/user/login` and the administration answer;
- an uploaded image is shown and an uploaded file downloads through `content/download`;
- the scheduled task that runs `runcronjobs.php` works with the CLI PHP ([Cron jobs](../operations/cron-jobs.md));
- the web PHP and the CLI PHP are the same version with the same extensions
  ([Chapter 2, Command line and web PHP](../../../install/02-requirements.md#command-line-and-web-php));
- 64-bit PHP: 32-bit PHP limits a site to 30 languages ([Operating system support](../../os-support.md#processor-architectures)).

## Alternatives on Windows machines

- **Apache with FastCGI PHP** on Windows: the shipped `.htaccess_root` applies directly (copied with
  `php bin/php/console exp:setup:htaccess --apply`).
- **WSL2** runs the Linux tiers and is the simpler path for development on Windows.
- **A Linux virtual machine or container** on the Windows host, if the plan can be built that way
  ([Containers, Docker and Podman](../models/containers-docker-and-podman.md)).

## Checklist

- [ ] Official PHP 8 x64 non-thread-safe build with FastCGI; `intl` and the other required extensions present.
- [ ] A rule set that reproduces the routing table above, tested with the routing checks.
- [ ] Plan description states that Velocity's worker pool and asynchronous publishing are not part of it.
- [ ] Scheduled tasks for cron jobs in place and tested.
- [ ] Tested on staging before sale (tier 3).

## Related pages

- [Choosing a web server](choosing-a-web-server.md)
- [Operating systems for hosts](../basics/operating-systems-for-hosts.md)
- [Operating system support](../../os-support.md)
- [Chapter 8: Serving the site](../../../install/08-serving-the-site.md)
- [Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
