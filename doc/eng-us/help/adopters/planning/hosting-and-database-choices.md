---
title: Hosting and database choices for adopters
audience: adopter, project-manager, admin, hosting
category: adoption
order: 35
summary: The technical choices an adopter makes before building - PHP version, database in the project's order of preference, web server or Velocity engine, operating system, and the environments needed - with the reasons for each and links to the details.
keywords: hosting, database, SQLite, PostgreSQL, PHP version, Velocity, Apache, Nginx, environments, staging, production, operating system
type: help
---
# Hosting and database choices for adopters

## Contents

1. [The choices](#the-choices)
2. [Environments](#environments)
3. [Questions that decide](#questions-that-decide)

## The choices

| Choice | Options | Guidance |
|---|---|---|
| PHP | 8.0 to 8.5 | "please use the latest version of PHP available on your OS" (`README.md`) |
| Database | SQLite 3 (default), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, in the project's order of preference | [Database support](../../database-support.md) |
| Serving | Velocity (Qbix recommended, FrankenPHP the alternative) or Apache, Nginx and others | [Web servers and engines](../../ecosystem/php-stack/web-servers-and-engines.md) |
| Operating system | Almost any with PHP 8.0 or later; see the tiers | [Operating system support](../../os-support.md) |
| Search | The built-in search engine or Solr | [Search backends](../../hosting/basics/search-backends.md) |

The project managers' page covers the same choice for a project: [Choosing hosting, database and operating system](../../leadership/choosing-hosting-database-and-os.md).

## Environments

| Environment | For |
|---|---|
| Development | Developers' work, on their own machines (SQLite is convenient) |
| Staging | Rehearsals, acceptance tests, training |
| Production | The live site |

Keep code in Git and move it between environments the same way each time ([Develop with Git](../../develop-with-git.md),
[Running a team on Git](../../leadership/running-a-team-on-git.md)).

## Questions that decide

| Question | If yes |
|---|---|
| One server, modest traffic, few editors? | SQLite is "a full production database for a site served from one machine" |
| Several web servers on one database? | Not SQLite: "Several web servers on one database: no" for SQLite ([Database support](../../database-support.md)) |
| Organization standardized on one database? | Use it if it is among the six |
| Shared hosting without long-lived processes? | Apache or LiteSpeed rather than Velocity |
| Several customers or sites that must be isolated? | One Velocity instance per site, or per-site PHP-FPM pools |

## Related pages

- [Planning the adoption](README.md)
- [Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md)
- [Choosing who runs it](../deciding/choosing-who-runs-it.md)
- [Security hardening](../../security-hardening.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
