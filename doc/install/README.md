# Installing and running Exponential 6.0: the book

This book takes you from an empty server to a production Exponential 6.0 site. It covers checking the requirements,
getting the code, choosing and running an installer, serving the site, choosing a database, and keeping the site
healthy afterwards, through to upgrades, troubleshooting and hardening. Each chapter stands on its own and ends with
references to the supporting documentation in this repository and to the official sources outside it.

In a hurry? [The short installation guide](../INSTALL.md) has the quick start and links back into the chapters here.

## Contents

### Part I: Before you install

| Chapter | What it covers |
|---|---|
| [1. Introduction: what you are installing](01-introduction.md) | The kernel, extensions, siteaccesses (site, admin, editor) and designs; the three install methods; conventions and glossary |
| [2. Requirements](02-requirements.md) | PHP 8.0 to 8.5, every extension and why, the php.ini settings the setup checks, databases, Composer, web server, operating systems, sizing |
| [3. Getting the code](03-getting-the-code.md) | Versions and tags, `composer create-project`, git, installing on PHP 8.0 to 8.3, the Velocity package, extensions as packages, a tour of the installation root |

### Part II: Installing

| Chapter | What it covers |
|---|---|
| [4. Choosing an install method](04-choosing-an-install-method.md) | One installer with three front ends, site packages, what an installation writes, unattended installs in CI and containers |
| [5. The setup wizard](05-setup-wizard.md) | The browser installer page by page: every field, its checks and what it writes; restarting a wizard that stopped |
| [6. The kickstarter](06-kickstarter.md) | Unattended installs from `kickstart.ini`: commands, the full settings reference, `DatabaseAction`, annotated examples, dry runs, re-running |
| [7. The console install: `exp:install`](07-console-install.md) | One command with options instead of a settings file: every option, dry run, exit status, examples per database |

### Part III: Running the site

| Chapter | What it covers |
|---|---|
| [8. Serving the site](08-serving-the-site.md) | Exponential Velocity, which serves HTTP and HTTPS itself with no separate web server; FrankenPHP; Apache and nginx with PHP-FPM; permissions; reverse proxies |
| [9. Databases](09-databases.md) | SQLite 3 as a production database under heavy load (WAL, queued writes, caching, scaling, backups); MySQL and MariaDB; PostgreSQL; MongoDB; Oracle; a comparison |
| [10. After installing: the operations handbook](10-after-installing.md) | First login, siteaccesses, cron, mail, notifications and newsletters, caches, autoloads, backups, logs, monitoring, performance, extensions |

### Part IV: Keeping it running

| Chapter | What it covers |
|---|---|
| [11. Upgrading](11-upgrading.md) | Which version you run, the update files, the paths from 3.x, 4.x and 5.x, and from any 6.0.x to today |
| [12. Troubleshooting](12-troubleshooting.md) | Symptom, cause and fix for every stage: Composer, the wizard, the kickstarter, run time, databases, Velocity, caches, signing in |
| [13. Security hardening for production](13-security-hardening.md) | What the server must never hand out, secrets, debug output, the admin siteaccess, passwords and sessions, form tokens, headers and HTTPS, the audit log |

## Which chapters do I need?

| Your situation | Read |
|---|---|
| A first test install on a laptop | [Short guide](../INSTALL.md), then chapters [3](03-getting-the-code.md) and [7](07-console-install.md) |
| A production site on one server, no separate web server | Chapters [2](02-requirements.md), [3](03-getting-the-code.md), [5](05-setup-wizard.md) or [6](06-kickstarter.md), [8](08-serving-the-site.md) (Velocity), [9](09-databases.md) (SQLite), [10](10-after-installing.md), [13](13-security-hardening.md) |
| A production site behind Apache or nginx with MySQL or PostgreSQL | Chapters [2](02-requirements.md), [3](03-getting-the-code.md), [5](05-setup-wizard.md) or [6](06-kickstarter.md), [8](08-serving-the-site.md), [9](09-databases.md), [10](10-after-installing.md), [13](13-security-hardening.md) |
| Unattended installs in CI or containers | Chapters [4](04-choosing-an-install-method.md), [6](06-kickstarter.md), [7](07-console-install.md) |
| Upgrading an existing site | Chapters [11](11-upgrading.md), [12](12-troubleshooting.md) |
| Something does not work | Chapter [12](12-troubleshooting.md) |

## Other documentation

- [Getting started](../guides/getting-started.md), [Operating a site](../guides/operating-a-site.md) and [Upgrading](../guides/upgrading.md): the task guides.
- [Backwards compatibility notes for 6.0](../bc/6.0/): what changed and what a site has to do about it, per topic.
- [Changelogs](../changelogs/6.0/): what each 6.0.x release changed.
- [The project README](../../README.md): an overview of Exponential and its features.
