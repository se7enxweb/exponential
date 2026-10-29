---
title: Glossary for hosts
audience: hosting
category: hosting
order: 900
summary: The terms used in the hosting section of the help, from siteaccess and VarDir to Velocity, response cache, kickstarter and request classifier, each with a one-line meaning and the page that explains it.
keywords: glossary, terms, definitions, siteaccess, VarDir, Velocity, qbix, FrankenPHP, response cache, view cache, kickstarter, exp:install, setup wizard, CheckValidity, site package, request classifier, request-shield, audit trail, DFS, cluster, maintenance mode
type: help
---
# Glossary for hosts

The terms the hosting pages use, in alphabetical order, each with the page that explains it. The general glossary of
the documentation is [doc/eng-us/glossary.md](../../glossary.md).

| Term | Meaning | More |
|---|---|---|
| Audit trail | hash-chained record of sign-ins, password, role and setting changes, removals and commands; `exp:audit verify` checks the chain | [Audit trail](security/audit-trail.md) |
| `CheckValidity` | `[SiteAccessSettings]` key; while `true` every request goes to the setup wizard; the installer sets it to `false` | [The setup wizard on shared hosting](provisioning/setup-wizard-on-shared-hosting.md) |
| Cluster (DFS) | several web nodes sharing file storage through the cluster handlers of `file.ini` | [Clustering and shared storage](operations/clustering-and-shared-storage.md) |
| Content Security Policy (CSP) | the browser policy sent from `site.ini [ContentSecurityPolicy]`; enforced on the administration once its reports are clean | [Content Security Policy](security/content-security-policy.md) |
| `CreateSites` | the install step that writes the database, the packages and the settings; every install method ends in it | [One-click installers](provisioning/one-click-installers.md) |
| `exp:install` | the console install: one command, options with defaults, exit status for scripts | [exp:install for hosts](provisioning/exp-install.md) |
| `exp:velocity deploy` | runs every step a PHP code change needs, in order, with PASS or FAIL per step | [Deploying changes](operations/deploying-changes.md) |
| ezupdate | the extension that checks for and applies updates | [Automated updates with ezupdate](operations/automated-updates-with-ezupdate.md) |
| FrankenPHP | Caddy with PHP built in; a production-ready alternative engine, run through Velocity | [FrankenPHP](servers/frankenphp.md) |
| Kickstarter | installs from a reviewed `kickstart.ini`; `exp:kickstarter run --apply` | [The kickstarter for hosts](provisioning/kickstarter.md) |
| Log viewer | **Setup > Log files** and `./console exp:logs`: lists, follows, searches and rotates the logs, with personal data masked | [Log viewer and rotation](operations/log-viewer-and-rotation.md) |
| Maintenance mode | a 503 page for visitors while work is done; `php bin/php/maintenance.php` with `on`, `off` or `status`, marker `var/maintenance.json` | [Maintenance mode](operations/maintenance-mode.md) |
| `ManagedByDeployment` | per-siteaccess switch that stops the administration from writing settings, templates and packages | [Hardening for hosts](security/hardening-for-hosts.md) |
| qbix | the name of Velocity's own engine in settings and commands (`[ServerSettings] Engine=qbix`) | [Velocity for hosts](servers/velocity-for-hosts.md) |
| Request classifier | the kernel part that writes probes and attacks to `var/log/security.log`; it classifies, it does not block | [Security log](operations/security-log.md) |
| request-shield | a separate web application firewall for PHP that decides before the kernel starts | [request-shield and request rules](security/request-shield-and-request-rules.md) |
| Response cache | Velocity's cache of whole rendered pages, answered without PHP; `exp:velocity cache clear` | [Velocity response cache](performance/velocity-response-cache.md) |
| Runbook | a step-by-step page for an incident | [Runbooks](runbooks/README.md) |
| Setup wizard | the browser installer, one page per step | [The setup wizard on shared hosting](provisioning/setup-wizard-on-shared-hosting.md) |
| Site package | the package of type `site` an install puts on a new site, for example `sevenx_multisite` | [Site packages](provisioning/site-packages.md) |
| Siteaccess | one way into the installation: a name, its settings and a matching rule (URL, host or port) | [Multisite on one installation](provisioning/multisite-on-one-installation.md) |
| `settings/override` | the installation's own settings, including the database password; back it up, keep it private | [What to back up](operations/backups-what-to-back-up.md) |
| `var/storage` | uploaded files and image variations: customer data | [Storage layout](operations/storage-layout.md) |
| `VarDir` | the directory a site writes caches, logs and storage to (`var/` or `var/<site>`) | [Storage layout](operations/storage-layout.md) |
| Velocity | Exponential Velocity, the persistent-worker web server written in PHP; recommended for every stage | [Velocity for hosts](servers/velocity-for-hosts.md) |
| View cache | the cache of rendered content views, `[ContentSettings] ViewCaching=enabled` | [Caching layers](performance/caching-layers.md) |
| Watchdog | an optional systemd timer that starts Velocity again after a crash; `exp:velocity watchdog install` | [Velocity will not start](runbooks/velocity-will-not-start.md) |
| Worker | a Velocity process that keeps the application in memory and serves requests | [Velocity workers and pools](servers/velocity-workers-and-pools.md) |

**Demo mode** - A public demo's state, switched on by the environment variable `EXP_DEMO_MODE=1` for the site's processes: administrator passwords cannot be changed or reset, the installers accept the published password, the administration shows a banner. Never for a customer's real site. [Demo mode for hosts](security/demo-mode-for-hosts.md).

## Related pages

- [Hosting companies: start here](hosting-companies-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
